<?php

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;


StaticFunctions::ajax_form('general');
StaticFunctions::new_session();

$Jwt = StaticFunctions::post('Jwt');
$UrlToken = StaticFunctions::post('UrlToken');
$FcmToken = StaticFunctions::post('FcmToken');
$StaticFcmToken = $FcmToken;

if ($Jwt == '' || $UrlToken == '' ||  $FcmToken == '') {
    http_response_code(401);
    exit;
}


$recaptcha = new \ReCaptcha\ReCaptcha(ProjectDefines::RecaptchaV3()['SecretKey']);
$resp = $recaptcha->setExpectedAction('push')
    ->setScoreThreshold(0.3)
    ->verify(StaticFunctions::post('Recaptcha'), StaticFunctions::get_ip());

if (!$resp->isSuccess()) {
    http_response_code(401);
    echo StaticFunctions::JsonOutput([
        'status' => 'failed',
        'label' => 'danger',
        'message' => StaticFunctions::lang('Recaptha tarafından engellendiniz. Lütfen tekrar deneyiniz.')
    ]);
    exit;
}


try {
    $JwtUser = \Firebase\JWT\JWT::decode($Jwt, StaticFunctions::JwtKey(), array('HS256'));
    if ($JwtUser->TokenExpire > time()) {
        $UserID = $JwtUser->UserId;
    } else {
        http_response_code(401);
        exit;
    }
} catch (\Throwable $th) {
    http_response_code(401);
    exit;
}

$GetUser = $db->query("SELECT push_id,real_name,token,id FROM users WHERE id = '{$UserID}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
if (!$GetUser) {
    http_response_code(401);
    exit;
}

if ($UrlToken != $GetUser['token']) {
    http_response_code(401);
    exit;
}

$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'https://iid.googleapis.com/iid/info/' . $FcmToken, [
    'http_errors' => false,
    'headers' => [
        'Authorization' => 'key=' . ProjectDefines::FirebaseCloudMsg()['ServerKey']
    ]
]);

if ($response->getStatusCode() != 200) {
    http_response_code(401);
    exit;
}

$Jsn = json_decode($response->getBody());

if ($Jsn->authorizedEntity != ProjectDefines::FirebaseCloudMsg()['SenderId']) {
    http_response_code(401);
    exit;
}


DeviceParserAbstract::setVersionTruncation(DeviceParserAbstract::VERSION_TRUNCATION_NONE);
$dd = new DeviceDetector($_SERVER['HTTP_USER_AGENT']);
$dd->parse();

if ($dd->isBot()) {
    http_response_code(401);
    exit;
} else {
    $clientInfo = $dd->getClient();
    $osInfo = $dd->getOs();
    $device = $dd->getDeviceName();
    $brand = $dd->getBrandName();
    $model = $dd->getModel();
}


$InsertDevice = $db->prepare("INSERT INTO fcm_devices SET
user_id = ?,
fcm_token = ?,
client_type = ?,
client_name = ?,
client_version = ?,
os_name = ?,
os_version = ?,
device_type = ?,
added_time = ?,
status = ?");
$insert = $InsertDevice->execute(array(
    $GetUser['id'], $FcmToken, $clientInfo['type'], $clientInfo['name'], $clientInfo['version'], $osInfo['name'], $osInfo['version'], $device, time(), 1
));

$last_id = $db->lastInsertId();

$FcmValid = false;
$FcmID =  $GetUser['push_id'];
if ($FcmID != '') {
    $FcmToken = $db->query("SELECT id FROM fcm_devices WHERE id = '{$FcmID}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
    if ($FcmToken) {
        $FcmValid = true;
    }
}

/*
if (!$FcmValid) {
    $UpdateFcm = $db->prepare("UPDATE users SET
        push_id = :fcmt
        WHERE id = :uid and status=1 ");
    $update = $UpdateFcm->execute(array(
        "fcmt" => $last_id,
        "uid" => $GetUser['id']
    ));
}
*/


if (isset($JwtUser->SendPin) && $JwtUser->SendPin) {

    $Now = time();
    $FindPin = $db->query("SELECT pin_code FROM pin_codes WHERE user_id = '{$UserID}' and process_type='push_notification' and last_time > $Now ")->fetch(PDO::FETCH_ASSOC);
    if ($FindPin) {
        $PinCode = $FindPin['pin_code'];

        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'key=' . ProjectDefines::FirebaseCloudMsg()['ServerKey'],
            ],
            'http_errors' => false
        ]);

        $FirstNameExplode = explode(' ', $GetUser['real_name']);

        $response = $client->post(
            'https://fcm.googleapis.com/fcm/send',
            ['body' => '{
					"to":"' . $StaticFcmToken . '",
					"data":{
						"notification":{
							"title":"' . StaticFunctions::lang('Easybot Çift Faktörlü Doğrulama') . '",
							"body":"' . StaticFunctions::lang('Selam {0}, {1} pin kodu ile işlemini tamamlayabilirsin.', [
                $FirstNameExplode[0], $PinCode
            ]) . '",
							"url":"https://easybot.dev",
							"notif_id":"' . $GetUser['id'] . rand(1, 9999) . '",
							"vibrate":"[300,100,400]",
							"badge":"' . PROTOCOL . DOMAIN . PATH . 'assets/media/favicon.ico",
							"icon":"' . PROTOCOL . DOMAIN . PATH . 'assets/media/favicon.ico"
						}
					}
				}']
        );

        $PinInfo = json_encode([
            'FcmDeviceID' => $last_id
        ]);

        $UpdatePinInfo = $db->prepare("UPDATE pin_codes SET
            process_data = :new_Data
        WHERE user_id = :uids and pin_code= :pnc and process_type='push_notification' ");
        $update = $UpdatePinInfo->execute(array(
            "new_Data" => $PinInfo,
            "uids" => $GetUser['id'],
            'pnc' => $PinCode
        ));
    }
}


echo StaticFunctions::ApiJson([
    'process' => 'success'
]);