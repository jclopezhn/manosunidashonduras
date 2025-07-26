-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS ong_manos_unidas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ong_manos_unidas;

-- Tabla de donaciones
CREATE TABLE IF NOT EXISTS donaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefono VARCHAR(50),
    direccion TEXT,
    alimentos_cantidad INT DEFAULT 0,
    ropa_cantidad INT DEFAULT 0,
    medicamentos_cantidad INT DEFAULT 0,
    utiles_cantidad INT DEFAULT 0,
    juguetes_cantidad INT DEFAULT 0,
    dinero_monto DECIMAL(10,2) DEFAULT 0.00,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'en_proceso', 'completada', 'cancelada') DEFAULT 'pendiente',
    notas TEXT,
    INDEX idx_email (email),
    INDEX idx_fecha_creacion (fecha_creacion),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de voluntarios
CREATE TABLE IF NOT EXISTS voluntarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(50),
    ciudad VARCHAR(100),
    tipo_ayuda ENUM('voluntario', 'donador', 'ambos') NOT NULL,
    habilidades TEXT,
    disponibilidad ENUM('completa', 'parcial', 'fines', 'eventos') NOT NULL,
    como_conocio TEXT,
    acepta_privacidad BOOLEAN DEFAULT FALSE,
    acepta_newsletter BOOLEAN DEFAULT FALSE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo', 'pendiente') DEFAULT 'pendiente',
    INDEX idx_email (email),
    INDEX idx_tipo_ayuda (tipo_ayuda),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de actividades
CREATE TABLE IF NOT EXISTS actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo ENUM('alimentacion', 'educacion', 'salud', 'otro') NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    ubicacion VARCHAR(255),
    estado ENUM('planificada', 'en_curso', 'completada', 'cancelada') DEFAULT 'planificada',
    voluntarios_necesarios INT DEFAULT 0,
    voluntarios_inscritos INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_fecha_inicio (fecha_inicio),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de participación en actividades
CREATE TABLE IF NOT EXISTS participacion_actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voluntario_id INT NOT NULL,
    actividad_id INT NOT NULL,
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('inscrito', 'confirmado', 'asistio', 'no_asistio') DEFAULT 'inscrito',
    FOREIGN KEY (voluntario_id) REFERENCES voluntarios(id) ON DELETE CASCADE,
    FOREIGN KEY (actividad_id) REFERENCES actividades(id) ON DELETE CASCADE,
    UNIQUE KEY unique_participacion (voluntario_id, actividad_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de beneficiarios
CREATE TABLE IF NOT EXISTS beneficiarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    apellidos VARCHAR(255),
    fecha_nacimiento DATE,
    genero ENUM('masculino', 'femenino', 'otro'),
    telefono VARCHAR(50),
    direccion TEXT,
    ciudad VARCHAR(100),
    tipo_ayuda_recibida ENUM('alimentacion', 'educacion', 'salud', 'vivienda', 'otro'),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    notas TEXT,
    INDEX idx_tipo_ayuda (tipo_ayuda_recibida),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de seguimiento de donaciones
CREATE TABLE IF NOT EXISTS seguimiento_donaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donacion_id INT NOT NULL,
    estado_anterior ENUM('pendiente', 'en_proceso', 'completada', 'cancelada'),
    estado_nuevo ENUM('pendiente', 'en_proceso', 'completada', 'cancelada'),
    comentario TEXT,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_cambio VARCHAR(100),
    FOREIGN KEY (donacion_id) REFERENCES donaciones(id) ON DELETE CASCADE,
    INDEX idx_donacion_id (donacion_id),
    INDEX idx_fecha_cambio (fecha_cambio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos de ejemplo para actividades
INSERT INTO actividades (titulo, descripcion, tipo, fecha_inicio, fecha_fin, ubicacion, voluntarios_necesarios) VALUES
('Campaña de Recolección de Alimentos', 'Recolección de alimentos no perecederos para familias necesitadas', 'alimentacion', '2024-01-15', '2024-01-20', 'Centro Comercial Multiplaza', 10),
('Taller de Alfabetización', 'Clases de lectura y escritura para adultos', 'educacion', '2024-01-22', '2024-03-22', 'Centro Comunitario Palmira', 5),
('Jornada Médica Gratuita', 'Atención médica básica y entrega de medicamentos', 'salud', '2024-02-10', '2024-02-10', 'Colonia Kennedy', 8);

-- Crear vistas útiles
CREATE VIEW vista_donaciones_recientes AS
SELECT 
    id,
    nombre,
    email,
    alimentos_cantidad,
    ropa_cantidad,
    medicamentos_cantidad,
    utiles_cantidad,
    juguetes_cantidad,
    dinero_monto,
    fecha_creacion,
    estado
FROM donaciones 
WHERE fecha_creacion >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY fecha_creacion DESC;

CREATE VIEW vista_estadisticas_donaciones AS
SELECT 
    COUNT(*) as total_donaciones,
    SUM(dinero_monto) as total_dinero,
    SUM(alimentos_cantidad) as total_alimentos,
    SUM(ropa_cantidad) as total_ropa,
    SUM(medicamentos_cantidad) as total_medicamentos,
    SUM(utiles_cantidad) as total_utiles,
    SUM(juguetes_cantidad) as total_juguetes,
    AVG(dinero_monto) as promedio_donacion
FROM donaciones 
WHERE estado = 'completada';

-- Crear procedimientos almacenados
DELIMITER //

CREATE PROCEDURE sp_actualizar_estado_donacion(
    IN p_donacion_id INT,
    IN p_nuevo_estado VARCHAR(20),
    IN p_usuario VARCHAR(100),
    IN p_comentario TEXT
)
BEGIN
    DECLARE v_estado_anterior VARCHAR(20);
    
    -- Obtener estado anterior
    SELECT estado INTO v_estado_anterior 
    FROM donaciones 
    WHERE id = p_donacion_id;
    
    -- Actualizar estado
    UPDATE donaciones 
    SET estado = p_nuevo_estado 
    WHERE id = p_donacion_id;
    
    -- Registrar cambio en seguimiento
    INSERT INTO seguimiento_donaciones (donacion_id, estado_anterior, estado_nuevo, comentario, usuario_cambio)
    VALUES (p_donacion_id, v_estado_anterior, p_nuevo_estado, p_comentario, p_usuario);
    
END //

CREATE PROCEDURE sp_obtener_donaciones_por_periodo(
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    SELECT 
        id,
        nombre,
        email,
        alimentos_cantidad,
        ropa_cantidad,
        medicamentos_cantidad,
        utiles_cantidad,
        juguetes_cantidad,
        dinero_monto,
        fecha_creacion,
        estado
    FROM donaciones 
    WHERE DATE(fecha_creacion) BETWEEN p_fecha_inicio AND p_fecha_fin
    ORDER BY fecha_creacion DESC;
END //

DELIMITER ; 