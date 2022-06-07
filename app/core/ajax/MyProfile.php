<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

$Uid = StaticFunctions::get_id();
$User = $db->query("SELECT * FROM users WHERE id = '{$Uid}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
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

$PhoneNumber = $User['phone_code'] . $User['phone_number'];

try {
    $number = PhoneNumber::parse($PhoneNumber);
    $PhnNumber = $number->format(PhoneNumberFormat::INTERNATIONAL);
} catch (PhoneNumberParseException $e) {
    $PhnNumber = StaticFunctions::lang('Henüz eklenmedi.');
}

$NameExplode = explode(' ', $User['real_name']);

if (count($NameExplode) == 1) {
    $FirstName = $User['real_name'];
    $LastName = '';
} else {
    $FirstName = '';
    $LastName  = end($NameExplode);
    $LsKey =  array_key_last($NameExplode);
    unset($NameExplode[$LsKey]);
    $FirstName = implode(' ', $NameExplode);

    if ($FirstName == $LastName) {
        $LastName = '';
    }
}

if ($User['phone_verify'] == 1) {
    $ValidateText = StaticFunctions::lang('Seviye 2 - Doğrulanmış');
} else {
    $ValidateText = StaticFunctions::lang('Telefon onayı bekliyor..');
}

if ($User['2step_verification'] == 1) {
    $T2Step = [
        'active' => true,
        'text' => StaticFunctions::lang('2 adımlı doğrulama aktif')
    ];
} else {
    $T2Step = [
        'active' => false,
        'text' => StaticFunctions::lang('2 adımlı doğrulama pasif')
    ];
}

$GoogleAuth = false;
$Notification = false;

if ($User['2step_authenticator'] == 1) {
    $GoogleAuth = true;
}

if ($User['authenticator_id'] == '') {
    $GoogleAuth = false;
}

if ($User['2step_push'] == 1) {
    $Notification = true;
}

if ($User['push_id'] == '') {
    $Notification = false;
} else {
    $FcmID =  $User['push_id'];
    $FcmToken = $db->query("SELECT id FROM fcm_devices WHERE id = '{$FcmID}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
    if (!$FcmToken) {
        $Notification = false;
    }
}

$UserAvatar = $User['avatar'];
if (mb_substr($UserAvatar, 0, 4) == 'http') {
    $AvatarUrl = $UserAvatar;
} else {
    $AvatarUrl = '/assets/media/avatars/' . $UserAvatar;
}

$BannedSocials = [
    'Google' => [
        'isActive' => true,
    ],
    'Github' => [
        'isActive' => true,
    ],
    'Linkedin' => [
        'isActive' => true,
    ],
    'Facebook' => [
        'isActive' => true,
    ]
];

$UserPrefences = json_decode($User['user_prefences'], true);

if (isset($UserPrefences['BannedSocials'])) {

    if (isset($UserPrefences['BannedSocials']['Google']) && $UserPrefences['BannedSocials']['Google'] == 'banned') {
        $BannedSocials['Google']['isActive'] = false;
    }
    if (isset($UserPrefences['BannedSocials']['Github']) && $UserPrefences['BannedSocials']['Github'] == 'banned') {
        $BannedSocials['Github']['isActive'] = false;
    }
    if (isset($UserPrefences['BannedSocials']['Linkedin']) && $UserPrefences['BannedSocials']['Linkedin'] == 'banned') {
        $BannedSocials['Linkedin']['isActive'] = false;
    }
    if (isset($UserPrefences['BannedSocials']['Facebook']) && $UserPrefences['BannedSocials']['Facebook'] == 'banned') {
        $BannedSocials['Facebook']['isActive'] = false;
    }
}

$IsAllActive = false;
$IsActiveLabel = StaticFunctions::lang('Sosyal medya ile hızlı giriş pasif');

foreach ($BannedSocials as $key => $value) {
    if ($value['isActive'] == true) {
        $IsAllActive = true;
        $IsActiveLabel = StaticFunctions::lang('Sosyal medya ile hızlı giriş aktif');
        break;
    }
}

$IsDefaultAvatar = true;

if (mb_substr($AvatarUrl, 0, 4) == 'http') {
    $IsDefaultAvatar = false;
}

echo json_encode([
    'avatar' => $AvatarUrl,
    'isDefaultAvatar' => $IsDefaultAvatar,
    'profile' => [
        'name' => StaticFunctions::say($FirstName),
        'surname' => StaticFunctions::say($LastName),
        'fullname' => StaticFunctions::say($User['real_name']),
        'email' => StaticFunctions::say($User['email']),
        'level' => StaticFunctions::say($ValidateText),
        'phone_mumber' => $PhnNumber,
        'phone_number_nomask' => $PhoneNumber,
        'pastedtime' => StaticFunctions::lang('{0} birlikte vakit geçirdik.', [
            StaticFunctions::timerFormat($User['created_time'], time())
        ]),
        'AppMode' => AppMode,
        'MenuMode' => MenuType,
        'AppLang' => LANG,
    ],
    't2step' => [
        'isActive' => $T2Step,
        'channels' => [
            'email' => true,
            'sms' => true,
            'google' => $GoogleAuth,
            'notification' => $Notification
        ]
    ],
    'socialLoginActive' => $IsAllActive,
    'socialLoginLabel' => $IsActiveLabel,
    'bannedSocials' => $BannedSocials
]);
