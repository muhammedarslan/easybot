<?php

// Folder defines.
define('ROOT_DIR', __DIR__);
define('APP_DIR', ROOT_DIR . '/app');
define('CORE_DIR', APP_DIR  . '/core');
define('VDIR', APP_DIR  . '/views');
define('CDIR', APP_DIR  . '/controllers');
define('TMPDIR', ROOT_DIR . '/assets/tmp');


// Start app.
require_once CORE_DIR . '/load.app.php';
$App->run();