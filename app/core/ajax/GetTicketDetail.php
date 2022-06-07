<?php

use Jenssegers\Date\Date;

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

$TicketToken = StaticFunctions::post('ticket');

if ($TicketToken == '') {
    http_response_code(401);
    exit;
}

Date::setLocale(mb_strtolower(LANG));

$GetTicketID = $db->query("SELECT * FROM support_tickets WHERE ticket_token='{$TicketToken}' and user_id='{$Me}' ")->fetch(PDO::FETCH_ASSOC);

if (!$GetTicketID) {
    http_response_code(401);
    exit;
}

if ($GetTicketID['is_readed'] == 0) {
    $UpdateTicket = $db->prepare("UPDATE support_tickets SET
            is_readed = :newstar
            WHERE ticket_id = :tid");
    $update = $UpdateTicket->execute(array(
        "newstar" => 1,
        "tid" => $GetTicketID['ticket_id']
    ));
}

$TicketID = $GetTicketID['ticket_id'];

$TicketMessages = [];

$MessagesQuery = $db->query("SELECT users.id,users.user_type,support_tickets.id,support_tickets.ticket_id,users.real_name,users.avatar,support_tickets.ticket_message_user,support_tickets.ticket_message,support_tickets.ticket_files,support_tickets.ticket_time FROM
    support_tickets INNER JOIN users ON support_tickets.ticket_message_user=users.id WHERE support_tickets.ticket_id='{$TicketID}'
 ", PDO::FETCH_ASSOC);

if ($MessagesQuery->rowCount()) {
    foreach ($MessagesQuery as $key => $Message) {
        $UserType = ($Message['user_type'] == 'classic') ? StaticFunctions::lang('Kullanıcı') : StaticFunctions::lang('EasyBot Destek Görevlisi');
        $Unix = (int) $Message['ticket_time'];
        $date = new Date(date('Y-m-d H:i:s', $Unix));
        $JsFiles = json_decode($Message['ticket_files'], true);
        $FilesArray = [];
        if (count($JsFiles) > 0) {
            $FilesIds = implode(',', $JsFiles);
            $TicketFilesQuery = $db->query("SELECT file_real_name,file_token FROM uploaded_files WHERE id IN({$FilesIds}) and upload_channel='support_ticket'", PDO::FETCH_ASSOC);
            if ($TicketFilesQuery->rowCount()) {
                foreach ($TicketFilesQuery as $key => $File) {
                    array_push($FilesArray, [
                        'fileName' => $File['file_real_name'],
                        'fileToken' => $File['file_token']
                    ]);
                }
            }
        }

        array_push($TicketMessages, [
            'messageUser' => [
                'realName' => $Message['real_name'],
                'avatar' => $Message['avatar'],
                'role' => $UserType
            ],
            'ticketMessage' => StaticFunctions::say(str_replace("\n", '<p>', $Message['ticket_message'])),
            'ticketTime' => [
                'time' => $date->format('A h:i'),
                'date' => $date->format('j F Y l')
            ],
            'ticketFiles' => $FilesArray
        ]);
    }
}

$TicketFolders = [
    1 => [
        'label' => 'primary',
        'text' => StaticFunctions::lang('Çözümlenmiş Talep')
    ],
    2 => [
        'label' => 'success',
        'text' => StaticFunctions::lang('Yanıt Bekleyen Talep')
    ],
    3 => [
        'label' => 'warning',
        'text' => StaticFunctions::lang('İşlem Bekleyen Talep')
    ]
];

echo StaticFunctions::ApiJson([
    'status' => 'success',
    'ticketToken' => $GetTicketID['ticket_token'],
    'ticketSubject' => StaticFunctions::say($GetTicketID['ticket_subject']),
    'ticketFolder' => $TicketFolders[$GetTicketID['ticket_folder']],
    'isReaded' => ($GetTicketID['is_readed'] == 1) ? true : false,
    'isStarred' => ($GetTicketID['is_starred'] == 1) ? true : false,
    'ticketMessages' => $TicketMessages
]);