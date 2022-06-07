<?php


class AppLanguage
{

    public static $AllowedLangs = [
        'tr' => [
            'LangName' => 'Türkçe',
            'LangFile' => 'original_content'
        ],
        'en' => [
            'LangName' => 'English (US)',
            'LangFile' => 'en_US'
        ],
        'gb' => [
            'LangName' => 'English (UK)',
            'LangFile' => 'en_GB'
        ],
        'de' => [
            'LangName' => 'Deutsch',
            'LangFile' => 'de_DE'
        ],
        'es' => [
            'LangName' => 'Español',
            'LangFile' => 'es_ES'
        ],
        'fr' => [
            'LangName' => 'Français',
            'LangFile' => 'fr_FR'
        ],
        'it' => [
            'LangName' => 'Italiano',
            'LangFile' => 'it_IT'
        ]
    ];

    private static function GetNonLangPages()
    {
        return [
            'console',
            'web-service',
            'login',
            'register',
            'go',
            'social-login',
            'social-callback',
            'mail',
            'reset-password',
            'email',
            'set',
            'community'
        ];
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

    public static function UrlMaker($Url)
    {
        if (self::clear($_SERVER['SERVER_NAME']) == API_DOMAIN) :
            return 'en';
        else :
            $UrlExplode = explode('/', rtrim(urldecode(strtok($Url, '?')), '/'));
            if (!isset($UrlExplode[1]) || !in_array($UrlExplode[1], self::GetNonLangPages())) :
                if (!isset($UrlExplode[1]) || !isset(self::$AllowedLangs[$UrlExplode[1]])) :
                    header("Location:/" . self::getLang() . $_SERVER['REQUEST_URI']);
                    exit;
                endif;

                unset($UrlExplode[1]);
                $NewUrl = '';
                foreach ($UrlExplode as $key => $value) {
                    $NewUrl .=  $value . '/';
                }
                if (isset($UrlExplode[2]) && in_array($UrlExplode[2], self::GetNonLangPages())) :
                    header("Location:" . $NewUrl);
                    exit;
                endif;

                return $NewUrl;
            else :
                return $Url;
            endif;
        endif;
    }

    private static function CheckPage()
    {
        $route_path = rtrim(urldecode(strtok($_SERVER["REQUEST_URI"], '?')), '/');
        $route_path = (str_replace(PATH, '/', $route_path) == '') ? '/' : str_replace(PATH, '/', $route_path);
        $UrlExplode = explode('/', rtrim(urldecode(strtok($route_path, '?')), '/'));
        if (!isset($UrlExplode[1]) || !in_array($UrlExplode[1], self::GetNonLangPages())) :
            return 'LanguagePage';
        else :
            return 'NonLanguagePage';
        endif;
    }

    private static function GetDefault()
    {
        $PublicDefault = 'en';

        if (
            isset($_SERVER['HTTP_CF_IPCOUNTRY']) && $_SERVER['HTTP_CF_IPCOUNTRY'] != ''
            && isset(self::$AllowedLangs[mb_strtolower(self::clear($_SERVER['HTTP_CF_IPCOUNTRY']))])
        ) :
            $PublicDefault = mb_strtolower(self::clear($_SERVER['HTTP_CF_IPCOUNTRY']));
        else :
            if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) :
                $BrowserLang = mb_strtolower(substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2));
                if (isset(self::$AllowedLangs[$BrowserLang])) :
                    $PublicDefault = $BrowserLang;
                endif;
            endif;
        endif;

        return $PublicDefault;
    }

    public static function CookieLang()
    {
        if (isset($_COOKIE['AppLang']) && isset(self::$AllowedLangs[self::clear($_COOKIE['AppLang'])])) :
            return self::clear($_COOKIE['AppLang']);
        else :
            return null;
        endif;
    }

    public static function CheckUrlLanguage($UrlLang)
    {
        if (self::CookieLang() != null) :
            if (self::CookieLang() != $UrlLang) :
                return false;
            endif;
        else :
            self::SetLang($UrlLang);
            return true;
        endif;
    }

    public static function NoBarba()
    {
        if (isset($_SERVER['HTTP_X_BARBA'])) {
            http_response_code(401);
            exit;
        }
    }

    public static function SetLang($Lang)
    {
        self::NoBarba();
        if (isset(self::$AllowedLangs[$Lang])) :
            setcookie('AppLang', $Lang, time() + 60 * 60 * 24 * 30, '/');
        endif;

        if (self::CheckPage() == 'LanguagePage') :
            if (isset($_GET['hl'])) :
                $route_path = rtrim(urldecode(strtok($_SERVER["REQUEST_URI"], '?')), '/');
                $route_path = (str_replace(PATH, '/', $route_path) == '') ? '/' : str_replace(PATH, '/', $route_path);
                $UrlExplode = explode('/', rtrim(urldecode(strtok($route_path, '?')), '/'));
                unset($UrlExplode[1]);
                $NewUrl = $Lang;
                foreach ($UrlExplode as $key => $value) {
                    $NewUrl .=  $value . '/';
                }
                header("Location:/" . $NewUrl);
                exit;
            endif;
        else :
            if (isset($_GET['hl'])) :
                $route_path = trim(urldecode(strtok($_SERVER["REQUEST_URI"], '?')), '/');
                header("Location:/" . $route_path);
                exit;
            endif;
        endif;

        return null;
    }

    public static function getLang()
    {
        $Page       = AppLanguage::CheckPage();
        $ReturnLang = AppLanguage::GetDefault();
        $CookieLang = (AppLanguage::CookieLang() != null) ? AppLanguage::CookieLang() : $ReturnLang;

        if ($Page == 'LanguagePage') :

            $route_path = rtrim(urldecode(strtok($_SERVER["REQUEST_URI"], '?')), '/');
            $route_path = (str_replace(PATH, '/', $route_path) == '') ? '/' : str_replace(PATH, '/', $route_path);
            $UrlExplode = explode('/', rtrim(urldecode(strtok($route_path, '?')), '/'));
            $UrlLanguage = (isset($UrlExplode[1])) ? $UrlExplode[1] : $CookieLang;

            if (!isset(self::$AllowedLangs[$UrlLanguage])) :
                $UrlLanguage = $CookieLang;
            endif;

            if (!self::CheckUrlLanguage($UrlLanguage)) :
                self::SetLang($UrlLanguage);
            endif;
            return $UrlLanguage;
        else :
            return $CookieLang;
        endif;
    }
}