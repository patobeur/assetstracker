<?php
    namespace app\controllers;

    class LoginController
    {
        private $content = [];
        public function login()
        {
            
            $this->content['CONTENT'] = file_get_contents('../app/views/login.php');


            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';
                
                // if (isset($error)):  <p class="error">errors</p>

                if ($username === 'admin' && $password === 'admin') {
                    $_SESSION['user'] = $username;
                    header('Location: /profile');
                    // die();
                } else {
                    $this->content['CONTENT'] = str_replace("#ERRORS#",'<p class="error">Invalid credentials</p>',$this->content['CONTENT']);
                }
            } else {
                $this->content['CONTENT'] = str_replace("#ERRORS#","",$this->content['CONTENT']);
            }
            $this->content['TITLE'] = 'Page Login';
            
            return $this->content;
        }

        public function logout()
        {
            // session_start();
            session_destroy();
            header('Location: /login');
        }
    }
