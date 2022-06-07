<?php

StaticFunctions::ajax_form('private');

$Me = StaticFunctions::get_id();
$MeQuery = $db->query("SELECT * from users WHERE id='{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

if (!$MeQuery) {
    http_response_code(401);
    StaticFunctions::LogOut();
    exit;
}

require_once CDIR . '/class.security.layer.php';
$SecureClass = new SecurityLayer();
$SecureClass->setDb($db);
$SecureClass->setUser($MeQuery);

if ($SecureClass->IsSecure()) {
    http_response_code(401);
    exit;
}

$VerifyWith = StaticFunctions::post('selected');
$VerifyPossibleArray = ['Email', 'Sms', 'Authenticator', 'Notification'];

if ($VerifyWith == '' || !in_array($VerifyWith, $VerifyPossibleArray)) {
    http_response_code(401);
    exit;
}

$Layer = $SecureClass->Layer;
$PossibleMethods = $SecureClass->AcceptedMethods($Layer);

if ($PossibleMethods[$VerifyWith] != true) {
    http_response_code(401);
    exit;
}

$CheckActivePin = $SecureClass->CheckActivePin($Layer, $VerifyWith);

if ($CheckActivePin['hasValidPin']) {
    $IsNewCodeSend = false;
    $PinLastTime = $CheckActivePin['pinLastTime'];
} else {
    $IsNewCodeSend = true;
    $PinLastTime = $SecureClass->createPinCode($Layer, json_encode([
        'VerifyWith' => $VerifyWith,
        'require' => 'SecurityLayerPinVerified',
        'withData' => [
            'Layer' => $Layer,
            'VerifiedWith' => $VerifyWith
        ]
    ]), $VerifyWith);
}

switch ($VerifyWith) {
    case 'Email':
        $SendedInfo = StaticFunctions::lang('Lütfen gelen kutunu kontrol et ve sana gönderilen pin kodunu aşağıdaki alana girerek doğrula.');
        break;

    case 'Sms':
        $SendedInfo = StaticFunctions::lang('Lütfen cep telefonunu kontrol et ve sana gönderilen pin kodunu aşağıdaki alana girerek doğrula.');
        break;

    case 'Authenticator':
        $SendedInfo = StaticFunctions::lang('Lütfen Google Authenticator uygulaması üzerinde oluşan pin kodunu aşağıdaki alana girerek doğrula.');
        break;

    case 'Notification':
        $SendedInfo = StaticFunctions::lang('Lütfen mobil cihazını kontrol et ve sana gönderilen pin kodunu aşağıdaki alana girerek doğrula.');
        break;

    default:
        $SendedInfo = StaticFunctions::lang('Lütfen pin kodunu kontrol et ve sana gönderilen pin kodunu aşağıdaki alana girerek doğrula.');
        break;
}

echo StaticFunctions::ApiJson([
    'status' => 'success',
    'pinLeftSecond' => $PinLastTime - time(),
    'pinSendedMethod' => $VerifyWith,
    'newCodeSended' => $IsNewCodeSend,
    'TimerRandom' => StaticFunctions::random(16),
    'SendedInfo' => $SendedInfo
]);