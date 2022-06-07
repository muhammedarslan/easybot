<?php

$Jwt = $_Params[0];

if ($Jwt == '') {
    StaticFunctions::go_home();
    exit;
}

try {
    $Decoded = \Firebase\JWT\JWT::decode($Jwt, StaticFunctions::JwtKey(), array('HS256'));
} catch (Exception $e) {
    StaticFunctions::go_home();
    exit;
}


$BotName = (isset($Decoded->tokenData->botName)) ? $Decoded->tokenData->botName : '';

$Categories = [
    "1" => 'Alışveriş',
    "2" => 'Bilim',
    "3" => 'Bilişim',
    "4" => 'Blog',
    "5" => 'Din',
    "6" => 'Edebiyat',
    "7" => 'Eğitim',
    "8" => 'Eğlence',
    "9" => 'Ekonomi',
    "10" => 'İstatistik',
    "11" => 'Otomasyon sistemleri',
    "12" => 'Haber',
    "13" => 'Pazarlama',
    "14" => 'Sağlık',
    "15" => 'Sanat',
    "16" => 'Sosyal medya',
    "17" => 'Sözlük',
    "18" => 'Spor',
    "19" => 'Tarih',
    "20" => 'Topluluk/Forum',
    "21" => 'Video/Yayın',
    "22" => 'Diğer'
];

$BotCategories = (isset($Decoded->tokenData->botCategories)) ? $Decoded->tokenData->botCategories : [];

$PageCss = [
    '/assets/console/app-assets/css/pages/dashboard-analytics.css',
    '/assets/console/app-assets/vendors/css/forms/select/select2.min.css'
];

$PageJs = [
    '/assets/console/app-assets/vendors/js/forms/select/select2.full.min.js',
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/i18n/' . LANG . '.js',
    '/assets/console/app-assets/js/core/CreateBot.js'
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
<main data-barba="container" data-barba-easy="<?= StaticFunctions::seo_link(end($PageBreadCrumb['list'])) ?>">
    <style>
    .select2-container--classic .select2-selection--multiple .select2-selection__choice,
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        padding: 2px;
    }

    .select2-container--default .select2-selection--multiple {
        border: 1px solid #D9D9D9;
    }

    .select2-container--classic .select2-selection--multiple .select2-selection__rendered li .select2-search__field,
    .select2-container--default .select2-selection--multiple .select2-selection__rendered li .select2-search__field {
        font-size: 0.85rem;
    }

    ::-webkit-input-placeholder {
        /* Chrome/Opera/Safari */
        color: #D9D9D9;
    }

    ::-moz-placeholder {
        /* Firefox 19+ */
        color: #D9D9D9;
    }

    :-ms-input-placeholder {
        /* IE 10+ */
        color: #D9D9D9;
    }

    :-moz-placeholder {
        /* Firefox 18- */
        color: #D9D9D9;
    }
    </style>

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
            <div style="display:none;" class="content-body MainContent">


                <section id="dashboard-analytics">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card bg-analytics text-white">
                                <div class="card-content">
                                    <div class="card-body text-center">
                                        <img src="/assets/console/app-assets/images/elements/decore-left.png"
                                            class="img-left" alt="
            card-img-left">
                                        <img src="/assets/console/app-assets/images/elements/decore-right.png"
                                            class="img-right" alt="
            card-img-right">
                                        <div class="text-center">
                                            <h1 class="mb-2 text-white">
                                                <?= Staticfunctions::lang('Haydi Başlayalım!') ?></h1>
                                            <p class="m-auto w-75">
                                                <?= Staticfunctions::lang('Hiçbir kod bilgisine gerek olmadan yalnızca birkaç adımda özel botunu birlikte oluşturalım.') ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <section id="floating-label-input">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?= Staticfunctions::lang('Öncelikle Botunu Tanıyalım') ?>
                                    </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div style="margin-bottom: 8px;" class="col-12">
                                                <p><?= Staticfunctions::lang('Senden botun için güzel bir isim ve ne ile alakalı olduğunu bilebilmemiz için birkaç kategori belirlemeni isteyeceğim. İstediğin kadar kategori seçmekte özgürsün, istersen hiç seçmeyedebilirsin.') ?>
                                                </p>
                                            </div>

                                            <div class="col-sm-6 col-12">
                                                <fieldset class="form-label-group">
                                                    <input value="<?= StaticFunctions::say($BotName) ?>" type="text"
                                                        class="form-control" maxlength="50" id="floating-label1"
                                                        name="bot_name" placeholder="Botunun adı">
                                                    <label
                                                        for="floating-label1"><?= Staticfunctions::lang('Botunun adı') ?></label>
                                                </fieldset>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <fieldset class="form-group">
                                                    <select
                                                        placeholder="<?= StaticFunctions::lang('Botunun kategorileri') ?>"
                                                        data-placeholder="<?= StaticFunctions::lang('Botunun kategorileri') ?>"
                                                        lang="<?= LANG ?>" id="floating-label2"
                                                        class="select2 form-control" multiple="multiple">
                                                        <?php

                                                        if (count($BotCategories) > 0) {
                                                            foreach ($Categories as $key => $value) {
                                                                if (in_array($key, $BotCategories)) {
                                                                    echo '<option value="' . $key . '" selected >' . $value . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $key . '" >' . $value . '</option>';
                                                                }
                                                            }
                                                        } else {
                                                            foreach ($Categories as $key => $value) {
                                                                if ($key == '11') {
                                                                    echo '<option value="' . $key . '" selected >' . $value . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $key . '" >' . $value . '</option>';
                                                                }
                                                            }
                                                        }

                                                        ?>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <div class="row match-height">

                    <div class="col-xl-3 col-md-3 col-sm-3">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title"><?= Staticfunctions::lang('Adım Değişkenleri') ?></h4>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-success float-right">4</span>
                                        Cras justo odio
                                    </li>
                                </ul>
                                <div class="card-body">
                                    <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-9 col-md-9 col-sm-9">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?= Staticfunctions::lang('Zaman Akışı') ?></h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <ul class="activity-timeline timeline-left list-unstyled">
                                        <li onclick="NewStep();" style="cursor:pointer;">
                                            <div class="timeline-icon bg-primary">
                                                <i class="feather icon-plus font-medium-2"></i>
                                            </div>
                                            <div class="timeline-info">
                                                <p class="font-weight-bold">
                                                    <?= Staticfunctions::lang('Yeni Adım Oluştur') ?></p>
                                                <span><?= Staticfunctions::lang('Bir kaynaktan veri okumak, okunan verileri işlemek veya sonucu göndermek için buraya tıklayarak yeni bir adım oluştur.') ?></span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </div>
    <!-- END: Content-->


    <div class="modal-size-lg mr-1 mb-1 d-inline-block">
        <!-- Modal -->
        <div class="modal fade text-left" id="AddNewStepModal" tabindex="-1" role="dialog"
            aria-labelledby="AddNewStepModalLabel" aria-hidden="true">
            <div style="max-width: 1200px;" class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="AddNewStepModalLabel">
                            <?= StaticFunctions::lang('Ne yapmak istiyorsun?') ?>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">



                        <div class="row">
                            <div class="col-4">
                                <div class="card border-default text-center bg-transparent">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title mt-1">
                                                <?= Staticfunctions::lang('Statik web sitesinden veri al') ?></h4>
                                            <p class="card-text mb-25 step_card_text">
                                                <?= Staticfunctions::lang('Curl aracılığı ile bir web adresine bağlanır ve oluşan kaynağını alır. Daha hızlı ve ekonomiktir, fakat javascript çalıştırmaz.') ?>
                                            </p>
                                            <button onclick="SaveAndGo('1');"
                                                class="btn btn-primary mt-1 waves-effect waves-light"><?= Staticfunctions::lang('Adımı oluştur') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card border-default text-center bg-transparent">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title mt-1">
                                                <?= Staticfunctions::lang('Dinamik web sitesinden veri al') ?></h4>
                                            <p class="card-text mb-25 step_card_text">
                                                <?= Staticfunctions::lang('Web driver aracılığı ile web adresine bağlanır, içeriğin javascript ile render edilmesini bekler ve oluşan kaynağı alır. ') ?>
                                            </p>
                                            <button disabled onclick="SaveAndGo('2');"
                                                class="btn btn-primary mt-1 waves-effect waves-light"><?= Staticfunctions::lang('Çok yakında...') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card border-default text-center bg-transparent">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title mt-1">
                                                <?= Staticfunctions::lang('Web sitesinin gerçek verisini al') ?>
                                            </h4>
                                            <p class="card-text mb-25 step_card_text">
                                                <?= Staticfunctions::lang('Topluluğun rastgele bir üyesi görevi kabul eder. Üye web adresine gider, güvenlik doğrulamalarını geçer ve oluşan kaynağı alır.') ?>
                                            </p>
                                            <button disabled onclick="SaveAndGo('3');"
                                                class="btn btn-primary mt-1 waves-effect waves-light"><?= Staticfunctions::lang('Çok yakında...') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card border-default text-center bg-transparent">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title mt-1">
                                                <?= Staticfunctions::lang('Html kaynağını işle') ?></h4>
                                            <p class="card-text mb-25 step_card_text">
                                                <?= Staticfunctions::lang('Bot elde ettiği html kaynağı bu adımda işler. İşlenecek alanlar görsel editör yardımıyla seçilir ve botun algoritması belirlenmiş olur.') ?>
                                            </p>
                                            <button onclick="SaveAndGo('4');"
                                                class="btn btn-primary mt-1 waves-effect waves-light"><?= Staticfunctions::lang('Adımı oluştur') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card border-default text-center bg-transparent">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title mt-1">
                                                <?= Staticfunctions::lang('Metni değiştir veya tercüme et') ?></h4>
                                            <p class="card-text mb-25 step_card_text">
                                                <?= Staticfunctions::lang('Elde edilen metin kaynaklarının istenilen dile tercüme edilmesini sağlar. Ayrıca spin yöntemleri ile metinleri özgünleştirebilir.') ?>
                                            </p>
                                            <button onclick="SaveAndGo('5');"
                                                class="btn btn-primary mt-1 waves-effect waves-light"><?= Staticfunctions::lang('Adımı oluştur') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card border-default text-center bg-transparent">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title mt-1">
                                                <?= Staticfunctions::lang('Anlık durum raporu al') ?></h4>
                                            <p class="card-text mb-25 step_card_text">
                                                <?= Staticfunctions::lang('Belirlenen adıma gelene kadar oluşan durum ve hataları kontrol eder ve istenilen yöntem ile kullanıcıya raporlar.') ?>
                                            </p>
                                            <button onclick="SaveAndGo('6');"
                                                class="btn btn-primary mt-1 waves-effect waves-light"><?= Staticfunctions::lang('Adımı oluştur') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card border-default text-center bg-transparent">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title mt-1">
                                                <?= Staticfunctions::lang('Görsel içerikleri düzenle') ?></h4>
                                            <p class="card-text mb-25 step_card_text">
                                                <?= Staticfunctions::lang('Elde edilen görsellerin düzenlenmesini ve görseller üzerine logo eklenmesini sağlar. Ayrıca resim üzerindeki yazıları okuyabilir.') ?>
                                            </p>
                                            <button disabled onclick="SaveAndGo('7');"
                                                class="btn btn-primary mt-1 waves-effect waves-light"><?= Staticfunctions::lang('Çok yakında...') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card border-default text-center bg-transparent">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title mt-1">
                                                <?= Staticfunctions::lang('Sitesinin ekran görüntüsünü al') ?></h4>
                                            <p class="card-text mb-25 step_card_text">
                                                <?= Staticfunctions::lang('Web sitesinin istenilen zamandaki ekran görüntüsünü alır. Bot işlemi tamamlandıktan sonra doğrulama amaçlı kullanılabilir.') ?>
                                            </p>
                                            <button disabled onclick="SaveAndGo('8');"
                                                class="btn btn-primary mt-1 waves-effect waves-light"><?= Staticfunctions::lang('Çok yakında...') ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card border-default text-center bg-transparent">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title mt-1">
                                                <?= Staticfunctions::lang('Bir veri kaynağı yükle') ?></h4>
                                            <p class="card-text mb-25 step_card_text">
                                                <?= Staticfunctions::lang('Botun başka bir adımında kullanılmak üzere veri kaynağı yükleyebilirsiniz. Sql veritabanı dosyalarını veya office dosyalarını destekler.') ?>
                                            </p>
                                            <button disabled onclick="SaveAndGo('9');"
                                                class="btn btn-primary mt-1 waves-effect waves-light"><?= Staticfunctions::lang('Çok yakında...') ?></button>
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


</main>
<?php

require_once VDIR . '/console.footer.php';

?>