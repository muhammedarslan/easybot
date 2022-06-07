<?php

use Jenssegers\Date\Date;

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

$TicketFolder = StaticFunctions::post('folder');
$FolderArray = [1, 2, 3, 4];

if (!in_array($TicketFolder, $FolderArray)) {
    http_response_code(401);
    exit;
}

$ResponseArray = [
    'itemsCount' => 0,
    'itemsFolder' => $TicketFolder,
    'items' => [],
    'stats' => [
        'solved' => 0,
        'waitingReply' => 0,
        'waitingProcess' => 0,
        'avgReply' => 0,
        'unReaded' => [
            'solved' => 0,
            'waitingReply' => 0,
            'waitingProcess' => 0
        ]
    ]
];

$Tickets = [];
if ($TicketFolder == 1) {
    $GetTickets = $db->query("SELECT *,MAX(id) FROM support_tickets WHERE user_id='{$Me}' GROUP by ticket_id ORDER BY id DESC LIMIT 30  ", PDO::FETCH_ASSOC);
    if ($GetTickets->rowCount()) {
        foreach ($GetTickets as $row) {
            if ($row['id'] == $row['MAX(id)']) {
                array_push($Tickets, $row);
            } else {
                $SingleTicket = $db->query("SELECT *,MAX(id) FROM support_tickets WHERE id = '{$row['MAX(id)']}'")->fetch(PDO::FETCH_ASSOC);
                array_push($Tickets, $SingleTicket);
            }
        }
    }
} else if ($TicketFolder == 4) {
    $GetTickets = $db->query("SELECT *,MAX(id) FROM support_tickets WHERE user_id='{$Me}' and is_starred='1' GROUP by ticket_id ORDER BY id DESC LIMIT 30  ", PDO::FETCH_ASSOC);
    if ($GetTickets->rowCount()) {
        foreach ($GetTickets as $row) {
            if ($row['id'] == $row['MAX(id)']) {
                array_push($Tickets, $row);
            } else {
                $SingleTicket = $db->query("SELECT *,MAX(id) FROM support_tickets WHERE id = '{$row['MAX(id)']}'")->fetch(PDO::FETCH_ASSOC);
                array_push($Tickets, $SingleTicket);
            }
        }
    }
} else {
    $GetTickets = $db->query("SELECT *,MAX(id) FROM support_tickets WHERE user_id='{$Me}' and ticket_folder='{$TicketFolder}' GROUP by ticket_id ORDER BY id DESC LIMIT 30  ", PDO::FETCH_ASSOC);
    if ($GetTickets->rowCount()) {
        foreach ($GetTickets as $row) {
            if ($row['id'] == $row['MAX(id)']) {
                array_push($Tickets, $row);
            } else {
                $SingleTicket = $db->query("SELECT *,MAX(id) FROM support_tickets WHERE id = '{$row['MAX(id)']}'")->fetch(PDO::FETCH_ASSOC);
                array_push($Tickets, $SingleTicket);
            }
        }
    }
}

Date::setLocale(LANG);

foreach ($Tickets as $key => $Ticket) {
    $isStarred = ($Ticket['is_starred'] == 1) ? true : false;
    $isReaded = ($Ticket['is_readed'] == 1) ? true : false;
    $Unix = (int) $Ticket['ticket_time'];
    $date = new Date(date('Y-m-d H:i:s', $Unix));

    $LastMessageUser = $db->query("SELECT real_name,avatar FROM users WHERE id='{$Ticket['ticket_message_user']}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
    if (!$LastMessageUser) {
        $LastMessageUser['real_name'] = StaticFunctions::lang('Geçersiz Kullanıcı.');
        $LastMessageUser['avatar'] = '/assets/media/avatars/default/B.png';
    }

    array_push($ResponseArray['items'], [
        'ticketFolder' => $Ticket['ticket_folder'],
        'ticketToken' => $Ticket['ticket_token'],
        'ticketSubject' => StaticFunctions::say($Ticket['ticket_subject']),
        'ticketShortMessage' => StaticFunctions::say(mb_substr($Ticket['ticket_message'], 0, 350)),
        'isStarred' => $isStarred,
        'isReaded' => $isReaded,
        'ticketTime' => $date->ago(),
        'user' => [
            'writtenBy' => StaticFunctions::say($LastMessageUser['real_name']) . ' ' . Staticfunctions::lang('yazdı.'),
            'avatar'   => $LastMessageUser['avatar']
        ]
    ]);
}

$ResponseArray['itemsCount'] = count($ResponseArray['items']);

$GetReplyStat = $db->query("SELECT stat_data FROM easybot_stats WHERE stat_name = 'support_ticket_reply_avg'")->fetch(PDO::FETCH_ASSOC);
$date = new Date((time() - $GetReplyStat['stat_data']));
$ExplodedStat = explode(' ', $date->ago(null, true));
array_pop($ExplodedStat);
$ResponseArray['stats']['avgReply'] = implode(' ', $ExplodedStat);

$StatData1Query = $db->query("SELECT id FROM support_tickets WHERE user_id='{$Me}' and ticket_folder=1 GROUP by ticket_id ", PDO::FETCH_ASSOC);
$Stat1 = $StatData1Query->rowCount();

$StatData2Query = $db->query("SELECT id FROM support_tickets WHERE user_id='{$Me}' and ticket_folder=2 GROUP by ticket_id ", PDO::FETCH_ASSOC);
$Stat2 = $StatData2Query->rowCount();

$StatData3Query = $db->query("SELECT id FROM support_tickets WHERE user_id='{$Me}' and ticket_folder=3 GROUP by ticket_id ", PDO::FETCH_ASSOC);
$Stat3 = $StatData3Query->rowCount();

$StatData1UnReadedQuery = $db->query("SELECT id FROM support_tickets WHERE user_id='{$Me}' and is_readed=0 GROUP by ticket_id ", PDO::FETCH_ASSOC);
$Stat1UnReaded = $StatData1UnReadedQuery->rowCount();

$StatData2UnReadedQuery = $db->query("SELECT id FROM support_tickets WHERE user_id='{$Me}' and ticket_folder=2 and is_readed=0 GROUP by ticket_id ", PDO::FETCH_ASSOC);
$Stat2UnReaded = $StatData2UnReadedQuery->rowCount();

$StatData3UnReadedQuery = $db->query("SELECT id FROM support_tickets WHERE user_id='{$Me}' and ticket_folder=3 and is_readed=0 GROUP by ticket_id ", PDO::FETCH_ASSOC);
$Stat3UnReaded = $StatData3UnReadedQuery->rowCount();

$ResponseArray['stats']['solved'] = $Stat1;
$ResponseArray['stats']['waitingReply'] = $Stat2;
$ResponseArray['stats']['waitingProcess'] = $Stat3;

$ResponseArray['stats']['unReaded']['solved'] = $Stat1UnReaded;
$ResponseArray['stats']['unReaded']['waitingReply'] = $Stat2UnReaded;
$ResponseArray['stats']['unReaded']['waitingProcess'] = $Stat3UnReaded;

echo StaticFunctions::ApiJson($ResponseArray);