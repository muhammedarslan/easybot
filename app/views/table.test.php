<?php

require_once CDIR . '/class.set.table.php';
if (!isset($PageCss)) $PageCss = [];
if (!isset($PageJs))  $PageJs  = [];

$Table = new DataTable();
$Table->setTitle(StaticFunctions::lang($TableTitle));
$Table->setID($TableID);
$Table->setOptions([
    'Search' => true,
    'Export' => true,
    'PageLength' => [
        'Start' => 4,
        'Menu'  => '[4, 10, 15, 20,25]'
    ],
    'Order' => [
        'Order' => 1,
        'Type' => 'desc'
    ]
]);
$PageCss = $Table->setCss($PageCss);
$PageJs  = $Table->setJs($PageJs);
if ($Table->CheckAjax()) :
    $Table->getContent($db);
endif;

$Table->setHeaders([
    [
        'Name' => '#',
        'Orderable' => false,
        'TextCenter' => false,
        'HideMobile' => true,
        'AlwaysShow' => false,
        'Export' => false,
        'Render' => 'normal'
    ],
    [
        'Name' => StaticFunctions::lang('DENENME ZAMANI'),
        'Orderable' => true,
        'TextCenter' => false,
        'HideMobile' => false,
        'AlwaysShow' => false,
        'Export' => true,
        'Render' => 'bold'
    ],
    [
        'Name' => StaticFunctions::lang('IP ADRESİ'),
        'Orderable' => true,
        'TextCenter' => false,
        'HideMobile' => false,
        'AlwaysShow' => false,
        'Export' => true,
        'Render' => 'normal'
    ],
    [
        'Name' => StaticFunctions::lang('YAKLAŞIK KONUM'),
        'Orderable' => true,
        'TextCenter' => false,
        'HideMobile' => false,
        'AlwaysShow' => false,
        'Export' => true,
        'Render' => 'normal'
    ],
    [
        'Name' => StaticFunctions::lang('TARAYICI BİLGİSİ'),
        'Orderable' => true,
        'TextCenter' => false,
        'HideMobile' => false,
        'AlwaysShow' => false,
        'Export' => true,
        'Render' => 'normal'
    ],
    [
        'Name' => StaticFunctions::lang('DURUM'),
        'Orderable' => false,
        'TextCenter' => true,
        'HideMobile' => false,
        'AlwaysShow' => true,
        'Export' => true,
        'Render' => 'status'
    ]
]);

if ($Table->CheckOptions()) :
    header('Content-Type: application/javascript');
    echo $Table->GetOptions();
    exit;
endif;