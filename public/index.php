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
		'PROD' => false, // false en dev, true en prod
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
	$router->add(route: 'index',action: 'IndexController@showIndex@null@0');
	$router->add(route: 'login',action: 'LoginController@handleLogin@db@0');
	if (isset($_SESSION['user'])) {
		$router->add(route: 'profile',action: 'ProfileController@showProfile@null@1');
		$router->add(route: 'listpc',action: 'ListingController@listPc@db@1');
		$router->add(route: 'listeleves',action: 'ListingController@listEleves@db@1');
		$router->add(route: 'timeline',action: 'ListingController@listTimeline@db@1');
		$router->add(route: 'out',action: 'OutController@handle@db@1');
		$router->add(route: 'in',action: 'InController@handle@db@1');
		$router->add(route: 'logout',action: 'LoginController@logout@null@1');
	}

	$frontConstructor = new FrontConstructor(Console: $Console);

	// Récupération de l'URL
	$url = trim(string: parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH), characters: '/');

	$contents = $router->dispatch(url: $url);

	if(isset($contents['url'])) $url = $contents['url']; 

	$content = $contents['content'];

	// à découper en 2
	$frontConstructor->addContentToStack($content);
	$frontConstructor->addContentToStack(['TITLE' => "en plus",'CONTENT'   => '<h3>en plusss</h3>']);
	$pageToDisplay = $frontConstructor->getPageToDisplay($url);

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