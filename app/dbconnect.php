<?php
class Database {
    private static $instance = null;
    private $connexion;

    private function __construct() {
        $config = [
            'USER' => getenv('DB_USER') ?: $_ENV["DB_USER"],
            'PASSWD' => getenv('DB_PASSWORD') ?: $_ENV["DB_PASSWORD"],
            'SERVER' => getenv('DB_HOST') ?: $_ENV["DB_HOST"],
            'BASE' => getenv('DB_NAME') ?: $_ENV["DB_NAME"],
            'PORT' => getenv('DB_PORT') ?: 3306
        ];

        $dsn = "mysql:dbname={$config['BASE']};host={$config['SERVER']};port={$config['PORT']}";

        try {
            $this->connexion = new PDO($dsn, $config['USER'], $config['PASSWD']);
            $this->connexion->exec("set names utf8");
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            error_log("Échec de la connexion : " . $e->getMessage());
            die("Une erreur est survenue. Veuillez réessayer plus tard.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->connexion;
    }
}