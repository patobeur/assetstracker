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
				"Position" => 'position',
				"Last" => 'lasteleve_id',
			];

			$theaders = "<tr>";
			foreach ($titles as $key => $value) {
				$theaders .= "<th>".$key."</th>";
				$sqlList[] = $value;
			}
			$theaders .= "</tr>";

			$items = $this->CheckDb->list('pc',implode(",", $sqlList));

			foreach ($items as $item) {
					$content .= "<tr>";
					foreach ($item as $value) {
						$content .= "<td>".($value??'<em class="null">null</em>')."</td>";
					}
				$content .= "</tr>";
			}
			
            $html = str_replace('#PAGETITLE#', 'Liste des Pc', $html);
            $html = str_replace('{{TITLES}}', $theaders, $html);
            $html = str_replace('{{CONTENT}}', $content, $html);

			return [
				'CONTENT'=> $html,
				'TITLE'=> 'Pc list'
			];
		}
		// Gérer le traitement de connexion
		public function listEleves() {
			$html = file_get_contents('../app/views/listes.php');
			
			$sqlList = [];
			$titles = [
				"ID" => 'id',
				"barrecode" => 'barrecode',
				"nom" => 'nom',
				"prenom" => 'prenom',
				"promo" => 'promo',
				"classe" => 'classe',
				"birth" => 'birth',
				// "mail" => 'mail',
				"lastpc_id" => 'lastpc_id'
			];

			$theaders = "<tr>";
			foreach ($titles as $key => $value) {
				$theaders .= "<th>".$key."</th>";
				$sqlList[] = $value;
			}
			$theaders .= "<th>Action</th>";
			$theaders .= "</tr>";

			$items = $this->CheckDb->list('eleves',implode(",", $sqlList));

			$content = '';			
			foreach ($items as $item) {
					$content .= "<tr>";
					foreach ($item as $value) {
						$content .= "<td>".($value??'<em class="null">null</em>')."</td>";
					}
					$content .= '<td><i class="ico github"></i><a href="/eleve?num='.$item['id'].'">'.$item['id'].'</a></td>';
				$content .= "</tr>";
			}
			
            $html = str_replace('#PAGETITLE#', 'Liste des Élèves', $html);
            $html = str_replace('{{TITLES}}', $theaders, $html);
            $html = str_replace('{{CONTENT}}', $content, $html);
			
			return [
				'CONTENT'=> $html,
				'TITLE'=> 'Élèves list'
			];
		}

		// Gérer le traitement de connexion
		public function listTimeline() {
			$html = file_get_contents('../app/views/listes.php');
			$content = '';
			$sqlList = [];
			$titles = [
				"ID" => 'id',
				"idpc" => 'idpc',
				"ideleves" => 'ideleves',
				"typeaction" => 'typeaction',
				"Date" => 'birth'
			];
			$theaders = "<tr>";
			foreach ($titles as $key => $value) {
				$theaders .= "<th>".$key."</th>";
				$sqlList[] = $value;
			}
			$theaders .= "</tr>";

			$items = $this->CheckDb->list('timeline',implode(",", $sqlList));

			foreach ($items as $item) {
					$content .= "<tr>";
					foreach ($item as $value) {
						$content .= "<td>".$value."</td>";
					}
				$content .= "</tr>";
			}
			
            $html = str_replace('#PAGETITLE#', 'Timeline', $html);
            $html = str_replace('{{TITLES}}', $theaders, $html);
            $html = str_replace('{{CONTENT}}', $content, $html);

			return [
				'CONTENT'=> $html,
				'TITLE'=> 'Timeline'
			];
		}
	}
