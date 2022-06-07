    <?php
    $T1 = StaticFunctions::lang('Yükleniyor...');
    ?>
    <!doctype html>
    <html class="no-js" lang="<?= LANG ?>">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?= StaticFunctions::lang('Lütfen Doğrulayın..') ?></title>
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
        <script type="text/javascript">
        function RCallbackF() {
            $('.g-recaptcha').fadeOut();
            $('#Ptext').text('<?= $T1 ?>');
            setTimeout(() => {
                $.post('/web-service/email/captcha', {
                    'recaptcha_token': grecaptcha.getResponse()
                }, (() => {
                    window.location = '';
                }));
            }, 700);
        };
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
                        <div class="fxt-content">
                            <div class="fxt-header">
                                <a href="javascript:;" class="fxt-logo"><img src="/assets/media/logo-d.png"
                                        alt="Logo"></a>
                                <p id="Ptext" style="margin-top: 20px;">
                                    <?= StaticFunctions::lang('Lütfen Doğrulayın.') ?>
                                </p>
                            </div>
                            <div style="text-align: center;margin-top:-10px;" class="fxt-form">
                                <center>
                                    <div class="g-recaptcha" data-callback="RCallbackF"
                                        data-sitekey=" <?= ProjectDefines::RecaptchaV2()['SiteKey'] ?>"></div>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
        <!-- Custom Js -->
        <script src="/assets/l/js/main.js"></script>
        <script src="https://www.google.com/recaptcha/api.js?hl=tr"></script>


    </body>

    </html>