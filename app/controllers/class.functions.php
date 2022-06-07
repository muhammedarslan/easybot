<?php

class StaticFunctions
{

    public static function go($get)
    {
        $URL = PROTOCOL . DOMAIN . PATH . $get;
        echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
        echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
        die(StaticFunctions::lang('Yönlendiriliyorsunuz...'));
    }

    public static function new_session()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function system_down()
    {

        if ($_SERVER['SERVER_NAME'] == API_DOMAIN) :
            if (isset($_GET['format']) && StaticFunctions::clear($_GET['format']) == 'xml') :
                header('Content-Type: application/xml; charset=utf-8');
            else :
                header("Content-type: application/json; charset=utf-8");
            endif;
            echo self::ApiJson([
                'status' => 'failed',
                'error_code' => 'ERR_MAINTENANCE',
                'error_message' => 'we are maintaining our servers to give you a better service. thank you for your understanding.'
            ]);
        else :
            http_response_code(503);
            require_once VDIR . '/system.down.php';
        endif;
        exit;
    }

    public static function JsonOutput($data, $ex = '')
    {
        if (is_array($data)) {

            $DataArray = array(
                'HttpStatus' => 200,
                'Content-type' => 'Application/Json',
                'RequestTime' => date('d-m-Y H:i:s') . ' ' . date_default_timezone_get(),
                'TimeUnix'   => time()
            );

            return  json_encode(array_merge($DataArray, $data), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            return  json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }

    public static function Array2Xml($array, $xml = false)
    {

        if ($xml === false) {
            $xml = new SimpleXMLElement('<response/>');
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::Array2Xml($value, $xml->addChild($key));
            } else {
                $xml->addChild($key, $value);
            }
        }

        return $xml->asXML();
    }

    public static function ApiJson($ResponseData)
    {
        if (isset($_GET['format']) && self::clear($_GET['format']) == 'xml') :
            return self::Array2Xml($ResponseData, false);
        else :
            return json_encode($ResponseData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        endif;
    }

    public static function RemoveaBunchOfSlashes($url)
    {
        $url = PROTOCOL . DOMAIN . PATH . $url;
        $explode = explode('://', $url);
        while (strpos($explode[1], '//'))
            $explode[1] = str_replace('//', '/', $explode[1]);
        return implode('://', $explode);
    }

    public static function clear($mVar)
    {
        if (is_array($mVar)) {
            foreach ($mVar as $gVal => $gVar) {
                if (!is_array($gVar)) {
                    $mVar[$gVal] = htmlspecialchars(strip_tags(urldecode(addslashes(stripslashes(stripslashes(trim(htmlspecialchars_decode($gVar))))))));
                } else {
                    $mVar[$gVal] = self::clear($gVar);
                }
            }
        } else {
            $mVar = htmlspecialchars(strip_tags(urldecode(addslashes(stripslashes(stripslashes(trim(htmlspecialchars_decode($mVar))))))));
        }
        return $mVar;
    }

    public static function ajax_form($AjaxType)
    {

        if ($AjaxType != 'general') {
            StaticFunctions::new_session();
            if (!isset($_SESSION['CheckSession']) || $_SESSION['CheckSession'] != 'active') {
                header("Content-type: application/json; charset=utf-8");
                http_response_code(403);
                echo StaticFunctions::JsonOutput(array(
                    'HttpStatusCode' => 403,
                    'ErrorMessage'   => 'Access Denied.'
                ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;
            }

            if (defined('AppAjaxBlocked') && AppAjaxBlocked == true) {
                http_response_code(401);
                echo self::ApiJson([
                    'status' => 'failed',
                    'process' => 'failed',
                    'title' => self::lang('İstek başarısız oldu!'),
                    'message' => self::lang('Güvenlik katmanı tarafından işleminiz engellendi.')
                ]);
                exit;
            }
        }

        if ($AjaxType == 'validated') {
            $Me = self::get_id();
            global $db;
            $PhoneVerify =  $db->query("SELECT phone_verify FROM users WHERE id='{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);

            if (!$PhoneVerify) {
                self::LogOut();
                http_response_code(401);
                exit;
            }

            if ($PhoneVerify['phone_verify'] != 1) {
                echo self::ApiJson([
                    'status' => 'verifyrequired',
                    'process' => 'failed',
                    'title' => self::lang('İstek başarısız oldu!'),
                    'message' => self::lang('Devam etmek için hesabınızı doğrulamanız gerekmektedir.')
                ]);
                exit;
            }
        }

        return null;
    }

    public static function AjaxCheck()
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            return false;
        }

        if (!isset($_SERVER['HTTP_REFERER'])) {
            return false;
        }

        $AjaxDomain = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
        if ($AjaxDomain != DOMAIN) {
            return false;
        }

        return true;
    }

    public static function BoolText($bool)
    {
        if ($bool) :
            return 'true';
        else :
            return 'false';
        endif;
    }

    public static function PagePasswordValidate($Page)
    {
        self::new_session();
        if (!isset($_SESSION['PagePasswordValidate'][$Page]) || $_SESSION['PagePasswordValidate'][$Page] != 'validated') :
            $_SESSION['ValidatePageID'] = $Page;
            echo self::ApiJson([
                'status' => 'failed',
                'process' => 'failure',
                'validate' => 'required',
                'label' => 'warning',
                'message' => StaticFunctions::lang('Lütfen hesabınızı doğrulayın.')
            ]);
            exit;
        endif;
    }

    public static function get_id()
    {
        StaticFunctions::new_session();
        if (!isset($_SESSION['UserID'])) {
            http_response_code(401);
            exit;
        }
        if ($_SESSION['UserID'] == '') {
            http_response_code(401);
            exit;
        }
        return $_SESSION['UserID'];
    }

    public static function replace_tr($text)
    {
        $text = trim($text);
        $search = array('Ç', 'ç', 'Ğ', 'ğ', 'ı', 'İ', 'Ö', 'ö', 'Ş', 'ş', 'Ü', 'ü', ' ');
        $replace = array('c', 'c', 'g', 'g', 'i', 'i', 'o', 'o', 's', 's', 'u', 'u', '-');
        $new_text = str_replace($search, $replace, $text);
        return $new_text;
    }

    public static function NoBarba()
    {
        if (isset($_SERVER['HTTP_X_BARBA'])) {
            http_response_code(401);
            exit;
        }
    }

    public static function BarbaLoaded($Css, $Js, $AvR)
    {

        if (!isset($Js[0])) :
            $Js = [
                '/assets/console/app-assets/js/core/null.js'
            ];
        endif;

        if (isset($_GET['__a']) && $_GET['__a'] == 1 && self::AjaxCheck()) :
            header('Content-Type: application/json');
            array_reverse($Js);
            array_reverse($Css);

            echo self::JsonOutput([
                'PageCss' => $Css,
                'PageJs' => $Js,
                'AccountVerifyRequired' => $AvR,
                'MenuMode' => MenuType
            ]);
            exit;
        endif;
    }

    public static function reload_session()
    {
        global $db;
        StaticFunctions::new_session();
        $User = $_SESSION['UserSession']->id;
        $SocialClass = new EasybotSocialLogin();
        $SocialClass->LoginID($User, 'Login');
        return true;
    }

    public static function say($key)
    {
        return stripslashes($key);
    }

    public static function lang($key, $Ar = [])
    {

        $AllowedLangs = AppLanguage::$AllowedLangs;

        if (LANG == 'tr') :
            $text =  stripslashes($key);
        else :
            $LangFile = (isset($AllowedLangs[LANG])) ? $AllowedLangs[LANG]['LangFile'] : null;
            $text = stripslashes($key);
            if ($LangFile != null && file_exists(APP_DIR . '/lang/' . $LangFile . '.php')) :
                require_once APP_DIR . '/lang/' . $LangFile . '.php';
                if (isset($LangArray) && isset($LangArray[$key])) :
                    $text = stripslashes($LangArray[$key]);
                endif;
            endif;
        endif;

        foreach ($Ar as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }

        return $text;
    }

    public static function random($get)
    {
        $token = bin2hex(openssl_random_pseudo_bytes($get));
        return $token;
    }

    public static function random_with_time($get)
    {
        $token = bin2hex(openssl_random_pseudo_bytes($get));
        $unix_time = time();
        $token2 = substr($token, 0, 20);
        $token3 = str_replace($token2, '', $token);
        $token = $token2 . $unix_time . $token3;
        return md5($token);
    }

    public static function DefaultAvatar($Name)
    {
        $Avatar = 'default/' . strtoupper(substr(self::replace_tr($Name), 0, 1)) . '.png';

        if (file_exists(ROOT_DIR . '/assets/media/avatars/' . $Avatar)) {
            return $Avatar;
        } else {
            return 'default/B.png';
        }
    }

    public static function UserAvatar($Avatar)
    {
        if (mb_substr($Avatar, 0, 4) == 'http') {
            return $Avatar;
        } else {
            return '/assets/media/avatars/' . $Avatar;
        }
    }

    public static function timerFormat($start_time, $end_time, $std_format = false)
    {
        $total_time = $end_time - $start_time;
        $days       = floor($total_time / 86400);
        $hours      = floor($total_time / 3600);
        $minutes    = intval(($total_time / 60) % 60);
        $seconds    = intval($total_time % 60);
        $results = "";
        if ($std_format == false) {
            if ($days > 0) {
                $results .= $days . (($days > 1) ? " " . StaticFunctions::lang('gün') . " " : " " . StaticFunctions::lang('gün') . " ");
            }
            if ($hours > 0) {
                $results .= $hours . (($hours > 1) ? " " . StaticFunctions::lang('saat') . " " : " " . StaticFunctions::lang('saat') . " ");
            }
            if ($minutes > 0) {
                $results .= $minutes . (($minutes > 1) ? " " . StaticFunctions::lang('dk') . " " : " " . StaticFunctions::lang('dk') . " ");
            }
            if ($seconds > 0) {
                $results .= $seconds . (($seconds > 1) ? " " . StaticFunctions::lang('sn') . " " : " " . StaticFunctions::lang('sn') . " ");
            }

            if ($seconds > 0) {
                $result = $seconds . ' ' . StaticFunctions::lang('sn');
            }
            if ($minutes > 0) {
                $result = $minutes . ' ' . StaticFunctions::lang('dk');
            }
            if ($hours > 0) {
                $result = $hours . ' ' . StaticFunctions::lang('saat');
            }
            if ($days > 0) {
                $result = $days . ' ' . StaticFunctions::lang('gün');
            }
        }
        if (!isset($result) || $result == '') {
            return '0 ' . StaticFunctions::lang('sn');
        } else {
            return $result;
        }
    }

    public static function post($query)
    {
        if (isset($_POST[$query]) && StaticFunctions::clear($_POST[$query]) != '') {
            return StaticFunctions::clear($_POST[$query]);
        } else {
            return '';
        }
    }

    public static function getBrowser($agent = null)
    {
        $u_agent = ($agent != null) ? $agent : $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'Mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'Windows';
        }

        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
        }

        $i = count($matches['browser']);
        if ($i != 1) {
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform
        );
    }


    public static function load_page($PageOptions)
    {

        $Page = $PageOptions['Class'];
        $FileName = $PageOptions['View'];
        $Params = StaticFunctions::clear($PageOptions['Params']);
        $Title = $PageOptions['Title'];

        if (!file_exists(VDIR . '/' . $Page . '.' . $FileName . '.php')) {
            require_once VDIR . '/page.404.php';
            exit;
        } else {
            $_Params = $Params;
            $__PageTitle = $Title . ' ' . PR_NAME;
            global $db;
            require_once VDIR . '/' . $Page . '.' . $FileName . '.php';
        }
    }

    public static function go_home()
    {
        $URL = PROTOCOL . DOMAIN . PATH;
        echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
        echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
        die(StaticFunctions::lang('Yönlendiriliyorsunuz...'));
    }

    public static function reload()
    {
        $URL = $_SERVER['REQUEST_URI'];
        echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
        echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';
        die(StaticFunctions::lang('Yönlendiriliyorsunuz...'));
    }

    public static function password($query)
    {
        $pass = sha1(base64_encode(md5(base64_encode($query))));
        $end = substr($pass, 5, 32);
        return $end;
    }

    public static function get_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        // For Localhost in development mode.
        if ($ipaddress == '::1' && Debug == true) {
            $ipaddress = '176.88.28.132';
        }

        return $ipaddress;
    }

    public static function JwtKey()
    {
        return ProjectDefines::JwtSecretKey()['EasyBot'];
    }

    public static function AddLog($LogData, $Uid = '-')
    {
        global $db;

        if ($Uid == '-') {
            $Uid = StaticFunctions::get_id();
        }

        $CheckLog = $db->query("SELECT id FROM log WHERE user_id = " . $Uid . " and log_data='" . json_encode($LogData) . "' ")->fetch(PDO::FETCH_ASSOC);
        if (!$CheckLog) {
            $InsertLog = $db->prepare("INSERT INTO log SET
            user_id = :bir,
            log_data = :iki,
            log_time = :uc");
            $insert = $InsertLog->execute(array(
                "bir" => $Uid,
                "iki" => json_encode($LogData),
                "uc" => time()
            ));
        }

        return null;
    }

    public static function ErrorLog($LogData, $Uid = '-')
    {
        global $db;

        if ($Uid == '-') {
            $Uid = 0;
        }

        $CheckLog = $db->query("SELECT id FROM error_log WHERE user_id = " . $Uid . " and log_data='" . json_encode($LogData) . "' ")->fetch(PDO::FETCH_ASSOC);
        if (!$CheckLog) {
            $InsertLog = $db->prepare("INSERT INTO error_log SET
            user_id = :bir,
            log_data = :iki,
            log_time = :uc");
            $insert = $InsertLog->execute(array(
                "bir" => $Uid,
                "iki" => json_encode($LogData),
                "uc" => time()
            ));
        }

        return null;
    }

    public static function seo_link($text)
    {
        $text  = str_replace('&', '', $text);
        $find = array("/Ğ/", "/Ü/", "/Ş/", "/İ/", "/Ö/", "/Ç/", "/ğ/", "/ü/", "/ş/", "/ı/", "/ö/", "/ç/");
        $degis = array("G", "U", "S", "I", "O", "C", "g", "u", "s", "i", "o", "c");
        $text = preg_replace("/[^0-9a-zA-ZÄzÜŞİÖÇğüşıöç]/", " ", $text);
        $text = preg_replace($find, $degis, $text);
        $text = preg_replace("/ +/", " ", $text);
        $text = preg_replace("/ /", "-", $text);
        $text = preg_replace("/\s/", "", $text);
        $text = strtolower($text);
        $text = preg_replace("/^-/", "", $text);
        $text = preg_replace("/-$/", "", $text);
        $text = str_replace('-amp-', '-', $text);
        return $text;
    }

    public static function validate_url($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);

        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }

    public static function LogOut()
    {
        global $db;
        self::new_session();
        if (isset($_SESSION['CheckSession'])) :
            $Me = self::get_id();
            $RememberToken = isset($_COOKIE['RMB']) ? self::clear($_COOKIE['RMB']) : null;

            if ($RememberToken != null) :
                $delete = $db->exec("DELETE FROM remember_me WHERE user_id= '{$Me}' and remember_token = '{$RememberToken}' ");
                setcookie("RMB", 'null', time() + 604801, '/', DOMAIN, false, true);
            endif;
            session_destroy();
        endif;
        self::go('login');
        exit();
    }

    public static function ConsoleBreadCrumb($Br)
    {
        $Breadcrumb = '';
        if (!$Br['isActive']) {
            return $Breadcrumb;
        }

        $Breadcrumb .= '<div id="ConsoleBreadCrumb" class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">' . self::say($Br['list']['active']) . '</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">';

        foreach ($Br['list'] as $key => $item) {
            if ($key == 'active') {
                $Breadcrumb .= '<li class="breadcrumb-item active">' . self::say($item) . '</li>';
            } else {
                $Breadcrumb .= '<li class="breadcrumb-item"><a href="' . $key . '">' . self::say($item) . '</a></li>';
            }
        }

        $Breadcrumb .= '</ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return $Breadcrumb;
    }

    public static function fast_request($url)
    {
        $parts = parse_url($url);
        $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);
        $out = "GET " . $parts['path'] . " HTTP/1.1\r\n";
        $out .= "Host: " . $parts['host'] . "\r\n";
        $out .= "Content-Length: 0" . "\r\n";
        $out .= "Connection: Close\r\n\r\n";

        fwrite($fp, $out);
        fclose($fp);
    }
}