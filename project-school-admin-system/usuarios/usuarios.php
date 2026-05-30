<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

include '../conexion.php';

// Configuración de ordenamiento
$orden_usuario_col = $_GET['orden_usuario_col'] ?? 'usuario';
$orden_usuario_dir = $_GET['orden_usuario_dir'] ?? 'asc';
$nuevo_orden_usuario_dir = $orden_usuario_dir === 'asc' ? 'desc' : 'asc';

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="usuario.css">
</head>
<body>
    <div class="container">
        <a href="../dashboard.php" class="back-button">← Volver al panel</a>

<?php
// Mostrar formulario solo si no es capturista
if ($_SESSION["rol"] == "admin" || $_SESSION["rol"] == "encargado") :
?>
    <h2>Agregar Nuevo Usuario</h2>
    <form action="guardar_usuario.php" method="POST">
        <label for="usuario">Usuario:</label>
        <input type="text" name="usuario" required>

        <label for="contraseña">Contraseña:</label>
        <input type="password" name="contraseña" required>

        <label for="rol">Rol:</label>
        <select name="rol" required>
            <?php if ($_SESSION["rol"] == "admin"): ?>
                <option value="encargado">Encargado</option>
                <option value="admin">Administrador</option>
                <option value="capturista">Capturista</option>
            <?php elseif ($_SESSION["rol"] == "encargado"): ?>
                <option value="capturista">Capturista</option>
                <option value="encargado">Encargado</option>
            <?php endif; ?>
        </select>

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
                    $dept_result = $conn->query("SELECT id, nombre FROM departamentos WHERE parent_id = '$mi_departamento'");
                    while ($dept = $dept_result->fetch_assoc()) {
                        echo "<option value='{$dept['id']}'>{$dept['nombre']}</option>";
                    }
                } else {
                    echo '<option value="">No tienes departamento asignado</option>';
                }
            }
            ?>
        </select>

        <button type="submit">Guardar Usuario</button>
    </form>
<?php endif; ?>

<h2>Lista de Usuarios</h2>
<table border="1">
    <tr>
        <th>
            Usuario
            <a href="?orden_usuario_col=usuario&orden_usuario_dir=<?php echo $nuevo_orden_usuario_dir; ?>" style="text-decoration: none;">
                <?php echo $orden_usuario_col == 'usuario' ? ($orden_usuario_dir === 'asc' ? '🔼' : '🔽') : '↕'; ?>
            </a>
        </th>
        <th>
            Rol
            <a href="?orden_usuario_col=rol&orden_usuario_dir=<?php echo $nuevo_orden_usuario_dir; ?>" style="text-decoration: none;">
                <?php echo $orden_usuario_col == 'rol' ? ($orden_usuario_dir === 'asc' ? '🔼' : '🔽') : '↕'; ?>
            </a>
        </th>
        <th>
            Departamento
            <a href="?orden_usuario_col=departamento_nombre&orden_usuario_dir=<?php echo $nuevo_orden_usuario_dir; ?>" style="text-decoration: none;">
                <?php echo $orden_usuario_col == 'departamento_nombre' ? ($orden_usuario_dir === 'asc' ? '🔼' : '🔽') : '↕'; ?>
            </a>
        </th>
        <th>Acciones</th>
    </tr>
    <?php
    $sql = "SELECT usuarios.*, departamentos.nombre AS departamento_nombre 
            FROM usuarios 
            JOIN departamentos ON usuarios.departamento_id = departamentos.id
            ORDER BY $orden_usuario_col $orden_usuario_dir";
    $result = $conn->query($sql);

    // Obtener permisos para encargado
    $mis_subdepartamentos = [];
    if ($_SESSION["rol"] == "encargado") {
        $mis_subdepartamentos = obtenerSubdepartamentos($conn, $_SESSION["departamento_id"]);
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $acciones = '';
            // ADMIN puede todo
            if ($_SESSION["rol"] == "admin") {
                if ($row['rol'] != 'admin') {
                    $acciones = "<a href='editar_usuario.php?id={$row['id']}'>✏️ Editar</a> | 
                                 <a href='eliminar_usuario.php?id={$row['id']}' onclick='return confirm(\"¿Seguro que deseas eliminar este usuario?\")'>🗑️ Eliminar</a>";
                } else {
                    $acciones = ""; // No mostrar acciones para otros administradores
                }
            }
            // ENCARGADO: reglas según departamento y rol
            elseif ($_SESSION["rol"] == "encargado") {
                $es_subdepartamento = in_array($row['departamento_id'], $mis_subdepartamentos);
                $es_mi_departamento = $row['departamento_id'] == $_SESSION["departamento_id"];
                if ($row['rol'] == 'admin') {
                    $acciones = '';
                } elseif ($es_subdepartamento && ($row['rol'] == 'capturista' || $row['rol'] == 'encargado')) {
                    $acciones = "<a href='editar_usuario.php?id={$row['id']}'>✏️ Editar</a> | 
                                 <a href='eliminar_usuario.php?id={$row['id']}' onclick='return confirm(\"¿Seguro que deseas eliminar este usuario?\")'>🗑️ Eliminar</a>";
                } elseif ($es_mi_departamento && $row['rol'] == 'capturista') {
                    $acciones = "<a href='editar_usuario.php?id={$row['id']}'>✏️ Editar</a> | 
                                 <a href='eliminar_usuario.php?id={$row['id']}' onclick='return confirm(\"¿Seguro que deseas eliminar este usuario?\")'>🗑️ Eliminar</a>";
                }
            }
            // CAPTURISTA: solo puede ver
            echo "<tr>
                    <td>{$row['usuario']}</td>
                    <td>{$row['rol']}</td>
                    <td>{$row['departamento_nombre']}</td>
                    <td>$acciones</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No hay usuarios registrados</td></tr>";
    }
    $conn->close();
    ?>
</table>