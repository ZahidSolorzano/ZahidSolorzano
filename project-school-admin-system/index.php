<?php
session_start();
if (isset($_SESSION["usuario"])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Procesos Administrativos | Cetis 27 Uruapan</title>

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .main-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 0.8s ease-in-out;
        }
        
        .logo-container {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 15px;
        }
        
        .titulo-principal {
            color: var(--primary-color);
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        }
        
        .subtitulo {
            color: var(--dark-color);
            font-size: 1.2rem;
            text-align: center;
            margin-bottom: 40px;
            font-weight: 300;
            opacity: 0.8;
        }
        
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s ease;
        }
        
        .form-container:hover {
            transform: translateY(-5px);
        }
        
        .form-container h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.8rem;
        }
        
        form {
            display: flex;
            flex-direction: column;
        }
        
        input {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s ease;
        }
        
        input:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        button {
            background-color: var(--secondary-color);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: var(--dark-color);
        }
        
        .register-link a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .register-link a:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: var(--dark-color);
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .titulo-principal {
                font-size: 1.8rem;
            }
            
            .form-container {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="logo-container">
            
            
            <h1 class="titulo-principal">Sistema de Procesos Administrativos</h1>
            <p class="subtitulo">Cetis 27 Uruapan</p>
        </div>
        
        <div class="form-container">
            <h2>Iniciar sesión</h2>
            
            <form action="login.php" method="POST">
                <input type="text" name="usuario" placeholder="Nombre de usuario" required>
                <input type="password" name="contraseña" placeholder="Contraseña" required>
                <button type="submit">Acceder al sistema</button>
            </form>
            
            <p class="register-link">¿No tienes cuenta? <a href="registrarse.php">Regístrate aquí</a></p>
        </div>
        
        <div class="footer">
            © <?php echo date('Y'); ?> CETIS 27 Uruapan. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>