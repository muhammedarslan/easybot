<?php

use Jenssegers\Date\Date;

StaticFunctions::ajax_form('private');
$Me = StaticFunctions::get_id();

$ResponseArray = [
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

Date::setLocale(LANG);

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