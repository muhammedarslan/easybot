<?php

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

$Session = $_SESSION['UserSession'];

echo StaticFunctions::ApiJson([
    'userEmail' => $Session->email,
    'userName' => $Session->real_name,
    'userID' => $Me,
]);