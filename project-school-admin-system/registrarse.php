<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"]);
    $contraseña = trim($_POST["contraseña"]);
    
    if (empty($usuario) || empty($contraseña)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
        $rol = "encargado"; // Todos los registrados tendrán el rol de encargado

        $sql = "INSERT INTO usuarios (usuario, contraseña, rol) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $usuario, $contraseña_hash, $rol);

        if ($stmt->execute()) {
            header("Location: index.php?mensaje=Registro exitoso. Ahora puedes iniciar sesión.");
            exit();
        } else {
            $error = "Error al registrar usuario. El nombre de usuario puede estar en uso.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Procesos Administrativos | Cetis 27 Uruapan</title>
   
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark-color);
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 15px;
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
            background-color: var(--success-color);
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
            margin-top: 10px;
        }
        
        button:hover {
            background-color: #219653;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: var(--dark-color);
        }
        
        .login-link a {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: var(--accent-color);
            text-decoration: underline;
        }
        
        .error-message {
            color: var(--accent-color);
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: rgba(231, 76, 60, 0.1);
            border-radius: 5px;
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
            
            <h1 class="titulo-principal">Registro de Usuario</h1>
            <p class="subtitulo">Sistema de Procesos Administrativos</p>
        </div>
        
        <div class="form-container">
            <h2>Crea tu cuenta</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="registrarse.php" method="POST">
                <div class="form-group">
                    <label for="usuario">Nombre de usuario</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>
                
                <div class="form-group">
                    <label for="contraseña">Contraseña</label>
                    <input type="password" id="contraseña" name="contraseña" required>
                </div>
                
                <button type="submit">Registrarse</button>
            </form>
            
            <p class="login-link">¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a></p>
        </div>
        
        <div class="footer">
            © <?php echo date('Y'); ?> CETIS 27 Uruapan. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>