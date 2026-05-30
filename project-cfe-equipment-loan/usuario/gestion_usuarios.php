<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION["RPE"]) || !isset($_SESSION["rol"])){
    header("Location: vistalogin.php");
    exit();
}

include '../includes/conexion.php';

$mensaje = '';
$error = '';

// Obtener el rol del usuario actual
$rol_usuario_actual = $_SESSION["rol"];
$mi_rpe = $_SESSION["RPE"];

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $RPE = $_POST['RPE'];
    $nombre = $_POST['nombre'];
    $rol = $_POST['rol'];
    $departamento = $_POST['departamento'];
    $division = $_POST['division'];
    $contraseña = $_POST['contraseña'];
    $confirmar_contraseña = $_POST['confirmar_contraseña'];

    // Validación 1: Usuario empleado no puede crear administradores
    if ($rol_usuario_actual == 'empleado' && $rol == 'administrador') {
        $error = "No tienes permisos para crear usuarios administradores";
    }
    // Validación 2: Campos obligatorios - AHORA DEPARTAMENTO Y DIVISIÓN TAMBIÉN
    elseif (empty($RPE) || empty($nombre) || empty($rol) || empty($departamento) || empty($division) || empty($contraseña)) {
        $error = "Todos los campos son obligatorios";
    } 
    // Validación 3: Contraseñas coinciden
    elseif ($contraseña !== $confirmar_contraseña) {
        $error = "Las contraseñas no coinciden";
    } 
    // Validación 4: Longitud de contraseña
    elseif (strlen($contraseña) < 5) {
        $error = "La contraseña debe tener al menos 5 caracteres";
    } 
    // Validación 5: Formato del RPE
    elseif (!preg_match('/^[A-Za-z0-9]{5}$/', $RPE)) {
        $error = "El RPE debe tener exactamente 5 caracteres alfanuméricos";
    } else {
        // Convertir RPE a mayúsculas para consistencia
        $RPE = strtoupper($RPE);
        
        // Verificar si el RPE ya existe
        $sql_check = "SELECT * FROM usuarios WHERE RPE = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $RPE);
        $stmt_check->execute();
        $resultado_check = $stmt_check->get_result();

        if ($resultado_check->num_rows > 0) {
            $error = "El RPE ya está registrado";
        } else {
            // Encriptar la contraseña
            $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

            // Insertar usuario en la base de datos
            $sql = "INSERT INTO usuarios (RPE, nombre, rol, contraseña, departamento, division) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $RPE, $nombre, $rol, $contraseña_hash, $departamento, $division);

            if ($stmt->execute()) {
                $mensaje = "Usuario registrado exitosamente";
                // Limpiar los campos del formulario
                $_POST = array();
            } else {
                $error = "Error al registrar usuario: " . $conn->error;
            }
        }
    }
}

// Obtener lista de usuarios
$sql_usuarios = "SELECT RPE, nombre, rol, departamento, division FROM usuarios ORDER BY RPE";
$resultado_usuarios = $conn->query($sql_usuarios);
$usuarios = [];
if ($resultado_usuarios->num_rows > 0) {
    $usuarios = $resultado_usuarios->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Sistema de Inventarios | CFE</title>
    <link rel="stylesheet" href="gestion_usuarios.css">
    <link rel="icon" href="../img/logo_cfe.png">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="../img/logo_cfe.png" alt="Logo CFE" class="logo">
            <h1>Sistema de Inventarios | CFE</h1>
        </div>
        <nav>
            <ul>
                <li><a href="../vistas/dashboard.php">Dashboard</a></li>
                <li><a href="../includes/logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <div class="content-wrapper">
            <!-- Formulario de registro -->
            <div class="form-section">
                <h2>Registrar Nuevo Usuario</h2>
                
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?php echo $mensaje; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="" class="compact-form">
                    <div class="form-row">
                        <div class="form-group compact">
                            <label for="RPE">RPE:</label>
                            <input type="text" id="RPE" name="RPE" required 
                                   pattern="[A-Za-z0-9]{5}"
                                   title="El RPE debe tener exactamente 5 caracteres alfanuméricos (letras y números)"
                                   maxlength="5"
                                   minlength="5"
                                   value="<?php echo isset($_POST['RPE']) ? htmlspecialchars(strtoupper($_POST['RPE'])) : ''; ?>">
                        </div>

                        <div class="form-group compact">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" required
                                   value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group compact">
                            <label for="rol">Rol:</label>
                            <select id="rol" name="rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="empleado" <?php echo (isset($_POST['rol']) && $_POST['rol'] == 'empleado') ? 'selected' : ''; ?>>Empleado</option>
                                <?php if ($rol_usuario_actual == 'administrador'): ?>
                                    <option value="administrador" <?php echo (isset($_POST['rol']) && $_POST['rol'] == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                                <?php endif; ?>
                            </select>
                            <?php if ($rol_usuario_actual == 'empleado'): ?>
                               
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group compact">
                            <label for="departamento">Departamento:</label>
                            <input type="text" id="departamento" name="departamento" required
                                   value="<?php echo isset($_POST['departamento']) ? htmlspecialchars($_POST['departamento']) : ''; ?>">
                        </div>

                        <div class="form-group compact">
                            <label for="division">División:</label>
                            <input type="text" id="division" name="division" required
                                   value="<?php echo isset($_POST['division']) ? htmlspecialchars($_POST['division']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group compact">
                            <label for="contraseña">Contraseña:</label>
                            <input type="password" id="contraseña" name="contraseña" required minlength="5">
                        </div>

                        <div class="form-group compact">
                            <label for="confirmar_contraseña">Confirmar:</label>
                            <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required minlength="5">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                        <a href="../vistas/dashboard.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
            <!-- Lista de usuarios -->
            <div class="users-section">
                <h3>Usuarios Registrados</h3>
                
                <?php if (count($usuarios) > 0): ?>
                    <div class="users-table-container">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>RPE</th>
                                    <th>Nombre</th>
                                    <th>Rol</th>
                                    <th>Departamento</th>
                                    <th>División</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td data-label="RPE">
                                        <?php echo htmlspecialchars(strtoupper($usuario['RPE'])); ?>
                                        <?php if (strtoupper($usuario['RPE']) == strtoupper($mi_rpe)): ?>
                                            <span class="you-badge">(Tú)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Nombre"><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td data-label="Rol">
                                        <span class="rol-badge <?php echo $usuario['rol']; ?>">
                                            <?php echo htmlspecialchars($usuario['rol']); ?>
                                        </span>
                                    </td>
                                    <td data-label="Departamento"><?php echo htmlspecialchars($usuario['departamento']); ?></td>
                                    <td data-label="División"><?php echo htmlspecialchars($usuario['division']); ?></td>
                                    <td data-label="Acciones">
    <div class="action-buttons">
        <?php 
        // Convertir a mayúsculas para comparación consistente
        $usuario_rpe_upper = strtoupper($usuario['RPE']);
        $mi_rpe_upper = strtoupper($mi_rpe);
        $usuario_rol = $usuario['rol'];
        
        // LÓGICA para permisos de Editar/Eliminar:
        $puede_editar = false;
        $puede_eliminar = false;
        $es_mi_perfil = ($usuario_rpe_upper == $mi_rpe_upper);
        
        // CASO 1: Es mi propio perfil (tanto admin como empleado pueden editar/eliminar su perfil)
        if ($es_mi_perfil) {
            $puede_editar = true;
            $puede_eliminar = true;
        }
        // CASO 2: No es mi perfil, pero soy administrador y el usuario es empleado
        elseif ($rol_usuario_actual == 'administrador' && $usuario_rol == 'empleado') {
            $puede_editar = true;
            $puede_eliminar = true;
        }
        // CASO 3: Cualquier otra combinación = NO tiene permisos
        // (admin tratando de editar otro admin, o empleado tratando de editar otro usuario)
        
        // Determinar mensajes de tooltip
        $titulo_editar = $puede_editar ? "Editar usuario" : (
            $usuario_rol == 'administrador' && !$es_mi_perfil 
                ? "No puedes editar otros administradores" 
                : "No tienes permisos para editar este usuario"
        );
        
        $titulo_eliminar = $puede_eliminar ? "Eliminar usuario" : (
            $usuario_rol == 'administrador' && !$es_mi_perfil 
                ? "No puedes eliminar otros administradores" 
                : "No tienes permisos para eliminar este usuario"
        );
        ?>
        
        <!-- Botón Editar -->
        <?php if ($puede_editar): ?>
            <a href="editar_usuario.php?rpe=<?php echo $usuario['RPE']; ?>" class="btn-edit" title="<?php echo $titulo_editar; ?>">
                Editar
            </a>
        <?php else: ?>
            <span class="btn-disabled" title="<?php echo $titulo_editar; ?>">Editar</span>
        <?php endif; ?>
        
        <!-- Botón Eliminar -->
        <?php if ($puede_eliminar): ?>
            <a href="eliminar_usuario.php?rpe=<?php echo $usuario['RPE']; ?>" class="btn-delete" 
               onclick="return confirm('<?php echo $es_mi_perfil ? '¿Estás seguro de eliminar TU PROPIO usuario? Esta acción cerrará tu sesión inmediatamente.' : '¿Estás seguro de eliminar este usuario?'; ?>')"
               title="<?php echo $titulo_eliminar; ?>">
                Eliminar
            </a>
        <?php else: ?>
            <span class="btn-disabled" title="<?php echo $titulo_eliminar; ?>">Eliminar</span>
        <?php endif; ?>
    </div>
</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-users">
                        <p>No hay usuarios registrados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>