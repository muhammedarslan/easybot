<?php

$PageCss = [];

$PageJs = [];

$PageBreadCrumb = [
    'isActive' => true,
    'list' => [
        '/console/dashboard' => StaticFunctions::lang('Anasayfa'),
        'active'             => StaticFunctions::lang('BoşSayfa')
    ]

];

require_once VDIR . '/console.header.php';
require_once VDIR . '/console.menu.php';


?>
<main data-barba="container" data-barba-easy="<?= StaticFunctions::seo_link(end($PageBreadCrumb['list'])) ?>">

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
            <div style="display:none;" class="content-body MainContent">

                <?= Staticfunctions::lang('Hesabımı neden onaylamalıyım Bayım???') ?>

            </div>
        </div>
    </div>
    <!-- END: Content-->


</main>
<?php

require_once VDIR . '/console.footer.php';

?>