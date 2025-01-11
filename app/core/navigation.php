<?php
namespace app\core;

class Navigation
{
	private $pageToDisplay;
	private $navigation ='';
	private $menus;
	private $url;
	private $view = '
	<nav class="nav-top">
		<ul class="nav-top-brand">...</ul>
		<ul class="nav-top-ul">
			{{topnav}}
		</ul>
	</nav>';

	public function __construct() {
		$this->menus = [
			'index'=> [
				'type'=> 'a',
				'url'=> 'index',
				'content'=> 'Accueil',
				'classIco'=> 'fa fa-home',
				'hiddenIfUrl'=> false,
				'href'=> '/',
				'lv'=> true,
			],
			'in'=> [
				'type'=> 'a',
				'url'=> 'in',
				'content'=> 'Rendez',
				'classIco'=> 'fa-regular fa-circle-down',
				'hiddenIfUrl'=> false,
				'href'=> '/in',
				'needlog'=> true,
				'lv'=> 1,
			],
			'out'=> [
				'type'=> 'a',
				'url'=> 'out',
				'content'=> 'Empruntez',
				'classIco'=> 'fa-regular fa-circle-up',
				'hiddenIfUrl'=> false,
				'href'=> '/out',
				'needlog'=> true,
				'lv'=> 1,
			],
			'listpc'=> [
				'type'=> 'a',
				'url'=> 'listpc',
				'content'=> 'List Pc',
				'classIco'=> 'fa-solid fa-laptop',
				'hiddenIfUrl'=> false,
				'href'=> '/listpc',
				'needlog'=> true,
				'lv'=> 1,
			],
			'listeleves'=> [
				'type'=> 'a',
				'url'=> 'listeleves',
				'content'=> 'List Élèves',
				'classIco'=> 'fa-solid fa-users',
				'hiddenIfUrl'=> false,
				'href'=> '/listeleves',
				'needlog'=> true,
				'lv'=> 1,
			],
			'timeline'=> [
				'type'=> 'a',
				'url'=> 'timeline',
				'content'=> 'Timeline',
				'classIco'=> 'fa-solid fa-heart-pulse',
				'hiddenIfUrl'=> false,
				'href'=> '/timeline',
				'needlog'=> true,
				'lv'=> 1,
			],
			'profile'=> [
				'type'=> 'a',
				'url'=> 'profile',
				'content'=> 'Profile',
				'classIco'=> 'fa-solid fa-user',
				'hiddenIfUrl'=> ['profile'],
				'href'=> '/profile',
				'needlog'=> true,
				'lv'=> 1,
			],
			'login'=> [
				'type'=> 'a',
				'url'=> 'login',
				'content'=> 'LogIn',
				'classIco'=> 'fa-solid fa-user',
				'hiddenIfUrl'=> ['login'],
				'href'=> '/login',
				'needlog'=> true,
				'lv'=> 1,
			],
			'logout'=> [
				'type'=> 'a',
				'url'=> 'logout',
				'content'=> 'Déconnexion',
				'classIco'=> 'fa-solid fa-user',
				'hiddenIfUrl'=> false,
				'href'=> '/logout',
				'needlog'=> true,
				'lv'=> 1,
			],
			'github'=> [
				'type'=> 'a',
				'url'=> 'github',
				'content'=> 'Déconnexion',
				'classIco'=> 'fa-brands fa-github',
				'hiddenIfUrl'=> false,
				'href'=> 'https://github.com/patobeur/assetstracker',
				'target'=> '_github',
				'needlog'=> true,
				'lv'=> 1,
			],
		];
	}
	public function addNavigation($pageToDisplay,$url): string
	{
		$this->url= $url;
		$this->pageToDisplay= $pageToDisplay;
		$this->navigation = $this->getTopNav();

		$this->view = str_replace(search: "{{topnav}}",replace: $this->navigation,subject: $this->view);

		$this->pageToDisplay = str_replace(search: "{{topNavView}}",replace: $this->view, subject: $this->pageToDisplay);
		return $this->pageToDisplay;
	}
	private function getI($index,$key)
	{
		 return (isset($this->menus[$index]) && isset($this->menus[$index][$key]) )
			? '<i class="'.$this->menus[$index][$key].'"></i>'
			: '';
	}
	private function getLi($index)
	{
		$needlog = $this->menus[$index]['needlog'] ?? false;
		$requestUrl = $this->menus[$index]['url'];
		$requestHref = $this->menus[$index]['href'] ? " href=".$this->menus[$index]['href'] : "";
		$requestcontent = $this->menus[$index]['content'];
		$url = $this->url;

		$rules = $this->menus[$index]['hiddenIfUrl'] ?? [];
		$display = true;
		if ($rules && count($rules)>0){
			foreach ($rules as $value) if($url === $value) $display = false;
		}
		$liClass= "";
		if($display && !($needlog && !$_SESSION['user']) ){
			$liClass = '<li'.($url===$requestUrl ? ' class="on"':'').">";
			$liClass .= $this->getI($index,'classIco');
			$liClass .= '<a '.$requestHref.'>';
			$liClass .= $requestcontent;
			$liClass .= '</a>';
			$liClass .= '</li>';
		}
		return $liClass;
	}
	public function getTopNav(): string
	{
		$url = $this->url;
		// items
		$items = [
			'accueil' => $this->getLi('index'),
			'in' => $this->getLi('in'),
			'out' => $this->getLi('out'),
			'listpc' => $this->getLi('listpc'),
			'listeleves' => $this->getLi('listeleves'),
			'timeline' => $this->getLi('timeline'),
			'profile' => $this->getLi('profile'),
			// 'profile' => ((isset($_SESSION['user'])) ? '<li'.($url==='in'?' class="profile"':'').'><a href="/profile">Profile</a> <i class="fa-solid fa-user"></i></li>' : ''),
			'login'   => ((!isset($_SESSION['user']) && ($url != 'login')) ? '<li class="login"><a href="/login">login</a> <i class="fa-solid fa-right-to-bracket"></i></li>' : ''),
			// 'logout' => $this->getLi('logout'),
			'logout'  => ((isset($_SESSION['user'])) ? '<li class="deco"><a href="/logout">Déconnexion <i class="fa-solid fa-power-off"></i></li>' : ''),
			'github'  => ((isset($_SESSION['user'])) ? '<li class="github"><a href="https://github.com/patobeur/assetstracker" target="github">Github</a> <i class="fa-brands fa-github"></i></li>' : ''),
		];
		// 
		$menuItems = '';
	
		foreach ($items as $key => $value) {
			$menuItems .= $value;
		}

		return $menuItems;
	}
}
