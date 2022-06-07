<?php

$Me = StaticFunctions::get_id();
$PhoneVerify = $db->query("SELECT phone_verify FROM users WHERE id = '{$Me}' and status='1' ")->fetch(PDO::FETCH_ASSOC);
if (!$PhoneVerify) {
    StaticFunctions::LogOut();
    http_response_code(401);
    exit;
}


if (!isset($PageCss)) $PageCss = [];
if (!isset($PageJs))  $PageJs  = [];
if (!isset($_AccountVerifyRequired))  $_AccountVerifyRequired  = false;

StaticFunctions::BarbaLoaded($PageCss, $PageJs, $_AccountVerifyRequired);

?>
<!DOCTYPE html>
<html class="loading" lang="<?= LANG ?>" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="<?= StaticFunctions::lang('Easybot, bot oluşturmanın en kolay yolu.') ?>">
    <title><?= StaticFunctions::say($__PageTitle) ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="/assets/media/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/vendors/css/vendors.min.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/vendors/css/charts/apexcharts.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/vendors/css/extensions/tether-theme-arrows.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/vendors/css/extensions/tether.min.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css" href="/assets/console/app-assets/css/custom.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/vendors/css/extensions/shepherd-theme-default.css?v=<?= Version ?>">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/console/app-assets/css/bootstrap.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/css/bootstrap-extended.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css" href="/assets/console/app-assets/css/colors.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css" href="/assets/console/app-assets/css/components.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/css/themes/dark-layout.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/css/themes/semi-dark-layout.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/vendors/css/extensions/toastr.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/css/plugins/extensions/toastr.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/vendors/css/extensions/sweetalert2.min.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/css/plugins/forms/validation/form-validation.css?v=<?= Version ?>">

    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/vendors/css/tables/datatable/datatables.min.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/datatable/responsive.dataTables.min.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/css/pages/data-list-view.css?v=<?= Version ?>">

    <?php
    if ($PhoneVerify['phone_verify'] != 1) {
        echo '<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.3/build/css/intlTelInput.css?v=' . Version . '">';
    }
    ?>

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/css/core/menu/menu-types/vertical-menu.css?v=<?= Version ?>">
    <link rel="stylesheet" type="text/css"
        href="/assets/console/app-assets/css/core/colors/palette-gradient.css?v=<?= Version ?>">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/console/assets/css/style.css?v=<?= Version ?>">
    <!-- END: Custom CSS-->
    <script>
    const AppLang = '<?= LANG ?>';
    </script>
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<?php

$BodyDark = '';

if (AppMode == 'Dark') :
    $BodyDark = 'dark-layout';
endif;

if (MenuType == 'open') {
    $MenuOptionsArray = [
        'bodyClass' => '',
        'iconType' => 'icon-disc'
    ];
} else {
    $MenuOptionsArray = [
        'bodyClass' => 'menu-collapsed',
        'iconType' => 'icon-circle'
    ];
}

?>

<body
    class="vertical-layout vertical-menu-modern semi-dark-layout 2-columns  navbar-floating footer-static <?= $BodyDark ?> <?= $MenuOptionsArray['bodyClass'] ?>"
    data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" data-barba="wrapper"
    data-layout="semi-dark-layout">
    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow">
        <div class="navbar-wrapper">
            <div class="navbar-container content">
                <div class="navbar-collapse" id="navbar-mobile">
                    <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                        <ul class="nav navbar-nav">
                            <li class="nav-item mobile-menu d-xl-none mr-auto"><a
                                    class="nav-link nav-menu-main menu-toggle hidden-xs" href="javascript:;"><i
                                        class="ficon feather icon-menu"></i></a></li>
                        </ul>
                        <ul class="nav navbar-nav bookmark-icons">
                            <!-- li.nav-item.mobile-menu.d-xl-none.mr-auto-->
                            <!--   a.nav-link.nav-menu-main.menu-toggle.hidden-xs(href='#')-->
                            <!--     i.ficon.feather.icon-menu-->
                            <?php

                            foreach (EasyDefines::TopbarContent() as $key => $value) {
                                echo '<li class="nav-item d-none d-lg-block"><a class="nav-link" href="' . $value['MenuLink'] . '"
                                    data-toggle="tooltip" data-placement="top" title="' . $value['MenuName'] . '"><i
                                        class="ficon feather ' . $value['MenuIcon'] . '"></i></a></li>';
                            }

                            ?>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-todo.html"
                                    data-toggle="tooltip" data-placement="top" title="Todo"><i
                                        class="ficon feather icon-check-square"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-chat.html"
                                    data-toggle="tooltip" data-placement="top" title="Chat"><i
                                        class="ficon feather icon-message-square"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-email.html"
                                    data-toggle="tooltip" data-placement="top" title="Email"><i
                                        class="ficon feather icon-mail"></i></a></li>
                            <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-calender.html"
                                    data-toggle="tooltip" data-placement="top" title="Calendar"><i
                                        class="ficon feather icon-calendar"></i></a></li>
                        </ul>
                        <ul class="nav navbar-nav">
                            <li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i
                                        class="ficon feather icon-star warning"></i></a>
                                <div class="bookmark-input search-input">
                                    <div class="bookmark-input-icon"><i class="feather icon-search primary"></i>
                                    </div>
                                    <input class="form-control input" type="text"
                                        placeholder="<?= StaticFunctions::lang("Hızlı eylemleri keşfet...") ?>"
                                        tabindex="0" data-search="template-list">
                                    <ul class="search-list search-list-bookmark"></ul>
                                </div>
                                <!-- select.bookmark-select-->
                                <!--   option Chat-->
                                <!--   option email-->
                                <!--   option todo-->
                                <!--   option Calendar-->
                            </li>

                        </ul>
                    </div>
                    <ul class="nav navbar-nav float-right">
                        <li class="dropdown dropdown-language nav-item">
                            <?php

                            $LangsArray = AppLanguage::$AllowedLangs;
                            $SelectedLang = $LangsArray[LANG];
                            $flag = (LANG == 'en') ? 'us' : LANG;

                            echo '<a style="margin-top:2px;" class="dropdown-toggle nav-link"
                                id="dropdown-flag" href="javascript:;" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"><i class="flag-icon flag-icon-' . $flag . '"></i><span
                                    class="selected-language">' . $SelectedLang['LangName'] . '</span></a>';

                            unset($LangsArray[LANG]);
                            echo '<div class="dropdown-menu" aria-labelledby="dropdown-flag">';
                            foreach ($LangsArray as $key => $value) {
                                $key2 = ($key == 'en') ? 'us' : $key;
                                echo '<a class="dropdown-item no-barba" href="?hl=' . $key . '" ><i class="flag-icon flag-icon-' . $key2 . '"></i>' . $value['LangName'] . '</a>';
                            }

                            ?>
                </div>
                </li>
                <li class="nav-item d-none d-lg-block"><a data-toggle="tooltip" data-placement="top"
                        title="<?= StaticFunctions::lang('Tam ekran') ?>" class="nav-link nav-link-expand"><i
                            class="ficon feather icon-maximize"></i></a></li>

                <?php

                if (AppMode == 'Dark') :
                    $S2 = '';
                    $S1 = 'display:none';
                else :
                    $S1 = '';
                    $S2 = 'display:none';
                endif;

                ?>

                <li class="nav-item d-none d-lg-block"><a style="<?= $S1 ?>" data-toggle="tooltip" data-placement="top"
                        title="<?= StaticFunctions::lang('Aydınlık') ?>" id="Btn_LightMode" class="nav-link"><i
                            class="ficon feather icon-sun"></i></a></li>
                <li class="nav-item d-none d-lg-block"><a style="<?= $S2 ?>" data-toggle="tooltip" data-placement="top"
                        title="<?= StaticFunctions::lang('Karanlık') ?>" id="Btn_DarkMode" class="nav-link"><i
                            class="ficon feather icon-moon"></i></a></li>

                <li class="nav-item nav-search"><a data-toggle="tooltip" data-placement="top"
                        title="<?= StaticFunctions::lang('Keşfet') ?>" class="nav-link nav-link-search"><i
                            class="ficon feather icon-search"></i></a>
                    <div class="search-input">
                        <div class="search-input-icon"><i class="feather icon-search primary"></i></div>
                        <input class="input" type="text"
                            placeholder="<?= StaticFunctions::lang('Easybotu keşfet...') ?>" tabindex="-1"
                            data-search="template-list">
                        <div class="search-input-close"><i class="feather icon-x"></i></div>
                        <ul class="search-list search-list-main"></ul>
                    </div>
                </li>


                <li id="Notif_Area" class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label"
                        href="javascript:;" data-toggle="dropdown"><i class="ficon feather icon-bell"></i><span
                            class="badge badge-pill badge-primary badge-up" id="Notif_Count"></span></a>
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                        <li class="dropdown-menu-header">
                            <div class="dropdown-header m-0 p-2">
                                <h3 id="Notif_Count2" class="white"></h3><span
                                    class="notification-title"><?= StaticFunctions::lang('Uygulama Bildirimleriniz') ?></span>
                            </div>
                        </li>
                        <li id="Notif_List" class="scrollable-container media-list">
                        </li>
                        <li class="dropdown-menu-footer"><a id="ReadAllNotif" class="dropdown-item p-1 text-center"
                                href="javascript:;"><?= StaticFunctions::lang('Tümünü okundu olarak işaretle') ?></a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link"
                        href="javascript:;" data-toggle="dropdown">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name text-bold-600"
                                id="UserRealName"><?= StaticFunctions::clear($_SESSION['UserSession']->real_name) ?></span><span
                                class="user-status">king in the north</span></div>

                        <span><img class="round usr_avatar_"
                                src="<?= StaticFunctions::UserAvatar($_SESSION['UserSession']->avatar) ?>" alt="avatar"
                                height="40" width="40"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item"
                            href="/console/account/profile"><i class="feather icon-user"></i>
                            <?= StaticFunctions::lang('Hesap & Profil') ?></a><a class="dropdown-item"
                            href="/console/inbox"><i style="margin-right: 11px;"
                                class="feather icon-mail"></i><?= StaticFunctions::lang('Gelen kutusu') ?></a><a
                            class="dropdown-item" href="/console/security/login"><i class="feather icon-anchor"></i>
                            <?= StaticFunctions::lang('Hatalı girişler') ?></a>

                        <a class="dropdown-item" href="javascript:;" onclick="tidioChatApi.open();"><i
                                class="feather icon-message-square"></i>
                            <?= StaticFunctions::lang('Canlı destek') ?></a>

                        <a class="dropdown-item" href="/console/support/tickets"><i
                                class="feather icon-help-circle"></i>
                            <?= StaticFunctions::lang('Destek talepleri') ?></a>
                        <a class="dropdown-item" target="_blank" href="/community"><i class="feather icon-layers"></i>
                            <?= StaticFunctions::lang('EasyBot topluluğu') ?></a>
                        <div class="dropdown-divider"></div><a class="dropdown-item no-barba" href="/console/log-out"><i
                                class="feather icon-power"></i>
                            <?= StaticFunctions::lang('Güvenli çıkış') ?></a>
                    </div>
                </li>
                </ul>
            </div>
        </div>
        </div>
    </nav>
    <ul class="main-search-list-defaultlist d-none">
        <li class="d-flex align-items-center"><a class="pb-25" href="#">
                <h6 class="text-primary mb-0">Files</h6>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a
                class="d-flex align-items-center justify-content-between w-100" href="#">
                <div class="d-flex">
                    <div class="mr-50"><img src="/assets/console/app-assets/images/icons/xls.png" alt="png" height="32">
                    </div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing
                            Manager</small>
                    </div>
                </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a
                class="d-flex align-items-center justify-content-between w-100" href="#">
                <div class="d-flex">
                    <div class="mr-50"><img src="/assets/console/app-assets/images/icons/jpg.png" alt="png" height="32">
                    </div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd
                            Developer</small>
                    </div>
                </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a
                class="d-flex align-items-center justify-content-between w-100" href="#">
                <div class="d-flex">
                    <div class="mr-50"><img src="/assets/console/app-assets/images/icons/pdf.png" alt="png" height="32">
                    </div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital
                            Marketing Manager</small>
                    </div>
                </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a
                class="d-flex align-items-center justify-content-between w-100" href="#">
                <div class="d-flex">
                    <div class="mr-50"><img src="/assets/console/app-assets/images/icons/doc.png" alt="png" height="32">
                    </div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web
                            Designer</small>
                    </div>
                </div><small class="search-data-size mr-50 text-muted">&apos;256kb</small>
            </a></li>
        <li class="d-flex align-items-center"><a class="pb-25" href="#">
                <h6 class="text-primary mb-0">Members</h6>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a
                class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                <div class="d-flex align-items-center">
                    <div class="avatar mr-50"><img src="/assets/console/app-assets/images/portrait/small/avatar-s-8.jpg"
                            alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                    </div>
                </div>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a
                class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                <div class="d-flex align-items-center">
                    <div class="avatar mr-50"><img src="/assets/console/app-assets/images/portrait/small/avatar-s-1.jpg"
                            alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd
                            Developer</small>
                    </div>
                </div>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a
                class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                <div class="d-flex align-items-center">
                    <div class="avatar mr-50"><img
                            src="/assets/console/app-assets/images/portrait/small/avatar-s-14.jpg" alt="png"
                            height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing
                            Manager</small>
                    </div>
                </div>
            </a></li>
        <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a
                class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
                <div class="d-flex align-items-center">
                    <div class="avatar mr-50"><img src="/assets/console/app-assets/images/portrait/small/avatar-s-6.jpg"
                            alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
                    </div>
                </div>
            </a></li>
    </ul>
    <ul class="main-search-list-defaultlist-other-list d-none">
        <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer"><a
                class="d-flex align-items-center justify-content-between w-100 py-50">
                <div class="d-flex justify-content-start"><span
                        class="mr-75 feather icon-alert-circle"></span><span><?= StaticFunctions::lang('Sonuç bulunamadı.') ?></span>
                </div>
            </a></li>
    </ul>
    <!-- END: Header-->