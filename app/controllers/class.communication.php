<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;

class EasyBotSend
{

    public function Sms($UserID, $Message, $Variables, $PhoneNumber = null, $SendCgi = false)
    {
        global $db;

        if ($PhoneNumber == null) {
            $GetSmsNumber = $db->query("SELECT phone_code,phone_number FROM users WHERE id = '{$UserID}' and status=1 and phone_code != ''  and phone_number != '' ")->fetch(PDO::FETCH_ASSOC);
            if (!$GetSmsNumber) {
                if (!$SendCgi) {
                    echo StaticFunctions::ApiJson([
                        'status' => 'failed',
                        'label' => 'error',
                        'message' => StaticFunctions::lang('Geçerli bir telefon numaranız bulunamadı.')
                    ]);
                }
                return null;
            }
        } else {
            $GetSmsNumber['phone_code'] = $PhoneNumber['PhoneCode'];
            $GetSmsNumber['phone_number'] = $PhoneNumber['PhoneNumber'];
        }


        if ($this->SmsSpamCheck($UserID, $SendCgi) == 'spam') {
            return null;
        }

        foreach ($Variables as $key => $value) {
            $Message = str_replace('{' . $key . '}', $value, $Message);
        }

        if (Debug) {
            $Message = $Message . ' #' . rand(1, 9) . rand(1, 9) . rand(1, 9) . rand(1, 9);
        }

        $PhoneCode   = ($GetSmsNumber['phone_code'] != '') ? $GetSmsNumber['phone_code'] : null;
        $PhoneNumber = ($GetSmsNumber['phone_number'] != '') ? $GetSmsNumber['phone_number'] : null;

        if ($PhoneCode == null || $PhoneNumber == null) {
            if (!$SendCgi) {
                echo StaticFunctions::ApiJson([
                    'status' => 'failed',
                    'label' => 'error',
                    'message' => StaticFunctions::lang('Geçerli bir telefon numaranız bulunamadı.')
                ]);
            }
            return null;
        }

        if (!$this->ValidPhoneNumber($PhoneCode . $PhoneNumber)) {
            if (!$SendCgi) {
                echo StaticFunctions::ApiJson([
                    'status' => 'failed',
                    'label' => 'error',
                    'message' => StaticFunctions::lang('Geçerli bir telefon numaranız bulunamadı.')
                ]);
            }
            return null;
        }

        $SendPhoneNumber = $PhoneCode . $PhoneNumber;

        if ($PhoneCode == '+90') {
            $Body = $this->SendSmsNetgsm($SendPhoneNumber, $Message, $SendCgi);
        } else {
            $Body = $this->SendSmsTwilio($SendPhoneNumber, $Message, $SendCgi);
        }

        $InsertMessage = $db->prepare("INSERT INTO sended_sms SET
        user_id = ?,
        sended_to = ?,
        response_code = ?,
        message_text = ?,
        message_variables = ?,
        sended_time = ?");
        $insert = $InsertMessage->execute(array(
            $UserID, $SendPhoneNumber, $Body, $Message, json_encode($Variables), time()
        ));

        return true;
    }

    private function SendSmsTwilio($SendPhoneNumber, $Message, $SendCgi)
    {
        $input = iconv('UTF-8', 'ASCII//TRANSLIT', $Message);
        $input = preg_replace("/['|^|`|~|]/", "", $input);
        $input = preg_replace('/["]/', '', $input);
        $Message = preg_replace('/[" "]/', ' ', $input);

        $SmsApi = ProjectDefines::SmsApi()['Twilio'];
        $client = new Twilio\Rest\Client($SmsApi['Sid'], $SmsApi['Token']);
        $message = $client->messages->create(
            $SendPhoneNumber,
            [
                'from' => '+18053032652',
                'body' => $Message
            ]
        );

        return $message->sid;
    }

    private function SendSmsNetgsm($SendPhoneNumber, $Message, $SendCgi)
    {
        $SmsApi = ProjectDefines::SmsApi()['NetGsm'];
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://api.netgsm.com.tr/sms/send/get/', [
            'http_errors' => false,
            'query' => [
                'usercode' => $SmsApi['SmsUser'],
                'password' => $SmsApi['SmsPass'],
                'gsmno' => $SendPhoneNumber,
                'message' => $Message,
                'msgheader' => $SmsApi['SmsTitle']
            ]
        ]);


        if ($response->getStatusCode() != 200) {
            StaticFunctions::ErrorLog([
                'ErrType' => 'NETGSM_SMS_ERROR',
                'StatusCode' => $response->getStatusCode(),
                'Body' => mb_substr($response->getBody(), 0, 300)
            ]);
            if (!$SendCgi) {
                echo StaticFunctions::ApiJson([
                    'status' => 'failed',
                    'label' => 'error',
                    'message' => StaticFunctions::lang('Sistemsel bir hata oluştu, bunu kaydettik. En kısa sürede çözeceğiz.')
                ]);
            }
            return null;
        }

        $Body = $response->getBody();
        $First2Letter = mb_substr($Body, 0, 2);

        if ($First2Letter == 20 || $First2Letter == 30 || $First2Letter == 40 || $First2Letter == 70) {
            StaticFunctions::ErrorLog([
                'ErrType' => 'NETGSM_SMS_ERROR',
                'StatusCode' => $response->getStatusCode(),
                'Body' => mb_substr($response->getBody(), 0, 300)
            ]);
            if (!$SendCgi) {
                echo StaticFunctions::ApiJson([
                    'status' => 'failed',
                    'label' => 'error',
                    'message' => StaticFunctions::lang('Sistemsel bir hata oluştu, bunu kaydettik. En kısa sürede çözeceğiz.')
                ]);
            }
            return null;
        }

        return $Body;
    }


    private function SmsSpamCheck($UserID, $CgiCheck)
    {
        global $db;
        $Last60Minutes = time() - (60 * 60);
        $CheckSmsLimits = $db->query("SELECT id FROM sended_sms WHERE user_id='{$UserID}' and sended_time > $Last60Minutes ", PDO::FETCH_ASSOC);

        if ($CheckSmsLimits->rowCount() > 20) {
            if (!$CgiCheck) {
                echo StaticFunctions::ApiJson([
                    'status' => 'failed',
                    'label' => 'danger',
                    'message' => StaticFunctions::lang('Sms gönderim limiti aşıldı, lütfen daha sonra yeniden deneyiniz.'),
                    'text' => StaticFunctions::lang('Sms gönderim limiti aşıldı, lütfen daha sonra yeniden deneyiniz.')
                ]);
            }
            return 'spam';
        }
    }

    private function ValidPhoneNumber($Number)
    {
        try {
            $number = PhoneNumber::parse($Number);

            if (!$number->isPossibleNumber()) {
                return false;
            }

            return true;
        } catch (PhoneNumberParseException $e) {
            return false;
        }
    }

    private function EmailSpamCheck($UserID)
    {
        global $db;
        $Last60Minutes = time() - (60 * 60);
        $CheckEmailLimits = $db->query("SELECT id FROM sended_mails WHERE user_id='{$UserID}' and sended_time > $Last60Minutes ", PDO::FETCH_ASSOC);

        if ($CheckEmailLimits->rowCount() > 100) {
            echo StaticFunctions::ApiJson([
                'status' => 'failed',
                'label' => 'danger',
                'message' => StaticFunctions::lang('Email gönderim limiti aşıldı, lütfen daha sonra yeniden deneyiniz.'),
                'text' => StaticFunctions::lang('Email gönderim limiti aşıldı, lütfen daha sonra yeniden deneyiniz.')
            ]);
            return 'spam';
        }
    }


    public function Email($EmailOptions, $Template, $Variables)
    {

        if (isset($EmailOptions['UserID'])) {
            if ($this->EmailSpamCheck($EmailOptions['UserID']) == 'spam') {
                return null;
            }
        }

        $NewTokenForEmail = StaticFunctions::random(128);
        $ViewContent = file_get_contents(VDIR . '/email_templates/' . $Template . '.html');
        $ArrayChange = [
            'LANG' => LANG,
            'SUBJECT' => $EmailOptions['Subject'],
            'YEAR' => date('Y'),
            'EMAIL_TOKEN' => $NewTokenForEmail,
            'VIEW_IN_BROWSER' => StaticFunctions::lang('Tarayıcıda Görüntüle'),
        ];
        $ArrayAll = array_merge($ArrayChange, $Variables);
        foreach ($ArrayAll as $key => $value) {
            $ViewContent = str_replace('[[' . $key . ']]', $value, $ViewContent);
        }

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("no-reply@easybot.dev", "EasyBot");
        $email->setSubject($EmailOptions['Subject']);
        $email->setReplyTo('support@easybot.dev');
        $email->addTo($EmailOptions['To']['Email'], $EmailOptions['To']['Name']);
        $email->addContent(
            "text/html",
            $ViewContent
        );
        $sendgrid = new \SendGrid(ProjectDefines::SendgridApiKey());

        try {
            $sendgrid->send($email);
            global $db;
            $InsertMail = $db->prepare("INSERT INTO sended_mails SET
                user_id = ?,
                sended_to = ?,
                email_options = ?,
                email_template = ?,
                email_variables = ?,
                sended_time = ?,
                email_token = ?");
            $insert = $InsertMail->execute(array(
                $EmailOptions['UserID'], $EmailOptions['To']['Email'], json_encode($EmailOptions), $Template, json_encode($ArrayAll), time(), $NewTokenForEmail
            ));

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}