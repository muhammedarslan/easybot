<?php

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberFormat;

StaticFunctions::ajax_form('private');
StaticFunctions::new_session();
$Me = StaticFunctions::get_id();

$Now = time();
$GetPinInfo = $db->query("SELECT * FROM pin_codes WHERE user_id = '{$Me}' and process_type='change_phone_number_step2' and last_time > $Now ")->fetch(PDO::FETCH_ASSOC);
if ($GetPinInfo) {
    $PinInfo = json_decode($GetPinInfo['process_data'], true);
    $SendedToNumber = $PinInfo['withData']['PhoneCode'] . $PinInfo['withData']['PhoneNumber'];

    $SendType = 'sms';
    try {
        $number = PhoneNumber::parse($SendedToNumber);
        $SendedToNumber = $number->format(PhoneNumberFormat::INTERNATIONAL);
    } catch (PhoneNumberParseException $e) {
        $SendedToNumber = StaticFunctions::lang('Geçersiz!');
    }
    $SendedTo = StaticFunctions::lang('{0} numaralı telefonuna', [
        $SendedToNumber
    ]);

    echo StaticFunctions::ApiJson([
        'PinSendType' => $SendType,
        'PinSendedTo' => $SendedTo
    ]);
    exit;
} else {
    http_response_code(401);
    exit;
}