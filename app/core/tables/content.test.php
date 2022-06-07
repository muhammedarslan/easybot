<?php

$DataArray = [];
$Me = StaticFunctions::get_id();


$GetData = $db->query("SELECT * FROM failed_login WHERE user_id='{$Me}' ORDER BY ID DESC LIMIT 100 ", PDO::FETCH_ASSOC);
if ($GetData->rowCount()) {
    foreach ($GetData as $row) {

        $BrowserDecode = json_decode($row['user_browser']);

        array_push($DataArray, [
            $row['id'],
            date('d-m-Y H:i:s', $row['system_time']),
            $row['user_ip'],
            $row['user_location'],
            $BrowserDecode->platform . ' / ' . $BrowserDecode->name . ' / ' . $BrowserDecode->version,
            'danger,' . StaticFunctions::lang('Engellendi')
        ]);
    }
}


$DataJson = json_encode([
    'data' => $DataArray
]);