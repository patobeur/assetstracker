<?php
	namespace app\controllers;

	class ProfileController
	{
		private $view = '';
		private $contents = [
			'TITLE'=> 'Page Profil',
			'CONTENT'=> ''
		];

		public function __construct() {
			$this->view = file_get_contents('../app/views/profile.php');
		}
		
		public function showProfile()
		{
			if (!isset($_SESSION['user'])) {
				header('Location: /login');
			} else {
				
				$content = str_replace("{{TITLE}}",$this->contents['TITLE'],$this->view);

				$profileHtml = "<h3>".$_SESSION['user']['pseudo']."</h3>
				<div class=\"form-container\">
					<p>type : ".$_SESSION['user']['account']."</p>
					<p>prenom : ".$_SESSION['user']['prenom']."</p>
				</div>";

				$content = str_replace("{{CONTENT}}",$profileHtml, $content);



				$this->contents['CONTENT'] = $content;
				return $this->contents;
			}
		}
	}