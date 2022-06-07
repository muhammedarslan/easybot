<?php

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

if (!isset($IsCgi) || !$IsCgi) {
    http_response_code(401);
    exit;
}

$MeQuery = $db->query("SELECT * from users WHERE id='{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

if (!$MeQuery) {
    StaticFunctions::LogOut();
    http_response_code(401);
    exit;
}

$Layer      = $PinObjectData['Layer'];
$VerifyWith = $PinObjectData['VerifiedWith'];


require_once CDIR . '/class.security.layer.php';
$SecureClass = new SecurityLayer();
$SecureClass->setDb($db);
$SecureClass->setUser($MeQuery);

if ($SecureClass->IsSecure()) {
    http_response_code(401);
    exit;
}

$VerifyPossibleArray = ['Email', 'Sms', 'Authenticator', 'Notification'];

if ($VerifyWith == '' || !in_array($VerifyWith, $VerifyPossibleArray)) {
    http_response_code(401);
    exit;
}

$Layer = $SecureClass->Layer;
$PossibleMethods = $SecureClass->AcceptedMethods($Layer);

if ($PossibleMethods[$VerifyWith] != true) {
    http_response_code(401);
    exit;
}

$SecureClass->DeleteOldPins($Layer);
$SecureClass->SessionValidated($Layer);

if ($Layer == 'layer2') {
    $Channel = $_SESSION['SecureLevel_AuthChannel'];
    $UserPrefences = json_decode($MeQuery['user_prefences'], true);
    $UserPrefences['AuthorizedProfiles'][$Channel] = true;
    $JsonEnc = json_encode($UserPrefences);
    $EmailVerify = $MeQuery['email_verify'];

    if ($EmailVerify == 0) {
        if ($Channel == 'Login') {
            $EmailVerify = 1;
        }
    }
    $FailedLoginUpdate = $db->prepare("UPDATE users SET
                user_prefences   = :iki,
                email_verify = :em
                WHERE id = :dort");
    $update = $FailedLoginUpdate->execute(array(
        'iki' => $JsonEnc,
        'em' => $EmailVerify,
        "dort" => $MeQuery['id']
    ));
}


$FailedLoginUpdate = $db->prepare("UPDATE users SET
                failed_login   = :iki
                WHERE id = :dort");
$update = $FailedLoginUpdate->execute(array(
    'iki' => 0,
    "dort" => $MeQuery['id']
));


$CallbackJs = '';