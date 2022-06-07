<?php

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

$Me = StaticFunctions::get_id();
$User = $db->query("SELECT * FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
if (!$User) {
    $_SESSION['CheckSession'] = false;
    if (isset($_SESSION['CheckSession'])) :
        $Me = StaticFunctions::get_id();
        $RememberToken = isset($_COOKIE['RMB']) ? StaticFunctions::clear($_COOKIE['RMB']) : null;

        if ($RememberToken != null) :
            $delete = $db->exec("DELETE FROM remember_me WHERE user_id= '{$Me}' and remember_token = '{$RememberToken}' ");
            setcookie("RMB", 'null', time() + 604801, '/', DOMAIN, false, true);
        endif;
        session_destroy();
    endif;
    http_response_code(401);
    exit;
}

$UserPrefences = json_decode($User['user_prefences'], true);

$PostData = StaticFunctions::post('channels');

if ($PostData['active'] == 'false') {

    $UserPrefences['BannedSocials']['Google'] = 'banned';
    $UserPrefences['BannedSocials']['Github'] = 'banned';
    $UserPrefences['BannedSocials']['Linkedin'] = 'banned';
    $UserPrefences['BannedSocials']['Facebook'] = 'banned';
    $UserExtraJson = json_encode($UserPrefences);

    $UserPrefencesUpdate = $db->prepare("UPDATE users SET
     user_prefences   = :iki
     WHERE id = :dort and status=1 ");
    $update = $UserPrefencesUpdate->execute(array(
        'iki' => $UserExtraJson,
        'dort' => $User['id']
    ));

    $Stats = [
        'socialLoginActive' => false,
        'socialLoginLabel' => StaticFunctions::lang('Sosyal medya ile hızlı giriş pasif'),
        'bannedSocials' => [
            'Google' => [
                'isActive' => false,
            ],
            'Github' => [
                'isActive' => false,
            ],
            'Linkedin' => [
                'isActive' => false,
            ],
            'Facebook' => [
                'isActive' => false,
            ]
        ]
    ];
} else {

    $UserPrefences['BannedSocials']['Google'] = 'banned';
    $UserPrefences['BannedSocials']['Github'] = 'banned';
    $UserPrefences['BannedSocials']['Linkedin'] = 'banned';
    $UserPrefences['BannedSocials']['Facebook'] = 'banned';

    $SocialsArray = [
        'Google' => [
            'isActive' => false,
        ],
        'Github' => [
            'isActive' => false,
        ],
        'Linkedin' => [
            'isActive' => false,
        ],
        'Facebook' => [
            'isActive' => false,
        ]
    ];

    $IsAllActive = false;
    $IsActiveLabel = StaticFunctions::lang('Sosyal medya ile hızlı giriş pasif');

    if ($PostData['google'] == 'true') {
        $UserPrefences['BannedSocials']['Google'] = 'allowed';
        $SocialsArray['Google']['isActive'] = true;
    }

    if ($PostData['github'] == 'true') {
        $UserPrefences['BannedSocials']['Github'] = 'allowed';
        $SocialsArray['Github']['isActive'] = true;
    }

    if ($PostData['linkedin'] == 'true') {
        $UserPrefences['BannedSocials']['Linkedin'] = 'allowed';
        $SocialsArray['Linkedin']['isActive'] = true;
    }

    if ($PostData['facebook'] == 'true') {
        $UserPrefences['BannedSocials']['Facebook'] = 'allowed';
        $SocialsArray['Facebook']['isActive'] = true;
    }

    foreach ($SocialsArray as $key => $value) {
        if ($value['isActive'] == true) {
            $IsAllActive = true;
            $IsActiveLabel = StaticFunctions::lang('Sosyal medya ile hızlı giriş aktif');
            break;
        }
    }


    if (!$IsAllActive) {

        if ($PostData['topButton'] == 'true') {

            $IsAllActive = true;
            $IsActiveLabel = StaticFunctions::lang('Sosyal medya ile hızlı giriş aktif');

            $UserPrefences['BannedSocials']['Google'] = 'allowed';
            $UserPrefences['BannedSocials']['Github'] = 'allowed';
            $UserPrefences['BannedSocials']['Linkedin'] = 'allowed';
            $UserPrefences['BannedSocials']['Facebook'] = 'allowed';

            $SocialsArray['Google']['isActive'] = true;
            $SocialsArray['Github']['isActive'] = true;
            $SocialsArray['Linkedin']['isActive'] = true;
            $SocialsArray['Facebook']['isActive'] = true;
        } else {

            $IsAllActive = false;
            $IsActiveLabel = StaticFunctions::lang('Sosyal medya ile hızlı giriş pasif');

            $UserPrefences['BannedSocials']['Google'] = 'banned';
            $UserPrefences['BannedSocials']['Github'] = 'banned';
            $UserPrefences['BannedSocials']['Linkedin'] = 'banned';
            $UserPrefences['BannedSocials']['Facebook'] = 'banned';

            $SocialsArray['Google']['isActive'] = false;
            $SocialsArray['Github']['isActive'] = false;
            $SocialsArray['Linkedin']['isActive'] = false;
            $SocialsArray['Facebook']['isActive'] = false;
        }
    }


    $UserExtraJson = json_encode($UserPrefences);
    $UserPrefencesUpdate = $db->prepare("UPDATE users SET
     user_prefences   = :iki
     WHERE id = :dort and status=1 ");
    $update = $UserPrefencesUpdate->execute(array(
        'iki' => $UserExtraJson,
        'dort' => $User['id']
    ));

    $Stats = [
        'socialLoginActive' => $IsAllActive,
        'socialLoginLabel' => $IsActiveLabel,
        'bannedSocials' => $SocialsArray
    ];
}

echo StaticFunctions::ApiJson($Stats);