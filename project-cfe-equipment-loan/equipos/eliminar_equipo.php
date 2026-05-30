<?php
session_start();
if (!isset($_SESSION["RPE"])) {
    header("Location: ../vistas/vistalogin.php");
    exit();
}
require_once '../includes/conexion.php';

// Verificar si el usuario es administrador
$tipo_usuario = $_SESSION["rol"] ?? 'empleado';
if ($tipo_usuario !== 'administrador') {
    $_SESSION['error'] = "No tienes permisos para realizar esta acción.";
    header("Location: gestion_equipos.php");
    exit();
}

// Verificar que se haya proporcionado un número de serie
if (!isset($_GET['serie']) || empty($_GET['serie'])) {
    $_SESSION['error'] = "No se proporcionó el número de serie del equipo.";
    header("Location: gestion_equipos.php");
    exit();
}

$numero_serie = $conn->real_escape_string($_GET['serie']);

// Obtener información del equipo antes de eliminarlo (para el mensaje)
$query_info = "SELECT Numero_serie, Marca, Modelo FROM equipos WHERE Numero_serie = '$numero_serie'";
$result_info = $conn->query($query_info);

if ($result_info->num_rows === 0) {
    $_SESSION['error'] = "El equipo con número de serie '$numero_serie' no existe.";
    header("Location: gestion_equipos.php");
    exit();
}

$equipo_info = $result_info->fetch_assoc();

// Verificar si el equipo tiene préstamos activos (para mostrar advertencia)
$query_prestamos_activos = "SELECT COUNT(*) as total FROM prestamos WHERE numero_serie_equipo = '$numero_serie' AND estado_prestamo IN ('activo', 'esperando_inicio', 'esperando_devolucion')";
$result_prestamos_activos = $conn->query($query_prestamos_activos);
$prestamos_activos = $result_prestamos_activos->fetch_assoc()['total'];

// Verificar si hay algún préstamo (activo o histórico) relacionado
$query_prestamos_total = "SELECT COUNT(*) as total FROM prestamos WHERE numero_serie_equipo = '$numero_serie'";
$result_prestamos_total = $conn->query($query_prestamos_total);
$prestamos_total = $result_prestamos_total->fetch_assoc()['total'];

// Iniciar transacción para asegurar que todas las operaciones se completen
$conn->begin_transaction();

try {
    // 1. PRIMERO: Eliminar TODOS los préstamos relacionados con este equipo
    if ($prestamos_total > 0) {
        $sql_delete_prestamos = "DELETE FROM prestamos WHERE numero_serie_equipo = '$numero_serie'";
        if (!$conn->query($sql_delete_prestamos)) {
            throw new Exception("Error al eliminar préstamos relacionados: " . $conn->error);
        }
    }
    
    // 2. SEGUNDO: Eliminar el equipo
    $sql_delete_equipo = "DELETE FROM equipos WHERE Numero_serie = '$numero_serie'";
    if (!$conn->query($sql_delete_equipo)) {
        throw new Exception("Error al eliminar el equipo: " . $conn->error);
    }
    
    // Confirmar la transacción
    $conn->commit();
    
    // Registro de la acción en logs
    $rpe_usuario = $_SESSION["RPE"];
    $accion = "Eliminación de equipo";
    $detalles = "Equipo eliminado: " . $equipo_info['Marca'] . " " . $equipo_info['Modelo'] . " (Serie: " . $equipo_info['Numero_serie'] . ")";
    $detalles .= ". Préstamos eliminados: " . $prestamos_total . " (activos: " . $prestamos_activos . ")";
    
    
    if ($prestamos_activos > 0) {
        $_SESSION['warning'] = "Equipo eliminado correctamente. Se eliminaron " . $prestamos_total . " préstamos relacionados (" . $prestamos_activos . " activos).";
    } else if ($prestamos_total > 0) {
        $_SESSION['success'] = "Equipo eliminado correctamente. Se eliminaron " . $prestamos_total . " préstamos históricos relacionados.";
    } else {
        $_SESSION['success'] = "Equipo eliminado correctamente: " . $equipo_info['Marca'] . " " . $equipo_info['Modelo'] . " (Serie: " . $equipo_info['Numero_serie'] . ")";
    }
    
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    $_SESSION['error'] = "Error al eliminar el equipo: " . $e->getMessage();
}

// Cerrar conexión
$conn->close();

// Redirigir de vuelta a la gestión de equipos
header("Location: gestion_equipos.php");
exit();
?>