<?php


StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

AppNotifications::ReadAllNotifications($Me, $db);

echo StaticFunctions::JsonOutput([
    'process' => 'success'
]);