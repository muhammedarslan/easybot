     <!doctype html>
     <html class="no-js" lang="<?= LANG ?>">

     <head>
         <meta charset="utf-8">
         <meta http-equiv="x-ua-compatible" content="ie=edge">
         <title><?= StaticFunctions::lang('Şifremi Sıfırla') ?></title>
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
                             <div class="fxt-header">
                                 <a href="javascript:;" class="fxt-logo"><img src="/assets/media/logo-d.png"
                                         alt="Logo"></a>
                                 <p id="Ptext" style="margin-top: 20px;">
                                     <?= StaticFunctions::lang('Yeni Şifrenizi Belirleyin.') ?>
                                 </p>
                             </div>
                             <div style="text-align: center;margin-top:-10px;" class="fxt-form">

                                 <div class="fxt-form">
                                     <form autocomplete="off" method="POST" action="javascript:;" id="LoginForm">
                                         <div class="form-group fxt-transformY-50 fxt-transition-delay-2">
                                             <input autocomplete="off" id="form_password" type="password"
                                                 class="form-control" name="password1"
                                                 placeholder="<?= StaticFunctions::lang('Yeni Şifreniz') ?>"
                                                 required="required">
                                         </div>
                                         <div class="form-group fxt-transformY-50 fxt-transition-delay-2">
                                             <input autocomplete="off" id="form_password2" type="password"
                                                 class="form-control" name="password2"
                                                 placeholder="<?= StaticFunctions::lang('Şifrenizi Doğrulayın') ?>"
                                                 required="required">
                                         </div>
                                         <input type="text" name="token" value="<?= $token ?>" hidden>
                                         <div class="form-group fxt-transformY-50 fxt-transition-delay-3">
                                             <div class="fxt-content-between">
                                                 <button id="form_button" type="submit"
                                                     class="fxt-btn-fill"><?= StaticFunctions::lang('Şifremi Sıfırla') ?></button>
                                             </div>
                                         </div>
                                     </form>
                                 </div>

                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="row align-items-center justify-content-center">
                     <div id="PageAlert" class="alert col-xl-6 col-lg-7 col-sm-12 col-12 text-center alert-info"
                         role="alert">
                         <?= StaticFunctions::lang('Güvenliğiniz için güçlü bir şifre belirlemenizi öneririz.') ?>
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
         <?php

            $PageText1 = StaticFunctions::lang('Güvenliğiniz için güçlü bir şifre belirmenizi öneririz.');
            $PageText2 = StaticFunctions::lang('Lütfen bekleyiniz...');

            ?>
         <script>
         let CTimer;
         $('#LoginForm').on('submit', (() => {

             $('#PageAlert').attr('class',
                 'alert col-xl-6 col-lg-7 col-sm-12 col-12 text-center alert-warning');
             $('#PageAlert').text('<?= $PageText2 ?>');
             $('#ContentDiv').attr('style', 'pointer-events:none;opacity:0.3');
             clearTimeout(CTimer);
             setTimeout(() => {
                 $.post('/web-service/new/password',
                     $('#LoginForm').serialize(), (data) => {
                         try {
                             const JsonData = JSON.parse(data);

                             if (JsonData.status == 'success') {
                                 $('#PageAlert').attr('class',
                                     'alert col-xl-6 col-lg-7 col-sm-12 col-12 text-center alert-success'
                                 );
                                 $('#PageAlert').html(JsonData.message);
                                 $('#ContentDiv').removeAttr('style');

                                 setTimeout(() => {
                                     window.location = '/login';
                                 }, 3000);

                             } else {
                                 $('#PageAlert').attr('class',
                                     'alert col-xl-6 col-lg-7 col-sm-12 col-12 text-center alert-danger'
                                 );
                                 $('#PageAlert').html(JsonData.message);
                                 $('#ContentDiv').removeAttr('style');

                                 CTimer = setTimeout(() => {
                                     $('#PageAlert').attr('class',
                                         'alert col-xl-6 col-lg-7 col-sm-12 col-12 text-center alert-info'
                                     );
                                     $('#PageAlert').html('<?= $PageText1 ?>');
                                 }, 10000);

                             }

                         } catch (error) {
                             window.location = '';
                         }

                     }).fail(() => {
                     window.location = '';
                 });
             }, 2000);

         }));
         </script>


     </body>

     </html>