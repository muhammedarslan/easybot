<?php

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

$Layer = StaticFunctions::post('layer');

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
        $MyEmail = $MeQuery['email'];
        $MyLast  = $_SESSION['SecureLayer2_LastType'];
        $Now = time();
        $IsSend = $db->query("SELECT * FROM pin_codes WHERE user_id = '{$Me}' and process_type='layer2' and last_time > $Now ")->fetch(PDO::FETCH_ASSOC);

        if (!$IsSend) {

            $Pn1 = rand(1, 9);
            $Pn2 = rand(0, 9);
            $Pn3 = rand(0, 9);
            $Pn4 = rand(0, 9);
            $Pn5 = rand(0, 9);
            $Pn6 = rand(1, 9);
            $PinCode = $Pn1 . $Pn2 . $Pn3 . $Pn4 . $Pn5 . $Pn6;

            $DeletePins = $db->exec("DELETE FROM pin_codes WHERE process_type='layer2' and user_id='{$Me}' ");
            $InsertPin = $db->prepare("INSERT INTO pin_codes SET
            user_id = ?,
            pin_code = ?,
            process_type = ?,
            last_time = ?");
            $insert = $InsertPin->execute(array(
                $Me, $PinCode, 'layer2', time() + (60 * 5)
            ));

            require_once CDIR . '/class.communication.php';
            $Email = new EasyBotSend();
            $Email->Email(
                [
                    'UserID' => $Me,
                    'Subject' => StaticFunctions::lang("Hesabınızı Doğrulayın"),
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
                    'WELCOME' => StaticFunctions::lang("Hesap Güvenliği"),
                    'ALT_TEXT' => StaticFunctions::lang("Eğer bunu siz yapmadıysanız lütfen bu maili aldırmayın. Hesabınızı sizin için güvende tutacağız."),
                    'WELCOME_TEXT' => StaticFunctions::lang("Hesabınıza yeni bir giriş yöntemi ile bağlanmaya çalıştınız. Eğer bunu yapan siz iseniz aşağıdaki pin kodu ile hesabınızı doğrulayabilirsiniz."),
                ]
            );
            echo StaticFunctions::JsonOutput([
                'status' => 'success',
                'label' => 'success',
                'message' => StaticFunctions::lang('Yeni pin kodu başarıyla gönderildi.')
            ]);
            exit;
        } else {
            echo StaticFunctions::JsonOutput([
                'status' => 'failed',
                'label' => 'warning',
                'message' => StaticFunctions::lang('5 dakika içerisinde yalnız 1 kod alabilirsiniz.')
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