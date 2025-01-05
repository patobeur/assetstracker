<?php
	namespace app\controllers;
	
	class InController {
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
		public function handle(): array{
			$this->messages = [];
			if ($_SERVER['REQUEST_METHOD'] === 'GET') {
				if(isset($_GET['a'])) {
					// die($_GET['a']);
				}
			}

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$this->pc = null;

				if (!empty($_POST['pc'])){
					$assetBarrecode = $_POST['pc'] ?? '';
					$assetBarrecode = filter_var($assetBarrecode, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
					if (!empty($assetBarrecode)) {
						// recherche du barrecode dans eleve
						$row = $this->CheckDb->once('pc',$assetBarrecode);
						if(count($row)===1) {
							$this->pc = $row[0];
						}
						else {
							$this->messages[]=["content"=>"BarreCode PC introuvable !","result"=>"alerte"];
						}
					}
				}

				if($this->pc){
					$insertRespons = $this->CheckDb->insertTimelineIn($this->pc['id'], 'in') ;
					$this->messages[]=$insertRespons?["content"=>"ENREGISTREMENT OK !","result"=>"succes"]:["content"=>"ENREGISTREMENT Raté  !","result"=>"alerte"];

				}
			}
			if($this->pc ){
				$html = $this->renderView();
				
				$contents = [
					'CONTENT'=> $html,
					'TITLE'=> 'Page Login'
				];
				$contents['Redirect'] = [
					'url'=> '/in?',
					'refresh'=> CONFIG['WEBSITE']['refreshOut']
				];
			}
			else {
				$html = $this->renderView();
				
				$contents = [
					'CONTENT'=> $html,
					'TITLE'=> 'Page Login'
				];
			}

			return $contents;
		}
	
		// Afficher la vue login avec les erreurs
		private function renderView(): string {
			$html = file_get_contents(filename: '../app/views/in.php');
			$messageeleve = "";
			$messagepc = "";
			$messages = '';			
	
			
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {

				if($this->pc){
					$messagepc .= $this->pc['barrecode'];
					$html = str_replace('{{msgpc}}', $this->pc['barrecode'], $html);
					$html = str_replace('{{pcbarrecode}}', $this->pc['barrecode'], $html);
				}
				else {
					$html = str_replace('{{pcbarrecode}}', '', $html);
					$html = str_replace('{{msgpc}}', '', $html);
				}


				// Ajouter les erreurs
				if (!empty($this->messages)) {
					foreach ($this->messages as $error) {
						$content = $error['content'];
						$result = $error['result'];
						$messages .= '<p class="'.$result.'">' . htmlspecialchars($content, ENT_QUOTES, 'UTF-8') . "</p>";
					}
					$html = str_replace('{{errors}}', $messages, $html);
				}
				else {
					$html = str_replace('{{errors}}', '', $html);
				}
			}
			else {
				$html = str_replace('{{msgpc}}', '', $html);
				$html = str_replace('{{errors}}', '', $html);
				$html = str_replace('{{pcbarrecode}}', '', $html);
			}


			return $html;
		}
	}