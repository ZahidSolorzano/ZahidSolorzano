<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

if (!isset($_SESSION["RPE"])) {
    header("Location: ../vistas/vistalogin.php");
    exit();
}

// Obtener datos del usuario actual (responsable)
$rpe_responsable = $_SESSION["RPE"];
$nombre_responsable = $_SESSION["nombre"] ?? '';

// Validar datos del formulario
$required_fields = ['numero_serie_equipo', 'rpe_solicitante', 'fecha_inicio', 'fecha_fin'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "Todos los campos obligatorios deben ser completados.";
        header("Location: ../prestamos/agregar_prestamo.php");
        exit();
    }
}

$numero_serie_equipo = trim($_POST['numero_serie_equipo']);
$rpe_solicitante = trim($_POST['rpe_solicitante']);
$observaciones = trim($_POST['observaciones'] ?? '');

// 1. Recibir datos del HTML
$fecha_inicio_input = $_POST['fecha_inicio'];
$fecha_fin_input = $_POST['fecha_fin'];

// 2. Convertir y Limpiar
$fecha_inicio = date('Y-m-d H:i:s', strtotime($fecha_inicio_input));
$fecha_fin = date('Y-m-d H:i:s', strtotime($fecha_fin_input));

// 3. Crear timestamps basados en las fechas limpias
$fecha_inicio_timestamp = strtotime($fecha_inicio);
$fecha_fin_timestamp = strtotime($fecha_fin);
$hoy_medianoche = strtotime('today');

// 4. Validaciones
if ($fecha_inicio_timestamp >= $fecha_fin_timestamp) {
    $_SESSION['error'] = "La fecha de fin debe ser posterior a la fecha de inicio.";
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

if ($fecha_inicio_timestamp < $hoy_medianoche) {
    $_SESSION['error'] = "La fecha de inicio no puede ser en días anteriores.";
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

// Verificar que el equipo existe, está disponible y el usuario es responsable
$query_equipo = "SELECT * FROM equipos 
                 WHERE Numero_serie = ? AND RPE_responsable = ? AND Estado = 'Disponible'";
$stmt_equipo = $conn->prepare($query_equipo);

if (!$stmt_equipo) {
    $_SESSION['error'] = "Error en la consulta del equipo: " . $conn->error;
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

$stmt_equipo->bind_param('ss', $numero_serie_equipo, $rpe_responsable);
$stmt_equipo->execute();
$result_equipo = $stmt_equipo->get_result();

if ($result_equipo->num_rows === 0) {
    $_SESSION['error'] = "El equipo seleccionado no está disponible, no existe o no eres el responsable.";
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

// Guardar valores originales del responsable (que es el usuario actual)
$rpe_responsable_original = $rpe_responsable;
$nombre_responsable_original = $nombre_responsable;

// Obtener información del solicitante desde la tabla usuarios
$query_solicitante = "SELECT nombre, departamento, division FROM usuarios WHERE RPE = ?";
$stmt_solicitante = $conn->prepare($query_solicitante);

if (!$stmt_solicitante) {
    $_SESSION['error'] = "Error en la consulta del solicitante: " . $conn->error;
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

$stmt_solicitante->bind_param('s', $rpe_solicitante);
$stmt_solicitante->execute();
$result_solicitante = $stmt_solicitante->get_result();

if ($result_solicitante->num_rows === 0) {
    $_SESSION['error'] = "El RPE del solicitante no existe en el sistema.";
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

$solicitante = $result_solicitante->fetch_assoc();
$nombre_solicitante = $solicitante['nombre'];
$departamento_solicitante = $solicitante['departamento'];
$division_solicitante = $solicitante['division'];

// Insertar el préstamo en la base de datos
try {
    $conn->begin_transaction();
    
    $sql_insert = "INSERT INTO prestamos (
    numero_serie_equipo, 
    rpe_solicitante, 
    nombre_solicitante, 
    departamento_solicitante, 
    division_solicitante,
    rpe_responsable,
    nombre_responsable,
    fecha_solicitud,
    fecha_inicio_prestamo,
    fecha_fin_prestamo,
    observaciones,
    estado_prestamo,
    rpe_responsable_original,
    nombre_responsable_original
) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, 'esperando_inicio', ?, ?)";

$stmt_insert = $conn->prepare($sql_insert);

if (!$stmt_insert) {
    throw new Exception("Error al preparar la inserción: " . $conn->error);
}


$stmt_insert->bind_param(
    'ssssssssssss',  
    $numero_serie_equipo,
    $rpe_solicitante,
    $nombre_solicitante,
    $departamento_solicitante,
    $division_solicitante,
    $rpe_responsable,
    $nombre_responsable,
    $fecha_inicio,
    $fecha_fin,
    $observaciones,
    $rpe_responsable_original,
    $nombre_responsable_original
);
    
    if (!$stmt_insert->execute()) {
        throw new Exception("Error al crear el préstamo: " . $stmt_insert->error);
    }
    

    
    $conn->commit();
    
    $_SESSION['success'] = "Préstamo realizado exitosamente. El préstamo se activará automáticamente en la fecha de inicio.";
    header("Location: ../prestamos/gestion_prestamos.php");
    exit();
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    $_SESSION['error'] = "Error al procesar el préstamo: " . $e->getMessage();
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
} finally {
    // Cerrar todas las declaraciones si existen
    if (isset($stmt_equipo) && $stmt_equipo) $stmt_equipo->close();
    if (isset($stmt_solicitante) && $stmt_solicitante) $stmt_solicitante->close();
    if (isset($stmt_insert) && $stmt_insert) $stmt_insert->close();
    if (isset($conn) && $conn) $conn->close();
}
?>