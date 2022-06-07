<div class="sidenav-overlay"></div>
<div class="drag-target"></div>

<?php
if ($PhoneVerify['phone_verify'] != 1) :
?>
<div class="modal-size-lg mr-1 mb-1 d-inline-block">
    <!-- Modal -->
    <div class="modal fade text-left" id="ValidateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel17"><?= StaticFunctions::lang('Hesabınızı doğrulayın') ?>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="SccGif" width="150px" src="/assets/media/phone_verify.png" alt="">
                    <hr>
                    <?php

                        $UserNameE = explode(' ', $_SESSION['UserSession']->real_name);
                        $UserName = $UserNameE[0];
                        ?>
                    <h5 id="SccText"><?= StaticFunctions::lang('Selam {0}, Easybot\'un tüm fonksiyonlarına erişebilmen için telefon numaranı doğrulaman gerekli. Numaranı doğrulamak için telefonuna bir kod göndereceğiz.', [
                                                $UserName
                                            ]) ?></h5>
                    <br>

                    <form action="javascript:;" id="ValidateAccountForm" method="post">
                        <div id="AccountValidateArea" class="form-group">
                            <input type="tel" id="phone" name="phn" class="form-control nm_inp">
                        </div>

                    </form>

                    <div style="display: none;" id="PinVerifyArea" class="form-group pin_vtfy">
                        <span class="ph_t"><a href="javascript:;" id="PhoneN"></a>
                            <?= StaticFunctions::lang('numaralı telefonuna pin kodun başarıyla gönderildi.') ?>
                            <a onclick="WrongNumber();"
                                href="javascript:;"><?= StaticFunctions::lang('Numara yanlış mı?') ?></a></span>

                        <br>
                        <input type="text" id="pincode-input1">
                        <br>
                        <a onclick="RePin();"
                            href="javascript:;"><?= StaticFunctions::lang('Pin kodunu almadın mı?') ?></a>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" id="ValidateButton" class="btn btn-primary"
                        data-dismiss="modal"><?= StaticFunctions::lang('Doğrula') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
endif;
?>


<div class="modal-size-lg mr-1 mb-1 d-inline-block">
    <!-- Modal -->
    <div class="modal fade text-left" id="PinVerifyModal" tabindex="-1" role="dialog"
        aria-labelledby="PinVerifyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="PinVerifyModalLabel">
                        <?= StaticFunctions::lang('Bir dakika, önce güvenlik') ?>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="PinVerifySuccessGif" width="150px" src="/assets/media/phone_verify.png" alt="">
                    <hr>
                    <?php

                    $UserNameE = explode(' ', $_SESSION['UserSession']->real_name);
                    $UserName = $UserNameE[0];
                    ?>
                    <h5 id="PinVerifySuccessText"><?= StaticFunctions::lang('Selam {0}, yapmak istediğin işlemi tamamlamadan önce senin ve hesabının güvenliği için işlemi yapanın sen olduğunu doğrulamak istiyoruz.', [
                                                        $UserName
                                                    ]) ?></h5>
                    <br>

                    <div style="display: none;" id="PinVerifyFooterAreaLoading" class="form-group pin_vtfy">
                        <img width="80px" style="margin: 0 auto;" src="/assets/media/loading.gif" alt="">
                    </div>

                    <div id="PinVerifyFooterArea" class="form-group pin_vtfy">
                        <span class="ph_t"><a href="javascript:;" id="PinSendedInfo"></a>
                            <?= ' ' . StaticFunctions::lang('işlemi doğrulaman için pin kodun gönderildi. Lütfen kodu kontrol et ve işlemi onayla.') ?>
                        </span>

                        <br>
                        <input type="text" id="pincode-verify">
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>


<!-- BEGIN: Footer-->
<footer class="footer footer-static footer-light">
    <p class="clearfix blue-grey lighten-2 mb-0"><span class="float-md-left d-block d-md-inline-block mt-25"><a
                class="text-bold-800 grey darken-2" href="/console/dashboard">EasyBot</a>&copy; <?= date('Y') ?> |
            <?= StaticFunctions::lang('Tüm hakları saklıdır.') ?></span><span
            class="float-md-right d-none d-md-block"><?= StaticFunctions::lang('Msa tarafından {0} ile kodlandı.', ['<i style="margin-left:0px;" class="feather icon-heart pink"></i>']) ?></span>
        <button class="btn btn-primary btn-icon scroll-top" type="button"><i class="feather icon-arrow-up"></i></button>
    </p>
</footer>
<!-- END: Footer-->

<script>
const InternalAjaxHost = '<?= PROTOCOL . DOMAIN . PATH ?>';
let AppMode = '<?= AppMode ?>';
const IsTour = false;
</script>
<!-- BEGIN: Vendor JS-->
<script src="/assets/console/app-assets/vendors/js/vendors.min.js?v=<?= Version ?>"></script>
<script src="/assets/console/app-assets/vendors/js/extensions/shepherd.min.js?v=<?= Version ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/@barba/core"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js?v=<?= Version ?>"
    integrity="sha512-3j3VU6WC5rPQB4Ld1jnLV7Kd5xr+cq9avvhwqzbH/taCRNURoeEpoPBK9pDyeukwSxwRPJ8fDgvYXd6SkaZ2TA=="
    crossorigin="anonymous"></script>
<script src="/assets/console/app-assets/js/core/app-menu.js?v=<?= Version ?>"></script>
<script src="/assets/console/app-assets/js/core/app.js?v=<?= Version ?>"></script>
<script src="/assets/console/app-assets/vendors/js/extensions/toastr.min.js?v=<?= Version ?>"></script>
<script src="/assets/console/app-assets/vendors/js/extensions/sweetalert2.all.min.js?v=<?= Version ?>"></script>
<script src="/assets/console/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js?v=<?= Version ?>"></script>
<script src="//code.tidio.co/grr9yorqnt7jfqhyocx7ro14kcwcpxpt.js?v=<?= Version ?>" async></script>

<script src="/assets/console/app-assets/vendors/js/tables/datatable/datatables.min.js?v=<?= Version ?>"></script>
<script src="/assets/console/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js?v=<?= Version ?>">
</script>
<script src="/assets/console/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js?v=<?= Version ?>">
</script>
<script src="/assets/console/app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js?v=<?= Version ?>">
</script>
<script src="/assets/console/app-assets/vendors/js/tables/datatable/dataTables.select.min.js?v=<?= Version ?>">
</script>
<script src="/assets/console/app-assets/datatable/dataTables.responsive.min.js?v=<?= Version ?>"></script>
<script
    src="https://cdn.jsdelivr.net/npm/sf-bootstrap-pincode-input@1.5.0/js/bootstrap-pincode-input.min.js?v=<?= Version ?>">
</script>

<?php
if ($PhoneVerify['phone_verify'] != 1) {
    echo '<script src="/assets/console/app-assets/js/intlTelInput.js?v=' . Version . '"></script>' . "\n";
    echo '<script src="/assets/console/app-assets/js/jquery.inputmask.js?v=' . Version . '"></script>' . "\n";
    echo '<script src="/assets/console/app-assets/js/scripts/phone.validate.js?v=' . Version . '"></script>';
} else {
    echo '<script src="/assets/console/app-assets/js/jquery.inputmask.js?v=' . Version . '"></script>' . "\n";
}
?>

<script src="/assets/console/app-assets/js/scripts/components.js?v=<?= Version ?>"></script>
<script src="/assets/console/app-assets/js/scripts/topbar.min.js?v=<?= Version ?>"></script>
<script src="/assets/console/app-assets/js/scripts/dont-go.js?v=<?= Version ?>"></script>
<script src="/assets/console/app-assets/js/core/core.js?v=<?= Version ?>"></script>
<script src="/assets/console/app-assets/js/core/table.js?v=<?= Version ?>"></script>

<!-- END: Theme JS-->


</body>
<!-- END: Body-->

</html>