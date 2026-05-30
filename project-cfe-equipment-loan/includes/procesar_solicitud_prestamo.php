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

// Obtener datos del usuario actual
$rpe_solicitante = (string)$_SESSION["RPE"];
$nombre_solicitante = $_SESSION["nombre"] ?? '';
$departamento_solicitante = $_SESSION["departamento"] ?? '';
$division_solicitante = $_SESSION["division"] ?? '';

// Validar datos del formulario
$required_fields = ['numero_serie_equipo', 'fecha_inicio', 'fecha_fin'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "Todos los campos obligatorios deben ser completados.";
        header("Location: ../prestamos/agregar_prestamo.php");
        exit();
    }
}

$numero_serie_equipo = trim($_POST['numero_serie_equipo']);
$departamento_equipo = $equipo['Departamento'];
$division_equipo = $equipo['Division'];
$fecha_inicio_input = $_POST['fecha_inicio'];
$fecha_fin_input = $_POST['fecha_fin'];
$observaciones = trim($_POST['observaciones'] ?? '');

// Conversión de fechas
$fecha_inicio_mysql = date('Y-m-d H:i:s', strtotime($fecha_inicio_input));
$fecha_fin_mysql = date('Y-m-d H:i:s', strtotime($fecha_fin_input));

if (strtotime($fecha_inicio_input) >= strtotime($fecha_fin_input)) {
    $_SESSION['error'] = "La fecha de fin debe ser posterior a la fecha de inicio.";
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

// Verificar que el equipo existe y está disponible
$query_equipo = "SELECT * FROM equipos WHERE Numero_serie = ? AND Estado = 'Disponible'";
$stmt_equipo = $conn->prepare($query_equipo);

if (!$stmt_equipo) {
    $_SESSION['error'] = "Error en la consulta del equipo: " . $conn->error;
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

$stmt_equipo->bind_param('s', $numero_serie_equipo);
$stmt_equipo->execute();
$result_equipo = $stmt_equipo->get_result();

if ($result_equipo->num_rows === 0) {
    $_SESSION['error'] = "El equipo seleccionado no está disponible o no existe.";
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

$equipo = $result_equipo->fetch_assoc();

// TOMAR DEPARTAMENTO Y DIVISIÓN DESDE LA TABLA EQUIPOS
$departamento_equipo = $equipo['Departamento'];
$division_equipo = $equipo['Division'];

// Tomar información del responsable
$rpe_responsable = $equipo['RPE_responsable'];
$nombre_responsable = $equipo['Nombre_responsable'];

// Validar que al menos tenemos los datos básicos
if (empty($rpe_responsable) || empty($nombre_responsable)) {
    $_SESSION['error'] = "El equipo no tiene un responsable asignado correctamente.";
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
}

// Guardar valores originales
$rpe_responsable_original = $rpe_responsable;
$nombre_responsable_original = $nombre_responsable;

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
    $departamento_equipo,      // <-- Departamento del equipo
    $division_equipo,          // <-- División del equipo
    $rpe_responsable,
    $nombre_responsable,
    $fecha_inicio_mysql,
    $fecha_fin_mysql,
    $observaciones,
    $rpe_responsable_original,   // Valor original
    $nombre_responsable_original  // Valor original
    );
    
    if (!$stmt_insert->execute()) {
        throw new Exception("Error al crear el préstamo: " . $stmt_insert->error);
    }
    

    
    $conn->commit();
    
    $_SESSION['success'] = "Solicitud de préstamo creada exitosamente. El préstamo se activará automáticamente en la fecha de inicio.";
    header("Location: ../prestamos/gestion_prestamos.php");
    exit();
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    $_SESSION['error'] = "Error al procesar la solicitud: " . $e->getMessage();
    header("Location: ../prestamos/agregar_prestamo.php");
    exit();
} finally {
    // Cerrar todas las declaraciones si existen
    if (isset($stmt_equipo) && $stmt_equipo) $stmt_equipo->close();
    if (isset($stmt_insert) && $stmt_insert) $stmt_insert->close();
    if (isset($conn) && $conn) $conn->close();
}
?>