<?php
session_start();
include 'conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $RPE = $_POST['RPE'];
    $contraseña = $_POST['contraseña'];

    $sql = "SELECT * FROM usuarios WHERE RPE = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $RPE);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $usuario_data = $resultado->fetch_assoc();

        if (password_verify($contraseña, $usuario_data['contraseña'])) {
            $_SESSION['RPE'] = $RPE;
            $_SESSION['nombre'] = $usuario_data['nombre'];
            $_SESSION['rol'] = $usuario_data['rol'];
            $_SESSION['id_usuario'] = $usuario_data['id_usuario'];

            // redirigir al dashboard
            header("Location: ../vistas/dashboard.php");
            exit();
        } else {
            // si contraseña incorrecta redirigir con mensaje de error
            header("Location: ../vistas/vistalogin.php?error=Contraseña incorrecta");
            exit();
        }
    } else {
        // si RPE no encontrado redirigir con mensaje de error
        header("Location: ../vistas/vistalogin.php?error=RPE no encontrado");
        exit();
    }
    
    $stmt->close();
    $conn->close();
}
?>