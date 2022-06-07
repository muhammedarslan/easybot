<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\ObjectUploader;

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

$Me = StaticFunctions::get_id();

$User = $db->query("SELECT * FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

if (mb_substr($User['avatar'], 0, 4) == 'http') {

    $UserAvatar = $User['avatar'];
    $Exp = explode('/', $UserAvatar);
    $keyname = 'public/avatars/' . end($Exp);
    $s3Client = new S3Client(ProjectDefines::Aws());

    try {
        $result = $s3Client->deleteObject([
            'Bucket' => ProjectDefines::AwsBucket(),
            'Key'    => $keyname
        ]);
    } catch (\Throwable $th) {
        //throw $th;
    }

    $DefaultAvatar = StaticFunctions::DefaultAvatar($User['real_name']);

    $UserAvatarUpdate = $db->prepare("UPDATE users SET
                     avatar   = :iki
                     WHERE id = :dort and status=1 ");
    $update = $UserAvatarUpdate->execute(array(
        'iki' => $DefaultAvatar,
        'dort' => $User['id']
    ));

    $_SESSION['UserSession']->avatar = $DefaultAvatar;

    echo  PROTOCOL . DOMAIN . PATH . 'assets/media/avatars/' . $DefaultAvatar;
    exit;
} else {
    echo  PROTOCOL . DOMAIN . PATH . 'assets/media/avatars/' . $User['avatar'];
}