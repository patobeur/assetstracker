<?php
    session_start();
    define('PROD',0); // 0 en dev, 1 en prod
    
    $dbConfigPath = '../app/conf/dbconfig.php';
    $defaultHost = 'localhost';
    $defaultUser = 'root';
    $defaultDb = 'assetstracker';

    $defaultAdminPseudo = 'admin';
    $defaultAdminPasse = '';

    $defaultAdminMail = 'admin@example.com';
    $defaultAdminPrenom = 'admin';
    $defaultAdminNom = 'admin';
    $defaultAdminTypeaccount = 1;


    if (file_exists($dbConfigPath)) {
        deleteFile(); // Supprime ce script
        header('Location: /'); // Redirige vers la racine du site
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {


        $dbHost = trim($_POST['host'] ?? '');
        $dbName = trim($_POST['db'] ?? '');
        $dbUser = trim($_POST['user'] ?? '');
        $dbPassword = $_POST['pass'] ?? '';
        $defaultAdminPseudo = trim($_POST['adminpseudo'] ?? '');
        $defaultAdminPasse = $_POST['adminpass'] ?? '';
        
        $dbHost = filter_var($dbHost, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!filter_var($dbHost, FILTER_VALIDATE_DOMAIN)) {
            die("Hôte invalide");
        }

        if (empty($dbHost) || empty($dbName) || empty($dbUser) || empty($defaultAdminPseudo) || empty($defaultAdminPasse)) {
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
                    "CREATE TABLE IF NOT EXISTS typeassets (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        content VARCHAR(100)
                    )",
                    "INSERT INTO typeassets (content) VALUES 
                        ('Ordinateur portable'),
                        ('Chargeur')",
                    "CREATE TABLE IF NOT EXISTS typeaccounts (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        content VARCHAR(100)
                    )",
                    "INSERT INTO typeaccounts  (id, content) VALUES 
                        (1, 'master'),
                        (2, 'supermaster')",




                    "CREATE TABLE IF NOT EXISTS administrateurs (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        pseudo VARCHAR(100) NOT NULL UNIQUE,
                        motdepasse VARCHAR(255) NOT NULL,
                        nom VARCHAR(100),
                        prenom VARCHAR(100),
                        birth TIMESTAMP,
                        mail VARCHAR(255),
                        typeaccount_id INT,
                        FOREIGN KEY (typeaccount_id) REFERENCES typeaccounts(id) ON DELETE CASCADE ON UPDATE CASCADE
                    )",
                    "INSERT INTO administrateurs (pseudo, motdepasse, nom, prenom, mail, typeaccount_id) VALUES 
                        ('".
                        $defaultAdminPseudo."', '".
                        password_hash($defaultAdminPasse, PASSWORD_DEFAULT)."', '".
                        $defaultAdminNom."', '".
                        $defaultAdminPrenom."', '".
                        $defaultAdminMail."', '".
                        $defaultAdminTypeaccount."')",
                    "CREATE TABLE IF NOT EXISTS pc (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        barrecode VARCHAR(50),
                        model VARCHAR(100),
                        serialnum VARCHAR(100),
                        birth TIMESTAMP,
                        etat VARCHAR(50),
                        typeasset_id INT,
                        FOREIGN KEY (typeasset_id) REFERENCES typeassets(id) ON DELETE CASCADE ON UPDATE CASCADE
                    )",

                    "INSERT INTO pc (barrecode, model, serialnum, etat, typeasset_id) VALUES
                        ('10000001', 'Dell Inspiron', 'SN12345', 'Disponible', 1),
                        ('10000001', 'HP EliteBook', 'SN67890', 'En réparation', 1)",


                    "CREATE TABLE IF NOT EXISTS eleves (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        barrecode VARCHAR(50),
                        nom VARCHAR(100),
                        prenom VARCHAR(100),
                        promo VARCHAR(50),
                        classe VARCHAR(50),
                        birth TIMESTAMP,
                        mail VARCHAR(255)
                    )",
                    "INSERT INTO eleves (barrecode, nom, prenom, promo, classe, mail) VALUES
                        ('00000001', 'Doe', 'John', '2426', 'BTSCOM', 'john.doe@example.com'),
                        ('00000011', 'Smith', 'Jane', '2426', 'COM2325', 'jane.smith@example.com')",




                    "CREATE TABLE IF NOT EXISTS timeline (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        birth TIMESTAMP,
                        typeaction VARCHAR(50),
                        ideleves INT,
                        idpc INT,
                        FOREIGN KEY (ideleves) REFERENCES eleves(id) ON DELETE CASCADE ON UPDATE CASCADE,
                        FOREIGN KEY (idpc) REFERENCES pc(id) ON DELETE CASCADE ON UPDATE CASCADE
                    )",

                        


                    "INSERT INTO timeline (ideleves, idpc, typeaction) VALUES (1,1,'out'),(1,2,'in')",


                    "CREATE TABLE IF NOT EXISTS visites (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        datetime TIMESTAMP,
                        administrateurs_id INT,
                        FOREIGN KEY (administrateurs_id) REFERENCES administrateurs(id) ON DELETE CASCADE ON UPDATE CASCADE
                    )",
                ];

                foreach ($queries as $query) {
                    $pdo->exec($query);
                }

                // Création du fichier dbconfig.php
                $configContent = <<<PHP
<?php
    \$dbHost = '$dbHost';
    \$dbName = '$dbName';
    \$dbUser = '$dbUser';
    \$dbPassword = '$dbPassword';
    \$version = 0.1;
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        
        body {
                font-family: Arial, sans-serif;
                background: url('img/login_background_1.webp') no-repeat center center fixed;
                background-size: cover;
                color: #333;
                display: flex;
                justify-content: center;
                align-items: center;
        }

        .container {
            background: #fff;
            border-radius: 8px;
            width: 350px;
            background-color:rgb(236, 236, 236);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
            padding: 10px;
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
            font-weight: bold;
            text-align: left;
        }

        .input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-container input {
            padding: 10px 10px 10px 35px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
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
                    <i class="fa fa-server icon"></i>
                    <input type="text" id="host" name="host" required value="<?php echo $defaultHost; ?>">
                </div>

                <label for="db">Nom de la base de données :</label>
                <div class="input-container">
                    <i class="fa fa-database icon"></i>
                    <input type="text" id="db" name="db" required value="<?php echo $defaultDb; ?>">
                </div>

                <label for="user">Utilisateur :</label>
                <div class="input-container">
                    <i class="fa fa-user icon"></i>
                    <input type="text" id="user" name="user" required value="<?php echo $defaultUser; ?>">
                </div>

                <label for="pass">Mot de passe :</label>
                <div class="input-container">
                    <i class="fa fa-lock icon"></i>
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
                <label for="user">Pseudo Admin :</label>
                <div class="input-container"><i class="fa fa-user icon"></i><input type="text" id="adminpseudo" name="adminpseudo" required value="<?php echo $defaultAdminPseudo; ?>"></div>
                <label for="pass">Mot de passe Admin :</label>
                <div class="input-container">
                    <i class="fa fa-lock icon"></i>
                    <input type="password" id="adminpass" name="adminpass" style="padding-right: 30px;">
                    <span id="toggleAdminPass" style="position: absolute;top: 50%;right: 10px;transform: translateY(-50%);cursor: pointer;color: #999;">&#128065;</span>
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
    </script>
</body>
</html>
