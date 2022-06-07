<?php

class ProjectDefines
{
    public static function Db()
    {
        return [
            'Host'     => 'localhost',
            'Name'     => 'easybot',
            'CharSet'  => 'utf8',
            'User'     => 'root',
            'Password' => ''
        ];
    }

    public static function EasyAddress()
    {
        return [
            'Protocol' => 'http://',
            'Domain'   => 'localhost',
            'Path'     => '/',
            'Version'  => '1.0.0',
            'Timezone' => 'Europe/Istanbul',
            'Header'   => '| EasyBot'
        ];
    }

    public static function EasySetup()
    {
        return [
            'DebugMode'       => true,
            'MaintenanceMode' => false,
            'AllowRegister'   => true,
            'RegisterBalance' => 0.00
        ];
    }

    public static function EasyVersion()
    {
        if (self::EasySetup()['DebugMode']) {
            return time();
        } else {
            return self::EasyAddress()['Version'];
        }
    }

    public static function JwtSecretKey()
    {
        return [
            'EasyBot' => '',
            'Community' => ''
        ];
    }

    public static function RecaptchaV2()
    {
        return  [
            'SiteKey'   => '',
            'SecretKey' => ''
        ];
    }

    public static function RecaptchaV3()
    {
        return  [
            'SiteKey'   => '',
            'SecretKey' => ''
        ];
    }

    public static function SocialLogin()
    {
        return [
            'GithubApp'     => [
                'AppID'     => '',
                'AppSecret' => ''
            ],
            'GoogleApp'     => [
                'AppID'     => '',
                'AppSecret' => ''
            ],
            'LinkedinApp'   => [
                'AppID'     => '',
                'AppSecret' => ''
            ],
            'FacebookApp'   => [
                'AppID'     => '',
                'AppSecret' => ''
            ],
            'InstagramApp'  => [
                'AppID'     => '',
                'AppSecret' => ''
            ]
        ];
    }

    public static function SendgridApiKey()
    {
        return '';
    }

    public static function SmsApi()
    {
        return [
            'NetGsm'     => [
                'SmsUser'  => '',
                'SmsPass'  => '',
                'SmsTitle' => ''
            ],
            'Twilio'     => [
                'Sid'      => '',
                'Token'    => ''
            ]
        ];
    }

    public static function FirebaseConfig()
    {
        return [
            'apiKey'            => "",
            'authDomain'        => "",
            'databaseURL'       => "",
            'projectId'         => "",
            'storageBucket'     => "",
            'messagingSenderId' => "",
            'appId'             => ""
        ];
    }

    public static function FirebaseCloudMsg()
    {
        return [
            'ServerKey' => '',
            'SenderId'  => ''
        ];
    }

    public static function LuminatiProxy()
    {
        return [
            'proxy' => '',
            'port' => ,
            'user' => '',
            'password' => ''
        ];
    }

    public static function Aws()
    {
        return [
            'region'      => '',
            'version'     => '',
            'credentials' => [
                'key'     => '',
                'secret'  => '',
            ]
        ];
    }

    public static function AwsBucket()
    {
        return 'easybot-';
    }

    public static function AwsBase()
    {
        return '';
    }

    public static function IpInfoDb()
    {
        return '';
    }
}


// Some defines.
define('PROTOCOL', ProjectDefines::EasyAddress()['Protocol']);
define('DOMAIN', ProjectDefines::EasyAddress()['Domain']);
define('API_DOMAIN', 'api.' . DOMAIN);
define('PUSH_DOMAIN', 'notify.' . DOMAIN);
define('PATH', ProjectDefines::EasyAddress()['Path']);
define('PR_NAME', ProjectDefines::EasyAddress()['Header']);
define('Version', ProjectDefines::EasyVersion());

define('MaintenanceMode', ProjectDefines::EasySetup()['MaintenanceMode']);
define('AllowRegister', ProjectDefines::EasySetup()['AllowRegister']);
define('StarterBalance', ProjectDefines::EasySetup()['RegisterBalance']);
define('Debug', ProjectDefines::EasySetup()['DebugMode']);

// Timezone.
date_default_timezone_set(ProjectDefines::EasyAddress()['Timezone']);