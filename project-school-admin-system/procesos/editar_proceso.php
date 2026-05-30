<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

include '../conexion.php';
include '../funciones.php'; // Necesitamos la función obtenerSubdepartamentos()

// Verificar si se recibió ID
if (!isset($_GET['id'])) {
    die("ID no válido.");
}

$id = intval($_GET['id']);

// Obtener el proceso a editar
$sql = "SELECT * FROM procesos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$proceso = $result->fetch_assoc();

if (!$proceso) {
    die("Proceso no encontrado.");
}

// Determinar departamentos permitidos según rol
$departamentos_permitidos = [];
$es_admin = ($_SESSION["rol"] == 'admin');
$es_encargado = ($_SESSION["rol"] == 'encargado');
$es_capturista = ($_SESSION["rol"] == 'capturista');

if ($es_encargado || $es_capturista) {
    if (isset($_SESSION["departamento_id"])) {
        $departamentos_permitidos = obtenerSubdepartamentos($conn, $_SESSION["departamento_id"]);
        $departamentos_permitidos[] = $_SESSION["departamento_id"];
    }
}


// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST["titulo"]);
    $descripcion = trim($_POST["descripcion"]);
    $departamento_id = intval($_POST["departamento"]);
    
    // Validar campos (sin fecha)
    if (empty($titulo) || empty($descripcion) || empty($departamento_id)) {
        die("Todos los campos son obligatorios.");
    }
    
    // Validar permisos para departamento
    if (!$es_admin && !empty($departamentos_permitidos)) {
        if (!in_array($departamento_id, $departamentos_permitidos)) {
            die("No tienes permiso para asignar este proceso al departamento seleccionado.");
        }
    }
    
    // Iniciar transacción para actualizar proceso y tarea relacionada
    $conn->begin_transaction();
    
    try {
        // Actualizar el proceso
        $sql = "UPDATE procesos SET titulo = ?, descripcion = ?, departamento_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $titulo, $descripcion, $departamento_id, $id);
        $stmt->execute();
        
        // Verificar si hay una tarea relacionada con este proceso
        $sql_tarea = "SELECT id, estado FROM tareas WHERE proceso_relacionado_id = ?";
        $stmt_tarea = $conn->prepare($sql_tarea);
        $stmt_tarea->bind_param("i", $id);
        $stmt_tarea->execute();
        $result_tarea = $stmt_tarea->get_result();
        $tarea_relacionada = $result_tarea->fetch_assoc();
        
        // Si hay tarea relacionada y está "atrasada" o "en_correccion", cambiar a "en_progreso"
        if ($tarea_relacionada && in_array($tarea_relacionada['estado'], ['atrasada', 'en_correccion'])) {
            $sql_update_tarea = "UPDATE tareas SET estado = 'en_progreso' WHERE id = ?";
            $stmt_update_tarea = $conn->prepare($sql_update_tarea);
            $stmt_update_tarea->bind_param("i", $tarea_relacionada['id']);
            $stmt_update_tarea->execute();
        }
        
        $conn->commit();
        header("Location: procesos.php?success=Proceso actualizado correctamente");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        die("Error al actualizar: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proceso</title>
    <link rel="stylesheet" href="editar_proceso1.css">
</head>
<body>
    <div class="container">
        <h1>Editar Proceso</h1>
        <a href="procesos.php" class="back-button">← Volver a Procesos</a>
        
        <form method="POST">
        <div>
            <label>Título:</label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($proceso['titulo']) ?>" required>
        </div>

        <div>
            <label>Departamento:</label>
            <select name="departamento" required>
                <?php
                if ($es_admin) {
                    // Admin ve todos los departamentos
                    $dept_result = $conn->query("SELECT id, nombre FROM departamentos");
                    while ($dept = $dept_result->fetch_assoc()) {
                        $selected = ($proceso['departamento_id'] == $dept['id']) ? 'selected' : '';
                        echo "<option value='{$dept['id']}' $selected>{$dept['nombre']}</option>";
                    }
                } elseif ($es_encargado || $es_capturista) {
                    // Encargado/Capturista solo ven sus departamentos permitidos
                    if (!empty($departamentos_permitidos)) {
                        $ids_permitidos = implode(",", $departamentos_permitidos);
                        $dept_result = $conn->query("SELECT id, nombre FROM departamentos WHERE id IN ($ids_permitidos)");
                        
                        while ($dept = $dept_result->fetch_assoc()) {
                            $selected = ($proceso['departamento_id'] == $dept['id']) ? 'selected' : '';
                            echo "<option value='{$dept['id']}' $selected>{$dept['nombre']}</option>";
                        }
                    } else {
                        // Si no hay departamentos permitidos, mostrar solo el actual
                        $dept_result = $conn->query("SELECT id, nombre FROM departamentos WHERE id = {$proceso['departamento_id']}");
                        $dept = $dept_result->fetch_assoc();
                        echo "<option value='{$dept['id']}' selected>{$dept['nombre']}</option>";
                    }
                }
                ?>
            </select>
        </div>


        <div>
            <label>Descripción:</label>
            <textarea name="descripcion" required><?= htmlspecialchars($proceso['descripcion']) ?></textarea>
        </div>

        <button type="submit">Guardar Cambios</button>
    </form>
</body>
</html>
<?php
$conn->close();
?>