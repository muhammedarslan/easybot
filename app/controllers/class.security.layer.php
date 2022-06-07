<?php

class SecurityLayer
{

    private $DbConnection;
    private $User;
    public $Layer;

    public function setDb($db): void
    {
        $this->DbConnection = $db;
    }

    public function setUser($UserQuery): void
    {
        $this->User = $UserQuery;
    }

    public function Secure(): void
    {
        if (!$this->IsSecure()) {
            $RequiredLayer  = $this->Layer;
            $AcceptedMethos = $this->AcceptedMethods($RequiredLayer);
            $LayerTexts     = $this->LayerText($RequiredLayer);
            require_once VDIR . '/security.layer.active.php';
            exit;
        }
    }

    public function SessionValidated($Layer): void
    {
        StaticFunctions::new_session();
        switch ($Layer) {
            case 'layer1':
                $_SESSION['SecureLevel_2Factor'] = false;
                unset($_SESSION['SecureLevel_2Factor']);
                $this->RmbTokenValidated();
                break;
            case 'layer2':
                $_SESSION['SecureLevel_Auth'] = false;
                unset($_SESSION['SecureLevel_Auth']);
                break;
            case 'layer3':
                $_SESSION['SecureLevel_FailedLogin'] = false;
                unset($_SESSION['SecureLevel_FailedLogin']);
                break;
        }
    }

    private function RmbTokenValidated(): void
    {
        $RememberToken = (isset($_COOKIE['RMB']) && $_COOKIE['RMB'] != '') ? StaticFunctions::clear($_COOKIE['RMB']) : null;
        $Browser       = md5($_SERVER['HTTP_USER_AGENT']);
        $Now           = time();
        if ($RememberToken != null) {
            $CheckToken = $this->DbConnection->query("SELECT id,is_verified FROM remember_me WHERE remember_token = '{$RememberToken}' and user_browser = '$Browser' and expired_time > $Now and user_id='{$this->User['id']}' ")->fetch(PDO::FETCH_ASSOC);
            if ($CheckToken && $CheckToken['is_verified'] == 0) {
                $TokenVerified = $this->DbConnection->prepare("UPDATE remember_me SET
                        is_verified = :vrf
                         WHERE id = :ddid");
                $update = $TokenVerified->execute(array(
                    "vrf" => 1,
                    "ddid" => $CheckToken['id']
                ));
            }
        }
    }

    public function CheckActivePin($Layer, $Method): array
    {
        $Now = time() + 30;
        $VerifyWithLike = '"VerifyWith":"' . $Method . '"';
        $CheckValidPin = $this->DbConnection->query("SELECT last_time from pin_codes  WHERE user_id='{$this->User['id']}' and process_type = '{$Layer}' and last_time > $Now and process_data LIKE '%$VerifyWithLike%' ")->fetch(PDO::FETCH_ASSOC);
        if ($CheckValidPin) {
            return [
                'hasValidPin' => true,
                'pinLastTime' => $CheckValidPin['last_time']
            ];
        } else {
            return [
                'hasValidPin' => false
            ];
        }
    }

    public function createPinCode($Layer, $Data, $PinSendMethod): int
    {
        $Pn1 = rand(1, 9);
        $Pn2 = rand(0, 9);
        $Pn3 = rand(0, 9);
        $Pn4 = rand(0, 9);
        $Pn5 = rand(0, 9);
        $Pn6 = rand(1, 9);

        $PinCode =  $Pn1 . $Pn2 . $Pn3 . $Pn4 . $Pn5 . $Pn6;
        $this->DeleteOldPins($Layer);
        $Now = time();
        $CheckUniq = $this->DbConnection->query("SELECT id from pin_codes WHERE pin_code='{$PinCode}' and user_id='{$this->User['id']}' and last_time > $Now ")->fetch(PDO::FETCH_ASSOC);
        if ($CheckUniq) {
            $this->createPinCode($Layer, $Data, $PinSendMethod);
        } else {
            $PinLastTime = time() + (60 * 3);
            $InsertPin = $this->DbConnection->prepare("INSERT INTO pin_codes SET
            user_id = ?,
            pin_code = ?,
            process_type = ?,
            process_data = ?,
            last_time = ?");
            $insert = $InsertPin->execute(array(
                $this->User['id'], $PinCode, $Layer, $Data, $PinLastTime
            ));
            $SendPin = $this->SendPin($PinCode, $PinSendMethod);
            return $PinLastTime;
        }
    }

    private function SendPin($Pin, $Method): bool
    {
        switch ($Method) {
            case 'Email':
                return $this->SendPinEmail($Pin);
                break;
            case 'Sms':
                return $this->SendPinSms($Pin);
                break;
            case 'Notification':
                return $this->SendPinNotification($Pin);
                break;
            default:
                return true;
                break;
        }
    }

    private function SendPinEmail($Pin): bool
    {
        require_once CDIR . '/class.communication.php';
        $Comm = new EasyBotSend();
        $FirstNameExplode = explode(' ', $this->User['real_name']);
        $SendEmail = $Comm->Email(
            [
                'UserID' => $this->User['id'],
                'Subject' => StaticFunctions::lang("Easybot işleminizi onaylayın."),
                'To' => [
                    'Email' => $this->User['email'],
                    'Name' => $this->User['real_name']
                ]
            ],
            'security_layer2',
            [
                'PRE_HEADER' => StaticFunctions::lang('Güvenliğinizi her şeyden çok önemsiyoruz.'),
                'EMAIL' => StaticFunctions::lang('Pin Kodu'),
                'USER_EMAIL' => $Pin,
                'USER_INFORMATION' => StaticFunctions::lang('EASYBOT HESABIN'),
                'WELCOME' => StaticFunctions::lang("İşleminizi Onaylayın"),
                'ALT_TEXT' => StaticFunctions::lang("Eğer bu işlemden haberin yoksa endişelenme, hesabını senin için güvende tutacağız. Fakat yine de ele geçirilme ihtimaline karşı hesap şifreni kesinlikle değiştirmeni öneririz."),
                'WELCOME_TEXT' => StaticFunctions::lang("Selam {0}, aşağıdaki pin kodu ile EasyBot\'a giriş yapabilirsin. ", [$FirstNameExplode[0]]),
            ]
        );
        if ($SendEmail) {
            return true;
        } else {
            return false;
        }
    }

    private function SendPinSms($Pin): bool
    {
        require_once CDIR . '/class.communication.php';
        $Comm = new EasyBotSend();
        $FirstNameExplode = explode(' ', $this->User['real_name']);

        if ($Comm->Sms(
            $this->User['id'],
            StaticFunctions::lang('Selam {0}, {1} kodu ile Easybot\'a giriş yapabilirsin.', [
                $FirstNameExplode[0], $Pin
            ]),
            [$Pin]
        )) {
            return true;
        } else {
            return false;
        }
    }

    private function SendPinNotification($Pin): bool
    {
        $FcmTokenQuery = $this->DbConnection->query("SELECT fcm_devices.fcm_token FROM fcm_devices INNER JOIN users on users.push_id = fcm_devices.id WHERE fcm_devices.user_id='{$this->User['id']}' ")->fetch(PDO::FETCH_ASSOC);
        if ($FcmTokenQuery) {
            $FcmToken = $FcmTokenQuery['fcm_token'];
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=' . ProjectDefines::FirebaseCloudMsg()['ServerKey'],
                ],
                'http_errors' => false
            ]);

            $FirstNameExplode = explode(' ', $this->User['real_name']);

            $response = $client->post(
                'https://fcm.googleapis.com/fcm/send',
                ['body' => '{
					"to":"' . $FcmToken . '",
					"data":{
						"notification":{
							"title":"' . StaticFunctions::lang('Easybot Çift Faktörlü Doğrulama') . '",
							"body":"' . StaticFunctions::lang('Selam {0}, {1} pin kodu ile EasyBot\'a giriş yapabilirsin.', [
                    $FirstNameExplode[0], $Pin
                ]) . '",
							"url":"https://easybot.dev",
							"notif_id":"' . $this->User['id'] . rand(1, 9999) . '",
							"vibrate":"[300,100,400]",
							"badge":"' . PROTOCOL . DOMAIN . PATH . 'assets/media/favicon.ico",
							"icon":"' . PROTOCOL . DOMAIN . PATH . 'assets/media/favicon.ico"
						}
					}
				}']
            );
            return true;
        } else {
            return false;
        }
    }

    public function sendRegisterPin(): void
    {
        $Pn1 = rand(1, 9);
        $Pn2 = rand(0, 9);
        $Pn3 = rand(0, 9);
        $Pn4 = rand(0, 9);
        $Pn5 = rand(0, 9);
        $Pn6 = rand(1, 9);

        $Layer = 'layer2';
        $Data = json_encode([
            'VerifyWith' => 'Email',
            'require' => 'SecurityLayerPinVerified',
            'withData' => [
                'Layer' => $Layer,
                'VerifiedWith' => 'Email'
            ]
        ]);
        $PinSendMethod = 'Email';

        $PinCode =  $Pn1 . $Pn2 . $Pn3 . $Pn4 . $Pn5 . $Pn6;
        $this->DeleteOldPins($Layer);
        $Now = time();
        $CheckUniq = $this->DbConnection->query("SELECT id from pin_codes WHERE pin_code='{$PinCode}' and user_id='{$this->User['id']}' and last_time > $Now ")->fetch(PDO::FETCH_ASSOC);
        if ($CheckUniq) {
            $this->sendRegisterPin();
        } else {
            $PinLastTime = time() + (60 * 3);
            $InsertPin = $this->DbConnection->prepare("INSERT INTO pin_codes SET
            user_id = ?,
            pin_code = ?,
            process_type = ?,
            process_data = ?,
            last_time = ?");
            $insert = $InsertPin->execute(array(
                $this->User['id'], $PinCode, $Layer, $Data, $PinLastTime
            ));

            require_once CDIR . '/class.communication.php';
            $Comm = new EasyBotSend();
            $FirstNameExplode = explode(' ', $this->User['real_name']);
            $SendEmail = $Comm->Email(
                [
                    'UserID' => $this->User['id'],
                    'Subject' => StaticFunctions::lang("EasyBot'a Hoşgeldin."),
                    'To' => [
                        'Email' => $this->User['email'],
                        'Name' => $this->User['real_name']
                    ]
                ],
                'security_layer2',
                [
                    'PRE_HEADER' => StaticFunctions::lang('Seni aramızda görmek muhteşem.'),
                    'EMAIL' => StaticFunctions::lang('Pin Kodu'),
                    'USER_EMAIL' => $PinCode,
                    'USER_INFORMATION' => StaticFunctions::lang('EASYBOT HESABIN'),
                    'WELCOME' => StaticFunctions::lang("EasyBot'a Hoşgeldin."),
                    'ALT_TEXT' => StaticFunctions::lang("<strong>Easybot</strong> hesabına istediğin zaman istediğin sosyal medya hesabınla giriş yapabilirsin. Eğer bağlanılmasını istemediğin bir sosyal medya hesabın varsa <strong>profilim</strong> sayfasından ilgili hesabı kaldırabilirsin."),
                    'WELCOME_TEXT' => StaticFunctions::lang("Kayıt olduğun için teşekkür ederiz. E-posta adresini doğrulamak için aşağıdaki pin kodunu kullanabilirsin."),
                ]
            );
        }
    }

    public function DeleteOldPins($Layer): void
    {
        $UserID = $this->User['id'];
        $DeletePins = $this->DbConnection->exec("DELETE FROM pin_codes WHERE process_type='{$Layer}' and user_id='{$UserID}' ");
    }

    public function AcceptedMethods($Layer): array
    {
        $LayerAccepted = $this->LayerAcceptedMethods($Layer);
        $UserAccepted  = $this->UserAcceptedMethods($Layer);

        foreach ($UserAccepted as $key => $value) {
            if ($LayerAccepted[$key] != true) {
                $UserAccepted[$key] = false;
            }
        }

        return $UserAccepted;
    }

    private function LayerAcceptedMethods($Layer): array
    {
        switch ($Layer) {
            case 'layer1':
                return [
                    'Email' => true,
                    'Sms'   => true,
                    'Authenticator' => true,
                    'Notification' => true
                ];
                break;

            case 'layer2':
                return [
                    'Email' => true,
                    'Sms'   => false,
                    'Authenticator' => false,
                    'Notification' => false
                ];
                break;

            case 'layer3':
                return [
                    'Email' => true,
                    'Sms'   => true,
                    'Authenticator' => false,
                    'Notification' => false
                ];
                break;

            default:
                return [
                    'Email' => false,
                    'Sms'   => false,
                    'Authenticator' => false,
                    'Notification' => false
                ];
                break;
        }
    }

    private function UserAcceptedMethods(): array
    {

        $Methods = [
            'Email' => true,
            'Sms'   => false,
            'Authenticator' => false,
            'Notification' => false
        ];

        $User = $this->DbConnection->query("SELECT phone_verify,2step_authenticator,2step_push,authenticator_id,push_id FROM users WHERE id='{$this->User['id']}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
        if (!$User) {
            http_response_code(401);
            StaticFunctions::LogOut();
            exit;
        }

        if ($User['phone_verify'] == 1) {
            $Methods['Sms'] = true;
        }

        if ($User['2step_authenticator'] == 1) {
            $AuthenticatorID = $User['authenticator_id'];
            if ($AuthenticatorID != '') {
                $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
                try {
                    if ($g->getCode($AuthenticatorID) != '') {
                        $Methods['Authenticator'] = true;
                    }
                } catch (\Throwable $th) {
                    //Invalid Google Authenticator Secret.
                    $DisableAuthenticator = $this->DbConnection->prepare("UPDATE users SET
                authenticator_id = :2step_v
                WHERE id = :uids and status=1 ");
                    $update = $DisableAuthenticator->execute(array(
                        "2step_v" => '',
                        "uids" => $this->User['id']
                    ));
                }
            }
        }

        if ($User['2step_push'] == 1) {
            $CheckPushID = $this->DbConnection->query("SELECT id from fcm_devices WHERE id='{$User['push_id']}' and user_id='{$this->User['id']}' and fcm_token != '' ")->fetch(PDO::FETCH_ASSOC);
            if ($CheckPushID) {
                $Methods['Notification'] = true;
            }
        }

        return $Methods;
    }

    private function LayerText($Layer): array
    {
        switch ($Layer) {
            case 'layer1':
                return [
                    'Title' => StaticFunctions::lang('2 Adımlı Doğrulama'),
                    'Message' => StaticFunctions::lang('EasyBot\'a Hoşgeldin. Devam etmek için aşağıdaki doğrulama yöntemlerinden birisini seçip kimliğini doğrulamalısın.')
                ];
                break;
            case 'layer2':
                return [
                    'Title' => StaticFunctions::lang('Yeni Giriş Yöntemi'),
                    'Message' => StaticFunctions::lang('EasyBot\'a Hoşgeldin. Hesabına yeni bir giriş yöntemi ile bağlandın. Güvenliğin için lütfen kimliğini doğrula.')
                ];
                break;
            case 'layer3':
                return [
                    'Title' => StaticFunctions::lang('Hatalı Giriş Doğrulaması'),
                    'Message' => StaticFunctions::lang('EasyBot\'a Hoşgeldin. Hesabına birçok kez hatalı giriş denemesi yapıldı. Bu yüzden sonunda hesabına girenin sen olduğundan emin olmak istiyorum.')
                ];
                break;
        }
    }

    public function IsSecure(): bool
    {
        StaticFunctions::new_session();
        $Layer1 = (isset($_SESSION['SecureLevel_2Factor']) && $_SESSION['SecureLevel_2Factor'] == true) ? true : false;
        $Layer2 = (isset($_SESSION['SecureLevel_Auth'])  && $_SESSION['SecureLevel_Auth'] == true) ? true : false;
        $Layer3 = (isset($_SESSION['SecureLevel_FailedLogin']) && $_SESSION['SecureLevel_FailedLogin'] == true) ? true : false;

        if ($Layer1) {
            $this->Layer = 'layer1';
            return false;
        }

        if ($Layer2) {
            $this->Layer = 'layer2';
            return false;
        }

        if ($Layer3) {
            $this->Layer = 'layer3';
            return false;
        }

        return true;
    }
}