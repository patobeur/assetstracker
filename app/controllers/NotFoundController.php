<?php

	namespace app\controllers;

	class NotFoundController
	{
		public function showIndex()
		{
			$content = [
				'TITLE' => "c'est pourquoi ?",
				'CONTENT'   => "404 - Page not found"
			];
			return $content;
		}

	}
