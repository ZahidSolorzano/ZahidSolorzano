<?php
session_start();
include '../conexion.php';

$tarea_id = (int)$_GET['id'];

// Verificar permisos
$stmt = $conn->prepare("SELECT * FROM tareas WHERE id = ? AND usuario_origen_id = ?");
$stmt->bind_param("ii", $tarea_id, $_SESSION['id']);
$stmt->execute();
$tarea = $stmt->get_result()->fetch_assoc();

if (!$tarea || $tarea['estado'] != 'en_progreso') {
    header("Location: tareas.php?error=Tarea no válida");
    exit();
}

if ($_SESSION["rol"] == "admin") {
    // ADMIN: Autorización final
    $conn->begin_transaction();
    try {
        if ($tarea['proceso_relacionado_id']) {
            $stmt = $conn->prepare("UPDATE procesos SET estado = 'completo' WHERE id = ?");
            $stmt->bind_param("i", $tarea['proceso_relacionado_id']);
            $stmt->execute();
        }
        
        $stmt = $conn->prepare("DELETE FROM tareas WHERE id = ?");
        $stmt->bind_param("i", $tarea_id);
        $stmt->execute();
        
        $conn->commit();
        header("Location: tareas.php?success=Tarea autorizada definitivamente");
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: tareas.php?error=" . urlencode($e->getMessage()));
    }
    
} elseif ($_SESSION["rol"] == "encargado") {
    // ENCARGADO: Mostrar formulario para seleccionar admin
    
    // Obtener admins del mismo departamento
    $stmt = $conn->prepare("SELECT id, usuario FROM usuarios WHERE departamento_id = ? AND rol = 'admin' ORDER BY usuario");
    $departamento_actual = $_SESSION['departamento_id'];
    $stmt->bind_param("i", $departamento_actual);
    $stmt->execute();
    $admins = $stmt->get_result();

    // Si no hay admins en el mismo departamento, buscar en los padres
    while ($admins->num_rows == 0) {
        // Obtener el departamento padre
        $stmt_dep = $conn->prepare("SELECT parent_id FROM departamentos WHERE id = ?");
        $stmt_dep->bind_param("i", $departamento_actual);
        $stmt_dep->execute();
        $stmt_dep->bind_result($parent_id);
        $stmt_dep->fetch();
        $stmt_dep->close();

        if (!$parent_id) {
            break; // Ya no hay más padres
        }

        // Buscar admins en el departamento padre
        $stmt = $conn->prepare("SELECT id, usuario FROM usuarios WHERE departamento_id = ? AND rol = 'admin' ORDER BY usuario");
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $admins = $stmt->get_result();

        $departamento_actual = $parent_id;
    }

    // Si aún no hay admins, mostrar todos los admins del sistema para escoger
    if ($admins->num_rows == 0) {
        $admins = $conn->query("SELECT id, usuario FROM usuarios WHERE rol = 'admin' ORDER BY usuario");
        if ($admins->num_rows == 0) {
            header("Location: tareas.php?error=No hay administradores disponibles en el sistema");
            exit();
        }
    }

    // Si hay un solo admin, transferir directamente
    if ($admins->num_rows == 1) {
        $admin = $admins->fetch_assoc();

        $stmt = $conn->prepare("UPDATE tareas SET usuario_origen_id = ?, estado = 'autorizado_encargado' WHERE id = ?");
        $stmt->bind_param("ii", $admin['id'], $tarea_id);
        $stmt->execute();

        header("Location: tareas.php?success=Tarea enviada al administrador " . htmlspecialchars($admin['usuario']));
        exit();
    }

    // Si hay múltiples admins, mostrar formulario de selección
    ?>
<!DOCTYPE html>
<html>
<head>
    <title>Seleccionar Administrador</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f9f5;
        margin: 0;
        padding: 20px;
        color: #333;
    }

    .container {
        max-width: 800px;
        margin: 30px auto;
        padding: 30px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #2e7d32;
        margin-top: 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #e0f2e9;
    }

    p {
        margin-bottom: 20px;
        line-height: 1.6;
    }

    strong {
        color: #2e7d32;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        color: #1b5e20;
    }

    .form-group div {
        margin-bottom: 10px;
        padding: 12px 15px;
        background-color: #f1f8e9;
        border-radius: 6px;
        transition: all 0.3s ease;
        border-left: 4px solid #a5d6a7;
    }

    .form-group div:hover {
        background-color: #e8f5e9;
        transform: translateX(5px);
    }

    input[type="radio"] {
        margin-right: 10px;
        accent-color: #4caf50;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 20px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-success {
        background-color: #4caf50;
        color: white;
    }

    .btn-success:hover {
        background-color: #3d8b40;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-secondary {
        background-color: #e0e0e0;
        color: #333;
    }

    .btn-secondary:hover {
        background-color: #bdbdbd;
    }
    </style>
</head>
<body>
    <div class="container">
        <h2>Seleccionar Administrador</h2>
        <p><strong>Tarea:</strong> <?= htmlspecialchars($tarea['titulo']) ?></p>
        <p>Selecciona el administrador que debe revisar esta tarea:</p>
        
        <form method="POST" action="enviar_a_admin.php">
            <input type="hidden" name="tarea_id" value="<?= $tarea_id ?>">
            
            <div class="form-group">
                <label>Administrador:</label>
                <?php while ($admin = $admins->fetch_assoc()): ?>
                    <div>
                        <input type="radio" name="admin_id" value="<?= $admin['id'] ?>" id="admin_<?= $admin['id'] ?>" required>
                        <label for="admin_<?= $admin['id'] ?>"><?= htmlspecialchars($admin['usuario']) ?></label>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-success">📤 Enviar al Administrador</button>
                <a href="tareas.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
    <?php
    exit();
}
?>