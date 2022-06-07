<?php

use chillerlan\QRCode\{QRCode, QROptions};


StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

$Me = StaticFunctions::get_id();
$User = $db->query("SELECT authenticator_id,id,email,push_id,token FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
if (!$User) {
    $_SESSION['CheckSession'] = false;
    if (isset($_SESSION['CheckSession'])) :
        $Me = StaticFunctions::get_id();
        $RememberToken = isset($_COOKIE['RMB']) ? StaticFunctions::clear($_COOKIE['RMB']) : null;

        if ($RememberToken != null) :
            $delete = $db->exec("DELETE FROM remember_me WHERE user_id= '{$Me}' and remember_token = '{$RememberToken}' ");
            setcookie("RMB", 'null', time() + 604801, '/', DOMAIN, false, true);
        endif;
        session_destroy();
    endif;
    http_response_code(401);
    exit;
}


if ($User['push_id'] != '') {
    $FcmID =  $User['push_id'];
    $FcmToken = $db->query("SELECT id FROM fcm_devices WHERE id = '{$FcmID}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
    if ($FcmToken) {
        echo StaticFunctions::ApiJson([
            'showModal' => false,
            'ShowMessage' => true,
            'Title' => StaticFunctions::lang('Bir hata oluştu!'),
            'Message' => StaticFunctions::lang('Bildirimler zaten ayarlanmış. Cihazın ile ilgili bir sorun varsa bizimle iletişime geçebilirsin.')
        ]);
        exit;
    }
}


$Pn1 = rand(1, 9);
$Pn2 = rand(0, 9);
$Pn3 = rand(0, 9);
$Pn4 = rand(0, 9);
$Pn5 = rand(0, 9);
$Pn6 = rand(1, 9);
$PinCode = $Pn1 . $Pn2 . $Pn3 . $Pn4 . $Pn5 . $Pn6;

$DeletePins = $db->exec("DELETE FROM pin_codes WHERE process_type='push_notification' and user_id='{$Me}' ");
$InsertPin = $db->prepare("INSERT INTO pin_codes SET
            user_id = ?,
            pin_code = ?,
            process_type = ?,
            process_data = ?,
            last_time = ?");
$insert = $InsertPin->execute(array(
    $Me, $PinCode, 'push_notification', null, time() + (60 * 30)
));

$payload = array(
    'UserId' => $Me,
    'SendPin' => true,
    'TokenExpire' => time() + (60 * 10)
);
$jwt = \Firebase\JWT\JWT::encode($payload, StaticFunctions::JwtKey());
$QrUrlSet = PROTOCOL . PUSH_DOMAIN . '/set/' . $jwt . '/' . $User['token'];

$qrcode = new QRCode();
$QrUrl =  $qrcode->render($QrUrlSet);
echo StaticFunctions::ApiJson([
    'showModal' => true,
    'ShowMessage' => false,
    'QrCodeUrl' => $QrUrl
]);