<?php

$PageCss = [];

$PageJs = [];

$PageBreadCrumb = [
    'isActive' => true,
    'list' => [
        '/console/dashboard' => StaticFunctions::lang('Anasayfa'),
        'active'             => StaticFunctions::lang('Hatalı Girişler')
    ]

];

// Add table to page.
$TableTitle = 'Hatalı giriş denemeleri';
$TableID    = 'FailedLogin';
require_once VDIR . '/table.' . $TableID . '.php';
// End table.

require_once VDIR . '/console.header.php';
require_once VDIR . '/console.menu.php';

$UpdateFailedLogin = $db->prepare("UPDATE users SET
failed_login = :f
WHERE id = :e");
$update = $UpdateFailedLogin->execute(array(
    "f" => 0,
    "e" => StaticFunctions::get_id()
));

?>


<main data-barba="container" data-barba-easy="failedlogin">

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
            <div style="display:none;" class="content-body MainContent">
                <!-- Data list view starts -->
                <section id="data-list-view" class="data-list-view-header">
                    <!-- DataTable starts -->
                    <div class="alert alert-warning mb-2" role="alert">
                        <?= StaticFunctions::lang('Hesabına yapılan hatalı giriş denemelerini bu sayfadan görebilirsin. Güvenliğin için güçlü bir parola belirlemen gerektiğini hatırlatmak isterim.') ?>
                    </div>
                    <div class="alert alert-success mb-2" role="alert">
                        <?= StaticFunctions::lang('Giriş sayfamız recaptcha ile spam ve botlardan korunmaktadır. 3 adet hatalı giriş denemesi yapıldığında güvenlik doğrulaması otomatik olarak aktif olmaktadır.') ?>
                    </div>
                    <div class="alert alert-dark mb-2" role="alert">
                        <?php

                        switch ($_SESSION['UserSession']->last_type) {
                            case 'Login':
                                $LastType = StaticFunctions::lang('Giriş sayfası / Otomatik giriş');
                                break;
                            default:
                                $LastType = StaticFunctions::clear($_SESSION['UserSession']->last_type);
                                break;
                        }

                        echo StaticFunctions::lang('Son başarılı girişini <strong>{0}</strong> tarihinde <strong>{1}</strong> adresinden <strong>{2}</strong> yöntemi ile yaptın.', [date('d-m-Y H:i:s', $_SESSION['UserSession']->last_login), $_SESSION['UserSession']->last_ip, $LastType]);
                        echo '<a style="    margin-left: 3px;
    font-weight: 600;
    font-style: normal;" href="javascript:;" onclick="tidioChatApi.open();">' . StaticFunctions::lang('Ben Değildim?') . '</a>';
                        ?>
                    </div>
                    <div class="table-responsive">
                        <?= $Table->PageContent() ?>
                    </div>
                    <!-- DataTable ends -->
                </section>
                <!-- Data list view end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->


</main>
<?php

require_once VDIR . '/console.footer.php';

?>