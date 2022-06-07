<?php

use Jenssegers\Date\Date;

StaticFunctions::ajax_form('general');
StaticFunctions::new_session();

try {
    Date::setLocale(mb_strtolower(LANG));

    echo StaticFunctions::ApiJson([
        'time' => Date::now()->format('A h:i'),
        'date' => Date::now()->format('j F Y l')
    ]);
    exit;
} catch (\Throwable $th) {
    echo StaticFunctions::ApiJson([
        'time' => date('H:i'),
        'date' => date('d-m-Y')
    ]);
    exit;
}