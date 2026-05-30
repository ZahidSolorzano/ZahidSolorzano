<?php
session_start();
require_once '../conexion.php';

// Verificar si se recibió ID
if (!isset($_GET['id'])) {
    header("Location: tareas.php?error=ID no válido");
    exit();
}

$id = (int)$_GET['id'];

// Obtener la tarea para verificar permisos y proceso asociado
$stmt = $conn->prepare("SELECT usuario_origen_id, proceso_relacionado_id FROM tareas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tarea = $stmt->get_result()->fetch_assoc();

if (!$tarea) {
    header("Location: tareas.php?error=Tarea no encontrada");
    exit();
}

// Verificar permisos (solo el creador o admin puede eliminar)
if ($_SESSION['rol'] != 'admin' && $tarea['usuario_origen_id'] != $_SESSION['id']) {
    header("Location: tareas.php?error=No tienes permiso para eliminar esta tarea");
    exit();
}

// Iniciar transacción para eliminar tanto la tarea como el proceso asociado
$conn->begin_transaction();

try {
    // Si hay un proceso asociado, eliminarlo primero
    if (!empty($tarea['proceso_relacionado_id'])) {
        $stmt = $conn->prepare("DELETE FROM procesos WHERE id = ?");
        $stmt->bind_param("i", $tarea['proceso_relacionado_id']);
        $stmt->execute();
    }
    
    // Eliminar la tarea
    $stmt = $conn->prepare("DELETE FROM tareas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    $conn->commit();
    
    $mensaje = !empty($tarea['proceso_relacionado_id']) ? 
        "Tarea y proceso asociado eliminados" : 
        "Tarea eliminada";
    
    header("Location: tareas.php?success=" . urlencode($mensaje));
    
} catch (Exception $e) {
    $conn->rollback();
    header("Location: tareas.php?error=Error al eliminar: " . $e->getMessage());
}