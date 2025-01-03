<?php
	session_start();
	
	define('WEBSITE', [
		'header' => 'Content-type: text/html; charset=UTF-8',
		'siteurl' => 'http://' . $_SERVER['HTTP_HOST'],
		'sitedir' => dirname($_SERVER['PHP_SELF'])
	]);

	define('PROD',0); // 0 en dev, 1 en prod

	require_once '../app/core/autoloader.php';

	use app\core\router;
	use app\core\frontConstructor;
	use app\core\checkdb;

	$CheckDb = new CheckDb();

	$router = new Router($CheckDb);

	// $pdo = $CheckDb->getPdo();
	
	// Définition des routes
	$router->add('login', 'AuthController@login');
	$router->add('index','FrontController@showIndex@null');
	$router->add('login','LoginController@handleLogin@db');
	$router->add('logout','LoginController@logout@null');
	$router->add('profile','ProfileController@showProfile@null');
	$router->add('listpc','ListingController@listPc@db');
	$router->add('listeleves','ListingController@listEleves@db');
	$router->add('timeline','ListingController@listTimeline@db');
	$router->add('out','InOutController@handleOut@db');

	$frontConstructor = new FrontConstructor();

	// Récupération de l'URL
	$url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

	$content = $router->dispatch($url,$CheckDb);

	$pageToDisplay = $frontConstructor->getPageToDisplay($url,[$content]);

	if(isset($content['Redirect'])){
		$datas = 'refresh:'.$content['Redirect']['refresh'].';url='.$content['Redirect']['url'];
		header($datas);
	}
	if (!headers_sent()) {
		header(WEBSITE['header']);
	}
	echo $pageToDisplay;