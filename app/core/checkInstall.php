<?php
namespace app\core;

class CheckInstall
{
	private $dbConfigPath;
	private $installPath;

	private $CheckDb;
	private $Console;

	public function __construct($CheckDb,$Console) {
		$this->CheckDb = $CheckDb;
		$this->Console = $Console;
		$this->dbConfigPath = $this->CheckDb->getDbConfigPath();
		$this->installPath = $this->CheckDb->GetInstallPath();
		$this->checkInstallAndConfig();
	}
	public function checkInstallAndConfig(): void
	{
		$dbConfigExiste = file_exists(filename: $this->dbConfigPath);
		$installExiste = file_exists(filename: $this->installPath);

		if ($dbConfigExiste){
			// dbconfig.php ok
			if ($installExiste){
				// install.php aussi et ce n'est pas normal
				// on check la db voir si il y a des erreur !
				$dbErrors = $this->CheckDb->checkIfDb();
				if(count($dbErrors)>0) {
					// si il y a des erreur on die
					die("L'installation a eu lieu mais la base de donnée n'existe pas. Regardez votre dbConfig ? Attention un fichier install exite encore !");
				}
			}
			else {	
				// Pas d'install.php
				// on check la db voir si il y a des erreur !
				$dbErrors = $this->CheckDb->checkIfDb();
				if(count($dbErrors)>0) {
					// si il y a des erreur on die
					die("L'installation a eu lieu mais la base de donnée n'existe pas. Pour info: il n'y a aucun fichier d'installation.");
				} 
				else {
					// tout est ok !!!
				}
			}
		}
		else {
			// PAS de dbconfig.php
			if ($installExiste){
				// Fichier d'install trouvé alors on lance l'install
				session_destroy();
				header(header: 'Location: /install.php');
				die();
			}
			else {	
				// Pas de fichier install trouvé
				// alors !!! on die
				die("Il n'y a pas de fichier config et pas de fichier d'installation !!");
			}
		}

	}
	public function addCheckMessage($lv=0): void
	{
		if ($lv<2) return;

		if ($lv>=2) {

			$dbConfigExiste = file_exists(filename: $this->dbConfigPath);
			$installExiste = file_exists(filename: $this->installPath);
	
			$this->Console->addMsg([
				"content"=>$this->dbConfigPath.':'. ($dbConfigExiste?' existe':'no'),
				"title"=>($dbConfigExiste?'✅':'🚫'),
				"class"=>($dbConfigExiste?'succes':'alerte')
			]);
			$this->Console->addMsg([
				"content"=>$this->installPath.':'. ($installExiste?' existe':'no'),
				"title"=>($installExiste?'✅':'🚫'),
				"class"=>($installExiste?'succes':'alerte')
			]);
			if ($dbConfigExiste){
				// dbconfig.php ok
				if ($installExiste){
					// install.php en trop
					$this->Console->addMsg([
						"content"=>"dbconfig.php et install.php ne devraient pas exister en même temps ??",
						"title"=>'🚫',
						"class"=>'alerte',
						"birth"=>date("h:i:s")
					]);
				}
			}

		}
	}
}