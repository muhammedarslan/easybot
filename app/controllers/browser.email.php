<?php

StaticFunctions::new_session();

if (!isset($_SESSION['SessionValidateBrowseEmail']) || $_SESSION['SessionValidateBrowseEmail'] != 'validated') {
    require_once VDIR . '/validate.browse.email.php';
    exit;
}

$_SESSION['SessionValidateBrowseEmail'] = false;
unset($_SESSION['SessionValidateBrowseEmail']);

$CheckMail = $db->query("SELECT * FROM sended_mails WHERE email_token = '{$token}'")->fetch(PDO::FETCH_ASSOC);

if ($CheckMail) {
    $ViewContent = file_get_contents(VDIR . '/email_templates/' . $CheckMail['email_template'] . '.html');
    $ArrayAll = json_decode($CheckMail['email_variables']);
    foreach ($ArrayAll as $key => $value) {
        $ViewContent = str_replace('[[' . $key . ']]', $value, $ViewContent);
    }
    $ViewContent = str_replace('<a', '<a target="_blank" ', $ViewContent);
    echo $ViewContent;
} else {
    StaticFunctions::go_home();
}