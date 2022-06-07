<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

if (
    StaticFunctions::post('f_name') == '' ||
    StaticFunctions::post('f_app_mode') == '' ||
    StaticFunctions::post('f_surname') == '' ||
    StaticFunctions::post('f_site_lang') == ''
) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'process' => 'failure',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Lütfen tüm zorunlu alanları doldur.')
    ]);
    exit;
}

if (StaticFunctions::post('f_app_mode') != 'Light' && StaticFunctions::post('f_app_mode') != 'Dark') {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'process' => 'failure',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Lütfen tüm zorunlu alanları doldur.')
    ]);
    exit;
}
$LangsArray = AppLanguage::$AllowedLangs;
if (!isset($LangsArray[StaticFunctions::post('f_site_lang')])) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'process' => 'failure',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Lütfen tüm zorunlu alanları doldur.')
    ]);
    exit;
}

$UserFullName = StaticFunctions::post('f_name') . ' ' . StaticFunctions::post('f_surname');

$Me = StaticFunctions::get_id();
$User = $db->query("SELECT * FROM users WHERE id='{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

if (!$User) {
    StaticFunctions::LogOut();
    http_response_code(401);
    exit;
}

$ResponseArray = [
    'status' => 'processing',
    'openAccountValidateModal' => false,
    'accountValidatePhoneNumber' => null,
    'pinVerifyModal' => false,
    'pinVerifyInfo' => false,
    'passwordShowToast' => false,
    'passwordToastType' => null,
    'passwordToast' => [
        'title' => null,
        'message' => null
    ],
    'phoneErrorToast' => false,
    'phoneErrorToastTitle' => null,
    'phoneErrorToastMessage' => null,
    'title' => null,
    'message' => null,
    'ChangedLang' => null,
    'ChangeAppMode' => false,
    'ChangeMenuMode' => false,
    'ChangeLanguage' => false
];

$PhoneNumber = StaticFunctions::post('f_tel');
$PhoneNumber = str_replace([' ', '_', '-'], '', $PhoneNumber);
$PhoneNumber = '+' . $PhoneNumber;
$IsPhoneNumberValid = false;

if (StaticFunctions::post('f_tel') != '') {
    try {
        $number = PhoneNumber::parse($PhoneNumber);
        if ($number->isPossibleNumber()) {
            if ($number->isValidNumber()) {
                $IsPhoneNumberValid = true;
            }
        }
    } catch (PhoneNumberParseException $e) {
    }
}

if ($IsPhoneNumberValid) {
    if ($User['phone_verify'] != 1) {
        // User added phone number first time.
        $ResponseArray['openAccountValidateModal'] = true;
        $ResponseArray['accountValidatePhoneNumber'] = $PhoneNumber;
    } else {
        if ($PhoneNumber != ($User['phone_code'] . $User['phone_number'])) {
            // User want to change phone number.
            $VerifyPhoneCode   = '+' . $number->getCountryCode();
            $VerifyPhoneNumber = $number->getNationalNumber();

            $CheckUnique = $db->query("SELECT id FROM users WHERE phone_code='{$VerifyPhoneCode}' and phone_number='{$VerifyPhoneNumber}' ")->fetch(PDO::FETCH_ASSOC);
            if ($CheckUnique) {
                $ResponseArray['phoneErrorToast'] = true;
                $ResponseArray['phoneErrorToastTitle'] = StaticFunctions::lang('Telefon numaran değiştirilemedi!');
                $ResponseArray['phoneErrorToastMessage'] = StaticFunctions::lang('Bu telefon numarası başka bir hesapla zaten doğrulanmış. Güvenlik nedeniyle her telefon numarası sadece 1 hesap ile doğrulanabilir.');
            } else {
                try {
                    require_once CDIR . '/class.pin.verify.php';
                    $PinVerify = new PinCodeVerification();
                    $PinVerify->setDb($db);
                    $PinVerify->setUserId($Me);
                    $PinPhoneCode   = '+' . $number->getCountryCode();
                    $PinPhoneNumber = $number->getNationalNumber();
                    $SendPin = $PinVerify->VerifyProcess('change_phone_number_step1', [
                        'require' => 'ChangePhoneNumberStep1',
                        'withData' => [
                            'PhoneCode' => $PinPhoneCode,
                            'PhoneNumber' => $PinPhoneNumber
                        ]
                    ]);
                    $ResponseArray['pinVerifyModal'] = true;
                    $ResponseArray['pinVerifyInfo'] = $SendPin;
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
        }
    }
}

if (mb_strtolower(StaticFunctions::post('f_app_mode')) != mb_strtolower(AppMode)) {
    $ResponseArray['ChangeAppMode'] = true;
}

if (mb_strtolower(StaticFunctions::post('f_site_lang')) != mb_strtolower(LANG)) {
    $ResponseArray['ChangeLanguage'] = true;
    $ResponseArray['ChangedLang'] = StaticFunctions::post('f_site_lang');
}

$UpdateProfile = $db->prepare("UPDATE users SET
        real_name = :i
        WHERE id = :uids and status=1 ");
$update = $UpdateProfile->execute(array(
    "i" => $UserFullName,
    "uids" => $Me
));


if (StaticFunctions::post('f_new_password') != '' && StaticFunctions::post('f_old_password') != '') {
    if (StaticFunctions::post('f_new_password') == StaticFunctions::post('f_new_password2')) {

        if ($User['password'] == StaticFunctions::password(StaticFunctions::post('f_old_password'))) {
            $NewPassword = StaticFunctions::post('f_new_password');
            if (mb_strlen($NewPassword) > 6) {

                $NewPasswordHashed = StaticFunctions::password($NewPassword);
                $delete = $db->exec("DELETE FROM remember_me WHERE user_id = '$Me' ");
                $delete = $db->exec("DELETE FROM reset_password WHERE user_id = '$Me' ");

                $ChangeUserPassword = $db->prepare("UPDATE users SET
                     password   = :iki
                     WHERE id = :dort and status=1 ");
                $update = $ChangeUserPassword->execute(array(
                    'iki' => $NewPasswordHashed,
                    'dort' => $User['id']
                ));

                StaticFunctions::AddLog(['PasswordChanged' => [
                    'UserId' => $User['id'],
                    'UserIp' => StaticFunctions::get_ip(),
                    'UserBrowser' => StaticFunctions::getBrowser()
                ]], $User['id']);

                if ($User['phone_verify'] == 1) {
                    require_once CDIR . '/class.communication.php';
                    $Comm = new EasyBotSend();
                    $FirstNameExplode = explode(' ', $User['real_name']);
                    $Comm->Sms(
                        $User['id'],
                        StaticFunctions::lang('Selam {0}, Easybot hesabının şifresi az önce değiştirildi. Eğer bu bilgin dahilinde olmadıysa lütfen en kısa sürede http://easybot.dev/contact adresinden bize haber ver.', [
                            $FirstNameExplode[0]
                        ]),
                        []
                    );
                }

                $ResponseArray['passwordShowToast'] = true;
                $ResponseArray['passwordToastType'] = 'success';
                $ResponseArray['passwordToast']['title'] = StaticFunctions::lang('Şifren Başarıyla Değiştirildi!');
                $ResponseArray['passwordToast']['message'] = StaticFunctions::lang('Hesap şifren başarıyla değiştirildi.');
            } else {
                $ResponseArray['passwordShowToast'] = true;
                $ResponseArray['passwordToastType'] = 'warning';
                $ResponseArray['passwordToast']['title'] = StaticFunctions::lang('Şifren Değiştirilemedi!');
                $ResponseArray['passwordToast']['message'] = StaticFunctions::lang('Güvenliğin için en az 6 karakter uzunluğunda bir şifre belirlemelisin.');
            }
        } else {
            $ResponseArray['passwordShowToast'] = true;
            $ResponseArray['passwordToastType'] = 'warning';
            $ResponseArray['passwordToast']['title'] = StaticFunctions::lang('Şifren Değiştirilemedi!');
            $ResponseArray['passwordToast']['message'] = StaticFunctions::lang('Eski şifreni yanlış girdin.');
        }
    } else {
        $ResponseArray['passwordShowToast'] = true;
        $ResponseArray['passwordToastType'] = 'warning';
        $ResponseArray['passwordToast']['title'] = StaticFunctions::lang('Şifren Değiştirilemedi!');
        $ResponseArray['passwordToast']['message'] = StaticFunctions::lang('Girilen yeni şifreler birbiri ile eşleşmiyor.');
    }
}

$ResponseArray['status'] = 'success';
$ResponseArray['title'] = StaticFunctions::lang('Başarıyla Tamamlandı!');
$ResponseArray['message'] = StaticFunctions::lang('Profilin başarıyla kaydedildi.');

$_SESSION['UserSession']->real_name = $UserFullName;

echo StaticFunctions::ApiJson($ResponseArray);