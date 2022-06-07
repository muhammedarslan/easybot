<?php

if (StaticFunctions::clear($_SERVER['SERVER_NAME']) == API_DOMAIN) :
    echo StaticFunctions::ApiJson([
        'status' => 'failed',
        'error_code' => 'ERR_NOTFOUND',
        'error_message' => 'wrong api request, please check url and try again later.'
    ]);
    exit;
else :
    $route_path = rtrim(urldecode(strtok($_SERVER["REQUEST_URI"], '?')), '/');
    $route_method = $_SERVER['REQUEST_METHOD'];
    $route_path = (str_replace(PATH, '/', $route_path) == '') ? '/' : str_replace(PATH, '/', $route_path);
    $exp = explode('/', $route_path);
    StaticFunctions::new_session();

    if (isset($exp[1]) && $exp[1] == 'console' && isset($_SESSION['CheckSession'])) :
        $PageOptions = [
            'Title'  => StaticFunctions::lang('404 - Sayfa Bulunamadı'),
            'Params' => [],
            'View'   => '404',
            'Class'  => 'console',
            'BodyE'  => null
        ];
        StaticFunctions::load_page($PageOptions);
    else :
        http_response_code(404);
        header("Location:/");
        exit;
    endif;
endif;