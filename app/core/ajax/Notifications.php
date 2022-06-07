<?php

use SendGrid\Stats\Stats;

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();
$Me = StaticFunctions::get_id();

$NotifArray =  (array)(json_decode(AppNotifications::GetNotifications($Me, $db)));

if ($_SESSION['FailedLoginCount'] != 'false') :
    $FailedLoginCount = $_SESSION['FailedLoginCount'];
    $NotifArray['FailedLoginTexts'] = [
        'label' => StaticFunctions::lang('Güvenlik Uyarısı'),
        'text' => StaticFunctions::lang('Hesabınıza <strong>{0}</strong> adet başarısız giriş denemesi yapıldı. Detaylı bilgi için bu uyarıya tıklayınız.', [$FailedLoginCount])
    ];
else :
    $FailedLoginCount = 0;
endif;

$_SESSION['FailedLoginCount'] = 'false';
$NotifArray['FailedLogin'] = $FailedLoginCount;

$MeQuery = $db->query("SELECT phone_verify FROM users WHERE id = '{$Me}' and status='1' ")->fetch(PDO::FETCH_ASSOC);
if (!$MeQuery) {
    StaticFunctions::new_session();
    if (isset($_SESSION['CheckSession'])) :
        $Me = StaticFunctions::get_id();
        $RememberToken = isset($_COOKIE['RMB']) ? StaticFunctions::clear($_COOKIE['RMB']) : null;

        if ($RememberToken != null) :
            $delete = $db->exec("DELETE FROM remember_me WHERE user_id= '{$Me}' and remember_token = '{$RememberToken}' ");
            setcookie("RMB", 'null', time() + 604801, '/', DOMAIN, false, true);
        endif;
        session_destroy();
    endif;
    echo 'SessionDestroyed';
    exit;
}

if ($MeQuery['phone_verify'] != 1) {
    $NotifArray['NotificationCount'] = $NotifArray['NotificationCount'] + 1;
    $NotifArray['NotificationCountText'] = $NotifArray['NotificationCount'] . ' ' . StaticFunctions::lang('okunmamış');
    array_unshift($NotifArray['Notifications'], [
        'title' => StaticFunctions::lang('Hesabınızı Yükseltin'),
        'text' => StaticFunctions::lang('Tam erişim için hesabınızı yükseltin.'),
        'label' => [
            'icon' => 'icon-check-circle',
            'type' => 'primary'
        ],
        'time' => StaticFunctions::lang('her zaman'),
        'token' => 'verify'
    ]);
}

echo json_encode($NotifArray);
