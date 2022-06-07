<?php


if (isset($_SERVER['HTTP_X_BARBA'])) {
    http_response_code(401);
    exit;
}

$SwalText1 = StaticFunctions::lang('Başarıyla çıkış yaptın!');
$SwalText2 = StaticFunctions::lang('İyi günler dileriz, görüşmek üzere.');

?>
<!DOCTYPE html>
<html lang="<?= LANG ?>">

<head>
    <title><?= StaticFunctions::lang('Çıkış yapılıyor..') ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?= PATH ?>assets/media/favicon.ico" />
    <meta http-equiv="refresh" content="4;URL=<?= PATH ?>login">

    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-borderless@1/borderless.css?v=2.0.1" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" type="text/css" href="/assets/l/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.0/animate.min.css"
        integrity="sha512-kb1CHTNhoLzinkElTgWn246D6pX22xj8jFNKsDmVwIQo+px7n1yjJUZraVuR/ou6Kmgea4vZXZeUDbqKtXkEMg=="
        crossorigin="anonymous" />

</head>

<body style="background-color: #666666;">


    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js?v=2.0.1"></script>

    <script src="/assets/l/js/jquery-3.5.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.js?v=2.0.1"
        integrity="sha256-7OUNnq6tbF4510dkZHCRccvQfRlV3lPpBTJEljINxao=" crossorigin="anonymous"></script>

    <script type="text/javascript">
    Swal.fire({
        type: 'success',
        title: '<?= $SwalText1 ?>',
        text: '<?= $SwalText2 ?>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        showCancelButton: false
    })
    setTimeout(() => {
        window.location = '<?= PATH ?>login';
    }, 3000);
    </script>



</body>

</html>

<?php
StaticFunctions::new_session();
if (isset($_SESSION['CheckSession'])) :
    $Me = StaticFunctions::get_id();
    $RememberToken = isset($_COOKIE['RMB']) ? StaticFunctions::clear($_COOKIE['RMB']) : null;

    if ($RememberToken != null) :
        $delete = $db->exec("DELETE FROM remember_me WHERE user_id= '{$Me}' and remember_token = '{$RememberToken}' ");
        setcookie("RMB", 'null', time() + 604801, '/', DOMAIN, false, true);
    endif;
    session_destroy();
endif;

exit();