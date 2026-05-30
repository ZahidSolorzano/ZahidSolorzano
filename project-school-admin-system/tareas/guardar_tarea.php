<?php
session_start();
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $destino_id = (int)$_POST['usuario_destino_id'];
    $departamento_destino_id = (int)$_POST['departamento_destino_id'];
    
    if (empty($titulo) || empty($descripcion) || $destino_id <= 0 || $departamento_destino_id <= 0) {
        header("Location: tareas.php?error=Todos los campos son obligatorios");
        exit();
    }
    
    // Si no se puso fecha límite, guarda NULL
    $fecha_limite = !empty($_POST['fecha_limite']) ? $_POST['fecha_limite'] : null;

    $stmt = $conn->prepare("INSERT INTO tareas 
                           (titulo, descripcion, usuario_origen_id, usuario_destino_id, departamento_destino_id, fecha_limite, estado) 
                           VALUES (?, ?, ?, ?, ?, ?, 'pendiente')");
    $stmt->bind_param("ssiiss", $titulo, $descripcion, $_SESSION['id'], $destino_id, $departamento_destino_id, $fecha_limite);
    
    if ($stmt->execute()) {
        header("Location: tareas.php?success=Tarea creada correctamente");
    } else {
        header("Location: tareas.php?error=Error al crear tarea");
    }
    exit();
}
?>
