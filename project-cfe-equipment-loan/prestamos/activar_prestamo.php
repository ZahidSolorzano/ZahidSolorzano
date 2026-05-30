<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: gestion_prestamos.php?error=ID no recibido");
    exit();
}

$id_prestamo = intval($_GET['id']);

// Obtener información del préstamo
$sql = "SELECT * FROM prestamos WHERE id_prestamo = ? AND estado_prestamo = 'esperando_inicio'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_prestamo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: gestion_prestamos.php?error=Préstamo no encontrado o no está en espera de inicio");
    exit();
}

$prestamo = $result->fetch_assoc();

// INICIAR TRANSACCIÓN
$conn->begin_transaction();

try {
    // 1. Actualizar el equipo con los datos del solicitante
    $sql_update_equipo = "UPDATE equipos 
                          SET Estado = 'En_uso', 
                              RPE_responsable = ?, 
                              Nombre_responsable = ? 
                          WHERE Numero_serie = ?";
    $stmt_update = $conn->prepare($sql_update_equipo);
    $stmt_update->bind_param(
        "sss", 
        $prestamo['rpe_solicitante'],
        $prestamo['nombre_solicitante'],
        $prestamo['numero_serie_equipo']
    );
    $stmt_update->execute();
    
    // 2. Actualizar el préstamo a estado activo
    $sql_update_prestamo = "UPDATE prestamos 
                            SET estado_prestamo = 'activo',
                                rpe_responsable = ?,      // Cambiar al solicitante
                                nombre_responsable = ?    // Cambiar al solicitante
                            WHERE id_prestamo = ?";
    $stmt_update_prestamo = $conn->prepare($sql_update_prestamo);
    $stmt_update_prestamo->bind_param(
        "ssi",
        $prestamo['rpe_solicitante'],
        $prestamo['nombre_solicitante'],
        $id_prestamo
    );
    $stmt_update_prestamo->execute();
    
    $conn->commit();
    
    header("Location: gestion_prestamos.php?mensaje=Préstamo activado correctamente");
    
} catch (Exception $e) {
    $conn->rollback();
    header("Location: gestion_prestamos.php?error=Error al activar préstamo: " . urlencode($e->getMessage()));
}

exit();
?>