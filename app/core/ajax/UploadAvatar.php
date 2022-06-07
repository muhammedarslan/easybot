<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\ObjectUploader;

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();
$Me = StaticFunctions::get_id();
$User = $db->query("SELECT * FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
$UserOldAvatar = $User['avatar'];

$Random = StaticFunctions::random(64);

$handle = new \Verot\Upload\Upload($_FILES['profile_avatar']);
if ($handle->uploaded) {
    $handle->allowed = array('image/*');
    $handle->file_max_size = 1024 * 1024; // 1KB
    $handle->file_new_name_body   = $Random;
    $handle->image_convert = 'png';
    $handle->image_resize         = true;
    $handle->image_x              = 129;
    $handle->image_y              = 129;
    $handle->process(ROOT_DIR . '/assets/tmp/');
    if ($handle->processed) {
        $handle->clean();
        $UploadAvatarFilePath = ROOT_DIR . '/assets/tmp/' . $Random . '.png';

        if (!file_exists($UploadAvatarFilePath)) {
            echo StaticFunctions::ApiJson([
                'status' => 'failed',
                'title' => StaticFunctions::lang('Bir hata oluştu!'),
                'message' => StaticFunctions::lang('Profil fotoğrafının yüklenmesi sırasında bir hata oluştu. Lütfen daha sonra tekrar dene.')
            ]);
            exit;
        }
    } else {
        echo StaticFunctions::ApiJson([
            'status' => 'failed',
            'title' => StaticFunctions::lang('Bir hata oluştu!'),
            'message' => StaticFunctions::lang('Profil fotoğrafının yüklenmesi sırasında bir hata oluştu. Lütfen daha sonra tekrar dene.')
        ]);
        exit;
    }
} else {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Profil fotoğrafının yüklenmesi sırasında bir hata oluştu. Lütfen daha sonra tekrar dene.')
    ]);
    exit;
}

require_once CDIR . '/class.upload.php';

$Upload = new Upload();
$AwsUrl = $Upload->UploadAvatar($UploadAvatarFilePath, $Random);

@unlink($UploadAvatarFilePath);

if (mb_substr($AwsUrl, 0, 4) != 'http') {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Profil fotoğrafının yüklenmesi sırasında bir hata oluştu. Lütfen daha sonra tekrar dene.')
    ]);
    exit;
}

if (mb_substr($UserOldAvatar, 0, 4) == 'http') {
    $Exp = explode('/', $UserOldAvatar);
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
}

$UserAvatarUpdate = $db->prepare("UPDATE users SET
                     avatar   = :iki
                     WHERE id = :dort and status=1 ");
$update = $UserAvatarUpdate->execute(array(
    'iki' => $AwsUrl,
    'dort' => $User['id']
));

$_SESSION['UserSession']->avatar = $AwsUrl;

echo StaticFunctions::ApiJson([
    'status' => 'success',
    'title' => StaticFunctions::lang('Başarıyla tamamlandı!'),
    'message' => StaticFunctions::lang('Profil fotoğrafının başarıyla değiştirildi.'),
    'avatarUrl' => $AwsUrl
]);
exit;