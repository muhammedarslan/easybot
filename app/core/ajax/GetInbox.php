<?php

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();
StaticFunctions::PagePasswordValidate('Inbox');
$Items = [];

$GetEmails = $db->query("SELECT email_variables,email_token,sended_time FROM sended_mails WHERE user_id='$Me' order by id DESC LIMIT 50 ", PDO::FETCH_ASSOC);
if ($GetEmails->rowCount()) {
    foreach ($GetEmails as $row) {
        $Js = json_decode($row['email_variables']);

        array_push($Items, [
            'itemToken' => $row['email_token'],
            'Email' => [
                'Subject' => stripslashes(StaticFunctions::clear($Js->SUBJECT)),
                'Time' => StaticFunctions::timerFormat($row['sended_time'], time()) . ' ' . StaticFunctions::lang('önce'),
                'Text' => stripslashes(StaticFunctions::clear($Js->PRE_HEADER)) . ' ' . stripslashes(StaticFunctions::clear($Js->WELCOME_TEXT))

            ]
        ]);
    }
}

echo json_encode([
    'status' => 'success',
    'itemCount' => count($Items),
    'items' => $Items
]);