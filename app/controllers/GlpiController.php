<?php
	namespace app\controllers;
	
	class GlpiController {
		private $lvAuth = 5;
		private $pdfAuth;
		private $CheckDb;
		private $Console;
		private $errors;
		private $pdo;
		private $ElevesIds;
		private $pdoGlpi;
		private $content = 	[
			'TITLE' => "Glpi",
			'CONTENT'   => "Glpi ?",
		];
		private $tablesGlpi = [
			'pc'=>'glpi_computers',
			'eleves'=>'glpi_users',
		];
		
		// users : id 	name 	password 	password_last_update 	phone 	phone2 	mobile 	realname 	firstname 	locations_id language use_mode 	list_limit 	is_active 	comment 	auths_id 	authtype 	last_login 	date_mod 	date_sync 	 is_deleted 	profiles_id 	entities_id 	usertitles_id 	usercategories_id 	date_format 	number_format 	names_format csv_delimiter 	is_ids_visible 	use_flat_dropdowntree 	use_flat_dropdowntree_on_search_result 	show_jobs_at_login 	priority_1 priority_2 	priority_3 	priority_4 	priority_5 	priority_6 	followup_private 	task_private 	default_requesttypes_id password_forget_token 	password_forget_token_date 	user_dn 	user_dn_hash 	registration_number 	 show_count_on_tabs 	refresh_views 	set_default_tech 	personal_token 	personal_token_date 	api_token api_token_date 	cookie_token 	cookie_token_date 	display_count_on_home 	notification_to_myself 	 duedateok_color 	duedatewarning_color 	duedatecritical_color 	duedatewarning_less 	 duedatecritical_less 	duedatewarning_unit 	duedatecritical_unit 	display_options 	 is_deleted_ldap 	pdffont 	picture 	begin_date 	end_date 	keep_devices_when_purging_item privatebookmarkorder 	backcreated 	task_state 	palette 	page_layout 	fold_menu 	 fold_search 	savedsearches_pinned 	timeline_order 	itil_layout 	richtext_layout 	 set_default_requester 	lock_autolock_mode 	lock_directunlock_notification 	date_creation 	 highcontrast_css 	plannings 	sync_field 	groups_id 	users_id_supervisor 	timezone 	default_dashboard_central default_dashboard_assets 	default_dashboard_helpdesk 	default_dashboard_mini_ticket 	default_central_tab 	nickname timeline_action_btn_layout 	timeline_date_format
		private $usersTitles = [
			"ID" => 'id',
			"name" => 'name',
			"realname" => 'realname',
			"firstname" => 'firstname',
			"is_active" => 'is_active',
			// "comment" => 'comment',
			// "entities_id" => 'entities_id',
			"user_dn" => 'user_dn'
		];
		// computers : id 	entities_id 	name 	serial 	otherserial 	contact 	contact_num 	users_id_tech  groups_id_tech 	comment 	date_mod 	autoupdatesystems_id 	locations_id 	networks_id 	computermodels_id computertypes_id 	is_template 	template_name 	manufacturers_id 	is_deleted 	is_dynamic 	users_id 	groups_id states_id 	ticket_tco 	uuid 	date_creation 	is_recursive 	last_inventory_update 	last_boot
		private $computersTitles = [
			"ID" => 'id',
			"name" => 'name',
			"serial" => 'serial',
			"otherserial" => 'otherserial',
			// "contact" => 'contact',
			// "comment" => 'comment',
			// "computermodels_id" => 'computermodels_id',
			// "computertypes_id" => 'computertypes_id',
			"states_id" => 'states_id',
			// "date_creation" => 'date_creation'
		];
		public function __construct($CheckDb) {
			$this->pdfAuth = (isset($_SESSION['user']) && isset($_SESSION['user']['typeaccount_id']) && (int)$_SESSION['user']['typeaccount_id']>=$this->lvAuth );
			$this->CheckDb = $CheckDb;
			$this->Console = $this->CheckDb->Console;
			$this->pdo = $this->CheckDb->getPdo();
			$this->pdoGlpi = $this->CheckDb->getPdoGlpi();
		}
		public function handle() {
			// todo
			if($this->pdoGlpi){

				if($this->pdo){
					$Eleves = $this->getRowsFromSource('local','eleves','id,barrecode,nom,prenom,classe,promo,birth,glpi_id', ["glpi_id<>'null'"],['id'] );
					$this->ElevesIds = array_unique(
						array_filter(
							array_column($Eleves, 'glpi_id'), // Extraction des valeurs
							fn($value) => !is_null($value) && $value !== '' // Filtre pour exclure les vides et null
						)
					);
					$this->ElevesIds = array_values($this->ElevesIds);// Réindexation des clés du tableau
					
					$Pcs = $this->getRowsFromSource('local','pc','*', [],['id']);
					
					$userstheadersAndSql = $this->getTheadersAndCols($this->usersTitles);
					$usersCols = implode(",", $userstheadersAndSql['cols']);
					$userstheaders = $userstheadersAndSql['theaders'];

					$GlpiEleves = $this->getRowsFromSource('glpi',$this->tablesGlpi['eleves'],$usersCols , ["entities_id > '1'","user_dn <> ''"],['id'] );

					$pctheadersAndSql = $this->getTheadersAndCols($this->computersTitles);
					$pcCols = implode(",", $pctheadersAndSql['cols']);
					$pctheaders = $pctheadersAndSql['theaders'];

					$GlpiPcs = $this->getRowsFromSource('glpi',$this->tablesGlpi['pc'],$pcCols, [],['id']);

					$this->Console->addMsg([ "content"=>count($Eleves).' eleves trouvés en LOCAL',
						"title"=>'ℹ️',"class"=>'info',"birth"=>date("h:i:s")]);
					$this->Console->addMsg([ "content"=>count($Pcs).' pc trouvés en LOCAL',
						"title"=>'ℹ️',"class"=>'info',"birth"=>date("h:i:s")]);
					$this->Console->addMsg([ "content"=>count($GlpiEleves).' eleves trouvés dans GLPI',
						"title"=>'ℹ️',"class"=>'info',"birth"=>date("h:i:s")]);
					$this->Console->addMsg([ "content"=>count($GlpiPcs).' pc trouvés dans GLPI',
						"title"=>'ℹ️',"class"=>'info',"birth"=>date("h:i:s")]);
				}
				
				$glpiElevesHtml = $this->getList(
					'Liste des users',
					$this->tablesGlpi['eleves'],
					$userstheaders,
					$GlpiEleves,
					'eleve'
				);
				$glpiPcsHtml = $this->getList(
					'Liste des Computers',
					$this->tablesGlpi['pc'],
					$pctheaders,
					$GlpiPcs,
					'pc'
				);

				$this->content['CONTENT'] = $glpiElevesHtml['CONTENT'].$glpiPcsHtml['CONTENT'];
			}
			
			$this->renderView();
			return $this->content;
		}
		
		private function renderView(){
			$htmlView = file_get_contents(filename: '../app/views/glpipc.php');
				
			$htmlView = str_replace('{{TITLE}}', $this->content['TITLE'], $htmlView);
			$htmlView = str_replace('{{CONTENT}}', $this->content['CONTENT'], $htmlView);
			// $htmlView = str_replace('{{FORMACTION}}', ' action="listpc"', $htmlView);
			$htmlView = str_replace('{{PRINTINPUT}}', '', $htmlView);



			$this->content['CONTENT'] = $htmlView;

		}
		private function getTheadersAndCols($titles=[]) {
			$cols = [];
			$theaders = "<tr>";
			foreach ($titles as $key => $value) {
				if($key==='user_dn'){
					// $theaders .= "<th>".$key."</th>";
					$theaders .= "<th>promo</th>";
					$theaders .= "<th>section</th>";
				}
				else {
					$theaders .= "<th>".$key."</th>";
				}
				$cols[] = $value;
			}
			if ($this->pdfAuth) $theaders .= '<th>Check</th>';
			$theaders .= "</tr>";
			return ["theaders"=> $theaders,"cols"=> $cols];	
		}
		private function getList($title, $table, $theaders=false, $rows=[], $categorie) {


			$html = file_get_contents('../app/views/glpipc/listesGlpi.php');
			$content = '';

			foreach ($rows as $item) {
					$content .= "<tr>";
					foreach ($item as $key => $value) {
						if($key==='user_dn' && $value != '') {
							// $content .= "<td>".($value??'<em class="null">null</em>')."</td>";
							$string = explode(",", $value);
							$paquet = substr($string[1],3);
							$section = substr($paquet, 0,-4);
							$section = str_replace("BTS", "", $section);
							$promo = substr($paquet, -4);
							$content .= "<td>".$section."</td>";
							$content .= "<td>".$promo."</td>";
						}
						else {
							$content .= "<td>".($value??'<em class="null">null</em>')."</td>";
						}
						
					}

					if($table===$this->tablesGlpi['eleves']){
						if (in_array($item['id'], $this->ElevesIds)) {
							if ($this->pdfAuth) $content .= '<td class="check"></td>';
						} else {
							if ($this->pdfAuth) $content .= '<td class="check"><input type="checkbox" id="item_'.$item['id'].'" name="item_'.$item['id'].'" checked /></td>';
						}
					}
					if($table===$this->tablesGlpi['pc']){
						if ($this->pdfAuth) $content .= '<td class="check"><input type="checkbox" id="item_'.$item['id'].'" name="item_'.$item['id'].'" checked /></td>';
					}

				$content .= "</tr>";
			}
			
            $html = str_replace('{{PAGETITLE}}', $title, $html);
            $html = str_replace('{{TITLES}}', $theaders, $html);
            $html = str_replace('{{CONTENT}}', $content, $html);


			if($categorie==='pc'){
				$html = str_replace('{{FORMACTION}}', ' action="listpc"', $html);
			}
			elseif($categorie==='eleve'){
				$html = str_replace('{{FORMACTION}}', ' action="listeleve"', $html);
			}
			else{
				$html = str_replace('{{FORMACTION}}', '', $html);
			}
			$html = str_replace('{{buttons}}', '', $html);
			

			return ['CONTENT'=> $html];
		}
		// BDD
		
		/**
		 * Fonction pour lire certaines colonnes d'une table
		 */
		private function getRowsFromSource($source='local', $table=null,$cols=null,$wheres=null,$orders=null): array{ 
			$pdo = ($source==='glpi') ? $this->pdoGlpi : $this->pdo;
			$respons = [];

			if($table && $cols){
				try {
					$where= "";
					$order= "";
					$rows = []; 
					if($wheres && gettype($wheres)==='array'){
						foreach($wheres as $value){	
							if ($where==="") {$where = " WHERE " . $value;}
							else {$where .= " AND " . $value;}
						}
					}
					if($orders && gettype($orders)==='array'){
						foreach($orders as $value){	
							if ($order==="") {$order = " ORDER BY " . $value;}
							else {$order .= ", " . $value;}
						}
					}
					if($table){
						$query = "SELECT {$cols} FROM {$table}{$where}{$order}";
						$stmt = $pdo->prepare($query);
						$stmt->execute();
						$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
					}
					return $rows;
				} catch (\PDOException $e) {
					die("Erreur de connexion à la base de données : " . $e->getMessage());
				} catch (\Exception $e) {
					die("Erreur de connexion à la base de données : " . $e->getMessage());
				}
			}

			return $respons;
		}
		
	}
