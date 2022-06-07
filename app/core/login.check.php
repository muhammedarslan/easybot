<?php

if (
    StaticFunctions::clear($_SERVER['SERVER_NAME']) != API_DOMAIN &&
    StaticFunctions::clear($_SERVER['SERVER_NAME']) != PUSH_DOMAIN
) :

    StaticFunctions::new_session();
    $_LoginExplode = explode('/', rtrim(urldecode(strtok($_SERVER["REQUEST_URI"], '?')), '/'));

    if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') {

        $Me = StaticFunctions::get_id();
        $MeQuery = $db->query("SELECT * FROM users WHERE id = '{$Me}' and status='1' ")->fetch(PDO::FETCH_ASSOC);
        if (!$MeQuery) {
            StaticFunctions::LogOut();
            StaticFunctions::go('login');
            exit;
        }

        if (!isset($_SESSION['FailedLoginCount']) || $_SESSION['FailedLoginCount'] != 'false') :
            $_SESSION['FailedLoginCount'] = $MeQuery['failed_login'];
        endif;

        $MyJson = (array) json_decode($MeQuery['user_prefences']);
        if (isset($MyJson['AppMode'])) :
            $AppMode = $MyJson['AppMode'];
        else :
            $AppMode = 'Light';
        endif;
        define('AppMode', $AppMode);

        if (isset($MyJson['MenuClosed'])) :
            $MenuCls = $MyJson['MenuClosed'];
        else :
            $MenuCls = 'open';
        endif;
        define('MenuType', $MenuCls);

        if (!isset($_SESSION['SecurityHash']) || $_SESSION['SecurityHash'] == '') {
            StaticFunctions::LogOut();
            StaticFunctions::go('login');
            exit;
        }

        $SecureHash = $_SESSION['SecurityHash'];
        try {
            $DecodeHash = \Firebase\JWT\JWT::decode($SecureHash, StaticFunctions::JwtKey(), array('HS256'));
        } catch (Exception $e) {
            StaticFunctions::LogOut();
            StaticFunctions::go('login?SecureLayer');
            exit;
        }

        if ($DecodeHash->UserId != $Me || $DecodeHash->UserIp != StaticFunctions::get_ip() || $DecodeHash->UserBrowser != md5($_SERVER['HTTP_USER_AGENT'])) {
            StaticFunctions::LogOut();
            StaticFunctions::go('login?SecureLayer');
            exit;
        }

        if (isset($_LoginExplode[1]) && $_LoginExplode[1] == 'console') {

            require_once CDIR . '/class.security.layer.php';
            $SecureLayer = new SecurityLayer();
            $SecureLayer->setDb($db);
            $SecureLayer->setUser($MeQuery);
            $SecureLayer->Secure();
        }

        if (isset($_LoginExplode[1]) && $_LoginExplode[1] == 'web-service') {

            require_once CDIR . '/class.security.layer.php';
            $SecureLayer = new SecurityLayer();
            $SecureLayer->setDb($db);
            $SecureLayer->setUser($MeQuery);
            $DefineAjaxBlocked = false;

            if (
                !isset($_LoginExplode[2]) || $_LoginExplode[2] != 'security' ||
                !isset($_LoginExplode[3]) || $_LoginExplode[3] != 'layer' || !isset($_LoginExplode[4])
            ) {
                if (!$SecureLayer->IsSecure()) {
                    $DefineAjaxBlocked = true;
                }
            }
            define('AppAjaxBlocked', $DefineAjaxBlocked);
        }

        $UserLanguage = $MeQuery['user_language'];
        if ($UserLanguage != LANG) {
            $UpdateUserLang = $db->prepare("UPDATE users SET
                user_language   = :newLang
                WHERE id = :userID");
            $update = $UpdateUserLang->execute(array(
                'newLang' => LANG,
                'userID' => $MeQuery['id']
            ));
        }
    }

    if (isset($_LoginExplode[1]) && $_LoginExplode[1] == 'console') {

        $Url = rtrim(urldecode(strtok($_SERVER["REQUEST_URI"], '?')));
        if ($Url == '/Log-out' || $Url == '/log-out') $Url = '/';
        if ($Url == '/Login' || $Url == '/login') $Url = '/';
        $Url2 = ($Url != '' && $Url != '/') ? '?next=' . $Url : '';

        if (!isset($_SESSION['CheckSession'])) {

            if (isset($_COOKIE['RMB']) && StaticFunctions::clear($_COOKIE['RMB']) != 'false') {

                $CookieToken = StaticFunctions::clear($_COOKIE['RMB']);
                $Browser     = md5($_SERVER['HTTP_USER_AGENT']);
                $time        = time();


                $CheckRememberToken = $db->query("SELECT * FROM remember_me WHERE remember_token = '{$CookieToken}' and user_browser = '$Browser' and expired_time > $time ")->fetch(PDO::FETCH_ASSOC);
                if ($CheckRememberToken) {

                    $SessionUser = $CheckRememberToken['user_id'];

                    session_regenerate_id();

                    $UserQuery = $db->query("SELECT * FROM users WHERE id = '{$SessionUser}' and status='1' ")->fetch(PDO::FETCH_ASSOC);
                    if ($UserQuery) {

                        require_once CDIR . '/class.account.php';
                        $AccountClass = new Account();
                        $AccountClass->setDb($db);
                        $AccountClass->Login($UserQuery['id'], 'Login', true);

                        StaticFunctions::reload();
                        exit;
                    } else {
                        setcookie("RMB", 'false', time() - 3600, '/', DOMAIN, false, true);
                        header("Location:/login" . $Url2);
                        exit;
                    }
                } else {
                    setcookie("RMB", 'false', time() - 3600, '/', DOMAIN, false, true);
                    header("Location:/login" . $Url2);
                    exit;
                }
            }


            header("Location:/login" . $Url2);
            exit;
        }

        if ($_SESSION['CheckSession'] != 'active') {
            StaticFunctions::LogOut();
            header("Location:/login" . $Url2);
            exit;
        }
    }

endif;