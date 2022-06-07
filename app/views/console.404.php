<?php

$PageCss = [
    '/assets/console/app-assets/css/pages/error.css'
];

$PageBreadCrumb = [
    'isActive' => true,
    'list' => [
        '/console/dashboard' => StaticFunctions::lang('Anasayfa'),
        'active'             => StaticFunctions::lang('404 - Sayfa Bulunamadı')
    ]

];

require_once VDIR . '/console.header.php';
require_once VDIR . '/console.menu.php';

?>
<main data-barba="container" data-barba-easy="notfound">


    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
            <div style="display:none;" class="content-body MainContent">
                <!-- error 404 -->
                <section class="row flexbox-container">
                    <div style="margin: 0 auto;" class="col-xl-7 col-md-8 col-12 d-flex justify-content-center">
                        <div class="card auth-card bg-transparent shadow-none rounded-0 mb-0 w-100">
                            <div class="card-content">
                                <div class="card-body text-center">
                                    <img src="/assets/console/app-assets/images/pages/404.png" class="img-fluid align-self-center" alt="branding logo">
                                    <h1 class="font-large-2 my-1">
                                        <?= StaticFunctions::lang('404 - Sayfa Bulunamadı!') ?></h1>
                                    <p class="p-2">
                                        <?= StaticFunctions::lang('Aradığın sayfa burada bulunamadı. Bağlanmaya çalıştığın adresi kontrol edip yeniden deneyebilir veya ana sayfaya dönebilirsin.') ?>
                                    </p>
                                    <a class="btn btn-primary btn-lg mt-2" href="/console/dashboard"><?= StaticFunctions::lang('Ana Sayfaya Dön') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- error 404 end -->

            </div>
        </div>
    </div>


</main>
<?php

require_once VDIR . '/console.footer.php';

?>