<?php


if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
	header("Content-type: application/json; charset=utf-8");
	http_response_code(403);
	echo StaticFunctions::JsonOutput(array(
		'HttpStatusCode' => 403,
		'ErrorMessage'   => 'Access Denied.'
	), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	exit;
}

if (!isset($_SERVER['HTTP_REFERER'])) {
	header("Content-type: application/json; charset=utf-8");
	http_response_code(403);
	echo StaticFunctions::JsonOutput(array(
		'HttpStatusCode' => 403,
		'ErrorMessage'   => 'Access Denied.'
	), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	exit;
}

$AjaxDomain = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
if ($AjaxDomain != DOMAIN && $AjaxDomain != PUSH_DOMAIN) {
	header("Content-type: application/json; charset=utf-8");
	http_response_code(403);
	echo StaticFunctions::JsonOutput(array(
		'HttpStatusCode' => 403,
		'ErrorMessage'   => 'Access Denied.'
	), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	exit;
}

if (!mb_strstr($Req, '/')) {
	$Req = '/' . $Req;
}

$UrlParse = explode('/', $Req);
$AjaxFile = '';

foreach ($UrlParse as $key => $value) {
	$AjaxFile .= ucwords($value);
}

if (!$_POST) {
	http_response_code(401);
	exit;
}

if (!file_exists(CORE_DIR . '/ajax/' . $AjaxFile . '.php')) {
	http_response_code(401);
	exit;
}

global $db;
require_once CORE_DIR . '/ajax/' . $AjaxFile . '.php';