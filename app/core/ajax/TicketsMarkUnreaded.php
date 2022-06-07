<?php

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

$Tickets = StaticFunctions::post('tickets');

if (count($Tickets) > 30) {
    http_response_code(401);
    exit;
}

foreach ($Tickets as $key => $value) {
    $CheckTicket = $db->query("SELECT ticket_id FROM support_tickets WHERE ticket_token = '{$value}' and is_readed=1 ")->fetch(PDO::FETCH_ASSOC);
    if ($CheckTicket) {
        $UpdateTicket = $db->prepare("UPDATE support_tickets SET
            is_readed = :newstar
            WHERE ticket_id = :tid");
        $update = $UpdateTicket->execute(array(
            "newstar" => 0,
            "tid" => $CheckTicket['ticket_id']
        ));
    }
}

echo StaticFunctions::ApiJson([
    'status' => 'success'
]);