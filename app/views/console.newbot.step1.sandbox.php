<?php

$Jwt = $_Params[0];
$requestToken = $_Params[1];

if ($Jwt == '' || $requestToken == '') {
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

$Me = StaticFunctions::get_id();
$Now = time();
$TempQuery = $db->query("SELECT * FROM processor_temp WHERE temp_token='{$requestToken}' and user_id='{$Me}' and temp_status=2 and temp_process='create_bot_876541'")->fetch(PDO::FETCH_ASSOC);

if (!$TempQuery || $TempQuery['temp_file'] == null || $TempQuery['temp_headers'] == null) {
    StaticFunctions::go_home();
    exit;
}

if (!file_exists(APP_DIR . '/tmp/' . $TempQuery['temp_file'])) {
    StaticFunctions::go_home();
    exit;
}

$RequestData = json_decode($TempQuery['temp_data'], true);

$PageCss = [];

$PageJs = [];

$PageBreadCrumb = [
    'isActive' => true,
    'list' => [
        '/console/dashboard' => StaticFunctions::lang('Anasayfa'),
        'active'             => StaticFunctions::lang('BoşSayfa')
    ]

];

$_AccountVerifyRequired = true;

require_once VDIR . '/console.header.php';
require_once VDIR . '/console.menu.php';


?>
<main data-barba="container" data-barba-easy="parsehtml_<?= time() ?>">

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
            <div style="display:none;" class="content-body MainContent">
                <?php

                var_dump($Decoded);
                var_dump($RequestData);

                ?>


            </div>
        </div>
    </div>
    <!-- END: Content-->


</main>
<?php

require_once VDIR . '/console.footer.php';

?>