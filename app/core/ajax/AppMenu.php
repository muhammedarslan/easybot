<?php

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();


$MyJson = $db->query("SELECT id,user_prefences FROM users WHERE id='{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

if (!$MyJson) exit;

$MyJsonExtra = (array) json_decode($MyJson['user_prefences']);

$Mode = 'open';

if (isset($MyJsonExtra['MenuClosed'])) {
    $Mode = $MyJsonExtra['MenuClosed'];
}

if ($Mode == 'open') {
    $NewMod = 'closed';
} else if ($Mode == 'closed') {
    $NewMod = 'open';
}

$MyJsonExtra['MenuClosed'] = $NewMod;

$LastJson = json_encode($MyJsonExtra);
$LastLoginUpdate = $db->prepare("UPDATE users SET
     user_prefences   = :iki
     WHERE id = :dort and status=1 ");
$update = $LastLoginUpdate->execute(array(
    'iki' => $LastJson,
    'dort' => $MyJson['id']
));

echo StaticFunctions::JsonOutput([
    'process' => 'success'
]);
