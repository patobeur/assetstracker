<?php
	session_start();
	define('CONFIG', [
		'WEBSITE' => [
			'header' => 'Content-type: text/html; charset=UTF-8',
			'siteurl' => 'http://' . $_SERVER['HTTP_HOST'],
			'sitedir' => dirname($_SERVER['PHP_SELF']),
			'refreshOut' => 10,
		],
		'PROD' => false, // 0 en dev, 1 en prod
	]);


	define(constant_name: 'PROD', value: false); // 0 en dev, 1 en prod
	
	require_once '../app/core/autoloader.php';

	use app\core\console;
	use app\core\router;
	use app\core\frontConstructor;
	use app\core\checkdb;

	$Console = new Console(active: true);

	$CheckDb = new CheckDb(Console: $Console);
	$router = new Router(CheckDb: $CheckDb,Console: $Console);

	// $pdo = $CheckDb->getPdo();
	
	// Définition des routes
	$router->add(route: 'login', action: 'AuthController@login');
	$router->add(route: 'index',action: 'FrontController@showIndex@null');
	$router->add(route: 'login',action: 'LoginController@handleLogin@db');
	$router->add(route: 'logout',action: 'LoginController@logout@null');
	$router->add(route: 'profile',action: 'ProfileController@showProfile@null');
	$router->add(route: 'listpc',action: 'ListingController@listPc@db');
	$router->add(route: 'listeleves',action: 'ListingController@listEleves@db');
	$router->add(route: 'timeline',action: 'ListingController@listTimeline@db');
	$router->add(route: 'out',action: 'InOutController@handleOut@db');

	$frontConstructor = new FrontConstructor(Console: $Console);

	// Récupération de l'URL
	$url = trim(string: parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH), characters: '/');

	$content = $router->dispatch(url: $url);

	$pageToDisplay = $frontConstructor->getPageToDisplay(url: $url,stack: [$content]);

	if(isset($content['Redirect'])){
		$datas = 'refresh:'.$content['Redirect']['refresh'].';url='.$content['Redirect']['url'];
		header(header: $datas);
	}
	if (!headers_sent()) {
		header(header: CONFIG['WEBSITE']['header']);
	}
	echo $pageToDisplay;