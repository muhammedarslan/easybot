<?php


$PageCss = [
    '/assets/console/app-assets/css/pages/app-user.css'
];

$PageJs = [
    '/assets/console/app-assets/js/scripts/pages/app-user.js'
];

$PageBreadCrumb = [
    'isActive' => true,
    'list' => [
        '/console/dashboard' => StaticFunctions::lang('Anasayfa'),
        'active'             => StaticFunctions::lang('Hesap & Profil')
    ]

];

require_once VDIR . '/console.header.php';
require_once VDIR . '/console.menu.php';


?>
<main data-barba="container" data-barba-easy="accountandprofile">

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
            <div style="display:none;" class="content-body MainContent">

                <section class="page-users-view">
                    <div class="row">
                        <!-- account start -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title"><?= StaticFunctions::lang('Hesabım') ?></div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="users-view-image">
                                            <img id="AvatarSrc" src="/assets/media/img_loading.gif"
                                                class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
                                        </div>
                                        <div class="col-12 col-sm-9 col-md-6 col-lg-5">
                                            <table>
                                                <tr>
                                                    <td class="font-weight-bold"><?= StaticFunctions::lang('Ad') ?>
                                                    </td>
                                                    <td class="user-profile-data" data-key="name"></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold"><?= StaticFunctions::lang('E-posta') ?>
                                                    </td>
                                                    <td class="user-profile-data" data-key="email"></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Hesap seviyesi') ?></td>
                                                    <td class="user-profile-data" data-key="level"></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-12 col-md-12 col-lg-5">
                                            <table class="ml-0 ml-sm-0 ml-lg-0">
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Soyad') ?></td>
                                                    <td class="user-profile-data" data-key="surname"></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold"><?= StaticFunctions::lang('Telefon') ?>
                                                    </td>
                                                    <td class="user-profile-data" data-key="phone_mumber"></td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Zaman değerli') ?></td>
                                                    <td class="user-profile-data" data-key="pastedtime"></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-12">
                                            <a href="/console/account/profile/edit" class="btn btn-primary mr-1"><i
                                                    class="feather icon-edit-1"></i>
                                                <?= StaticFunctions::lang('Düzenle') ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- account end -->
                        <!-- information start -->
                        <div class="container col-12">
                            <div class="row">
                                <div class="col-md-6 col-12 ">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-title mb-2">
                                                <?= StaticFunctions::lang('2 Adımlı Doğrulama') ?>
                                                <img style="display: none;" id="2step_loading" width="45px"
                                                    src="/assets/media/mini_loading.gif" alt="">
                                            </div>
                                        </div>
                                        <div id="2step_area" class="card-body">
                                            <table>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Aktif/Pasif') ?> </td>
                                                    <td>
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input onchange="t2StepRefresh();" disabled type="checkbox"
                                                                class="custom-control-input 2step_checkbox"
                                                                id="p_switch1">
                                                            <label class="custom-control-label" for="p_switch1">
                                                            </label>
                                                            <span id="t2step_text" class="switch-label"></span>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>

                                                    <td style="    font-size: 16px;
                                             color: #626262;" class="font-weight-bold">
                                                        <hr>
                                                        <?= StaticFunctions::lang('Doğrulama Kanalları') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('E-posta') ?></td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input onchange="t2StepRefresh();" disabled type="checkbox"
                                                                class="custom-control-input 2step_checkbox 2step_channels"
                                                                id="p_switch2">
                                                            <label class="custom-control-label" for="p_switch2"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Kısa Mesaj') ?>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input onchange="t2StepRefresh();" disabled type="checkbox"
                                                                class="custom-control-input 2step_checkbox 2step_channels"
                                                                id="p_switch3">
                                                            <label class="custom-control-label" for="p_switch3"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Google Authenticator') ?></td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input onchange="t2StepRefresh();" disabled type="checkbox"
                                                                class="custom-control-input 2step_checkbox 2step_channels"
                                                                id="p_switch4">
                                                            <label class="custom-control-label" for="p_switch4"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Mobil Bildirim') ?>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input onchange="t2StepRefresh();" disabled type="checkbox"
                                                                class="custom-control-input 2step_checkbox 2step_channels"
                                                                id="p_switch5">
                                                            <label class="custom-control-label" for="p_switch5"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- information start -->
                                <!-- social links end -->
                                <div class="col-md-6 col-12 ">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-title mb-2">
                                                <?= StaticFunctions::lang('Sosyal Medya İle Hızlı Giriş') ?>
                                                <img style="display: none;" id="social_banned_loading" width="45px"
                                                    src="/assets/media/mini_loading.gif" alt="">
                                            </div>
                                        </div>
                                        <div id="social_banned_area" class="card-body">


                                            <table>

                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Aktif/Pasif') ?> </td>
                                                    <td>
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input onchange="SocialBlockRefresh(true);" disabled
                                                                type="checkbox"
                                                                class="custom-control-input social_banned_checkbox"
                                                                id="s_switch0">
                                                            <label class="custom-control-label" for="s_switch0">
                                                            </label>
                                                            <span id="s_switch0_text" class="switch-label"></span>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>

                                                    <td style="    font-size: 16px;
                                             color: #626262;" class="font-weight-bold">
                                                        <hr>
                                                        <?= StaticFunctions::lang('Giriş Yöntemleri') ?>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Google') ?></td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input onchange="SocialBlockRefresh();" disabled
                                                                type="checkbox"
                                                                class="custom-control-input social_banned_checkbox social_checknox2"
                                                                id="s_switch1">
                                                            <label class="custom-control-label" for="s_switch1"></label>
                                                            <span id="s_switch1_text" class="switch-label"></span>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Github') ?></td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input onchange="SocialBlockRefresh();" disabled
                                                                type="checkbox"
                                                                class="custom-control-input social_banned_checkbox social_checknox2"
                                                                id="s_switch2">
                                                            <label class="custom-control-label" for="s_switch2"></label>
                                                            <span id="s_switch2_text" class="switch-label"></span>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Linkedin') ?></td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input onchange="SocialBlockRefresh();" disabled
                                                                type="checkbox"
                                                                class="custom-control-input social_banned_checkbox social_checknox2"
                                                                id="s_switch3">
                                                            <label class="custom-control-label" for="s_switch3"></label>
                                                            <span id="s_switch3_text" class="switch-label"></span>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="font-weight-bold">
                                                        <?= StaticFunctions::lang('Facebook') ?></td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input onchange="SocialBlockRefresh();" disabled
                                                                type="checkbox"
                                                                class="custom-control-input social_banned_checkbox social_checknox2"
                                                                id="s_switch4">
                                                            <label class="custom-control-label" for="s_switch4"></label>
                                                            <span id="s_switch4_text" class="switch-label"></span>
                                                        </div>
                                                    </td>
                                                </tr>



                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- social links end -->
                        <!-- permissions start -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header border-bottom mx-2 px-0">
                                    <h6 class="border-bottom py-1 mb-0 font-medium-2"><i
                                            class="feather icon-lock mr-50 "></i><?= StaticFunctions::lang('Hangi kampanyalarımız hakkında güncel fırsat ve haberleri almak istersin?') ?>
                                    </h6>
                                </div>
                                <div class="card-body px-75">
                                    <div class="table-responsive users-view-permission">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><?= StaticFunctions::lang('Email') ?></th>
                                                    <th><?= StaticFunctions::lang('Sms') ?></th>
                                                    <th><?= StaticFunctions::lang('Mobil bildirim') ?></th>
                                                    <th><?= StaticFunctions::lang('Whatsapp') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><?= StaticFunctions::lang('Tüm kampanyalar') ?></td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox1"
                                                                class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label"
                                                                for="users-checkbox1"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox2"
                                                                class="custom-control-input" disabled checked><label
                                                                class="custom-control-label"
                                                                for="users-checkbox2"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox3"
                                                                class="custom-control-input" disabled checked><label
                                                                class="custom-control-label"
                                                                for="users-checkbox3"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-checkbox ml-50"><input
                                                                type="checkbox" id="users-checkbox4"
                                                                class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label"
                                                                for="users-checkbox4"></label>
                                                        </div>
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- permissions end -->
                    </div>
                </section>

            </div>
        </div>
    </div>
    <!-- END: Content-->



    <div class="modal-size-lg mr-1 mb-1 d-inline-block">
        <!-- Modal -->
        <div class="modal fade text-left" id="GoogleModal" tabindex="-1" role="dialog"
            aria-labelledby="GoogleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="GoogleModalLabel">
                            <?= StaticFunctions::lang('Google Authenticator') ?>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="GoogleSuccessGif" width="150px" src="/assets/media/loading.gif" alt="">
                        <hr>
                        <h5 id="GoogleSuccessText">
                            <?= StaticFunctions::lang('Hesabını Google Authenticator ile korumaya alabilmen için yukarıdaki karekodu uygulama üzerinden okutman gerekli. Uygulamaya Android için <a target="_blank" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">buradan</a>, IOS için ise <a target="_blank" href="https://itunes.apple.com/tr/app/google-authenticator/id388497605">buradan</a> ulaşabilirsin.', []) ?>
                        </h5>
                        <br>

                        <div style="display: none;" id="GoogleFooterAreaLoading" class="form-group pin_vtfy">
                            <img width="80px" style="margin: 0 auto;" src="/assets/media/loading.gif" alt="">
                        </div>

                        <div class="alert alert-primary google_footer_k" role="alert">
                            <strong><?= StaticFunctions::lang('Gizli Anahtarın:') . ' ' ?></strong><span
                                id="GoogleSecretKey"></span>
                        </div>

                        <span style="width: 80%;display:block;margin:0 auto;" class="ph_t google_footer_k"><a
                                href="javascript:;" id="GooglePinSendedInfo"></a>
                            <?= StaticFunctions::lang('Gizli anahtarını telefonuna erişememe ihtimaline karşı sakladığından emin olmalısın.') . ' ' ?>
                            <?= StaticFunctions::lang('Karekodu okuttuktan sonra oluşan 6 haneli pin kodunu girerek işlemi tamamlayabilirsin.') ?>
                        </span>

                        <div id="GoogleFooterArea" class="form-group pin_vtfy">


                            <br>
                            <input type="text" id="google-pincode-verify">
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal-size-lg mr-1 mb-1 d-inline-block">
        <!-- Modal -->
        <div class="modal fade text-left" id="NtfModal" tabindex="-1" role="dialog" aria-labelledby="NtfModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="NtfModalLabel">
                            <?= StaticFunctions::lang('Bildirim cihazınızı ayarlayın') ?>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="NtfSuccessGif" width="150px" src="/assets/media/loading.gif" alt="">
                        <hr>
                        <h5 id="NtfSuccessText">
                            <?= StaticFunctions::lang('Giriş yaptığınızda pin kodunu bildirim olarak almak istediğiniz cihazınızda yukarıdaki karekodu okutun ve tarayıcı bildirimlerine izin verin.') ?>
                        </h5>
                        <br>

                        <div style="display: none;" id="NtfFooterAreaLoading" class="form-group pin_vtfy">
                            <img width="80px" style="margin: 0 auto;" src="/assets/media/loading.gif" alt="">
                        </div>


                        <span style="width: 80%;display:block;margin:0 auto;" class="ph_t Ntf_footer_k"><a
                                href="javascript:;" id="NtfPinSendedInfo"></a>
                            <?= StaticFunctions::lang('Bildirimlere izin verdikten sonra gelen 6 haneli pin kodunu girerek işlemi tamamlayabilirsin.') ?>
                        </span>

                        <div id="NtfFooterArea" class="form-group pin_vtfy">


                            <br>
                            <input type="text" id="Ntf-pincode-verify">
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>


</main>
<?php

require_once VDIR . '/console.footer.php';

?>