<!doctype html>
<html class="no-js" lang="<?= LANG ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= StaticFunctions::lang('Bakımdayız...') ?></title>
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
</head>

<?php

$PageText1 = StaticFunctions::lang('Sizlere daha iyi bir hizmet verebilmek için sunucularımızda bakım yapıyoruz.');
$PageText2 = StaticFunctions::lang('En kısa sürede geri geleceğiz. Anlayışınız için teşekkür ederiz.');

?>

<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <section class="fxt-template-animation fxt-template-layout14" data-bg-image="/assets/media/bg-standart.jpg">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-6 col-lg-7 col-sm-12 col-12 fxt-bg-color">
                    <div class="fxt-content">
                        <div class="fxt-header">
                            <a style="margin-bottom: 10px;" class="fxt-logo"><img src="/assets/media/maint.gif"
                                    alt="Logo"></a>
                            <hr>
                            <p style="margin-top: 20px;"><?= $PageText1 ?>
                            </p>
                            <p style="margin-top: 20px;font-weight:600;"><?= $PageText2 ?></p>
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
    <!-- Validator js -->
    <script src="/assets/l/js/validator.min.js"></script>
    <!-- Custom Js -->
    <script src="/assets/l/js/main.js"></script>

</body>

</html>
<?php
exit;