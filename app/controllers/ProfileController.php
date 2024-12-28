<?php
    // ProfileController.php
    namespace App\Controllers;

    class ProfileController
    {
        private $content = [];
        public function showProfile()
        {
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
            } else {
                $this->content['CONTENT'] = file_get_contents('../app/views/profile.php');
                $this->content['TITLE'] = 'Page Profil';

                
                $this->content['CONTENT'] = str_replace("#CONTENT#","<h3>".$_SESSION['user']."<h3>",$this->content['CONTENT']);

                return $this->content;











                // include '../app/views/profile.php';
            }
        }
    }