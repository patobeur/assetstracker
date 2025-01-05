<?php
namespace app\core;

class CheckDb
{
	private $dbConfigPath = '../app/conf/dbconfig.php';
	private $installPath = '../public/install.php';
	private $pdo;
	private $Console;

	public function __construct($Console) {
		$this->Console = $Console;
		$this->checkInstallAndConfig();
	}

	public function checkInstallAndConfig(): void
	{
		$dbConfigExiste = file_exists(filename: $this->dbConfigPath);
		$installExiste = file_exists(filename: $this->installPath);

		if (!$dbConfigExiste && $installExiste) {

			// dbConfig.php exite mais install.php aussi ;(
			// si dbConfig.php n'exite pas mais que install.php existe 
			// on lance l'install
			header(header: 'Location: install.php'); // Redirige vers la l'installation
			die();
		}
		if ($dbConfigExiste && $installExiste) {
			// dbConfig.php exite mais install.php aussi ;(
			
			if(CONFIG['PROD']){
				die("En mode PROD, 'dbconfig.php' et 'install.php' ne devraient pas exister en même temps ??");
			}
			else {
				$dbErrors = $this->checkDb();
				if(count($dbErrors)>0) {
					echo "La bdd n'existe pas, regardez votre dbConfig ?";
					die();
				}
				$this->Console->addMsg(["content"=>"dbconfig.php et install.php ne devraient pas exister en même temps ??","title"=>'ATTENTION',"class"=>'alerte']);
			}
		}
		elseif ($dbConfigExiste && !$installExiste) {
			$dbErrors = $this->checkDb();
			if(count($dbErrors)>0) {echo "La bdd n'existe pas et il n'y a aucun fichier d'installation ?";die();}
		}
	}
	public function getPdo(): \PDO
	{
		return $this->pdo;
	}

	public function checkDb(): array
	{
		require_once $this->dbConfigPath ;
		$dberror = [];
		if (!empty($dbHost) && !empty($dbName) && !empty($dbUser) && !empty($dbUser) && isset($dbPassword)) {
			try {
				// Créer une connexion PDO
				$dsn = "mysql:host=".$dbHost.";dbname=".$dbName.";charset=utf8";
				$this->pdo = new \PDO(dsn: $dsn, username: $dbUser, password: $dbPassword);
				if($this->pdo){
					if (CONFIG['PROD']) { // en prod
						$this->pdo->setAttribute(attribute: \PDO::ATTR_ERRMODE, value: \PDO::ERRMODE_SILENT);
					}
					else { // en dev
						$this->pdo->setAttribute(attribute: \PDO::ATTR_ERRMODE, value: \PDO::ERRMODE_EXCEPTION);
					}

					// Vérifier si la table assetstracker existe
					$query = $this->pdo->query("SHOW TABLES LIKE 'administrateurs'");

					if ($query->rowCount() < 1) {
						$dberror[]="La table 'administrateurs' n'existe pas dans la base de données.";
						// header('Location: /');
					}
				}
				else {
					die("no bdd");
				}
			} catch (\PDOException $e) {
				$dberror[]="Erreur lors de la connexion à la base de données : " . $e->getMessage();
				// header('Location: /');
			}
		} else {
			$dberror[]="Les paramètres de connexion à la base de données sont incomplets.";
			// header('Location: /');
		}
		return $dberror;
	}

	/**
	 * Fonction pour login
	 */
	function login($username=null,$password=null): array{
		$errors = [];
		$username = trim(string: $username);  
		$password = trim(string: $password);
		if (empty($username) || empty($password)) {
			$errors[] = "Tous les champs doivent être remplis.";
		}
		if (empty($errors)) {
			if(!$this->pdo){
				$errors[] = "Pas de connexion active !";
			}
			else {
				$query = "SELECT * FROM administrateurs WHERE pseudo = :username LIMIT 1";
				$stmt = $this->pdo->prepare($query);
				$stmt->bindParam(':username', $username, \PDO::PARAM_STR);
				$stmt->execute();
				$admin = $stmt->fetch(\PDO::FETCH_ASSOC);
				if ($admin && password_verify(password: $password, hash: $admin['motdepasse'])) {
					// Authentification réussie
					$_SESSION['user'] = [
						'id' => $admin['id'],
						'pseudo' => $admin['pseudo'],
						// 'nom' => $admin['nom'],
						// 'prenom' => $admin['prenom']
					];
	
					$this->loginUpdate();
	
					header(header: "Location: /");
					exit;
				} else {
					$errors[] = "Pseudo ou mot de passe incorrect.";
				}
			}
		}
		return $errors;
	}

	/**
	 * Fonction pour noter les visites
	 */
	private function loginUpdate(): void{  
		if(isset($_SESSION['user']) && isset($_SESSION['user']['id'])){
			$query = "INSERT INTO visites (administrateurs_id) VALUES (".$_SESSION['user']['id'].")";
			$stmt = $this->pdo->prepare($query);
			$stmt->execute();
		}
	}
	/**
	 * Fonction pour avoir la liste des Pc
	 */
	public function list($table=null,$cols=null): array{ 
		$respons = [];
		if($table && $cols){
			try {
				$pcs = []; 
				if($table){
					$query = "SELECT {$cols} FROM {$table} ORDER by birth DESC";
					$stmt = $this->pdo->prepare($query);
					$stmt->execute();
					$pcs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
				}
				return $pcs;
			} catch (\PDOException $e) {
				die("Erreur de connexion à la base de données : " . $e->getMessage());
			} catch (\Exception $e) {
				die("Erreur de connexion à la base de données : " . $e->getMessage());
			}
		}
		return $respons;
	}
	/**
	 * Fonction pour avoir une seule réponse
	 */
	public function once($table,$barrecode): array{
		$respons = [];
		if($table && $barrecode){
			try {
				$query = "SELECT * FROM {$table} WHERE barrecode = :barrecode LIMIT 1";
				$stmt = $this->pdo->prepare($query);
				$stmt->bindParam(':barrecode', $barrecode, \PDO::PARAM_STR);
				$stmt->execute();
				$respons = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			} catch (\PDOException $e) {
				die("Erreur de connexion ou de création de la base de données : " . $e->getMessage());
			} catch (\Exception $e) {
				die("Erreur : " . $e->getMessage());
			}
		}
		return $respons;
	}
	public function insertTimeline($idpc, $ideleves=null, $typeaction) {  
		$this->Console->addMsg(["content"=>$ideleves,"title"=>'ideleves']);
		$this->Console->addMsg(["content"=>$idpc,"title"=>'idpc']);
		print_r(gettype($ideleves));
		if(($ideleves && $idpc) || (gettype($ideleves)==='null' && $idpc) ) {
			try {
				$query = "INSERT INTO timeline (idpc, ideleves, typeaction) VALUES (:idpc, :ideleves, :typeaction)";
				$stmt = $this->pdo->prepare($query);
				$stmt->bindParam(':ideleves', $ideleves, \PDO::PARAM_STR);
				$stmt->bindParam(':idpc', $idpc, \PDO::PARAM_STR);
				$stmt->bindParam(':typeaction', $typeaction, \PDO::PARAM_STR);
				$stmt->execute();
			} catch (\PDOException $e) {
				die("Erreur d'enregistrement des données : " . $e->getMessage());
			} catch (\Exception $e) {
				die("Erreur d'enregistrement des données : " . $e->getMessage());
			}
			return true;
		}
		return false;
	}
	public function insertTimelineIn($idpc, $typeaction) {  
		$this->Console->addMsg(["content"=>$idpc,"title"=>'idpc']);

		if($idpc) {
			try {
				$query = "INSERT INTO timeline (idpc, typeaction) VALUES (:idpc, :typeaction)";
				$stmt = $this->pdo->prepare($query);
				$stmt->bindParam(':idpc', $idpc, \PDO::PARAM_STR);
				$stmt->bindParam(':typeaction', $typeaction, \PDO::PARAM_STR);
				$stmt->execute();
			} catch (\PDOException $e) {
				die("Erreur d'enregistrement des données : " . $e->getMessage());
			} catch (\Exception $e) {
				die("Erreur d'enregistrement des données : " . $e->getMessage());
			}
			return true;
		}
		return false;
	}
}