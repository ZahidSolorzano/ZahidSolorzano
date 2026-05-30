<?php
session_start();

// Verificar si hay una sesion
if (!isset($_SESSION["RPE"])) {
    header("Location: vistas/vistalogin.php");
    exit();
}

// Si existe la sesion redirigir al dashboard
header("Location: vistas/dashboard.php");
exit();
?>