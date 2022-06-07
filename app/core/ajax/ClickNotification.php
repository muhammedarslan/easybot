<?php

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();
$Me = StaticFunctions::get_id();

$Url = AppNotifications::SingleNotificationUrl($Me, $db, StaticFunctions::post('tkn'));

echo $Url;