<?php

StaticFunctions::ajax_form('general');
StaticFunctions::new_session();

if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') {
    http_response_code(401);
    exit;
} else {

    $Email = StaticFunctions::post('email');

    if ($Email == '' || !filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        echo StaticFunctions::JsonOutput([
            'status' => 'failed',
            'label' => 'danger',
            'title' => 'Bir hata oluştu!',
            'message' => StaticFunctions::lang('Lütfen geçerli bir e-posta adresi giriniz.')
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
                'title' => 'Bir hata oluştu!',
                'message' => StaticFunctions::lang('Recaptha tarafından engellendiniz. Lütfen tekrar deneyiniz.')
            ]);
            exit;
        }

        $UserQuery = $db->query("SELECT * FROM users WHERE email='{$Email}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

        if ($UserQuery) {

            $Uid = $UserQuery['id'];
            $N = time();
            $ResetCheck = $db->query("SELECT * FROM reset_password WHERE user_id='{$Uid}' and reset_time > $N ")->fetch(PDO::FETCH_ASSOC);

            if ($ResetCheck) {
                echo StaticFunctions::JsonOutput([
                    'status' => 'failed',
                    'label' => 'warning',
                    'title' => 'Bir hata oluştu!',
                    'message' => StaticFunctions::lang('Lütfen yeni bir kod almadan önce 30 dakika bekleyiniz.')
                ]);
                exit;
            } else {

                $delete = $db->exec("DELETE FROM reset_password WHERE user_id='$Uid'");
                $Token = StaticFunctions::random(64);
                $InsertQ = $db->prepare("INSERT INTO reset_password SET
			        user_id = ?,
			        reset_token = ?,
			        reset_time = ?");
                $insert = $InsertQ->execute(array(
                    $Uid, $Token, (time() + (60 * 30))
                ));
                if ($insert) {

                    require_once CDIR . '/class.communication.php';
                    $Email = new EasyBotSend();
                    $Email->Email(
                        [
                            'UserID' => $UserQuery['id'],
                            'Subject' => StaticFunctions::lang("Şifremi Unuttum"),
                            'To' => [
                                'Email' => $UserQuery['email'],
                                'Name' => $UserQuery['real_name']
                            ]
                        ],
                        'lost_password',
                        [
                            'PRE_HEADER' => StaticFunctions::lang('Şifremi unuttum talebin hakkında.'),
                            'EMAIL' => StaticFunctions::lang('Sıfırlama Bağlantın'),
                            'GO_LOGIN' => 'ŞİFREMİ SIFIRLA',
                            'RESET_TOKEN' => $Token,
                            'USER_INFORMATION' => StaticFunctions::lang('EASYBOT HESABIN'),
                            'WELCOME' => StaticFunctions::lang("Şifremi Unuttum"),
                            'ALT_TEXT' => StaticFunctions::lang("Eğer bunu sen yapmadıysan lütfen bu maili aldırma. Hesabını senin için güvende tutacağız."),
                            'WELCOME_TEXT' => StaticFunctions::lang("Selam, Easybot hesabının şifresini unuttuğuna dair bir talepte bulundun. Eğer bunu sen yaptıysan aşağıdaki bağlantıyı kullanarak şifreni sıfırlayabilirsin."),
                        ]
                    );

                    echo StaticFunctions::JsonOutput([
                        'status' => 'success',
                        'label' => 'success',
                        'title' => 'Başarıyla gönderildi!',
                        'message' => StaticFunctions::lang('Şifre sıfırlama bağlantın e-posta adresine başarıyla gönderildi. Lütfen spam kutusu dahil tüm gelen kutularını kontrol et.')
                    ]);
                    exit;
                } else {
                    http_response_code(500);
                    exit;
                }
            }
        } else {
            echo StaticFunctions::JsonOutput([
                'status' => 'failed',
                'label' => 'warning',
                'title' => 'Bir hata oluştu!',
                'message' => StaticFunctions::lang('Bu bilgiler ile eşleşen aktif kullanıcı bulunamadı.')
            ]);
            exit;
        }
    }
}