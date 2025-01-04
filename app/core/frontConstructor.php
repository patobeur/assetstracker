<?php
namespace app\core;

class FrontConstructor
{
	private $defaultPage;
	private $Console;

	public function __construct($Console) {
		$this->Console = $Console;
		$this->defaultPage = file_get_contents(filename: '../app/views/front.php');
	}

	public function addContent($stack=[]): void
	{
		if (count($stack)>0){
			$contents = '';
			for ($i=0; $i < count($stack) ; $i++) {
				$contents.="#CONTENT".$i."#";
			}
			$this->defaultPage = str_replace("{{CONTENTS}}",$contents,$this->defaultPage);

			for ($i=0; $i < count($stack) ; $i++) {
				$this->defaultPage = str_replace("#CONTENT".$i."#",$stack[$i]['CONTENT'],$this->defaultPage);
			}

			$this->defaultPage = str_replace("{{TITLE}}",$stack[0]['TITLE'],$this->defaultPage);
		}
		else {
			$this->defaultPage = str_replace("{{CONTENT}}",'vide',$this->defaultPage);
			$this->defaultPage = str_replace("{{TITLE}}",'default',$this->defaultPage);
		}
	}

	public function addConsole(): void
	{
		$this->defaultPage = $this->Console->addConsole($this->defaultPage);
	}

	public function getPageToDisplay($url,$stack=[]): string
	{
		$this->addNavigation(url: $url);
		$this->addContent(stack: $stack);
		$this->addBodyBackground(url: $url);

		$this->addConsole();

		return $this->defaultPage;
	}

	public function addBodyBackground($url): void
	{
		$defaultStyle = "";
		switch ($url) {
			case 'login':
				$defaultStyle = "<style>body {background: url('/img/login.webp');}</style>";
				break;
			default:
				$defaultStyle = "";
				break;
		}
		$this->defaultPage = str_replace( "{{background}}", $defaultStyle, $this->defaultPage);
	}

	public function addNavigation($url): void
	{
		if(!isset($_SESSION['user'])) {
			$this->defaultPage = str_replace(search: "{{NAVIGATION}}",replace: '',subject: $this->defaultPage);

		} else {
			$this->defaultPage = str_replace(search: "{{NAVIGATION}}",replace: $this->getTopNav(url: $url),subject: $this->defaultPage);
		}
	}
	
	public function getTopNav($url): string
	{
		// items
		$items = [
			'accueil' => '<li><a href="/">Accueil</a></li>',
			'login'   => ((!isset($_SESSION['user']) && ($url != 'login')) ? '<li><a href="/login">login</a></li>' : ''),
			'out'   => ((isset($_SESSION['user'])) ? '<li><a href="/out">Empruntez</a></li>' : ''),
			// 'in'   => ((isset($_SESSION['user'])) ? '<li><a href="#">Rendez</a></li>' : ''),
			'listpc'   => ((isset($_SESSION['user'])) ? '<li><a href="/listpc">List Pc</a></li>' : ''),
			'listeleves'   => ((isset($_SESSION['user'])) ? '<li><a href="/listeleves">List Élèves</a></li>' : ''),
			'timeline'   => ((isset($_SESSION['user'])) ? '<li><a href="/timeline">Timeline</a></li>' : ''),
			// 'profile' => ((isset($_SESSION['user'])) ? '<li><a href="/profile">Profile</a></li>' : ''),
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
