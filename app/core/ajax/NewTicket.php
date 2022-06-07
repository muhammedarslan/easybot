<?php

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

$Subject = StaticFunctions::post('new_ticket_subject');
$Label = StaticFunctions::post('new_ticket_label');
$Message = StaticFunctions::post('new_ticket_message');
$PageToken = StaticFunctions::post('page_token');

if (
    $Subject == '' ||
    $Label == '' ||
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

if (mb_strlen($Subject) < 3) {
    echo StaticFunctions::JsonOutput([
        'status' => 'failed',
        'title' => 'Bir hata oluştu!',
        'message' => StaticFunctions::lang('Konunun bu kadar kısa olduğuna emin misin?')
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

$LabelArray = [1, 2, 3];
$PostLabel = 2;

if (in_array($Label, $LabelArray)) $PostLabel = $Label;

$TicketFiles = [];

$FindTicketFiles = $db->query("SELECT id FROM uploaded_files WHERE user_id='{$Me}' and upload_channel='support_ticket' and file_upload_token='{$PageToken}' and status=1 ", PDO::FETCH_ASSOC);
if ($FindTicketFiles->rowCount()) {
    foreach ($FindTicketFiles as $row) {
        array_push($TicketFiles, $row['id']);
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
    $Me, 0, 2, $PostLabel, $Subject, $Me, $Message, json_encode($TicketFiles), time(), $TicketRandomToken
));

$last_id = $db->lastInsertId();

$UpdateTicketID = $db->prepare("UPDATE support_tickets SET
ticket_id = :tid
WHERE id = :ids and ticket_id=0 ");
$update = $UpdateTicketID->execute(array(
    "tid" => $last_id,
    "ids" => $last_id
));

echo StaticFunctions::ApiJson([
    'status' => 'success',
    'title' => StaticFunctions::lang('Başarıyla oluşturuldu!'),
    'message' => StaticFunctions::lang('Destek talebin başarıyla oluşturuldu. Talebin yanıtlandığı anda sana haber vereceğiz.'),
    'ticketToken' => $TicketRandomToken
]);