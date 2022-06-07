<?php

StaticFunctions::ajax_form('general');
StaticFunctions::new_session();

$recaptcha = new \ReCaptcha\ReCaptcha(ProjectDefines::RecaptchaV2()['SecretKey']);
$resp = $recaptcha->verify(StaticFunctions::post('recaptcha_token'), StaticFunctions::get_ip());
if ($resp->isSuccess()) {
    $_SESSION['SessionValidateResetPass'] = 'validated';
}
