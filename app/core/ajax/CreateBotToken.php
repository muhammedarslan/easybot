<?php

StaticFunctions::ajax_form('validated');

$Me = StaticFunctions::get_id();
$MyToken = $db->query("SELECT token from users WHERE id='{$Me}' and status=1")->fetch(PDO::FETCH_ASSOC);
if (!$MyToken) exit;

$payload = [
    'createdTime' => time(),
    'owner' => $MyToken['token'],
    'botToken' => StaticFunctions::random_with_time(32),
    'tokenData' => []
];

$jwt = \Firebase\JWT\JWT::encode($payload, StaticFunctions::JwtKey());

echo StaticFunctions::ApiJson([
    'status' => 'success',
    'token' => $jwt
]);