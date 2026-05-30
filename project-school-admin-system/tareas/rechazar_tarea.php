<?php
session_start();
include '../conexion.php';

$tarea_id = (int)$_GET['id'];

// Verificar permisos
$stmt = $conn->prepare("SELECT * FROM tareas WHERE id = ? AND usuario_origen_id = ?");
$stmt->bind_param("ii", $tarea_id, $_SESSION['id']);
$stmt->execute();
$tarea = $stmt->get_result()->fetch_assoc();

if (!$tarea || $tarea['estado'] != 'en_progreso') {
    header("Location: tareas.php?error=Tarea no válida");
    exit();
}

$conn->begin_transaction();

try {
    // Cambiar el estado de la tarea a "en_correccion"
    $stmt = $conn->prepare("UPDATE tareas SET estado = 'en_correccion' WHERE id = ?");
    $stmt->bind_param("i", $tarea_id);
    $stmt->execute();
    
    $conn->commit();
    header("Location: tareas.php?success=Tarea enviada para corrección");
    
} catch (Exception $e) {
    $conn->rollback();
    header("Location: tareas.php?error=Error al rechazar");
}
?>
