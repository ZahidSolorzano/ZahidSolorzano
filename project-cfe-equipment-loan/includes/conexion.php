<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "inventarios_internos"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicializar event scheduler solo una vez por sesión
if (!isset($_SESSION['event_scheduler_inicializado'])) {
    try {
        $result = $conn->query("SHOW VARIABLES LIKE 'event_scheduler'");
        if ($result && $row = $result->fetch_assoc()) {
            if ($row['Value'] !== 'ON') {
                if ($conn->query("SET GLOBAL event_scheduler = ON")) {
                    error_log("Event scheduler activado para sesión: " . session_id());
                }
            }
        }
        $_SESSION['event_scheduler_inicializado'] = true;
    } catch (Exception $e) {
        // Error silencioso - no afecta funcionalidad principal
        $_SESSION['event_scheduler_inicializado'] = false;
    }
}

if (!isset($_SESSION['evento_prestamos_verificado'])) {
    try {
        // Verificar si existe el evento
        $result = $conn->query("
            SELECT EVENT_NAME 
            FROM information_schema.EVENTS 
            WHERE EVENT_NAME = 'actualizar_estados_prestamos_event'
            AND EVENT_SCHEMA = DATABASE()
        ");
        
        if ($result->num_rows === 0) {
            // Crear el evento si no existe
            $conn->query("
                CREATE EVENT IF NOT EXISTS actualizar_estados_prestamos_event
                ON SCHEDULE EVERY 1 MINUTE
                STARTS CURRENT_TIMESTAMP
                ON COMPLETION PRESERVE
                ENABLE
                DO CALL actualizar_estados_prestamos()
            ");
        }
        
        $_SESSION['evento_prestamos_verificado'] = true;
    } catch (Exception $e) {
        // Error silencioso
    }
}

// Establecer zona horaria también para MySQL
date_default_timezone_set('America/Mexico_City');


?>