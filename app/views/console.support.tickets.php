<?php

use DeviceDetector\Cache\StaticCache;

$PageCss = [
    '/assets/console/app-assets/css/pages/app-email.css'
];

$PageJs = [
    '/assets/console/app-assets/js/scripts/pages/app-email.js'
];

$PageBreadCrumb = [
    'isActive' => true,
    'list' => [
        '/console/dashboard' => StaticFunctions::lang('Anasayfa'),
        'active'             => StaticFunctions::lang('Destek Talepleri')
    ]

];

$PageTokenUpload = StaticFunctions::random(64);
$PageTokenUploadReply = StaticFunctions::random(64);

require_once VDIR . '/console.header.php';
require_once VDIR . '/console.menu.php';


?>


<main class="email-application" data-barba="container" data-barba-easy="supporttickets">


    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <?php echo StaticFunctions::ConsoleBreadCrumb($PageBreadCrumb); ?>
            <div class="content-body">

                <section id="statistics-card">

                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 data-stat-key="folder1" class="text-bold-700 mb-0"><img
                                                src="/assets/media/mini_loading.gif" width="40px" alt=""></h2>
                                        <p><?= Staticfunctions::lang('Çözümlenmiş Talep') ?></p>
                                    </div>
                                    <div class="avatar bg-rgba-primary p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-check text-primary font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 data-stat-key="folder2" class="text-bold-700 mb-0"><img
                                                src="/assets/media/mini_loading.gif" width="40px" alt=""></h2>
                                        <p><?= Staticfunctions::lang('Yanıt Bekleyen') ?></p>
                                    </div>
                                    <div class="avatar bg-rgba-success p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="fa fa-paper-plane-o text-success font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 data-stat-key="folder3" class="text-bold-700 mb-0"><img
                                                src="/assets/media/mini_loading.gif" width="40px" alt=""></h2>
                                        <p><?= Staticfunctions::lang('İşlem Bekleyen') ?></p>
                                    </div>
                                    <div class="avatar bg-rgba-warning p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-edit-2 text-warning font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 data-stat-key="folder4" class="text-bold-700 mb-0"><img
                                                src="/assets/media/mini_loading.gif" width="40px" alt=""></h2>
                                        <p><?= Staticfunctions::lang('Ortalama Yanıt Süresi') ?></p>
                                    </div>
                                    <div class="avatar bg-rgba-info p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-clock text-info font-medium-5"></i>
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

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div style="margin-top: 0px;" class="content-area-wrapper">
            <div class="sidebar-left">
                <div class="sidebar">
                    <div class="sidebar-content email-app-sidebar d-flex">
                        <span class="sidebar-close-icon">
                            <i class="feather icon-x"></i>
                        </span>
                        <div class="email-app-menu">
                            <div class="form-group form-group-compose text-center compose-btn">
                                <button type="button" class="btn btn-primary btn-block my-2" onclick="NewTicket();"><i
                                        class="feather icon-edit"
                                        style="margin-right: 5px;"></i><?= StaticFunctions::lang('Yeni Talep Oluştur') ?></button>
                            </div>
                            <div class="sidebar-menu-list">
                                <div class="list-group list-group-messages font-medium-1">
                                    <a data-folder-id="1" href="javascript:;" onclick="GetTickets(1);"
                                        class="list-group-item list-group-item-action border-0 pt-0 active">
                                        <i class="font-medium-5 feather icon-mail mr-50"></i>
                                        <?= Staticfunctions::lang('Son Taleplerim') ?>
                                        <span data-stat-key="folderPill1"
                                            class="badge badge-primary badge-pill float-right"></span>
                                    </a>
                                    <a data-folder-id="3" href="javascript:;" onclick="GetTickets(3);"
                                        class="list-group-item list-group-item-action border-0"><i
                                            class="font-medium-5 feather icon-edit-2 mr-50"></i>
                                        <?= Staticfunctions::lang('İşlem Bekleyenler') ?>
                                        <span data-stat-key="folderPill3"
                                            class="badge badge-warning badge-pill float-right"></span>
                                    </a>
                                    <a data-folder-id="2" href="javascript:;" onclick="GetTickets(2);"
                                        class="list-group-item list-group-item-action border-0"><i
                                            class="font-medium-5 fa fa-paper-plane-o mr-50"></i>
                                        <?= Staticfunctions::lang('Yanıt Bekleyenler') ?>
                                        <span data-stat-key="folderPill2"
                                            class="badge badge-success badge-pill float-right"></span>
                                    </a>

                                    <a data-folder-id="4" href="javascript:;" onclick="GetTickets(4);"
                                        class="list-group-item list-group-item-action border-0"><i
                                            class="font-medium-5 feather icon-star mr-50"></i><?= Staticfunctions::lang('Favorilerim') ?></a>
                                </div>
                                <hr>
                                <h5 class="my-2 pt-25"><?= Staticfunctions::lang('Destek Kanalları') ?></h5>
                                <div class="list-group list-group-labels font-medium-1">
                                    <a title="<?= StaticFunctions::lang('Aktif') ?>" href="javascript:;"
                                        onclick="tidioChatApi.open();"
                                        class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span
                                            class="bullet bullet-success mr-1"></span>
                                        <?= Staticfunctions::lang('Canlı Destek') ?></a>
                                    <a title="<?= StaticFunctions::lang('Aktif') ?>" href="javascript:;"
                                        class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span
                                            class="bullet bullet-success mr-1"></span>
                                        <?= Staticfunctions::lang('Destek Talepleri') ?></a>
                                    <a title="<?= StaticFunctions::lang('Aktif') ?>" href="/redirect/whatsapp"
                                        target="_blank"
                                        class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span
                                            class="bullet bullet-success mr-1"></span>
                                        <?= Staticfunctions::lang('Whatsapp Hattı') ?></a>
                                    <a title="<?= StaticFunctions::lang('Aktif') ?>" href="/community" target="_blank"
                                        class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span
                                            class="bullet bullet-success mr-1"></span>
                                        <?= Staticfunctions::lang('Topluluk Desteği') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div class="content-right">
                <div class="content-wrapper">
                    <div class="content-header row">
                    </div>
                    <div style="display:none;" class="content-body MainContent">
                        <div class="app-content-overlay"></div>
                        <div class="email-app-area">
                            <!-- Email list Area -->
                            <div class="email-app-list-wrapper">
                                <div class="email-app-list">
                                    <div class="app-fixed-search">
                                        <div class="sidebar-toggle d-block d-lg-none"><i class="feather icon-menu"></i>
                                        </div>
                                        <fieldset class="form-group position-relative has-icon-left m-0">
                                            <input type="text" class="form-control" id="email-search"
                                                placeholder="<?= Staticfunctions::lang('Destek talebi ara...') ?>">
                                            <div class="form-control-position">
                                                <i class="feather icon-search"></i>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="app-action">
                                        <div class="action-left">
                                            <div class="vs-checkbox-con selectAll">
                                                <input type="checkbox">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-minus"></i>
                                                    </span>
                                                </span>
                                                <span><?= Staticfunctions::lang('Hepsini Seç') ?></span>
                                            </div>
                                        </div>
                                        <div class="action-right">
                                            <ul class="list-inline m-0">
                                                <li title="<?= StaticFunctions::lang('Yeni talep oluştur') ?>"
                                                    onclick="NewTicket();" class="list-inline-item"><span
                                                        class="action-icon"><i
                                                            class="feather icon-plus-circle"></i></span>
                                                </li>
                                                <li class="list-inline-item">
                                                    <div class="dropdown">
                                                        <a href="#" class="dropdown-toggle" id="folder"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            <i class="feather icon-tag"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right"
                                                            aria-labelledby="folder">
                                                            <a onclick="MarkReaded();"
                                                                class="dropdown-item d-flex font-medium-1"
                                                                href="javascript:;"><i
                                                                    class="font-medium-3 feather icon-book mr-50"></i>
                                                                <?= Staticfunctions::lang('Okundu olarak işaretle') ?></a>
                                                            <a onclick="MarkUnReaded();"
                                                                class="dropdown-item d-flex font-medium-1"
                                                                href="javascript:;"><i
                                                                    class="font-medium-3 feather icon-book-open mr-50"></i>
                                                                <?= Staticfunctions::lang('Okunmadı olarak işaretle') ?></a>
                                                            <a onclick="MarkSolved();"
                                                                class="dropdown-item d-flex font-medium-1"
                                                                href="javascript:;"><i
                                                                    class="font-medium-3 feather icon-check mr-50"></i>
                                                                <?= Staticfunctions::lang('Çözüldü olarak işaretle') ?></a>
                                                        </div>
                                                    </div>
                                                </li>

                                                <li onclick="RefreshTickets();"
                                                    title="<?= StaticFunctions::lang('Yenile') ?>"
                                                    class="list-inline-item"><span class="action-icon"><i
                                                            class="feather icon-refresh-cw"></i></span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="email-user-list list-group">
                                        <ul id="TicketsArea" class="users-list-wrapper media-list">

                                            <center> <img style="margin-top: 40px;" src="/assets/media/lg.gif" />
                                            </center>

                                        </ul>
                                        <div class="no-results d-none">
                                            <h5><?= Staticfunctions::lang('Destek Talebi Bulunamadı.') ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/ Email list Area -->
                            <!-- Detailed Email View -->
                            <div style="display: none;" id="TicketDetail" class="email-app-details">
                                <div class="email-detail-header">
                                    <div class="email-header-left d-flex align-items-center mb-1">
                                        <span class="go-back mr-1"><i
                                                class="feather icon-arrow-left font-medium-4"></i></span>
                                        <h3 data-ticket-detail="subject"></h3>
                                    </div>
                                    <div class="email-header-right mb-1 ml-2 pl-1">
                                        <ul class="list-inline m-0">
                                            <li class="list-inline-item"><span data-ticket-detail="starred"
                                                    class="action-icon favorite"><i onclick="StarTicketDetail();"
                                                        id="StarTicketDetailToken" data-ticket-detail-token=""
                                                        class="feather icon-star font-medium-5"></i></span></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="email-scroll-area">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="email-label ml-2 my-2 pl-1">
                                                <span data-ticket-detail="folder"
                                                    class="mr-1 bullet bullet-primary bullet-sm"></span><small
                                                    data-ticket-detail="foldertext" class="mail-label"></small>
                                            </div>
                                        </div>
                                    </div>


                                    <div id="TicketDetailMessagesList">

                                    </div>


                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card px-1">

                                                <div class="card-body mail-message-wrapper pt-2 mb-0">
                                                    <div style="margin-bottom: 20px;" class="mail-message">
                                                        <form onsubmit="ReplyTicket();" id="ReplyTicketForm"
                                                            method="post" action="javascript:;">


                                                            <input type="text" value="hidden" name="hidden" hidden="">
                                                            <input type="text" value="" id="TicketDetailAddToken"
                                                                name="ticketToken" hidden="">
                                                            <input type="text" value="<?= $PageTokenUploadReply ?>"
                                                                name="page_token" hidden="">


                                                            <textarea required=""
                                                                data-validation-required-message="Bu alan zorunludur."
                                                                name="reply_ticket_message"
                                                                placeholder="Talebinize ek mesajınızı buraya yazabilirsiniz. Talebini çözmemizde yardımcı olabilecek dosyalar var ise alt tarafta bulunan Dosya Ekle bölümünden ek ekleyebilirsin."
                                                                class="form-control" style="width: 100%;"
                                                                id="reply_tc_mess" cols="30" rows="4"></textarea>


                                                        </form>
                                                    </div>

                                                    <div class="row">
                                                        <div style="margin-top: 11px;" class="col-9">
                                                            <a id="TicketReplyUploadFileLink"
                                                                onclick="$('#InputTicketFilesReply').click();"
                                                                style="color:#626262;" href="javascript:;">
                                                                <i
                                                                    class="feather icon-paperclip font-medium-5 mr-50"></i>
                                                                <span><?= Staticfunctions::lang('Dosya Ekle') ?></span>
                                                                <small><?= Staticfunctions::lang('(Maksimum dosya başı 2 Mb, 3 adet dosya)') ?></small>
                                                            </a>
                                                            <img style="margin-top: 1px; width: 50px;display:none;"
                                                                src="/assets/media/mini_loading.gif" alt=""
                                                                id="TicketReplyUploadFileLoading" srcset="">
                                                        </div>
                                                        <div class="col-3">

                                                            <button onclick="ReplyTicket(); return false;"
                                                                id="ReplyTcketButton" type="button"
                                                                class="btn btn-primary btn-block waves-effect waves-light"><i
                                                                    class="feather icon-edit"
                                                                    style="margin-right: 5px;"></i><?= Staticfunctions::lang('Cevabımı Gönder') ?></button>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div id="MailReplyFiles" class="mail-files py-2"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!--/ Detailed Email View -->

                            <!-- New Ticket View -->
                            <div style="display: none;" id="NewTicket" class="email-app-details">
                                <div class="email-detail-header">
                                    <div class="email-header-left d-flex align-items-center mb-1">
                                        <span class="go-back mr-1"><i
                                                class="feather icon-arrow-left font-medium-4"></i></span>
                                        <h3><?= StaticFunctions::lang('Yeni Destek Talebi') ?></h3>
                                    </div>
                                    <div class="email-header-right mb-1 ml-2 pl-1">
                                        <ul class="list-inline m-0">
                                            <li onclick="DeleteNewTicket();" class="list-inline-item"><span
                                                    class="action-icon"><i
                                                        class="feather icon-trash font-medium-5"></i></span></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="email-scroll-area">
                                    <div style="margin-top:20px;" class="row">
                                        <div class="col-12">
                                            <div class="card px-1">
                                                <div class="card-header email-detail-head ml-75">
                                                    <div
                                                        class="user-details d-flex justify-content-between align-items-center flex-wrap">
                                                        <div class="avatar mr-75">
                                                            <img src="/assets/media/img_loading.gif"
                                                                id="NewTicketAvatar" alt="avtar img holder" width="61"
                                                                height="61">
                                                        </div>
                                                        <div class="mail-items">
                                                            <h4 class="list-group-item-heading mb-0"
                                                                id="UserRealNameNewTicket"></h4>
                                                            <div class="email-info-dropup dropdown">
                                                                <span class="font-small-3">
                                                                    <?= $_SESSION['UserSession']->email ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mail-meta-item">
                                                        <div id="NewTicketDate1" class="mail-time mb-1"></div>
                                                        <div id="NewTicketDate2" class="mail-date"></div>
                                                    </div>
                                                </div>
                                                <div class="card-body mail-message-wrapper pt-2 mb-0">
                                                    <div style="margin-bottom: 20px;" class="mail-message">

                                                        <form onsubmit="SendNewTicket();" id="NewTicketForm"
                                                            method="post" action="javascript:;">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-1">
                                                                    <fieldset class="form-group">
                                                                        <label
                                                                            for="helpInputTop"><?= StaticFunctions::lang('Talebinin konusu:') ?></label>
                                                                        <small
                                                                            class="text-muted"><?= StaticFunctions::lang('örn:') ?><i><?= StaticFunctions::lang('Botuma nasıl yeni bir özellik ekleyebilirim?') ?></i></small>
                                                                        <input required
                                                                            data-validation-required-message="<?= StaticFunctions::lang('Bu alan zorunludur.') ?>"
                                                                            name="new_ticket_subject"
                                                                            placeholder="<?= StaticFunctions::lang('Sana hangi konuda yardımcı olabiliriz?') ?>"
                                                                            type="text" class="form-control"
                                                                            id="helpInputTop">
                                                                    </fieldset>
                                                                </div>

                                                                <div class="col-md-6 mb-1">
                                                                    <fieldset class="form-group">
                                                                        <label
                                                                            for="helpInputTop"><?= StaticFunctions::lang('Öncelik seviyesi:') ?></label>
                                                                        <small
                                                                            class="text-muted"><i><?= StaticFunctions::lang('Gün içi saatlerinde canlı destekten anında yanıt veriyoruz.') ?></i></small>
                                                                        <select name="new_ticket_label" id=""
                                                                            class="form-control">
                                                                            <option value="1">
                                                                                <?= StaticFunctions::lang('Düşük öncelikli') ?>
                                                                            </option>
                                                                            <option value="2" selected>
                                                                                <?= StaticFunctions::lang('Orta öncelikli') ?>
                                                                            </option>
                                                                            <option value="3">
                                                                                <?= StaticFunctions::lang('Yüksek öncelikli') ?>
                                                                            </option>
                                                                        </select>
                                                                    </fieldset>
                                                                </div>
                                                            </div>

                                                            <input type="text" value="hidden" name="hidden" hidden>
                                                            <input type="text" value="<?= $PageTokenUpload ?>"
                                                                name="page_token" hidden>


                                                            <textarea required
                                                                data-validation-required-message="<?= StaticFunctions::lang('Bu alan zorunludur.') ?>"
                                                                name="new_ticket_message"
                                                                placeholder="<?= StaticFunctions::lang('Talebini içeren mesajını detaylıca buraya yazabilirsin. Talebini çözmemizde yardımcı olabilecek dosyalar var ise alt tarafta bulunan Dosya Ekle bölümünden ek ekleyebilirsin.') ?>"
                                                                class="form-control" style="width: 100%;"
                                                                name="new_ticket_message" id="new_tc_mess" cols="30"
                                                                rows="10"></textarea>


                                                    </div>



                                                    <div class="row">
                                                        <div style="margin-top: 11px;" class="col-9">
                                                            <a id="TicketUploadFileLink"
                                                                onclick="$('#InputTicketFiles').click();"
                                                                style="color:#626262;" href="javascript:;">
                                                                <i
                                                                    class="feather icon-paperclip font-medium-5 mr-50"></i>
                                                                <span><?= StaticFunctions::lang('Dosya Ekle') ?></span>
                                                                <small><?= StaticFunctions::lang('(Maksimum dosya başı 2 Mb, 3 adet dosya)') ?></small>
                                                            </a>
                                                            <img style="margin-top: 1px; width: 50px;display:none;"
                                                                src="/assets/media/mini_loading.gif" alt=""
                                                                id="TicketUploadFileLoading" srcset="">
                                                        </div>
                                                        <div class="col-3">

                                                            <button onclick="SendNewTicket(); return false;"
                                                                id="CreateTcketButton" type="button"
                                                                class="btn btn-primary btn-block waves-effect waves-light"><i
                                                                    class="feather icon-edit"
                                                                    style="margin-right: 5px;"></i><?= StaticFunctions::lang('Talebimi Oluştur') ?></button>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div id="MailFiles" class="mail-files py-2">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <!--/ New Ticket View -->
                        </div>
                        <?php

                        require_once CDIR . '/class.upload.php';
                        $UploadClass = new Upload();

                        echo $UploadClass->UploadForm('TicketFiles', 'support_ticket', 'UploadTicketFiles', '', $PageTokenUpload);
                        echo $UploadClass->UploadForm('TicketFilesReply', 'support_ticket', 'UploadReplyTicketFiles', '', $PageTokenUploadReply);

                        ?>
                        <span id="MaxFileCountText"
                            style="display: none;"><?= StaticFunctions::lang('En fazla 3 dosya seçebilirsiniz.') ?></span>

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