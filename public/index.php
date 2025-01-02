<?php
	session_start();
	
	define('WEBSITE', [
		'header' => 'Content-type: text/html; charset=UTF-8',
		'siteurl' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'])
	]);

	define('PROD',0); // 0 en dev, 1 en prod

	require_once '../app/core/autoloader.php';

	use app\core\router;
	use app\core\frontConstructor;
	use app\core\checkdb;

	$CheckDb = new CheckDb();
	$router = new Router($CheckDb);
	$frontConstructor = new FrontConstructor();

	// Récupération de l'URL
	$url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

	$content = $router->dispatch($url,$CheckDb);
	$pageToDisplay = $frontConstructor->getPageToDisplay($url,[$content]);
	
	if (!headers_sent()) {
		header(WEBSITE['header']);
	}
	echo $pageToDisplay;