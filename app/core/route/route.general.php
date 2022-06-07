<?php


$App->get('/', function () {
	StaticFunctions::go('console');
});

$App->get('/web-service', function () {
	require_once VDIR . '/page.403.php';
});

$App->get('/web-service/(.*?)', function () {
	require_once VDIR . '/page.403.php';
});

$App->get('/login/(.*?)', function () {
	header("Location:" . PATH . "login");
	exit;
});

$App->get('/register/(.*?)', function () {
	header("Location:" . PATH . "register");
	exit;
});

$App->post('/web-service/(.*?)', function ($Req) {
	require_once CDIR . '/ajax.requests.php';
});

$App->get('/login', function () {
	StaticFunctions::new_session();
	if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') {
		header("Location:" . PATH . "console/dashboard");
		exit;
	}
	require_once VDIR . '/login.php';
});

$App->get('/register', function () {
	StaticFunctions::new_session();
	if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') {
		header("Location:" . PATH . "console/dashboard");
		exit;
	}
	require_once VDIR . '/register.php';
});

$App->get('/go', function () {
	sleep(2);
	if (isset($_GET['href'])) {
		header("Location:" . $_GET['href']);
	} else {
		header("Location:" . PATH);
	}
	exit;
});

$App->get('/community', function () {
	StaticFunctions::new_session();
	if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') {
		header("Location:" . PATH . "console/community/login");
		exit;
	} else {
		if (isset($_GET['force']) && StaticFunctions::clear($_GET['force']) == 'login') {
			header("Location:" . PATH . 'login?next=/community');
			exit;
		} else {
			header("Location:https://community.easybot.dev");
			exit;
		}
	}
});

$App->get('/social-login/with/(.*?)', function ($with) {
	StaticFunctions::new_session();
	if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') {
		header("Location:" . PATH . "console/dashboard");
		exit;
	}
	require_once CDIR . '/class.social.login.php';
	$SocialLogin = new EasybotSocialLogin();
	$SocialLogin->go($with);
	exit;
});

$App->get('/social-callback/(.*?)', function ($with) {
	StaticFunctions::new_session();
	if (isset($_SESSION['CheckSession']) && $_SESSION['CheckSession'] == 'active') {
		header("Location:" . PATH . "console/dashboard");
		exit;
	}
	require_once CDIR . '/class.social.login.php';
	$SocialLogin = new EasybotSocialLogin();
	$SocialLogin->callback($with);
});

$App->get('/mail/(.*?)', function ($token) {
	global $db;
	require_once CDIR . '/browser.email.php';
});

$App->get('/reset-password/(.*?)', function ($token) {
	global $db;
	require_once CDIR . '/reset.password.php';
});