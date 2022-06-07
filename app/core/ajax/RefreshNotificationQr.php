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
        http_response_code(401);
    }
}


$payload = array(
    'UserId' => $Me,
    'SendPin' => true,
    'TokenExpire' => time() + (60 * 10)
);
$jwt = \Firebase\JWT\JWT::encode($payload, StaticFunctions::JwtKey());
$QrUrlSet = PROTOCOL . PUSH_DOMAIN . '/set/' . $jwt . '/' . $User['token'];

$qrcode = new QRCode();
$QrUrl =  $qrcode->render($QrUrlSet);

echo $QrUrl;