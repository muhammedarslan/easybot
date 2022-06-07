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

$SlLang = (LANG == 'gb') ? 'en' : LANG;

$BotName = (isset($Decoded->tokenData->botName) && $Decoded->tokenData->botName != '') ? $Decoded->tokenData->botName : StaticFunctions::lang('Yeni Özel Bot');

$PageCss = [
    '/assets/console/app-assets/css/pages/dashboard-analytics.css',
    '/assets/console/app-assets/vendors/css/forms/select/select2.min.css'
];

$PageJs = [
    '/assets/console/app-assets/vendors/js/forms/select/select2.full.min.js',
    'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/i18n/' . $SlLang . '.js',
    '/assets/console/app-assets/js/core/CreateBot876541.js'
];

$PageBreadCrumb = [
    'isActive' => true,
    'list' => [
        '/console/dashboard' => StaticFunctions::lang('Anasayfa'),
        '/console/custom/bots' => StaticFunctions::lang('Kişisel Botlarım'),
        '/console/create/bot/' . $Jwt => $BotName,
        'active'             => StaticFunctions::lang('Statik web sitesinden veri al')
    ]

];

$_AccountVerifyRequired = true;
$__PageTitle = $BotName . ' | EasyBot';

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
    <form id="CreateBot876541Form" autocomplete="off" novalidate>
        <div class="app-content content">
            <div class="content-overlay"></div>
            <div class="header-navbar-shadow"></div>
            <div class="content-wrapper">
                <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
                <a href="/console/create/bot/<?= $Jwt ?>">
                    <button type="button"
                        class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light back_cr_btn"><?= Staticfunctions::lang('Kuruluma Geri Dön') ?></button>
                </a>
                <div style="display:none;" class="content-body MainContent">


                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?= Staticfunctions::lang('Adresi Belirleyelim') ?>
                                    </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div style="margin-bottom: 8px;" class="col-12">
                                                <p><?= Staticfunctions::lang('Öncelikle veri çekmek istediğin adresi girmeni, protokolü ve istek tipini belirlemeni isteyeceğim. Gecikmelerden kaçınmak için doğru protokolü seçtiğinden ve adresi tam olarak doğru girdiğinden emin olmalısın.') ?>
                                                </p>
                                            </div>

                                            <div style="padding-right: 5px;" class="col-1">
                                                <fieldset class="form-label-group">
                                                    <select style="font-weight: bold;text-align-last:center;"
                                                        class="form-control" name="876541_type" id="label3">
                                                        <option value="get">GET</option>
                                                        <option value="post">POST</option>
                                                        <option value="put">PUT</option>
                                                        <option value="patch">PATCH</option>
                                                        <option value="delete">DELETE</option>
                                                        <option value="head">HEAD</option>
                                                    </select>
                                                    <label
                                                        for="label3"><?= Staticfunctions::lang('İstek tipi') ?></label>
                                                </fieldset>
                                            </div>
                                            <div style="padding-right: 5px;padding-left:5px;" class="col-1">
                                                <fieldset class="form-label-group">
                                                    <select style="font-weight: bold;text-align-last:center;"
                                                        class="form-control" name="876541_protocol" id="label1">
                                                        <option value="http">http://</option>
                                                        <option value="https">https://</option>
                                                    </select>
                                                    <label for="label1"><?= Staticfunctions::lang('Protokol') ?></label>
                                                </fieldset>
                                            </div>
                                            <div style="padding-left: 5px;" class="col-10">
                                                <fieldset class="form-group form-label-group">
                                                    <input onkeyup="ParameterChangeEventInput();" required
                                                        data-validation-required-message="<?= StaticFunctions::lang('Bu alan zorunludur.') ?>"
                                                        onchange="AddressChangeEvent();ParameterChangeEventInput();RemoveUnusedPrameters();"
                                                        value="" type="text" data-show-variables="on"
                                                        class="form-control" maxlength="500" id="label2"
                                                        name="876541_address"
                                                        placeholder="<?= Staticfunctions::lang('Veri çekmek istediğin adresi girmelisin. Örneğin: www.example.com/first/second/third.html') ?>">
                                                    <label
                                                        for="label2"><?= Staticfunctions::lang('Veri çekmek istediğin adres:') ?></label>
                                                </fieldset>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="text" name="page_token" value="<?= $Jwt ?>" hidden>

                    <section id="nav-justified">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card overflow-hidden">
                                    <div class="card-header">
                                        <h4 class="card-title"><?= Staticfunctions::lang('Verileri Belirleyelim') ?>
                                        </h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <p><?= Staticfunctions::lang('Adrese göndermek istediğin {0}, {1} {2} ve {3} verilerini buradan belirleyebilirsin.', [
                                                    '<code>' . Staticfunctions::lang('parametreleri') . '</code>',
                                                    '<code>header</code>',
                                                    '<code>body</code>',
                                                    '<code>çerez</code>'
                                                ]) ?></p>
                                            <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="home-tab-justified" data-toggle="tab"
                                                        href="#data-tab1" role="tab" aria-controls="data-tab1"
                                                        aria-selected="true"><?= Staticfunctions::lang('Url Parametreleri') ?></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="profile-tab-justified" data-toggle="tab"
                                                        href="#data-tab2" role="tab" aria-controls="data-tab2"
                                                        aria-selected="false"><?= Staticfunctions::lang('Header verileri') ?></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="messages-tab-justified" data-toggle="tab"
                                                        href="#data-tab3" role="tab" aria-controls="data-tab3"
                                                        aria-selected="false"><?= Staticfunctions::lang('Body verileri') ?></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="settings-tab-justified" data-toggle="tab"
                                                        href="#data-tab4" role="tab" aria-controls="data-tab4"
                                                        aria-selected="false"><?= Staticfunctions::lang('Çerez verileri') ?></a>
                                                </li>
                                            </ul>

                                            <!-- Tab panes -->
                                            <div class="tab-content pt-1">
                                                <div class="tab-pane active" id="data-tab1" role="tabpanel"
                                                    aria-labelledby="home-tab-justified">


                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mb-0 text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th><?= Staticfunctions::lang('Anahtar') ?></th>
                                                                    <th><?= Staticfunctions::lang('Değer') ?></th>
                                                                    <th><?= Staticfunctions::lang('Açıklama (isteğe bağlı)') ?>
                                                                    <th><?= Staticfunctions::lang('Kaldır') ?>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="Tab1Table">
                                                                <tr>


                                                                    <td style="width: 20px;" scope="row">
                                                                        <fieldset>
                                                                            <div
                                                                                class="vs-checkbox-con vs-checkbox-primary">
                                                                                <input onclick="ParameterChangeEvent();"
                                                                                    type="checkbox"
                                                                                    name="876541_data1_active[]"
                                                                                    checked="" value="active">
                                                                                <span class="vs-checkbox">
                                                                                    <span class="vs-checkbox--check">
                                                                                        <i
                                                                                            class="vs-icon feather icon-check"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </fieldset>
                                                                    </td>


                                                                    <td>
                                                                        <input
                                                                            onkeyup="ValueChangeTab1();ParameterChangeEvent();"
                                                                            onchange="ValueChangeTab1();ParameterChangeEvent();"
                                                                            onclick="ValueChangeTab1();ParameterChangeEvent();"
                                                                            value="" type="text"
                                                                            data-show-variables="on"
                                                                            class="form-control" maxlength="250"
                                                                            name="876541_data1_key[]"
                                                                            placeholder="<?= Staticfunctions::lang('Anahtar') ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input
                                                                            onkeyup="ValueChangeTab1();ParameterChangeEvent();"
                                                                            onchange="ValueChangeTab1();ParameterChangeEvent();"
                                                                            onclick="ValueChangeTab1();ParameterChangeEvent();"
                                                                            value="" type="text"
                                                                            data-show-variables="on"
                                                                            class="form-control" maxlength="250"
                                                                            name="876541_data1_value[]"
                                                                            placeholder="<?= Staticfunctions::lang('Değer') ?>">
                                                                    </td>
                                                                    <td>
                                                                        <textarea rows="1"
                                                                            name="876541_data1_description[]"
                                                                            maxlength="250"
                                                                            placeholder="<?= StaticFunctions::lang('Açıklama (isteğe bağlı)') ?>"
                                                                            class="form-control"></textarea>
                                                                    </td>
                                                                    <td>
                                                                        <a onclick="DeleteTab1Row(this);ParameterChangeEvent();"
                                                                            class="delete_link" href="javascript:;">
                                                                            <span class="action-delete"><i
                                                                                    class="feather icon-trash"></i></span>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>


                                                <div class="tab-pane" id="data-tab2" role="tabpanel"
                                                    aria-labelledby="profile-tab-justified">


                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mb-0 text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th><?= Staticfunctions::lang('Anahtar') ?></th>
                                                                    <th><?= Staticfunctions::lang('Değer') ?></th>
                                                                    <th><?= Staticfunctions::lang('Açıklama (isteğe bağlı)') ?>
                                                                    <th><?= Staticfunctions::lang('Kaldır') ?>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="Tab2Table">
                                                                <?php

                                                                $StaticHeaders = [
                                                                    [
                                                                        'key' => 'Cache-Control',
                                                                        'value' => 'no-cache',
                                                                        'description' => Staticfunctions::lang('İstek-yanıt zinciri boyunca tüm önbellekleme mekanizmalarının uyması gereken yönergeleri belirtmek için kullanılır.'),
                                                                        'rows' => 3
                                                                    ],
                                                                    [
                                                                        'key' => 'Content-Length',
                                                                        'value' =>  Staticfunctions::lang('<istek gönderilirken hesaplanır>'),
                                                                        'description' => Staticfunctions::lang('İstek gövdesinin sekizli uzunluğu (8-bit bayt) otomatik olarak hesaplanır ve gönderilir.'),
                                                                        'rows' => 2
                                                                    ],
                                                                    [
                                                                        'key' => 'Easybot-Ray',
                                                                        'value' =>  Staticfunctions::lang('<istek gönderilirken belirlenir>'),
                                                                        'description' => Staticfunctions::lang('Easybot tarafından her istek için özel üretilen benzersiz ve gönderilmesi zorunlu olan anahtar değeridir. Raporlama ve hata ayıklama amacıyla kullanılır.'),
                                                                        'rows' => 3
                                                                    ]
                                                                ];

                                                                foreach ($StaticHeaders as $key => $value) {
                                                                    echo '<tr>
                                                                    <td style="width: 20px;" scope="row">
                                                                        <fieldset>
                                                                            <div
                                                                                class="vs-checkbox-con vs-checkbox-primary">
                                                                                <input checked disabled type="checkbox"
                                                                                    name="static_876541_data2_active[]"
                                                                                    checked="" value="active">
                                                                                <span class="vs-checkbox">
                                                                                    <span class="vs-checkbox--check">
                                                                                        <i
                                                                                            class="vs-icon feather icon-check"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </fieldset>
                                                                    </td>
                                                                    <td>
                                                                        <input disabled value="' . StaticFunctions::say($value['key']) . '" type="text"
                                                                            class="form-control" maxlength="250"
                                                                            name="static_876541_data2_key[]"
                                                                            placeholder="' . Staticfunctions::lang('Anahtar') . '">
                                                                    </td>
                                                                    <td>
                                                                        <input disabled value="' . StaticFunctions::say($value['value']) . '" type="text"
                                                                            class="form-control" maxlength="250"
                                                                            name="static_876541_data2_value[]"
                                                                            placeholder="' . Staticfunctions::lang('Değer') . '">
                                                                    </td>
                                                                    <td>
                                                                        <textarea style="resize:none;" disabled rows="' . StaticFunctions::say($value['rows']) . '"
                                                                            name="static_876541_data2_description[]"
                                                                            maxlength="250"
                                                                            placeholder="' . StaticFunctions::lang('Açıklama (isteğe bağlı)') . '"
                                                                            class="form-control">' . StaticFunctions::say($value['description']) . '</textarea>
                                                                    </td>
                                                                    <td>
                                                                        <a style="pointer-events: none;opacity:0.5;"
                                                                            class="delete_link_static delete_data_2"
                                                                            href="javascript:;">
                                                                            <span class="action-delete"><i
                                                                                    class="feather icon-trash"></i></span>
                                                                        </a>
                                                                    </td>
                                                                </tr>';
                                                                }

                                                                ?>

                                                                <tr>
                                                                    <td style="width: 20px;" scope="row">
                                                                        <fieldset>
                                                                            <div
                                                                                class="vs-checkbox-con vs-checkbox-primary">
                                                                                <input type="checkbox"
                                                                                    name="876541_data2_active[]"
                                                                                    checked="" value="active">
                                                                                <span class="vs-checkbox">
                                                                                    <span class="vs-checkbox--check">
                                                                                        <i
                                                                                            class="vs-icon feather icon-check"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </fieldset>
                                                                    </td>
                                                                    <td>
                                                                        <input onkeyup="ValueChangeTab2();"
                                                                            onchange="ValueChangeTab2();"
                                                                            onclick="ValueChangeTab2();" value=""
                                                                            type="text" data-show-variables="on"
                                                                            class="form-control" maxlength="250"
                                                                            name="876541_data2_key[]"
                                                                            placeholder="<?= Staticfunctions::lang('Anahtar') ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input onkeyup="ValueChangeTab2();"
                                                                            onchange="ValueChangeTab2();"
                                                                            onclick="ValueChangeTab2();" value=""
                                                                            type="text" data-show-variables="on"
                                                                            class="form-control" maxlength="250"
                                                                            name="876541_data2_value[]"
                                                                            placeholder="<?= Staticfunctions::lang('Değer') ?>">
                                                                    </td>
                                                                    <td>
                                                                        <textarea rows="1"
                                                                            name="876541_data2_description[]"
                                                                            maxlength="250"
                                                                            placeholder="<?= StaticFunctions::lang('Açıklama (isteğe bağlı)') ?>"
                                                                            class="form-control"></textarea>
                                                                    </td>
                                                                    <td>
                                                                        <a onclick="DeleteTab2Row(this);"
                                                                            class="delete_data_2" href="javascript:;">
                                                                            <span class="action-delete"><i
                                                                                    class="feather icon-trash"></i></span>
                                                                        </a>
                                                                    </td>
                                                                </tr>




                                                            </tbody>
                                                        </table>
                                                    </div>


                                                </div>

                                                <div class="tab-pane" id="data-tab3" role="tabpanel"
                                                    aria-labelledby="profile-tab-justified">

                                                    <ul class="list-unstyled mb-0">
                                                        <li class="d-inline-block mr-2">
                                                            <fieldset>
                                                                <label>
                                                                    <input value="0" onclick="Tab3RadioClick();"
                                                                        type="radio" name="876541_data3_radio">
                                                                    <?= Staticfunctions::lang('Veri yok') ?>
                                                                </label>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-2">
                                                            <fieldset>
                                                                <label>
                                                                    <input value="1" onclick="Tab3RadioClick();"
                                                                        type="radio" name="876541_data3_radio">
                                                                    form-data
                                                                </label>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-2">
                                                            <fieldset>
                                                                <label>
                                                                    <input value="2" onclick="Tab3RadioClick();" checked
                                                                        type="radio" name="876541_data3_radio">
                                                                    x-www-form-urlencoded
                                                                </label>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-2">
                                                            <fieldset>
                                                                <label>
                                                                    <input value="3" onclick="Tab3RadioClick();"
                                                                        type="radio" name="876541_data3_radio">
                                                                    raw
                                                                </label>
                                                            </fieldset>
                                                        </li>

                                                    </ul>

                                                    <div style="display: none;"
                                                        class="easycreate_tab3 easycreate_tab3_0">
                                                        <p class="text-muted mt-50 mb-0">
                                                            <?= Staticfunctions::lang('Bu istek bir body verisi içermiyor.') ?>
                                                        </p>
                                                    </div>

                                                    <div class="easycreate_tab3 easycreate_tab3_1 table-responsive">
                                                        <table class="table table-bordered mb-0 text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th><?= Staticfunctions::lang('Anahtar') ?></th>
                                                                    <th><?= Staticfunctions::lang('Değer') ?></th>
                                                                    <th><?= Staticfunctions::lang('Açıklama (isteğe bağlı)') ?>
                                                                    <th><?= Staticfunctions::lang('Kaldır') ?>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="Tab3Table">

                                                                <tr>
                                                                    <td style="width: 20px;" scope="row">
                                                                        <fieldset>
                                                                            <div
                                                                                class="vs-checkbox-con vs-checkbox-primary">
                                                                                <input type="checkbox"
                                                                                    name="876541_data3_active[]"
                                                                                    checked="" value="active">
                                                                                <span class="vs-checkbox">
                                                                                    <span class="vs-checkbox--check">
                                                                                        <i
                                                                                            class="vs-icon feather icon-check"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </fieldset>
                                                                    </td>
                                                                    <td>
                                                                        <input onkeyup="ValueChangeTab3();"
                                                                            onchange="ValueChangeTab3();"
                                                                            onclick="ValueChangeTab3();" value=""
                                                                            type="text" data-show-variables="on"
                                                                            class="form-control" maxlength="250"
                                                                            name="876541_data3_key[]"
                                                                            placeholder="<?= Staticfunctions::lang('Anahtar') ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input onkeyup="ValueChangeTab3();"
                                                                            onchange="ValueChangeTab3();"
                                                                            onclick="ValueChangeTab3();" value=""
                                                                            type="text" data-show-variables="on"
                                                                            class="form-control" maxlength="250"
                                                                            name="876541_data3_value[]"
                                                                            placeholder="<?= Staticfunctions::lang('Değer') ?>">
                                                                    </td>
                                                                    <td>
                                                                        <textarea rows="1"
                                                                            name="876541_data3_description[]"
                                                                            maxlength="250"
                                                                            placeholder="<?= StaticFunctions::lang('Açıklama (isteğe bağlı)') ?>"
                                                                            class="form-control"></textarea>
                                                                    </td>
                                                                    <td>
                                                                        <a onclick="DeleteTab3Row(this);"
                                                                            class="delete_data_3" href="javascript:;">
                                                                            <span class="action-delete"><i
                                                                                    class="feather icon-trash"></i></span>
                                                                        </a>
                                                                    </td>
                                                                </tr>




                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div style="display: none;"
                                                        class="easycreate_tab3 easycreate_tab3_3">
                                                        <textarea data-show-variables="on" rows="25" cols="40"
                                                            name="876541_data3_raw" maxlength="1000"
                                                            class="raw_data form-control"></textarea>
                                                    </div>


                                                </div>



                                                <div class="tab-pane" id="data-tab4" role="tabpanel"
                                                    aria-labelledby="settings-tab-justified">


                                                    <div class="table-responsive">
                                                        <table class="table table-bordered mb-0 text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th><?= Staticfunctions::lang('Anahtar') ?></th>
                                                                    <th><?= Staticfunctions::lang('Değer') ?></th>
                                                                    <th><?= Staticfunctions::lang('Alan Adı') ?>
                                                                    <th><?= Staticfunctions::lang('Kaldır') ?>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="Tab4Table">
                                                                <tr>
                                                                    <td style="width: 20px;" scope="row">
                                                                        <fieldset>
                                                                            <div
                                                                                class="vs-checkbox-con vs-checkbox-primary">
                                                                                <input type="checkbox"
                                                                                    name="876541_data4_active[]"
                                                                                    checked="" value="active">
                                                                                <span class="vs-checkbox">
                                                                                    <span class="vs-checkbox--check">
                                                                                        <i
                                                                                            class="vs-icon feather icon-check"></i>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </fieldset>
                                                                    </td>
                                                                    <td>
                                                                        <input onkeyup="ValueChangeTab4();"
                                                                            onchange="ValueChangeTab4();"
                                                                            onclick="ValueChangeTab4();" value=""
                                                                            type="text" data-show-variables="on"
                                                                            class="form-control" maxlength="250"
                                                                            name="876541_data4_key[]"
                                                                            placeholder="<?= Staticfunctions::lang('Anahtar') ?>">
                                                                    </td>
                                                                    <td>
                                                                        <textarea onkeyup="ValueChangeTab4();"
                                                                            onchange="ValueChangeTab4();"
                                                                            onclick="ValueChangeTab4();"
                                                                            data-show-variables="on" rows="1"
                                                                            name="876541_data4_value[]" maxlength="250"
                                                                            placeholder="<?= StaticFunctions::lang('Çerez değeri') ?>"
                                                                            class="form-control"></textarea>
                                                                    </td>
                                                                    <td>
                                                                        <input onkeyup="ValueChangeTab4();"
                                                                            onchange="ValueChangeTab4();"
                                                                            onclick="ValueChangeTab4();" value=""
                                                                            type="text" data-show-variables="on"
                                                                            class="form-control" maxlength="250"
                                                                            name="876541_data4_domain[]"
                                                                            placeholder="<?= Staticfunctions::lang('Alan adı (example.com)') ?>">
                                                                    </td>
                                                                    <td>
                                                                        <a onclick="DeleteTab4Row(this);"
                                                                            class="delete_data_4" href="javascript:;">
                                                                            <span class="action-delete"><i
                                                                                    class="feather icon-trash"></i></span>
                                                                        </a>
                                                                    </td>
                                                                </tr>




                                                            </tbody>
                                                        </table>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div id="Page876541Texts" style="display: none !important;">
                        <span data-key="BackSwalTitle"><?= StaticFunctions::lang('Emin Misin?') ?></span>
                        <span data-key="BackSwalButton1"><?= StaticFunctions::lang('Geri Dön') ?></span>
                        <span data-key="BackSwalButton2"><?= StaticFunctions::lang('Adımı Koru') ?></span>
                        <span
                            data-key="BackSwalMessage"><?= StaticFunctions::lang('Kuruluma geri döndüğün taktirde bu adımda yaptığın tüm değişiklikleri kaybedeceksin. Ne yapmak istiyorsun?') ?></span>
                    </div>



                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?= Staticfunctions::lang('Son Ayarlamaları Yapalım') ?>
                                    </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-3">
                                                <p><?= Staticfunctions::lang('Kullanmak istediğin tarayıcıyı seçebilirsin.') ?>
                                                </p>
                                                <div class="form-group">
                                                    <select name="876541_browser"
                                                        data-placeholder="<?= StaticFunctions::lang('Tarayıcı seçimi') ?>"
                                                        class="select2-icons form-control">

                                                        <optgroup label="<?= Staticfunctions::lang('Tarayıcılar') ?>">
                                                            <option value="0" data-icon="fa fa-windows">Windows 10
                                                                (chrome)</option>
                                                            <option value="1" data-icon="fa fa-windows">Windows 8
                                                                (chrome)</option>
                                                            <option value="2" data-icon="fa fa-windows">Windows 7
                                                                (chrome)</option>
                                                            <option value="3" data-icon="fa fa-windows">Windows NT
                                                                (chrome)</option>
                                                            <option value="4" data-icon="fa fa-windows">Windows Phone
                                                            </option>
                                                            <option value="5" data-icon="fa fa-android">Android</option>
                                                            <option value="6" data-icon="fa fa-mobile">iPhone OS
                                                            </option>
                                                            <option value="7" data-icon="fa fa-firefox">Firefox OS
                                                            </option>
                                                            <option value="8" data-icon="fa fa-linux">FreeBSD</option>
                                                            <option value="9" data-icon="fa fa-linux">Linux</option>
                                                            <option value="10" data-icon="fa fa-linux">Ubuntu</option>
                                                            <option value="11" data-icon="fa fa-linux">NetBSD</option>
                                                            <option value="12" data-icon="fa fa-apple">OS X</option>
                                                            <option value="13" data-icon="fa fa-gamepad">PlayStation 3
                                                            </option>
                                                            <option value="14" data-icon="fa fa-gamepad">PlayStation 4
                                                            </option>
                                                            <option value="15" data-icon="fa fa-gamepad">PlayStation
                                                                Portable</option>
                                                            <option value="16" data-icon="fa fa-gamepad">PlayStation
                                                                Vita</option>
                                                            <option value="17" data-icon="fa fa-gamepad">Xbox One
                                                            </option>
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <p><?= Staticfunctions::lang('Kullanmak istediğin tarayıcı dilini seçebilirsin.') ?>
                                                </p>
                                                <div class="form-group">
                                                    <select name="876541_browser_language"
                                                        data-placeholder="<?= StaticFunctions::lang('Dil seçimi') ?>"
                                                        class="select2-icons form-control">

                                                        <optgroup label="<?= Staticfunctions::lang('Diller') ?>">
                                                            <?php

                                                            $ListArray = json_decode(file_get_contents(APP_DIR . '/storage/languageList.json'), true);

                                                            foreach ($ListArray as $key => $lang) {
                                                                $icon = ($key == 'en') ? 'us' : $key;
                                                                $clang = (LANG == 'gb') ? 'en' : LANG;
                                                                $sl = ($key == $clang) ? 'selected' : '';
                                                                echo '<option ' . $sl . ' value="' . $key . '" data-icon="flag-icon flag-icon-' . $icon . '">' . StaticFunctions::say($lang['nativeName']) . '</option>';
                                                            }

                                                            ?>
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <p><?= Staticfunctions::lang('Bağlanmak istediğin ülkeyi seçebilirsin. (proxy)') ?>
                                                </p>
                                                <div class="form-group">
                                                    <select name="876541_proxy_country" onchange="ProxyCountryChange();"
                                                        data-placeholder="<?= StaticFunctions::lang('Ülke seçimi') ?>"
                                                        class="select2-icons form-control">
                                                        <?php

                                                        $ListArray = json_decode(file_get_contents(APP_DIR . '/storage/proxyCountryList.json'), true);
                                                        $CountList = file_get_contents(APP_DIR . '/storage/proxyCountryDatacenter.json');
                                                        $DecodeCount = json_decode($CountList, true);

                                                        foreach ($ListArray as $key => $Region) {
                                                            echo ' <optgroup label="' . Staticfunctions::lang($key) . '">';
                                                            foreach ($Region as $key => $Country) {

                                                                if (isset($DecodeCount[$key])) {
                                                                    $icon = ($key == 'en') ? 'us' : $key;
                                                                    $clang = (LANG == 'en') ? 'us' : LANG;
                                                                    $sl = ($icon == $clang) ? 'selected' : '';
                                                                    echo '<option ' . $sl . ' value="' . $key . '" data-icon="flag-icon flag-icon-' . $icon . '">' . StaticFunctions::say($Country) . '</option>';
                                                                }
                                                            }
                                                            echo '</optgroup>';
                                                        }

                                                        ?>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <p><?= Staticfunctions::lang('Proxy ağ türünü seçebilirsin.') ?>
                                                </p>
                                                <div class="form-group">
                                                    <select name="876541_proxy_network_type" disabled
                                                        data-placeholder="<?= StaticFunctions::lang('Ağ Türleri') ?>"
                                                        class="select2-icons-proxy form-control">

                                                        <option selected value="0" data-icon="fa fa-server">
                                                            <?= Staticfunctions::lang('Veri Merkezi Ip Havuzu') ?>
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                                <button type="submit"
                                                    class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1"><?= StaticFunctions::lang('Adımı Oluştur') ?></button>
                                                <button onclick="Back2Create();" type="button"
                                                    class="btn btn-outline-primary"><?= StaticFunctions::lang('Geri Dön') ?></button>
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
        <!-- END: Content-->

    </form>
</main>
<?php

require_once VDIR . '/console.footer.php';

?>