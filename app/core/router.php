<?php
namespace app\core;

class Router
{
	private $routes = [];
	private $CheckDb;
	private $defaultPage = '';
	private $pdo = null;

	public function __construct($CheckDb) {
		$this->CheckDb = $CheckDb;
		$this->pdo = $this->CheckDb->getPdo();
		// Définition des routes
		// $this->routes = [
		// 	'index'=> 'FrontController@showIndex@null',
		// 	'login'=> 'LoginController@handleLogin@db',
		// 	'logout'=> 'LoginController@logout@null',
		// 	'profile'=> 'ProfileController@showProfile@null',
		// 	'listpc'=> 'ListingController@listPc@db',
		// 	'listeleves'=> 'ListingController@listEleves@db',
		// 	'timeline'=> 'ListingController@listTimeline@db',
		// 	'out'=> 'InOutController@handleOut@db'
		// ];
	}

    public function add($route, $action)
    {
        $this->routes[$route] = $action;
    }


	public function setdefaultPage()
	{
		$this->defaultPage = file_get_contents('../app/views/front.php');
	}

	public function display()
	{
		echo $this->defaultPage;
	}

	public function dispatch($url="")
	{
		if ($url==="") {$url="index";}
		if (!isset($_SESSION['user'])) {$url="login";}
		if (!$this->pdo) {$url="index";}


		

		$this->setdefaultPage();

		if (isset($this->routes[$url])) {
			$action = $this->routes[$url];
			list($controller, $method, $db) = explode('@', $action);
			
			$controller = "app\\controllers\\$controller";

			if (class_exists($controller) && method_exists($controller, $method)) {
				$controllerInstance = new $controller($db==="db"?$this->CheckDb:null);
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
