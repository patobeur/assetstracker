<?php
    namespace app\controllers;
    
    class LoginController {
        private $pdo;
        private $CheckDb;
    
        public function __construct($CheckDb) {
            $this->CheckDb=$CheckDb;
            $this->pdo=$this->CheckDb->getPdo();
        }
        
        // Gérer le traitement de connexion
        public function handleLogin() {
            $errors = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = $_POST['username'] ?? null;
                $password = $_POST['password'] ?? null;
                $username = trim($username);
                $password = trim($password);
                // Validation des champs
                if (empty($username) || empty($password)) {
                    $errors=$this->CheckDb->login($username,$password);
                }
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
            
            return [
                'CONTENT'=> $html,
                'TITLE'=> 'Page Login'
            ];
        }
        
        public function logout()
        {
            session_destroy();
            header('Location: /login');
        }
    }