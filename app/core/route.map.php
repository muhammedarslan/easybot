<?php

StaticFunctions::new_session();


if (StaticFunctions::clear($_SERVER['SERVER_NAME']) == API_DOMAIN) {
	require_once CORE_DIR . '/route/route.api.php';
} else if (StaticFunctions::clear($_SERVER['SERVER_NAME']) == PUSH_DOMAIN) {
	require_once CORE_DIR . '/route/route.push.php';
} else {
	require_once CORE_DIR . '/route/route.general.php';
	if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') :
		require_once CORE_DIR . '/route/route.console.php';
	endif;
}