<?php
namespace app\core;

class Router
{
	private $routes = [];
	private $CheckDb;

	public function __construct($CheckDb) {
		$this->CheckDb = $CheckDb;
		// Définition des routes
		$this->routes = [
			'index'=> 'FrontController@showIndex',
			'login'=> 'LoginController@handleLogin',
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
		if ($url==="") {$url="index";}
		if (!isset($_SESSION['user'])) {$url="login";}

		$this->createPage();

		if (isset($this->routes[$url])) {
			$action = $this->routes[$url];
			list($controller, $method) = explode('@', $action);
			

			$controller = "app\\controllers\\$controller";

			if (class_exists($controller) && method_exists($controller, $method)) {
				$controllerInstance = new $controller($this->CheckDb);
				return $controllerInstance->$method();
			} else {
				
				return $this->notFound();
			}
		} else {
			return $this->notFound();
		}
	}

	private function notFound()
	{
		
		$notFoundController = "app\\controllers\\NotFoundController";

		if (class_exists($notFoundController) && method_exists($notFoundController, 'showIndex')) {
			$controllerInstance = new $notFoundController();
			$content = $controllerInstance->showIndex();
			// http_response_code(404);
			return $content;
		}
		else {die('404');}
	}
}
