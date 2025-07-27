<?php
require_once dirname(__DIR__) . '/config/database.php';

class Donacion {
    private $conn;
    private $table_name = "donaciones";

    // Propiedades de la donación
    public $id;
    public $nombre;
    public $email;
    public $telefono;
    public $direccion;
    public $alimentos_cantidad;
    public $ropa_cantidad;
    public $medicamentos_cantidad;
    public $utiles_cantidad;
    public $juguetes_cantidad;
    public $dinero_monto;
    public $fecha_creacion;
    public $estado;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear nueva donación
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . "
                (nombre, email, telefono, direccion, 
                 alimentos_cantidad, ropa_cantidad, medicamentos_cantidad, 
                 utiles_cantidad, juguetes_cantidad, dinero_monto, 
                 fecha_creacion, estado)
                VALUES
                (:nombre, :email, :telefono, :direccion,
                 :alimentos_cantidad, :ropa_cantidad, :medicamentos_cantidad,
                 :utiles_cantidad, :juguetes_cantidad, :dinero_monto,
                 NOW(), 'pendiente')";

        $stmt = $this->conn->prepare($query);

        // Limpiar y sanitizar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":direccion", $this->direccion);
        $stmt->bindParam(":alimentos_cantidad", $this->alimentos_cantidad);
        $stmt->bindParam(":ropa_cantidad", $this->ropa_cantidad);
        $stmt->bindParam(":medicamentos_cantidad", $this->medicamentos_cantidad);
        $stmt->bindParam(":utiles_cantidad", $this->utiles_cantidad);
        $stmt->bindParam(":juguetes_cantidad", $this->juguetes_cantidad);
        $stmt->bindParam(":dinero_monto", $this->dinero_monto);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Leer todas las donaciones
    public function leer() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer una donación específica
    public function leerUno() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nombre = $row['nombre'];
            $this->email = $row['email'];
            $this->telefono = $row['telefono'];
            $this->direccion = $row['direccion'];
            $this->alimentos_cantidad = $row['alimentos_cantidad'];
            $this->ropa_cantidad = $row['ropa_cantidad'];
            $this->medicamentos_cantidad = $row['medicamentos_cantidad'];
            $this->utiles_cantidad = $row['utiles_cantidad'];
            $this->juguetes_cantidad = $row['juguetes_cantidad'];
            $this->dinero_monto = $row['dinero_monto'];
            $this->fecha_creacion = $row['fecha_creacion'];
            $this->estado = $row['estado'];
            return true;
        }
        return false;
    }

    // Actualizar estado de donación
    public function actualizarEstado($estado) {
        $query = "UPDATE " . $this->table_name . " SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":id", $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obtener estadísticas de donaciones
    public function obtenerEstadisticas() {
        $query = "SELECT 
                    COUNT(*) as total_donaciones,
                    SUM(dinero_monto) as total_dinero,
                    SUM(alimentos_cantidad) as total_alimentos,
                    SUM(ropa_cantidad) as total_ropa,
                    SUM(medicamentos_cantidad) as total_medicamentos,
                    SUM(utiles_cantidad) as total_utiles,
                    SUM(juguetes_cantidad) as total_juguetes
                  FROM " . $this->table_name . " 
                  WHERE estado = 'completada'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>