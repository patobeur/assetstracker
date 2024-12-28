<?php
    session_start();
    
    define('PROD',0); // 0 en dev, 1 en prod

    require_once '../app/core/autoloader.php';

    use app\core\router;
    use app\core\frontConstructor;
    use app\core\checkdb;

    $frontConstructor = new CheckDb();
    $router = new Router();
    $frontConstructor = new FrontConstructor();

    // Définition des routes
    // $router->add('index', 'FrontController@showIndex');
    // $router->add('login', 'LoginController@login');
    // $router->add('logout', 'LoginController@logout');
    // $router->add('profile', 'ProfileController@showProfile');

    // Récupération de l'URL
    $url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    $content = $router->dispatch($url);
    $frontConstructor->displayPage($url,[$content]);
