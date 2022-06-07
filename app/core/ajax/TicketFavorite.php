<?php

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();
$TicketToken = StaticFunctions::post('ticketToken');

if ($TicketToken == '') {
    http_response_code(401);
    exit;
}

$GetTicket = $db->query("SELECT ticket_id,is_starred FROM support_tickets WHERE ticket_token = '{$TicketToken}'")->fetch(PDO::FETCH_ASSOC);
if (!$GetTicket) {
    http_response_code(401);
    exit;
}

$TicketID = $GetTicket['ticket_id'];

if ($GetTicket['is_starred'] == 1) {
    $NewStar = 0;
} else {
    $NewStar = 1;
}

$UpdateStar = $db->prepare("UPDATE support_tickets SET
is_starred = :newstar
WHERE ticket_id = :tid");
$update = $UpdateStar->execute(array(
    "newstar" => $NewStar,
    "tid" => $TicketID
));

$StarBool = ($NewStar == 1) ? true : false;

echo StaticFunctions::ApiJson([
    'isStarred' => $StarBool,
    'ticketToken' => $TicketToken
]);