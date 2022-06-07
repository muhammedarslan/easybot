<?php

StaticFunctions::ajax_form('general');
StaticFunctions::new_session();

if ((isset($_SESSION['SessionValidateResetPass']) && $_SESSION['SessionValidateResetPass'] == 'validated')) {

    $token = StaticFunctions::post('token');
    $p1 = StaticFunctions::post('password1');
    $p2 = StaticFunctions::post('password2');

    $N = time();
    $CheckToken = $db->query("SELECT * FROM reset_password WHERE reset_token='{$token}' and reset_time > $N ")->fetch(PDO::FETCH_ASSOC);

    if ($CheckToken) {
        $Uid = $CheckToken['user_id'];
        $CheckUser = $db->query("SELECT * FROM users WHERE id='{$Uid}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
        if ($CheckUser) {
            if (mb_strlen($p1) < 6) {
                echo StaticFunctions::JsonOutput([
                    'status' => 'failed',
                    'message' => StaticFunctions::lang('Lütfen en az 6 karakter uzunluğunda bir şifre belirleyin.')
                ]);
                exit;
            }

            if ($p1 != $p2) {
                echo StaticFunctions::JsonOutput([
                    'status' => 'failed',
                    'message' => StaticFunctions::lang('Girilen şifreler birbiri ile eşleşmiyor.')
                ]);
                exit;
            }

            $NewPss = StaticFunctions::password($p1);

            $_SESSION['SessionValidateResetPass'] = false;
            unset($_SESSION['SessionValidateResetPass']);

            $LastLoginUpdate = $db->prepare("UPDATE users SET
                     password   = :iki
                     WHERE id = :dort and status=1 ");
            $update = $LastLoginUpdate->execute(array(
                'iki' => $NewPss,
                'dort' => $CheckUser['id']
            ));

            StaticFunctions::AddLog(['ResetPassword' => [
                'UserId' => $CheckUser['id'],
                'UserIp' => StaticFunctions::get_ip(),
                'UserBrowser' => StaticFunctions::getBrowser(),
                'Token' => $token
            ]], $CheckUser['id']);

            $UserID = $CheckUser['id'];
            $delete = $db->exec("DELETE FROM remember_me WHERE user_id = '$UserID' ");
            $delete = $db->exec("DELETE FROM reset_password WHERE user_id = '$UserID' ");

            if ($CheckUser['phone_verify'] == 1) {
                require_once CDIR . '/class.communication.php';
                $Comm = new EasyBotSend();
                $FirstNameExplode = explode(' ', $CheckUser['real_name']);
                $Comm->Sms(
                    $CheckUser['id'],
                    StaticFunctions::lang('Selam {0}, Easybot hesabının şifresi az önce değiştirildi. Eğer bu bilgin dahilinde olmadıysa lütfen en kısa sürede http://easybot.dev/contact adresinden bize haber ver.', [
                        $FirstNameExplode[0]
                    ]),
                    []
                );
            }

            StaticFunctions::new_session();
            if (isset($_SESSION['CheckSession'])) :
                $Me = StaticFunctions::get_id();
                $RememberToken = isset($_COOKIE['RMB']) ? StaticFunctions::clear($_COOKIE['RMB']) : null;

                if ($RememberToken != null) :
                    $delete = $db->exec("DELETE FROM remember_me WHERE user_id= '{$Me}' and remember_token = '{$RememberToken}' ");
                    setcookie("RMB", 'null', time() + 604801, '/', DOMAIN, false, true);
                endif;
                session_destroy();
            endif;

            echo StaticFunctions::JsonOutput([
                'status' => 'success',
                'message' => StaticFunctions::lang('Şifreniz başarıyla değiştirildi, yönlendiriliyorsunuz...')
            ]);
            exit;
        }
    }
}

http_response_code(500);
exit;