<?php

StaticFunctions::new_session();

if (!isset($_SESSION['SessionValidateResetPass']) || $_SESSION['SessionValidateResetPass'] != 'validated') {
    require_once VDIR . '/validate.reset.pass.php';
    exit;
}


$N = time();
$CheckToken = $db->query("SELECT * FROM reset_password WHERE reset_token='{$token}' and reset_time > $N ")->fetch(PDO::FETCH_ASSOC);

if ($CheckToken) {

    $Uid = $CheckToken['user_id'];
    $CheckUser = $db->query("SELECT * FROM users WHERE id='{$Uid}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
    if ($CheckUser) {
        require_once VDIR . '/reset.pass.php';
        exit;
    }
}

$_SESSION['SessionValidateResetPass'] = false;
unset($_SESSION['SessionValidateResetPass']);
StaticFunctions::go_home();
exit;