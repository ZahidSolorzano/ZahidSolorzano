<?php
session_start();
if (!isset($_SESSION["RPE"])) {
    header("Location: vistalogin.php");
    exit();
}

include '../includes/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: gestion_prestamos.php?error=ID no recibido");
    exit();
}

$id_prestamo = intval($_GET['id']);

// Obtener toda la información necesaria del préstamo
$sql = "SELECT 
            p.*,
            p.rpe_responsable_original,
            p.nombre_responsable_original
        FROM prestamos p 
        WHERE p.id_prestamo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_prestamo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: gestion_prestamos.php?error=Préstamo no encontrado");
    exit();
}

$prestamo = $result->fetch_assoc();
$numero_serie = $prestamo['numero_serie_equipo'];

// INICIAR TRANSACCIÓN
$conn->begin_transaction();

try {
    // 1. SI EL PRÉSTAMO ESTÁ ACTIVO O ESPERANDO DEVOLUCIÓN, RESTAURAR VALORES ORIGINALES
    if ($prestamo['estado_prestamo'] == 'activo' || $prestamo['estado_prestamo'] == 'esperando_devolucion') {
        // Restaurar el responsable original del equipo
        $sql_update_equipo = "UPDATE equipos 
                              SET Estado = 'Disponible', 
                                  RPE_responsable = ?, 
                                  Nombre_responsable = ? 
                              WHERE Numero_serie = ?";
        $stmt_update = $conn->prepare($sql_update_equipo);
        $stmt_update->bind_param(
            "sss", 
            $prestamo['rpe_responsable_original'],
            $prestamo['nombre_responsable_original'],
            $numero_serie
        );
        
        if (!$stmt_update->execute()) {
            throw new Exception("Error al restaurar valores del equipo: " . $stmt_update->error);
        }
    } 
    // 2. SI EL PRÉSTAMO ESTÁ ESPERANDO INICIO, solo marcar equipo como disponible
    elseif ($prestamo['estado_prestamo'] == 'esperando_inicio') {
        // Primero verificar si el equipo sigue marcado como "Prestado"
        $sql_check_equipo = "SELECT Estado FROM equipos WHERE Numero_serie = ?";
        $stmt_check = $conn->prepare($sql_check_equipo);
        $stmt_check->bind_param("s", $numero_serie);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $equipo = $result_check->fetch_assoc();
        
        // Solo actualizar si el equipo no está disponible
        if ($equipo && $equipo['Estado'] != 'Disponible') {
            $sql_update_equipo = "UPDATE equipos SET Estado = 'Disponible' WHERE Numero_serie = ?";
            $stmt_update = $conn->prepare($sql_update_equipo);
            $stmt_update->bind_param("s", $numero_serie);
            
            if (!$stmt_update->execute()) {
                throw new Exception("Error al actualizar estado del equipo: " . $stmt_update->error);
            }
        }
    }
    // 3. Si el préstamo ya está finalizado, no hacer nada con el equipo
    
    // 4. ELIMINAR EL PRÉSTAMO
    $sql_delete = "DELETE FROM prestamos WHERE id_prestamo = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_prestamo);
    
    if (!$stmt_delete->execute()) {
        throw new Exception("Error al eliminar préstamo: " . $stmt_delete->error);
    }
    
    // CONFIRMAR TRANSACCIÓN
    $conn->commit();
    
    header("Location: gestion_prestamos.php?mensaje=Préstamo eliminado correctamente");
    
} catch (Exception $e) {
    // REVERTIR EN CASO DE ERROR
    $conn->rollback();
    header("Location: gestion_prestamos.php?error=Error al eliminar: " . $e->getMessage());
    exit();
}

exit();
?>