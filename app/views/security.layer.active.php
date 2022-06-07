<!doctype html>
<html class="no-js" lang="<?= LANG ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= StaticFunctions::lang('Güvenlik Merkezi') ?></title>
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
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/vendors/css/extensions/toastr.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/css/plugins/extensions/toastr.css?v=<?= Version ?>">
    <link rel="stylesheet" href="/assets/l/style.css">
    <link rel="stylesheet" href="/assets/console/app-assets/css/custom.css">
    <style>
    .pincode-input-container {
        display: flex;
    }

    .pincode-input-text {
        text-align: center;
        font-weight: 600;
    }

    .fxt-template-layout21 .fxt-content {
        padding-bottom: 15px;
    }

    #toast-container {
        font-size: 14px;
    }
    </style>
    <script>
    const InternalAjaxHost = '<?= PROTOCOL . DOMAIN . PATH ?>';
    </script>
</head>

<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <section class="fxt-template-animation fxt-template-layout21">
        <!-- Animation Start Here -->
        <div id="particles-js"></div>
        <!-- Animation End Here -->
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-6 col-lg-7 col-sm-12 col-12 fxt-bg-color">
                    <div id="ContentDiv" class="fxt-content">
                        <div style="margin-bottom: -10px;" class="fxt-header">
                            <img src="/assets/media/logo-d.png" alt="Logo">
                            <div style="margin-top: 10px;" class="fxt-style-line">
                                <div class="fxt-transformY-50 fxt-transition-delay-5">
                                    <h3><?= Staticfunctions::lang($LayerTexts['Title']) ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="fxt-transformY-50 fxt-transition-delay-5 text-center">

                            <div id="SecurityArea1">
                                <p><?= Staticfunctions::lang($LayerTexts['Message']) ?></p>
                                <?php

                                if ($AcceptedMethos['Email'] == true) :
                                    echo '<div style="margin-bottom:10px;" class="form-group">
                                <div class="fxt-transformY-10 fxt-transition-delay-1">
                                    <button data-verify-src="Email"  onclick="VerifyWith(\'Email\');" type="button"
                                        class="fxt-btn-fill">' . Staticfunctions::lang('E-posta Adresi ile doğrula') . '</button>
                                     </div>
                                </div>';
                                endif;

                                if ($AcceptedMethos['Sms'] == true) :
                                    echo '<div style="margin-bottom:10px;" class="form-group">
                                <div class="fxt-transformY-10 fxt-transition-delay-1">
                                    <button data-verify-src="Sms"  onclick="VerifyWith(\'Sms\');" type="button"
                                        class="fxt-btn-fill">' . Staticfunctions::lang('Telefon Numarası ile doğrula') . '</button>
                                     </div>
                                </div>';
                                endif;

                                if ($AcceptedMethos['Authenticator'] == true) :
                                    echo '<div style="margin-bottom:10px;" class="form-group">
                                <div class="fxt-transformY-10 fxt-transition-delay-1">
                                    <button data-verify-src="Authenticator"  onclick="VerifyWith(\'Authenticator\');" type="button"
                                        class="fxt-btn-fill">' . Staticfunctions::lang('Google Authenticator ile doğrula') . '</button>
                                     </div>
                                </div>';
                                endif;

                                if ($AcceptedMethos['Notification'] == true) :
                                    echo '<div style="margin-bottom:10px;" class="form-group">
                                <div class="fxt-transformY-10 fxt-transition-delay-1">
                                    <button data-verify-src="Notification"  onclick="VerifyWith(\'Notification\');" type="button"
                                        class="fxt-btn-fill">' . Staticfunctions::lang('Mobil Bildirim ile doğrula') . '</button>
                                     </div>
                                </div>';
                                endif;

                                ?>
                            </div>
                            <div style="display: none;" id="SecurityArea2">

                                <p id="SendedInfo"></p>

                                <div style="text-align: center;margin-top:-10px;margin-bottom:10px;" class="fxt-form">
                                    <div id="PinCodeArea">
                                        <input autocomplete="new-password" type="text" id="pincode-input1">
                                    </div>


                                </div>

                                <p id="TimeLeftArea" style="margin-bottom: 0px;">
                                    <strong><?= Staticfunctions::lang('Kalan süre:') ?></strong>
                                    <span id="Min">{min}</span> <?= Staticfunctions::lang('dk') ?> <span
                                        id="Sec">{sec}</span>
                                    <?= Staticfunctions::lang('sn') ?>
                                </p>

                                <a href="javascript:;" id="SendAgain" style="display:none;" onclick="SendAgain();"
                                    class="switcher-text2"><?= Staticfunctions::lang('Tekrar gönder') ?></a>

                                <hr>
                                <div class="fxt-footer">
                                    <div class="animated fadeIn">
                                        <p style="margin-bottom: 0px;">
                                            <?= Staticfunctions::lang('Bir sorun mu var?') ?><a href="javascript:;"
                                                onclick="ChangeMethod();"
                                                class="switcher-text2"><?= Staticfunctions::lang('Farklı bir yöntem dene.') ?></a>
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <div style="display: none;" class="text-center" id="SecurityArea3">
                                <img style="width: 150px; margin-bottom: 20px;" src="/assets/media/success.gif" alt=""
                                    srcset="">
                                <p id="SccText" style="display: none;">
                                    <?= Staticfunctions::lang('Kimliğin başarıyla doğrulandı, yönlendiriliyorsun...') ?>
                                </p>
                            </div>

                            <div id="LogOutFooter" class="fxt-footer">
                                <div class="animated fadeIn">
                                    <p><?= Staticfunctions::lang('Doğrulama yapamıyor musun?') ?><a href="javascript:;"
                                            onclick="LogOut();"
                                            class="switcher-text2"><?= Staticfunctions::lang('Oturumu sonlandır.') ?></a>
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>



        </div>

    </section>
    <input type="text" name="hidden_timer" hidden value="" id="timerInput">
    <input type="text" name="selected_button" hidden value="" id="selectedMethod">

    <!-- jquery-->
    <script src="/assets/l/js/jquery-3.5.0.min.js"></script>
    <!-- Popper js -->
    <script src="/assets/l/js/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="/assets/l/js/bootstrap.min.js"></script>
    <!-- Imagesloaded js -->
    <script src="/assets/l/js/imagesloaded.pkgd.min.js"></script>
    <!-- Particles js -->
    <script src="/assets/l/js/particles.min.js"></script>
    <script src="/assets/l/js/particles-1.js"></script>
    <!-- Validator js -->
    <script src="/assets/l/js/validator.min.js"></script>
    <script src="/assets/console/app-assets/vendors/js/extensions/toastr.min.js?v=<?= Version ?>"></script>

    <!-- Custom Js -->
    <script src="/assets/l/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sf-bootstrap-pincode-input@1.5.0/js/bootstrap-pincode-input.min.js">
    </script>
    <script src="/assets/console/app-assets/js/core/SecurityLayer.js?v=<?= Version ?>"></script>

</body>

</html>