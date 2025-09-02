<?php
	// anciennement : install.php
	// fichier renommé apres l'install
	session_start();
	define('PROD',0); // 0 en dev, 1 en prod
	
	$dbConfigPath = '../app/conf/dbconfig.php';
	$defaultHost = 'localhost';
	$defaultUser = 'root';
	$defaultDb = 'assetsTracker';
	
	$defaultHostGlpi = '';
	$defaultUserGlpi = '';
	$defaultDbGlpi = 'glpi';

	$version = '0.5.0.3';
	$creation = date('Y-m-d H:i:s');

	$defaultAdminPseudo = '';
	$defaultAdminPasse = '';
	$defaultAdminMail = 'admin@example.com';
	$defaultAdminPrenom = 'admin';
	$defaultAdminNom = 'admin';
	$defaultAdminTypeaccount = 1;
	
	$comptes = [
		[
			'pseudo'=> 'mathis',
			'passe'=> '$2y$10$JK1Me6L6EQJR/tgeG87fd.7UqN8oueH6zp/0Ba0EiOO3KYB8ROmIa',
			'nom'=>'mathis',
			'prenom'=> 'mathis',
			'mail'=> 'mathis@example.com',
			'typeaccount' => (int)5,
		],
		[
			'pseudo'=> 'alicia',
			'passe'=> '$2y$10$JK1Me6L6EQJR/tgeG87fd.7UqN8oueH6zp/0Ba0EiOO3KYB8ROmIa',
			'nom'=>'alicia',
			'prenom'=> 'alicia',
			'mail'=> 'alicia@example.com',
			'typeaccount' => (int)8,
		],
		[
			'pseudo'=> 'eric',
			'passe'=> '$2y$10$JK1Me6L6EQJR/tgeG87fd.7UqN8oueH6zp/0Ba0EiOO3KYB8ROmIa',
			'nom'=>'eric',
			'prenom'=> 'eric',
			'mail'=> 'eric@example.com',
			'typeaccount' => (int)9,
		]
	];
	if (file_exists($dbConfigPath)) {
		if(PROD===0){
			deleteFile(); // Supprime ce script
			header('Location: /'); // Redirige vers la racine du site
			exit;
		}
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$dbHost = trim($_POST['host']) ?? '';
		$dbName = trim($_POST['db']) ?? '';
		$dbUser = trim($_POST['user']) ?? '';
		$dbPassword = $_POST['pass'] ?? '';
		
		$checkboxGlpi = $_POST['glpitrigger'] ?? null;
		$hostGlpi = ($_POST['hostGlpi'] && $_POST['hostGlpi']!='') ? $_POST['hostGlpi'] : $dbHost;
		$dbGlpi = ($_POST['dbGlpi'] && $_POST['dbGlpi']!='') ? $_POST['dbGlpi'] :'glpi';
		$userGlpi = ($_POST['userGlpi'] && $_POST['userGlpi']!='') ? $_POST['userGlpi'] :$dbUser;
		$passGlpi = ($_POST['passGlpi'] && $_POST['passGlpi']!='') ? $_POST['passGlpi'] :$dbPassword;

		$defaultAdminPseudo = trim($_POST['adminpseudo'] ?? '');
		$defaultAdminPasse = $_POST['adminpass'] ?? '';
		
		$dbHost = filter_var($dbHost, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

		if (!filter_var($dbHost, FILTER_VALIDATE_DOMAIN)) {
			die("Hôte invalide");
		}

		if (empty($dbHost) || empty($dbName) || empty($dbUser) || empty($defaultAdminPseudo) || empty($defaultAdminPasse)) {
			$error = "Veuillez remplir tous les champs.";
		} else {
			if (!empty($dbHost) && !empty($dbName) && !empty($dbUser)) {
				try {
					// Créer une connexion PDO
					$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8";
					$pdo = new \PDO($dsn, $dbUser, $dbPassword);
					$error = "La base de données '".$dbName."' existe déjà: ";
				} 
				catch (\PDOException $e) {
					// $error = "Erreur : " . $e->getMessage();
					} 
				catch (Exception $e) {
					// $error = "Erreur : " . $e->getMessage();
				}
	
			}

			if ($checkboxGlpi==='on' && !empty($hostGlpi) && !empty($dbGlpi) && !empty($userGlpi)) {
				try {
					// Créer une connexion PDO
					$dsnGlpi = "mysql:host=$hostGlpi;dbname=$dbGlpi;charset=utf8";
					$pdoGlpi = new \PDO($dsnGlpi, $userGlpi, $passGlpi);
				} 
				catch (\PDOException $e) {
					
					$error = "La base de données ".$dbGlpi." n'existe pas: ";
					$error = "Erreur : " . $e->getMessage();
				} 
				catch (Exception $e) {
					$error = "La base de données ".$dbGlpi." n'existe pas: ";
					$error = "Erreur : " . $e->getMessage();
				}
			}
			else {
				$checkboxGlpi = false;
				$hostGlpi = null;
				$dbGlpi = null;
				$userGlpi = null;
				$passGlpi = null;
			}
			if(!isset($error)){
				try {
					// Connexion à la base de données
					$dsn = "mysql:host=$dbHost";
					$pdo = new PDO($dsn, $dbUser, $dbPassword);
					
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

					// Création de la base de données si elle n'existe pas
					$pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
					$pdo->exec("USE `$dbName`");
					// Création des tables
					$queries = [
						// type d'assets (pc, tv, ou autre)
						"CREATE TABLE IF NOT EXISTS typeassets (
							id INT AUTO_INCREMENT PRIMARY KEY,
							content VARCHAR(100)
						)",
						// type d'assets (pc, tv, ou autre)
						"INSERT INTO typeassets (content) VALUES 
							('Ordinateur portable'),
							('Chargeur'),
							('Cable Hdmi'),
							('Rallonge'),
							('Tv')",
						"CREATE TABLE IF NOT EXISTS typeaccounts (
							id INT AUTO_INCREMENT PRIMARY KEY,
							content VARCHAR(30)
						)",
						"INSERT INTO typeaccounts  (id, content) VALUES 
							(1, 'Opérateur'),
							(2, 'Opérateur de niveau 2'),
							(3, 'Opérateur de niveau 3'),
							(4, 'Opérateur de niveau 4'),
							(5, 'Constructeur'),
							(6, 'Architecte'),
							(7, 'Contrôleur Central'),
							(8, 'Contrôleur Maître'),
							(9, 'Contrôleur Suprême')",
						"CREATE TABLE IF NOT EXISTS administrateurs (
							id INT AUTO_INCREMENT PRIMARY KEY,
							pseudo VARCHAR(30) NOT NULL UNIQUE,
							motdepasse VARCHAR(255) NOT NULL,
							nom VARCHAR(30),
							prenom VARCHAR(30),
							birth TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
							sessionkey VARCHAR(255),
							mail VARCHAR(50),
							typeaccount_id INT,
							FOREIGN KEY (typeaccount_id) REFERENCES typeaccounts(id)
						)",
						"INSERT INTO administrateurs (pseudo, motdepasse, nom, prenom, mail, typeaccount_id) VALUES 
							('".$defaultAdminPseudo."', '".password_hash($defaultAdminPasse, PASSWORD_DEFAULT)."', '".
							$defaultAdminNom."', '".$defaultAdminPrenom."', '".
							$defaultAdminMail."', '".$defaultAdminTypeaccount."')",
						// TABLE DES ASSETS
						"CREATE TABLE IF NOT EXISTS pc (
							id INT AUTO_INCREMENT PRIMARY KEY,
							barrecode VARCHAR(50) UNIQUE NOT NULL,
							model VARCHAR(100),
							serialnum VARCHAR(100),
							birth TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
							etat VARCHAR(50),
							typeasset_id INT NOT NULL,
							used INT DEFAULT 0,
							position VARCHAR(10) DEFAULT 'in',
							glpi_id INT NULL COMMENT 'id dans la table computer de glpi',
							lasteleve_id INT NULL COMMENT 'last owner id',
							in_date TIMESTAMP NULL COMMENT 'last date in',
							out_date TIMESTAMP NULL COMMENT 'last date out',
							FOREIGN KEY (typeasset_id) REFERENCES typeassets(id) ON UPDATE CASCADE
						)",
						// TABLE DES CLIENTS
						"CREATE TABLE IF NOT EXISTS eleves (
							id INT AUTO_INCREMENT PRIMARY KEY,
							barrecode VARCHAR(50) UNIQUE COMMENT 'doit être unique',
							nom VARCHAR(100) NOT NULL,
							prenom VARCHAR(100) NOT NULL,
							promo VARCHAR(50) NOT NULL COMMENT 'exemple:2426',
							classe VARCHAR(50) NOT NULL COMMENT 'exemple:BTSCOM',
							birth TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'date de création',
							mail VARCHAR(255) NOT NULL,
							glpi_id INT NULL COMMENT 'id user dans la table glpi',
							lastpc_id INT NULL COMMENT 'id pc du dernier emprunt',
							in_date TIMESTAMP NULL COMMENT 'date du dernier retour',
							out_date TIMESTAMP NULL COMMENT 'date du dernier emprunt',
							pivot_id INT NULL COMMENT 'idpivot pour un service en paralèlle',
							FOREIGN KEY (lastpc_id) REFERENCES pc(id))",
						// on ajoute une FK
						"ALTER TABLE pc ADD CONSTRAINT lasteleve_id FOREIGN KEY (lasteleve_id) REFERENCES eleves(id)",
						// on ajoute des ASSETS
						"INSERT INTO pc (barrecode, model, serialnum, etat, typeasset_id, position) VALUES
							('10000001', 'Dell Inspiron', 'SN12345', 'Disponible', 1, 'in'),
							('10000011', 'HP EliteBook', 'SN67890', 'En réparation', 1, 'in'),
							('30089587', 'Air ProMaster', 'SN00007', 'Disponible', 1, 'in'),
							('4056489371724', 'WTF ChallengerPro', 'SN00008', 'Disponible', 1, 'in')",
						"INSERT INTO eleves (barrecode, nom, prenom, promo, classe, mail) VALUES
							('00000001', 'Doe', 'John', '2426', 'BTSCOM', 'john.doe@example.com'),
							('00000011', 'Smith', 'Jane', '2325', 'BTSAG', 'jane.smith@example.com'),
							('4006396038531', 'Smith', 'Alice', '2325', 'BTSCOM', 'alice.smith@example.com')",
						"CREATE TABLE IF NOT EXISTS timeline (
							id INT AUTO_INCREMENT PRIMARY KEY,
							birth TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
							typeaction VARCHAR(50) NOT NULL,
							ideleves INT NULL,
							idpc INT NOT NULL,
							FOREIGN KEY (ideleves) REFERENCES eleves(id) ON UPDATE CASCADE,
							FOREIGN KEY (idpc) REFERENCES pc(id) ON UPDATE CASCADE
						)",
						"INSERT INTO timeline (ideleves, idpc, typeaction) VALUES (1,1,'out'),(1,2,'in')",
						"CREATE TABLE IF NOT EXISTS visites (
							id INT AUTO_INCREMENT PRIMARY KEY,
							login_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'date de la dernière connection',
							logout_date TIMESTAMP NULL COMMENT 'date de la dernière deco',
							administrateurs_id INT NOT NULL
						)",
					];
					foreach ($comptes as $compte) {
						$queries[] = "INSERT INTO administrateurs (pseudo, motdepasse, nom, prenom, mail, typeaccount_id) VALUES ('".
						$compte['pseudo']."', '".$compte['passe']."', '".$compte['nom']."', '".
						$compte['prenom']."', '".$compte['mail']. "', '".$compte['typeaccount']."')";
					}
					foreach ($queries as $query) {
						$pdo->exec($query);
					}
					// Création du fichier dbconfig.php
					$host = 'http://' . $_SERVER['HTTP_HOST'];
					$sitedir = stripslashes(dirname($_SERVER['PHP_SELF']));
					$configContent = <<<PHP
<?php
	\$dbHost = '$dbHost';
	\$dbName = '$dbName';
	\$dbUser = '$dbUser';
	\$dbPassword = '$dbPassword';
	
	\$hostGlpi = '$hostGlpi';
	\$dbGlpi = '$dbGlpi';
	\$userGlpi = '$userGlpi';
	\$passGlpi = '$passGlpi';

	\$version = '$version';
	\$creation = '$creation';
	
	define('CONFIG', [
		'WEBSITE' => [
			'header' => 'Content-type: text/html; charset=UTF-8',
			'siteurl' => '$host',
			'sitedir' => '$sitedir',
		],
		'REFRESH' => [
			'in' => 2,
			'out' => 2
		],
		'PROD' => false, // false en dev, true en prod
	]);
PHP;
					file_put_contents($dbConfigPath, $configContent);
					// Supprime ce fichier
					deleteFile();
					// Redirection après succès
					header('Location: /');
					exit;
				} catch (PDOException $e) {
					$error = "Erreur de connexion ou de création de la base de données : " . $e->getMessage();
				} catch (Exception $e) {
					$error = "Erreur : " . $e->getMessage();
				}
			}
		}
	}
	
	/**
	 * Fonction pour supprimer le fichier install.php
	 */
	function deleteFile(){
		PROD===0 ? rename('install.php', "install.save") : unlink(__FILE__);
	}
	?><!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Installer les tables</title>
	<style>
		*{margin:0;padding:0;box-sizing: border-box;font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;		}
		body {
				background-color: rgb(38, 50, 184);
				background-image: linear-gradient(hsla(0, 0%, 0%, .05) 2px, transparent 0), linear-gradient(90deg, hsla(0, 0%, 0%, .05) 2px, transparent 0), linear-gradient(hsla(0, 0%, 0%, .05) 1px, transparent 0), linear-gradient(90deg, hsla(0, 0%, 0%, .05) 1px, transparent 0);
				background-position: -2px -2px, -2px -2px, -1px -1px, -1px -1px;
				background-size: 100px 100px, 100px 100px, 20px 20px, 20px 20px;
				color: #333;
				display: flex;
				justify-content: center;
				align-items: center;
		}
		.container {
			background: #fff;
			border-radius: 8px;
			width: 350px;
			background-color:rgba(236, 236, 236, 0.95);
			box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
			padding: 10px;
			margin:10px;
				display: flex;
				flex-direction: column;
		}
		form {
			display: flex;
			flex-direction: column;
			gap: 15px;
		}
		.blocs {
			padding: 5px;
			padding-top: 20px;
			&.bloc-glpi {
				padding-top: 0px;
				display:none;
			}
		}
		.blocs.center {
			display: flex;
			justify-content: center;
		}
		h1 {
			font-size: 24px;
			color: #555;
			margin-bottom: 0;
			text-align: center;
		}
		p {
			text-align: center;
		}
		.error {
			color: #ff4d4d;
			font-size: 14px;
			margin-bottom: 15px;
		}
		label {
			position: relative;
			font-weight: bold;
			text-align: left;
			padding: 7px ;
		}
		.input-container {
			position: relative;
			display: flex;
			align-items: center;
			&.glpi {
				display: flex;
				flex-direction: row;
				flex-wrap: wrap;
				align-content: center;
				align-items: center;
				justify-content: flex-start;

				label{
					text-wrap: nowrap;
				}
				input {
					padding: 0;
					border: 1px solid #ddd;
					border-radius: 4px;
					width: initial;
					border-radius: 0;
				}
			}
		}
		.input-container input {
			padding: 10px 10px 10px 35px;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 16px;
			width: 100%;
			border-radius: 9px;
			box-sizing: border-box;
		}
		.input-container input:focus {
			border-color: #5b9bd5;
			outline: none;
			box-shadow: 0 0 5px rgba(91, 155, 213, 0.5);
		}
		.input-container .icon {
			position: absolute;
			left: 10px;
			color:rgb(77, 77, 77);
			font-size: 18px;
		}
		.input-container:hover .icon {
			color:rgb(7, 77, 5);
		}
		button {
			padding: 10px;
			background-color: #5b9bd5;
			color: #fff;
			border: none;
			border-radius: 4px;
			font-size: 16px;
			cursor: pointer;
			width: fit-content;
			transition: background-color 0.3s ease;
		}
		button:hover {
			background-color: #4178a9;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1>Assets Tracker Installation</h1>
		<?php if (!empty($error)): ?>
			<p class="error"><?php echo htmlspecialchars($error); ?></p>
		<?php endif; ?>
		<form method="post">
			<div class="blocs">
				<label for="host">Hôte :</label>
				<div class="input-container">
					<span class="icon">🎁</span>
					<input type="text" id="host" name="host" required value="<?php echo $defaultHost; ?>">
				</div>

				<label for="db">Nom de la base de données :</label>
				<div class="input-container">
					<span class="icon">💽</span>
					<input type="text" id="db" name="db" required value="<?php echo $defaultDb; ?>">
				</div>

				<label for="user">Utilisateur :</label>
				<div class="input-container">
					<span class="icon">🤚</span>
					<input type="text" id="user" name="user" required value="<?php echo $defaultUser; ?>">
				</div>

				<label for="pass">Mot de passe :</label>
				<div class="input-container">
					<span class="icon">🔒</span>
					<input type="password" id="pass" name="pass" style="padding-right: 30px;">
					<span id="togglePass" style="
						position: absolute;
						top: 50%;
						right: 10px;
						transform: translateY(-50%);
						cursor: pointer;
						color: #999;
					">&#128065;</span>
				</div>
			</div>

			
			<div class="blocs">
				<h2>Compte admin</h2>
				<label for="user">Pseudo Admin :</label>
				<div class="input-container">
					<span class="icon">🤚</span>
					<input type="text" id="adminpseudo" name="adminpseudo" required value="<?php echo $defaultAdminPseudo; ?>">
				</div>
				<label for="pass">Mot de passe Admin :</label>
				<div class="input-container">
					<span class="icon">🔒</span>
					<input type="password" id="adminpass" name="adminpass" style="padding-right: 30px;"  value="">
					<span id="toggleAdminPass" style="position: absolute;top: 50%;right: 10px;transform: translateY(-50%);cursor: pointer;color: #999;">&#128065;</span>
				</div>
			</div>
			
			<div class="blocs">
				<h2>Compte Glpi</h2>
				<div class="input-container glpi">
					<label for="pass">activer Glpi </label>
					<input type="checkbox" id="glpitrigger" name="glpitrigger"  />
				</div>
			</div>
			
			<div class="blocs bloc-glpi" id="blocGlpi">
				<label for="host">Nom de l'Hôte Glpi :</label>
				<div class="input-container">
					<span class="icon">🎁</span>
					<input type="text" id="hostGlpi" name="hostGlpi" placeholder="idem que plus haut si vide">
				</div>
				<label for="db">Nom de la base de données Glpi:</label>
				<div class="input-container">
					<span class="icon">💽</span>
					<input type="text" id="dbGlpi" name="dbGlpi" placeholder="glpi si vide">
				</div>

				<label for="user">Utilisateur :</label>
				<div class="input-container">
					<span class="icon">🤚</span>
					<input type="text" id="userGlpi" name="userGlpi" placeholder="idem que plus haut si vide">
				</div>

				<label for="pass">Mot de passe :</label>
				<div class="input-container">
					<span class="icon">🔒</span>
					<input type="password" id="passGlpi" name="passGlpi" style="padding-right: 30px;" placeholder="idem que plus haut si vide">
					<span id="togglePassGlpi" style="
						position: absolute;
						top: 50%;
						right: 10px;
						transform: translateY(-50%);
						cursor: pointer;
						color: #999;
					">&#128065;</span>
				</div>
			</div>
			<div class="blocs center">
				<button type="submit">Installer</button>
			</div>
		</form>
	</div>
	<script>
		const passwordField = document.getElementById('pass');
		const togglePass = document.getElementById('togglePass');
		const passwordAField = document.getElementById('adminpass');
		const toggleAdminPass = document.getElementById('toggleAdminPass');

		const passwordAFieldGlpi = document.getElementById('passGlpi');
		const toggleAdminPassGlpi = document.getElementById('togglePassGlpi');

		const glpitrigger = document.getElementById('glpitrigger');
		const blocGlpi = document.getElementById('blocGlpi');

		glpitrigger.addEventListener('click', () => {
			if (glpitrigger.checked) {
				blocGlpi.style.display = 'initial'
			} else {
				blocGlpi.style.display = 'none'
			}
		});
		togglePass.addEventListener('click', () => {
			if (passwordField.type === 'password') {
				passwordField.type = 'text';
				togglePass.innerHTML = '&#128064;';
			} else {
				passwordField.type = 'password';
				togglePass.innerHTML = '&#128065;';
			}
		});
		toggleAdminPass.addEventListener('click', () => {
			if (passwordAField.type === 'password') {
				passwordAField.type = 'text';
				toggleAdminPass.innerHTML = '&#128064;';
			} else {
				passwordAField.type = 'password';
				toggleAdminPass.innerHTML = '&#128065;';
			}
		});
		toggleAdminPassGlpi.addEventListener('click', () => {
			if (passwordAFieldGlpi.type === 'password') {
				passwordAFieldGlpi.type = 'text';
				toggleAdminPassGlpi.innerHTML = '&#128064;';
			} else {
				passwordAFieldGlpi.type = 'password';
				toggleAdminPassGlpi.innerHTML = '&#128065;';
			}
		});
	</script>
</body>
</html>
