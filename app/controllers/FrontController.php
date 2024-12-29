<?php

	namespace app\controllers;

	class FrontController
	{
		public function showIndex()
		{
			$content = [
				'TITLE' => "Accueil title",
				'CONTENT'   => "<h1>Accueil</h1>"
			];
			return $content;
		}
	}