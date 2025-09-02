<?php
        namespace app\controllers;

        use app\core\View;

        class InterfaceController {
		private $content = [
				'TITLE' => "Interface",
				'CONTENT'   => ''
		];

		public function __construct() {
		}
        
		public function interfaceHandler($boule=1)
		{	
			$this->renderView();
			return $this->content;
		}
		
                private function renderView(){
                        $this->content['CONTENT'] = View::render('interface.php', [
                                'TITLE' => $this->content['TITLE'],
                                'CONTENT' => $this->content['CONTENT']
                        ]);

                }
		
    }