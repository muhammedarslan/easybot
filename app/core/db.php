<?php


// Try to connect Db.
try {
	$DbDefine = ProjectDefines::Db();
	$db = new PDO("mysql:host=" . $DbDefine['Host'] . ";dbname=" . $DbDefine['Name'] . ";charset=" . $DbDefine['CharSet'], $DbDefine['User'], $DbDefine['Password']);
} catch (PDOException $e) {

	StaticFunctions::system_down();
	exit;
}


if (Debug == true) :
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
endif;