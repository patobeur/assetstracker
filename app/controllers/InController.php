<?php
	namespace app\controllers;
	
	class InController {
		private $pdo;
		private $CheckDb;
		private $eleve = null;
		private $pc = null;
		private $messages = [];
		private $messagepc = '';
		private $lastClientDatas = [];
		private $lastLastTimeline = [];	
		private $lastPcDatas = [];	
		private $contents = [
			'CONTENT'=> '',
			'TITLE'=> 'Page Login',
			'Redirect'=> false
		];	
	
		public function __construct($CheckDb=false) {
			if($CheckDb){
				$this->CheckDb = $CheckDb;
				$this->pdo = $this->CheckDb->getPdo();
			}
		}
		
		// Gérer le traitement de connexion
		public function handle(): array{
			$this->messages = [];
			

                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $token = $_POST['csrf_token'] ?? '';
                                if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                                        $this->messages[]=["content"=>"Token CSRF invalide","result"=>"error"];
                                } else {
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
                                                                $this->messages[]=["content"=>"BarreCode PC introuvable !","result"=>"error"];
                                                        }
                                                }
                                        }

                                        if($this->pc){


					// qui a réservé ce pc dans la derniere action ?
					$this->lastPcDatas = $this->getLastTimelineBy($this->pc['id'],'out');


					$lpd = $this->lastPcDatas;


					
					if($lpd && count($lpd)> 0){
$this->messagepc .= "<div>Pc N°".htmlspecialchars($lpd[0]['idpc'], ENT_QUOTES, 'UTF-8')." loué le: ".htmlspecialchars($lpd[0]['birth'], ENT_QUOTES, 'UTF-8')."</div>";
$this->messagepc .= "<div>Model:".htmlspecialchars($this->pc['model'], ENT_QUOTES, 'UTF-8')."</div>";
$this->messagepc .= "<div>état: ".htmlspecialchars($this->pc['etat'], ENT_QUOTES, 'UTF-8')."</div>";
$this->messagepc .= "<div>Par ideleve: ".htmlspecialchars($lpd[0]['ideleves'], ENT_QUOTES, 'UTF-8')."</div>";

						// qui était locataire by id
						$this->lastClientDatas = $this->getClientById($lpd[0]['ideleves']) ;
						$lcd = $this->lastClientDatas;
						if($lpd && count($lpd)> 0){
$this->messagepc .= "<div>lastClientDatas: ".htmlspecialchars($lcd[0]['id'], ENT_QUOTES, 'UTF-8')."</div> ";
$this->messagepc .= "<div>nom prenom: ".htmlspecialchars($lcd[0]['nom'], ENT_QUOTES, 'UTF-8')." ".htmlspecialchars($lcd[0]['prenom'], ENT_QUOTES, 'UTF-8')."</div>";
$this->messagepc .= "<div>[".htmlspecialchars($lcd[0]['classe'], ENT_QUOTES, 'UTF-8')."</div> ";
$this->messagepc .= "<div>".htmlspecialchars($lcd[0]['promo'], ENT_QUOTES, 'UTF-8')."]</div>";
							$this->messagepc .= '<img src="/vendor/feunico/svg/profile.svg" style="width:100px">';
						}

						
						// renregistrement dans Timeline
						$insertRespons = $this->insertTimeline($this->pc['id'],$lpd[0]['ideleves'], 'in',$this->pc,$lcd[0]);
					}
					else {
						$insertRespons = $this->insertTimeline($this->pc['id'],null, 'in',$this->pc);
					}


					$this->messages[] = $insertRespons
						? ["content"=>"ENREGISTREMENT OK !","result"=>"succes"]
						: ["content"=>"ENREGISTREMENT Raté  !","result"=>"alerte"];

					$id = $last[0]['id'] ?? '';				
                                        $this->contents['Redirect'] = [
                                                'url'=> '/in?last='.$id,
                                                'refresh'=> CONFIG['REFRESH']['in']
                                        ];
                                }
                        }
                        }

                        $html = $this->renderView();
			
			$this->contents = [
				'CONTENT'=> $html,
				'TITLE'=> 'Page Login',
				'Redirect'=> $this->contents['Redirect'] ?? false,
			];
			
			return $this->contents;
		}
	
		/**
		 * Fonction pour avoir une seule réponse
		 */
		public function getClientById($eleveId=false): array{
			$respons = [];
			if($eleveId){
				try {
					$query = "SELECT * FROM eleves WHERE id = :id LIMIT 1";
					$stmt = $this->pdo->prepare($query);
					$stmt->bindParam(':id', $eleveId, \PDO::PARAM_INT);
					$stmt->execute();
					$respons = $stmt->fetchAll(\PDO::FETCH_ASSOC);
				} catch (\PDOException $e) {
					die("Erreur de connexion ou de création de la base de données : " . $e->getMessage());
				} catch (\Exception $e) {
					die("Erreur : " . $e->getMessage());
				}
			}
			return $respons;
		}
		/**
		 * Fonction pour avoir une seule réponse
		 * 
		 */
		public function getLastTimelineBy($idpc=false,$typeaction=false): array{
			$respons = [];
			if($idpc && $typeaction){
				try {
					$query = "SELECT * FROM timeline WHERE idpc=:idpc AND typeaction=:typeaction ORDER BY id DESC LIMIT 1";
					$stmt = $this->pdo->prepare($query);
					$stmt->bindParam(':idpc', $idpc, \PDO::PARAM_INT);
					$stmt->bindParam(':typeaction', $typeaction, \PDO::PARAM_STR);
					$stmt->execute();
					$respons = $stmt->fetchAll(\PDO::FETCH_ASSOC);
				} catch (\PDOException $e) {
					die("Erreur de connexion ou de création de la base de données : " . $e->getMessage());
				} catch (\Exception $e) {
					die("Erreur : " . $e->getMessage());
				}
			}
			return $respons;
		}
		/**
		 * Fonction pour avoir la dernière Timeline
		 */
		public function getLastTimeLine($table=null,$cols=null): array{ 
			$last = [];
			if($table && $cols){
				try {
					if($table){
						$query = "SELECT {$cols} FROM {$table} ORDER by id DESC LIMIT 1";
						$stmt = $this->pdo->prepare($query);
						$stmt->execute();
						$last = $stmt->fetchAll(\PDO::FETCH_ASSOC);
					}
					return $last;
				} catch (\PDOException $e) {
					die("Erreur de connexion à la base de données : " . $e->getMessage());
				} catch (\Exception $e) {
					die("Erreur de connexion à la base de données : " . $e->getMessage());
				}
			}
			return $last;
		}		
		public function insertTimeline($idpc, $ideleves=null, $typeaction, $pc=false, $eleve=false) { 
			
			$idpc = ($pc && $pc['id']) ? $pc['id'] : false;			
			$ideleves = ($eleve && $eleve['id']) ? $eleve['id'] : null;
			if(($ideleves && $idpc) || ($ideleves===null && $idpc) ) {
				try {
					$birth = date(format: "y-m-d H:i:s");
					$query = "INSERT INTO timeline (idpc, ideleves, typeaction, birth) VALUES (:idpc, :ideleves, :typeaction, :birth)";
					$stmt = $this->pdo->prepare($query);
					$stmt->bindParam(':ideleves', $ideleves, \PDO::PARAM_STR);
					$stmt->bindParam(':idpc', $idpc, \PDO::PARAM_STR);
					$stmt->bindParam(':typeaction', $typeaction, \PDO::PARAM_STR);
					$stmt->bindParam(':birth', $birth, \PDO::PARAM_STR);
					$stmt->execute(); 
					$this->CheckDb->Console->addMsgSESSION([
						"content"=>($typeaction==='in')?"{$eleve['prenom']} {$eleve['nom']} {$eleve['classe']}{$eleve['promo']} rend PC {$pc['id']}{$pc['barrecode']}":"Élève {$ideleves} emprunte PC {$idpc}",
						"title"=>($typeaction==='in')?'➡️':'⬅️',
						"class"=>'',
						"birth"=>$birth
					]);
					
					$used = ($typeaction==='out') ? (int)$this->pc['used']+1 : false;

					$this->CheckDb->setPcPosition($ideleves, $idpc, $typeaction, $used, $birth);
					$this->CheckDb->setEleveLastpcid($ideleves, $idpc, $typeaction, $birth);
	
				} catch (\PDOException $e) {
					die("Erreur d'enregistrement des données : " . $e->getMessage());
				} catch (\Exception $e) {
					die("Erreur d'enregistrement des données : " . $e->getMessage());
				}
				return true;
			}
			return false;
		}

		// Afficher la vue login avec les erreurs
                private function renderView(): string {
                        $vars = [
                                'csrf_token' => htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'),
                                'errors' => '',
                                'msgpc' => '',
                                'pcbarrecode' => ''
                        ];

                        if (!empty($this->messages)) {
                                foreach ($this->messages as $error) {
                                        $content = $error['content'];
                                        $result = $error['result'];
                                        $vars['errors'] .= '<p class="'.$result.'">' . htmlspecialchars($content, ENT_QUOTES, 'UTF-8') . '</p>';
                                }
                        }

                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                if ($this->pc) {
                                        $vars['msgpc'] = $this->messagepc;
                                        $vars['pcbarrecode'] = htmlspecialchars($this->pc['barrecode'], ENT_QUOTES, 'UTF-8');
                                }
                        }

                        return View::render('in.php', $vars);
                }
	}