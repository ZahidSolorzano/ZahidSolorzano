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

// Eliminar el proceso
$sql = "DELETE FROM procesos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: procesos.php");
    exit();
} else {
    echo "Error al eliminar.";
}

$conn->close();
?>
