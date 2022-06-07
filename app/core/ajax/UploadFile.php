<?php

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();
$Me = StaticFunctions::get_id();
$User = $db->query("SELECT id,token FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

if (!$User) {
    StaticFunctions::LogOut();
    http_response_code(401);
    exit;
}

$UploadChannel = StaticFunctions::post('channel');

if ($UploadChannel == '') {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'errorTitle' => StaticFunctions::lang('Bir hata oluştu!'),
        'errorMessages' => [],
        'totalUploadedCount' => 0,
        'uploadedFiles' => [],
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Lütfen sayfayı yenileyerek tekrar dene.')
    ]);
    exit;
}

if (!$_FILES['upload_files']) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'errorTitle' => StaticFunctions::lang('Bir hata oluştu!'),
        'errorMessages' => [],
        'totalUploadedCount' => 0,
        'uploadedFiles' => [],
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Lütfen en az 1 dosya seçin.')
    ]);
    exit;
}

$IsMultiple = isset($_FILES['upload_files'][0]['tmp_name']) ? true : false;

require_once CDIR . '/class.upload.php';
$UploadClass = new Upload();

if (!$UploadClass->CheckChannel($UploadChannel)) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'errorTitle' => StaticFunctions::lang('Bir hata oluştu!'),
        'errorMessages' => [],
        'totalUploadedCount' => 0,
        'uploadedFiles' => [],
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Lütfen sayfayı yenileyerek tekrar dene.')
    ]);
    exit;
}

$ChannelIsMultiple = $UploadClass->CheckMultiple($UploadChannel);
$Channel = $UploadClass->GetChannel($UploadChannel);

if ($ChannelIsMultiple && !isset($_FILES['upload_files']['tmp_name'][0])) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'errorTitle' => StaticFunctions::lang('Bir hata oluştu!'),
        'errorMessages' => [],
        'totalUploadedCount' => 0,
        'uploadedFiles' => [],
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Lütfen en az 1 dosya seçin.')
    ]);
    exit;
}

if ($IsMultiple && !$ChannelIsMultiple) {
    $_FILES['upload_files'] = [
        'name' => $_FILES['upload_files']['name'][0],
        'type' => $_FILES['upload_files']['type'][0],
        'tmp_name' => $_FILES['upload_files']['tmp_name'][0],
        'error' => $_FILES['upload_files']['error'][0],
        'size' => $_FILES['upload_files']['size'][0]
    ];
}

$FilesArray = [];

if ($ChannelIsMultiple) {
    foreach ($_FILES['upload_files']['name'] as $key => $file) {

        if (count($FilesArray) < $Channel['limits']['limit']) {
            array_push($FilesArray, [
                'name' => $_FILES['upload_files']['name'][$key],
                'type' => $_FILES['upload_files']['type'][$key],
                'tmp_name' => $_FILES['upload_files']['tmp_name'][$key],
                'error' => $_FILES['upload_files']['error'][$key],
                'size' => $_FILES['upload_files']['size'][$key]
            ]);
        } else {
            break;
        }
    }
} else {
    $FilesArray[0] = $_FILES['upload_files'];
}

$ResponseArray = [
    'status' => 'processing..',
    'errorTitle' => StaticFunctions::lang('Bir hata oluştu!'),
    'errorMessages' => [],
    'totalUploadedCount' => 0,
    'uploadedFiles' => [],
    'title' => null,
    'message' => null
];

$_FILES['upload_files'] = null;

$NowBack = time() - (60 * ($Channel['limits']['inMinute']));
$UploadedFilesQuery = $db->query("SELECT id FROM uploaded_files WHERE user_id = '{$Me}' and upload_channel='{$UploadChannel}' and upload_time > $NowBack and status=1", PDO::FETCH_ASSOC);
$UploadedFilesCount = $UploadedFilesQuery->rowCount();
$UploadLimit = $Channel['limits']['limit'];

foreach ($FilesArray as $key => $File) {

    if ($UploadedFilesCount > $UploadLimit) {
        array_push($ResponseArray['errorMessages'], StaticFunctions::lang('<strong>{0}</strong> isimli dosya yüklenemedi: Belirli bir zamanda verilen dosya yükleme limitine ulaşıldı.', [
            StaticFunctions::say($File['name'])
        ]));
        continue;
    }

    $_FILES['upload_files'] = $File;

    $upload = new \Delight\FileUpload\FileUpload();
    $upload->withTargetDirectory(ROOT_DIR . '/assets/tmp');
    $upload->withMaximumSizeInMegabytes($Channel['maxFileSize']);
    $upload->withAllowedExtensions($UploadClass->AllowedExtentions());
    $upload->from('upload_files');

    try {
        $uploadedFile = $upload->save();

        // success
        $UploadedFilesCount++;
        $FilePath =  $uploadedFile->getPath();
        $FileExtention = $uploadedFile->getExtension();
        $RealFileName = StaticFunctions::clear($File['name']);
        $FileSize = $File['size'];

        if (!$UploadClass->IsFileSecure($FilePath)) {
            @unlink($FilePath);
            array_push($ResponseArray['errorMessages'], StaticFunctions::lang('<strong>{0}</strong> isimli dosya yüklenemedi: Dosya güvenlik algoritması tarafından zararlı olarak algılandı.', [
                StaticFunctions::say($File['name'])
            ]));
            continue;
        }

        $Random = $uploadedFile->getFilenameWithExtension();
        $AwsUrl = $UploadClass->UploadAws($FilePath, $Random);
        @unlink($FilePath);

        if ($AwsUrl == null) {
            array_push($ResponseArray['errorMessages'], StaticFunctions::lang('<strong>{0}</strong> isimli dosya yüklenemedi: Sistemsel bir hata oluştu.', [
                StaticFunctions::say($File['name'])
            ]));
            continue;
        }

        $ExplodeUrl = explode('/', $AwsUrl);
        $FileToken = StaticFunctions::random(64);

        if (StaticFunctions::post('page_token') != '') {
            $UploadToken = StaticFunctions::post('page_token');
        } else {
            $UploadToken = null;
        }

        $InsertFile = $db->prepare("INSERT INTO uploaded_files SET
                user_id = ?,
                upload_channel = ?,
                upload_time = ?,
                file_extention = ?,
                file_real_name = ?,
                file_size = ?,
                file_upload_token = ?,
                file_aws_path = ?,
                file_token = ?,
                upload_info = ?,
                status = ?");
        $insert = $InsertFile->execute(array(
            $Me, $UploadChannel, time(), $FileExtention, $RealFileName, $FileSize, $UploadToken, 'storage/' . end($ExplodeUrl), $FileToken, json_encode([
                'UserId' => $Me,
                'UserIp' => StaticFunctions::get_ip(),
                'UserBrowser' => StaticFunctions::getBrowser(),
            ]), 1
        ));

        if (!$insert) {
            array_push($ResponseArray['errorMessages'], StaticFunctions::lang('<strong>{0}</strong> isimli dosya yüklenemedi: Sistemsel bir hata oluştu.', [
                StaticFunctions::say($File['name'])
            ]));
            continue;
        }

        array_push($ResponseArray['uploadedFiles'], [
            'name' => $RealFileName,
            'token' => $FileToken
        ]);
    } catch (\Delight\FileUpload\Throwable\InputNotFoundException $e) {
        array_push($ResponseArray['errorMessages'], StaticFunctions::lang('<strong>{0}</strong> isimli dosya yüklenemedi: Dosya bulunamadı.', [
            StaticFunctions::say($File['name'])
        ]));
    } catch (\Delight\FileUpload\Throwable\InvalidFilenameException $e) {
        array_push($ResponseArray['errorMessages'], StaticFunctions::lang('<strong>{0}</strong> isimli dosya yüklenemedi: Dosya ismi geçersiz.', [
            StaticFunctions::say($File['name'])
        ]));
    } catch (\Delight\FileUpload\Throwable\InvalidExtensionException $e) {
        array_push($ResponseArray['errorMessages'], StaticFunctions::lang('<strong>{0}</strong> isimli dosya yüklenemedi: Dosya uzantısı desteklenmiyor.', [
            StaticFunctions::say($File['name'])
        ]));
    } catch (\Delight\FileUpload\Throwable\FileTooLargeException $e) {
        array_push($ResponseArray['errorMessages'], StaticFunctions::lang('<strong>{0}</strong> isimli dosya yüklenemedi: Maksimum dosya boyutu {1} Mb olmalıdır.', [
            StaticFunctions::say($File['name']), $Channel['maxFileSize']
        ]));
    } catch (\Delight\FileUpload\Throwable\UploadCancelledException $e) {
        array_push($ResponseArray['errorMessages'], StaticFunctions::lang('<strong>{0}</strong> isimli dosya yüklenemedi: Yükleme iptal edildi.', [
            StaticFunctions::say($File['name'])
        ]));
    } catch (\Throwable $th) {
        array_push($ResponseArray['errorMessages'], StaticFunctions::lang('<strong>{0}</strong> isimli dosya yüklenemedi: Sistemsel bir hata oluştu.', [
            StaticFunctions::say($File['name'])
        ]));
    }
}

$ResponseArray['totalUploadedCount'] = count($ResponseArray['uploadedFiles']);
$ResponseArray['status'] = 'success';
$ResponseArray['title'] = StaticFunctions::lang('Başarıyla tamamlandı!');
$ResponseArray['message'] = StaticFunctions::lang('Dosyaların yüklenmesi başarıyla tamamlandı.');

echo StaticFunctions::ApiJson($ResponseArray);