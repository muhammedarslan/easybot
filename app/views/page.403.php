<?php

header('Content-Type: application/xml; charset=utf-8');
http_response_code(403);

echo '<?xml version="1.0" encoding="UTF-8"?>
<WebService>
    <HttpStatus>403</HttpStatus>
    <HttpMessage>Access Denied.</HttpMessage>
    <Content-type>Application/xml</Content-type>
    <ErrorMessage>Service authentication required.</ErrorMessage>
    <EasyRayID>' . StaticFunctions::random(30) . '</EasyRayID>
    <RequestTime>' . date('d-m-Y H:i:s') . ' ' . date_default_timezone_get() . '</RequestTime>
    <UnixTime>' . time() . '</UnixTime>
</WebService>';