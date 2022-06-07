<?php

if (isset($_GET['format']) && StaticFunctions::clear($_GET['format']) == 'xml') :
    header('Content-Type: application/xml; charset=utf-8');
else :
    header("Content-type: application/json; charset=utf-8");
endif;

$App->get('/msa', function () {
    echo 'Api anasayfa';
});