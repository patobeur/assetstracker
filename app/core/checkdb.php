<?php
namespace app\core;

class CheckDb
{
    private $dbConfigPath = '../app/conf/dbconfig.php';
    private $installPath = '../public/install.php';
    private $pdo;

    public function __construct() {
        $configFile = file_exists($this->dbConfigPath);
        $installFile = file_exists($this->installPath);


        if (!$configFile && $installFile) {
            // echo("go install<br/>");
            header('Location: install.php'); // Redirige vers la l'installation
            exit;
        }
        elseif ($configFile && !$installFile) {
            $this->check();
        }
        elseif ($configFile && $installFile) {
            echo("dbconfig.php et install.php ne devraient pas exister en même temps ??<br/>");
        }
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    public function check()
    {
        require_once($this->dbConfigPath);

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
                    $dberror="La table 'administrateurs' n'existe pas dans la base de données.";
                    // header('Location: /');
                }
            } catch (\PDOException $e) {
                $dberror="Erreur lors de la connexion à la base de données : " . $e->getMessage();
                // header('Location: /');
            }
        } else {
            $dberror="Les paramètres de connexion à la base de données sont incomplets.";
            // header('Location: /');
        }
    }

    /**
     * Fonction pour login
     */
    function login($username=null,$password=null){  
        $username = trim($username);
        $password = trim($password);  
        if (empty($username) || empty($password)) {
            $errors[] = "Tous les champs doivent être remplis.";
        }
        if (empty($errors)) {
            
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
        return $errors;
    }

    /**
     * Fonction pour noter les visites
     */
    private function loginUpdate(){  
        // $_SESSION['user']['id']
        $query = "INSERT INTO visites (administrateursid) VALUES (".$_SESSION['user']['id'].")";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
    }
    /**
     * Fonction pour login
     */
    public function listPc(){  
        // $_SESSION['user']['id']
            $query = "SELECT * FROM pc";
            $stmt = $this->pdo->prepare($query);
            // $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
            $stmt->execute();
            $pcs = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $pcs;
    }
}
