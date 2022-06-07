<?php

if (file_exists(ROOT_DIR . '/assets/console/app-assets/scripts/' . $file)) :
    header("Location:/assets/console/app-assets/scripts/" . $file . '?v=' . ProjectDefines::EasyVersion());
    exit;
else :
    header('Content-Type: application/javascript');
    echo '"use strict";';
endif;