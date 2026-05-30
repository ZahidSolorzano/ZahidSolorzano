<?php
session_start();

// verificar si el usuario es administrador
if (!isset($_SESSION["RPE"]) || $_SESSION["rol"] !== 'administrador') {
    header("Location: vistalogin.php");
    exit();
}

include '../includes/conexion.php';

// obtener RPE del usuario a eliminar
$rpe_eliminar = isset($_GET['rpe']) ? trim($_GET['rpe']) : '';

if (empty($rpe_eliminar)) {
    header("Location: gestion_usuarios.php?error=RPE no válido");
    exit();
}

// Convertir a mayúsculas para consistencia
$rpe_eliminar = strtoupper($rpe_eliminar);

// Verificar formato 
if (!preg_match('/^[A-Z0-9]{5}$/', $rpe_eliminar)) {
    header("Location: gestion_usuarios.php?error=RPE no válido. Debe tener 5 caracteres alfanuméricos");
    exit();
}

// DEBUG: Mostrar RPE recibido
error_log("RPE a eliminar: " . $rpe_eliminar);

// verifica que el usuario existe y obtener su rol
$sql_check = "SELECT * FROM usuarios WHERE RPE = ?";
$stmt_check = $conn->prepare($sql_check);

if (!$stmt_check) {
    error_log("Error preparando consulta: " . $conn->error);
    header("Location: gestion_usuarios.php?error=Error en la base de datos");
    exit();
}

$stmt_check->bind_param("s", $rpe_eliminar);
$stmt_check->execute();
$resultado_check = $stmt_check->get_result();

if ($resultado_check->num_rows === 0) {
    $stmt_check->close();
    header("Location: gestion_usuarios.php?error=Usuario no encontrado");
    exit();
}

$usuario_eliminar = $resultado_check->fetch_assoc();
$stmt_check->close();

// DEBUG: Mostrar datos del usuario
error_log("Usuario encontrado: " . print_r($usuario_eliminar, true));

// admins no pueden eliminar otros admins (excepto a sí mismos)
if ($usuario_eliminar['rol'] === 'administrador' && $rpe_eliminar != $_SESSION['RPE']) {
    header("Location: gestion_usuarios.php?error=No puedes eliminar otros administradores");
    exit();
}

// Verificar si hay préstamos relacionados (para información)
$sql_check_prestamos = "SELECT COUNT(*) as total FROM prestamos WHERE rpe_solicitante = ?";
$stmt_check_prestamos = $conn->prepare($sql_check_prestamos);
$stmt_check_prestamos->bind_param("s", $rpe_eliminar);
$stmt_check_prestamos->execute();
$result_check_prestamos = $stmt_check_prestamos->get_result();
$prestamos_count = $result_check_prestamos->fetch_assoc()['total'];
$stmt_check_prestamos->close();

// Eliminar usuario 
$sql_delete = "DELETE FROM usuarios WHERE RPE = ?";
$stmt_delete = $conn->prepare($sql_delete);

if (!$stmt_delete) {
    error_log("Error preparando DELETE: " . $conn->error);
    header("Location: gestion_usuarios.php?error=Error al preparar la eliminación");
    exit();
}

$stmt_delete->bind_param("s", $rpe_eliminar);

if ($stmt_delete->execute()) {
    $stmt_delete->close();
    
    // DEBUG: Éxito
    error_log("Usuario eliminado exitosamente: " . $rpe_eliminar);
    
    // si el usuario eliminado es el mismo que está logueado, cerrar sesión
    if ($rpe_eliminar == $_SESSION['RPE']) {
        session_destroy();
        header("Location: ../vistalogin.php?mensaje=Tu usuario ha sido eliminado exitosamente");
        exit();
    } else {
        $mensaje = "Usuario eliminado correctamente";
        if ($prestamos_count > 0) {
            $mensaje .= ". Se eliminaron " . $prestamos_count . " préstamo(s) relacionados.";
        }
        header("Location: gestion_usuarios.php?mensaje=" . urlencode($mensaje));
        exit();
    }
} else {
    error_log("Error ejecutando DELETE: " . $stmt_delete->error);
    $stmt_delete->close();
    header("Location: gestion_usuarios.php?error=Error al eliminar usuario: " . urlencode($conn->error));
    exit();
}

$conn->close();
?>