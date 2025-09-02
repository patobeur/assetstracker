<?php

        namespace app\controllers;

        use app\core\View;

        class NotFoundController
	{

		private $boule = '';
		private $contents = [
			1=> [
				'TITLE' => "c'est pourquoi ?",
				'CONTENT'   => "404 - Page not found"
			],
			2=> [
				'TITLE' => "c'est ou ?",
				'CONTENT'   => 'Unknow Page'
			]
		];
		private $content = 	[];
		
		public function showIndex($boule=1)
		{	
			$this->content = $this->contents[$boule];
			$this->renderView();
			return $this->content;
		}
		// Afficher la vue login avec les erreurs
                private function renderView(){
                        $this->content['CONTENT'] = View::render('notfound.php', [
                                'TITLE' => $this->content['TITLE'],
                                'CONTENT' => $this->content['CONTENT']
                        ]);
                }
        }
