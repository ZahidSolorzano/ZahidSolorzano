<?php
session_start();
require_once '../includes/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: gestion_prestamos.php?error=ID no recibido");
    exit();
}

$id_prestamo = intval($_GET['id']);

// Obtener información del préstamo
$sql = "SELECT * FROM prestamos 
        WHERE id_prestamo = ? 
        AND (estado_prestamo = 'activo' OR estado_prestamo = 'esperando_devolucion')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_prestamo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: gestion_prestamos.php?error=Préstamo no encontrado o no está activo");
    exit();
}

$prestamo = $result->fetch_assoc();

// INICIAR TRANSACCIÓN
$conn->begin_transaction();

try {
    // 1. Restaurar valores originales del equipo
    $sql_update_equipo = "UPDATE equipos 
                          SET Estado = 'Disponible', 
                              RPE_responsable = ?, 
                              Nombre_responsable = ? 
                          WHERE Numero_serie = ?";
    $stmt_update = $conn->prepare($sql_update_equipo);
    $stmt_update->bind_param(
        "sss", 
        $prestamo['rpe_responsable_original'],
        $prestamo['nombre_responsable_original'],
        $prestamo['numero_serie_equipo']
    );
    $stmt_update->execute();
    
    // 2. Actualizar el préstamo a finalizado
    $sql_update_prestamo = "UPDATE prestamos 
                            SET estado_prestamo = 'finalizado',
                                fecha_devolucion = NOW()
                            WHERE id_prestamo = ?";
    $stmt_update_prestamo = $conn->prepare($sql_update_prestamo);
    $stmt_update_prestamo->bind_param("i", $id_prestamo);
    $stmt_update_prestamo->execute();
    
    $conn->commit();
    
    header("Location: gestion_prestamos.php?mensaje=Devolución procesada correctamente");
    
} catch (Exception $e) {
    $conn->rollback();
    header("Location: gestion_prestamos.php?error=Error al procesar devolución: " . urlencode($e->getMessage()));
}

exit();
?>