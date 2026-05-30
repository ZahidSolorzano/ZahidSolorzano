<?php
session_start();
if (!isset($_SESSION["RPE"])) {
    header("Location: vistalogin.php");
    exit();
}

require_once '../includes/conexion.php';

// Obtener datos del usuario actual
$rpe_usuario = $_SESSION["RPE"];

// Obtener información completa del usuario desde la base de datos
$query_usuario = "SELECT nombre, rol, departamento, division FROM usuarios WHERE RPE = ?";
$stmt_usuario = $conn->prepare($query_usuario);
$stmt_usuario->bind_param('s', $rpe_usuario);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

if ($result_usuario->num_rows === 0) {
    // Usuario no encontrado, cerrar sesión
    session_destroy();
    header("Location: vistalogin.php");
    exit();
}

$usuario_data = $result_usuario->fetch_assoc();

// Asignar variables
$nombre_usuario = $usuario_data['nombre'];
$rol_usuario = $usuario_data['rol'];
$departamento_usuario = $usuario_data['departamento'];
$division_usuario = $usuario_data['division'];

// Actualizar la sesión con los datos obtenidos
$_SESSION["nombre"] = $nombre_usuario;
$_SESSION["rol"] = $rol_usuario;
$_SESSION["departamento"] = $departamento_usuario;
$_SESSION["division"] = $division_usuario;

// Obtener equipos disponibles para préstamo
$query_equipos = "SELECT Numero_serie, Tipo, Marca, Modelo, Nombre_responsable, 
                         RPE_responsable, Departamento, Division 
                  FROM equipos 
                  WHERE Estado = 'Disponible' 
                  ORDER BY Tipo, Marca, Modelo";
$result_equipos = $conn->query($query_equipos);
$equipos_disponibles = $result_equipos->fetch_all(MYSQLI_ASSOC);

// Obtener equipos donde el usuario es responsable
$query_mis_equipos = "SELECT Numero_serie, Tipo, Marca, Modelo, Departamento, Division 
                      FROM equipos 
                      WHERE RPE_responsable = ? AND Estado = 'Disponible' 
                      ORDER BY Tipo, Marca, Modelo";
$stmt_mis_equipos = $conn->prepare($query_mis_equipos);
$stmt_mis_equipos->bind_param('s', $rpe_usuario);
$stmt_mis_equipos->execute();
$result_mis_equipos = $stmt_mis_equipos->get_result();
$mis_equipos = $result_mis_equipos->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Préstamo - Sistema de Inventarios</title>
    <link rel="stylesheet" href="agregar_prestamo.css">
    
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <header class="header">
            <div class="logo-section">
                <img src="../img/logo_cfe.png" alt="Logo CFE" class="logo">
                <h1>Sistema de Gestión de Inventarios</h1>
            </div>
            <div class="user-info">
                <a href="../vistas/dashboard.php" class="nav-btn">Dashboard</a>
                <a href="gestion_prestamos.php" class="nav-btn">Préstamos</a>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </header>

        <!-- Contenido Principal -->
        <div class="content">
            <!-- Mostrar mensajes de éxito/error -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>
            <!-- Encabezado de página -->
            <div class="page-header">
                <h2>Nuevo Préstamo</h2>
                
            </div>

            <!-- Contenedor de formularios -->
            <div class="form-container">
                <!-- Formulario 1: Solicitar Equipo -->
<div class="form-section">
    <h3>📋 Solicitar un Equipo</h3>
    <p style="margin-bottom: 20px; color: #666;">Solicita un equipo que necesites usar</p>
    
    <form action="../includes/procesar_solicitud_prestamo.php" method="POST">
        <!-- Información del solicitante (automática) -->
    <div class="form-group">
        <label class="form-label">Solicitante</label>
        <div class="equipo-info">
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre_usuario); ?></p>
            <p><strong>RPE:</strong> <?php echo htmlspecialchars($rpe_usuario); ?></p>
            <p><strong>Departamento:</strong> <?php echo htmlspecialchars($departamento_usuario); ?></p>
            <p><strong>División:</strong> <?php echo htmlspecialchars($division_usuario); ?></p>
        </div>
    </div>

        <!-- Selección de equipo -->
        <div class="form-group">
            <label class="form-label">Equipo a Solicitar <span class="required">*</span></label>
            <select name="numero_serie_equipo" class="form-select" required 
                    onchange="mostrarInfoEquipo(this)">
                <option value="">Selecciona un equipo...</option>
                <?php foreach ($equipos_disponibles as $equipo): ?>
                    <option value="<?php echo $equipo['Numero_serie']; ?>"
                            data-departamento="<?php echo htmlspecialchars($equipo['departamento'] ?? ''); ?>"
                            data-division="<?php echo htmlspecialchars($equipo['division'] ?? ''); ?>"
                            data-responsable="<?php echo htmlspecialchars($equipo['RPE_responsable'] ?? ''); ?>"
                            data-nombre-responsable="<?php echo htmlspecialchars($equipo['Nombre_responsable'] ?? ''); ?>">
                        <?php echo htmlspecialchars($equipo['Tipo'] . ' ' . $equipo['Marca'] . ' ' . $equipo['Modelo'] . ' (Serie: ' . $equipo['Numero_serie'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <!-- Información del equipo seleccionado -->
            <div id="info-equipo" class="equipo-info" style="display: none; margin-top: 15px;">
                <p><strong>Responsable:</strong> <span id="nombre-responsable"></span></p>
                <p><strong>Departamento:</strong> <span id="departamento-equipo"></span></p>
                <p><strong>División:</strong> <span id="division-equipo"></span></p>
            </div>
            
            <?php if (empty($equipos_disponibles)): ?>
                <div class="no-equipos">
                    <h4>No hay equipos disponibles</h4>
                    <p>Todos los equipos están actualmente en uso o en mantenimiento.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Campo oculto para el responsable del equipo -->
        <input type="hidden" name="rpe_responsable" id="rpe_responsable" value="<?php echo htmlspecialchars($rpe_usuario); ?>">
                       

                        <!-- Fechas del préstamo -->
                        <div class="form-group">
                            <label class="form-label">Fecha de Inicio <span class="required">*</span></label>
                            <input type="datetime-local" name="fecha_inicio" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fecha de Fin <span class="required">*</span></label>
                            <input type="datetime-local" name="fecha_fin" class="form-input" required>
                        </div>

                      <!-- observaciones -->
                <div class="form-group">
    <label class="form-label">Observaciones</label>
    <textarea name="observaciones" class="form-textarea" placeholder="Describe para qué necesitas el equipo (opcional)..."></textarea>
</div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Solicitar Préstamo</button>
                            <a href="gestion_prestamos.php" class="btn-cancel">Cancelar</a>
                        </div>
                    </form>
                </div>

                <!-- Formulario 2: Prestar mi Equipo -->
<div class="form-section">
    <h3>🔄 Prestar mi Equipo</h3>
    <p style="margin-bottom: 20px; color: #666;">Presta uno de los equipos de los que eres responsable</p>
    
    <form action="../includes/procesar_prestamo_directo.php" method="POST">
        <!-- Información del responsable (automática) -->
    <div class="form-group">
        <label class="form-label">Responsable del Equipo</label>
        <div class="equipo-info">
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre_usuario); ?></p>
            <p><strong>RPE:</strong> <?php echo htmlspecialchars($rpe_usuario); ?></p>
            <p><strong>Departamento:</strong> <span id="departamento-responsable-display"><?php echo htmlspecialchars($departamento_usuario); ?></span></p>
            <p><strong>División:</strong> <span id="division-responsable-display"><?php echo htmlspecialchars($division_usuario); ?></span></p>
        </div>
    </div>

        <!-- Selección de mi equipo -->
        <div class="form-group">
            <label class="form-label">Mi Equipo a Prestar <span class="required">*</span></label>
            <select name="numero_serie_equipo" class="form-select" required 
                    onchange="actualizarDepartamentoDivision(this)">
                <option value="">Selecciona tu equipo...</option>
                <?php foreach ($mis_equipos as $equipo): ?>
                    <option value="<?php echo $equipo['Numero_serie']; ?>"
                            data-departamento="<?php echo htmlspecialchars($equipo['departamento'] ?? ''); ?>"
                            data-division="<?php echo htmlspecialchars($equipo['division'] ?? ''); ?>">
                        <?php echo htmlspecialchars($equipo['Tipo'] . ' ' . $equipo['Marca'] . ' ' . $equipo['Modelo'] . ' (Serie: ' . $equipo['Numero_serie'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (empty($mis_equipos)): ?>
                <div class="no-equipos">
                    <h4>No tienes equipos disponibles</h4>
                    <p>No eres responsable de ningún equipo o todos están en uso.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Información del departamento y división del equipo (se actualiza automáticamente) -->
        <input type="hidden" name="departamento_equipo" id="departamento_equipo" value="<?php echo htmlspecialchars($departamento_usuario); ?>">
        <input type="hidden" name="division_equipo" id="division_equipo" value="<?php echo htmlspecialchars($division_usuario); ?>">

        <!-- Solo RPE del solicitante -->
        <div class="form-group">
            <label class="form-label">RPE del Solicitante <span class="required">*</span></label>
            <input type="text" name="rpe_solicitante" class="form-input" placeholder="Ingresa el RPE del solicitante" required>
            <small style="color: #666; font-size: 0.8rem;">El nombre se obtendrá automáticamente si el usuario está registrado</small>
        </div>

        <!-- Fechas del préstamo -->
        <div class="form-group">
            <label class="form-label">Fecha de Inicio <span class="required">*</span></label>
            <input type="datetime-local" name="fecha_inicio" class="form-input" required>
        </div>

        <div class="form-group">
            <label class="form-label">Fecha de Fin <span class="required">*</span></label>
            <input type="datetime-local" name="fecha_fin" class="form-input" required>
        </div>

        <!-- Observaciones (NO obligatorias) -->
        <div class="form-group">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-textarea" placeholder="Observaciones adicionales (opcional)"></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Realizar Préstamo</button>
            <a href="gestion_prestamos.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>
            </div>
        </div>
    </div>

    <script>
        // VALIDACIÓN DE FECHAS 
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const fechaInicio = form.querySelector('input[name="fecha_inicio"]');
        const fechaFin = form.querySelector('input[name="fecha_fin"]');
        
        if (fechaInicio && fechaFin) {
            // MÉTODO MANUAL: Construir el string exacto de la hora local
            const now = new Date();
            
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0'); // Meses son 0-11
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            // Formato requerido por HTML5: YYYY-MM-DDTHH:mm
            const todayStr = `${year}-${month}-${day}T${hours}:${minutes}`;
            
            // Establecer el mínimo
            fechaInicio.min = todayStr;
            
            // Validar que fecha fin sea mayor que fecha inicio
            fechaInicio.addEventListener('change', function() {
                fechaFin.min = this.value;
            });
        }
    });
    
    
    const selectEquipo = document.querySelector('select[name="numero_serie_equipo"]');
    if (selectEquipo) {
        selectEquipo.addEventListener('change', function() {
           
        });
    }
});
    </script>
</body>
</html>

<?php
// Cerrar conexiones
if (isset($stmt_usuario)) $stmt_usuario->close();
if (isset($stmt_mis_equipos)) $stmt_mis_equipos->close();
if (isset($conn)) $conn->close();
?>