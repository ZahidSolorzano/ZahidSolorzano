<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre_departamento"]);

    if (empty($nombre)) {
        die("El campo nombre del departamento es obligatorio.");
    }

    $sql = "INSERT INTO departamentos (nombre) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error al guardar el departamento: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>