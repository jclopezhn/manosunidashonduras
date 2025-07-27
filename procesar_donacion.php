<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'models/Donacion.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    // Obtener datos del formulario
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST; // Fallback para formularios tradicionales
    }

    // Validar campos requeridos
    $campos_requeridos = ['nombre', 'email'];
    foreach ($campos_requeridos as $campo) {
        if (empty($input[$campo])) {
            throw new Exception("El campo '$campo' es requerido");
        }
    }

    // Validar email
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("El formato del email no es válido");
    }

    // Crear instancia de donación
    $donacion = new Donacion();
    
    // Asignar valores
    $donacion->nombre = $input['nombre'];
    $donacion->email = $input['email'];
    $donacion->telefono = $input['telefono'] ?? '';
    $donacion->direccion = $input['direccion'] ?? '';
    
    // Procesar donaciones seleccionadas
    $donaciones_seleccionadas = $input['donation'] ?? [];
    
    // Inicializar cantidades en 0
    $donacion->alimentos_cantidad = 0;
    $donacion->ropa_cantidad = 0;
    $donacion->medicamentos_cantidad = 0;
    $donacion->utiles_cantidad = 0;
    $donacion->juguetes_cantidad = 0;
    $donacion->dinero_monto = 0;

    // Procesar cada tipo de donación
    foreach ($donaciones_seleccionadas as $tipo) {
        switch ($tipo) {
            case 'alimentos':
                $donacion->alimentos_cantidad = intval($input['alimentos_cantidad'] ?? 1);
                break;
            case 'ropa':
                $donacion->ropa_cantidad = intval($input['ropa_cantidad'] ?? 1);
                break;
            case 'medicamentos':
                $donacion->medicamentos_cantidad = intval($input['medicamentos_cantidad'] ?? 1);
                break;
            case 'utiles':
                $donacion->utiles_cantidad = intval($input['utiles_cantidad'] ?? 1);
                break;
            case 'juguetes':
                $donacion->juguetes_cantidad = intval($input['juguetes_cantidad'] ?? 1);
                break;
            case 'dinero':
                $donacion->dinero_monto = floatval($input['dinero_monto'] ?? 100);
                break;
        }
    }

    // Guardar en la base de datos
    $id_donacion = $donacion->crear();
    
    if ($id_donacion) {
        // Enviar email de confirmación (opcional)
        enviarEmailConfirmacion($donacion);
        
        // Respuesta exitosa
        echo json_encode([
            'success' => true,
            'message' => '¡Gracias por tu donación! Hemos recibido tu solicitud correctamente.',
            'id_donacion' => $id_donacion,
            'datos' => [
                'nombre' => $donacion->nombre,
                'email' => $donacion->email,
                'alimentos' => $donacion->alimentos_cantidad,
                'ropa' => $donacion->ropa_cantidad,
                'medicamentos' => $donacion->medicamentos_cantidad,
                'utiles' => $donacion->utiles_cantidad,
                'juguetes' => $donacion->juguetes_cantidad,
                'dinero' => $donacion->dinero_monto
            ]
        ]);
    } else {
        throw new Exception("Error al guardar la donación en la base de datos");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

// Función para enviar email de confirmación
function enviarEmailConfirmacion($donacion) {
    $to = $donacion->email;
    $subject = "Confirmación de Donación - ONG Manos Unidas";
    
    $message = "
    <html>
    <head>
        <title>Confirmación de Donación</title>
    </head>
    <body>
        <h2>¡Gracias por tu donación!</h2>
        <p>Hola {$donacion->nombre},</p>
        <p>Hemos recibido tu donación correctamente. Aquí están los detalles:</p>
        
        <h3>Resumen de tu donación:</h3>
        <ul>";
    
    if ($donacion->alimentos_cantidad > 0) {
        $message .= "<li>Alimentos: {$donacion->alimentos_cantidad} unidades</li>";
    }
    if ($donacion->ropa_cantidad > 0) {
        $message .= "<li>Ropa: {$donacion->ropa_cantidad} unidades</li>";
    }
    if ($donacion->medicamentos_cantidad > 0) {
        $message .= "<li>Medicamentos: {$donacion->medicamentos_cantidad} unidades</li>";
    }
    if ($donacion->utiles_cantidad > 0) {
        $message .= "<li>Útiles escolares: {$donacion->utiles_cantidad} unidades</li>";
    }
    if ($donacion->juguetes_cantidad > 0) {
        $message .= "<li>Juguetes: {$donacion->juguetes_cantidad} unidades</li>";
    }
    if ($donacion->dinero_monto > 0) {
        $message .= "<li>Donación monetaria: L. {$donacion->dinero_monto}</li>";
    }
    
    $message .= "
        </ul>
        
        <p>Nos pondremos en contacto contigo pronto para coordinar la recolección de tu donación.</p>
        
        <p>Atentamente,<br>
        Equipo de ONG Manos Unidas</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: ONG Manos Unidas <noreply@manosunidas.org>" . "\r\n";
    
    mail($to, $subject, $message, $headers);
}
?> 