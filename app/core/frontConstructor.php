<?php
namespace app\core;
use app\core\navigation;

class FrontConstructor
{
	private $pageToDisplay;
	private $Console;
	private $Navigation;

	public function __construct($Console) {

		$this->Navigation = new Navigation();
		$this->Console = $Console;
		$this->pageToDisplay = file_get_contents(filename: '../app/views/front.php');
	}

	public function addContent($stack=[]): void
	{
		if (count($stack)>0){
			$contents = '';
			for ($i=0; $i < count($stack) ; $i++) {
				$contents.="#CONTENT".$i."#";
			}
			$this->pageToDisplay = str_replace("{{CONTENTS}}",$contents,$this->pageToDisplay);

			for ($i=0; $i < count($stack) ; $i++) {
				$this->pageToDisplay = str_replace("#CONTENT".$i."#",$stack[$i]['CONTENT'],$this->pageToDisplay);
			}

			$this->pageToDisplay = str_replace("{{TITLE}}",$stack[0]['TITLE'],$this->pageToDisplay);
		}
		else {
			$this->pageToDisplay = str_replace("{{CONTENT}}",'vide',$this->pageToDisplay);
			$this->pageToDisplay = str_replace("{{TITLE}}",'default',$this->pageToDisplay);
		}
	}

	public function addConsole(): void
	{
		$this->pageToDisplay = $this->Console->addConsole($this->pageToDisplay);
	}

	public function getPageToDisplay($url,$stack=[]): string
	{
		// $this->addNavigation(url: $url);
		
		$this->addContent(stack: $stack);
		$this->addBodyBackground(url: $url);

		$this->addConsole();
		$this->pageToDisplay = $this->Navigation->addNavigation($this->pageToDisplay, $url);

		return $this->pageToDisplay;
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
		$this->pageToDisplay = str_replace( "{{background}}", $defaultStyle, $this->pageToDisplay);
	}
}
