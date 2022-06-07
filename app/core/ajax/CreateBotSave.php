<?php

StaticFunctions::ajax_form('validated');

$Me = StaticFunctions::get_id();
$Jwt = StaticFunctions::post('urlToken');

$MyToken = $db->query("SELECT token from users WHERE id='{$Me}' and status=1")->fetch(PDO::FETCH_ASSOC);
if (!$MyToken) exit;

if ($Jwt == '') {
    http_response_code(401);
    exit;
}

try {
    $Decoded = \Firebase\JWT\JWT::decode($Jwt, StaticFunctions::JwtKey(), array('HS256'));
} catch (Exception $e) {
    http_response_code(401);
    exit;
}

if ($Decoded->owner != $MyToken['token']) {
    http_response_code(401);
    exit;
}

$payload = (array) $Decoded;

$payload['tokenData'] = [
    'botName' => StaticFunctions::post('botName'),
    'botCategories' => StaticFunctions::post('botCategories')
];

$jwt = \Firebase\JWT\JWT::encode($payload, StaticFunctions::JwtKey());

echo StaticFunctions::ApiJson([
    'status' => 'success',
    'token' => $jwt,
    'url' => StaticFunctions::post('urlAddress')
]);