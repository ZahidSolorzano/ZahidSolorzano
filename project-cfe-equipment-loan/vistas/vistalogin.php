<?php
session_start();
if (isset($_SESSION["RPE"])) {
    header("Location: dashboard.php");
    exit();
}

// mostrar mensaje de error
$error = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Sistema de Inventarios | CFE</title>
    <link rel="stylesheet" href="vistalogin.css">
</head>
<body>
    <div class="main-container">
        
     <div class="logo-container">
        <!-- logo centrado -->
        <img src="../img/logo_cfe.png" alt="Logo CFE - Comisión Federal de Electricidad" class="logo-cfe-rectangular">
        
        <h1 class="titulo-principal">Sistema de Inventarios Internos</h1>
        <p class="subtitulo">Comisión Federal de Electricidad</p>
    </div>
        
        <div class="form-container">
            <h2>Iniciar sesión</h2>
            
            <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($mensaje)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
            <?php endif; ?>
            
            <form action="../includes/login.php" method="POST">
                <input type="text" name="RPE" placeholder="RPE (Registro Personal de Empleado)" required>
                <input type="password" name="contraseña" placeholder="Contraseña" required>
                <button type="submit">Acceder al sistema</button>
            </form>
            
            <!-- Opción para registro de nuevo usuario -->
            <div class="registro-link">
                <p>¿No tienes perfil? <a href="registro_usuario.php">Regístrate aquí</a></p>
                
            </div>
        </div>
        
        <div class="footer">
            © <?php echo date('Y'); ?> Comisión Federal de Electricidad. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>