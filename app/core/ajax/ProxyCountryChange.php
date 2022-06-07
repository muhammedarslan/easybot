<?php

StaticFunctions::ajax_form('private');

$Country = StaticFunctions::post('c');

if ($Country == '') {
    http_response_code(401);
    exit;
}

$List = file_get_contents(APP_DIR . '/storage/proxyCountryDatacenter.json');
$Decode = json_decode($List, true);


if (isset($Decode[$Country])) {
    echo StaticFunctions::ApiJson([
        'showCount' => true,
        'countryCode' => mb_strtoupper($Country),
        'proxyCount' => number_format($Decode[$Country], 0, ',', '.')
    ]);
} else {
    echo StaticFunctions::ApiJson([
        'showCount' => false
    ]);
}
