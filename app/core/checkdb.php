<?php
namespace app\core;

class CheckDb
{
	private $dbConfigPath = '../app/conf/dbconfig.php';
	private $installPath = '../public/install.php';
	private $pdo;

	public function __construct() {
		
		if(!isset($_SESSION['errors'])){$_SESSION['errors'] = [];}
		$this->checkInstallAndConfig();
	}

	public function checkInstallAndConfig()
	{
		$dbConfigExiste = file_exists($this->dbConfigPath);
		$installExiste = file_exists($this->installPath);


		if ($dbConfigExiste && $installExiste) {
			// dbConfig.php exite mais install.php aussi ;(
			echo("dbconfig.php et install.php ne devraient pas exister en même temps ??");
			$_SESSION['errors'][]="dbconfig.php et install.php ne devraient pas exister en même temps ??";
			die();
		}
		if (!$dbConfigExiste && $installExiste) {

			// dbConfig.php exite mais install.php aussi ;(
			// si dbConfig.php n'exite pas mais que install.php existe 
			// on lance l'install
			header('Location: install.php'); // Redirige vers la l'installation
			die();
		}
		if ($dbConfigExiste && !$installExiste) {
			$errors = $this->checkDb();
		}
	}
	public function getPdo()
	{
		return $this->pdo;
	}

	public function checkDb()
	{
		require_once($this->dbConfigPath);
		$dberror = [];
		if (!empty($dbHost) && !empty($dbName) && !empty($dbUser)) {
			try {
				// Créer une connexion PDO
				$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8";
				$this->pdo = new \PDO($dsn, $dbUser, $dbPassword);

				if (PROD) { // en prod
					$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
				}
				else { // en dev
					$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
				}

				// Vérifier si la table assetstracker existe
				$query = $this->pdo->query("SHOW TABLES LIKE 'administrateurs'");
				if ($query->rowCount() == 0) {
					$dberror[]="La table 'administrateurs' n'existe pas dans la base de données.";
					// header('Location: /');
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
	function login($username=null,$password=null){
		$errors = [];
		$username = trim($username);  
		$password = trim($password);
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
				if ($admin && password_verify($password, $admin['motdepasse'])) {
					// Authentification réussie
					$_SESSION['user'] = [
						'id' => $admin['id'],
						'pseudo' => $admin['pseudo'],
						// 'nom' => $admin['nom'],
						// 'prenom' => $admin['prenom']
					];
	
					$this->loginUpdate();
	
					header("Location: /");
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
	private function loginUpdate(){  
		if(isset($_SESSION['user']) && isset($_SESSION['user']['id'])){
			$query = "INSERT INTO visites (administrateurs_id) VALUES (".$_SESSION['user']['id'].")";
			$stmt = $this->pdo->prepare($query);
			$stmt->execute();
		}
	}
	/**
	 * Fonction pour avoir la liste des Pc
	 */
	public function list($table=null,$cols){  
		if($table){
			$query = "SELECT ".$cols." FROM ".$table;
			$stmt = $this->pdo->prepare($query);
			$stmt->execute();
			$pcs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $pcs;
		}
		return null;
	}
	/**
	 * Fonction pour avoir un/une eleve
	 */
	public function eleveOnce($table,$barrecode){
		try {
			$query = "SELECT * FROM ".$table." WHERE barrecode = :barrecode LIMIT 1";
			$stmt = $this->pdo->prepare($query);
			$stmt->bindParam(':barrecode', $barrecode, \PDO::PARAM_STR);
			$stmt->execute();
			$respons = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $respons;
		} catch (\PDOException $e) {
			die("Erreur de connexion ou de création de la base de données : " . $e->getMessage());
		} catch (\Exception $e) {
			die("Erreur : " . $e->getMessage());
		}
	}
}