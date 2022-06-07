<?php

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

$Me = StaticFunctions::get_id();
$User = $db->query("SELECT authenticator_id,id,email,push_id FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
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

$ValidFcm = false;

if ($User['push_id'] != '') {
    $FcmID =  $User['push_id'];
    $FcmToken = $db->query("SELECT id FROM fcm_devices WHERE id = '{$FcmID}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
    if ($FcmToken) {
        $ValidFcm = true;
    }
}

if ($ValidFcm) {
    echo StaticFunctions::ApiJson([
        'process' => 'failed',
        'callbackJs' => '',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Mobil cihazında bildirimleri açtığına emin misin?')
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

$GetUserSecret = $db->query("SELECT * FROM pin_codes WHERE user_id = '{$Me}' and process_type='push_notification' and process_data != '' order by id DESC  ")->fetch(PDO::FETCH_ASSOC);
if (!$GetUserSecret) {
    echo StaticFunctions::ApiJson([
        'process' => 'failed',
        'callbackJs' => '',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Mobil cihazında bildirimleri açtığına emin misin?')
    ]);
    exit;
}

$UserFcmID = json_decode($GetUserSecret['process_data'], true)['FcmDeviceID'];

if ($GetUserSecret['pin_code'] != $PinCode) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'callbackJs' => '',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Pin kodu geçersiz görünüyor.')
    ]);
    exit;
}


$DeletePins = $db->exec("DELETE FROM pin_codes WHERE process_type='push_notification' and user_id='{$Me}' ");
$Active2Step = $db->prepare("UPDATE users SET
        push_id = :2step_v
        WHERE id = :uids and status=1 ");
$update = $Active2Step->execute(array(
    "2step_v" => $UserFcmID,
    "uids" => $Me
));

echo StaticFunctions::ApiJson([
    'process' => 'success',
    'callbackJs' => 'FastRefresh2Step',
    'title' => StaticFunctions::lang('Başarıyla tamamlandı!'),
    'message' => StaticFunctions::lang('Mobil bildirimler hesabınız için başarıyla aktif hale getirildi.')
]);
exit;