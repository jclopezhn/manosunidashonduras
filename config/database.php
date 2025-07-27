<?php
// Configuración de la base de datos MySQL

class Database {
    // Parámetros de conexión remota
    private $host = 'if0_39568676_ong_manos_unidas';
    private $db_name = 'if0_39568676_ong_manos_unidas';
    private $username = 'if0_39568676';
    private $password = 'cDLEIKu5GL4U8s';

    // Parámetros de conexión local (opcional, descomentar si se usa localmente)
    //private $host = 'localhost';
    //private $db_name = 'ong_manos_unidas';
    //private $username = 'root';
    //private $password = '';
    //private $conn;

    
    
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