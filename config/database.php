<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $config = require __DIR__ . '/config.php';
        
        try {
            $this->conn = new PDO(
                "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']}",
                $config['db_user'],
                $config['db_pass']
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}