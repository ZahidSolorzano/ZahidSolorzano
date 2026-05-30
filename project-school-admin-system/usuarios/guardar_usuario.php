<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

include '../conexion.php';
include '../funciones.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"]);
    $contraseña = $_POST["contraseña"];
    $rol = $_POST["rol"];
    $departamento_id = intval($_POST["departamento_id"]);

    // Validar campos obligatorios
    if (empty($usuario) || empty($contraseña) || empty($rol) || empty($departamento_id)) {
        die("Todos los campos son obligatorios.");
    }

    // Verificar si el nombre de usuario ya existe
    $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt_check->bind_param("s", $usuario);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        header("Location: usuarios.php?error=El nombre de usuario ya existe");
        exit();
    }
    $stmt_check->close();

    // Restricciones para encargados
    if ($_SESSION["rol"] == "encargado") {
        // Obtener departamentos permitidos
        $departamentos_permitidos = obtenerSubdepartamentos($conn, $_SESSION["departamento_id"]);
        $departamentos_permitidos[] = $_SESSION["departamento_id"];
        
        // Verificar que el departamento seleccionado esté permitido
        if (!in_array($departamento_id, $departamentos_permitidos)) {
            die("No tienes permiso para crear usuarios en este departamento.");
        }
        
        // Verificar que el rol sea permitido
        $es_subdepartamento = in_array($departamento_id, obtenerSubdepartamentos($conn, $_SESSION["departamento_id"]));
        $es_mismo_departamento = ($departamento_id == $_SESSION["departamento_id"]);
        
        if ($es_mismo_departamento && $rol != 'capturista') {
            die("En tu propio departamento solo puedes crear capturistas.");
        }
        
        if ($es_subdepartamento && !in_array($rol, ['capturista', 'encargado'])) {
            die("En subdepartamentos solo puedes crear capturistas o encargados.");
        }
    }

    // Encriptar contraseña
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (usuario, contraseña, rol, departamento_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $usuario, $contraseña_hash, $rol, $departamento_id);

    if ($stmt->execute()) {
        header("Location: usuarios.php?success=Usuario creado exitosamente");
    } else {
        header("Location: usuarios.php?error=Error al registrar usuario: " . urlencode($conn->error));
    }

    $stmt->close();
    $conn->close();
}
?>