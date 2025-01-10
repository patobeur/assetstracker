<?php
	namespace app\controllers;
	
	class LoginController {
		private $pdo;
		private $CheckDb;
	
		public function __construct($CheckDb=false) {
			if($CheckDb){
				$this->CheckDb=$CheckDb;
				$this->pdo = $this->CheckDb->getPdo();
			}
		}
		
		// Gérer le traitement de connexion
		public function handleLogin() {
			$errors = [];
			if(!$this->pdo){
				$errors[] = "Pas de connexion à la bdd !<br/>Merci de patienter !";
			}
			else {
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
					// Récupération et validation des données d'entrée
					$username = isset($_POST['username']) ? trim($_POST['username']) : '';
					$password = isset($_POST['password']) ? $_POST['password'] : '';
	
					// Valider le nom d'utilisateur pour éviter des caractères non valides
					$username = filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
					
					if (!empty($username) && !empty($password)) {
						$errors=$this->CheckDb->login($username,$password);
					}
				}
			}            
			return $this->renderView($errors);
		}
	
		// Afficher la vue login avec les erreurs
		private function renderView($errors = []) {
			$html = file_get_contents('../app/views/login.php');
			$htmlform = file_get_contents('../app/views/loginForm.php');
	
			// Ajouter les erreurs
			$errorHtml = '';
			
			if (!empty($errors)) {
				foreach ($errors as $error) {
					$errorHtml .= "<p class='error'>" . $error . "</p>";
				}
			}
	
			$html = str_replace('{{errors}}', $errorHtml, $html);
			if($this->pdo){
				$html = str_replace('{{loginform}}', $htmlform, $html);
			}
			else {
				$html = str_replace('{{loginform}}', '', $html);
			}
			return [
				'CONTENT'=> $html,
				'TITLE'=> 'Page Login',
			];
		}
		
		public function logout()
		{
			session_destroy();
			header('Location: /login');
		}
	}