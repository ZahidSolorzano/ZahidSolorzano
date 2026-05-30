<?php
session_start();

// verificar si el usuario es admin
if (!isset($_SESSION["RPE"])){
    header("Location: vistalogin.php");
    exit();
}

include '../includes/conexion.php';

$mensaje = '';
$error = '';

// Lógica inteligente: Si hay GET, úsalo. Si no, usa el RPE de la sesión.
if (isset($_GET['rpe'])) {
    $rpe_editar = trim($_GET['rpe']);
    
    // Si el usuario NO es administrador y trata de editar otro usuario, redirigir
    if ($_SESSION['rol'] !== 'administrador' && $rpe_editar != $_SESSION['RPE']) {
        header("Location: gestion_usuarios.php?error=No tienes permiso para editar otros usuarios");
        exit();
    }
} else {
    // Si vengo del dashboard sin parámetros, edito mi propio perfil
    $rpe_editar = $_SESSION['RPE'];
}

// Convertir a mayúsculas para consistencia
$rpe_editar = strtoupper($rpe_editar);

// Validación del formato RPE
if (!preg_match('/^[A-Z0-9]{5}$/', $rpe_editar)) {
    header("Location: gestion_usuarios.php?error=RPE no válido");
    exit();
}

// obtener datos del usuario que se va a editar
$sql = "SELECT * FROM usuarios WHERE RPE = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rpe_editar);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: gestion_usuarios.php");
    exit();
}

$usuario = $resultado->fetch_assoc();
$stmt->close();

// Determinar si el usuario actual puede editar este usuario
$es_admin = ($_SESSION['rol'] === 'administrador');
$edita_propio_perfil = ($rpe_editar == $_SESSION['RPE']);
$es_empleado = (!$es_admin); // Usuario actual es empleado

// Verificar permisos
if (!$es_admin) {
    // Empleados solo pueden editar su propio perfil
    if (!$edita_propio_perfil) {
        header("Location: gestion_usuarios.php?error=No tienes permiso para editar otros usuarios");
        exit();
    }
} else {
    // Admins no pueden editar otros admins (excepto su propio perfil)
    if ($usuario['rol'] === 'administrador' && !$edita_propio_perfil) {
        header("Location: gestion_usuarios.php?error=No puedes editar otros administradores");
        exit();
    }
}

// actualizar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_rpe = strtoupper(trim($_POST['rpe']));
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_rol = $_POST['rol'];
    $nuevo_departamento = trim($_POST['departamento']);
    $nueva_division = trim($_POST['division']);
    $nueva_contraseña = $_POST['contraseña'];
    
    // Validar que empleados no cambien su rol
    if ($es_empleado && $edita_propio_perfil) {
        // El empleado siempre mantiene su rol actual
        $nuevo_rol = $usuario['rol'];
    }
    
    // validaciones 
    if (empty($nuevo_rpe) || empty($nuevo_nombre)) {
        $error = "El RPE y nombre son obligatorios";
    } elseif (!preg_match('/^[A-Z0-9]{5}$/', $nuevo_rpe)) {
        $error = "El RPE debe tener exactamente 5 caracteres alfanuméricos";
    } else {
        // Verificar si el nuevo RPE ya existe (excepto si es el mismo)
        if ($nuevo_rpe != $rpe_editar) {
            $sql_check = "SELECT RPE FROM usuarios WHERE RPE = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $nuevo_rpe);
            $stmt_check->execute();
            $resultado_check = $stmt_check->get_result();
            
            if ($resultado_check->num_rows > 0) {
                $error = "El RPE $nuevo_rpe ya está registrado por otro usuario";
            }
            $stmt_check->close();
        }
        
        if (empty($error)) {
            // Si se puso una nueva contraseña
            if (!empty($nueva_contraseña)) {
                if (strlen($nueva_contraseña) < 5) {
                    $error = "La contraseña debe tener al menos 5 caracteres";
                } else {
                    $contraseña_hash = password_hash($nueva_contraseña, PASSWORD_DEFAULT);
                    $sql_update = "UPDATE usuarios SET RPE = ?, nombre = ?, rol = ?, departamento = ?, division = ?, contraseña = ? WHERE RPE = ?";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bind_param("sssssss", $nuevo_rpe, $nuevo_nombre, $nuevo_rol, $nuevo_departamento, $nueva_division, $contraseña_hash, $rpe_editar);
                }
            } else {
                // si no cambio la contraseña
                $sql_update = "UPDATE usuarios SET RPE = ?, nombre = ?, rol = ?, departamento = ?, division = ? WHERE RPE = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ssssss", $nuevo_rpe, $nuevo_nombre, $nuevo_rol, $nuevo_departamento, $nueva_division, $rpe_editar);
            }
            
            if (empty($error)) {
                if ($stmt_update->execute()) {
                    $mensaje = "Usuario actualizado correctamente";
                    
                    // Actualizar datos 
                    $usuario['RPE'] = $nuevo_rpe;
                    $usuario['nombre'] = $nuevo_nombre;
                    $usuario['rol'] = $nuevo_rol;
                    $usuario['departamento'] = $nuevo_departamento;
                    $usuario['division'] = $nueva_division;
                    
                    // si se edito el propio usuario, actualizar la sesión
                    if ($rpe_editar == $_SESSION['RPE']) {
                        $_SESSION['RPE'] = $nuevo_rpe;
                        $_SESSION['nombre'] = $nuevo_nombre;
                        $_SESSION['rol'] = $nuevo_rol;
                    }
                    
                    // mostrar datos actualizados
                    $rpe_editar = $nuevo_rpe;
                    
                } else {
                    $error = "Error al actualizar usuario: " . $conn->error;
                }
                $stmt_update->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Sistema de Inventario CFE</title>
    <link rel="stylesheet" href="editar_usuario.css">
    <link rel="icon" href="../img/logo_cfe.png">
</head>
<body>
    
    <header>
        <div class="logo-container">
            <img src="../img/logo_cfe.png" alt="Logo CFE" class="logo">
            <h1>Sistema de Inventarios | CFE</h1>
        </div>
        <div class="user-info">
            <a href="../vistas/dashboard.php">Dashboard</a>
            <?php if ($_SESSION['rol'] === 'administrador'): ?>
                <a href="gestion_usuarios.php">Gestión de Usuarios</a>
            <?php endif; ?>
            <a href="../includes/logout.php">Cerrar sesión</a>
        </div>
    </header>

    <div class="main-container">
        <div class="form-container">
            <h1>Editar Usuario</h1>
            <p class="subtitle">
                <?php echo ($edita_propio_perfil) ? 'Editando tu perfil' : 'Administrador editando usuario: ' . htmlspecialchars($usuario['nombre']); ?>
            </p>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-success">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- Información del usuario en grid horizontal -->
            <div class="user-info-grid">
                <div class="info-card">
                    <span class="info-label">RPE actual</span>
                    <span class="info-value"><?php echo htmlspecialchars($usuario['RPE']); ?></span>
                </div>
                <div class="info-card">
                    <span class="info-label">Nombre actual</span>
                    <span class="info-value"><?php echo htmlspecialchars($usuario['nombre']); ?></span>
                </div>
                <div class="info-card">
                    <span class="info-label">Rol actual</span> 
                    <span class="rol-badge <?php echo $usuario['rol']; ?>">
                        <?php echo htmlspecialchars($usuario['rol']); ?>
                    </span>
                </div>
                <div class="info-card">
                    <span class="info-label">Departamento actual</span>
                    <span class="info-value"><?php echo htmlspecialchars($usuario['departamento']); ?></span>
                </div>
                <div class="info-card">
                    <span class="info-label">División actual</span>
                    <span class="info-value"><?php echo htmlspecialchars($usuario['division']); ?></span>
                </div>
            </div>
            
            <!-- Formulario en grid de 2 columnas -->
            <form method="POST" action="" id="editarUsuarioForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="rpe">Nuevo RPE:</label>
                        <input type="text" id="rpe" name="rpe" 
                               value="<?php echo htmlspecialchars($usuario['RPE']); ?>" 
                               pattern="[A-Z0-9]{5}"
                               title="5 caracteres alfanuméricos (letras y números)"
                               maxlength="5"
                               minlength="5"
                               required
                               placeholder="Ej: 9M26A o 12345">
                        
                    </div>
                    
                    <div class="form-group">
                        <label for="nombre">Nombre completo:</label>
                        <input type="text" id="nombre" name="nombre" 
                               value="<?php echo htmlspecialchars($usuario['nombre']); ?>" 
                               required
                               placeholder="Ingrese el nombre completo">
                    </div>
                    
                    <div class="form-group">
                        <label for="rol">Rol:</label>
                        <?php if ($es_empleado && $edita_propio_perfil): ?>
                            <!-- Empleado editando su propio perfil - NO puede cambiar rol -->
                            <input type="text" id="rol-display" 
                                   value="<?php echo htmlspecialchars($usuario['rol']); ?>" 
                                   readonly
                                   class="readonly-field">
                            <input type="hidden" name="rol" value="<?php echo htmlspecialchars($usuario['rol']); ?>">
                            <small class="help-text">No puedes cambiar tu rol</small>
                        <?php else: ?>
                            <!-- Administrador (editando cualquier perfil) o cualquier usuario editando otro -->
                            <select id="rol" name="rol" required>
                                <option value="empleado" <?php echo ($usuario['rol'] == 'empleado') ? 'selected' : ''; ?>>Empleado</option>
                                <option value="administrador" <?php echo ($usuario['rol'] == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                            <?php if ($edita_propio_perfil): ?>
                               
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="departamento">Departamento:</label>
                        <input type="text" id="departamento" name="departamento" 
                               value="<?php echo htmlspecialchars($usuario['departamento']); ?>" 
                               placeholder="Ingrese el departamento">
                    </div>
                    
                    <div class="form-group">
                        <label for="division">División:</label>
                        <input type="text" id="division" name="division" 
                               value="<?php echo htmlspecialchars($usuario['division']); ?>" 
                               placeholder="Ingrese la división">
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="contraseña">Nueva contraseña (opcional):</label>
                        <input type="password" id="contraseña" name="contraseña" 
                               placeholder="Dejar en blanco para mantener la actual"
                               minlength="5">
                        <small class="help-text">Mínimo 5 caracteres. Solo llene si desea cambiar la contraseña.</small>
                    </div>
                    
                    <div class="btn-container">
                        <button type="submit" class="btn-primary">Guardar Cambios</button>
                        <a href="<?php echo ($es_admin) ? 'gestion_usuarios.php' : '../vistas/dashboard.php'; ?>" class="btn-secondary">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Comisión Federal de Electricidad. Todos los derechos reservados.</p>
    </footer>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const rpeInput = document.getElementById('rpe');
        const passwordInput = document.getElementById('contraseña');
        const form = document.getElementById('editarUsuarioForm');
        
        // Convertir automáticamente a mayúsculas el RPE
        if (rpeInput) {
            rpeInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
                
                // Solo permitir letras y números
                this.value = this.value.replace(/[^A-Z0-9]/g, '');
                
                // Limitar a 5 caracteres
                if (this.value.length > 5) {
                    this.value = this.value.slice(0, 5);
                }
            });
            
            // Validar formato al perder foco
            rpeInput.addEventListener('blur', function() {
                if (!/^[A-Z0-9]{5}$/.test(this.value)) {
                    this.setCustomValidity('El RPE debe tener exactamente 5 caracteres alfanuméricos');
                    this.reportValidity();
                } else {
                    this.setCustomValidity('');
                }
            });
        }
        
        // Validar longitud de contraseña
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0 && this.value.length < 5) {
                    this.setCustomValidity('La contraseña debe tener al menos 5 caracteres');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
        
        // Validación del formulario
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Validar RPE
                if (rpeInput && !/^[A-Z0-9]{5}$/.test(rpeInput.value)) {
                    e.preventDefault();
                    rpeInput.setCustomValidity('El RPE debe tener exactamente 5 caracteres alfanuméricos');
                    rpeInput.reportValidity();
                    isValid = false;
                }
                
                // Validar contraseña si se ingresó
                if (passwordInput && passwordInput.value.length > 0 && passwordInput.value.length < 5) {
                    e.preventDefault();
                    passwordInput.setCustomValidity('La contraseña debe tener al menos 5 caracteres');
                    passwordInput.reportValidity();
                    isValid = false;
                }
                
                return isValid;
            });
        }
    });
    </script>
</body>
</html>