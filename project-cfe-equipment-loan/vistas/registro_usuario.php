<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION["RPE"])) {
    header("Location: dashboard.php");
    exit();
}

include '../includes/conexion.php';

$mensaje = '';
$error = '';

// Procesar formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $RPE = $_POST['RPE'];
    $nombre = $_POST['nombre'];
    $departamento = $_POST['departamento'];
    $division = $_POST['division'];
    $contraseña = $_POST['contraseña'];
    $confirmar_contraseña = $_POST['confirmar_contraseña'];

    // Validaciones - todos los campos son obligatorios
    if (empty($RPE) || empty($nombre) || empty($departamento) || empty($division) || empty($contraseña)) {
        $error = "Todos los campos son obligatorios";
    } elseif ($contraseña !== $confirmar_contraseña) {
        $error = "Las contraseñas no coinciden";
    } elseif (strlen($contraseña) < 5) {
        $error = "La contraseña debe tener al menos 5 caracteres";
    } elseif (!preg_match('/^[A-Za-z0-9]{5}$/', $RPE)) {
        $error = "El RPE debe tener exactamente 5 caracteres alfanuméricos";
    } else {
        // Convertir RPE a mayúsculas
        $RPE = strtoupper($RPE);
        
        // Verificar si el RPE ya existe
        $sql_check = "SELECT * FROM usuarios WHERE RPE = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $RPE);
        $stmt_check->execute();
        $resultado_check = $stmt_check->get_result();

        if ($resultado_check->num_rows > 0) {
            $error = "El RPE ya está registrado en el sistema";
        } else {
            // Encriptar la contraseña
            $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
            
            // Fijar rol como "empleado" (no se permite auto-registro como administrador)
            $rol = 'empleado';

            // Insertar usuario en la base de datos 
            $sql = "INSERT INTO usuarios (RPE, nombre, rol, contraseña, departamento, division) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $RPE, $nombre, $rol, $contraseña_hash, $departamento, $division);

            if ($stmt->execute()) {
                // Redirigir al login con mensaje de éxito
                header("Location: vistalogin.php?mensaje=Usuario registrado exitosamente. Ahora puedes iniciar sesión.");
                exit();
            } else {
                $error = "Error al registrar usuario: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Sistema de Inventarios | CFE</title>
    <link rel="stylesheet" href="vistalogin.css">
    <style>
        /* Estilos para el formulario de registro */
        .registro-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group .required::after {
            content: " *";
            color: #e74c3c;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .btn-registro {
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-registro:hover {
            background-color: #34495e;
        }
        
        .volver-login {
            text-align: center;
            margin-top: 15px;
        }
        
        .volver-login a {
            color: #3498db;
            text-decoration: none;
        }
        
        .volver-login a:hover {
            text-decoration: underline;
        }
        
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .campo-obligatorio {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="logo-container">
            <img src="../img/logo_cfe.png" alt="Logo CFE" class="logo-cfe-rectangular">
            <h1 class="titulo-principal">Registro de Nuevo Usuario</h1>
            <p class="subtitulo">Sistema de Inventarios Internos - CFE</p>
        </div>
        
        <div class="form-container registro-container">
            <div class="info-box">
                <p><strong>Información importante:</strong></p>
                <p>Solo se permite registro de usuarios tipo "Empleado"</p>
                <p>Los usuarios "Administrador" deben ser creados por otro administrador</p>
              
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="RPE" class="required">RPE (Registro Personal de Empleado):</label>
                    <input type="text" id="RPE" name="RPE" required 
                           pattern="[A-Za-z0-9]{5}"
                           title="5 caracteres alfanuméricos (ejemplo: A1234)"
                           maxlength="5"
                           minlength="5"
                           placeholder="Ejemplo: A1234"
                           value="<?php echo isset($_POST['RPE']) ? htmlspecialchars(strtoupper($_POST['RPE'])) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="nombre" class="required">Nombre completo:</label>
                    <input type="text" id="nombre" name="nombre" required
                           placeholder="Ejemplo: Juan Pérez López"
                           value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="departamento" class="required">Departamento:</label>
                    <input type="text" id="departamento" name="departamento" required
                           placeholder="Ejemplo: Recursos Humanos, Operaciones, Mantenimiento"
                           value="<?php echo isset($_POST['departamento']) ? htmlspecialchars($_POST['departamento']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="division" class="required">División:</label>
                    <input type="text" id="division" name="division" required
                           placeholder="Ejemplo: División Norte, División Centro, División Sur"
                           value="<?php echo isset($_POST['division']) ? htmlspecialchars($_POST['division']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="contraseña" class="required">Contraseña:</label>
                    <input type="password" id="contraseña" name="contraseña" required
                           minlength="5"
                           placeholder="Mínimo 5 caracteres">
                </div>
                
                <div class="form-group">
                    <label for="confirmar_contraseña" class="required">Confirmar contraseña:</label>
                    <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required
                           minlength="5"
                           placeholder="Repite la contraseña">
                </div>
                
                <div class="campo-obligatorio">* Campos obligatorios</div>
                
                <input type="hidden" name="rol" value="empleado">
                
                <button type="submit" class="btn-registro">Registrarse</button>
                
                <div class="volver-login">
                    <p>¿Ya tienes cuenta? <a href="vistalogin.php">Inicia sesión aquí</a></p>
                </div>
            </form>
        </div>
        
        <div class="footer">
            © <?php echo date('Y'); ?> Comisión Federal de Electricidad. Todos los derechos reservados.
        </div>
    </div>
    
    <script>
        // Validación en tiempo real de RPE
        document.getElementById('RPE').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
        
        // Validación de formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            // Validar contraseñas
            var pass1 = document.getElementById('contraseña').value;
            var pass2 = document.getElementById('confirmar_contraseña').value;
            
            if (pass1 !== pass2) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                document.getElementById('contraseña').focus();
                return false;
            }
            
            if (pass1.length < 5) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 5 caracteres');
                document.getElementById('contraseña').focus();
                return false;
            }
            
            // Validar RPE
            var rpe = document.getElementById('RPE').value;
            if (!/^[A-Z0-9]{5}$/.test(rpe)) {
                e.preventDefault();
                alert('El RPE debe tener exactamente 5 caracteres alfanuméricos');
                document.getElementById('RPE').focus();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>