<?php


$App->get('/', function () {
    header("Location:" . PROTOCOL . DOMAIN . PATH);
    exit;
});

$App->get('/set/(.*?)/(.*?)', function ($jwt, $token) {
    try {
        $JwtUser = \Firebase\JWT\JWT::decode($jwt, StaticFunctions::JwtKey(), array('HS256'));
        if ($JwtUser->TokenExpire > time()) {
            global $db;
            $UserID = $JwtUser->UserId;
            require_once VDIR . '/push.allow.php';
        } else {
            header("Location:" . PROTOCOL . DOMAIN . PATH . 'console/push/id');
            exit;
        }
    } catch (\Throwable $th) {
        header("Location:" . PROTOCOL . DOMAIN . PATH);
        exit;
    }
});

$App->get('/sw.js', function () {
    header('Content-Type: application/javascript');
    $JsContent = file_get_contents(ROOT_DIR . '/assets/sw/push.js');
    $JsContent = str_replace('[[SENDER_ID]]', ProjectDefines::FirebaseCloudMsg()['SenderId'], $JsContent);
    echo $JsContent;
});


$App->get('/web-service', function () {
    require_once VDIR . '/page.403.php';
});

$App->get('/web-service/(.*?)', function () {
    require_once VDIR . '/page.403.php';
});

$App->post('/web-service/(.*?)', function ($Req) {
    require_once CDIR . '/ajax.requests.php';
});