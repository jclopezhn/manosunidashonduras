<?php
// Archivo de prueba para verificar la conexión a la base de datos
require_once 'config/database.php';

echo "<h1>Prueba de Conexión - ONG Manos Unidas</h1>";

try {
    // Crear instancia de la base de datos
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
        echo "✅ Conexión a la base de datos exitosa!";
        echo "</div>";
        
        // Verificar que las tablas existen
        $tablas = ['donaciones', 'voluntarios', 'actividades', 'beneficiarios'];
        
        echo "<h2>Verificación de Tablas:</h2>";
        foreach ($tablas as $tabla) {
            $query = "SHOW TABLES LIKE '$tabla'";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                echo "<div style='color: green; margin: 5px 0;'>";
                echo "✅ Tabla '$tabla' existe";
                echo "</div>";
            } else {
                echo "<div style='color: red; margin: 5px 0;'>";
                echo "❌ Tabla '$tabla' no existe";
                echo "</div>";
            }
        }
        
        // Mostrar información de la base de datos
        echo "<h2>Información de la Base de Datos:</h2>";
        $query = "SELECT DATABASE() as db_name, VERSION() as version";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<p><strong>Base de datos:</strong> " . $info['db_name'] . "</p>";
        echo "<p><strong>Versión MySQL:</strong> " . $info['version'] . "</p>";
        
        // Contar registros en cada tabla
        echo "<h2>Estadísticas de Registros:</h2>";
        foreach ($tablas as $tabla) {
            $query = "SELECT COUNT(*) as total FROM $tabla";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<p><strong>$tabla:</strong> " . $result['total'] . " registros</p>";
        }
        
    } else {
        echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
        echo "❌ Error: No se pudo establecer la conexión a la base de datos";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
    echo "❌ Error de conexión: " . $e->getMessage();
    echo "</div>";
}

// Verificar extensiones PHP necesarias
echo "<h2>Verificación de Extensiones PHP:</h2>";
$extensiones = ['pdo', 'pdo_mysql', 'json'];

foreach ($extensiones as $ext) {
    if (extension_loaded($ext)) {
        echo "<div style='color: green; margin: 5px 0;'>";
        echo "✅ Extensión '$ext' está habilitada";
        echo "</div>";
    } else {
        echo "<div style='color: red; margin: 5px 0;'>";
        echo "❌ Extensión '$ext' no está habilitada";
        echo "</div>";
    }
}

// Información del servidor
echo "<h2>Información del Servidor:</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<hr>";
echo "<p><em>Si todos los elementos muestran ✅, tu configuración está lista para usar el sistema de donaciones.</em></p>";
echo "<p><a href='Proyecto_ong.html'>← Volver al sitio principal</a></p>";
?> 