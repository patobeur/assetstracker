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
                                $token = $_POST['csrf_token'] ?? '';
                                if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                                        $this->messages[] = ["content"=>"Token CSRF invalide","result"=>"error"];
                                } else {
                                        $this->eleve = null;
                                        $this->pc = null;

                                        if (!empty($_POST['eleve'])){
                                                $memberBarrecode = isset($_POST['eleve']) ? trim($_POST['eleve']) : '';
                                                $memberBarrecode = filter_var($memberBarrecode, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                if (!empty($memberBarrecode)) {
                                                        // recherche du barrecode dans eleve
                                                       $row = $this->CheckDb->once('eleves',$memberBarrecode);
                                                       if(isset($row['error'])) {
                                                               $this->messages[]=["content"=>$row['error'],"result"=>"error"];
                                                       }
                                                       elseif(count($row)===1) {
                                                               $this->eleve = $row[0];
                                                       }
                                                       else {
                                                               $this->messages[]=["content"=>"BarreCode élève introuvable !","result"=>"error"];
                                                       }
                                                }
                                        }
                                        if (!empty($_POST['pc'])){
                                                $assetBarrecode = $_POST['pc'] ?? '';
                                                $assetBarrecode = filter_var($assetBarrecode, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                                                if (!empty($assetBarrecode)) {
                                                        // recherche du barrecode dans eleve
                                                       $row = $this->CheckDb->once('pc',$assetBarrecode);
                                                       if(isset($row['error'])) {
                                                               $this->messages[]=["content"=>$row['error'],"result"=>"error"];
                                                       }
                                                       elseif(count($row)===1) {
                                                               $this->pc = $row[0];
                                                       }
                                                       else {
                                                               $this->messages[]=["content"=>"BarreCode PC introuvable !","result"=>"alerte"];
                                                       }
                                                }
                                        }

                                        if($this->pc && $this->eleve ){
                                                $insertRespons = $this->insertTimelineOut($this->pc['id'], $this->eleve['id'], 'out') ;
                                                $this->messages[] = $insertRespons
                                                        ?["content"=>"ENREGISTREMENT OK !","result"=>"succes"]
                                                        :["content"=>"ENREGISTREMENT Raté  !","result"=>"succes"];
                                        }
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
					'refresh'=> CONFIG['REFRESH']['out']
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

		public function insertTimelineOut($idpc, $ideleves=null, $typeaction) { 
			
			if(($ideleves && $idpc) || (gettype($ideleves)==='null' && $idpc) ) {
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
						"content"=>"Élève {$ideleves} et PC {$idpc}",
						"title"=>'⬅️',
						"class"=>'',
						"birth"=>$birth
					]);
					
					$used = (int)$this->pc['used']+1;
					$this->CheckDb->setPcPosition($ideleves, $idpc, $typeaction, $used, $birth);
					$this->CheckDb->setEleveLastpcid($ideleves, $idpc, $typeaction, $birth);
	
				} catch (\PDOException $e) {
					die("insertTimelineOut: Erreur d'enregistrement des données : " . $e->getMessage());
				} catch (\Exception $e) {
					die("insertTimelineOut: Erreur d'enregistrement des données : " . $e->getMessage());
				}
				return true;
			}
			return false;
		}
	
		// Afficher la vue login avec les erreurs
                private function renderView(): string {
                        $vars = [
                                'csrf_token' => htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'),
                                'msgeleve' => '',
                                'msgpc' => '',
                                'errors' => '',
                                'pcbarrecode' => '',
                                'elevebarrecode' => ''
                        ];

                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                if ($this->eleve) {
                                        $vars['msgeleve'] = htmlspecialchars($this->eleve['prenom'], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($this->eleve['nom'], ENT_QUOTES, 'UTF-8') . "<br>";
                                        $vars['elevebarrecode'] = htmlspecialchars($this->eleve['barrecode'], ENT_QUOTES, 'UTF-8');
                                }
                                if ($this->pc) {
                                        $vars['msgpc'] = htmlspecialchars($this->pc['barrecode'], ENT_QUOTES, 'UTF-8');
                                        $vars['pcbarrecode'] = htmlspecialchars($this->pc['barrecode'], ENT_QUOTES, 'UTF-8');
                                }
                                if (!empty($this->messages)) {
                                        foreach ($this->messages as $error) {
                                                $content = $error['content'];
                                                $result = $error['result'];
                                                $vars['errors'] .= '<p class="'.$result.'">' . htmlspecialchars($content, ENT_QUOTES, 'UTF-8') . '</p>';
                                        }
                                }
                        }

                        return View::render('out.php', $vars);
                }
	}