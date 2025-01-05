<?php
	namespace app\controllers;
	
	class OutController {
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
				$this->eleve = null;
				$this->pc = null;

				if (!empty($_POST['eleve'])){
					$memberBarrecode = isset($_POST['eleve']) ? trim($_POST['eleve']) : '';
					$memberBarrecode = filter_var($memberBarrecode, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
					if (!empty($memberBarrecode)) {
						// recherche du barrecode dans eleve
						$row = $this->CheckDb->once('eleves',$memberBarrecode);
						if(count($row)===1) {
							$this->eleve = $row[0];
						}
						else {
							$this->messages[]=["content"=>"BarreCode élève introuvable !","result"=>"alerte"];
						}
					}
				}
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

				if($this->pc && $this->eleve ){
					$insertRespons = $this->CheckDb->insertTimeline($this->pc['id'], $this->eleve['id'], 'out') ;
					$this->messages[]=$insertRespons?["content"=>"ENREGISTREMENT OK !","result"=>"succes"]:["content"=>"ENREGISTREMENT Raté  !","result"=>"succes"];

				}
			}
			if($this->pc && $this->eleve ){
				$html = $this->renderView();
				
				$contents = [
					'CONTENT'=> $html,
					'TITLE'=> 'Page Login'
				];
				$contents['Redirect'] = [
					'url'=> '/out?',
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
			$html = file_get_contents(filename: '../app/views/out.php');
			$messageeleve = "";
			$messagepc = "";
			$messages = '';			
	
			
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				if($this->eleve){
					$messageeleve .= $this->eleve['barrecode'];
					$html = str_replace('{{msgeleve}}', $this->eleve['prenom']." ".$this->eleve['nom'], $html);
					$html = str_replace('{{elevebarrecode}}', $this->eleve['barrecode'], $html);
				}
				else {
					$html = str_replace('{{elevebarrecode}}', '', $html);
					$html = str_replace('{{msgeleve}}', '', $html);
				}

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
				$html = str_replace('{{msgeleve}}', '', $html);
				$html = str_replace('{{errors}}', '', $html);
				$html = str_replace('{{pcbarrecode}}', '', $html);
				$html = str_replace('{{elevebarrecode}}', '', $html);
			}


			return $html;
		}
	}