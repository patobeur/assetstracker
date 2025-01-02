<?php
namespace app\core;

class FrontConstructor
{
	private $defaultPage;

	public function __construct() {
		$this->defaultPage = file_get_contents('../app/views/front.php');
	}

	public function addContent($stack=[])
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

	public function getPageToDisplay($url,$stack=[])
	{
		$this->addNavigation($url);
		$this->addContent($stack);
		return $this->defaultPage;
	}

	public function addNavigation($url)
	{
		if(!isset($_SESSION['user'])) {
			$this->defaultPage = str_replace("{{NAVIGATION}}",'',$this->defaultPage);

		} else {
			$this->defaultPage = str_replace("{{NAVIGATION}}",$this->getTopNav($url),$this->defaultPage);
		}
	}
	
	public function getTopNav($url)
	{
		// items
		$items = [
			'accueil' => '<li><a href="/">Accueil</a></li>',
			'login'   => ((!isset($_SESSION['user']) && ($url != 'login' && $url != 'profile')) ? '<li><a href="/login">login</a></li>' : ''),
			'out'   => ((isset($_SESSION['user']) && ($url != 'out')) ? '<li><a href="/out">Out</a></li>' : ''),
			'listpc'   => ((isset($_SESSION['user']) && ($url != 'listpc')) ? '<li><a href="/listpc">List Pc</a></li>' : ''),
			'listeleves'   => ((isset($_SESSION['user']) && ($url != 'listeleves')) ? '<li><a href="/listeleves">List Élèves</a></li>' : ''),
			'timeline'   => ((isset($_SESSION['user']) && ($url != 'timeline')) ? '<li><a href="/timeline">Timeline</a></li>' : ''),
			// 'profile' => ((isset($_SESSION['user']) && $url != 'profile') ? '<li><a href="/profile">Profile</a></li>' : ''),
			'logout'  => ((isset($_SESSION['user'])) ? '<li class="deco" ><a class="deco" href="/logout">Déconnexion</a></li>' : ''),
			// 'pseudo'  => (isset($_SESSION['user']) && isset($_SESSION['user']['pseudo'])) ? '<li><a>['.$_SESSION['user']['pseudo'].']</a></li>' : ''
		];
	
		$menuItems = '';
	
		foreach ($items as $key => $value) {
			$menuItems .= $value;
		}

		return $menuItems;
	}
}
