<?php

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

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