<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

class PinCodeVerification
{

    private $db;
    private $UserId;

    private function CreatePin()
    {
        $Pn1 = rand(1, 9);
        $Pn2 = rand(0, 9);
        $Pn3 = rand(0, 9);
        $Pn4 = rand(0, 9);
        $Pn5 = rand(0, 9);
        $Pn6 = rand(1, 9);

        return $Pn1 . $Pn2 . $Pn3 . $Pn4 . $Pn5 . $Pn6;
    }

    private function DeleteOldPins($ProcessName): void
    {
        $UserID = $this->UserId;
        $DeletePins = $this->db->exec("DELETE FROM pin_codes WHERE process_type='{$ProcessName}' and user_id='{$UserID}' ");
    }


    public function VerifyProcess($ProcessName, $ProcessData = null)
    {

        $UserID = $this->UserId;
        $IsUserPhoneValidated = $this->CheckUserPhoneValidation();
        $LastPinSendedCheck   = $this->LastPinCheck($ProcessName, $ProcessData);

        if (!$LastPinSendedCheck) {
            $PinCode = $this->CreatePin();
            $this->DeleteOldPins($ProcessName);
            $InsertPin = $this->db->prepare("INSERT INTO pin_codes SET
            user_id = ?,
            pin_code = ?,
            process_type = ?,
            process_data = ?,
            last_time = ?");
            $insert = $InsertPin->execute(array(
                $UserID, $PinCode, $ProcessName, json_encode($ProcessData), time() + (60 * 3)
            ));
            $InsertedPinID = $this->db->lastInsertId();

            if ($ProcessName == 'change_phone_number_step2') {
                $payload = $this->SendPinNoValidate($ProcessData['withData'], $PinCode);
            } else {
                $payload = $this->SendPinCode($IsUserPhoneValidated, $PinCode);
            }
        } else {

            $GetLastSms  = $this->db->query("SELECT sended_to,sended_time FROM sended_sms WHERE user_id = '{$UserID}' order by id DESC ")->fetch(PDO::FETCH_ASSOC);
            $GetLastMail = $this->db->query("SELECT sended_to,sended_time FROM sended_mails WHERE user_id = '{$UserID}' order by id DESC ")->fetch(PDO::FETCH_ASSOC);


            if (($GetLastSms['sended_time']) > ($GetLastMail['sended_time'])) {
                $SendType = 'sms';
                try {
                    $number = PhoneNumber::parse($GetLastSms['sended_to']);
                    $SendedToNumber = $number->format(PhoneNumberFormat::INTERNATIONAL);
                } catch (PhoneNumberParseException $e) {
                    $SendedToNumber = StaticFunctions::lang('Geçersiz!');
                }
                $SendedTo = StaticFunctions::lang('{0} numaralı telefonuna', [
                    $SendedToNumber
                ]);
            } else {
                $SendType = 'email';
                $SendedTo = StaticFunctions::lang('{0} olan e-posta adresine', [
                    $GetLastMail['sended_to']
                ]);
            }

            $payload = [
                'PinSendType' => $SendType,
                'PinSendedTo' => $SendedTo
            ];
        }

        return $payload;
    }

    private function SendPinCode($IsUserPhoneValidated, $PinCode)
    {
        $UserID = $this->UserId;
        require_once CDIR . '/class.communication.php';
        $Comm = new EasyBotSend();
        $PinSended = false;

        $MeQuery = $this->db->query("SELECT email,real_name,phone_code,phone_number,real_name FROM users WHERE id = '{$UserID}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
        $FirstNameExplode = explode(' ', $MeQuery['real_name']);
        $RealName = $FirstNameExplode[0];

        if ($IsUserPhoneValidated) {
            if ($Comm->Sms(
                $UserID,
                StaticFunctions::lang('Selam {1}, {0} pin kodu ile Easybot üzerinde yapmak istediğin işlemi onaylayabilirsin.', [
                    $PinCode, $RealName
                ]),
                [$PinCode],
                null,
                true
            )) {
                $PinSended = true;
                $SendType = 'sms';
                $PhoneNumber = $MeQuery['phone_code'] . $MeQuery['phone_number'];
                try {
                    $number = PhoneNumber::parse($PhoneNumber);
                    $SendedToNumber = $number->format(PhoneNumberFormat::INTERNATIONAL);
                } catch (PhoneNumberParseException $e) {
                    $SendedToNumber = StaticFunctions::lang('Geçersiz!');
                }
                $SendedTo = StaticFunctions::lang('{0} numaralı telefonuna', [
                    $SendedToNumber
                ]);
            }
        }

        if (!$PinSended) {

            $Comm->Email(
                [
                    'UserID' => $UserID,
                    'Subject' => StaticFunctions::lang("Easybot işleminizi onaylayın."),
                    'To' => [
                        'Email' => $MeQuery['email'],
                        'Name' => $MeQuery['real_name']
                    ]
                ],
                'security_layer2',
                [
                    'PRE_HEADER' => StaticFunctions::lang('Güvenliğinizi her şeyden çok önemsiyoruz.'),
                    'EMAIL' => StaticFunctions::lang('Pin Kodu'),
                    'USER_EMAIL' => $PinCode,
                    'USER_INFORMATION' => StaticFunctions::lang('EASYBOT HESABIN'),
                    'WELCOME' => StaticFunctions::lang("İşleminizi Onaylayın"),
                    'ALT_TEXT' => StaticFunctions::lang("Eğer bu işlemden haberin yoksa endişelenme, hesabını senin için güvende tutacağız. Fakat yine de ele geçirilme ihtimaline karşı hesap şifreni kesinlikle değiştirmeni öneririz."),
                    'WELCOME_TEXT' => StaticFunctions::lang("Selam {0}, aşağıdaki pin kodu ile Easybot üzerinde yapmak istediğin işlemi onaylayabilirsin. ", [$RealName]),
                ]
            );
            $PinSended = true;
            $SendType = 'email';
            $SendedTo = StaticFunctions::lang('{0} olan e-posta adresine', [
                $MeQuery['email']
            ]);
        }

        return [
            'PinSendType' => $SendType,
            'PinSendedTo' => $SendedTo
        ];
    }

    private function SendPinNoValidate($PhoneData, $PinCode)
    {
        $UserID = $this->UserId;
        require_once CDIR . '/class.communication.php';
        $Comm = new EasyBotSend();
        $PinSended = false;

        $MeQuery = $this->db->query("SELECT real_name FROM users WHERE id = '{$UserID}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
        $FirstNameExplode = explode(' ', $MeQuery['real_name']);
        $RealName = $FirstNameExplode[0];
        $PhoneNumber = $PhoneData['PhoneCode'] . $PhoneData['PhoneNumber'];

        if ($Comm->Sms(
            $UserID,
            StaticFunctions::lang('Selam {1}, {0} pin kodu ile Easybot üzerinde yapmak istediğin işlemi onaylayabilirsin.', [
                $PinCode, $RealName
            ]),
            [$PinCode],
            $PhoneData,
            true
        )) {
            $PinSended = true;
            $SendType = 'sms';
            try {
                $number = PhoneNumber::parse($PhoneNumber);
                $SendedToNumber = $number->format(PhoneNumberFormat::INTERNATIONAL);
            } catch (PhoneNumberParseException $e) {
                $SendedToNumber = StaticFunctions::lang('Geçersiz!');
            }
            $SendedTo = StaticFunctions::lang('{0} numaralı telefonuna', [
                $SendedToNumber
            ]);
        }


        return [
            'PinSendType' => $SendType,
            'PinSendedTo' => $SendedTo
        ];
    }

    private function CheckUserPhoneValidation()
    {
        $UserID = $this->UserId;
        $GetSmsNumber = $this->db->query("SELECT phone_code,phone_number FROM users WHERE id = '{$UserID}' and status=1 and phone_verify=1 and phone_code != ''  and phone_number != '' ")->fetch(PDO::FETCH_ASSOC);
        if ($GetSmsNumber) {
            return true;
        } else {
            return false;
        }
    }

    private function LastPinCheck($ProcessName, $ProcessData)
    {
        $UserID = $this->UserId;
        $Now = time();
        $LastPin = $this->db->query("SELECT id FROM pin_codes WHERE user_id='{$UserID}' and process_type='{$ProcessName}' and last_time > $Now ")->fetch(PDO::FETCH_ASSOC);
        if ($LastPin) {

            $UpdatePinData = $this->db->prepare("UPDATE pin_codes SET
            process_data = :pdata
            WHERE id = :pid");
            $update = $UpdatePinData->execute(array(
                "pdata" => json_encode($ProcessData),
                "pid" => $LastPin['id']
            ));

            return true;
        } else {
            return false;
        }
    }

    public function setDb($dataBase): void
    {
        $this->db = $dataBase;
    }

    public function setUserId($Uid): void
    {
        $this->UserId = $Uid;
    }
}