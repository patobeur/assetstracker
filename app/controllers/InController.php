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

			
			if ($_SERVER['REQUEST_METHOD'] === 'GET') {
				if(isset($_GET['last']) && !empty($_GET['last'])) {
					$last = $this->CheckDb->onceTimeline('timeline','*',$_GET['last']);
					if($last) {
						// $this->messages[]=["content"=>"dernière loc !","result"=>"succes"];
					}
				}
			}

			$html = $this->renderView();
			
			$contents = [
				'CONTENT'=> $html,
				'TITLE'=> 'Page Login'
			];
			

			if($this->pc ){
				$last = $this->CheckDb->lastTimeline('timeline','*');
				$id = $last[0]['id'] ?? '';				
				$contents['Redirect'] = [
					'url'=> '/in?last='.$id,
					'refresh'=> CONFIG['REFRESH']['in']
				];
			}



			

			return $contents;
		}
	
		// Afficher la vue timeline dernière action reçus
		private function addLastTimeline(): string {
			//
		}
	
		// Afficher la vue login avec les erreurs
		private function renderView(): string {
			$html = file_get_contents(filename: '../app/views/in.php');
			$messageeleve = "";
			$messagepc = "";
			$messages = '';			
	
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


			}
			else {
				$html = str_replace('{{msgpc}}', '', $html);

				


				// $html = str_replace('{{errors}}', '', $html);
				$html = str_replace('{{pcbarrecode}}', '', $html);
			}


			return $html;
		}
	}