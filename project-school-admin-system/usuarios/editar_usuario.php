<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

include '../conexion.php';

if (!isset($_GET['id'])) {
    die("ID no válido.");
}

$id = $_GET['id'];

// Restricción para encargados: solo pueden editar usuarios de su departamento o subdepartamentos y no pueden editar administradores
if ($_SESSION["rol"] == "encargado") {
    $mi_departamento = $_SESSION["departamento_id"];
    include_once '../funciones.php';
    $subdepartamentos = obtenerSubdepartamentos($conn, $mi_departamento);
    $departamentos_permitidos = $subdepartamentos;
    $departamentos_permitidos[] = $mi_departamento;
    // Obtener el departamento y rol del usuario a editar
    $sql_dep = "SELECT departamento_id, rol FROM usuarios WHERE id = ?";
    $stmt_dep = $conn->prepare($sql_dep);
    $stmt_dep->bind_param("i", $id);
    $stmt_dep->execute();
    $result_dep = $stmt_dep->get_result();
    $usuario_dep = $result_dep->fetch_assoc();
    if (!$usuario_dep || !in_array($usuario_dep['departamento_id'], $departamentos_permitidos) || $usuario_dep['rol'] == 'admin') {
        die("No tienes permiso para editar este usuario.");
    }
}

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_usuario = $_POST["usuario"];
    $rol = $_POST["rol"];
    $departamento_id = $_POST["departamento_id"];
    $contrasena = $_POST["contrasena"];

    if (!empty($contrasena)) {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET usuario = ?, rol = ?, contraseña = ?, departamento_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nuevo_usuario, $rol, $hash, $departamento_id, $id);
    } else {
        $sql = "UPDATE usuarios SET usuario = ?, rol = ?, departamento_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $nuevo_usuario, $rol, $departamento_id, $id);
    }

    if ($stmt->execute()) {
        header("Location: ../dashboard.php?success=Usuario actualizado correctamente");
        exit();
    } else {
        $error = "Error al actualizar usuario. El nombre de usuario puede estar en uso.";
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario | Sistema Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
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
            background-color: #f5f7fa;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        h1 {
            color: var(--primary-color);
            font-size: 1.8rem;
        }
        
        .back-button {
            background-color: var(--light-color);
            color: var(--dark-color);
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background-color: #ddd;
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
        
        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s ease;
        }
        
        input:focus, select:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .submit-button {
            background-color: var(--success-color);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }
        
        .submit-button:hover {
            background-color: #219653;
        }
        
        .error-message {
            color: var(--accent-color);
            background-color: rgba(231, 76, 60, 0.1);
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 15px;
            }
            
            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Editar Usuario</h1>
            <a href="usuarios.php" class="back-button">← Volver al panel</a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="usuario">Nombre de usuario</label>
                <input type="text" id="usuario" name="usuario" 
                       value="<?php echo htmlspecialchars($usuario['usuario']); ?>" required>
            </div>
            
            <div class="form-group">
    <label for="rol">Rol del usuario</label>
    <select id="rol" name="rol">
        <?php if ($_SESSION["rol"] == "admin"): ?>
            <option value="encargado" <?php if ($usuario['rol'] == 'encargado') echo 'selected'; ?>>Encargado</option>
            <option value="admin" <?php if ($usuario['rol'] == 'admin') echo 'selected'; ?>>Administrador</option>
            <option value="capturista" <?php if ($usuario['rol'] == 'capturista') echo 'selected'; ?>>Capturista</option>
        <?php elseif ($_SESSION["rol"] == "encargado"): ?>
            <option value="encargado" <?php if ($usuario['rol'] == 'encargado') echo 'selected'; ?>>Encargado</option>
            <option value="capturista" <?php if ($usuario['rol'] == 'capturista') echo 'selected'; ?>>Capturista</option>
        <?php elseif ($_SESSION["rol"] == "capturista"): ?>
            <option value="capturista" selected>Capturista</option>
        <?php endif; ?>
    </select>
</div>

            <div class="form-group">
    <label for="departamento_id">Departamento</label>
    <select id="departamento_id" name="departamento_id" required>
        <option value="">Seleccione un departamento</option>
        <?php
        $dept_result = $conn->query("SELECT id, nombre FROM departamentos");
        if (!$dept_result) {
            die("Error al obtener departamentos: " . $conn->error);
        }
        
        while ($dept = $dept_result->fetch_assoc()) {
            $selected = ($usuario['departamento_id'] == $dept['id']) ? 'selected' : '';
            echo "<option value='{$dept['id']}' $selected>{$dept['nombre']}</option>";
        }
        ?>
    </select>
</div>

            <div class="form-group">
                <label for="contrasena">Nueva contraseña (deja en blanco para no cambiarla)</label>
                <input type="password" id="contrasena" name="contrasena">
            </div>
            
            <button type="submit" class="submit-button">Actualizar Usuario</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    $conn->close();
    ?>