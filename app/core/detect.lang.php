<?php

// Check the param and change language if is valid.
if (isset($_GET['hl'])) :
    AppLanguage::SetLang(AppLanguage::clear($_GET['hl']));
endif;

// Get and set App Language.
$GetCurrentLang = AppLanguage::getLang();
define('LANG', $GetCurrentLang);