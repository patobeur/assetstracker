<?php
    session_start();
    define('PROD',0); // 0 en dev, 1 en prod
    
    $dbConfigPath = '../app/conf/dbconfig.php';

    // liste des administrateurs/trices à ajouter dans la table admins
    // myObj[n] = [pseudo, nom, MD5(mot de passe), mail]
    $myObj = array();
    $myObj[0] = ["alice", "Alice", MD5('passwordAlice'), "alice@example.com"];
    $myObj[1] = ["bob", "Bob", MD5('passwordBob'), "bob@example.com"];
    $myObj[2] = ["pat", "Pat", MD5('passwordPat'), "pat@example.com"];

    if (file_exists($dbConfigPath)) {
        deleteFile(); // Supprime ce script
        header('Location: /'); // Redirige vers la racine du site
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {


        $dbHost = $_POST['host'] ?? '';
        $dbName = $_POST['db'] ?? '';
        $dbUser = $_POST['user'] ?? '';
        $dbPassword = $_POST['pass'] ?? '';

        if (empty($dbHost) || empty($dbName) || empty($dbUser)) {
            $error = "Veuillez remplir tous les champs.";
        } else {
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
                    "CREATE TABLE IF NOT EXISTS eleves (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        barrecode VARCHAR(50),
                        nom VARCHAR(100),
                        prenom VARCHAR(100),
                        promo VARCHAR(50),
                        classe VARCHAR(50),
                        idaccount INT DEFAULT 0,
                        birth TIMESTAMP,
                        mail VARCHAR(255)
                    )",
                    "CREATE TABLE IF NOT EXISTS administrateurs (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        pseudo VARCHAR(100) NOT NULL UNIQUE,
                        motdepasse VARCHAR(255) NOT NULL,
                        nom VARCHAR(100),
                        prenom VARCHAR(100),
                        idaccount INT DEFAULT 9,
                        birth TIMESTAMP,
                        mail VARCHAR(255)
                    )",
                    "CREATE TABLE IF NOT EXISTS pc (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        barrecode VARCHAR(50),
                        model VARCHAR(100),
                        serialnum VARCHAR(100),
                        birth TIMESTAMP,
                        etat VARCHAR(50)
                    )",
                    "CREATE TABLE IF NOT EXISTS timeline (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        birth TIMESTAMP,
                        ideleves INT,
                        idpc INT
                    )",
                    "CREATE TABLE IF NOT EXISTS typeassets (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        typeasset VARCHAR(100)
                    )",
                    "CREATE TABLE IF NOT EXISTS typeaccounts (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        typeaccount VARCHAR(100)
                    )"
                ];

                foreach ($queries as $query) {
                    $pdo->exec($query);
                }
                
                // liste des administrateurs/trices à ajouter dans la table admins
                // myObj[n] = [pseudo, nom, MD5(mot de passe), mail]
                $myObj = array();
                $myObj[0] = ["alice", "Alice", MD5('passwordAlice'), "alice@example.com"];
                $myObj[1] = ["pat", "Pat", MD5('passwordPat'), "pat@example.com"];
                $myObj[2] = ["bob", "Bob", MD5('passwordBob'), "bob@example.com"];

                // Ajout des administrateurs 0 et 1 avec pseudo et mot de passe
                $insertAdmins = [
                    "INSERT INTO administrateurs (pseudo, motdepasse, nom, prenom, idaccount, birth, mail) VALUES 
                        ('{$myObj[0][0]}', '{$myObj[0][2]}', '{$myObj[0][1]}', 'Admin', 9, NOW(), '{$myObj[0][3]}')",
                    "INSERT INTO administrateurs (pseudo, motdepasse, nom, prenom, idaccount, birth, mail) VALUES 
                        ('{$myObj[1][0]}', '{$myObj[1][2]}', '{$myObj[1][1]}', 'Admin', 9, NOW(), '{$myObj[1][3]}')"
                ];

                foreach ($insertAdmins as $query) {
                    $pdo->exec($query);
                }

                // Insertion des données par défaut
                $pdo->exec("

                    INSERT INTO eleves (barrecode, nom, prenom, classe) VALUES
                    ('00000001', 'Doe', 'John', 'COM2426'),
                    ('00000011', 'Smith', 'Jane', 'COM2325');

                    INSERT INTO pc (barrecode, model, serialnum, etat) VALUES
                    ('10000001', 'Dell Inspiron', 'SN12345', 'Disponible'),
                    ('10000001', 'HP EliteBook', 'SN67890', 'En réparation');

                    INSERT INTO timeline (ideleves, idpc) VALUES (1,1),(1,2);
                ");

                // Création du fichier dbconfig.php
                $configContent = <<<PHP
<?php
    \$dbHost = '$dbHost';
    \$dbName = '$dbName';
    \$dbUser = '$dbUser';
    \$dbPassword = '$dbPassword';
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
    
    /**
     * Fonction pour supprimer le fichier install.php
     */
    function deleteFile(){
        PROD===0 ? rename('install.php', "install.save") : unlink(__FILE__);
    }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer les tables</title>
    <style>
        /* Global styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container styles */
        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 20px;
            text-align: center;
        }

        /* Title styles */
        h1 {
            font-size: 24px;
            color: #555;
            margin-bottom: 20px;
        }

        /* Error message */
        .error {
            color: #ff4d4d;
            font-size: 14px;
            margin-bottom: 15px;
        }

        /* Form styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            text-align: left;
        }

        input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #5b9bd5;
            outline: none;
            box-shadow: 0 0 5px rgba(91, 155, 213, 0.5);
        }

        button {
            padding: 10px;
            background-color: #5b9bd5;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #4178a9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Installation des tables</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="host">Hôte :</label>
            <input type="text" id="host" name="host" required value="localhost">

            <label for="db">Nom de la base de données :</label>
            <input type="text" id="db" name="db" required value="patobeurmvc">

            <label for="user">Utilisateur :</label>
            <input type="text" id="user" name="user" required value="root">

            <label for="pass">Mot de passe :</label>
            <input type="password" id="pass" name="pass">

            <button type="submit">Installer</button>
        </form>
    </div>
</body>
</html>
