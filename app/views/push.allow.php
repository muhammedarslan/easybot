<?php


$GetUser = $db->query("SELECT push_id,real_name,token FROM users WHERE id = '{$UserID}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
if (!$GetUser) {
    header("Location:" . PROTOCOL . DOMAIN . PATH);
    exit;
}

if (StaticFunctions::clear($token) != $GetUser['token']) {
    header("Location:" . PROTOCOL . DOMAIN . PATH);
    exit;
}

$R3Key = ProjectDefines::RecaptchaV3()['SiteKey'];

?>

<!doctype html>
<html class="no-js" lang="<?= LANG ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= StaticFunctions::lang('Gelişmelerden anında haberdar ol!') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="/assets/media/favicon.ico"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/assets/l/css/bootstrap.min.css">
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="/assets/l/css/fontawesome-all.min.css">
    <!-- Flaticon CSS -->
    <link rel="stylesheet" href="/assets/l/font/flaticon.css">
    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/l/style2.css">
    <style>
    .btn_s {
        width: 100%;
        background-color: #3bb67f !important;
    }

    .grecaptcha-badge {
        z-index: 1;
    }
    </style>
    <script
        src='https://www.google.com/recaptcha/api.js?render=<?= ProjectDefines::RecaptchaV3()['SiteKey'] . '&hl=' . LANG ?>'>
    </script>
</head>

<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <section class="fxt-template-animation fxt-template-layout27" data-bg-image="/assets/media/push-bg.jpg">
        <!-- Animation Start Here -->
        <div id="particles-js"></div>
        <!-- Animation End Here -->
        <div class="fxt-content">

            <div class="fxt-form f_static">
                <div class="fxt-transformY-50 fxt-transition-delay-1 text-center">
                    <img style="width: 140px;  margin-bottom: 20px;" src="/assets/media/push.png" />
                    <p id="T1">
                        <?= StaticFunctions::lang('Gelişmelerden anında haberdar olmak için bildirimlere izin ver.') ?>
                    </p>
                    <p style="display:none;" id="T2">
                        <img style="margin-top: 20px;" width="100px" src="/assets/media/loading.gif" alt="">
                    </p>
                </div>
                <div class="form-group">
                    <div class="fxt-transformY-50 fxt-transition-delay-4">
                        <div class="fxt-checkbox-area">
                            <button id="SetID" type="submit"
                                class="fxt-btn-fill btn_s"><?= StaticFunctions::lang('BİLDİRİMLERE İZİN VER') ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="text" hidden id="ScKey" value="" />

            <div style="display:none" class="fxt-form f_success">
                <div class="fxt-transformY-50 fxt-transition-delay-1 text-center">
                    <img style="width: 200px;  margin-bottom: 20px;" src="/assets/media/success.gif" />
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
    <script src="/assets/l/js/particles.js"></script>
    <script src="/assets/l/js/particles-2.js"></script>
    <!-- Validator js -->
    <script src="/assets/l/js/validator.min.js"></script>

    <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.7.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.7.0/firebase-messaging.js"></script>

    <!-- Custom Js -->
    <script src="/assets/l/js/main.js"></script>
    <script src="/assets/console/app-assets/js/core/push.js"></script>

    <script>
    const RecaptchaG = () => {
        grecaptcha.ready(function() {
            grecaptcha.execute('<?= $R3Key ?>', {
                    action: 'push'
                })
                .then(function(token) {
                    let RecaptchaInp = document.getElementById('ScKey');
                    RecaptchaInp.value = token;
                });
        });
    };
    RecaptchaG();
    </script>

</body>

</html>