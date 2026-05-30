<?php
session_start();
if (!isset($_SESSION["RPE"])) {
    header("Location: vistalogin.php");
    exit();
}

require_once '../includes/conexion.php';

// Verificar si el usuario es administrador
$tipo_usuario = $_SESSION["rol"] ?? 'empleado';
if ($tipo_usuario !== 'administrador') {
    header("Location: gestion_equipos.php");
    exit();
}

// Obtener el número de serie del equipo a editar
if (!isset($_GET['serie']) || empty($_GET['serie'])) {
    header("Location: gestion_equipos.php");
    exit();
}

$numero_serie_original = $conn->real_escape_string($_GET['serie']);

// Obtener datos del equipo
$query = "SELECT * FROM equipos WHERE Numero_serie = '$numero_serie_original'";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header("Location: gestion_equipos.php");
    exit();
}

$equipo = $result->fetch_assoc();

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanitizar los datos
    $numero_serie_nuevo = $conn->real_escape_string($_POST['numero_serie']);
    $estado = $conn->real_escape_string($_POST['estado']);
    $departamento = $conn->real_escape_string($_POST['departamento']);
    $division = $conn->real_escape_string($_POST['division']);
    $rpe_responsable = $conn->real_escape_string($_POST['rpe_responsable']);
    $nombre_responsable = $conn->real_escape_string($_POST['nombre_responsable']);
    $tipo = $conn->real_escape_string($_POST['tipo']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $modelo = $conn->real_escape_string($_POST['modelo'] ?? '');
    $sistema_operativo = $conn->real_escape_string($_POST['sistema_operativo'] ?? '');
    $velocidad = $conn->real_escape_string($_POST['velocidad'] ?? '');
    $requiere_mantenimiento = $conn->real_escape_string($_POST['requiere_mantenimiento']);
    $fallas = $conn->real_escape_string($_POST['fallas'] ?? '');
    $notas = $conn->real_escape_string($_POST['notas'] ?? '');

    // Validar campos obligatorios
    if (empty($numero_serie_nuevo) || empty($estado) || empty($departamento) || empty($division) || empty($rpe_responsable) || empty($nombre_responsable) || empty($tipo) || empty($marca) || empty($requiere_mantenimiento)) {
        $error = "Por favor complete todos los campos obligatorios.";
    } else {
        // Verificar si el nuevo número de serie ya existe (si se cambió)
        if ($numero_serie_nuevo !== $numero_serie_original) {
            $check_query = "SELECT COUNT(*) as count FROM equipos WHERE Numero_serie = '$numero_serie_nuevo'";
            $check_result = $conn->query($check_query);
            $count = $check_result->fetch_assoc()['count'];
            
            if ($count > 0) {
                $error = "El número de serie '$numero_serie_nuevo' ya existe en el sistema.";
            }
        }

        if (!isset($error)) {
            // Actualizar en la base de datos
            $sql = "UPDATE equipos SET
                    Numero_serie = '$numero_serie_nuevo',
                    Estado = '$estado',
                    Departamento = '$departamento',
                    Division = '$division',
                    RPE_responsable = '$rpe_responsable',
                    Nombre_responsable = '$nombre_responsable',
                    Tipo = '$tipo',
                    Marca = '$marca',
                    Modelo = '$modelo',
                    Sistema_operativo = '$sistema_operativo',
                    Velocidad = '$velocidad',
                    Requiere_mantenimiento = '$requiere_mantenimiento',
                    Fallas = '$fallas',
                    Notas = '$notas'
                    WHERE Numero_serie = '$numero_serie_original'";

            if ($conn->query($sql) === TRUE) {
                $success = "Equipo actualizado correctamente.";
                
                // Si cambió el número de serie, actualizar la variable para mostrar
                if ($numero_serie_nuevo !== $numero_serie_original) {
                    $numero_serie_original = $numero_serie_nuevo;
                }
                
                // Actualizar datos del equipo para mostrar en el formulario
                $equipo = array_merge($equipo, [
                    'Numero_serie' => $numero_serie_nuevo,
                    'Estado' => $estado,
                    'Departamento' => $departamento,
                    'Division' => $division,
                    'RPE_responsable' => $rpe_responsable,
                    'Nombre_responsable' => $nombre_responsable,
                    'Tipo' => $tipo,
                    'Marca' => $marca,
                    'Modelo' => $modelo,
                    'Sistema_operativo' => $sistema_operativo,
                    'Velocidad' => $velocidad,
                    'Requiere_mantenimiento' => $requiere_mantenimiento,
                    'Fallas' => $fallas,
                    'Notas' => $notas
                ]);
            } else {
                $error = "Error al actualizar el equipo: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipo - Sistema de Inventarios | CFE</title>
    <link rel="stylesheet" href="gestion_equipos.css">
    <link rel="stylesheet" href="agregar_equipo.css">
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <img src="../img/logo_cfe.png" alt="Logo CFE" class="logo">
                <h1>Sistema de Inventarios Internos</h1>
            </div>
            <div class="user-info">
                <a href="gestion_equipos.php" class="nav-btn">Volver a Equipos</a>
                <a href="../includes/logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="content">
            <div class="page-header">
                <h2>Editar Equipo</h2>
                <div class="actions">
                    <span class="numero-serie-display">Editando equipo</span>
                </div>
            </div>

            <!-- Información del creador -->
            <div class="creator-info">
                <p><strong>Información de registro:</strong></p>
                <p>Creado por: <?php echo htmlspecialchars($equipo['RPE_creador']); ?> 
                   el <?php echo date('d/m/Y H:i', strtotime($equipo['Fecha_creacion'])); ?></p>
                <?php 
                // Verificar si existe la columna Fecha_actualizacion y si tiene un valor válido
                $fecha_actualizacion = isset($equipo['Fecha_actualizacion']) && !empty($equipo['Fecha_actualizacion']) && $equipo['Fecha_actualizacion'] != '0000-00-00 00:00:00' 
                    ? $equipo['Fecha_actualizacion'] 
                    : null;
                
                if ($fecha_actualizacion && $fecha_actualizacion != $equipo['Fecha_creacion']): 
                ?>
                <p>Última actualización: <?php echo date('d/m/Y H:i', strtotime($fecha_actualizacion)); ?></p>
                <?php endif; ?>
            </div>

            <!-- Mensajes de éxito/error -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <p>Redirigiendo a la gestión de equipos en 3 segundos...</p>
                </div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'gestion_equipos.php';
                    }, 3000);
                </script>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="form-container">
                <form method="POST" action="">
                    <!-- Primera fila -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="numero_serie" class="required">Número de Serie</label>
                            <input type="text" id="numero_serie" name="numero_serie" 
                                   value="<?php echo htmlspecialchars($equipo['Numero_serie']); ?>" 
                                   required maxlength="50" placeholder="Ingrese el número de serie">
                            <small class="optional-text">Puede modificar el número de serie si es necesario</small>
                        </div>
                        <div class="form-group">
                            <label for="estado" class="required">Estado</label>
                            <select id="estado" name="estado" required>
                                <option value="">Seleccionar estado</option>
                                <option value="Disponible" <?php echo $equipo['Estado'] == 'Disponible' ? 'selected' : ''; ?>>Disponible</option>
                                <option value="En_uso" <?php echo $equipo['Estado'] == 'En_uso' ? 'selected' : ''; ?>>En uso</option>
                                <option value="En_mantenimiento" <?php echo $equipo['Estado'] == 'En_mantenimiento' ? 'selected' : ''; ?>>En mantenimiento</option>
                                <option value="Descompuesto" <?php echo $equipo['Estado'] == 'Descompuesto' ? 'selected' : ''; ?>>Descompuesto</option>
                            </select>
                        </div>
                    </div>

                    <!-- Segunda fila -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="departamento" class="required">Departamento</label>
                            <input type="text" id="departamento" name="departamento" 
                                   value="<?php echo htmlspecialchars($equipo['Departamento']); ?>" 
                                   required maxlength="100" placeholder="Ingrese el departamento">
                        </div>
                        <div class="form-group">
                            <label for="division" class="required">División</label>
                            <input type="text" id="division" name="division" 
                                   value="<?php echo htmlspecialchars($equipo['Division']); ?>" 
                                   required maxlength="100" placeholder="Ingrese la división">
                        </div>
                    </div>

                    <!-- Tercera fila -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rpe_responsable" class="required">RPE Responsable</label>
                            <input type="text" id="rpe_responsable" name="rpe_responsable" 
                                   value="<?php echo htmlspecialchars($equipo['RPE_responsable']); ?>" 
                                   required maxlength="20" placeholder="Ingrese el RPE del responsable">
                        </div>
                        <div class="form-group">
                            <label for="nombre_responsable" class="required">Nombre Responsable</label>
                            <input type="text" id="nombre_responsable" name="nombre_responsable" 
                                   value="<?php echo htmlspecialchars($equipo['Nombre_responsable']); ?>" 
                                   required maxlength="100" placeholder="Ingrese el nombre del responsable">
                        </div>
                    </div>

                    <!-- Cuarta fila -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo" class="required">Tipo de Equipo</label>
                            <input type="text" id="tipo" name="tipo" 
                                   value="<?php echo htmlspecialchars($equipo['Tipo']); ?>" 
                                   required maxlength="50" placeholder="Ej: Laptop, Tableta, Computadora de escritorio">
                        </div>
                        <div class="form-group">
                            <label for="marca" class="required">Marca</label>
                            <input type="text" id="marca" name="marca" 
                                   value="<?php echo htmlspecialchars($equipo['Marca']); ?>" 
                                   required maxlength="50" placeholder="Ingrese la marca del equipo">
                        </div>
                    </div>

                    <!-- Quinta fila -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modelo" class="required">Modelo</label>
                            <input type="text" id="modelo" name="modelo" 
                                   value="<?php echo htmlspecialchars($equipo['Modelo']); ?>" 
                                   required maxlength="50" placeholder="Ingrese el modelo del equipo">
                        </div>
                        <div class="form-group">
                            <label for="requiere_mantenimiento" class="required">Requiere Mantenimiento</label>
                            <select id="requiere_mantenimiento" name="requiere_mantenimiento" required>
                                <option value="">Seleccionar</option>
                                <option value="Si" <?php echo $equipo['Requiere_mantenimiento'] == 'Si' ? 'selected' : ''; ?>>Sí</option>
                                <option value="No" <?php echo $equipo['Requiere_mantenimiento'] == 'No' ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sexta fila - Campos opcionales -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sistema_operativo">Sistema Operativo</label>
                            <input type="text" id="sistema_operativo" name="sistema_operativo" 
                                   value="<?php echo htmlspecialchars($equipo['Sistema_operativo']); ?>" 
                                   maxlength="50" placeholder="Ej: Windows 10, Android, Linux">
                        </div>
                        <div class="form-group">
                            <label for="velocidad">Velocidad</label>
                            <input type="text" id="velocidad" name="velocidad" 
                                   value="<?php echo htmlspecialchars($equipo['Velocidad']); ?>" 
                                   maxlength="50" placeholder="Ej: 2.4GHz, Mayor a 3GHz">
                        </div>
                    </div>

                    <!-- Campos de texto largos opcionales -->
                    <div class="form-group">
                        <label for="fallas">Fallas Reportadas</label>
                        <textarea id="fallas" name="fallas" maxlength="500" 
                                  placeholder="Describa las fallas del equipo si las hay..."><?php echo htmlspecialchars($equipo['Fallas']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="notas">Notas Adicionales</label>
                        <textarea id="notas" name="notas" maxlength="500" 
                                  placeholder="Agregue cualquier nota adicional sobre el equipo..."><?php echo htmlspecialchars($equipo['Notas']); ?></textarea>
                    </div>

                    <!-- Botones de acción -->
                    <div class="form-actions">
                        <a href="gestion_equipos.php" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Actualizar Equipo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
?>