<?php

StaticFunctions::ajax_form('private');

$Me = StaticFunctions::get_id();
$MeQuery = $db->query("SELECT * FROM users WHERE id='{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

if (!$MeQuery) {
    http_response_code(401);
    exit;
}

require_once CDIR . '/class.security.layer.php';
$SecureLayer = new SecurityLayer();
$SecureLayer->setDb($db);
$SecureLayer->setUser($MeQuery);

if ($SecureLayer->IsSecure()) {
    http_response_code(401);
    exit;
}

StaticFunctions::LogOut();
echo StaticFunctions::ApiJson([
    'logOut' => true
]);