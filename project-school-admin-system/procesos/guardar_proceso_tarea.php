<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST["titulo"]);
    $descripcion = trim($_POST["descripcion"]);
    $tarea_id = intval($_POST["tarea_id"]);
    $usuario = $_SESSION["usuario"];
    
   
    if (empty($titulo) || empty($descripcion) || empty($tarea_id)) {
        die("Todos los campos son obligatorios.");
    }
    
    // Verificar que la tarea existe y está pendiente
    $stmt = $conn->prepare("SELECT * FROM tareas WHERE id = ? AND estado = 'pendiente'");
    $stmt->bind_param("i", $tarea_id);
    $stmt->execute();
    $tarea = $stmt->get_result()->fetch_assoc();
    
    if (!$tarea) {
        die("Tarea no válida o ya procesada.");
    }
    
    // Usar el departamento de la tarea 
    $departamento_id = $tarea['departamento_destino_id'];
    
    $conn->begin_transaction();
    
    try {
        //Crear el proceso con estado 'en_progreso'
        $stmt = $conn->prepare("INSERT INTO procesos (titulo, descripcion, departamento_id, usuario, fecha, estado) 
                               VALUES (?, ?, ?, ?, NOW(), 'en_progreso')");
        $stmt->bind_param("ssis", $titulo, $descripcion, $departamento_id, $usuario);
        $stmt->execute();
        $proceso_id = $conn->insert_id;
        
        //Actualizar la tarea: cambiar estado a 'en_progreso' y vincular el proceso
        $stmt = $conn->prepare("UPDATE tareas SET estado = 'en_progreso', proceso_relacionado_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $proceso_id, $tarea_id);
        $stmt->execute();
        
        $conn->commit();
        header("Location: ../tareas/tareas.php?success=Proceso creado y tarea en progreso");
        
    } catch (Exception $e) {
        $conn->rollback();
        die("Error al crear el proceso: " . $e->getMessage());
    }
}
?>