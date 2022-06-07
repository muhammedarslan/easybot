<?php

// System components.
require_once APP_DIR  . '/vendor/autoload.php';
require_once CORE_DIR . '/static.defines.php';
require_once CDIR     . '/project.defines.php';
require_once CDIR     . '/class.language.php';
require_once CORE_DIR . '/detect.lang.php';
require_once CDIR     . '/class.functions.php';
require_once CDIR     . '/class.notifications.php';
require_once CDIR     . '/request.validation.php';

try {

	// System core.
	require_once CORE_DIR . '/db.php';
	require_once CORE_DIR . '/login.check.php';
	require_once CDIR     . '/class.route.php';
} catch (Exception $e) {
	if (Debug) {
		echo $e;
	}
	StaticFunctions::system_down();
}