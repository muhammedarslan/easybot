<?php

StaticFunctions::ajax_form('validated');
$Me = StaticFunctions::get_id();

$Jwt = StaticFunctions::post('pageToken');
$requestToken = StaticFunctions::post('requestToken');

if ($Jwt == '' || $requestToken == '') {
    http_response_code(401);
    exit;
}

try {
    $Decoded = \Firebase\JWT\JWT::decode($Jwt, StaticFunctions::JwtKey(), array('HS256'));
} catch (Exception $e) {
    http_response_code(401);
    exit;
}

$MyUser = $db->query("SELECT * from users WHERE id='{$Me}' and status=1")->fetch(PDO::FETCH_ASSOC);
if ($Decoded->owner != $MyUser['token']) {
    http_response_code(401);
    exit;
}

$Now = time();
$TempQuery = $db->query("SELECT * FROM processor_temp WHERE temp_token='{$requestToken}' and user_id='{$Me}' and temp_process='create_bot_876541'  and expired_time > $Now and temp_status=0 ")->fetch(PDO::FETCH_ASSOC);
if (!$TempQuery) {
    http_response_code(401);
    exit;
}

$RequestData = json_decode($TempQuery['temp_data'], true);
$BackTime = time() - (60 * 10);
$MySandboxActiveProcess = $db->query("SELECT id FROM processor_temp WHERE user_id='{$Me}' and expired_time > $Now and created_time > $BackTime and temp_status=1 ", PDO::FETCH_ASSOC);
$MySandboxAllProcess = $db->query("SELECT id FROM processor_temp WHERE user_id='{$Me}' and expired_time > $Now and created_time > $BackTime and ( temp_status=1 or temp_status=2 ) ", PDO::FETCH_ASSOC);
$MyRowCount = $MySandboxActiveProcess->rowCount(); // Benim aktif görev sayım.
$MyAllRowCount = $MySandboxAllProcess->rowCount(); // Benim tüm görev sayım.

if ($MyRowCount > 1) {
    echo StaticFunctions::ApiJson([
        'status' => 'success',
        'tokens' => [
            'pageToken' => $Jwt,
            'requestToken' => $requestToken
        ],
        'process' => 'wait',
        'waitingReason' => 'USER_ACTIVITY_COUNT',
        'waitMessage' => StaticFunctions::lang('Aktif diğer bot işlemlerinizin tamamlanması bekleniyor...')
    ]);
    exit;
}

if ($MyAllRowCount > 10) {
    echo StaticFunctions::ApiJson([
        'status' => 'success',
        'tokens' => [
            'pageToken' => $Jwt,
            'requestToken' => $requestToken
        ],
        'process' => 'wait',
        'waitingReason' => 'USER_TOTAL_ACTIVITY_COUNT',
        'waitMessage' => StaticFunctions::lang('İşleminiz sırada bekliyor...')
    ]);
    exit;
}

$TotalSandboxActiveProcess = $db->query("SELECT id FROM processor_temp WHERE expired_time > $Now and created_time > $BackTime and temp_status=1 ", PDO::FETCH_ASSOC);
$TotalRowCount = $TotalSandboxActiveProcess->rowCount();

if ($TotalRowCount > 10) {
    echo StaticFunctions::ApiJson([
        'status' => 'success',
        'tokens' => [
            'pageToken' => $Jwt,
            'requestToken' => $requestToken
        ],
        'process' => 'wait',
        'waitingReason' => 'TOTAL_ACTIVITY_COUNT',
        'waitMessage' => StaticFunctions::lang('Genel bir yoğunluktan ötürü işleminiz sırada bekletiliyor...')
    ]);
    exit;
}

if (StaticFunctions::post('startBot') == 'false') {
    echo StaticFunctions::ApiJson([
        'status' => 'success',
        'tokens' => [
            'pageToken' => $Jwt,
            'requestToken' => $requestToken
        ],
        'process' => 'processing',
        'location' => false,
        'processMessage' => StaticFunctions::lang('veriler işleniyor...')
    ]);
    exit;
}


$UpdateTempRow = $db->prepare("UPDATE processor_temp SET
temp_status = :new_stat
WHERE id = :current_id");
$update = $UpdateTempRow->execute(array(
    "new_stat" => 1,
    "current_id" => $TempQuery['id']
));


$SandboxData = json_decode($TempQuery['temp_data'], true);

require_once CDIR . '/class.bot.restrictions.php';
require_once CDIR . '/class.http.codes.php';
require_once CDIR . '/class.upload.php';
require_once CDIR . '/class.bot.static.php';
$StaticContent = new EasyBotStaticContent();
$StaticContent->setDb($db);
$StaticContent->setUser($MyUser);
$StaticContent->setRequest($SandboxData);
$StaticContent->sendRequest();

if ($StaticContent->RequestValid() != true) {

    $UpdateTempRow = $db->prepare("UPDATE processor_temp SET
    temp_status = :new_stat
    WHERE id = :current_id");
    $update = $UpdateTempRow->execute(array(
        "new_stat" => 2,
        "current_id" => $TempQuery['id']
    ));

    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'tokens' => [
            'pageToken' => $Jwt,
            'requestToken' => $requestToken
        ],
        'process' => 'fail',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => $StaticContent->GetErrors(),
        'buttons' => [
            'cancel' => StaticFunctions::lang('Kapat')
        ]
    ]);
    exit;
}

$RequestBody = $StaticContent->getBody();
$RequestHeaders = $StaticContent->getHeaders();
$RandomTmpKey = md5(StaticFunctions::random_with_time(32) . $Me);

try {
    file_put_contents(APP_DIR . '/tmp/bot_static_html/' . $RandomTmpKey . '.easybot_temp', $RequestBody);
} catch (\Throwable $th) {
    $UpdateTempRow = $db->prepare("UPDATE processor_temp SET
    temp_status = :new_stat
    WHERE id = :current_id");
    $update = $UpdateTempRow->execute(array(
        "new_stat" => 2,
        "current_id" => $TempQuery['id']
    ));
    http_response_code(401);
    exit;
}

$UpdateTempRow = $db->prepare("UPDATE processor_temp SET
temp_status = :new_stat,
temp_headers = :temp_d,
temp_file = :temp_f
WHERE id = :current_id");
$update = $UpdateTempRow->execute(array(
    "new_stat" => 2,
    "temp_f" => 'bot_static_html/' . $RandomTmpKey . '.easybot_temp',
    "temp_d" => json_encode($RequestHeaders),
    "current_id" => $TempQuery['id']
));

echo StaticFunctions::ApiJson([
    'status' => 'success',
    'tokens' => [
        'pageToken' => $Jwt,
        'requestToken' => $requestToken
    ],
    'process' => 'completed',
    'location' => true,
    'unixTimeStap' => time(),
    'processMessage' => StaticFunctions::lang('başarıyla tamamlandı.')
]);
exit;