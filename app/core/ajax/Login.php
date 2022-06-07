<?php

StaticFunctions::ajax_form('general');
StaticFunctions::new_session();

if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') {
    http_response_code(401);
    exit;
} else {

    $Email = StaticFunctions::post('email');
    $Password = StaticFunctions::post('password');

    if ($Email == '' || $Password == '' || !filter_var($Email, FILTER_VALIDATE_EMAIL)) {
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
                'message' => StaticFunctions::lang('Recaptha tarafından engellendiniz. Lütfen tekrar deneyiniz.')
            ]);
            exit;
        }

        $LoginType = 'Login';
        $UserQuery = $db->query("SELECT * FROM users WHERE email='{$Email}' ")->fetch(PDO::FETCH_ASSOC);

        if ($UserQuery) {

            if ($UserQuery['password'] != StaticFunctions::password($Password)) {
                $FailedLoginUpdate = $db->prepare("UPDATE users SET
                failed_login   = :iki
                WHERE id = :dort");
                $update = $FailedLoginUpdate->execute(array(
                    'iki' => ($UserQuery['failed_login'] + 1),
                    "dort" => $UserQuery['id']
                ));

                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'http://api.ipinfodb.com/v3/ip-city/?key=' . ProjectDefines::IpInfoDb() . '&format=json&ip=' . StaticFunctions::get_ip(), [
                    'http_errors' => false
                ]);

                if ($response->getStatusCode() == 200) {
                    $StatusCode = json_decode($response->getBody());
                    if ($StatusCode->statusCode != 'ERROR') {
                        $Location = $StatusCode->cityName . ' / ' . $StatusCode->regionName . ' / ' . $StatusCode->countryName;
                    } else {
                        $Location = 'Unknown';
                    }
                } else {
                    $Location = 'Unknown';
                }

                $query = $db->prepare("INSERT INTO failed_login SET
                    user_id = ?,
                    user_ip = ?,
                    user_browser = ?,
                    user_location = ?,
                    system_time = ?");
                $insert = $query->execute(array(
                    $UserQuery['id'], StaticFunctions::get_ip(), json_encode(StaticFunctions::getBrowser()), $Location, time()
                ));
                echo StaticFunctions::JsonOutput([
                    'status' => 'failed',
                    'label' => 'warning',
                    'message' => StaticFunctions::lang('Bu bilgiler ile eşleşen kullanıcı bulunamadı.')
                ]);
                exit;
            }

            if ($UserQuery['status'] != 1) {
                echo StaticFunctions::JsonOutput([
                    'status' => 'failed',
                    'label' => 'danger',
                    'message' => StaticFunctions::lang('Bazı sebeplerden ötürü bu hesap <strong>bloke</strong> edilmiş. Bir hata olduğunu düşünüyorsan bizimle <a href="/contact" >iletişime</a> geç.')
                ]);
                exit;
            }

            require_once CDIR . '/class.account.php';
            $AccountClass = new Account();
            $AccountClass->setDb($db);
            $AccountClass->Login($UserQuery['id'], 'Login');
            $UserID = $UserQuery['id'];

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
                'message' => StaticFunctions::lang('Başarıyla giriş yaptın, yönlendiriliyorsun...')
            ]);
            exit;
        } else {
            echo StaticFunctions::JsonOutput([
                'status' => 'failed',
                'label' => 'warning',
                'message' => StaticFunctions::lang('Bu bilgiler ile eşleşen kullanıcı bulunamadı.')
            ]);
            exit;
        }
    }
}