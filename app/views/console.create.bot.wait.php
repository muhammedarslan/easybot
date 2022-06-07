<?php

$PageCss = [];

$PageJs = [
    '/assets/console/app-assets/js/core/CreateBotWait.js'
];

$PageBreadCrumb = [
    'isActive' => true,
    'list' => [
        '/console/dashboard' => StaticFunctions::lang('Anasayfa'),
        '/console/custom/bots' => StaticFunctions::lang('Kişisel Botlarım'),
        'active'             => StaticFunctions::lang('Bot Oluşturma Sihirbazı')
    ]

];

$_AccountVerifyRequired = true;

require_once VDIR . '/console.header.php';
require_once VDIR . '/console.menu.php';


?>
<main data-barba="container" data-barba-easy="<?= StaticFunctions::seo_link(end($PageBreadCrumb['list'])) ?>wait">

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
            <div style="display:none;" class="content-body MainContent">

                <section id="block-level-buttons">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <?= Staticfunctions::lang('Bot Oluşturma Sihirbazına Hoşgeldin') ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <!-- Block level buttons -->
                                        <p>Bot oluşturmak çok kolay, hemen başla.
                                        </p>
                                        <div class="row">

                                            <div class="col-lg-6 col-md-12">
                                                <!-- Block level buttons with icon -->
                                                <div class="form-group">
                                                    <button id="StartNowButton" onclick="CreateBot();" type="button"
                                                        class="btn mb-1 btn-outline-primary btn-icon btn-lg btn-block waves-effect waves-light">Hemen
                                                        Başla
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
    <!-- END: Content-->


</main>
<?php

require_once VDIR . '/console.footer.php';

?>