<?php
session_start();

if (!isset($_SESSION["RPE"])) {
    header("Location: vistalogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Inventarios | CFE</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="icon" href="../img/logo_cfe.png">
</head>
<body>
    <!-- Header-->
    <header class="header">
        <div class="logo-section">
            <img src="../img/logo_cfe.png" alt="Logo CFE" class="logo">
            <h1>Sistema de Inventarios | CFE</h1>
        </div>
        <div class="user-info">
            <a href="../includes/logout.php" class="logout-btn">Cerrar sesión</a>
        </div>
    </header>



    <div class="main-container">
        <div class="content">
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
            <p>Selecciona una opción del menú:</p>
            
            <div class="dashboard-options">
                <a href="../equipos/gestion_equipos.php" class="dashboard-btn btn-computer">
                    <i class="icon-computer"></i>
                    <span>Equipos de Cómputo</span>
                </a>
                
                <a href="../prestamos/gestion_prestamos.php" class="dashboard-btn btn-loan">
                    <i class="icon-loan"></i>
                    <span>Préstamos</span>
                </a>

                <a href="../usuario/editar_usuario.php" class="dashboard-btn btn-user">
                    <i class="icon-user"></i>
                    <span>Editar mi usuario</span>
                </a>
                
                <a href="../usuario/gestion_usuarios.php" class="dashboard-btn">
                    <i class="icon-users"></i>
                    <span>Gestión de Usuarios</span>
                </a>
                
                
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Comisión Federal de Electricidad. Todos los derechos reservados.</p>
    </footer>
</body>
</html>