<?php

    namespace app\controllers;

    class FrontController
    {
        public function showIndex()
        {
            $content = [
                'TITLE' => "Accueil title",
                'CONTENT'   => "Page Accueil"
            ];











            return $content;
        }

    }
