<?php
        session_start();
        if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        require_once '../app/core/autoloader.php';

	use app\core\console;
	use app\core\checkdb;
	use app\core\router;
	use app\core\frontConstructor;

	$Console = new Console(true);
	$CheckDb = new CheckDb($Console); // lance checkInstallAndConfig
	$router = new Router($CheckDb,$Console);
	$frontConstructor = new FrontConstructor(Console: $Console);

	// Récupération de l'URL
	$url = trim(string: parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH), characters: '/');

	// récupération des blocs à afficher en fonction de l'url
	$contentDatas = $router->dispatch(url: $url);

	// récuperation de l'url retournée le cas échéant
	if(isset($contentDatas['url'])) $url = $contentDatas['url']; 

	$content = $contentDatas['content'];
	// on ajoute le contenu au front
	$frontConstructor->addContentToStack($content);

	// on pourrais en rajouter à la main aussi
	// $frontConstructor->addContentToStack(['TITLE' => "en plus",'CONTENT'   => '<h3>un test en plus ?</h3>']);
	
	// on récupère la page construite avec l'url d'origine
	$pageToDisplay = $frontConstructor->getPageToDisplay($url);

	// récuperation d'un 'Redirect' le cas échéant
	if(isset($content['Redirect']) && $content['Redirect'] && isset($content['Redirect']['url'])){
		$newurl = $content['Redirect']['url'];
		$delay = $content['Redirect']['refresh'];
		$redirect = 'refresh:'.$delay.';url='.$newurl;
		header(header: $redirect);
	}
	if (!headers_sent()) {
		header(header: CONFIG['WEBSITE']['header']);
	}
	// on affiche la page
	echo $pageToDisplay;