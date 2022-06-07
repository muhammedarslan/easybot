<?php

StaticFunctions::ajax_form('private');
$Password = StaticFunctions::password(StaticFunctions::post('password'));
$Me = StaticFunctions::get_id();

$UserQuery = $db->query("SELECT * FROM users WHERE id='{$Me}' and password='{$Password}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

if ($UserQuery) {
    StaticFunctions::new_session();
    $_SESSION['PagePasswordValidate'][$_SESSION['ValidatePageID']] = 'validated';
    echo StaticFunctions::JsonOutput([
        'status' => 'success',
        'callbackJs' => StaticFunctions::post('callbackFunction')
    ]);
    exit;
} else {
    echo StaticFunctions::JsonOutput([
        'status' => 'failed',
        'ErrorText' => StaticFunctions::lang('Lütfen şifrenizi kontrol ediniz.')
    ]);
    exit;
}