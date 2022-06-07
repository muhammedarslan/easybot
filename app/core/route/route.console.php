<?php


$App->get('/console', function () {
	header("Location:" . PATH . 'console/dashboard');
	exit;
});

$App->get('/console/notification/go/(.*?)', function ($token) {
	StaticFunctions::NoBarba();
	global $db;
	$Me = StaticFunctions::get_id();
	AppNotifications::SingleNotification($Me, $db, $token);
	StaticFunctions::go_home();
});

$App->get('/console/log-out', function () {
	StaticFunctions::NoBarba();
	global $db;
	require_once VDIR . '/log-out.php';
	exit;
});

$App->get('/console/community/login', function () {
	global $db;
	$Me = StaticFunctions::get_id();
	$MeQuery = $db->query("SELECT email,real_name,avatar FROM users WHERE id='{$Me}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
	if (!$MeQuery) {
		StaticFunctions::LogOut();
		http_response_code(403);
		exit;
	}
	try {
		$payload = [
			'userRealName' => StaticFunctions::say($MeQuery['real_name']),
			'userAvatar'   => $MeQuery['avatar'],
			'userEmail'    => $MeQuery['email'],
			'tokenExpire'  => time() + (60 * 5)
		];
		$jwt = \Firebase\JWT\JWT::encode($payload, ProjectDefines::JwtSecretKey()['Community']);
		header("Location:https://community.easybot.dev/session/sso_login?token=" . $jwt);
	} catch (\Throwable $th) {
		http_response_code(403);
		exit;
	}
});

$App->get('/console/script/(.*?)', function ($file) {
	StaticFunctions::NoBarba();
	require_once CDIR . '/get.script.php';
	exit;
});

$App->get('/console/dashboard', function () {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Konsol Anasayfa'),
		'Params' => [],
		'View'   => 'dashboard',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});

$App->get('/console/create/bot', function () {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Bot Oluşturma Sihirbazı'),
		'Params' => [],
		'View'   => 'create.bot.wait',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});

$App->get('/console/create/bot/(.*?)/876541', function ($jwt) {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Yeni Adım1 - Bot Oluşturma Sihirbazı'),
		'Params' => [$jwt],
		'View'   => 'newbot.step1',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});

$App->get('/console/create/bot/(.*?)', function ($jwt) {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Bot Oluşturma Sihirbazı'),
		'Params' => [$jwt],
		'View'   => 'create.bot',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});

$App->get('/console/parse/html/(.*?)/876541/(.*?)/t/(.*?)', function ($jwt, $tempToken, $unix) {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Verileri Belirle'),
		'Params' => [$jwt, $tempToken],
		'View'   => 'newbot.step1.sandbox',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});

$App->get('/console/account/verify', function () {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Hesabını Onayla'),
		'Params' => [],
		'View'   => 'verify.account',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});


$App->get('/console/inbox', function () {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Gelen Kutusu'),
		'Params' => [],
		'View'   => 'inbox',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});

$App->get('/console/security/login', function () {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Hatalı Giriş Denemeleri'),
		'Params' => [],
		'View'   => 'failed.login',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});

$App->get('/console/account/profile', function () {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Hesap & Profil'),
		'Params' => [],
		'View'   => 'profile',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});

$App->get('/console/support/tickets', function () {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Destek Talepleri'),
		'Params' => [],
		'View'   => 'support.tickets',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});

$App->get('/console/account/profile/edit', function () {
	$PageOptions = [
		'Title'  => StaticFunctions::lang('Hesap & Profil'),
		'Params' => [],
		'View'   => 'edit.profile',
		'Class'  => 'console',
		'BodyE'  => null
	];
	StaticFunctions::load_page($PageOptions);
});

$App->get('/console/storage/download/(.*?)', function ($FileToken) {
	global $db;
	require_once CDIR . '/class.upload.php';
	$UploadClass = new Upload();
	$UploadClass->setDb($db);
	$UploadClass->SingleAwsFile(StaticFunctions::clear($FileToken));
	exit;
});

$App->get('/console/push/id', function () {
	global $db;
	$UserID = StaticFunctions::get_id();
	$UserToken = $db->query("SELECT token FROM users WHERE id = '{$UserID}' and status=1 ")->fetch(PDO::FETCH_ASSOC);
	if (!$UserToken) {
		StaticFunctions::go('console/log-out');
		exit;
	}
	$payload = array(
		'UserId' => $UserID,
		'TokenExpire' => time() + (60 * 10)
	);
	$jwt = \Firebase\JWT\JWT::encode($payload, StaticFunctions::JwtKey());
	header("Location:" . PROTOCOL . PUSH_DOMAIN . '/set/' . $jwt . '/' . $UserToken['token']);
	exit;
});