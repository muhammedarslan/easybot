<?php

StaticFunctions::ajax_form('general');
StaticFunctions::new_session();

if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') {
    echo StaticFunctions::JsonOutput([
        'isLogged' => true,
        'label' => 'success',
        'message' => StaticFunctions::lang('Başarıyla giriş yaptın, yönlendiriliyorsun...')
    ]);
} else {

    if (isset($_SESSION['SocialLoginError']) && $_SESSION['SocialLoginError'] != '') {
        $Message = $_SESSION['SocialLoginError'];
        $Label = 'warning';
        $_SESSION['SocialLoginError'] = '';
        unset($_SESSION['SocialLoginError']);
    } else {
        $Message = StaticFunctions::lang('Hızlı giriş işlemin başarısız oldu.');
        $Label = 'info';
    }

    echo StaticFunctions::JsonOutput([
        'isLogged' => false,
        'label' => $Label,
        'message' => $Message
    ]);
}