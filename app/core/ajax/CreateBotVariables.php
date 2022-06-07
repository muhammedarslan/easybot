<?php

StaticFunctions::ajax_form('validated');

$Me = StaticFunctions::get_id();
$Jwt = StaticFunctions::post('token');

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

echo StaticFunctions::ApiJson([
    'title' => StaticFunctions::lang('Kullanabileceğin Değişkenler')
]);