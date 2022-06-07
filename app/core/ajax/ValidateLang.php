<?php

StaticFunctions::ajax_form('private');

echo StaticFunctions::ApiJson([
    'Title' => StaticFunctions::lang('Kimliğinizi doğrulayın'),
    'Button1' => StaticFunctions::lang('Şifremi doğrula'),
    'Button2' => StaticFunctions::lang('Geri dön'),
    'Text' => StaticFunctions::lang('<hr>Bu sayfa hesap güvenliğiniz için önemli olabilecek bilgiler içerebilir. Lütfen sayfaya erişmeden önce şifrenizi yazarak kimliğinizi doğrulayın.<hr>')
]);