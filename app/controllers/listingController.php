<?php
	namespace app\controllers;
	
	class ListingController {
		private $CheckDb;
	
		public function __construct($CheckDb) {
			$this->CheckDb=$CheckDb;
		}
		
		// Gérer le traitement de connexion
		public function listPc() {
			$html = file_get_contents('../app/views/listes.php');
			$content = '';
			$sqlList = [];
			$titles = [
				"ID" => 'id',
				"barrecode" => 'barrecode',
				"Modèle" => 'model',
				"Numéro de Série" => 'serialnum',
				"État" => 'etat',
				"Entrée" => 'birth',
			];

			$theaders = "<tr>";
			foreach ($titles as $key => $value) {
				$theaders .= "<th>".$key."</th>";
				$sqlList[] = $value;
			}
			$theaders .= "/<tr>";

			$sqlList = implode(",", $sqlList);
			$items = $this->CheckDb->listPc($sqlList);

			foreach ($items as $item) {
					$content .= "<tr>";
					foreach ($item as $value) {
						$content .= "<td>".$value."</td>";
					}
				$content .= "</tr>";
			}
			
            $html = str_replace('#PAGETITLE#', 'Liste des Pc', $html);
            $html = str_replace('#TITLES#', $theaders, $html);
            $html = str_replace('#CONTENT#', $content, $html);

			return [
				'CONTENT'=> $html,
				'TITLE'=> 'Pc list'
			];
		}
		// Gérer le traitement de connexion
		public function listEleves() {
			$html = file_get_contents('../app/views/listes.php');
			$content = '';
			$items = $this->CheckDb->listEleves('id,barrecode,nom,prenom,promo,classe,idaccount,birth,mail');
			
			
			$content = '';
			$sqlList = [];
			$titles = [
				"ID" => 'id',
				"barrecode" => 'barrecode',
				"nom" => 'nom',
				"prenom" => 'prenom',
				"promo" => 'promo',
				"classe" => 'classe',
				"idaccount" => 'idaccount',
				"birth" => 'birth',
				"mail" => 'mail',
			];

			$theaders = "<tr>";
			foreach ($titles as $key => $value) {
				$theaders .= "<th>".$key."</th>";
				$sqlList[] = $value;
			}
			$theaders .= "/<tr>";

			$sqlList = implode(",", $sqlList);
			$items = $this->CheckDb->listEleves($sqlList);

			foreach ($items as $item) {
					$content .= "<tr>";
					foreach ($item as $value) {
						$content .= "<td>".$value."</td>";
					}
				$content .= "</tr>";
			}
			
            $html = str_replace('#PAGETITLE#', 'Liste des Élèves', $html);
            $html = str_replace('#TITLES#', $theaders, $html);
            $html = str_replace('#CONTENT#', $content, $html);
			
			return [
				'CONTENT'=> $html,
				'TITLE'=> 'Élèves list'
			];
		}
	}