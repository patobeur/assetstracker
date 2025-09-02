<?php
        namespace app\controllers;

        class ProfileController
        {
                private $view = '';

                private $pdo;
                private $CheckDb;
                private $contents = [
                        'TITLE'=> 'Page Profil',
                        'CONTENT'=> ''
                ];

                public function __construct($CheckDb) {
                        if($CheckDb){
                                $this->CheckDb = $CheckDb;
                        }
                        $this->view = file_get_contents('../app/views/profile.php');
                }

                public function showProfile()
                {
                        $this->pdo = $this->CheckDb->getPdo();
                        if (!isset($_SESSION['user'])) {
                                header('Location: /login');
                        } else {

                                $content = str_replace("{{TITLE}}",$this->contents['TITLE'],$this->view);
                                $profileHtml = 'une erreur sans doute ?';
                                $row = $this->getProfilRow();
                                if ($row && count($row) > 0){
                                        $profileHtml = "
                                        <div class=\"form-container\">
                                        <h3>".htmlspecialchars($_SESSION['user']['pseudo'], ENT_QUOTES, 'UTF-8')."</h3>
                                                <p>Pseudo: ".htmlspecialchars($row['pseudo'], ENT_QUOTES, 'UTF-8')."</p>
                                                <p>Nom: ".htmlspecialchars($_SESSION['user']['nom'], ENT_QUOTES, 'UTF-8')."</p>
                                                <p>Prénom: ".htmlspecialchars($_SESSION['user']['prenom'], ENT_QUOTES, 'UTF-8')."</p>
                                                <p>account: ".htmlspecialchars($_SESSION['user']['typeaccount'], ENT_QUOTES, 'UTF-8')." (lv:".htmlspecialchars($_SESSION['user']['typeaccount_id'], ENT_QUOTES, 'UTF-8').")</p>
                                                <p>Création: ".htmlspecialchars($row['birth'], ENT_QUOTES, 'UTF-8')."</p>
                                                <p>mail: <a href=\"mailto:".htmlspecialchars($row['mail'], ENT_QUOTES, 'UTF-8')."\">M'envoyer un mail ?</a></p>
                                        </div>";
                                }


                                $content = str_replace("{{CONTENT}}",$profileHtml, $content);



                                $this->contents['CONTENT'] = $content;
                                return $this->contents;
                        }
                }
                private function  getProfilRow(){
                        if(!$this->pdo) return false;
                        try{
                                $id = $_SESSION['user']['id'];
                                $sessionkey = $_SESSION['user']['sessionkey'];
                                $select = 'pseudo, nom, prenom, birth, mail';
                                $query = "SELECT ".$select." FROM administrateurs WHERE id = :id AND sessionkey = :sessionkey LIMIT 1";

                                $stmt = $this->pdo->prepare($query);
                                $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
                                $stmt->bindParam(':sessionkey', $sessionkey, \PDO::PARAM_STR);
                                $stmt->execute();
                                $row = $stmt->fetch(\PDO::FETCH_ASSOC);
                                if ($row && count($row) > 0){
                                        // on a une réponse
                                        return $row;
                                }
                        }
                        catch (\PDOException $e) {
                                die("getProfilRow : Erreur de connexion à la base de données : " . $e->getMessage());
                        } catch (\Exception $e) {
                                die("getProfilRow : Erreur de deconnexion : " . $e->getMessage());
                        }
                        return false;
                }
        }
