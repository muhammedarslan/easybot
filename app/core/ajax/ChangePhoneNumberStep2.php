<?php

// $PinObjectData

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();
$Me = StaticFunctions::get_id();

if (!isset($IsCgi) || !$IsCgi) {
    http_response_code(401);
    exit;
}

$User = $db->query("SELECT * FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
if (!$User) {
    StaticFunctions::LogOut();
    http_response_code(401);
    exit;
}

$PhoneCode   = $PinObjectData['PhoneCode'];
$PhoneNumber = $PinObjectData['PhoneNumber'];

$UpdateUserNumber = $db->prepare("UPDATE users SET
phone_code = :p1,
phone_number = :p2,
phone_verify = :p3
WHERE id = :uids and status=1 ");
$update = $UpdateUserNumber->execute(array(
    "p1" => $PhoneCode,
    "p2" => $PhoneNumber,
    "p3" => 1,
    "uids" => $Me
));

StaticFunctions::AddLog(['PhoneNumberChanged' => [
    'UserId' => $Me,
    'OldNumber' => $User['phone_code'] . $User['phone_number'],
    'NewNumber' => $PhoneCode . $PhoneNumber,
    'UserIp' => StaticFunctions::get_ip(),
    'UserBrowser' => StaticFunctions::getBrowser()
]], $User['id']);

$_SESSION['UserSession']->phone_code = $PhoneCode;
$_SESSION['UserSession']->phone_number = $PhoneNumber;


$CallbackJs = 'PhoneNumberChanged';