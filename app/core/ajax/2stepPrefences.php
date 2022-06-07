<?php

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

$Me = StaticFunctions::get_id();
$User = $db->query("SELECT * FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
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

$Status = [
    'PinValidate' => false,
    't2step' => [
        'isActive' => [
            'active' => false,
            'text' => StaticFunctions::lang('2 adımlı doğrulama pasif')
        ],
        'channels' => [
            'email' => true,
            'sms' => true,
            'google' => false,
            'notification' => false
        ],
        'modals' => [
            'google' => false,
            'notification' => false
        ]
    ]
];

$PostData = StaticFunctions::post('channels');

if ($PostData['active'] == 'false') {

    if ($User['2step_verification'] == 1) {
        require_once CDIR . '/class.pin.verify.php';
        $PinVerify = new PinCodeVerification();
        $PinVerify->setDb($db);
        $PinVerify->setUserId($Me);
        $SendPin = $PinVerify->VerifyProcess('2step_off', [
            'require' => '2StepOff',
            'withData' => []
        ]);
        echo json_encode([
            'PinValidate' => 'required',
            'PinInfo' => $SendPin
        ]);
        exit;
    }

    $Status['t2step']['isActive']['active'] = false;
    $Status['t2step']['isActive']['text'] = StaticFunctions::lang('2 adımlı doğrulama pasif');
    $Status['t2step']['channels']['email'] = false;
    $Status['t2step']['channels']['sms'] = false;
    $Status['t2step']['channels']['google'] = false;
    $Status['t2step']['channels']['notification'] = false;
    $Status['t2step']['modals']['google'] = false;
    $Status['t2step']['modals']['notification'] = false;
} else {

    if ($User['2step_verification'] == 0) {
        $Active2Step = $db->prepare("UPDATE users SET
        2step_verification = :2step_v
        WHERE id = :uids and status=1 ");
        $update = $Active2Step->execute(array(
            "2step_v" => 1,
            "uids" => $Me
        ));
    }

    // Open Google.
    if ($PostData['google'] == 'true' && $User['2step_authenticator'] == 0) {
        $Edit2Step = $db->prepare("UPDATE users SET
        2step_authenticator = :2step_v
        WHERE id = :uids and status=1 ");
        $update = $Edit2Step->execute(array(
            "2step_v" => 1,
            "uids" => $Me
        ));
    }

    // Close Google.
    if ($PostData['google'] == 'false' && $User['2step_authenticator'] == 1) {
        $Edit2Step = $db->prepare("UPDATE users SET
        2step_authenticator = :2step_v
        WHERE id = :uids and status=1 ");
        $update = $Edit2Step->execute(array(
            "2step_v" => 0,
            "uids" => $Me
        ));
    }

    // Open Notification.
    if ($PostData['notification'] == 'true' && $User['2step_push'] == 0) {
        $Edit2Step = $db->prepare("UPDATE users SET
        2step_push = :2step_v
        WHERE id = :uids and status=1 ");
        $update = $Edit2Step->execute(array(
            "2step_v" => 1,
            "uids" => $Me
        ));
    }

    // Close Notification.
    if ($PostData['notification'] == 'false' && $User['2step_push'] == 1) {
        $Edit2Step = $db->prepare("UPDATE users SET
        2step_push = :2step_v
        WHERE id = :uids and status=1 ");
        $update = $Edit2Step->execute(array(
            "2step_v" => 0,
            "uids" => $Me
        ));
    }


    $User = $db->query("SELECT * FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
    $Status['t2step']['isActive']['active'] = true;
    $Status['t2step']['isActive']['text'] = StaticFunctions::lang('2 adımlı doğrulama aktif');
    $Status['t2step']['channels']['email'] = true;
    $Status['t2step']['channels']['sms'] = true;

    $GoogleAuth = false;
    $Notification = false;
    $GoogleModal = false;
    $NotifModal = false;

    if ($User['2step_authenticator'] == 1) {
        $GoogleAuth = true;
    }

    if ($User['authenticator_id'] == '') {
        $GoogleAuth = false;
        $GoogleModal = true;
    }

    if ($User['2step_push'] == 1) {
        $Notification = true;
    }

    if ($User['push_id'] == '') {
        $Notification = false;
        $NotifModal = true;
    }

    if ($PostData['google'] == 'false') {
        $GoogleModal = false;
    }

    if ($PostData['notification'] == 'false') {
        $NotifModal = false;
    }

    if ($Notification) {
        $FcmID =  $User['push_id'];
        $FcmToken = $db->query("SELECT id FROM fcm_devices WHERE id = '{$FcmID}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
        if (!$FcmToken) {
            $Notification = false;
            $NotifModal = true;
        }
    }

    $Status['t2step']['channels']['google'] = $GoogleAuth;
    $Status['t2step']['channels']['notification'] = $Notification;
    $Status['t2step']['modals']['google'] = $GoogleModal;
    $Status['t2step']['modals']['notification'] = $NotifModal;
}


echo json_encode($Status);