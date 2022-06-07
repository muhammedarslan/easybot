<?php

$PageCss = [
    '/assets/console/app-assets/css/pages/app-email.css'
];

$PageJs = [
    '/assets/console/app-assets/vendors/js/editors/quill/katex.min.js',
    '/assets/console/app-assets/vendors/js/editors/quill/highlight.min.js'
];

$PageBreadCrumb = [
    'isActive' => true,
    'list' => [
        '/console/dashboard' => StaticFunctions::lang('Anasayfa'),
        'active'             => StaticFunctions::lang('Gelen Kutusu')
    ]

];

require_once VDIR . '/console.header.php';
require_once VDIR . '/console.menu.php';


?>
<main class="email-application" data-barba="container" data-validate-callback="GetInboxJ" data-barba-easy="inbox">

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
        </div>
    </div>


    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div style="margin-top: 0px;" class="content-area-wrapper">

            <div style="width: 100%;" class="content-right">
                <div id="Mail_Height" class="content-wrapper">
                    <div class="content-header row">
                    </div>
                    <div style="display:none;" class="content-body MainContent">
                        <div class="app-content-overlay"></div>
                        <div class="email-app-area">
                            <!-- Email list Area -->
                            <div class="email-app-list-wrapper">
                                <div class="email-app-list">
                                    <div class="app-fixed-search">
                                        <fieldset class="form-group position-relative has-icon-left m-0">
                                            <input type="text" class="form-control" id="email-search"
                                                placeholder="<?= StaticFunctions::lang('Mailler içerisinde arayın...') ?>">
                                            <div class="form-control-position">
                                                <i class="feather icon-search"></i>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="app-action">
                                    </div>
                                    <div class="email-user-list list-group">

                                        <ul id="ListMails" class="users-list-wrapper media-list">

                                            <center>
                                                <img style="margin-top: 40px;" src="/assets/media/lg.gif" />
                                            </center>

                                        </ul>
                                        <div class="no-results">
                                            <h5><?= StaticFunctions::lang('Mail bulunamadı.') ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/ Email list Area -->
                            <!-- Detailed Email View -->
                            <div style="width: 100%;display:none;" class="email-app-details">
                                <div class="email-detail-header">
                                    <div class="email-header-left d-flex align-items-center mb-1">
                                        <span class="go-back mr-1"><i
                                                class="feather icon-arrow-left font-medium-4"></i></span>
                                        <h3 id="Mail_Title">...</h3>
                                    </div>
                                    <div class="email-header-right mb-1 ml-2 pl-1">

                                    </div>
                                </div>
                                <div style="height: 100%;" class="email-scroll-area">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="email-label ml-2 my-2 pl-1">
                                                <span class="mr-1 bullet bullet-success bullet-sm"></span><small
                                                    class="mail-label"><strong><?= StaticFunctions::lang('Başarıyla iletildi.') ?></strong></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card px-1">
                                                <div class="card-header email-detail-head ml-75">
                                                    <div
                                                        class="user-details d-flex justify-content-between align-items-center flex-wrap">
                                                        <div class="avatar mr-75">
                                                            <img src="<?= StaticFunctions::UserAvatar($_SESSION['UserSession']->avatar) ?>"
                                                                alt="avtar img holder" width="61" height="61">
                                                        </div>
                                                        <div class="mail-items">
                                                            <h4 class="list-group-item-heading mb-0">
                                                                <?= $_SESSION['UserSession']->real_name ?>
                                                            </h4>
                                                            <div class="email-info-dropup dropdown">
                                                                <span style="cursor:pointer;" class="font-small-3">
                                                                    <?= $_SESSION['UserSession']->email ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mail-meta-item">
                                                        <div class="mail-time mb-1" id="Mail_Time1"></div>
                                                        <div class="mail-date" id="Mail_Time2"></div>
                                                    </div>
                                                </div>
                                                <div style="height:100%;"
                                                    class="card-body mail-message-wrapper pt-2 mb-0">
                                                    <div style="height:100%;" id="Mail_Iframe" class="mail-message">

                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/ Detailed Email View -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

</main>
<?php

require_once VDIR . '/console.footer.php';

?>