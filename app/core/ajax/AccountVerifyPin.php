<?php


StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

$PhoneVerify = $db->query("SELECT phone_verify,real_name,id FROM users WHERE id = '{$Me}' and status='1' ")->fetch(PDO::FETCH_ASSOC);
if (!$PhoneVerify) {
    StaticFunctions::LogOut();
    http_response_code(401);
    exit;
}

if ($PhoneVerify['phone_verify'] == 1) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Hesabınız zaten onaylanmış, teşekkür ederiz.')
    ]);
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


$Now = time();
$LastPin = $db->query("SELECT * FROM phone_verify WHERE user_id='{$Me}' and sended_time > $Now order by id DESC LIMIT 1 ")->fetch(PDO::FETCH_ASSOC);
if (!$LastPin) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Pin kodu hatalı!'),
        'message' => StaticFunctions::lang('Pin kodu geçersiz görünüyor. Kodu kontrol ederek tekrar deneyebilir veya yeni bir kod isteyebilirsin.')
    ]);
    exit;
}

if ($LastPin['pin_code'] != $PinCode) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Pin kodu hatalı!'),
        'message' => StaticFunctions::lang('Pin kodu geçersiz görünüyor. Kodu kontrol ederek tekrar deneyebilir veya yeni bir kod isteyebilirsin.')
    ]);
    exit;
}

$PhoneCode = $LastPin['phone_code'];
$PhoneNumber = $LastPin['phone_number'];

$DeletePins = $db->exec("DELETE FROM phone_verify WHERE user_id='{$Me}' ");

$UpdateUserNumber = $db->prepare("UPDATE users SET
phone_code = :p1,
phone_number = :p2,
phone_verify = :p3
WHERE id = :uids and status=1 ");
$update = $UpdateUserNumber->execute(array(
    "p1" => $PhoneCode,
    "p2" => $PhoneNumber,
    "p3" => 1,
    "uids" => $Me
));

echo StaticFunctions::ApiJson([
    'process' => 'success',
    'title' => StaticFunctions::lang('Hesabınız doğrulandı!'),
    'message' => StaticFunctions::lang('Doğrulama için teşekkür ederiz. Easybot\'un keyfini çıkarabilirsin.')
]);
exit;