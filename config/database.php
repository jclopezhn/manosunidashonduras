<?php
// Configuración de la base de datos MySQL
class Database {
    private $host = 'switchyard.proxy.rlwy.net';
    private $db_name = 'railway';
    private $username = 'root';
    private $password = 'kBcDffzEIXJWmcZXzwlbQhHIsFsKDSQF';
    private $conn;
    //mysql://root:kBcDffzEIXJWmcZXzwlbQhHIsFsKDSQF@switchyard.proxy.rlwy.net:46054/railway
    // Obtener conexión a la base de datos
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?> 