# ONG Manos Unidas - Sistema de Donaciones

Este proyecto incluye un sistema completo de donaciones para la ONG Manos Unidas con conexión a base de datos MySQL.

## Características

- ✅ Formulario de donaciones interactivo
- ✅ Conexión a base de datos MySQL
- ✅ Validación de formularios
- ✅ Envío de emails de confirmación
- ✅ Interfaz responsiva con Tailwind CSS
- ✅ Sistema de gestión de voluntarios
- ✅ Seguimiento de donaciones
- ✅ Estadísticas y reportes

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensión PDO para PHP
- Extensión MySQL para PHP

## Instalación

### 1. Configuración de la Base de Datos

1. **Crear la base de datos:**
   ```sql
   -- Ejecutar el archivo database/schema.sql en tu servidor MySQL
   mysql -u root -p < database/schema.sql
   ```

2. **Configurar la conexión:**
   Edita el archivo `config/database.php` y actualiza los datos de conexión:
   ```php
   private $host = 'localhost';
   private $db_name = 'ong_manos_unidas';
   private $username = 'tu_usuario';
   private $password = 'tu_contraseña';
   ```

### 2. Configuración del Servidor Web

1. **Coloca los archivos en tu servidor web:**
   ```
   /var/www/html/ong-manos-unidas/
   ├── Proyecto_ong.html
   ├── config/
   │   └── database.php
   ├── models/
   │   └── Donacion.php
   ├── database/
   │   └── schema.sql
   ├── procesar_donacion.php
   └── README.md
   ```

2. **Configurar permisos:**
   ```bash
   chmod 755 /var/www/html/ong-manos-unidas/
   chmod 644 /var/www/html/ong-manos-unidas/*.php
   ```

### 3. Configuración de Email (Opcional)

Para habilitar el envío de emails de confirmación, configura tu servidor de correo en `procesar_donacion.php`:

```php
// Configurar servidor SMTP
ini_set('SMTP', 'tu_servidor_smtp.com');
ini_set('smtp_port', '587');
```

## Estructura de la Base de Datos

### Tablas Principales

1. **donaciones** - Almacena todas las donaciones recibidas
2. **voluntarios** - Registro de voluntarios y donadores
3. **actividades** - Gestión de actividades y eventos
4. **beneficiarios** - Personas que reciben ayuda
5. **seguimiento_donaciones** - Historial de cambios de estado

### Vistas Útiles

- `vista_donaciones_recientes` - Donaciones de los últimos 30 días
- `vista_estadisticas_donaciones` - Estadísticas generales

## Uso del Sistema

### 1. Formulario de Donaciones

El formulario permite seleccionar múltiples tipos de donación:
- Alimentos no perecederos
- Ropa y calzado
- Medicamentos
- Útiles escolares
- Juguetes
- Donación monetaria

### 2. Procesamiento de Donaciones

1. El usuario llena el formulario
2. Se validan los datos en el frontend
3. Se envían al servidor via AJAX
4. Se procesan y guardan en la base de datos
5. Se envía email de confirmación
6. Se muestra mensaje de éxito/error

### 3. Gestión de Voluntarios

El sistema incluye un formulario de registro para voluntarios con:
- Información personal
- Tipo de ayuda (voluntario/donador/ambos)
- Habilidades y disponibilidad
- Preferencias de comunicación

## API Endpoints

### POST /procesar_donacion.php

Procesa una nueva donación.

**Parámetros:**
```json
{
  "nombre": "Juan Pérez",
  "email": "juan@ejemplo.com",
  "telefono": "+504 1234-5678",
  "direccion": "Col. Palmira, Tegucigalpa",
  "donation": ["alimentos", "ropa", "dinero"],
  "alimentos_cantidad": 5,
  "ropa_cantidad": 3,
  "dinero_monto": 500
}
```

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "¡Gracias por tu donación! Hemos recibido tu solicitud correctamente.",
  "id_donacion": 123,
  "datos": {
    "nombre": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "alimentos": 5,
    "ropa": 3,
    "dinero": 500
  }
}
```

## Seguridad

- ✅ Validación de datos en frontend y backend
- ✅ Sanitización de inputs
- ✅ Prepared statements para prevenir SQL injection
- ✅ Validación de email
- ✅ Headers de seguridad CORS

## Mantenimiento

### Backup de Base de Datos

```bash
mysqldump -u root -p ong_manos_unidas > backup_$(date +%Y%m%d).sql
```

### Logs de Errores

Los errores se registran en:
- Logs del servidor web (Apache/Nginx)
- Logs de PHP
- Respuestas JSON del API

## Personalización

### Cambiar Colores

Edita las clases de Tailwind CSS en `Proyecto_ong.html`:
- `teal-700` → `blue-700` (azul)
- `teal-800` → `green-800` (verde)
- `teal-600` → `purple-600` (morado)

### Agregar Nuevos Tipos de Donación

1. Agregar campo en la tabla `donaciones`
2. Actualizar el modelo `Donacion.php`
3. Modificar el formulario HTML
4. Actualizar el procesamiento en `procesar_donacion.php`

## Soporte

Para soporte técnico o preguntas sobre el sistema, contacta al equipo de desarrollo.

## Licencia

Este proyecto está desarrollado para uso educativo y sin fines de lucro.

---

**ONG Manos Unidas** - Trabajando por un mundo más justo y solidario desde 1995. 