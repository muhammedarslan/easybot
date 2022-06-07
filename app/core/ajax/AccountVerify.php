<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

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

$PhoneNumber = StaticFunctions::post('phoneNumber');
$PhoneNumber = str_replace([' ', '_', '-'], '', $PhoneNumber);
$PhoneNumber = '+' . $PhoneNumber;


if ($PhoneNumber == '') {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Telefon numarası geçersiz görünüyor.')
    ]);
    exit;
}

if (mb_substr($PhoneNumber, 0, 2) == '+90') {
    if (mb_substr($PhoneNumber, 0, 3) == '+900') {
        echo StaticFunctions::ApiJson([
            'process' => 'fail',
            'title' => StaticFunctions::lang('Bir hata oluştu!'),
            'message' => StaticFunctions::lang('Telefon numarası geçersiz görünüyor.')
        ]);
        exit;
    }
}

try {
    $number = PhoneNumber::parse($PhoneNumber);
} catch (PhoneNumberParseException $e) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Telefon numarası geçersiz görünüyor.')
    ]);
    exit;
}

if (!$number->isPossibleNumber()) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Telefon numarası geçersiz görünüyor.')
    ]);
    exit;
}

if (!$number->isValidNumber()) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Telefon numarası geçersiz görünüyor.')
    ]);
    exit;
}


$PhoneCode   = '+' . $number->getCountryCode();
$PhoneNumber = $number->getNationalNumber();

$CheckUnique = $db->query("SELECT id FROM users WHERE phone_code='{$PhoneCode}' and phone_number='{$PhoneNumber}' ")->fetch(PDO::FETCH_ASSOC);
if ($CheckUnique) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Bu telefon numarası başka bir hesapla doğrulanmış.<br> Güvenlik nedeniyle her telefon numarası sadece 1 hesap ile doğrulanabilir.')
    ]);
    exit;
}

$VerifyNumber = $PhoneCode . $PhoneNumber;

$Now = time();
$IsSend = $db->query("SELECT * FROM phone_verify WHERE user_id = '{$Me}' and phone_code = '{$PhoneCode}' and phone_number = '{$PhoneNumber}' and sended_time > $Now ")->fetch(PDO::FETCH_ASSOC);

if ($IsSend) {
    echo StaticFunctions::ApiJson([
        'process' => 'fail',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Yeni bir kod almadan önce lütfen en az 2 dakika bekleyiniz.')
    ]);
    exit;
}



$Pn1 = rand(1, 9);
$Pn2 = rand(0, 9);
$Pn3 = rand(0, 9);
$Pn4 = rand(0, 9);
$Pn5 = rand(0, 9);
$Pn6 = rand(1, 9);
$PinCode = $Pn1 . $Pn2 . $Pn3 . $Pn4 . $Pn5 . $Pn6;

$DeletePins = $db->exec("DELETE FROM phone_verify WHERE user_id='{$Me}' ");
$InsertPin = $db->prepare("INSERT INTO phone_verify SET
            user_id = ?,
            pin_code = ?,
            phone_code = ?,
            phone_number = ?,
            sended_time = ?");
$insert = $InsertPin->execute(array(
    $Me, $PinCode, $PhoneCode, $PhoneNumber, time() + (60 * 3)
));

require_once CDIR . '/class.communication.php';
$Comm = new EasyBotSend();
$FirstNameExplode = explode(' ', $PhoneVerify['real_name']);

if ($Comm->Sms(
    $PhoneVerify['id'],
    StaticFunctions::lang('Selam {0}, {1} kodu ile Easybot hesabını doğrulayabilirsin.', [
        $FirstNameExplode[0], $PinCode
    ]),
    [$PinCode],
    [
        'PhoneCode' => $PhoneCode,
        'PhoneNumber' => $PhoneNumber
    ]
)) {
    echo StaticFunctions::ApiJson([
        'process' => 'success',
        'title' => StaticFunctions::lang('Başarıyla gönderildi!'),
        'message' => StaticFunctions::lang('Hesabını doğrulaman için pin kodun telefon numarana başarıyla gönderildi. Lütfen telefonunu kontrol et.'),
        'PhoneNumber' => $number->format(PhoneNumberFormat::INTERNATIONAL)
    ]);
    exit;
} else {
    http_response_code(401);
}