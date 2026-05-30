<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}
include '../conexion.php';

if (!isset($_GET['id'])) {
    die("ID no válido.");
}

$id = intval($_GET['id']);

// Obtener datos actuales del departamento
$stmt = $conn->prepare("SELECT nombre, parent_id FROM departamentos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$departamento = $result->fetch_assoc();

if (!$departamento) {
    die("Departamento no encontrado.");
}

// obtener lista de todos los departamentos excepto el actual y sus hijos
function obtenerIdsDescendientes($conn, $padre_id, &$ids) {
    $stmt = $conn->prepare("SELECT id FROM departamentos WHERE parent_id = ?");
    $stmt->bind_param("i", $padre_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($fila = $resultado->fetch_assoc()) {
        $ids[] = $fila['id'];
        obtenerIdsDescendientes($conn, $fila['id'], $ids);
    }
}

$excluidos = [$id];
obtenerIdsDescendientes($conn, $id, $excluidos);
$excluidos_str = implode(",", $excluidos);

// Guardar cambios
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nuevo_nombre = trim($_POST["nombre_departamento"]);
    $nuevo_padre_id = $_POST["parent_id"] !== "" ? intval($_POST["parent_id"]) : null;

    if ($nuevo_padre_id === $id || in_array($nuevo_padre_id, $excluidos)) {
        die("Error: No se puede asignar a sí mismo ni a un subdepartamento como padre.");
    }

    $stmt = $conn->prepare("UPDATE departamentos SET nombre = ?, parent_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $nuevo_nombre, $nuevo_padre_id, $id);
    $stmt->execute();

    header("Location: departamentos.php");
    exit();
}

// Obtener lista de posibles padres (excluyendo a sí mismo y descendientes)
$sql = "SELECT id, nombre FROM departamentos WHERE id NOT IN ($excluidos_str) ORDER BY nombre";
$posibles_padres = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Departamento</title>
    <style>
        <?php include 'editar_departamento.css'; ?>
    </style>
</head>
<body>
    <div class="container">
        <a href="departamentos.php">← Volver a Departamentos</a>
        <h2>Editar Departamento</h2>
        
        <form method="POST">
            <div>
                <label>Nombre:</label>
                <input type="text" name="nombre_departamento" value="<?= htmlspecialchars($departamento['nombre']) ?>" required>
            </div>
            
            <div>
                <label>Departamento Padre:</label>
                <select name="parent_id">
                    <option value="">-- Ninguno --</option>
                    <?php while ($padre = $posibles_padres->fetch_assoc()): ?>
                        <option value="<?= $padre['id'] ?>" <?= $padre['id'] == $departamento['parent_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($padre['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="botones">
                <button type="submit">Guardar Cambios</button>
                <a href="departamentos.php">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>