<?php

StaticFunctions::ajax_form('general');
StaticFunctions::new_session();

if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') {
    http_response_code(401);
    exit;
} else {

    $Email = StaticFunctions::post('email');
    $Password = StaticFunctions::post('password');
    $NameSurname = StaticFunctions::post('name_surname');

    if ($Email == '' || $Password == '' || $NameSurname == '' || !filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        echo StaticFunctions::JsonOutput([
            'status' => 'failed',
            'label' => 'danger',
            'message' => StaticFunctions::lang('Lütfen tüm alanları doldurunuz.')
        ]);
        exit;
    } else {

        $recaptcha = new \ReCaptcha\ReCaptcha(ProjectDefines::RecaptchaV3()['SecretKey']);
        $resp = $recaptcha->setExpectedAction('login')
            ->setScoreThreshold(0.3)
            ->verify(StaticFunctions::post('recaptcha_token'), StaticFunctions::get_ip());

        if (!$resp->isSuccess()) {
            echo StaticFunctions::JsonOutput([
                'status' => 'failed',
                'label' => 'danger',
                'message' => StaticFunctions::lang('Recaptha tarafından engellendiniz. Tekrar deneyiniz.')
            ]);
            exit;
        }

        if (!AllowRegister) {
            echo StaticFunctions::JsonOutput([
                'status' => 'failed',
                'label' => 'danger',
                'message' => StaticFunctions::lang('Şu anda yeni kayıtlara maalesef izin vermiyoruz.')
            ]);
            exit;
        }

        if (StaticFunctions::post('frm_terms') != 'on') {
            echo StaticFunctions::JsonOutput([
                'status' => 'failed',
                'label' => 'primary',
                'message' => StaticFunctions::lang('Kullanım şartlarını kabul etmeniz gerekmektedir.')
            ]);
            exit;
        }

        $LoginType = 'Login';
        $UserQuery = $db->query("SELECT * FROM users WHERE email='{$Email}' ")->fetch(PDO::FETCH_ASSOC);

        if (!$UserQuery) {

            if (mb_strlen($Password) < 6) {
                echo StaticFunctions::JsonOutput([
                    'status' => 'failed',
                    'label' => 'danger',
                    'message' => StaticFunctions::lang('Lütfen en az 6 karakter uzunluğunda bir şifre belirleyin.')
                ]);
                exit;
            }

            $AuthLoginProfiles = [
                'Login' => false,
                'Google' => false,
                'Github' => false,
                'Linkedin' => false,
                'Facebook' => false,
                'Instagram' => false
            ];


            $Avatar = StaticFunctions::DefaultAvatar($NameSurname);

            $LastLoginJson = json_encode([
                'u' => time(),
                'i' => StaticFunctions::get_ip(),
                't' => 'Login'
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
                'classic', LANG, StaticFunctions::password($Password),
                '', $Email, '', 0, 0, StarterBalance, $NameSurname, $Avatar, time(),
                $LastLoginJson, StaticFunctions::random(64), json_encode([
                    'AuthorizedProfiles' => $AuthLoginProfiles
                ]), 0, 1
            ));
            $InsertID = $db->lastInsertId();
            $UserQuery = $db->query("SELECT * FROM users WHERE id='{$InsertID}' ")->fetch(PDO::FETCH_ASSOC);
            $_SESSION['NewUser'] = true;

            require_once CDIR . '/class.account.php';
            $AccountClass = new Account();
            $AccountClass->setDb($db);
            $AccountClass->Login($UserQuery['id'], 'Login');

            require_once CDIR . '/class.security.layer.php';
            $SecureLayer = new SecurityLayer();
            $SecureLayer->setDb($db);
            $SecureLayer->setUser($UserQuery);
            $SecureLayer->sendRegisterPin();

            $UserID = $UserQuery['id'];
            $delete = $db->exec("DELETE FROM remember_me WHERE user_id = '$UserID' ");

            if (isset($_POST['remember_me']) && StaticFunctions::post('remember_me') == 'on') {

                $NewToken = StaticFunctions::random(46);

                $InsertRememberMe = $db->prepare("INSERT INTO remember_me SET
                 user_id = :bir,
                remember_token = :iki,
                 expired_time = :uc,
                user_browser = :dort");
                $insert = $InsertRememberMe->execute(array(
                    "bir" => $UserID,
                    "iki" => $NewToken,
                    "uc" => time() + 604800,
                    'dort' => md5($_SERVER['HTTP_USER_AGENT'])

                ));
                setcookie("RMB", $NewToken, time() + 604801, '/', DOMAIN, false, true);
            }

            echo StaticFunctions::JsonOutput([
                'status' => 'success',
                'label' => 'success',
                'message' => StaticFunctions::lang('Hesabınız başarıyla oluşturuldu, lütfen e-posta adresinize gönderilen kodu onaylayınız.')
            ]);
            exit;
        } else {
            echo StaticFunctions::JsonOutput([
                'status' => 'failed',
                'label' => 'warning',
                'message' => StaticFunctions::lang('Bu bilgiler ile kayıtlı bir hesap zaten mevcut.')
            ]);
            exit;
        }
    }
}