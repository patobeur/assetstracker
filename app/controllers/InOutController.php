<?php
	namespace app\controllers;
	
	class InOutController {
		private $pdo;
		private $CheckDb;
	
		public function __construct($CheckDb=false) {
			if($CheckDb){
				$this->CheckDb=$CheckDb;
				$this->pdo = $this->CheckDb->getPdo();
			}
		}
		
		// Gérer le traitement de connexion
		public function handleOut() {
			$errors = [];
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				if (!empty($_POST['eleves']) && !empty($_POST['pc'])){

					// Récupération et validation des données d'entrée
					$memberBarrecode = isset($_POST['eleves']) ? trim($_POST['eleves']) : '';
					$assetBarrecode = isset($_POST['pc']) ? $_POST['pc'] : '';

					// Valider le nom d'utilisateur pour éviter des caractères non valides
					$memberBarrecode = filter_var($memberBarrecode, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
					
					if (!empty($memberBarrecode) && !empty($assetBarrecode)) {
						// $errors=$this->CheckDb->eleveOnce($memberBarrecode);
						// $errors[]=$this->CheckDb->pcOnce($assetBarrecode);
					}
				}
			}
			
			return $this->renderView($errors);
		}
	
		// Afficher la vue login avec les erreurs
		private function renderView($errors = []) {
			$html = file_get_contents('../app/views/out.php');
	
			// Ajouter les erreurs
			$errorHtml = '';
			
			if (!empty($errors)) {
				foreach ($errors as $error) {
					$errorHtml .= "<p class='error'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>";
				}
			}
	
			$html = str_replace('{{errors}}', $errorHtml, $html);
			
			return [
				'CONTENT'=> $html,
				'TITLE'=> 'Page Login'
			];
		}
		
		public function logout()
		{
			session_destroy();
			header('Location: /login');
		}
	}