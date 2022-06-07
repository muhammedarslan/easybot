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
        'process' => 'failed',
        'callbackJs' => '',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Bir hata oluştu. Lütfen sayfayı yenileyerek tekrar dene.')
    ]);
    exit;
}

$PinCode = StaticFunctions::post('pin');

if ($PinCode == '' || mb_strlen($PinCode) != 6) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'callbackJs' => '',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Pin kodu geçersiz görünüyor.')
    ]);
    exit;
}

$GetUserSecret = $db->query("SELECT * FROM pin_codes WHERE user_id = '{$Me}' and process_type='google_authenticator' order by id DESC  ")->fetch(PDO::FETCH_ASSOC);
if (!$GetUserSecret) {
    echo StaticFunctions::ApiJson([
        'process' => 'failed',
        'callbackJs' => '',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Bir hata oluştu. Lütfen sayfayı yenileyerek tekrar dene.')
    ]);
    exit;
}

$UserSecret = json_decode($GetUserSecret['process_data'], true)['UserSecret'];

$g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

if ($g->getCode($UserSecret) != $PinCode) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'callbackJs' => '',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Pin kodu geçersiz görünüyor.')
    ]);
    exit;
}

$DeletePins = $db->exec("DELETE FROM pin_codes WHERE process_type='google_authenticator' and user_id='{$Me}' ");

$Active2Step = $db->prepare("UPDATE users SET
        authenticator_id = :2step_v
        WHERE id = :uids and status=1 ");
$update = $Active2Step->execute(array(
    "2step_v" => $UserSecret,
    "uids" => $Me
));

echo StaticFunctions::ApiJson([
    'process' => 'success',
    'callbackJs' => 'FastRefresh2Step',
    'title' => StaticFunctions::lang('Başarıyla tamamlandı!'),
    'message' => StaticFunctions::lang('Google Authenticator hesabın için başarıyla aktif hale getirildi.')
]);
exit;