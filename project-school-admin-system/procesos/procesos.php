<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

include '../conexion.php';

$orden_procesos_col = $_GET['orden_procesos_col'] ?? 'fecha';
$orden_procesos_dir = $_GET['orden_procesos_dir'] ?? 'desc';
$nuevo_orden_procesos_dir = $orden_procesos_dir === 'asc' ? 'desc' : 'asc';

// Configuración de ordenamiento para PROCESOS
$orden_procesos_col = isset($_GET['orden_procesos_col']) ? $_GET['orden_procesos_col'] : 'fecha';
$orden_procesos_dir = isset($_GET['orden_procesos_dir']) ? $_GET['orden_procesos_dir'] : 'desc';
$nuevo_orden_procesos_dir = $orden_procesos_dir === 'asc' ? 'desc' : 'asc';

// Función para obtener subdepartamentos
function obtenerSubdepartamentos($conn, $departamento_id) {
    $subdepartamentos = [];
    $stmt = $conn->prepare("SELECT id FROM departamentos WHERE parent_id = ?");
    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($fila = $result->fetch_assoc()) {
        $subdepartamentos[] = $fila['id'];
        $subdepartamentos = array_merge($subdepartamentos, obtenerSubdepartamentos($conn, $fila['id']));
    }
    return $subdepartamentos;
}

// Obtener subdepartamentos hijos para el encargado
$mis_subdepartamentos = [];
if ($_SESSION["rol"] == "encargado") {
    $mis_subdepartamentos = obtenerSubdepartamentos($conn, $_SESSION["departamento_id"]);
}

// Obtener departamentos para el formulario
$departamentos = [];
$result_departamentos = $conn->query("SELECT id, nombre FROM departamentos");
if ($result_departamentos && $result_departamentos->num_rows > 0) {
    while ($depto = $result_departamentos->fetch_assoc()) {
        $departamentos[] = $depto;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Procesos</title>
    <link rel="stylesheet" href="procesos15.css">
</head>
<body>
    <div class="container">
        <a href="../dashboard.php" class="back-button">← Volver al panel</a>
        <h2>Nuevo Proceso Administrativo</h2>

<form action="guardar_proceso.php" method="POST">
    <label for="titulo">Título:</label>
    <input type="text" name="titulo" required>
    
    <label for="descripcion">Descripción:</label>
    <textarea name="descripcion" required></textarea>

    <label for="departamento_id">Departamento:</label>
<select name="departamento_id" required>
    <option value="">Seleccione un departamento</option>
    <?php
    if ($_SESSION["rol"] == "admin") {
        $dept_result = $conn->query("SELECT id, nombre FROM departamentos");
        while ($dept = $dept_result->fetch_assoc()) {
            echo "<option value='{$dept['id']}'>{$dept['nombre']}</option>";
        }
    } else if ($_SESSION["rol"] == "encargado") {
        $mi_departamento = $_SESSION["departamento_id"] ?? null;
        if ($mi_departamento) {
            // Propio departamento
            $dept_result = $conn->query("SELECT id, nombre FROM departamentos WHERE id = '$mi_departamento'");
            while ($dept = $dept_result->fetch_assoc()) {
                echo "<option value='{$dept['id']}'>{$dept['nombre']}</option>";
            }
            // Subdepartamentos 
            $subdepartamentos = obtenerSubdepartamentos($conn, $mi_departamento);
            if (!empty($subdepartamentos)) {
                $ids = implode(",", $subdepartamentos);
                $dept_result = $conn->query("SELECT id, nombre FROM departamentos WHERE id IN ($ids)");
                while ($dept = $dept_result->fetch_assoc()) {
                    echo "<option value='{$dept['id']}'>{$dept['nombre']}</option>";
                }
            }
        } else {
            echo '<option value="">No tienes departamento asignado</option>';
        }
    } else if ($_SESSION["rol"] == "capturista") {
        $mi_departamento = $_SESSION["departamento_id"] ?? null;
        if ($mi_departamento) {
            // Propio departamento
            $dept_result = $conn->query("SELECT id, nombre FROM departamentos WHERE id = '$mi_departamento'");
            while ($dept = $dept_result->fetch_assoc()) {
                echo "<option value='{$dept['id']}'>{$dept['nombre']}</option>";
            }
            // Subdepartamentos 
            $subdepartamentos = obtenerSubdepartamentos($conn, $mi_departamento);
            if (!empty($subdepartamentos)) {
                $ids = implode(",", $subdepartamentos);
                $dept_result = $conn->query("SELECT id, nombre FROM departamentos WHERE id IN ($ids)");
                while ($dept = $dept_result->fetch_assoc()) {
                    echo "<option value='{$dept['id']}'>{$dept['nombre']}</option>";
                }
            }
        } else {
            echo '<option value="">No tienes departamento asignado</option>';
        }
    }
    ?>
</select>

    
    <button type="submit">Guardar</button>
</form>



<!-- Formularios de descarga -->
<div style="margin-bottom: 20px; display: flex; gap: 20px; flex-wrap: wrap; align-items: center;">
    <form action="descargar_procesos.php" method="post">
        <button type="submit" class="download-btn download-all">
            📥 Descargar Todos los Procesos
        </button>
    </form>

    <form action="descargar_procesos_departamento.php" method="post" style="display: flex; align-items: center; gap: 10px;">
        <div style="position: relative;">
            <select name="departamento_id" required>
                <option value="" disabled selected>-- Seleccione Departamento --</option>
                <?php foreach ($departamentos as $depto): ?>
                    <option value="<?= htmlspecialchars($depto['id']) ?>">
                        <?= htmlspecialchars($depto['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;">▼</span>
        </div>
        
        <button type="submit" class="download-btn">
            📥 Descargar Procesos
        </button>
    </form>
</div>

<?php 
if (empty($departamentos)) {
    echo '<p style="color: red; margin-bottom: 10px;">No se encontraron departamentos en la base de datos.</p>';
}
?>

<h2>Procesos Guardados</h2>
<table border="1">
    <tr>
        <?php 
        $columnas_procesos = ['titulo', 'descripcion', 'departamento_nombre', 'usuario', 'fecha', 'estado'];
        foreach ($columnas_procesos as $columna) {
            $columna_orden = $columna === 'departamento_nombre' ? 'departamentos.nombre' : $columna;
            $icono = ($orden_procesos_col == $columna_orden) ? ($orden_procesos_dir === 'asc' ? '🔼' : '🔽') : '↕';
            echo "<th>
                    " . ucfirst(str_replace('_nombre', '', $columna)) . "
                    <a href='?orden_procesos_col=$columna_orden&orden_procesos_dir=$nuevo_orden_procesos_dir' style='text-decoration: none;'>$icono</a>
                  </th>";
        }
        ?>
        <th>Acciones</th>
    </tr>
    <?php
    $sql = "SELECT procesos.*, departamentos.nombre AS departamento_nombre, usuarios.rol AS rol_usuario
            FROM procesos
            JOIN departamentos ON procesos.departamento_id = departamentos.id
            JOIN usuarios ON procesos.usuario = usuarios.usuario
            ORDER BY $orden_procesos_col $orden_procesos_dir";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        $todas_acciones = [];

        // Botón "Ver Proceso" 
        $todas_acciones[] = "<a href='ver_proceso.php?id={$row['id']}'>Ver Proceso</a>";

        // Descargar
        $mostrar_descarga = ($row['estado'] != 'en_progreso');
        if ($mostrar_descarga) {
            $todas_acciones[] = "<a href='descargar_proceso.php?id={$row['id']}'>Descargar</a>";
        }

        // Editar y Eliminar (según permisos)
        $puede_editar = false;
        $puede_eliminar = false;

        if ($_SESSION["rol"] == "admin") {
            $puede_editar = $puede_eliminar = true;
        } elseif ($_SESSION["rol"] == "encargado") {
            if (
                $row['usuario'] == $_SESSION['usuario'] ||
                ($row['departamento_id'] == $_SESSION['departamento_id'] && $row['rol_usuario'] == 'capturista') ||
                (in_array($row['departamento_id'], $mis_subdepartamentos) && ($row['rol_usuario'] == 'capturista' || $row['rol_usuario'] == 'encargado'))
            ) {
                $puede_editar = $puede_eliminar = true;
            }
        } elseif ($_SESSION["rol"] == "capturista") {
            $mi_departamento = $_SESSION["departamento_id"];
            $mis_subdepartamentos = obtenerSubdepartamentos($conn, $mi_departamento);
            if (
                $row['usuario'] == $_SESSION['usuario'] ||
                (in_array($row['departamento_id'], $mis_subdepartamentos) && $row['rol_usuario'] == 'capturista')
            ) {
                $puede_editar = $puede_eliminar = true;
            }
        }

        if ($puede_editar) {
            $todas_acciones[] = "<a href='editar_proceso.php?id={$row['id']}'>Editar</a>";
        }
        if ($puede_eliminar && $row['estado'] != 'en_progreso') {
            $todas_acciones[] = "<a href='eliminar_proceso.php?id={$row['id']}' onclick='return confirm(\"¿Seguro que deseas eliminar este proceso?\")'>Eliminar</a>";
        }

        // Dividir acciones en dos filas: 2 botones máximo por fila
        $acciones_arriba = array_slice($todas_acciones, 0, 2);
        $acciones_abajo = array_slice($todas_acciones, 2, 2);

        echo "<tr>
        <td>{$row['titulo']}</td>
        <td class=\"tooltip-cell\">" . 
            htmlspecialchars(substr($row['descripcion'], 0, 30)) . (strlen($row['descripcion']) > 30 ? '...' : '') . 
            "<span class=\"tooltip\">" . htmlspecialchars($row['descripcion']) . "</span>" .
        "</td>
        <td>{$row['departamento_nombre']}</td>
        <td>{$row['usuario']}</td>
        <td>{$row['fecha']}</td>
        <td>
            <span class='" . ($row['estado'] == 'completo' ? 'estado-completo' : 'estado-progreso') . "'>
                " . htmlspecialchars($row['estado']) . "
            </span>
        </td>
        <td>
            <div class='acciones-fila'>" . implode(' ', $acciones_arriba) . "</div>
            <div class='acciones-fila'>" . implode(' ', $acciones_abajo) . "</div>
        </td>
      </tr>";
    }
    $conn->close();
    ?>
</table>


