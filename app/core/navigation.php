<?php
namespace app\core;

class Navigation
{
	private $pageToDisplay;
	private $navigation ='';
    private $view = '
    <nav class="nav-top">
		<ul class="nav-top-ul">
			{{topnav}}
		</ul>
	</nav>';

	public function addNavigation($pageToDisplay,$url): string
	{
        $this->pageToDisplay= $pageToDisplay;
        $this->navigation = !isset($_SESSION['user']) ? '' : $this->getTopNav(url: $url);

        $this->view = str_replace(search: "{{topnav}}",replace: $this->navigation,subject: $this->view);

        $this->pageToDisplay = str_replace(search: "{{topNavView}}",replace: $this->view, subject: $this->pageToDisplay);
        return $this->pageToDisplay;
	}
	
	public function getTopNav($url): string
	{
		// items
		$items = [
			'accueil' => '<li><a href="/">Accueil</a></li>',
			'login'   => ((!isset($_SESSION['user']) && ($url != 'login')) ? '<li><a href="/login">login</a></li>' : ''),
			'in'   => ((isset($_SESSION['user'])) ? '<li><a href="/in">Rendez</a></li>' : ''),
			'out'   => ((isset($_SESSION['user'])) ? '<li><a href="/out">Empruntez</a></li>' : ''),
			// 'in'   => ((isset($_SESSION['user'])) ? '<li><a href="#">Rendez</a></li>' : ''),
			'listpc'   => ((isset($_SESSION['user'])) ? '<li><a href="/listpc">List Pc</a></li>' : ''),
			'listeleves'   => ((isset($_SESSION['user'])) ? '<li><a href="/listeleves">List Élèves</a></li>' : ''),
			'timeline'   => ((isset($_SESSION['user'])) ? '<li><a href="/timeline">Timeline</a></li>' : ''),
			'profile' => ((isset($_SESSION['user'])) ? '<li><a href="/profile">Profile</a></li>' : ''),
			'logout'  => ((isset($_SESSION['user'])) ? '<li class="deco"><a href="/logout">Déconnexion</a></li>' : ''),
			// 'pseudo'  => (isset($_SESSION['user']) && isset($_SESSION['user']['pseudo'])) ? '<li><a>['.$_SESSION['user']['pseudo'].']</a></li>' : ''
		];
		// 
		$menuItems = '';
	
		foreach ($items as $key => $value) {
			$menuItems .= $value;
		}

		return $menuItems;
	}
}
