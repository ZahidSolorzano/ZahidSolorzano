<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

include '../conexion.php';

if (!isset($_GET['id'])) {
    header("Location: ../tareas/tareas.php?error=ID no válido");
    exit();
}

$proceso_id = (int)$_GET['id'];

// Obtener el proceso con información del departamento
$stmt = $conn->prepare("SELECT p.*, d.nombre as departamento_nombre 
                       FROM procesos p 
                       JOIN departamentos d ON p.departamento_id = d.id 
                       WHERE p.id = ?");
$stmt->bind_param("i", $proceso_id);
$stmt->execute();
$proceso = $stmt->get_result()->fetch_assoc();

if (!$proceso) {
    header("Location: ../tareas/tareas.php?error=Proceso no encontrado");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Proceso</title>
    <style>
        <?php include 'ver_proceso.css'; ?>
    </style>
</head>
<body>
    <div class="container">
        <h1>📄 Ver Proceso</h1>
        
        <div class="info-grid">
            <div class="info-label">Título:</div>
            <div><?= htmlspecialchars($proceso['titulo']) ?></div>
            
            <div class="info-label">Departamento:</div>
            <div><?= htmlspecialchars($proceso['departamento_nombre']) ?></div>
            
            <div class="info-label">Creado por:</div>
            <div><?= htmlspecialchars($proceso['usuario']) ?></div>
            
            <div class="info-label">Fecha de creación:</div>
            <div><?= date('d/m/Y H:i', strtotime($proceso['fecha'])) ?></div>
        </div>
        
        <div class="info-label">Descripción:</div>
        <div class="descripcion">
            <?= nl2br(htmlspecialchars($proceso['descripcion'])) ?>
        </div>
        
        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
            <a href="procesos.php" class="btn" style="background:#3498db; color:#fff;">← Volver a Procesos</a>
            <a href="../dashboard.php" class="btn" style="background:#f1c40f; color:#333;">←  Volver al Panel</a>
            <a href="../tareas/tareas.php" class="btn" style="background:#2ecc71; color:#fff;">← Volver a Tareas</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
