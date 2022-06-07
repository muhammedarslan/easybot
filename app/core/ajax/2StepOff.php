<?php

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

if (!isset($IsCgi) || !$IsCgi) {
    http_response_code(401);
    exit;
}


$Disable2Step = $db->prepare("UPDATE users SET
        2step_verification = :2step_v
        WHERE id = :uids and status=1 ");
$update = $Disable2Step->execute(array(
    "2step_v" => 0,
    "uids" => $Me
));

$CallbackJs = 'FastRefresh2Step';