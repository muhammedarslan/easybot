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
        '/console/dashboard'       => StaticFunctions::lang('Anasayfa'),
        '/console/account/profile' => StaticFunctions::lang('Hesap & Profil'),
        'active'                   => StaticFunctions::lang('Profil Düzenle')
    ]

];

require_once VDIR . '/console.header.php';
require_once VDIR . '/console.menu.php';


?>
<main data-barba="container" data-barba-easy="accountandprofileedit">

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
            <div style="display:none;" class="content-body MainContent">
                <div id="EditProfileTexts" style="display: none !important;">
                    <span data-key="DeleteAvatarModalTitle"><?= StaticFunctions::lang('Emin Misin?') ?></span>
                    <span data-key="DeleteAvatarModalButton1"><?= StaticFunctions::lang('Evet, kaldır') ?></span>
                    <span data-key="DeleteAvatarModalButton2"><?= StaticFunctions::lang('Hayır') ?></span>
                    <span data-key="PhoneChangedTitle"><?= StaticFunctions::lang('Başarıyla Tamamlandı!') ?></span>
                    <span
                        data-key="PhoneChangedMessage"><?= StaticFunctions::lang('Telefon numaran başarıyla güncellendi.') ?></span>
                    <span
                        data-key="DeleteAvatarModalMessage"><?= StaticFunctions::lang('Profil fotoğrafın kaldırılacak. Devam etmek istiyor musun?') ?></span>
                </div>


                <!-- users edit start -->
                <section class="users-edit">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center active" id="account-tab"
                                            data-toggle="tab" href="#account" aria-controls="account" role="tab"
                                            aria-selected="true">
                                            <i class="feather icon-user mr-25"></i><span
                                                class="d-none d-sm-block"><?= StaticFunctions::lang('Profilimi Düzenle') ?></span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="account" aria-labelledby="account-tab"
                                        role="tabpanel">
                                        <!-- users edit media object start -->
                                        <div class="media mb-2">
                                            <a class="mr-2 my-25" href="javascript:;">
                                                <img src="/assets/media/img_loading.gif" alt="users avatar"
                                                    id="AvatarSrc" class="users-avatar-shadow rounded" height="90"
                                                    width="90">
                                            </a>
                                            <div class="media-body mt-50">
                                                <h4 class="media-heading" data-key="fullname">
                                                    <?= $_SESSION['UserSession']->real_name ?></h4>
                                                <div class="col-12 d-flex mt-1 px-0">
                                                    <a href="javascript:;" onclick="ChangeAvatar();"
                                                        class="btn btn-primary d-none d-sm-block mr-75 edt_avatar"><?= StaticFunctions::lang('Değiştir') ?></a>
                                                    <a href="javascript:;" onclick="ChangeAvatar();"
                                                        class="btn btn-primary d-block d-sm-none mr-75 edt_avatar"><i
                                                            class="feather icon-edit-1"></i></a>
                                                    <a href="javascript:;" onclick="RemoveAvatar();"
                                                        class="btn btn-outline-danger d-none d-sm-block rmv_avatar"><?= StaticFunctions::lang('Kaldır') ?></a>
                                                    <a href="javascript:;" onclick="RemoveAvatar();"
                                                        class="btn btn-outline-danger d-block d-sm-none rmv_avatar"><i
                                                            class="feather icon-trash-2"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <form class="UploadAvatarForm" method="post" style="display: none;">
                                            <input type="text" hidden name="file" value="upload">
                                            <input onChange="UploadAvatar();" autocomplete="off" hidden
                                                accept="image/png, image/jpeg" name="profile_avatar" id="profile_avatar"
                                                type="file" class="form-control form-input profile_avatar">

                                        </form>
                                        <!-- users edit media object ends -->
                                        <!-- users edit account form start -->
                                        <form style="pointer-events: none;opacity:0.5" id="EditMyProfileForm"
                                            novalidate>
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label><?= StaticFunctions::lang('Adınız') ?></label>
                                                            <input name="f_name" data-val="name" type="text"
                                                                class="form-control"
                                                                placeholder="<?= StaticFunctions::lang('Adınız') ?>"
                                                                value="" required
                                                                data-validation-required-message="<?= StaticFunctions::lang('Bu alan zorunludur.') ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div data-toggle="popover"
                                                            data-content="<?= StaticFunctions::lang('Hesap güvenliğiniz nedeniyle e-posta adresinizi değiştiremezsiniz. Eğer buna gerçekten ihtiyacınız varsa doğrulama için lütfen bizimle iletişime geçin.') ?>"
                                                            data-trigger="hover" data-placement="bottom"
                                                            data-original-title="<?= StaticFunctions::lang('Neden düzenleyemiyorum?') ?>"
                                                            class="controls">
                                                            <label><?= StaticFunctions::lang('E-posta Adresiniz') ?></label>
                                                            <input data-val="email" disabled type="email"
                                                                class="form-control"
                                                                placeholder="<?= StaticFunctions::lang('E-posta Adresiniz') ?>"
                                                                value="">
                                                        </div>

                                                    </div>
                                                    <div class="form-group">
                                                        <label><?= StaticFunctions::lang('Uygulama Modu') ?></label>
                                                        <select data-val="AppMode" name="f_app_mode"
                                                            class="form-control">
                                                            <option value="Light">
                                                                <?= StaticFunctions::lang('Açık Mod') ?></option>
                                                            <option value="Dark">
                                                                <?= StaticFunctions::lang('Koyu Mod') ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">

                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label><?= StaticFunctions::lang('Soyadınız') ?></label>
                                                            <input name="f_surname" data-val="surname" type="text"
                                                                class="form-control"
                                                                placeholder="<?= StaticFunctions::lang('Soyadınız') ?>"
                                                                value="" required
                                                                data-validation-required-message="<?= StaticFunctions::lang('Bu alan zorunludur.') ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label><?= StaticFunctions::lang('Telefon Numaranız') ?></label>
                                                            <input name="f_tel" id="PhnNumbersEdit"
                                                                data-val="phone_number_nomask" type="text"
                                                                class="form-control"
                                                                placeholder="<?= StaticFunctions::lang('Telefon Numaranız') ?>"
                                                                value="">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label><?= StaticFunctions::lang('Uygulama Dili') ?></label>
                                                        <select data-val="AppLang" name="f_site_lang"
                                                            class="form-control">
                                                            <?php

                                                            $LangsArray = AppLanguage::$AllowedLangs;

                                                            foreach ($LangsArray as $key => $value) {
                                                                $key2 = ($key == 'en') ? 'us' : $key;
                                                                echo '<option value="' . $key . '" >' . $value['LangName'] . '</option>';
                                                            }

                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div style="margin-top: 20px;margin-bottom:20px;" class="col-12">
                                                    <div class="table-responsive border rounded px-1 ">
                                                        <h6 class="border-bottom py-1 mx-1 mb-0 font-medium-2"><i
                                                                class="feather icon-lock mr-50 "></i><?= StaticFunctions::lang('Şifremi Değiştir') ?>
                                                        </h6>


                                                        <div class="col-8" style="margin-top: 40px;">

                                                            <div class="form form-horizontal">
                                                                <div class="form-body">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span><?= StaticFunctions::lang('Eski Şifreniz') ?></span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <div
                                                                                        class="position-relative has-icon-left">
                                                                                        <input type="password"
                                                                                            id="fname-icon"
                                                                                            class="form-control cng_password"
                                                                                            name="f_old_password"
                                                                                            placeholder="<?= StaticFunctions::lang('Eski Şifreniz') ?>">
                                                                                        <div
                                                                                            class="form-control-position">
                                                                                            <i
                                                                                                class="feather icon-user"></i>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span><?= StaticFunctions::lang('Yeni Şifreniz') ?></span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <div
                                                                                        class="position-relative has-icon-left">
                                                                                        <input type="password"
                                                                                            id="email-icon"
                                                                                            class="form-control cng_password"
                                                                                            name="f_new_password"
                                                                                            placeholder="<?= StaticFunctions::lang('Yeni Şifreniz') ?>">
                                                                                        <div
                                                                                            class="form-control-position">
                                                                                            <i
                                                                                                class="feather icon-lock"></i>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span><?= StaticFunctions::lang('Yeni Şifrenizi Onaylayın') ?></span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <div
                                                                                        class="position-relative has-icon-left">
                                                                                        <input type="password"
                                                                                            id="pass-icon"
                                                                                            class="form-control cng_password"
                                                                                            name="f_new_password2"
                                                                                            placeholder="<?= StaticFunctions::lang('Yeni Şifrenizi Onaylayın') ?>">
                                                                                        <div
                                                                                            class="form-control-position">
                                                                                            <i
                                                                                                class="feather icon-lock"></i>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>


                                                    </div>
                                                </div>
                                                <div
                                                    class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                                    <button type="submit"
                                                        class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1"><?= StaticFunctions::lang('Değişiklikleri Kaydet') ?></button>
                                                    <button onclick="LoadProfileInfo();" type="reset"
                                                        class="btn btn-outline-warning"><?= StaticFunctions::lang('Varsayılana Dön') ?></button>
                                                </div>
                                            </div>
                                        </form>
                                        <!-- users edit account form ends -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- users edit ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->


</main>
<?php

require_once VDIR . '/console.footer.php';

?>