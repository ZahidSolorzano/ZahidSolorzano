<?php
session_start();
include '../conexion.php';

if (!isset($_GET['id'])) {
    header("Location: tareas.php?error=ID de tarea no proporcionado");
    exit();
}

$tarea_id = (int)$_GET['id'];

// Verificar permisos - Admin puede devolver cualquier tarea, encargado solo las suyas
$where_condition = "";
$params = [$tarea_id];
$types = "i";

if ($_SESSION['rol'] == 'admin') {
    // Admin puede devolver cualquier tarea
    $where_condition = "WHERE id = ?";
} elseif ($_SESSION['rol'] == 'encargado') {
    // Encargado solo puede devolver sus tareas
    $where_condition = "WHERE id = ? AND usuario_origen_id = ?";
    $params[] = $_SESSION['id'];
    $types .= "i";
} else {
    header("Location: tareas.php?error=Sin permisos");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM tareas $where_condition");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$tarea = $stmt->get_result()->fetch_assoc();

if (!$tarea) {
    header("Location: tareas.php?error=Tarea no encontrada");
    exit();
}

// Permitir devolver desde cualquier estado excepto 'completado' y 'atrasada'
if (in_array($tarea['estado'], ['completado', 'atrasada'])) {
    header("Location: tareas.php?error=La tarea está en estado '{$tarea['estado']}' y no se puede devolver");
    exit();
}

$conn->begin_transaction();

try {
    
    if ($_SESSION['rol'] == 'admin') {
        // ADMIN devuelve: solo cambiar estado a 'atrasada' (mantiene el dueño original)
        $stmt = $conn->prepare("UPDATE tareas SET estado = 'atrasada' WHERE id = ?");
        $stmt->bind_param("i", $tarea_id);
        $stmt->execute();
        
        $mensaje = "Tarea devuelta para revisión";
        
    } elseif ($_SESSION['rol'] == 'encargado') {
        // ENCARGADO devuelve: cambiar estado a 'atrasada' (mantiene la propiedad)
        $stmt = $conn->prepare("UPDATE tareas SET estado = 'atrasada' WHERE id = ?");
        $stmt->bind_param("i", $tarea_id);
        $stmt->execute();
        
        $mensaje = "Tarea devuelta para cambios";
    }
    
    // Cambiar estado del proceso relacionado a 'en_progreso'
    if ($tarea['proceso_relacionado_id']) {
        $stmt = $conn->prepare("UPDATE procesos SET estado = 'en_progreso' WHERE id = ?");
        $stmt->bind_param("i", $tarea['proceso_relacionado_id']);
        $stmt->execute();
    }
    
    $conn->commit();
    header("Location: tareas.php?success=" . urlencode($mensaje));
    
} catch (Exception $e) {
    $conn->rollback();
    header("Location: tareas.php?error=" . urlencode("Error al devolver tarea: " . $e->getMessage()));
}
?>
