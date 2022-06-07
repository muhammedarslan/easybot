<?php


class EasybotSocialLogin
{

    private $GithubApp;
    private $GoogleApp;
    private $LinkedinApp;
    private $FacebookApp;
    private $InstagramApp;

    private function DefineApps(): void
    {
        $this->GithubApp = ProjectDefines::SocialLogin()['GithubApp'];
        $this->GoogleApp = ProjectDefines::SocialLogin()['GoogleApp'];
        $this->LinkedinApp = ProjectDefines::SocialLogin()['LinkedinApp'];
        $this->FacebookApp = ProjectDefines::SocialLogin()['FacebookApp'];
        $this->InstagramApp = ProjectDefines::SocialLogin()['InstagramApp'];
    }

    public function go($with)
    {

        $this->DefineApps();
        switch ($with) {
            case 'google':
                $this->LoginWithGoogle();
                break;
            case 'github':
                $this->LoginWithGithub();
                break;
            case 'linkedin':
                $this->LoginWithLinkedin();
                break;
            case 'facebook':
                $this->LoginWithFacebook();
                break;
            case 'instagram':
                $this->Error404();
                //$this->LoginWithInstagram();
                break;
            default:
                $this->Error404();
                break;
        }
    }

    private function Error404()
    {
        echo StaticFunctions::lang('Lütfen bekleyin...') . '<script>window.close();</script>';
        exit;
    }

    private function LoginWithGoogle()
    {
        $config = [
            'google' => [
                'client_id'     => $this->GoogleApp['AppID'],
                'client_secret' => $this->GoogleApp['AppSecret'],
                'redirect'      => PROTOCOL . DOMAIN . PATH . 'social-callback/google',
            ],
        ];

        $socialite = new Overtrue\Socialite\SocialiteManager($config);
        $response = $socialite->driver('google')->redirect();
        echo $response->send();
        exit;
    }

    private function LoginWithGithub()
    {
        $config = [
            'github' => [
                'client_id'     => $this->GithubApp['AppID'],
                'client_secret' => $this->GithubApp['AppSecret'],
                'redirect'      => PROTOCOL . DOMAIN . PATH . 'social-callback/github',
            ],
        ];

        $socialite = new Overtrue\Socialite\SocialiteManager($config);
        $response = $socialite->driver('github')->redirect();
        echo $response->send();
        exit;
    }

    private function LoginWithLinkedin()
    {
        $config = [
            'linkedin' => [
                'client_id'     => $this->LinkedinApp['AppID'],
                'client_secret' => $this->LinkedinApp['AppSecret'],
                'redirect'      => PROTOCOL . DOMAIN . PATH . 'social-callback/linkedin',
            ],
        ];

        $socialite = new Overtrue\Socialite\SocialiteManager($config);
        $response = $socialite->driver('linkedin')->redirect();
        echo $response->send();
        exit;
    }

    private function LoginWithFacebook()
    {
        $config = [
            'facebook' => [
                'client_id'     => $this->FacebookApp['AppID'],
                'client_secret' => $this->FacebookApp['AppSecret'],
                'redirect'      => PROTOCOL . DOMAIN . PATH . 'social-callback/facebook',
            ],
        ];

        $socialite = new Overtrue\Socialite\SocialiteManager($config);
        $response = $socialite->driver('facebook')->redirect();
        echo $response->send();
        exit;
    }

    private function LoginWithInstagram()
    {
        $config = [
            'apiKey'      => $this->InstagramApp['AppID'],
            'apiSecret'   => $this->InstagramApp['AppSecret'],
            'apiCallback' => 'https://' . DOMAIN . PATH . 'social-callback/instagram',
            'scope'       => ['user_profile'],
        ];

        header("Location:https://www.instagram.com/oauth/authorize?client_id=" . $this->InstagramApp['AppID'] . "&redirect_uri=" . urlencode($config['apiCallback']) . "&scope=user_profile&response_type=code");
        exit;
    }

    public function callback($with)
    {
        $this->DefineApps();
        switch ($with) {
            case 'google':
                $UserArray = $this->CallbackGoogle();
                break;
            case 'github':
                $UserArray = $this->CallbackGithub();
                break;
            case 'linkedin':
                $UserArray = $this->CallbackLinkedin();
                break;
            case 'facebook':
                $UserArray = $this->CallbackFacebook();
                break;
            case 'instagram':
                $this->Error404();
                //$UserArray = $this->CallbackInstagram();
                break;
            default:
                $this->Error404();
                break;
        }
        $this->StartSession($UserArray);
    }

    private function CallbackGoogle()
    {
        $config = [
            'google' => [
                'client_id'     => $this->GoogleApp['AppID'],
                'client_secret' => $this->GoogleApp['AppSecret'],
                'redirect'      => PROTOCOL . DOMAIN . PATH . 'social-callback/google',
            ],
        ];
        $socialite = new Overtrue\Socialite\SocialiteManager($config);
        try {
            $user = $socialite->driver('google')->user();
        } catch (\Throwable $th) {
            $this->Error404();
        }

        return [
            'Provider' => 'Google',
            'UserEmail' =>
            $user->getEmail(),
            'RealName' => $user->getName(),
            'Avatar' => $user->getAvatar()
        ];
    }

    private function CallbackGithub()
    {
        $config = [
            'github' => [
                'client_id'     => $this->GithubApp['AppID'],
                'client_secret' => $this->GithubApp['AppSecret'],
                'redirect'      => PROTOCOL . DOMAIN . PATH . 'social-callback/github',
            ],
        ];
        $socialite = new Overtrue\Socialite\SocialiteManager($config);
        try {
            $user = $socialite->driver('github')->user();
        } catch (\Throwable $th) {
            $this->Error404();
        }

        return [
            'Provider' => 'Github',
            'UserEmail' =>
            $user->getEmail(),
            'RealName' => $user->getName(),
            'Avatar' => $user->getAvatar()
        ];
    }

    private function CallbackLinkedin()
    {
        $config = [
            'linkedin' => [
                'client_id'     => $this->LinkedinApp['AppID'],
                'client_secret' => $this->LinkedinApp['AppSecret'],
                'redirect'      => PROTOCOL . DOMAIN . PATH . 'social-callback/linkedin',
            ],
        ];
        $socialite = new Overtrue\Socialite\SocialiteManager($config);
        try {
            $user = $socialite->driver('linkedin')->user();
        } catch (\Throwable $th) {
            $this->Error404();
        }

        return [
            'Provider' => 'Linkedin',
            'UserEmail' =>
            $user->getEmail(),
            'RealName' => $user->getName(),
            'Avatar' => $user->getAvatar()
        ];
    }

    private function CallbackFacebook()
    {
        $config = [
            'facebook' => [
                'client_id'     => $this->FacebookApp['AppID'],
                'client_secret' => $this->FacebookApp['AppSecret'],
                'redirect'      => PROTOCOL . DOMAIN . PATH . 'social-callback/facebook',
            ],
        ];
        $socialite = new Overtrue\Socialite\SocialiteManager($config);
        try {
            $user = $socialite->driver('facebook')->user();
        } catch (\Throwable $th) {
            $this->Error404();
        }

        return [
            'Provider' => 'Facebook',
            'UserEmail' =>
            $user->getEmail(),
            'RealName' => $user->getName(),
            'Avatar' => $user->getAvatar()
        ];
    }

    private function CallbackInstagram()
    {
        $config = [
            'apiKey'      => $this->InstagramApp['AppID'],
            'apiSecret'   => $this->InstagramApp['AppSecret'],
            'apiCallback' => 'https://' . DOMAIN . PATH . 'social-callback/instagram',
            'scope'       => ['user_profile'],
        ];

        $Rew = false;

        if (isset($_GET['code']) && $_GET['code'] != '') {

            $client = new \GuzzleHttp\Client();

            $response = $client->request('POST', 'https://api.instagram.com/oauth/access_token', [
                'http_errors' => false,
                'form_params' => [
                    'client_id' => $this->InstagramApp['AppID'],
                    'client_secret' => $this->InstagramApp['AppSecret'],
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $config['apiCallback'],
                    'code' => $_GET['code']
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $Json =  json_decode($response->getBody());

                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://graph.instagram.com/' . $Json->user_id . '?fields=id,username&access_token=' . $Json->access_token, [
                    'http_errors' => false
                ]);

                if ($response->getStatusCode() == 200) {
                    $UserJson = json_decode($response->getBody());
                    $Rew = true;
                    return [
                        'Provider' => 'Instagram',
                        'Username' => $UserJson->username,
                        'UserID' => $UserJson->id
                    ];
                }
            }
        }
        if ($Rew == false) {
            $this->Error404();
        }
    }

    private function StartSession($Array)
    {
        global $db;

        if ($Array['Provider'] == 'Instagram') {

            $UName = 'i_' . $Array['UserID'];
            $CheckUser = $db->query("SELECT * FROM users WHERE username = '{$UName}'")->fetch(PDO::FETCH_ASSOC);
            if ($CheckUser) {
                if ($CheckUser['status'] == 1) {
                    $this->LoginID($CheckUser['id'], 'Instagram');
                }
            } else {
                $this->RegisterUser($Array);
            }
        } else {
            $Email = $Array['UserEmail'];
            $CheckUser = $db->query("SELECT * FROM users WHERE email = '{$Email}'")->fetch(PDO::FETCH_ASSOC);
            if ($CheckUser) {
                if ($CheckUser['status'] == 1) {
                    $this->LoginID($CheckUser['id'], $Array['Provider']);
                }
            } else {
                $this->RegisterUser($Array);
            }
        }
        echo StaticFunctions::lang('Yönlendiriliyorsunuz...') . '<script>window.close();</script>';
        exit;
    }

    public function LoginID($Uid, $Source)
    {
        global $db;
        StaticFunctions::new_session();

        $UserQuery = $db->query("SELECT * FROM users WHERE id='{$Uid}' and status=1  ")->fetch(PDO::FETCH_ASSOC);
        if (!$UserQuery) {
            $this->Error404();
            exit;
        }

        $UserScLogin = json_decode($UserQuery['user_prefences'], true);

        if (isset($UserScLogin['BannedSocials'])) {
            if (isset($UserScLogin['BannedSocials'][$Source]) && $UserScLogin['BannedSocials'][$Source] == 'banned') {
                StaticFunctions::new_session();
                $_SESSION['SocialLoginError'] = StaticFunctions::lang('Bu hesaba <strong>{0}</strong> yöntemi ile giriş yapamazsınız. Lütfen farklı bir yöntem deneyin.', [
                    $Source
                ]);
                $this->Error404();
                exit;
            }
        }

        require_once CDIR . '/class.account.php';
        $AccountClass = new Account();
        $AccountClass->setDb($db);
        $AccountClass->Login($UserQuery['id'], $Source);

        return null;
    }

    private function RegisterUser($Array)
    {
        global $db;
        if (AllowRegister == true) {
            if ($Array['Provider'] == 'Instagram') {
                $UName = 'i_' . $Array['UserID'];
                $CheckUser = $db->query("SELECT * FROM users WHERE username = '{$UName}'")->fetch(PDO::FETCH_ASSOC);
                if (!$CheckUser) {
                    print_r($Array);
                    exit;
                }
            } else {
                $Email = $Array['UserEmail'];
                $CheckUser = $db->query("SELECT * FROM users WHERE email = '{$Email}'")->fetch(PDO::FETCH_ASSOC);
                if (!$CheckUser) {
                    $AuthLoginProfiles = [
                        'Login' => false,
                        'Google' => false,
                        'Github' => false,
                        'Linkedin' => false,
                        'Facebook' => false,
                        'Instagram' => false
                    ];

                    if ($Array['UserEmail'] == '') {
                        $this->Error404();
                    }

                    if ($Array['RealName'] == '') {
                        $Array['RealName'] = 'User';
                    }

                    $Randoms = StaticFunctions::random(64);

                    if ($Array['Avatar'] == '') {
                        $Array['Avatar'] = StaticFunctions::DefaultAvatar($Array['RealName']);
                    } else {
                        require_once CDIR . '/class.upload.php';
                        $Upload = new Upload();
                        $UploadAvatar = $Upload->UploadAvatar($Array['Avatar'], $Randoms);
                        $Array['Avatar'] = $UploadAvatar;
                    }

                    $AuthLoginProfiles[$Array['Provider']] = true;
                    $RandomPassword = StaticFunctions::random(7);

                    $LastLoginJson = json_encode([
                        'u' => time(),
                        'i' => StaticFunctions::get_ip(),
                        't' => $Array['Provider']
                    ]);

                    $InsertUser = $db->prepare("INSERT INTO users SET
                user_type = ?,
                user_language = ?,
                password = ?,
                phone_code = ?,
                email = ?,
                phone_number = ?,
                email_verify = ?,
                phone_verify = ?,
                balance = ?,
                real_name = ?,
                avatar = ?,
                created_time = ?,
                last_login = ?,
                token = ?,
                user_prefences = ?,
                failed_login = ?,
                status = ?");
                    $insert = $InsertUser->execute(array(
                        'classic', LANG, StaticFunctions::password($RandomPassword),
                        '', $Array['UserEmail'], '', 1, 0, StarterBalance, $Array['RealName'], $Array['Avatar'], time(),
                        $LastLoginJson, $Randoms, json_encode([
                            'AuthorizedProfiles' => $AuthLoginProfiles
                        ]), 0, 1
                    ));
                    if ($insert) {
                        $InsertID = $db->lastInsertId();

                        require_once CDIR . '/class.communication.php';
                        $Email = new EasyBotSend();
                        $Email->Email([
                            'UserID' => $InsertID,
                            'Subject' => StaticFunctions::lang("EasyBot'a Hoşgeldin."),
                            'To' => [
                                'Email' => $Array['UserEmail'],
                                'Name' => $Array['RealName']
                            ]
                        ], 'social_register', [
                            'PRE_HEADER' => StaticFunctions::lang('Seni aramızda görmek muhteşem.'),
                            'EMAIL' => StaticFunctions::lang('E-posta adresin'),
                            'TEMP_PASSWORD_TEXT' => StaticFunctions::lang('Geçici şifren'),
                            'USER_INFORMATION' => StaticFunctions::lang('EASYBOT HESABIN'),
                            'WELCOME' => StaticFunctions::lang("EasyBot'a Hoşgeldin."),
                            'GO_LOGIN' => StaticFunctions::lang("GİRİŞ YAP"),
                            'INFO_TEXT' => StaticFunctions::lang("BİLGİLENDİRME"),
                            'WELCOME_TEXT' => StaticFunctions::lang("Kayıt olduğun için teşekkür ederiz. Hesabına e-posta adresin ile girebilmen için geçici bir şifre oluşturduk. Bu şifreyi aşağıda görebilir ve istediğin zaman değiştirebilirsin."),
                            'ALT_TEXT' => StaticFunctions::lang("<strong>Easybot</strong> hesabına istediğin zaman istediğin sosyal medya hesabınla giriş yapabilirsin. Eğer bağlanılmasını istemediğin bir sosyal medya hesabın varsa <strong>profilim</strong> sayfasından ilgili hesabı kaldırabilirsin."),
                            'USER_EMAIL' => $Array['UserEmail'],
                            'TEMP_PASSWORD' => $RandomPassword,
                        ]);
                        StaticFunctions::new_session();
                        $_SESSION['NewUser'] = true;
                        $this->LoginID($InsertID, $Array['Provider']);
                    } else {
                        $this->Error404();
                    }
                }
            }
        } else {
            StaticFunctions::new_session();
            $_SESSION['SocialLoginError'] = StaticFunctions::lang('Şu anda yeni kayıtlara maalesef izin vermiyoruz.');
        }
    }
}