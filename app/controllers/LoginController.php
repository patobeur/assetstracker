<?php
    namespace app\controllers;
    
    class LoginController {
        private $pdo;
    
        public function __construct($CheckDb) {
            $this->pdo=$CheckDb->getPdo();
            // $this->initDBConnection();
        }
    
        // // Initialiser la connexion à la base de données
        // private function initDBConnection() {
        //     try {
        //         // var_dump($this->pdo);
        //     } catch (\PDOException $e) {
        //         die("Erreur de connexion à la base de données : " . $e->getMessage());
        //     }
        // }
    
        // Gérer le traitement de connexion
        public function handleLogin() {
            $errors = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = $_POST['username'] ?? null;
                $password = $_POST['password'] ?? null;
        
                // Validation des champs
                if (empty($username) || empty($password)) {
                    $errors[] = "Tous les champs doivent être remplis.";
                }
        
                if (empty($errors)) {
                    $query = "SELECT * FROM administrateurs WHERE pseudo = :username LIMIT 1";
                    $stmt = $this->pdo->prepare($query);
                    $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
                    $stmt->execute();
                    $admin = $stmt->fetch(\PDO::FETCH_ASSOC);
                    if ($admin && password_verify($password, $admin['motdepasse'])) {
                        // Authentification réussie
                        $_SESSION['user'] = [
                            'id' => $admin['id'],
                            'pseudo' => $admin['pseudo'],
                            'nom' => $admin['nom'],
                            'prenom' => $admin['prenom']
                        ];
                        header("Location: /profile");
                        exit;
                    } else {
                        $errors[] = "Pseudo ou mot de passe incorrect.";
                    }
                }
        
                // Si des erreurs existent, renvoyer à la vue
            }
                return $this->renderView($errors);
        }
    
        // Afficher la vue login avec les erreurs
        private function renderView($errors = []) {
            $html = file_get_contents('../app/views/login.php');
    
            // Ajouter les erreurs
            $errorHtml = '';
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $errorHtml .= "<p class='error'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>";
                }
            }
    
            $html = str_replace('{{errors}}', $errorHtml, $html);
            print_r('k---------------------------------k',$html);
            
            return [
                'CONTENT'=> $html,
                'TITLE'=> 'Page Login'
            ];
        }
        
        public function logout()
        {
            // session_start();
            session_destroy();
            header('Location: /login');
        }
    }