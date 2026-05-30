<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: ../index.php");
    exit();
}

include '../conexion.php';

// Verificar el rol del usuario
$es_capturista = ($_SESSION["rol"] == 'capturista');

// Obtener todos los departamentos para consulta 
$departamentos = [];
$res = $conn->query("SELECT id, nombre, parent_id FROM departamentos");
while ($row = $res->fetch_assoc()) {
    $departamentos[$row['id']] = $row;
}

// Obtener todos los departamentos para el <select> de padres
$sql = "SELECT id, nombre, parent_id FROM departamentos";
$result = $conn->query($sql);
$todosDepartamentos = [];
while ($row = $result->fetch_assoc()) {
    $todosDepartamentos[] = $row;
}

// Función para mostrar jerarquía
function mostrarJerarquia($todos, $parent_id = null, $nivel = 0, $es_capturista = false, $departamentosPermitidos = []) {
    foreach ($todos as $id => $dept) {
        if ($dept['parent_id'] == $parent_id) {
            $indent = str_repeat("↳ ", $nivel);
            echo "<tr>
                    <td>{$indent}" . htmlspecialchars($dept['nombre']) . "</td>
                    <td>" . ($dept['parent_id'] ? htmlspecialchars($todos[$dept['parent_id']]['nombre']) : '---') . "</td>
                    <td class='actions'>";
            
            if ($es_capturista) {
                echo "<span class='text-muted'>Solo lectura</span>";
            } else {
                // Admin puede todo, encargado solo los permitidos
                if (
                    $_SESSION["rol"] == "admin" ||
                    ($_SESSION["rol"] == "encargado" && in_array($dept['id'], $departamentosPermitidos))
                ) {
                    echo "<a href='editar_departamento.php?id={$dept['id']}'>✏️ Editar</a> | 
                          <a href='eliminar_departamento.php?id={$dept['id']}' onclick='return confirm(\"¿Eliminar este departamento?\")'>🗑️ Eliminar</a>";
                } else {
                    echo "<span class='text-muted'>Sin permisos</span>";
                }
            }
            
            echo "</td></tr>";
            mostrarJerarquia($todos, $id, $nivel + 1, $es_capturista, $departamentosPermitidos);
        }
    }
}

// Guardar nuevo departamento/subdepartamento (solo si no es capturista)
if (!$es_capturista && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nombre_departamento"])) {
    $nombre = trim($_POST["nombre_departamento"]);
    $parent_id = !empty($_POST["parent_id"]) ? $_POST["parent_id"] : null;

    if ($nombre !== "") {
        $stmt = $conn->prepare("INSERT INTO departamentos (nombre, parent_id) VALUES (?, ?)");
        $stmt->bind_param("si", $nombre, $parent_id);
        $stmt->execute();
        $stmt->close();
        header("Location: departamentos.php");
        exit();
    }
}

// Obtener departamentos permitidos para encargado
$departamentosPermitidos = [];
if ($_SESSION["rol"] == "encargado") {
    $mi_departamento = $_SESSION["departamento_id"];
    // Función para obtener todos los subdepartamentos
    function obtenerSubdepartamentos($todos, $parent_id) {
        $subs = [];
        foreach ($todos as $d) {
            if ($d['parent_id'] == $parent_id) {
                $subs[] = $d['id'];
                $subs = array_merge($subs, obtenerSubdepartamentos($todos, $d['id']));
            }
        }
        return $subs;
    }
    $departamentosPermitidos = obtenerSubdepartamentos($todosDepartamentos, $mi_departamento);
    $departamentosPermitidos[] = $mi_departamento;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Departamentos</title>
    <link rel="stylesheet" href="departamento.css">
</head>
<body>
    <div class="container">
        <a href="../dashboard.php" class="back-button">← Volver al panel</a>
     
        <h2>Nuevo departamento</h2>

    <?php if ($es_capturista): ?>
        <div class="alert-info">
            <strong>Nota:</strong> Usted tiene permisos de solo lectura como Capturista.
        </div>
    <?php endif; ?>

 <!-- Formulario para agregar (solo visible si no es capturista) -->
<?php if (!$es_capturista): ?>
<form method="POST">
    <label>Nombre del Departamento:</label>
    <input type="text" name="nombre_departamento" required>
    
    <label>Departamento Padre <?= $_SESSION["rol"] == "encargado" ? "(obligatorio)" : "(opcional)" ?>:</label>
    <select name="parent_id" <?= $_SESSION["rol"] == "encargado" ? "required" : "" ?>>
        <?php if ($_SESSION["rol"] == "admin"): ?>
            <option value="">-- Ninguno --</option>
        <?php endif; ?>
        
        <?php foreach ($todosDepartamentos as $d): ?>
            <?php
            // Admin ve todos, encargado solo los permitidos
            if (
                $_SESSION["rol"] == "admin" ||
                ($_SESSION["rol"] == "encargado" && in_array($d['id'], $departamentosPermitidos))
            ):
            ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
    
    <?php if ($_SESSION["rol"] == "encargado"): ?>
        <small style="color: #666;">Solo puedes crear subdepartamentos dentro de tu jerarquía</small>
    <?php endif; ?>
    
    <button type="submit">Guardar</button>
</form>
<?php endif; ?>
<h2>Lista de departamentos</h2>
    <!-- Tabla -->
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Dep. Padre</th>
                <th class="actions">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php mostrarJerarquia($departamentos, null, 0, $es_capturista, $departamentosPermitidos); ?>
        </tbody>
    </table>
</body>
</html>