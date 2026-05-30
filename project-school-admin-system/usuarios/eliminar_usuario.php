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

$id = $_GET['id'];

if ($_SESSION["rol"] == "encargado") {
    $mi_departamento = $_SESSION["departamento_id"];
    include_once '../funciones.php';
    $subdepartamentos = obtenerSubdepartamentos($conn, $mi_departamento);
    $departamentos_permitidos = $subdepartamentos;
    $departamentos_permitidos[] = $mi_departamento;
    // Obtener el departamento y rol del usuario a eliminar
    $sql_dep = "SELECT departamento_id, rol FROM usuarios WHERE id = ?";
    $stmt_dep = $conn->prepare($sql_dep);
    $stmt_dep->bind_param("i", $id);
    $stmt_dep->execute();
    $result_dep = $stmt_dep->get_result();
    $usuario_dep = $result_dep->fetch_assoc();
    if (!$usuario_dep || !in_array($usuario_dep['departamento_id'], $departamentos_permitidos) || $usuario_dep['rol'] == 'admin') {
        die("No tienes permiso para eliminar este usuario.");
    }
}

$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: usuarios.php");
    exit();
} else {
    echo "Error al eliminar.";
}

$conn->close();
?>
