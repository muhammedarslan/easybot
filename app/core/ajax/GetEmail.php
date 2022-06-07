<?php

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();
StaticFunctions::PagePasswordValidate('Inbox');
$token = StaticFunctions::post('token');


$GetSingleMail = $db->query("SELECT * FROM sended_mails WHERE user_id = '{$Me}' and email_token='{$token}' ")->fetch(PDO::FETCH_ASSOC);
if (!$GetSingleMail) {
    echo StaticFunctions::JsonOutput([
        'process' => 'failure'
    ]);
    exit;
}

StaticFunctions::new_session();
$_SESSION['SessionValidateBrowseEmail'] = 'validated';

$Js = json_decode($GetSingleMail['email_variables']);

echo StaticFunctions::JsonOutput([
    'process' => 'success',
    'title' => stripslashes(StaticFunctions::clear($Js->SUBJECT)),
    'time1' => StaticFunctions::timerFormat($GetSingleMail['sended_time'], time()) . ' ' . StaticFunctions::lang('önce'),
    'time2' => date('d-m-Y H:i:s', $GetSingleMail['sended_time']),
    'token' => $GetSingleMail['email_token']
]);