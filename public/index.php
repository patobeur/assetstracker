<?php
	session_start();
	
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
	$frontConstructor->displayPage($url,[$content]);