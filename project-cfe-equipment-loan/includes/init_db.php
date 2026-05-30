<?php
$conn = new mysqli("localhost", "root", "", "inventarios_internos");

if ($conn->connect_error) {
    die("Error de conexión");
}

$result = $conn->query("SHOW VARIABLES LIKE 'event_scheduler'");
$row = $result->fetch_assoc();

if ($row['Value'] !== 'ON') {
    $conn->query("SET GLOBAL event_scheduler = ON");
}

$conn->close();