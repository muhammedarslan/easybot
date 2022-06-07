<?php

use Jenssegers\Date\Date;

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();

$Me = StaticFunctions::get_id();
$User = $db->query("SELECT * FROM users WHERE id = '{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
if (!$User) {
    $_SESSION['CheckSession'] = false;
    if (isset($_SESSION['CheckSession'])) :
        $Me = StaticFunctions::get_id();
        $RememberToken = isset($_COOKIE['RMB']) ? StaticFunctions::clear($_COOKIE['RMB']) : null;

        if ($RememberToken != null) :
            $delete = $db->exec("DELETE FROM remember_me WHERE user_id= '{$Me}' and remember_token = '{$RememberToken}' ");
            setcookie("RMB", 'null', time() + 604801, '/', DOMAIN, false, true);
        endif;
        session_destroy();
    endif;
    http_response_code(401);
    exit;
}

$TicketToken = StaticFunctions::post('ticketToken');

if ($TicketToken == '') {
    http_response_code(401);
    exit;
}

$InsertTicketID = $db->query("SELECT ticket_id,ticket_subject,ticket_folder FROM support_tickets WHERE ticket_token='{$TicketToken}' and user_id='{$Me}' ")->fetch(PDO::FETCH_ASSOC);

if (!$InsertTicketID) {
    http_response_code(401);
    exit;
}

$TicketInsertID = $InsertTicketID['ticket_id'];

$Message = StaticFunctions::post('reply_ticket_message');
$PageToken = StaticFunctions::post('page_token');

if (
    $Message == '' ||
    $PageToken == ''
) {
    echo StaticFunctions::JsonOutput([
        'status' => 'failed',
        'title' => 'Bir hata oluştu!',
        'message' => StaticFunctions::lang('Lütfen tüm alanları doldurunuz.')
    ]);
    exit;
}

if (mb_strlen($Message) < 10) {
    echo StaticFunctions::JsonOutput([
        'status' => 'failed',
        'title' => 'Bir hata oluştu!',
        'message' => StaticFunctions::lang('Mesajının bu kadar kısa olduğuna emin misin?')
    ]);
    exit;
}

$TicketFiles = [];
$FilesArray = [];
$FindTicketFiles = $db->query("SELECT id,file_real_name,file_token FROM uploaded_files WHERE user_id='{$Me}' and upload_channel='support_ticket' and file_upload_token='{$PageToken}' and status=1 ", PDO::FETCH_ASSOC);
if ($FindTicketFiles->rowCount()) {
    foreach ($FindTicketFiles as $row) {
        array_push($TicketFiles, $row['id']);
        array_push($FilesArray, [
            'fileName' => $row['file_real_name'],
            'fileToken' => $row['file_token']
        ]);
    }
}

$TicketRandomToken = StaticFunctions::random(32);

$InsertTicket = $db->prepare("INSERT INTO support_tickets SET
user_id = ?,
ticket_id = ?,
ticket_folder = ?,
ticket_priority = ?,
ticket_subject = ?,
ticket_message_user = ?,
ticket_message = ?,
ticket_files = ?,
ticket_time = ?,
ticket_token = ?");
$insert = $InsertTicket->execute(array(
    $Me, $TicketInsertID, 2, $InsertTicketID['ticket_folder'], $InsertTicketID['ticket_subject'], $Me, $Message, json_encode($TicketFiles), time(), $TicketRandomToken
));

Date::setLocale(mb_strtolower(LANG));

echo StaticFunctions::ApiJson([
    'status' => 'success',
    'title' => StaticFunctions::lang('Başarıyla oluşturuldu!'),
    'message' => StaticFunctions::lang('Cevabın destek talebine başarıyla eklendi. Talebin yanıtlandığı anda sana haber vereceğiz.'),
    'ticketToken' => $TicketRandomToken,
    'item' => [
        'messageUser' => [
            'realName' => $User['real_name'],
            'avatar' => $User['avatar'],
            'role' => StaticFunctions::lang('Kullanıcı')
        ],
        'ticketMessage' => StaticFunctions::say(str_replace("\n", '<p>', $Message)),
        'ticketTime' => [
            'time' => Date::now()->format('A h:i'),
            'date' => Date::now()->format('j F Y l')
        ],
        'ticketFiles' => $FilesArray
    ]
]);