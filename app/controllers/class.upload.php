<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\ObjectUploader;

class Upload
{

    private  $db;
    private $UploadChannels = [
        'support_ticket' => [
            'isMultiple' => true,
            'maxFileSize' => 2,
            'limits' => [
                'limit' => 20,
                'inMinute' => 10
            ]
        ]
    ];

    private function S3Slient()
    {
        return new S3Client(ProjectDefines::Aws());
    }

    public function IsFileSecure($FilePath)
    {
        // File virus scan.
        return true;
    }

    public function UploadForm($FormID, $UploadChannel, $JsFunction, $Accept = '', $PageToken = null)
    {
        $Multiple = $this->UploadChannels[$UploadChannel]['isMultiple'];
        $HtmlOutput = '';
        $HtmlOutput .= '<form id="' . $FormID . '" method="post" style="display: none;">
         <input type="text" hidden name="file" value="upload">
        <input type="text" hidden name="channel" value="' . $UploadChannel . '">';

        if ($PageToken != null) {
            $HtmlOutput .= '<input type="text" hidden name="page_token" value="' . $PageToken . '">';
        }

        if ($Multiple) {
            $HtmlOutput .= '<input id="Input' . $FormID . '" data-max="' . $this->UploadChannels[$UploadChannel]['limits']['limit'] . '" onChange="' . $JsFunction . '();" autocomplete="off" hidden multiple name="upload_files[]" type="file" accept="' . $Accept . '" class="form-control form-input">';
        } else {
            $HtmlOutput .= '<input id="Input' . $FormID . '" data-max="1" onChange="' . $JsFunction . '();" autocomplete="off" hidden name="upload_files" type="file" accept="' . $Accept . '" class="form-control form-input">';
        }

        $HtmlOutput .= '</form>';


        return $HtmlOutput;
    }

    public function CheckChannel($Channel)
    {
        if (isset($this->UploadChannels[$Channel])) {
            return true;
        } else {
            return false;
        }
    }

    public function CheckMultiple($Channel)
    {
        return $this->UploadChannels[$Channel]['isMultiple'];
    }

    public function GetChannel($Channel)
    {
        return $this->UploadChannels[$Channel];
    }

    public function AllowedExtentions()
    {
        return [
            'doc', 'dot', 'docx', 'docm', 'dotx', 'dotm', 'rtf', 'ott', 'pdf', 'txt', 'xls', 'xlt', 'xlsx', 'xlsm', 'ods', 'ots', 'tsv', 'ppt', 'pps',
            'pot', 'pptx', 'pptm', 'ppsx', 'ppsm', 'potx', 'potm', 'odp', 'otp', 'xml', 'zip', 'htm', 'html', 'xhtml', 'xml', 'dtd', 'json', 'yaml', 'yml',
            'md', 'pdf', 'bmp', 'png', 'gif', 'jpeg', 'jpg', 'tiff', 'txml', 'po'
        ];
    }

    public function setDb($Db)
    {
        $this->db = $Db;
    }

    public function UploadAws($FilePath, $fileName)
    {
        $s3Client = $this->S3Slient();
        $Content = file_get_contents($FilePath);

        try {
            $result = $s3Client->putObject([
                'Bucket' => ProjectDefines::AwsBucket(),
                'Key'    => 'storage/' . $fileName,
                'Body'   => $Content,
                'ACL'    => 'private',
            ]);

            return $result->get('ObjectURL');
        } catch (Aws\S3\Exception\S3Exception $e) {
            return null;
        }
    }

    public function SingleAwsFile($FileToken)
    {
        $Me = StaticFunctions::get_id();
        $GetSingleFile = $this->db->query("SELECT file_aws_path FROM uploaded_files WHERE file_token = '{$FileToken}' and user_id='{$Me}' and status=1 and file_aws_path != '' and file_size > 0 ")->fetch(PDO::FETCH_ASSOC);
        if ($GetSingleFile) {
            $FileKey = $GetSingleFile['file_aws_path'];
            try {
                $s3Client = $this->S3Slient();
                $cmd = $s3Client->getCommand('GetObject', [
                    'Bucket' => ProjectDefines::AwsBucket(),
                    'Key' => $FileKey
                ]);

                $request = $s3Client->createPresignedRequest($cmd, '+10 minutes');
                $presignedUrl = (string)$request->getUri();
                header("Location:" . $presignedUrl);
                exit;
            } catch (\Throwable $th) {
                require_once VDIR . '/page.404.php';
            }
        } else {
            require_once VDIR . '/page.404.php';
        }
    }

    public function UploadAvatar($Url, $token)
    {
        $s3Client = $this->S3Slient();
        $Content = file_get_contents($Url);

        if (!$this->IsFileSecure($Url)) {
            echo StaticFunctions::ApiJson([
                'status' => 'failed',
                'process' => 'failed',
                'label' => 'danger',
                'title' => StaticFunctions::lang('Bir hata oluştu!'),
                'message' => StaticFunctions::lang('Dosya güvenlik algoritması tarafından zararlı olarak algılandı.')
            ]);
            exit;
        }

        try {
            $result = $s3Client->putObject([
                'Bucket' => ProjectDefines::AwsBucket(),
                'Key'    => 'public/avatars/' . $token,
                'Body'   => $Content,
                'ACL'    => 'public-read',
                'ContentType' => 'image/png'
            ]);

            return $result->get('ObjectURL');
        } catch (Aws\S3\Exception\S3Exception $e) {
            return 'default/avatars/B.png';
        }
    }
}