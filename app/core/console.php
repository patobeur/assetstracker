<?php

	namespace app\core;

	class Console
	{
		private $defaultPage;
		private $msg = [];
		private $active = false;
		private $vue = '<div class="console">{{console}}</div>';

		public function __construct($active=false) {
			$this->active= $active ?? false;
			$this->msg[] = ["content"=>'Console',"title"=>'Core'];
		}
	
		public function addMsg($datas): void {
			$this->msg[] = $datas;
		}

		public function addConsole($defaultPage): string {
			$this->defaultPage = $defaultPage ?? null;
			if ($this->active) {
	
				if (count(value: $this->msg)>0){
					$contents = '';
					for ($i=0; $i < count(value: $this->msg) ; $i++) {
						$contents .= "{{console{$i}}}";
					}
					$this->vue = str_replace(search: "{{console}}",replace: $contents,subject: $this->vue);
	
					for ($i=0; $i < count(value: $this->msg) ; $i++) {
						$pack = "<p>".($this->msg[$i]['title']??'Titre').":";
						$pack .= "".$this->msg[$i]['content']."</p>";
						$this->vue = str_replace(search: "{{console{$i}}}",replace: $pack,subject: $this->vue);
					}
				}
				else {
					$this->vue = str_replace(search: "{{console}}",replace: "♥",subject: $this->vue);
				}
				$this->defaultPage = str_replace(search: "{{console}}",replace: $this->vue,subject: $this->defaultPage);
			} else {
				$this->defaultPage = str_replace(search: "{{console}}",replace: '',subject: $this->defaultPage);
			}
			return $this->defaultPage;
		}
	}