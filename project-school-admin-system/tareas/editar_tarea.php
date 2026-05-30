<?php
session_start();
require_once '../conexion.php';

// Verificar si se recibió ID
if (!isset($_GET['id'])) {
    header("Location: tareas.php?error=ID no válido");
    exit();
}

$id = (int)$_GET['id'];

// Obtener la tarea actual
$stmt = $conn->prepare("SELECT * FROM tareas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tarea = $stmt->get_result()->fetch_assoc();

if (!$tarea) {
    header("Location: tareas.php?error=Tarea no encontrada");
    exit();
}

// Verificar permisos (solo el creador o admin puede editar)
if ($_SESSION['rol'] != 'admin' && $tarea['usuario_origen_id'] != $_SESSION['id']) {
    header("Location: tareas.php?error=No tienes permiso para editar esta tarea");
    exit();
}

// Procesar formulario de edición
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $destino_id = (int)$_POST['usuario_destino_id'];
    $departamento_destino_id = (int)$_POST['departamento_destino_id'];
    $fecha_limite = !empty($_POST['fecha_limite']) ? $_POST['fecha_limite'] : null;

    $stmt = $conn->prepare("UPDATE tareas SET titulo = ?, descripcion = ?, usuario_destino_id = ?, departamento_destino_id = ?, fecha_limite = ? WHERE id = ?");
    $stmt->bind_param("ssiisi", $titulo, $descripcion, $destino_id, $departamento_destino_id, $fecha_limite, $id);

    if ($stmt->execute()) {
        header("Location: tareas.php?success=Tarea actualizada");
    } else {
        header("Location: tareas.php?error=Error al actualizar");
    }
    exit();
}

// Función para obtener subdepartamentos
function obtenerSubdepartamentos($conn, $departamento_id) {
    $subdepartamentos = [];
    $stmt = $conn->prepare("SELECT id FROM departamentos WHERE parent_id = ?");
    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($fila = $result->fetch_assoc()) {
        $subdepartamentos[] = $fila['id'];
        $subdepartamentos = array_merge($subdepartamentos, obtenerSubdepartamentos($conn, $fila['id']));
    }
    return $subdepartamentos;
}

// Obtener usuarios para el select
$usuarios = $conn->query("SELECT id, usuario FROM usuarios WHERE id != ".$_SESSION['id'])->fetch_all(MYSQLI_ASSOC);

// Obtener departamentos para el select según el rol
$departamentos = [];
if ($_SESSION["rol"] == "admin") {
    // Admin puede ver todos los departamentos
    $departamentos = $conn->query("SELECT id, nombre FROM departamentos ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
} elseif ($_SESSION["rol"] == "encargado") {
    // Encargado solo ve su departamento y subdepartamentos
    $mi_departamento = $_SESSION["departamento_id"];
    if ($mi_departamento) {
        // Agregar su propio departamento
        $stmt = $conn->prepare("SELECT id, nombre FROM departamentos WHERE id = ?");
        $stmt->bind_param("i", $mi_departamento);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($dept = $result->fetch_assoc()) {
            $departamentos[] = $dept;
        }
        
        // Agregar subdepartamentos
        $subdepartamentos = obtenerSubdepartamentos($conn, $mi_departamento);
        if (!empty($subdepartamentos)) {
            $ids = implode(",", $subdepartamentos);
            $result = $conn->query("SELECT id, nombre FROM departamentos WHERE id IN ($ids) ORDER BY nombre");
            while ($dept = $result->fetch_assoc()) {
                $departamentos[] = $dept;
            }
        }
    }
} elseif ($_SESSION["rol"] == "capturista") {
    // Capturista solo ve su departamento
    $mi_departamento = $_SESSION["departamento_id"];
    if ($mi_departamento) {
        $stmt = $conn->prepare("SELECT id, nombre FROM departamentos WHERE id = ?");
        $stmt->bind_param("i", $mi_departamento);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($dept = $result->fetch_assoc()) {
            $departamentos[] = $dept;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="editar_tarea6.css">
</head>
<body>
     <a href="tareas.php" class="back-button">← Volver a tareas</a>
    <div class="container">
        <h1>Editar Tarea</h1>
        <form method="POST">
    <label for="titulo">Título:</label>
    <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($tarea['titulo']) ?>" required>
    
    <label for="descripcion">Descripción:</label>
    <textarea id="descripcion" name="descripcion" required><?= htmlspecialchars($tarea['descripcion']) ?></textarea>
    
    <label for="usuario_destino_id">Asignar a:</label>
    <select id="usuario_destino_id" name="usuario_destino_id" required>
        <?php foreach ($usuarios as $usuario): ?>
            <option value="<?= $usuario['id'] ?>" <?= $usuario['id'] == $tarea['usuario_destino_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($usuario['usuario']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <label for="departamento_destino_id">Departamento:</label>
    <select id="departamento_destino_id" name="departamento_destino_id" required>
        <?php foreach ($departamentos as $departamento): ?>
            <option value="<?= $departamento['id'] ?>" <?= $departamento['id'] == $tarea['departamento_destino_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($departamento['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <label for="fecha_limite">Fecha límite (opcional):</label>
    <input type="datetime-local" id="fecha_limite" name="fecha_limite" value="<?= $tarea['fecha_limite'] ? date('Y-m-d\TH:i', strtotime($tarea['fecha_limite'])) : '' ?>">
    
    <button type="submit">Guardar Cambios</button>
</form>
    </div>
</body>
</html>