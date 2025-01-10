<?php
	session_start();

	define('CONFIG', [
		'WEBSITE' => [
			'header' => 'Content-type: text/html; charset=UTF-8',
			'siteurl' => 'http://' . $_SERVER['HTTP_HOST'],
			'sitedir' => dirname($_SERVER['PHP_SELF']),
		],
		'REFRESH' => [
			'in' => 2,
			'out' => 2
		],
		'PROD' => false, // 0 en dev, 1 en prod
	]);
	
	require_once '../app/core/autoloader.php';

	use app\core\console;
	use app\core\checkdb;
	use app\core\router;
	use app\core\frontConstructor;

	$Console = new Console(active: true);
	$CheckDb = new CheckDb(Console: $Console);
	$router = new Router(CheckDb: $CheckDb,Console: $Console);
	
	// Définition des routes
	$router->add(route: 'login', action: 'AuthController@login');
	$router->add(route: 'index',action: 'IndexController@showIndex@null');
	$router->add(route: 'login',action: 'LoginController@handleLogin@db');
	$router->add(route: 'logout',action: 'LoginController@logout@null');
	$router->add(route: 'profile',action: 'ProfileController@showProfile@null');
	$router->add(route: 'listpc',action: 'ListingController@listPc@db');
	$router->add(route: 'listeleves',action: 'ListingController@listEleves@db');
	$router->add(route: 'timeline',action: 'ListingController@listTimeline@db');
	$router->add(route: 'out',action: 'OutController@handle@db');
	$router->add(route: 'in',action: 'InController@handle@db');

	$frontConstructor = new FrontConstructor(Console: $Console);

	// Récupération de l'URL
	$url = trim(string: parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH), characters: '/');

	$content = $router->dispatch(url: $url);

	$pageToDisplay = $frontConstructor->getPageToDisplay(url: $url,stack: [$content]);

	if(isset($content['Redirect'])){
		$newurl = $content['Redirect']['url'];
		$delay = $content['Redirect']['refresh'];
		$redirect = 'refresh:'.$delay.';url='.$newurl;
		header(header: $redirect);
	}
	if (!headers_sent()) {
		header(header: CONFIG['WEBSITE']['header']);
	}
	echo $pageToDisplay;