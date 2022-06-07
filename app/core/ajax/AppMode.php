<?php

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

$Mode = StaticFunctions::post('mode');
if ($Mode != 'Light' && $Mode != 'Dark') exit;

$MyJson = $db->query("SELECT * FROM users WHERE id='{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

if (!$MyJson) exit;

$MyJsonExtra = (array) json_decode($MyJson['user_prefences']);

if ($Mode == 'Light') :
    $MyJsonExtra['AppMode'] = 'Light';
endif;

if ($Mode == 'Dark') :
    $MyJsonExtra['AppMode'] = 'Dark';
endif;

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
