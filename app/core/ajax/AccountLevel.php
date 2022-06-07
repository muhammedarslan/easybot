<?php

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

$IsAccountVerify = $db->query("SELECT phone_verify from users WHERE id='{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

if (!$IsAccountVerify) {
    StaticFunctions::LogOut();
    http_response_code(401);
    exit;
}

$IsVerified = ($IsAccountVerify['phone_verify'] == 1) ? true : false;

echo StaticFunctions::ApiJson([
    'process' => 'success',
    'timeUnix' => time(),
    'isVerified' => $IsVerified
]);