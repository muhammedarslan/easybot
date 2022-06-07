<?php

use Rakit\Validation\Validator;
use Spatie\Url\Url;

StaticFunctions::ajax_form('validated');

$Me = StaticFunctions::get_id();
$Jwt = StaticFunctions::post('page_token');

$MyToken = $db->query("SELECT token from users WHERE id='{$Me}' and status=1")->fetch(PDO::FETCH_ASSOC);
if (!$MyToken) exit;

if ($Jwt == '') {
    http_response_code(401);
    exit;
}

try {
    $Decoded = \Firebase\JWT\JWT::decode($Jwt, StaticFunctions::JwtKey(), array('HS256'));
} catch (Exception $e) {
    http_response_code(401);
    exit;
}

if ($Decoded->owner != $MyToken['token']) {
    http_response_code(401);
    exit;
}

$TempBotToken = $Decoded->botToken;

$Address = StaticFunctions::post('876541_address');

if ($Address == '') {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'process' => 'failure',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('İstek adresi geçersiz.')
    ]);
    exit;
}

if (mb_strlen($Address) > 505) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'process' => 'failure',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('İstek adresi geçersiz.')
    ]);
    exit;
}


$ValidInputs = [
    '876541_type' => [
        'get', 'post', 'put', 'patch', 'delete', 'head'
    ],
    '876541_protocol' => [
        'http', 'https'
    ],
    '876541_browser' => [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17'
    ],
    '876541_data3_radio' => [
        '0', '1', '2', '3'
    ],
    '876541_browser_language' => array_keys(json_decode(file_get_contents(APP_DIR . '/storage/languageList.json'), true)),
    '876541_proxy_country' => array_keys(json_decode(file_get_contents(APP_DIR . '/storage/proxyCountryDatacenter.json'), true))
];

foreach ($ValidInputs as $key => $ValidValues) {
    if (!isset($_POST[$key]) || StaticFunctions::post($key) == '') {
        echo StaticFunctions::ApiJson([
            'status' => 'failed',
            'process' => 'failure',
            'title' => StaticFunctions::lang('Bir hata oluştu!'),
            'message' => StaticFunctions::lang('Lütfen tüm zorunlu alanları doldur.')
        ]);
        exit;
    }

    if (!in_array(StaticFunctions::post($key), $ValidValues)) {
        echo StaticFunctions::ApiJson([
            'status' => 'failed',
            'process' => 'failure',
            'title' => StaticFunctions::lang('Bir hata oluştu!'),
            'message' => StaticFunctions::lang('Giriş değeri geçersiz görünüyor: {0}', [
                str_replace('876541_', '', $key)
            ])
        ]);
        exit;
    }
}

$PostData['RequestType'] = StaticFunctions::post('876541_type');
$PostData['Protocol'] = StaticFunctions::post('876541_protocol');
$PostData['UrlParams'] = [];
$PostData['Headers'] = [];
$PostData['Cookies'] = [];

$UrlWithProtocol = $PostData['Protocol'] . '://' . $Address;

$validator = new Validator;

$validation = $validator->make(
    [
        'bot_url_address' => $UrlWithProtocol
    ],
    [
        'bot_url_address' => 'required|url:http,https'
    ]
);


$validation->validate();

if ($validation->fails()) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'process' => 'failure',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Veri almak istediğin url adresi geçersiz görünüyor.')
    ]);
    exit;
}

$url = Url::fromString($UrlWithProtocol);
$PostData['Url'] = [
    'scheme' => $url->getScheme(),
    'host' => $url->getHost(),
    'path' => $url->getPath(),
    'url' => $url->getScheme() . '://' . $url->getHost() . $url->getPath(),
    'params' => $url->getQuery(),
];

if (!checkdnsrr($url->getHost(), "A")) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'process' => 'failure',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('Alan adı geçersiz görünüyor: {0}', [
            $url->getHost()
        ])
    ]);
    exit;
}

require_once APP_DIR . '/controllers/class.bot.restrictions.php';
$CheckRestrictions = new EasyBotRestrictions();
$CheckRestrictions->setDb($db);
$CheckRestrictions->setUserID($Me);

if (!$CheckRestrictions->Domain($url->getHost())) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'process' => 'failure',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('EasyBot politikaları gereği bu alan adı kullanılamıyor: {0}', [
            $url->getHost()
        ])
    ]);
    exit;
}

if (!$CheckRestrictions->Path($url->getPath())) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'process' => 'failure',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('EasyBot politikaları gereği bu dizin kullanılamıyor: {0}', [
            $url->getPath()
        ])
    ]);
    exit;
}

if (!$CheckRestrictions->Parametres($url->getQuery())) {
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'process' => 'failure',
        'title' => StaticFunctions::lang('Bir hata oluştu!'),
        'message' => StaticFunctions::lang('EasyBot politikaları gereği bu parametre kullanılamıyor: {0}', [
            $url->getQuery()
        ])
    ]);
    exit;
}

if (isset(StaticFunctions::post('876541_data1_active')[0])) {
    foreach (StaticFunctions::post('876541_data1_active') as $key => $value) {
        $Act = (isset(StaticFunctions::post('876541_data1_active')[$key])) ? StaticFunctions::post('876541_data1_active')[$key] : null;
        $Key = (isset(StaticFunctions::post('876541_data1_key')[$key])) ? StaticFunctions::post('876541_data1_key')[$key] : null;
        $Val = (isset(StaticFunctions::post('876541_data1_value')[$key])) ? StaticFunctions::post('876541_data1_value')[$key] : null;
        $Des = (isset(StaticFunctions::post('876541_data1_description')[$key])) ? StaticFunctions::post('876541_data1_description')[$key] : null;
        if ($Act == 'active' && $Key != null) {
            $PostData['UrlParams'][$Key] = [
                'value' => $Val,
                'description' => $Des
            ];
        }
    }
}

if (isset(StaticFunctions::post('876541_data2_active')[0])) {
    foreach (StaticFunctions::post('876541_data2_active') as $key => $value) {
        $Act = (isset(StaticFunctions::post('876541_data2_active')[$key])) ? StaticFunctions::post('876541_data2_active')[$key] : null;
        $Key = (isset(StaticFunctions::post('876541_data2_key')[$key])) ? StaticFunctions::post('876541_data2_key')[$key] : null;
        $Val = (isset(StaticFunctions::post('876541_data2_value')[$key])) ? StaticFunctions::post('876541_data2_value')[$key] : null;
        $Des = (isset(StaticFunctions::post('876541_data2_description')[$key])) ? StaticFunctions::post('876541_data2_description')[$key] : null;
        if ($Act == 'active' && $Key != null) {
            $PostData['Headers'][$Key] = [
                'value' => $Val,
                'description' => $Des
            ];
        }
    }
}

if (isset(StaticFunctions::post('876541_data4_active')[0])) {
    foreach (StaticFunctions::post('876541_data4_active') as $key => $value) {
        $Act = (isset(StaticFunctions::post('876541_data4_active')[$key])) ? StaticFunctions::post('876541_data4_active')[$key] : null;
        $Key = (isset(StaticFunctions::post('876541_data4_key')[$key])) ? StaticFunctions::post('876541_data4_key')[$key] : null;
        $Val = (isset(StaticFunctions::post('876541_data4_value')[$key])) ? StaticFunctions::post('876541_data4_value')[$key] : null;
        $Dom = (isset(StaticFunctions::post('876541_data4_domain')[$key])) ? StaticFunctions::post('876541_data4_domain')[$key] : null;
        if ($Act == 'active' && $Key != null && $Dom != null) {
            $PostData['Cookies'][$Key] = [
                'value' => $Val,
                'domain' => $Dom
            ];
        }
    }
}

$BodyTextArray = [
    '0' => 'none',
    '1' => 'form-data',
    '2' => 'x-www-form-urlencoded',
    '3' => 'raw'
];

$PostData['Body'] = [
    'bodyType' => $BodyTextArray[StaticFunctions::post('876541_data3_radio')],
    'bodyValue' => null
];

if (StaticFunctions::post('876541_data3_radio') == 1 || StaticFunctions::post('876541_data3_radio') == 2) {
    $PostData['Body']['bodyValue'] = [];

    if (isset(StaticFunctions::post('876541_data3_active')[0])) {
        foreach (StaticFunctions::post('876541_data3_active') as $key => $value) {
            $Act = (isset(StaticFunctions::post('876541_data3_active')[$key])) ? StaticFunctions::post('876541_data3_active')[$key] : null;
            $Key = (isset(StaticFunctions::post('876541_data3_key')[$key])) ? StaticFunctions::post('876541_data3_key')[$key] : null;
            $Val = (isset(StaticFunctions::post('876541_data3_value')[$key])) ? StaticFunctions::post('876541_data3_value')[$key] : null;
            $Des = (isset(StaticFunctions::post('876541_data3_description')[$key])) ? StaticFunctions::post('876541_data3_description')[$key] : null;
            if ($Act == 'active' && $Key != null) {
                $PostData['Body']['bodyValue'][$Key] = [
                    'value' => $Val,
                    'description' => $Des
                ];
            }
        }
    }
}

if (StaticFunctions::post('876541_data3_radio') == 3) {
    if (strlen(StaticFunctions::post('876541_data3_raw')) < 2 || strlen(StaticFunctions::post('876541_data3_raw')) > 1000) {
        echo StaticFunctions::ApiJson([
            'status' => 'failed',
            'process' => 'failure',
            'title' => StaticFunctions::lang('Bir hata oluştu!'),
            'message' => StaticFunctions::lang('Raw data boyutu geçersiz. En az 2 en fazla 1000 karakter olabilir.')
        ]);
        exit;
    }
    $PostData['Body']['bodyValue'] = StaticFunctions::post('876541_data3_raw');
}


$BrowserArray = [
    '0' => [
        'os_name' => 'Windows 10'
    ],
    '1' => [
        'os_name' => 'Windows 8'
    ],
    '2' => [
        'os_name' => 'Windows 7'
    ],
    '3' => [
        'os_name' => 'Windows NT'
    ],
    '4' => [
        'os_name' => 'Windows Phone'
    ],
    '5' => [
        'os_name' => 'Android'
    ],
    '6' => [
        'os_name' => 'iPhone OS'
    ],
    '7' => [
        'os_name' => 'Firefox OS'
    ],
    '8' => [
        'os_name' => 'FreeBSD'
    ],
    '9' => [
        'os_name' => 'Linux'
    ],
    '10' => [
        'os_name' => 'Ubuntu'
    ],
    '11' => [
        'os_name' => 'NetBSD'
    ],
    '12' => [
        'os_name' => 'OS X'
    ],
    '13' => [
        'os_name' => 'Playstation 3'
    ],
    '14' => [
        'os_name' => 'Playstation 4'
    ],
    '15' => [
        'os_name' => 'Playstation Portable'
    ],
    '16' => [
        'os_name' => 'Playstation Vita'
    ],
    '17' => [
        'os_name' => 'Xbox One'
    ]
];

$PostData['Headers']['User-Agent'] = \Campo\UserAgent::random($BrowserArray[StaticFunctions::post('876541_browser')]);
$PostData['Headers']['Accept-Language'] = json_decode(file_get_contents(APP_DIR . '/storage/languageIsoFormat.json'), true)[StaticFunctions::post('876541_browser_language')] . ',*';
$PostData['ProxyCountry'] = StaticFunctions::post('876541_proxy_country');
$PostData['ProxyType'] = 'datacenter';
$PostData['JwtTokenInfo'] = $Decoded;

$RandomToken = StaticFunctions::random(32);

$InsertProcessorTemp = $db->prepare("INSERT INTO processor_temp SET
user_id = ?,
temp_process = ?,
temp_data = ?,
temp_token = ?,
temp_status = ?,
created_time = ?,
expired_time = ?");
$insert = $InsertProcessorTemp->execute(array(
    $Me, 'create_bot_876541', json_encode($PostData), $RandomToken, 0, time(), (time() + (60 * 10))
));

echo StaticFunctions::ApiJson([
    'status' => 'success',
    'process' => 'success',
    'showToastr' => false,
    'requestToken' => $RandomToken,
    'pageToken' => $Jwt,
    'translations' => [
        'swal' => [
            'title' => StaticFunctions::lang('Her Şey Hazır!'),
            'text' => StaticFunctions::lang('Mükemmel ilerliyoruz. Sırada verdiğin adresten hangi bilgileri alacağımızı belirlemek var. Bunun için verdiğin adrese bağlanmamız gerekiyor. Bu yüzden botunu tamamlayana kadar önemli bilgilerini kullanmamanı rica edeceğim. {0}', [
                '<p>' . StaticFunctions::lang('Hazırsan devam edelim mi?') . '</p>'
            ]),
            'confirmButtonText' => StaticFunctions::lang('Devam Et'),
            'cancelButtonText' => StaticFunctions::lang('İptal'),
            'processing' => StaticFunctions::lang('Bot başlatılıyor..')
        ]
    ]
]);
exit;