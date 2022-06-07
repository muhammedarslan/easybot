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

$PinCode = StaticFunctions::post('pin');

if ($PinCode == '' || mb_strlen($PinCode) != 6) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Pin kodu geçersiz görünüyor.')
    ]);
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

if ($VerifyWith == 'Authenticator') {

    $UserSecret = $MeQuery['authenticator_id'];
    $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

    if ($g->getCode($UserSecret) != $PinCode) {
        echo StaticFunctions::ApiJson([
            'process' => 'fail',
            'title' => StaticFunctions::lang('Pin kodu geçersiz!'),
            'message' => StaticFunctions::lang('Pin kodu geçersiz görünüyor. Lütfen kontrol ederek tekrar dene.')
        ]);
        exit;
    }

    $SecureClass->DeleteOldPins($Layer);
    $SecureClass->SessionValidated($Layer);

    echo StaticFunctions::ApiJson([
        'process' => 'success',
        'title' => StaticFunctions::lang('Kimliğin doğrulandı!'),
        'message' => StaticFunctions::lang('Doğrulama için teşekkürler, işlemini hemen tamamlıyorum..'),
        'callbackJs' => ''
    ]);
    exit;
} else {
    require_once CORE_DIR . '/ajax/SecurityPinVerify.php';
}