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

$UpdateTempRow = $db->prepare("UPDATE processor_temp SET
temp_status = :new_stat
WHERE id = :current_id");
$update = $UpdateTempRow->execute(array(
    "new_stat" => 2,
    "current_id" => $TempQuery['id']
));

echo StaticFunctions::ApiJson([
    'request' => 'finished'
]);