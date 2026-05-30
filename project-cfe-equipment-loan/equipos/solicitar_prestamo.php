<?php
session_start();
if (!isset($_SESSION["RPE"])) {
    header("Location: ../vistas/vistalogin.php");
    exit();
}

require_once '../includes/conexion.php';

// Obtener número de serie del equipo
if (!isset($_GET['serie'])) {
    header("Location: gestion_equipos.php?error=No se especificó el equipo");
    exit();
}

$numero_serie = trim($_GET['serie']);

// Obtener información del equipo
$query_equipo = "SELECT * FROM equipos WHERE Numero_serie = ? AND Estado = 'Disponible'";
$stmt_equipo = $conn->prepare($query_equipo);
$stmt_equipo->bind_param('s', $numero_serie);
$stmt_equipo->execute();
$result_equipo = $stmt_equipo->get_result();

if ($result_equipo->num_rows === 0) {
    header("Location: gestion_equipos.php?error=Equipo no disponible o no encontrado");
    exit();
}

$equipo = $result_equipo->fetch_assoc();

// Obtener información del usuario actual (solicitante)
$rpe_solicitante = $_SESSION["RPE"];
$nombre_solicitante = $_SESSION["nombre"] ?? '';

// TOMAR DEPARTAMENTO Y DIVISIÓN DEL EQUIPO 
$departamento_solicitante = $equipo['Departamento'];
$division_solicitante = $equipo['Division'];

// Obtener información del responsable del equipo
$rpe_responsable = $equipo['RPE_responsable'];
$nombre_responsable = $equipo['Nombre_responsable'];
$departamento_equipo = $equipo['Departamento'];
$division_equipo = $equipo['Division'];

// Calcular fechas por defecto (corregido para evitar problemas de zona horaria)
$fecha_actual = date('Y-m-d');
$fecha_manana = date('Y-m-d', strtotime('+1 day'));

// Crear timestamp actual y sumarle 5 minutos para valor por defecto
$tiempo_actual = time();
$tiempo_por_defecto = $tiempo_actual + (5 * 60); 

// Formatear para datetime local (YYYY-MM-DDTHH:MM)
$hora_inicio_default = date('H:i', $tiempo_por_defecto);
// Redondear minutos a múltiplos de 5 
$minutos = date('i', $tiempo_por_defecto);
$minutos_redondeados = ceil($minutos / 5) * 5;
if ($minutos_redondeados >= 60) {
    $hora_inicio_default = date('H', $tiempo_por_defecto) + 1 . ':00';
} else {
    $hora_inicio_default = date('H', $tiempo_por_defecto) . ':' . str_pad($minutos_redondeados, 2, '0', STR_PAD_LEFT);
}

$stmt_equipo->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Préstamo - Sistema de Inventarios</title>
    <link rel="stylesheet" href="solicitar_prestamo.css">
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <header class="header">
            <div class="logo-section">
                <h1>🏢 Sistema de Gestión de Equipos</h1>
            </div>
            <div class="user-info">
                <a href="gestion_equipos.php" class="nav-btn">Volver a Equipos</a>
                <a href="../includes/logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </header>

        <!-- Main Content -->
        <div class="content">
            <div class="page-header">
                <h2>Solicitar Préstamo de Equipo</h2>
                <p>Complete los detalles del préstamo para solicitar el equipo</p>
            </div>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" id="errorMessage" style="display: flex;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" id="successMessage" style="display: flex;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <div class="equipo-highlight">
                Equipo a solicitar: 
                <strong><?php echo htmlspecialchars($equipo['Marca'] . ' ' . $equipo['Modelo']); ?></strong> 
                - N° Serie: <strong><?php echo htmlspecialchars($numero_serie); ?></strong>
            </div>
            
            <!-- Información pre-llenada -->
            <div class="info-section">
                <div class="section-title">
                    Información del Préstamo
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Equipo</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($equipo['Tipo'] . ' - ' . $equipo['Marca'] . ' ' . $equipo['Modelo']); ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">N° Serie</div>
                        <div class="info-value"><?php echo htmlspecialchars($numero_serie); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Solicitante</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($nombre_solicitante); ?> 
                            (<?php echo htmlspecialchars($rpe_solicitante); ?>)
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Departamento/División</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($departamento_equipo . ' / ' . $division_equipo); ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Responsable del Equipo</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($nombre_responsable); ?> 
                            (<?php echo htmlspecialchars($rpe_responsable); ?>)
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Formulario para fechas y observaciones -->
            <form action="../includes/procesar_solicitud_prestamo.php" method="POST" id="prestamoForm">
                <input type="hidden" name="numero_serie_equipo" value="<?php echo htmlspecialchars($numero_serie); ?>">
                
                <div class="form-section">
                    <div class="section-title">
                        Detalles del Préstamo
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha_inicio" class="required">Fecha y Hora de Inicio</label>
                            <!-- CORREGIDO: sin restricción de hora mínima, dejar que el usuario elija -->
                            <input type="datetime-local" 
                                   id="fecha_inicio" 
                                   name="fecha_inicio" 
                                   required
                                   min="<?php echo $fecha_actual . 'T00:00'; ?>"
                                   value="<?php echo $fecha_actual . 'T' . $hora_inicio_default; ?>">
                            <div class="date-note">Puede seleccionar desde hoy a cualquier hora</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_fin" class="required">Fecha y Hora de Fin</label>
                            <!-- CORREGIDO: sin restricción inicial, se ajustará con javascript -->
                            <input type="datetime-local" 
                                   id="fecha_fin" 
                                   name="fecha_fin" 
                                   required
                                   min="<?php echo $fecha_actual . 'T00:00'; ?>"
                                   value="<?php echo $fecha_manana . 'T17:00'; ?>">
                            <div class="date-note">Debe ser posterior a la fecha de inicio</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="observaciones">Observaciones (Opcional)</label>
                        <textarea id="observaciones" 
                                  name="observaciones" 
                                  placeholder="Agregue observaciones adicionales sobre el préstamo..."></textarea>
                    </div>
                </div>
                
                <div class="buttons">
                    <button type="button" class="btn btn-cancel" onclick="cancelarSolicitud()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-submit" id="submitBtn">
                        Solicitar Préstamo
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Función para obtener la fecha y hora actual en formato datetime local
        function getCurrentDateTimeLocal() {
            const now = new Date();
            
            // Ajustar por zona horaria del navegador
            const timezoneOffset = now.getTimezoneOffset() * 60000; // en milisegundos
            const localTime = new Date(now.getTime() - timezoneOffset);
            
            return localTime.toISOString().slice(0, 16); // Formato YYYY-MM-DDTHH:MM
        }
        
        // Función para redondear minutos a múltiplos de 5
        function roundToNearest5Minutes(dateTimeStr) {
            const date = new Date(dateTimeStr);
            const minutes = date.getMinutes();
            const roundedMinutes = Math.ceil(minutes / 5) * 5;
            
            if (roundedMinutes === 60) {
                date.setHours(date.getHours() + 1);
                date.setMinutes(0);
            } else {
                date.setMinutes(roundedMinutes);
            }
            
            // Ajustar por zona horaria
            const timezoneOffset = date.getTimezoneOffset() * 60000;
            const localTime = new Date(date.getTime() - timezoneOffset);
            
            return localTime.toISOString().slice(0, 16);
        }
        
        // Configurar valores por defecto al cargar la página
        function configurarFechasPorDefecto() {
            const fechaInicioInput = document.getElementById('fecha_inicio');
            const fechaFinInput = document.getElementById('fecha_fin');
            
            // Obtener fecha y hora actual en el formato correcto
            const ahora = getCurrentDateTimeLocal();
            const ahoraRedondeado = roundToNearest5Minutes(ahora + ':00');
            
            // Si el valor actual es muy diferente al que debería ser por más de 30 minutos, actualizarlo
            const valorActual = new Date(fechaInicioInput.value + ':00').getTime();
            const valorEsperado = new Date(ahoraRedondeado + ':00').getTime();
            const diferencia = Math.abs(valorActual - valorEsperado);
            
            if (diferencia > 30 * 60 * 1000) { // Más de 30 minutos de diferencia
                fechaInicioInput.value = ahoraRedondeado;
            }
            
            // Establecer fecha mínima como hoy a las 00:00
            const hoy = ahora.slice(0, 10) + 'T00:00';
            fechaInicioInput.min = hoy;
            fechaFinInput.min = hoy;
            
            // Asegurar que fecha fin sea posterior a fecha inicio
            ajustarFechaFin();
        }
        
        // Ajustar fecha fin cuando cambia fecha inicio
        function ajustarFechaFin() {
            const fechaInicioInput = document.getElementById('fecha_inicio');
            const fechaFinInput = document.getElementById('fecha_fin');
            
            const fechaInicio = new Date(fechaInicioInput.value + ':00');
            const fechaFin = new Date(fechaFinInput.value + ':00');
            
            // Establecer mínimo de fecha fin
            fechaFinInput.min = fechaInicioInput.value;
            
            // Si fecha fin es anterior o igual a fecha inicio, ajustarla
            if (fechaFin <= fechaInicio) {
                const nuevaFechaFin = new Date(fechaInicio);
                nuevaFechaFin.setHours(nuevaFechaFin.getHours() + 8); // 8 horas por defecto
                nuevaFechaFin.setMinutes(0); // Redondear a hora en punto
                
                // Formatear para datetime local
                const timezoneOffset = nuevaFechaFin.getTimezoneOffset() * 60000;
                const localTime = new Date(nuevaFechaFin.getTime() - timezoneOffset);
                fechaFinInput.value = localTime.toISOString().slice(0, 16);
            }
        }
        
        // Validación del formulario
        document.getElementById('prestamoForm').addEventListener('submit', function(e) {
            const fechaInicioInput = document.getElementById('fecha_inicio');
            const fechaFinInput = document.getElementById('fecha_fin');
            
            const fechaInicio = new Date(fechaInicioInput.value + ':00');
            const fechaFin = new Date(fechaFinInput.value + ':00');
            const ahora = new Date();
            
            // Permitir un margen de 10 minutos hacia atrás
            const margenPermitido = 10 * 60 * 1000; // 10 minutos
            if (fechaInicio < (ahora - margenPermitido)) {
                e.preventDefault();
                alert('Error: La fecha de inicio no puede ser anterior a la hora actual.');
                fechaInicioInput.focus();
                return false;
            }
            
            if (fechaInicio >= fechaFin) {
                e.preventDefault();
                alert('⚠️ Error: La fecha de fin debe ser posterior a la fecha de inicio.');
                fechaFinInput.focus();
                return false;
            }
            
            // Validar duración mínima (15 minutos)
            const duracionMinima = 15 * 60 * 1000; // 15 minutos
            if ((fechaFin - fechaInicio) < duracionMinima) {
                e.preventDefault();
                alert('⚠️ Error: El préstamo debe tener una duración mínima de 15 minutos.');
                fechaFinInput.focus();
                return false;
            }
            
            // Deshabilitar botón para evitar doble envío
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').innerHTML = 'Procesando...';
            return true;
        });
        
        // Escuchar cambios en fecha inicio
        document.getElementById('fecha_inicio').addEventListener('change', function() {
            ajustarFechaFin();
        });
        
        function cancelarSolicitud() {
            if (confirm('¿Está seguro de que desea cancelar la solicitud de préstamo?')) {
                window.location.href = 'gestion_equipos.php';
            }
        }
        
        // Inicializar cuando cargue la página
        document.addEventListener('DOMContentLoaded', function() {
            configurarFechasPorDefecto();
            
            // Mostrar mensajes de error o exito temporalmente
            const errorMsg = document.getElementById('errorMessage');
            const successMsg = document.getElementById('successMessage');
            
            if (errorMsg) {
                setTimeout(() => {
                    errorMsg.style.display = 'none';
                }, 5000);
            }
            
            if (successMsg) {
                setTimeout(() => {
                    successMsg.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>
</html>