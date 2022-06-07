<?php

$T1 = StaticFunctions::lang('E-posta Adresinizi Giriniz');
$T2 = StaticFunctions::lang('Şifremi Sıfırla');
$T3 = StaticFunctions::lang('Kapat');
$T4 = StaticFunctions::lang('Lütfen boş bırakmayınız.');
$T5 = StaticFunctions::lang('EasyBot için hızlı giriş yap');
?>
<!doctype html>
<html class="no-js" lang="<?= LANG ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= StaticFunctions::lang('Aramıza Katıl ') . PR_NAME ?></title>
    <meta name="description" content="<?= StaticFunctions::lang('Easybot, bot oluşturmanın en kolay yolu.') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/assets/media/favicon.ico">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/assets/l/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="/assets/l/css/fontawesome-all.min.css">
    <!-- Flaticon CSS -->
    <link rel="stylesheet" href="/assets/l/font/flaticon.css">
    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/l/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.1.0/css/flag-icon.min.css" rel="stylesheet">
    <script>
    const InternalAjaxHost = '<?= PROTOCOL . DOMAIN . PATH ?>';
    </script>
    <script
        src='https://www.google.com/recaptcha/api.js?render=<?= ProjectDefines::RecaptchaV3()['SiteKey'] . '&hl=' . LANG ?>'>
    </script>
</head>

<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <style>
    ::-webkit-input-placeholder {
        letter-spacing: normal !important;
    }

    :-moz-placeholder {
        letter-spacing: normal !important;
        opacity: 1;
    }

    ::-moz-placeholder {
        letter-spacing: normal !important;
        opacity: 1;
    }

    :-ms-input-placeholder {
        /* Internet Explorer 10-11 */
        color: #909;
    }

    ::-ms-input-placeholder {
        letter-spacing: normal !important;
    }

    ::placeholder {
        letter-spacing: normal !important;
    }

    .fxt-template-layout5 .checkbox_t input[type="checkbox"]:checked+label::before {
        background-color: #007bff;
        border-color: #007bff;
    }
    </style>
    <div id="wrapper" class="wrapper">
        <div class="fxt-template-animation fxt-template-layout5">
            <div style="background-size: cover;
    -webkit-background-size: cover;
    
    -moz-background-size: cover;
    -o-background-size: cover;" class="fxt-bg-img fxt-none-767" data-bg-image="/assets/media/login-bg.jpg">
                <div style="margin-top: -40px;" class="fxt-intro">
                    <div class="sub-title"><?= StaticFunctions::lang('Seni aramızda görmek') ?></div>
                    <h1><?= StaticFunctions::lang('Muhteşem.') ?></h1>
                </div>
            </div>
            <div class="fxt-bg-color">
                <div class="fxt-header">
                    <a href="/" class="fxt-logo"><img src="/assets/media/logo-d.png" alt="Logo"></a>
                    <div class="fxt-page-switcher">
                        <a id="LoginButtonTop" style="min-width: 100px;" href="/login"
                            class="switcher-text switcher-text1"><?= StaticFunctions::lang('Giriş') ?></a>
                        <a id="RegisterButtonTop" href="/register"
                            class="switcher-text switcher-text2 active"><?= StaticFunctions::lang('Kayıt') ?></a>
                    </div>
                </div>
                <div class="fxt-form">
                    <div style="text-align:center;" id="AjaxContent"></div>

                    <form style="margin-top: 10px;display:none;" method="POST" action="javascript:;" id="LoginForm">
                        <div class="form-group fxt-transformY-50 fxt-transition-delay-1">
                            <input id="form_email" type="email" class="form-control" name="email"
                                placeholder="<?= StaticFunctions::lang('E-posta adresi') ?>" required="required">
                            <i class="flaticon-envelope"></i>
                        </div>
                        <div class="form-group fxt-transformY-50 fxt-transition-delay-2">
                            <input id="form_password" type="password" class="form-control" name="password"
                                placeholder="<?= StaticFunctions::lang('Hesap şifresi') ?>" required="required">
                            <i class="flaticon-padlock"></i>
                            <a href="javascript:;" id="LostPassword"
                                class="switcher-text3"><?= StaticFunctions::lang('şifremi unuttum?') ?></a>
                        </div>
                        <div class="form-group fxt-transformY-50 fxt-transition-delay-3">
                            <div class="fxt-content-between">
                                <button id="form_button" type="submit"
                                    class="fxt-btn-fill"><?= StaticFunctions::lang('Giriş yap') ?></button>
                                <div class="checkbox">
                                    <input name="remember_me" id="checkbox1" type="checkbox">
                                    <label style="cursor: pointer;"
                                        for="checkbox1"><?= StaticFunctions::lang('Beni Hatırla') ?></label>
                                </div>
                            </div>
                        </div>
                        <input type="text" hidden name="recaptcha_token" value="" id="recaptcha_inp">
                    </form>

                    <form style="margin-top: 10px;display:none;" method="POST" action="javascript:;" id="LostPwForm">
                        <div class="form-group fxt-transformY-50 fxt-transition-delay-1">
                            <input id="lost_form_email" type="email" class="form-control" name="email"
                                placeholder="<?= StaticFunctions::lang('E-posta adresinizi giriniz') ?>"
                                required="required">
                            <i class="flaticon-envelope"></i>
                        </div>

                        <div class="form-group fxt-transformY-50 fxt-transition-delay-3">
                            <div class="fxt-content-between">
                                <button id="form_button_lost" type="submit"
                                    class="fxt-btn-fill"><?= StaticFunctions::lang('Şifremi unuttum') ?></button>
                                <a href="javascript:;" id="Back2Login"
                                    class="switcher-text3"><?= StaticFunctions::lang('girişe dön') ?></a>

                            </div>
                        </div>
                        <input type="text" hidden name="recaptcha_token" value="" id="recaptcha_inp3">
                    </form>

                    <form autocomplete="off" style="margin-top: 10px;display:none;" method="POST" action="javascript:;"
                        id="EmailVerifyForm">
                        <div class="form-group fxt-transformY-50 fxt-transition-delay-1">
                            <input autocomplete="off" style="text-align: center;
    font-size: 40px;
    font-weight: 600;
    padding-bottom: 20px;
    letter-spacing: 30px;
    color: #495057;" onkeyup="this.value=this.value.replace(/[^\d]/,'')" maxlength="6" minlength="6"
                                id="verify_form_pin" type="text" class="form-control" name="pin"
                                placeholder="<?= StaticFunctions::lang('E-posta adresinize gönderilen kodu giriniz.') ?>"
                                required="required">
                            <i class="flaticon-envelope"></i>
                        </div>

                        <div class="form-group fxt-transformY-50 fxt-transition-delay-3">
                            <div class="fxt-content-between">
                                <button id="form_button_verify" type="submit"
                                    class="fxt-btn-fill"><?= StaticFunctions::lang('Doğrula') ?></button>
                                <a href="javascript:;" id="NwCode"
                                    class="switcher-text3"><?= StaticFunctions::lang('pin kodunu almadınız mı?') ?></a>
                            </div>
                        </div>
                        <input type="text" hidden name="layer" value="2">
                        <input type="text" hidden name="recaptcha_token" value="" id="recaptcha_inp4">
                    </form>

                    <form autocomplete="off" style="" method="POST" action="javascript:;" id="RegisterForm">
                        <div class="form-group fxt-transformY-50 fxt-transition-delay-1">
                            <input autocomplete="off" id="RandomInput1" type="text" class="form-control"
                                name="name_surname" placeholder="<?= StaticFunctions::lang('Ad & Soyad') ?>"
                                required="required">
                            <i class="flaticon-envelope"></i>
                        </div>
                        <div class="form-group fxt-transformY-50 fxt-transition-delay-1">
                            <input autocomplete="off" id="RandomInput2" type="email" class="form-control" name="email"
                                placeholder="<?= StaticFunctions::lang('E-posta adresi') ?>" required="required">
                            <i class="flaticon-envelope"></i>
                        </div>
                        <div class="form-group fxt-transformY-50 fxt-transition-delay-2">
                            <input id="RandomInput3" type="password" class="form-control" name="password"
                                placeholder="<?= StaticFunctions::lang('Hesap şifresi') ?>" required="required">
                            <i class="flaticon-padlock"></i>
                        </div>
                        <div class="form-group fxt-transformY-50 fxt-transition-delay-3">
                            <div class="fxt-content-between">
                                <button id="register_button" type="submit"
                                    class="fxt-btn-fill"><?= StaticFunctions::lang('Kayıt ol') ?></button>
                                <div class="checkbox checkbox_t">
                                    <input checked name="frm_terms" id="checkbox_terms" type="checkbox">
                                    <label style="cursor: pointer;" for="checkbox_terms"><?= StaticFunctions::lang('<a target="_blank" href="{0}">Kullanım şartlarını</a> kabul ediyorum.', [
                                                                                                PATH . LANG . '/terms-of-use'
                                                                                            ]) ?></label>
                                </div>
                            </div>
                        </div>
                        <input type="text" hidden name="recaptcha_token" value="" id="recaptcha_inp2">
                    </form>

                    <div class="fxt-footer">
                        <div class="fxt-style-line">
                            <div class="fxt-transformY-50 fxt-transition-delay-5">
                                <hr>
                                <h3 id="H11" style="color:#a4a4a4;font-size:20px;display:none;">
                                    <?= StaticFunctions::lang('Veya hızlı giriş yap') ?></h3>
                                <h3 id="H12" style="color:#a4a4a4;font-size:20px;">
                                    <?= StaticFunctions::lang('Veya hızlı kayıt ol') ?></h3>
                            </div>
                        </div>
                        <ul class="fxt-socials">
                            <li class="fxt-google fxt-transformY-50 fxt-transition-delay-7"><a href="javascript:;"
                                    onclick="LoginWith('google');" title="Google"><i class="fab fa-google"></i></a>
                            </li>
                            <li class="fxt-pinterest fxt-transformY-50 fxt-transition-delay-9"><a href="javascript:;"
                                    onclick="LoginWith('github');" title="Github"><i class="fab fa-github"></i></a>
                            </li>
                            <li class="fxt-linkedin fxt-transformY-50 fxt-transition-delay-8"><a href="javascript:;"
                                    onclick="LoginWith('linkedin');" title="Linkedin"><i
                                        class="fab fa-linkedin-in"></i></a></li>
                            <li class="fxt-facebook fxt-transformY-50 fxt-transition-delay-8"><a href="javascript:;"
                                    onclick="LoginWith('facebook');" title="Facebook"><i
                                        class="fab fa-facebook-f"></i></a></li>
                        </ul>
                    </div>

                    <div class="deep_footer fxt-transformY-50 fxt-transition-delay-5">
                        <hr>
                        <li class="nav-item dropdown">
                            <?php

                            $LangsArray = AppLanguage::$AllowedLangs;
                            $Selected = $LangsArray[LANG];
                            $flag = (LANG == 'en') ? 'us' : LANG;

                            echo '<a style="color:#a4a4a4;" class="nav-link dropdown-toggle" href="https://easybot.dev" id="lang_down"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span style="margin-right:6px"
                                    class="flag-icon flag-icon-' . $flag . '"> </span>' . ($Selected['LangName']) . '</a>';

                            unset($LangsArray[LANG]);
                            unset($LangsArray['it']);
                            echo '<div class="dropdown-menu" aria-labelledby="lang_down">';
                            foreach ($LangsArray as $key => $value) {
                                $key2 = ($key == 'en') ? 'us' : $key;
                                echo '<a class="dropdown-item" href="' . '?hl=' . $key . '"><span class="flag-icon flag-icon-' . $key2 . '"> </span>
                                ' . $value['LangName'] . '</a>';
                            }

                            echo '</div>';
                            ?>
                        </li>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- jquery-->
    <script src="/assets/l/js/jquery-3.5.0.min.js"></script>
    <!-- Popper js -->
    <script src="/assets/l/js/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="/assets/l/js/bootstrap.min.js"></script>
    <!-- Imagesloaded js -->
    <script src="/assets/l/js/imagesloaded.pkgd.min.js"></script>
    <!-- Validator js -->
    <script src="/assets/l/js/validator.min.js"></script>
    <!-- Custom Js -->

    <script src="/assets/l/js/main.js"></script>
    <script>
    const SomeText = [
        '<?= $T1 ?>',
        '<?= $T2 ?>',
        '<?= $T3 ?>',
        '<?= $T4 ?>',
        '<?= $T5 ?>',
    ];
    </script>
    <script src="/assets/l/js/login.js"></script>
    <script>
    const RecaptchaG = () => {
        grecaptcha.ready(function() {
            grecaptcha.execute('<?= ProjectDefines::RecaptchaV3()['SiteKey'] ?>', {
                    action: 'login'
                })
                .then(function(token) {
                    let RecaptchaInp = document.getElementById('recaptcha_inp');
                    let RecaptchaInp2 = document.getElementById('recaptcha_inp2');
                    let RecaptchaInp3 = document.getElementById('recaptcha_inp3');
                    let RecaptchaInp4 = document.getElementById('recaptcha_inp4');
                    RecaptchaInp.value = token;
                    RecaptchaInp2.value = token;
                    RecaptchaInp3.value = token;
                    RecaptchaInp4.value = token;
                });
        });
    };
    RecaptchaG();
    </script>

</body>

</html>