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
    $departamento_id = trim($_POST["departamento_id"]);
    $usuario = $_SESSION["usuario"]; // Obtener el usuario de la sesión

    // Validar que ningún campo esté vacío
    if (empty($titulo) || empty($descripcion) || empty($departamento_id)) {
        die("Todos los campos son obligatorios.");
    }

    // Insertar el nuevo proceso (estado por defecto será 'completo')
$sql = "INSERT INTO procesos (titulo, descripcion, departamento_id, usuario, fecha, estado)
        VALUES (?, ?, ?, ?, NOW(), 'completo')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssis", $titulo, $descripcion, $departamento_id, $usuario);

    if ($stmt->execute()) {
        header("Location: procesos.php");
        exit();
    } else {
        echo "Error al guardar el proceso: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>