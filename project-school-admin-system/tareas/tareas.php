<?php
session_start();
require_once '../conexion.php';
require_once '../funciones.php'; // Asegúrate de incluir funciones.php


// Obtener tareas según el usuario
if ($_SESSION['rol'] == 'admin') {
    // Admin ve todas las tareas
    $sql = "SELECT t.*, uo.usuario as origen, ud.usuario as destino 
            FROM tareas t
            JOIN usuarios uo ON t.usuario_origen_id = uo.id
            JOIN usuarios ud ON t.usuario_destino_id = ud.id
            ORDER BY t.fecha_creacion DESC";
    $stmt = $conn->prepare($sql);
} elseif ($_SESSION['rol'] == 'encargado') {
    // Encargado ve tareas de su departamento y subdepartamentos
    $mi_departamento = $_SESSION['departamento_id'];
    $subdepartamentos = obtenerSubdepartamentos($conn, $mi_departamento);
    $departamentos_permitidos = array_merge([$mi_departamento], $subdepartamentos);
    $ids_departamentos = implode(',', $departamentos_permitidos);
    
    $sql = "SELECT t.*, uo.usuario as origen, ud.usuario as destino 
            FROM tareas t
            JOIN usuarios uo ON t.usuario_origen_id = uo.id
            JOIN usuarios ud ON t.usuario_destino_id = ud.id
            JOIN usuarios u_origen ON uo.id = u_origen.id
            JOIN usuarios u_destino ON ud.id = u_destino.id
            WHERE u_origen.departamento_id IN ($ids_departamentos) 
               OR u_destino.departamento_id IN ($ids_departamentos)
               OR t.usuario_origen_id = ?
               OR t.usuario_destino_id = ?
            ORDER BY t.fecha_creacion DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['id'], $_SESSION['id']);
} else {
    // Capturista ve solo sus tareas asignadas y las que creó
    $sql = "SELECT t.*, uo.usuario as origen, ud.usuario as destino 
            FROM tareas t
            JOIN usuarios uo ON t.usuario_origen_id = uo.id
            JOIN usuarios ud ON t.usuario_destino_id = ud.id
            WHERE t.usuario_destino_id = ? OR t.usuario_origen_id = ?
            ORDER BY t.fecha_creacion DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['id'], $_SESSION['id']);
}

$stmt->execute();
$tareas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt = $conn->prepare($sql);
if ($_SESSION['rol'] != 'admin') {
    $stmt->bind_param("ii", $_SESSION['id'], $_SESSION['id']);
}
$stmt->execute();
$tareas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Obtener usuarios para el select según permisos
$usuarios = [];
if ($_SESSION['rol'] == 'admin') {
    // Admin puede asignar a cualquiera menos a sí mismo
    $usuarios = $conn->query("SELECT id, usuario FROM usuarios WHERE id != ".$_SESSION['id'])->fetch_all(MYSQLI_ASSOC);
} elseif ($_SESSION['rol'] == 'encargado') {
    
    $mi_departamento = $_SESSION['departamento_id'];
    $subdepartamentos = obtenerSubdepartamentos($conn, $mi_departamento);

    // Armar la consulta para capturistas y encargados de subdepartamentos hijos
    $ids_subdepartamentos = $subdepartamentos ? implode(',', $subdepartamentos) : 'NULL';
    $sql_usuarios = "
        SELECT id, usuario FROM usuarios 
        WHERE id != {$_SESSION['id']} AND (
            rol = 'capturista'
            OR (rol = 'encargado' AND departamento_id IN ($ids_subdepartamentos))
        )
    ";
    $usuarios = $conn->query($sql_usuarios)->fetch_all(MYSQLI_ASSOC);
} else {
    // Capturista no puede crear tareas
    $usuarios = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tareas</title>
    <link rel="stylesheet" href="tareas3.css">
</head>
<body>
    <div class="container">
        <a href="../dashboard.php" class="back-button">← Volver al panel</a>
        
        <!-- Mensajes de éxito o error -->
        <?php if (isset($_GET['success'])): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        <!-- Formulario de creación (igual que antes) -->
        <?php if ($_SESSION['rol'] != 'capturista'): ?>
            <!-- Formulario para crear tarea -->
            <div class="form-container">
                <h2>Crear Nueva Tarea</h2>
                <form action="guardar_tarea.php" method="POST">
    <div class="form-group">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required>
    </div>
    
    <div class="form-group">
        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" required></textarea>
    </div>
    
    <div class="form-group">
        <label for="usuario_destino_id">Asignar a:</label>
        <select id="usuario_destino_id" name="usuario_destino_id" required>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['usuario']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <!-- NUEVO: Departamento -->
<div class="form-group">
    <label for="departamento_destino_id">Departamento:</label>
    <select name="departamento_destino_id" required>
        <option value="">Seleccione un departamento</option>
        <?php
        if ($_SESSION["rol"] == "admin") {
            // Admin ve todos los departamentos
            $dept_result = $conn->query("SELECT id, nombre FROM departamentos");
            while ($dept = $dept_result->fetch_assoc()) {
                echo "<option value='{$dept['id']}'>{$dept['nombre']}</option>";
            }
        } elseif ($_SESSION["rol"] == "encargado") {
            // Encargado ve su departamento y subdepartamentos
            if (isset($_SESSION["departamento_id"])) {
                $mi_departamento = $_SESSION["departamento_id"];
                
                // Obtener subdepartamentos (usando la función que ya tienes)
                $departamentos_permitidos = obtenerSubdepartamentos($conn, $mi_departamento);
                $departamentos_permitidos[] = $mi_departamento; // Agregar su propio departamento
                
                if (!empty($departamentos_permitidos)) {
                    $ids_permitidos = implode(",", $departamentos_permitidos);
                    $dept_result = $conn->query("SELECT id, nombre FROM departamentos WHERE id IN ($ids_permitidos)");
                    
                    while ($dept = $dept_result->fetch_assoc()) {
                        echo "<option value='{$dept['id']}'>{$dept['nombre']}</option>";
                    }
                } else {
                    // Si no hay subdepartamentos, mostrar solo su departamento
                    $dept_result = $conn->query("SELECT id, nombre FROM departamentos WHERE id = $mi_departamento");
                    $dept = $dept_result->fetch_assoc();
                    if ($dept) {
                        echo "<option value='{$dept['id']}'>{$dept['nombre']}</option>";
                    }
                }
            }
        }
        ?>
    </select>
</div>
    
    <!-- NUEVO: Fecha límite -->
    <div class="form-group">
        <label for="fecha_limite">Fecha límite (opcional):</label>
        <input type="datetime-local" id="fecha_limite" name="fecha_limite">
    </div>
    
    <button type="submit">Crear Tarea</button>
</form>

            </div>
        <?php endif; ?>

        <h2>Lista de Tareas</h2>

        <!-- Listado de tareas con acciones -->
        <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>De</th>
                    <th>Para</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tareas as $tarea): ?>
                <tr>
                    <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                   <td title="<?= htmlspecialchars($tarea['descripcion']) ?>">
    <?= strlen($tarea['descripcion']) > 30 ? substr(htmlspecialchars($tarea['descripcion']), 0, 30).'...' : htmlspecialchars($tarea['descripcion']) ?>
                </td>
                    <td><?= htmlspecialchars($tarea['origen']) ?></td>
                    <td><?= htmlspecialchars($tarea['destino']) ?></td>
                    <td><?= date('d/m/Y', strtotime($tarea['fecha_creacion'])) ?></td>
                    <td><?= htmlspecialchars($tarea['estado']) ?></td>
                 <td class="acciones">
    <?php 
    $todos_botones = [];
    
    // Botones básicos de edición/eliminación
    // Solo puede editar/eliminar si es admin O (es el creador Y NO es la persona asignada)
    if ($_SESSION['rol'] == 'admin' || 
        ($_SESSION['rol'] != 'capturista' && $tarea['usuario_origen_id'] == $_SESSION['id'] && $tarea['usuario_destino_id'] != $_SESSION['id'])): 
        $todos_botones[] = '<a href="editar_tarea.php?id='.$tarea['id'].'" class="btn btn-editar">Editar</a>';
        $todos_botones[] = '<a href="eliminar_tarea.php?id='.$tarea['id'].'" class="btn btn-eliminar" onclick="return confirm(\'¿Eliminar esta tarea?\')">Eliminar</a>';
    endif;
    
    // ACCIONES DE GESTIÓN DE TAREA - Solo para el DUEÑO actual de la tarea
    if ($tarea['usuario_origen_id'] == $_SESSION['id']):
        // BOTÓN DEVOLVER - Disponible para todos los estados excepto completado y atrasada
        if (!in_array($tarea['estado'], ['completado', 'atrasada'])):
            $todos_botones[] = '<a href="volver_pedir.php?id='.$tarea['id'].'" class="btn btn-warning" onclick="return confirm(\'¿Devolver para cambios?\')">Devolver</a>';
        endif;
        
        // ACCIONES CUANDO ESTÁ EN PROGRESO
        if ($tarea['estado'] == 'en_progreso' && $tarea['proceso_relacionado_id']):
            $todos_botones[] = '<a href="../procesos/ver_proceso.php?id='.$tarea['proceso_relacionado_id'].'" class="btn btn-ver">Ver Proceso</a>';
            
            // BOTÓN AUTORIZAR - Diferente comportamiento según el rol
            if ($_SESSION["rol"] == "admin"):
                $todos_botones[] = '<a href="autorizar_tarea.php?id='.$tarea['id'].'" class="btn btn-success" onclick="return confirm(\'¿Autorizar definitivamente? Se completará el proceso y se eliminará la tarea.\')">Autorizar Final</a>';
            elseif ($_SESSION["rol"] == "encargado"):
                $todos_botones[] = '<a href="autorizar_tarea.php?id='.$tarea['id'].'" class="btn btn-success" onclick="return confirm(\'¿Enviar al administrador para autorización final?\')">Enviar a Admin</a>';
            endif;
            
            $todos_botones[] = '<a href="rechazar_tarea.php?id='.$tarea['id'].'" class="btn btn-danger" onclick="return confirm(\'¿Rechazar esta tarea?\')">Rechazar</a>';
        endif;
        
        // ACCIONES CUANDO ESTÁ AUTORIZADO POR ENCARGADO
        if ($tarea['estado'] == 'autorizado_encargado'):
            $todos_botones[] = '<a href="../procesos/ver_proceso.php?id='.$tarea['proceso_relacionado_id'].'" class="btn btn-ver">Ver Proceso</a>';
            $todos_botones[] = '<a href="autorizar_tarea.php?id='.$tarea['id'].'" class="btn btn-success" onclick="return confirm(\'¿Autorizar definitivamente? Se completará el proceso y se eliminará la tarea.\')">Autorizar Final</a>';
            $todos_botones[] = '<a href="rechazar_tarea.php?id='.$tarea['id'].'" class="btn btn-danger" onclick="return confirm(\'¿Rechazar esta tarea?\')">Rechazar</a>';
        endif;
    endif;

    // BOTÓN INICIAR/CONTINUAR PROCESO - Solo para el usuario asignado
    if ($tarea['usuario_destino_id'] == $_SESSION['id']):
        // Si hay proceso asociado, siempre mostrar botón "Ver Proceso"
        if (!empty($tarea['proceso_relacionado_id'])):
            $todos_botones[] = '<a href="../procesos/ver_proceso.php?id='.$tarea['proceso_relacionado_id'].'" class="btn btn-ver">Ver Proceso</a>';
        endif;
        
        if (in_array($tarea['estado'], ['pendiente', 'atrasada', 'en_correccion']) && empty($tarea['proceso_relacionado_id'])):
            $todos_botones[] = '<a href="../procesos/crear.php?tarea_id='.$tarea['id'].'" class="btn btn-completar">Iniciar Proceso</a>';
        elseif ($tarea['estado'] == 'en_progreso' && !empty($tarea['proceso_relacionado_id'])):
            $todos_botones[] = '<a href="../procesos/editar_proceso.php?id='.$tarea['proceso_relacionado_id'].'" class="btn btn-completar">Continuar Proceso</a>';
        elseif (in_array($tarea['estado'], ['atrasada', 'en_correccion']) && !empty($tarea['proceso_relacionado_id'])):
            $todos_botones[] = '<a href="../procesos/corregir_proceso.php?id='.$tarea['proceso_relacionado_id'].'" class="btn btn-completar">Corregir Proceso</a>';
        elseif ($tarea['estado'] == 'autorizado_encargado'):
            $todos_botones[] = '<span class="text-info">Esperando autorización del administrador</span>';
        endif;
    endif;
    
    // Dividir botones en dos filas: 3 botones máximo por fila
    $botones_fila1 = array_slice($todos_botones, 0, 3);
    $botones_fila2 = array_slice($todos_botones, 3, 3);
    
    // Mostrar primera fila de botones
    if (!empty($botones_fila1)): ?>
        <div class="botones-fila-1">
            <?php foreach ($botones_fila1 as $boton): ?>
                <?= $boton ?>
            <?php endforeach; ?>
        </div>
    <?php endif;
    
    // Mostrar segunda fila de botones
    if (!empty($botones_fila2)): ?>
        <div class="botones-fila-2">
            <?php foreach ($botones_fila2 as $boton): ?>
                <?= $boton ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</td>


                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>