<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

$tarea_id = $_GET['tarea_id'] ?? null;
if (!$tarea_id) {
    header("Location: ../tareas/tareas.php");
    exit();
}

include '../conexion.php';
$stmt = $conn->prepare("SELECT t.*, d.nombre AS departamento_nombre 
                        FROM tareas t 
                        JOIN departamentos d ON t.departamento_destino_id = d.id 
                        WHERE t.id = ? AND t.usuario_destino_id = ?");
$stmt->bind_param("ii", $tarea_id, $_SESSION['id']);
$stmt->execute();
$tarea = $stmt->get_result()->fetch_assoc();

if (!$tarea) {
    header("Location: ../tareas/tareas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Proceso</title>
    <link rel="stylesheet" href="crear.css">
</head>
<body>
    <div class="container">
        <h2> Crear Proceso para la Tarea</h2>
        
        <div class="info-box">
            <h3><?= htmlspecialchars($tarea['titulo']) ?></h3>
            <p><strong>Descripción:</strong> <?= htmlspecialchars($tarea['descripcion']) ?></p>
            <p><strong>Departamento:</strong> <?= htmlspecialchars($tarea['departamento_nombre']) ?></p>
            <p><strong>Fecha límite:</strong> <?= date('d/m/Y H:i', strtotime($tarea['fecha_limite'])) ?></p>
        </div>

        <form action="guardar_proceso_tarea.php" method="POST">
            <input type="hidden" name="tarea_id" value="<?= $tarea_id ?>">
            
            <div class="form-group">
                <label for="titulo">Título del Proceso:</label>
                <input type="text" name="titulo" required placeholder="Ej: Proceso de revisión de documentos">
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción del Proceso:</label>
                <textarea name="descripcion" required placeholder="Describe detalladamente el proceso que vas a realizar..."></textarea>
            </div>
            
            <div>
                <button type="submit">✅ Crear Proceso e Iniciar Tarea</button>
                <a href="../tareas/tareas.php" class="cancel-button">❌ Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>