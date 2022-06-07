<?php


StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

$CheckUser = $db->query("SELECT id FROM users WHERE id = '{$Me}' and status='1' ")->fetch(PDO::FETCH_ASSOC);
if (!$CheckUser) {
    StaticFunctions::LogOut();
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

$Now = time();
$CheckPin = $db->query("SELECT * FROM pin_codes WHERE user_id = '{$Me}' and pin_code='{$PinCode}' and last_time > $Now ")->fetch(PDO::FETCH_ASSOC);
if (!$CheckPin) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Pin kodu geçersiz!'),
        'message' => StaticFunctions::lang('Pin kodu geçersiz görünüyor. Lütfen kontrol ederek tekrar dene.')
    ]);
    exit;
}

$PinObject = json_decode($CheckPin['process_data'], true);

$Ptype = $CheckPin['process_type'];
$DeletePins = $db->exec("DELETE FROM pin_codes WHERE user_id='{$Me}' and process_type='{$Ptype}' ");
$CallbackJs = '';
if (isset($PinObject['require'])) {
    $PinObjectData = $PinObject['withData'];
    $IsCgi = true;
    require_once CORE_DIR . '/ajax/' . $PinObject['require'] . '.php';
}

echo StaticFunctions::ApiJson([
    'process' => 'success',
    'title' => StaticFunctions::lang('Kimliğin doğrulandı!'),
    'message' => StaticFunctions::lang('Doğrulama için teşekkürler, işlemini hemen tamamlıyorum..'),
    'callbackJs' => $CallbackJs
]);
exit;