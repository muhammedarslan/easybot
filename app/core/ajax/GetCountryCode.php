<?php

StaticFunctions::ajax_form('private');

$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'http://www.geoplugin.net/json.gp?ip=' . StaticFunctions::get_ip(), [
    'http_errors' => false
]);

if ($response->getStatusCode() == 200) {
    $Jsn = json_decode($response->getBody(), true);
    if (isset($Jsn['geoplugin_countryCode'])) {
        echo $Jsn['geoplugin_countryCode'];
        exit;
    }
}

echo 'TR';