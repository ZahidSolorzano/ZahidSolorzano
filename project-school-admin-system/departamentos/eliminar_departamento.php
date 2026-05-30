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

// Verificar si tiene subdepartamentos
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM departamentos WHERE parent_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();

if ($datos['total'] > 0) {
    echo "No se puede eliminar este departamento porque tiene subdepartamentos.";
    echo "<br><a href='departamentos.php'>Volver</a>";
    exit();
}

// Verificar si tiene usuarios asociados
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM usuarios WHERE departamento_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();

if ($datos['total'] > 0) {
    echo "No se puede eliminar este departamento porque tiene usuarios asignados.";
    echo "<br><a href='departamentos.php'>Volver</a>";
    exit();
}

// Verificar si tiene procesos asociados
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM procesos WHERE departamento_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();

if ($datos['total'] > 0) {
    echo "No se puede eliminar este departamento porque tiene procesos asociados.";
    echo "<br><a href='departamentos.php'>Volver</a>";
    exit();
}

// Verificar si tiene tareas asociadas
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tareas WHERE departamento_destino_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();

if ($datos['total'] > 0) {
    echo "No se puede eliminar este departamento porque tiene tareas asociadas.";
    echo "<br><a href='departamentos.php'>Volver</a>";
    exit();
}

// Eliminar
$stmt = $conn->prepare("DELETE FROM departamentos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: departamentos.php");
exit();
?>
