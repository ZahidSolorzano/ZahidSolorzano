<?php
session_start();
include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] != "POST" || $_SESSION["rol"] != "encargado") {
    header("Location: tareas.php?error=Acceso no válido");
    exit();
}

$tarea_id = (int)$_POST['tarea_id'];
$admin_id = (int)$_POST['admin_id'];

// Verificar que la tarea pertenece al encargado
$stmt = $conn->prepare("SELECT * FROM tareas WHERE id = ? AND usuario_origen_id = ? AND estado = 'en_progreso'");
$stmt->bind_param("ii", $tarea_id, $_SESSION['id']);
$stmt->execute();
$tarea = $stmt->get_result()->fetch_assoc();

if (!$tarea) {
    header("Location: tareas.php?error=Tarea no válida");
    exit();
}

// Verificar que el admin seleccionado existe y está en el mismo departamento
$stmt = $conn->prepare("SELECT usuario FROM usuarios WHERE id = ? AND departamento_id = ? AND rol = 'admin'");
$stmt->bind_param("ii", $admin_id, $_SESSION['departamento_id']);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin) {
    header("Location: tareas.php?error=Administrador no válido");
    exit();
}

// Transferir la tarea al admin seleccionado
$stmt = $conn->prepare("UPDATE tareas SET usuario_origen_id = ?, estado = 'autorizado_encargado' WHERE id = ?");
$stmt->bind_param("ii", $admin_id, $tarea_id);

if ($stmt->execute()) {
    header("Location: tareas.php?success=Tarea enviada al administrador " . urlencode($admin['usuario']));
} else {
    header("Location: tareas.php?error=Error al enviar la tarea");
}
?>