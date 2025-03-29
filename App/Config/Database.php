<?php 
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            try {
                // Vérification plus stricte des variables d'environnement
                $host = self::getEnvVar('DB_HOST', 'Database host');
                $dbname = self::getEnvVar('DB_NAME', 'Database name');
                $username = self::getEnvVar('DB_USER', 'Database username');
                $password = self::getEnvVar('DB_PASS', 'Database password');
                
                $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

                // Options PDO plus sécurisées
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 5, // Timeout de 5 secondes
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                    PDO::ATTR_PERSISTENT => false
                ];

                self::$connection = new PDO($dsn, $username, $password, $options);
                
                // Désactivation de l'affichage des erreurs contenant des infos sensibles
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // Journaliser l'erreur sans exposer les détails sensibles
                error_log("Database connection failed: " . $e->getMessage());
                throw new \RuntimeException("Impossible de se connecter à la base de données. Veuillez contacter l'administrateur.");
            }
        }
        return self::$connection;
    }
    
    /**
     * Récupère une variable d'environnement de manière sécurisée
     */
    private static function getEnvVar($name, $description) {
        $value = $_ENV[$name] ?? null;
        if ($value === null || trim($value) === '') {
            error_log("$description not set in environment ($name)");
            throw new \RuntimeException("$description not properly configured");
        }
        return $value;
    }
    
    /**
     * Ferme explicitement la connexion
     */
    public static function closeConnection() {
        self::$connection = null;
    }
}
?>
