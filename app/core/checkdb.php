<?php
namespace app\core;

class CheckDb
{
    private $dbConfigPath = '../app/conf/dbconfig.php';
    private $installPath = '../public/install.php';

    public function __construct() {
        $configFile = file_exists($this->dbConfigPath);
        $installFile = file_exists($this->installPath);


        if (!$configFile && $installFile) {
            echo("go install<br/>");
            header('Location: install.php'); // Redirige vers la l'installation
        }
        elseif ($configFile && !$installFile) {
            $this->check();
        }
        elseif ($configFile && $installFile) {
            echo("dbconfig.php et install.php ne devraient pas exister en même temps ??<br/>");
        }
    }

    public function check()
    {
        require($this->dbConfigPath);

        if (!empty($dbHost) && !empty($dbName) && !empty($dbUser)) {
            try {
                // Créer une connexion PDO
                $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8";
                $pdo = new \PDO($dsn, $dbUser, $dbPassword);

                if (PROD) {
                    // en prod
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
                }
                else {
                    // en dev
                    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                }

                // Vérifier si la table assetstracker existe
                $query = $pdo->query("SHOW TABLES LIKE 'administrateurs'");
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
     * Fonction pour supprimer le fichier install.php
     */
    function deleteFile(){
        unlink(__FILE__);
    }

}
