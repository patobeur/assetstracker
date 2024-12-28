<?php
namespace app\core;

class Router
{
    private $routes = [];

    public function __construct() {
        // Définition des routes
        $this->routes = [
            'index'=> 'FrontController@showIndex',
            'login'=> 'LoginController@login',
            'logout'=> 'LoginController@logout',
            'profile'=> 'ProfileController@showProfile'
        ];
    }
    private $defaultPage = '';

    public function add($route, $action)
    {
        $this->routes[$route] = $action;
    }

    public function createPage()
    {
        $this->defaultPage = file_get_contents('../app/views/front.php');
    }
    public function display()
    {
        echo $this->defaultPage;
    }

    public function dispatch($url)
    {
        if ($url==="") {
            $url="index";
        }
        $this->createPage();

        if (isset($this->routes[$url])) {
            $action = $this->routes[$url];
            list($controller, $method) = explode('@', $action);
            

            $controller = "app\\controllers\\$controller";

            if (class_exists($controller) && method_exists($controller, $method)) {
                $controllerInstance = new $controller();
                return $controllerInstance->$method();
            } else {
                $this->notFound();
            }
        } else {
            $this->notFound();
        }
    }

    private function notFound()
    {
        http_response_code(404);
        echo "404 - Page not found";
    }
}
