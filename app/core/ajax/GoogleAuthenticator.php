<?php

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

$Me = StaticFunctions::get_id();
$User = $db->query("SELECT authenticator_id,id,email FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
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


if ($User['authenticator_id'] != '') {
    echo StaticFunctions::ApiJson([
        'showModal' => false,
        'ShowMessage' => true,
        'Title' => StaticFunctions::lang('Bir hata oluştu!'),
        'Message' => StaticFunctions::lang('Google Authenticator zaten ayarlanmış. Gizli anahtarınızı unuttuysanız bizimle iletişime geçin.')
    ]);
    exit;
}

$g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
$UserRandomSecret = $g->generateSecret();
$QrUrl =  \Sonata\GoogleAuthenticator\GoogleQrUrl::generate($User['email'], $UserRandomSecret, 'EasyBot');

$DeletePins = $db->exec("DELETE FROM pin_codes WHERE process_type='google_authenticator' and user_id='{$Me}' ");
$InsertPin = $db->prepare("INSERT INTO pin_codes SET
            user_id = ?,
            pin_code = ?,
            process_type = ?,
            process_data = ?,
            last_time = ?");
$insert = $InsertPin->execute(array(
    $Me, 000000, 'google_authenticator', json_encode([
        'UserSecret' => $UserRandomSecret
    ]), time() + (60 * 10)
));

echo StaticFunctions::ApiJson([
    'showModal' => true,
    'ShowMessage' => false,
    'QrCodeUrl' => $QrUrl,
    'SecretKey' => $UserRandomSecret
]);