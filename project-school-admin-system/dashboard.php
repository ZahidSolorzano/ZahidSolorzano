<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

include 'conexion.php';

// Obtener el nombre del departamento del usuario
$departamento_nombre = '';
if (isset($_SESSION["departamento_id"])) {
    $stmt = $conn->prepare("SELECT nombre FROM departamentos WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["departamento_id"]);
    $stmt->execute();
    $stmt->bind_result($departamento_nombre);
    $stmt->fetch();
    $stmt->close();
}

if (!isset($_SESSION['id'])) {
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $_SESSION["usuario"]);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $_SESSION['id'] = $user_id;
    $stmt->close();
}

$id_usuario = $_SESSION['id'];

// Consulta: tareas que creó el usuario o le asignaron a él
$sql = "SELECT t.*, 
               u1.usuario AS origen, 
               u2.usuario AS destino 
        FROM tareas t
        JOIN usuarios u1 ON t.usuario_origen_id = u1.id
        JOIN usuarios u2 ON t.usuario_destino_id = u2.id
        WHERE t.usuario_origen_id = ? OR t.usuario_destino_id = ?
        ORDER BY t.fecha_creacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_usuario, $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$tareas_dashboard = [];
while ($fila = $resultado->fetch_assoc()) {
    $tareas_dashboard[] = $fila;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel principal</title>
    <link rel="stylesheet" href="dashboard4.css">
</head>
<body>
    <div class="header-container">
        <div class="welcome-section">
            <h1>
                Bienvenido, <?php echo htmlspecialchars($_SESSION["usuario"]); ?>
                <?php if (isset($_SESSION["rol"])): ?>
                    - <?php echo htmlspecialchars($_SESSION["rol"]); ?>
                <?php endif; ?>
                <?php if ($departamento_nombre): ?>
                    (<?php echo htmlspecialchars($departamento_nombre); ?>)
                <?php endif; ?>
            </h1>
            
            <!-- Botones de acción -->
            <div class="action-buttons">
                <a href="logout.php" class="btn btn-logout">Cerrar Sesión</a>
                <a href="usuarios/editar_usuario.php?id=<?php echo $_SESSION['id']; ?>" class="btn btn-profile">Editar mi perfil</a>
            </div>
            
            <!-- Navegación principal (solo para admin en el header) -->
            <?php if ($_SESSION["rol"] == "admin"): ?>
                <ul class="admin-nav">
                    <li><a href="usuarios/usuarios.php">👥 Usuarios</a></li>
                    <li><a href="departamentos/departamentos.php">🏢 Departamentos</a></li>
                    <li><a href="procesos/procesos.php">📄 Procesos</a></li>
                    <li><a href="tareas/tareas.php">📋 Tareas</a></li>
                </ul>
            <?php endif; ?>
        </div>
        
        <div class="logo-section">
            <div class="logo-display">
                <?php 
                // Buscar logo con cualquier extensión
                $logos_existentes = glob('assets/logo_cetis27.*');
                if (!empty($logos_existentes) && file_exists($logos_existentes[0])): 
                    $logo_path = $logos_existentes[0]; ?>
                    <img src="<?= $logo_path ?>?v=<?= time() ?>" alt="Logo CETIS 27" class="current-logo">
                <?php else: ?>
                    <div class="no-logo">Sin logo</div>
                <?php endif; ?>
            </div>
            <?php if ($_SESSION["rol"] == "admin"): ?>
                <form action="cambiar_logo.php" method="post" enctype="multipart/form-data" class="logo-form">
                    <label for="logo">Cambiar logo:</label>
                    <input type="file" name="logo" id="logo" accept="image/*" required>
                    <button type="submit">Subir</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mensajes de notificación -->
    <?php if (isset($_GET['success'])): ?>
        <div class="message success">✅ <?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="message error">❌ <?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- Navegación principal (para encargados y capturistas) -->
    <?php if ($_SESSION["rol"] != "admin"): ?>
        <ul>
            <li><a href="usuarios/usuarios.php">👥 Usuarios</a></li>
            <li><a href="departamentos/departamentos.php">🏢 Departamentos</a></li>
            <li><a href="procesos/procesos.php">📄 Procesos</a></li>
            <li><a href="tareas/tareas.php">📋 Tareas</a></li>
        </ul>
    <?php endif; ?>

    <!-- Lista de tareas creadas por el usuario -->
    <h2>Tareas que creaste para otros</h2>
    <table border="1" style="width:100%; margin-bottom:20px;">
        <tr>
            <th>Título</th>
            <th>Descripción</th>
            <th>De</th>
            <th>Para</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php
        $tiene_creadas = false;
        foreach ($tareas_dashboard as $tarea):
            // Mostrar en "creadas" solo si es el creador Y NO es el asignado (para evitar duplicados)
            if ($tarea['usuario_origen_id'] == $_SESSION['id'] && $tarea['usuario_destino_id'] != $_SESSION['id']):
                $tiene_creadas = true;
        ?>
        <tr>
            <td><?= htmlspecialchars($tarea['titulo']) ?></td>
           <td title="<?= htmlspecialchars($tarea['descripcion']) ?>">
    <?= substr(htmlspecialchars($tarea['descripcion']), 0, 50) ?>...
</td>
            <td><?= htmlspecialchars($tarea['origen']) ?></td>
            <td><?= htmlspecialchars($tarea['destino']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($tarea['fecha_creacion'])) ?></td>
            <td><?= htmlspecialchars($tarea['estado']) ?></td>
            <td class="acciones">
    <div class="acciones-fila">
        <?php
        // Primera fila de acciones
        if ($_SESSION['rol'] == 'admin' || $tarea['usuario_origen_id'] == $_SESSION['id']) {
            echo "<a href='tareas/editar_tarea.php?id={$tarea['id']}'>✏️ Editar</a>";
            echo "<a href='tareas/eliminar_tarea.php?id={$tarea['id']}' onclick='return confirm(\"¿Eliminar esta tarea?\")'>🗑️ Eliminar</a>";
        }
        if (!empty($tarea['proceso_relacionado_id'])) {
            echo "<a href='procesos/ver_proceso.php?id={$tarea['proceso_relacionado_id']}'>📄 Proceso</a>";
        }
        ?>
    </div>
    <div class="acciones-fila">
        <?php
        // Segunda fila de acciones (especiales)
        if ($tarea['usuario_origen_id'] == $_SESSION['id']) {
            // Botón Devolver - disponible para todos los estados excepto completado y atrasada
            if (!in_array($tarea['estado'], ['completado', 'atrasada'])) {
                echo "<a href='tareas/volver_pedir.php?id={$tarea['id']}'>🔄 Devolver</a>";
            }
            
            if ($tarea['estado'] == 'en_progreso') {
                if ($_SESSION["rol"] == "admin") {
                    echo "<a href='tareas/autorizar_tarea.php?id={$tarea['id']}'>✅ Autorizar</a>";
                    echo "<a href='tareas/rechazar_tarea.php?id={$tarea['id']}'>❌ Rechazar</a>";
                } elseif ($_SESSION["rol"] == "encargado") {
                    echo "<a href='tareas/autorizar_tarea.php?id={$tarea['id']}'>📤 Admin</a>";
                    echo "<a href='tareas/rechazar_tarea.php?id={$tarea['id']}'>❌ Rechazar</a>";
                }
            } elseif ($tarea['estado'] == 'autorizado_encargado' && $_SESSION["rol"] == "admin") {
                echo "<a href='tareas/autorizar_tarea.php?id={$tarea['id']}'>✅ Autorizar</a>";
                echo "<a href='tareas/rechazar_tarea.php?id={$tarea['id']}'>❌ Rechazar</a>";
            }
        }
        ?>
    </div>
</td>
        </tr>
        <?php
            endif;
        endforeach;
        if (!$tiene_creadas): ?>
        <tr><td colspan="7" style="text-align:center;">No has creado tareas para otros usuarios.</td></tr>
        <?php endif; ?>
    </table>

    <!-- Lista de tareas asignadas al usuario -->
    <h2>Tareas que tienes asignadas</h2>
    <table border="1" style="width:100%; margin-bottom:20px;">
        <tr>
            <th>Título</th>
            <th>Descripción</th>
            <th>De</th>
            <th>Para</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php
        $tiene_asignadas = false;
        foreach ($tareas_dashboard as $tarea):
            if ($tarea['usuario_destino_id'] == $_SESSION['id']):
                $tiene_asignadas = true;
        ?>
        <tr>
            <td><?= htmlspecialchars($tarea['titulo']) ?></td>
            <td title="<?= htmlspecialchars($tarea['descripcion']) ?>">
    <?= substr(htmlspecialchars($tarea['descripcion']), 0, 50) ?>...
</td>
            <td><?= htmlspecialchars($tarea['origen']) ?></td>
            <td><?= htmlspecialchars($tarea['destino']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($tarea['fecha_creacion'])) ?></td>
            <td><?= htmlspecialchars($tarea['estado']) ?></td>
            <td class="acciones" style="white-space:nowrap;">
                <?php
                // En tareas asignadas, NO mostrar botones de editar/eliminar
                // El usuario asignado solo debe trabajar en el proceso, no editar la tarea
                // Botón para ver proceso si existe
                if (!empty($tarea['proceso_relacionado_id'])) {
                    echo "<a href='procesos/ver_proceso.php?id={$tarea['proceso_relacionado_id']}'>📄 Ver Proceso</a> ";
                }
                // Botón para iniciar o continuar proceso 
                if (
                    $tarea['usuario_destino_id'] == $_SESSION['id'] &&
                    (in_array($tarea['estado'], ['pendiente', 'atrasada', 'en_correccion', 'en_progreso']))
                ) {
                    if ($tarea['estado'] == 'en_progreso') {
                        $url = "procesos/editar_proceso.php?id={$tarea['proceso_relacionado_id']}";
                        $texto = "Continuar";
                    } elseif (in_array($tarea['estado'], ['atrasada', 'en_correccion']) && !empty($tarea['proceso_relacionado_id'])) {
                        $url = "procesos/corregir_proceso.php?id={$tarea['proceso_relacionado_id']}";
                        $texto = "Corregir";
                    } else {
                        $url = "procesos/crear.php?tarea_id={$tarea['id']}";
                        $texto = "Iniciar";
                    }
                    echo "<a href='{$url}' class='btn btn-proceso'>▶️ {$texto} Proceso</a> ";
                }
                ?>
            </td>
        </tr>
        <?php
            endif;
        endforeach;
        if (!$tiene_asignadas): ?>
        <tr><td colspan="7" style="text-align:center;">No tienes tareas asignadas.</td></tr>
        <?php endif; ?>
    </table>
    
</body>
</html>



