<?php
	namespace app\controllers;
	
	class InOutController {
		private $pdo;
		private $CheckDb;
		private $eleve = null;
		private $pc = null;
		private $messages = [];
	
		public function __construct($CheckDb=false) {
			if($CheckDb){
				$this->CheckDb=$CheckDb;
				$this->pdo = $this->CheckDb->getPdo();
			}
		}
		
		// Gérer le traitement de connexion
		public function handleOut() {
			$this->messages = [];
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$this->eleve = null;
				$this->pc = null;

				if (!empty($_POST['eleve'])){
					$memberBarrecode = isset($_POST['eleve']) ? trim($_POST['eleve']) : '';
					$memberBarrecode = filter_var($memberBarrecode, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
					if (!empty($memberBarrecode)) {
						// recherche du barrecode dans eleve
						$row = $this->CheckDb->eleveOnce('eleves',$memberBarrecode);
						if(count($row)===1) {
							$this->eleve = $row[0];
						}
						else {
							$this->messages[]="BarreCode élève introuvable !";
						}
					}
				}
				if (!empty($_POST['pc'])){
					$assetBarrecode = isset($_POST['pc']) ? $_POST['pc'] : '';
					$memberBarrecode = filter_var($assetBarrecode, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
					if (!empty($memberBarrecode)) {
						// recherche du barrecode dans eleve
						$row = $this->CheckDb->eleveOnce('pc',$memberBarrecode);
						if(count($row)===1) {
							$this->pc = $row[0];
						}
						else {
							$this->messages[]="BarreCode PC introuvable !";
						}
					}
				}

			}
			
			return $this->renderView();
		}
	
		// Afficher la vue login avec les erreurs
		private function renderView() {
			$html = file_get_contents('../app/views/out.php');
			$messageleve = "";
			$messagepc = "";
	
			
			if($this->eleve){
				$messageleve .= $this->eleve['barrecode'];
				$html = str_replace('{{msgeleve}}', $this->eleve['barrecode'], $html);
				$html = str_replace('{{elevebarrecode}}', $this->eleve['barrecode'], $html);
			}
			else {
				$html = str_replace('{{elevebarrecode}}', '', $html);
			}

			if($this->pc){
				$messageleve .= $this->eleve['barrecode'];
				$html = str_replace('{{msgpc}}', $this->pc['barrecode'], $html);
				$html = str_replace('{{pcbarrecode}}', $this->pc['barrecode'], $html);
			}
			else {
				$html = str_replace('{{pcbarrecode}}', '', $html);
			}

			if($this->pc && $this->eleve ){
				$this->messages[]="ENREGISTREMENT OK !";
			}

			// Ajouter les erreurs
			$messages = '';			
			if (!empty($this->messages)) {
				foreach ($this->messages as $error) {
					$messages .= "<p class='sucess'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>";
				}
			}	
			$html = str_replace('{{errors}}', $messages, $html);


			$content = [
				'CONTENT'=> $html,
				'TITLE'=> 'Page Login'
			];
			if($this->pc && $this->eleve ){
				$content['Redirect'] = [
					'url'=> '/out',
					'refresh'=> 5
				];
			}


			return $content;
		}
		
		public function logout()
		{
			session_destroy();
			header('Location: /login');
		}
	}