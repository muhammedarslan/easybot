<?php

// $PinObjectData

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();
$Me = StaticFunctions::get_id();

if (!isset($IsCgi) || !$IsCgi) {
    http_response_code(401);
    exit;
}

$PhoneCode   = $PinObjectData['PhoneCode'];
$PhoneNumber = $PinObjectData['PhoneNumber'];


require_once CDIR . '/class.pin.verify.php';
$PinVerify = new PinCodeVerification();
$PinVerify->setDb($db);
$PinVerify->setUserId($Me);
$SendPin = $PinVerify->VerifyProcess('change_phone_number_step2', [
    'require' => 'ChangePhoneNumberStep2',
    'withData' => [
        'PhoneCode' => $PhoneCode,
        'PhoneNumber' => $PhoneNumber
    ]
]);



$CallbackJs = 'PhoneNumberChangeStep2';
