<?php

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

$Layer = StaticFunctions::post('layer');
$Pin = StaticFunctions::post('pin');

if ($Pin == '') {
    echo StaticFunctions::JsonOutput([
        'status' => 'failed',
        'label' => 'danger',
        'message' => StaticFunctions::lang('Geçersiz pin.')
    ]);
    exit;
}

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

$ErrType1 = (isset($_SESSION['SecureLevel_2Factor']) && $_SESSION['SecureLevel_2Factor'] == true) ? true : false;
$ErrType2 = (isset($_SESSION['SecureLevel_Auth'])  && $_SESSION['SecureLevel_Auth'] == true) ? true : false;
$ErrType3 = (isset($_SESSION['SecureLevel_FailedLogin']) && $_SESSION['SecureLevel_FailedLogin'] == true) ? true : false;


switch ($Layer) {
    case 1:
        if (!$ErrType1) {
            http_response_code(401);
            exit;
        }
        // Code.
        break;
    case 2:
        if (!$ErrType2) {
            http_response_code(401);
            exit;
        }

        $Me = StaticFunctions::get_id();
        global $db;
        $MeQuery = $db->query("SELECT * FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
        $Now = time();
        $IsSend = $db->query("SELECT * FROM pin_codes WHERE user_id = '{$Me}' and pin_code='{$Pin}' and process_type='layer2' and last_time > $Now ")->fetch(PDO::FETCH_ASSOC);
        if ($IsSend) {
            $DeletePins = $db->exec("DELETE FROM pin_codes WHERE process_type='layer2' and user_id='{$Me}' ");
            $_SESSION['SecureLevel_Auth'] = false;
            unset($_SESSION['SecureLevel_Auth']);
            $MyLast  = $_SESSION['SecureLevel_AuthChannel'];
            $MyPrefences = json_decode($MeQuery['user_prefences'], true);
            $Accepted = $MyPrefences['AuthLoginProfiles'];
            $Accepted[$MyLast] = true;
            $MyPrefences['AuthLoginProfiles'] = $Accepted;
            $MyNewPrefences = json_encode($MyNewPrefences);

            if ($Accepted['Login'] == true) {

                $LastLoginUpdate = $db->prepare("UPDATE users SET
                     user_prefences   = :iki,
                     email_verify = :em,
                     failed_login    = :lty
                     WHERE id = :dort");
                $update = $LastLoginUpdate->execute(array(
                    'iki' => $MyNewPrefences,
                    "lty" => 0,
                    "em" => 1,
                    'dort' => $MeQuery['id']
                ));
            } else {

                $LastLoginUpdate = $db->prepare("UPDATE users SET
                     user_prefences   = :iki,
                     failed_login    = :lty
                     WHERE id = :dort");
                $update = $LastLoginUpdate->execute(array(
                    'iki' => $MyNewPrefences,
                    "lty" => 0,
                    'dort' => $MeQuery['id']
                ));
            }

            echo StaticFunctions::JsonOutput([
                'status' => 'success',
                'label' => 'success',
                'message' => StaticFunctions::lang('Hesabınızı başarıyla doğruladınız, yönlendiriliyorsunuz...')
            ]);
            exit;
        } else {
            echo StaticFunctions::JsonOutput([
                'status' => 'failed',
                'label' => 'warning',
                'message' => StaticFunctions::lang('Lütfen pin kodunu kontrol ediniz veya yeni kod isteyiniz.')
            ]);
            exit;
        }

        break;
    case 3:
        if (!$ErrType3) {
            http_response_code(401);
            exit;
        }
        // Code.
        break;

    default:
        http_response_code(401);
        break;
}