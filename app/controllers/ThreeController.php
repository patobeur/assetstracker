<?php
        namespace app\controllers;

        use app\core\View;

        class ThreeController {
		private $CheckDb;
		private $pdo;
		private $pcs = [];
		private $timeline = [];
		private $pcsJson = '{}';
		private $timelineJson = '{}';

		private $content = [
				'TITLE' => "Three",
				'CONTENT'   => 'ok',
				'Redirect'   => [
					'url'=> false,
					'refresh'=> false
				],
		];

		public function __construct($CheckDb) {
			$this->CheckDb=$CheckDb;
			$this->pdo=$CheckDb->getPdo();
		}
        
		public function go()
		{	
			$this->setPcsJson();
			$this->setTimelineJson();
			$this->renderView();
			return $this->content;
		}
		
               private function setPcsJson(){
                       $this->pcs = $this->list('pc','*');
                       if($this->pcs && count($this->pcs)>0) {
                               $this->pcsJson = json_encode(
                                       $this->pcs,
                                       JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                               );
                       }
               }
               private function setTimelineJson(){
                       $this->timeline = $this->list('timeline','*');
                       if($this->timeline && count($this->timeline)>0) {
                               $this->timelineJson = json_encode(
                                       $this->timeline,
                                       JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                               );
                       }
               }

               private function renderView(){
                       $this->content['CONTENT'] = View::render('three.php', [
                               'pcsJson' => $this->pcsJson,
                               'timelineJson' => $this->timelineJson
                       ]);

               }
		// BDD
		
		/**
		 * Fonction pour avoir la liste des Pc
		 */
		private function list($table=null,$cols=null): array{ 
			$respons = [];
			if($table && $cols){
				try {
					$pcs = []; 
					if($table){
						$query = "SELECT {$cols} FROM {$table} ORDER by birth DESC";
						$stmt = $this->pdo->prepare($query);
						$stmt->execute();
						$pcs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
					}
					return $pcs;
				} catch (\PDOException $e) {
					die("Erreur de connexion à la base de données : " . $e->getMessage());
				} catch (\Exception $e) {
					die("Erreur de connexion à la base de données : " . $e->getMessage());
				}
			}
			return $respons;
		}
		
    }