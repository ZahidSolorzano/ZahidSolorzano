<?php
session_start();
include 'conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $usuario_data = $resultado->fetch_assoc();

        if (password_verify($contraseña, $usuario_data['contraseña'])) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['rol'] = $usuario_data['rol'];
            $_SESSION['departamento_id'] = $usuario_data['departamento_id']; 

            // Redirige al dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }
}
?>
