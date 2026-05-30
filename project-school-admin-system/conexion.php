<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "procesos_cetis27"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
