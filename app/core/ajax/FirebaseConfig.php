<?php

StaticFunctions::ajax_form('general');
StaticFunctions::new_session();

echo StaticFunctions::ApiJson(ProjectDefines::FirebaseConfig());