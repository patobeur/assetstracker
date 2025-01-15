<?php
namespace app\core;

class Router
{
	private $routes = [];
	private $CheckDb;
	private $defaultPage = '';
	private $pdo = null;
	private $Console = null;

	public function __construct($CheckDb,$Console) {
		$this->Console = $Console;
		$this->CheckDb = $CheckDb;
		$this->pdo = $this->CheckDb->getPdo();
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
		// if (!isset($_SESSION['user'])) {$url="login";}
		if (!$this->pdo) {$url="index";}

		$this->setdefaultPage();

		if (isset($this->routes[$url])) {
			$action = $this->routes[$url];
			list($controller, $method, $db) = explode('@', $action);
			
			$controller = "app\\controllers\\$controller";

			if (class_exists($controller) && method_exists($controller, $method)) {
				$controllerInstance = new $controller($db==="db"?$this->CheckDb:null);
				return [
					'content'=>$controllerInstance->$method()
				];
			} else {
				return [
					'content'=>$this->notFound(false)
				];
			}
		} else {
			return [
				'url'=>'404',
				'content'=>$this->notFound(true)
			];
		}
	}

	private function notFound($boule)
	{		
		$notFoundController = "app\\controllers\\NotFoundController";
		$notFound = new $notFoundController();
		$content = $notFound->showIndex($boule);
		// http_response_code(404);
		return $content;
	}
}
