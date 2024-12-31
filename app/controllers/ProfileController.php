<?php
    namespace App\Controllers;

    class ProfileController
    {
        private $content = [];

        public function __construct() {
            $this->content = [
                'VUE'=> file_get_contents('../app/views/profile.php'),
                'TITLE'=> 'Page Profil',
                'CONTENT'=> ''
            ];
        }
        
        public function showProfile()
        {
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
            } else {             
                $this->content['CONTENT'] = str_replace("{{CONTENT}}","<h3>".$_SESSION['user']['pseudo']."<h3>",$this->content['VUE']);
                return $this->content;
            }
        }
    }