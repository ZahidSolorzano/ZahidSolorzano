<?php
session_start();
if (!isset($_SESSION["RPE"])) {
    header("Location: vistalogin.php");
    exit();
}

require_once '../includes/conexion.php';

$mensaje = '';
$error = '';
$prestamo = null;

// Obtener ID del préstamo desde la URL
$id_prestamo = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_prestamo === 0) {
    header("Location: gestion_prestamos.php");
    exit();
}

// Obtener datos del préstamo
$sql = "SELECT p.*, e.numero_serie, e.Tipo, e.Marca, e.Modelo 
        FROM prestamos p 
        LEFT JOIN equipos e ON p.numero_serie_equipo = e.numero_serie 
        WHERE p.id_prestamo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_prestamo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    header("Location: gestion_prestamos.php?error=Prestamo no encontrado");
    exit();
}

$prestamo = $resultado->fetch_assoc();
$stmt->close();

// Verificar que el préstamo no esté finalizado
if ($prestamo['estado_prestamo'] === 'finalizado') {
    header("Location: gestion_prestamos.php?error=No se puede editar un préstamo finalizado");
    exit();
}

// Procesar formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $observaciones = $_POST['observaciones'];
    
    // Validar fechas
    if (empty($fecha_inicio) || empty($fecha_fin)) {
        $error = "Las fechas son obligatorias";
    } elseif (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
        $error = "La fecha de fin debe ser posterior a la fecha de inicio";
    } else {
        // Actualizar el préstamo
        $sql_update = "UPDATE prestamos SET fecha_inicio_prestamo = ?, fecha_fin_prestamo = ?, observaciones = ? WHERE id_prestamo = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $fecha_inicio, $fecha_fin, $observaciones, $id_prestamo);
        
        if ($stmt_update->execute()) {
            $mensaje = "Préstamo actualizado correctamente";
            // Actualizar datos locales
            $prestamo['fecha_inicio_prestamo'] = $fecha_inicio;
            $prestamo['fecha_fin_prestamo'] = $fecha_fin;
            $prestamo['observaciones'] = $observaciones;
        } else {
            $error = "Error al actualizar el préstamo: " . $conn->error;
        }
        $stmt_update->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Préstamo - Sistema de Inventarios</title>
    <link rel="stylesheet" href="gestion_prestamos.css">
    <style>
        .edit-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .edit-header {
            background: var(--surface-color);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .edit-header h2 {
            color: var(--dark-blue);
            margin-bottom: 10px;
        }
        
        .edit-form {
            background: var(--surface-color);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-section {
            margin-bottom: 25px;
        }
        
        .form-section h3 {
            color: var(--dark-blue);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-blue);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid var(--light-blue);
        }
        
        .info-label {
            display: block;
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .info-value {
            display: block;
            font-size: 0.9rem;
            color: var(--text-color);
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .form-input, .form-textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--light-blue);
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .required {
            color: var(--error-color);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .status-esperando_inicio {
            background: #f0ad4e;
            color: #fff;
        }
        
        .status-activo {
            background: #5cb85c;
            color: #fff;
        }
        
        .status-esperando_devolucion {
            background: #0275d8;
            color: #fff;
        }
        
        .status-finalizado {
            background: #d9534f;
            color: #fff;
        }
    </style>
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
            <div class="edit-container">
                <!-- Encabezado -->
                <div class="edit-header">
                    <h2>📝 Editar Préstamo</h2>
                    <p>ID: <?php echo $id_prestamo; ?> | Estado: 
                        <span class="status-badge status-<?php echo $prestamo['estado_prestamo']; ?>">
                            <?php echo $prestamo['estado_prestamo']; ?>
                        </span>
                    </p>
                </div>
                
                <!-- Mostrar mensajes -->
                <?php if ($mensaje): ?>
                    <div class="alert alert-success">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Información del préstamo (solo lectura) -->
                <div class="form-section">
                    <h3>Información del Préstamo</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Equipo</span>
                            <span class="info-value">
                                <?php echo htmlspecialchars($prestamo['Tipo'] ?? '') . ' ' . 
                                      htmlspecialchars($prestamo['Marca'] ?? '') . ' ' . 
                                      htmlspecialchars($prestamo['Modelo'] ?? ''); ?>
                                <br>
                                <small>Serie: <?php echo htmlspecialchars($prestamo['numero_serie'] ?? 'No disponible'); ?></small>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Solicitante</span>
                            <span class="info-value">
                                <?php echo htmlspecialchars($prestamo['nombre_solicitante']); ?>
                                <br>
                                <small>RPE: <?php echo htmlspecialchars($prestamo['rpe_solicitante']); ?></small>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Responsable</span>
                            <span class="info-value">
                                <?php echo htmlspecialchars($prestamo['nombre_responsable']); ?>
                                <br>
                                <small>RPE: <?php echo htmlspecialchars($prestamo['rpe_responsable']); ?></small>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Departamento/División</span>
                            <span class="info-value">
                                <?php echo htmlspecialchars($prestamo['departamento_solicitante']); ?> / 
                                <?php echo htmlspecialchars($prestamo['division_solicitante']); ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Fecha de Solicitud</span>
                            <span class="info-value">
                                <?php echo date('d/m/Y H:i', strtotime($prestamo['fecha_solicitud'])); ?>
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Devolución</span>
                            <span class="info-value">
                                <?php if ($prestamo['fecha_devolucion']): ?>
                                    <?php echo date('d/m/Y H:i', strtotime($prestamo['fecha_devolucion'])); ?>
                                <?php else: ?>
                                    <em>Pendiente</em>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Formulario de edición -->
                <div class="edit-form">
                    <h3>Campos Editables</h3>
                    <p style="margin-bottom: 20px; color: #666;">Puedes modificar las fechas y observaciones del préstamo.</p>
                    
                    <form method="POST" action="">
                        <!-- Fecha de Inicio -->
                        <div class="form-group">
                            <label class="form-label">Fecha de Inicio <span class="required">*</span></label>
                            <input type="datetime-local" 
                                   name="fecha_inicio" 
                                   class="form-input" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($prestamo['fecha_inicio_prestamo'])); ?>" 
                                   required>
                        </div>
                        
                        <!-- Fecha de Fin -->
                        <div class="form-group">
                            <label class="form-label">Fecha de Fin <span class="required">*</span></label>
                            <input type="datetime-local" 
                                   name="fecha_fin" 
                                   class="form-input" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($prestamo['fecha_fin_prestamo'])); ?>" 
                                   required>
                        </div>
                        
                        <!-- Observaciones -->
                        <div class="form-group">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" 
                                      class="form-textarea" 
                                      placeholder="Observaciones adicionales..."><?php echo htmlspecialchars($prestamo['observaciones'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Guardar Cambios</button>
                            <a href="gestion_prestamos.php" class="btn-cancel">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Validación de fechas en el formulario
    document.addEventListener('DOMContentLoaded', function() {
        const fechaInicio = document.querySelector('input[name="fecha_inicio"]');
        const fechaFin = document.querySelector('input[name="fecha_fin"]');
        const form = document.querySelector('form');
        
        // Establecer mínimo de fecha de inicio (hoy)
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const todayStr = `${year}-${month}-${day}T${hours}:${minutes}`;
        
        // fechaInicio.min = todayStr;
        
        // Validar que fecha fin sea mayor que fecha inicio
        fechaInicio.addEventListener('change', function() {
            fechaFin.min = this.value;
            
            // Si fecha fin es anterior a la nueva fecha inicio, actualizarla
            if (fechaFin.value && fechaFin.value < this.value) {
                fechaFin.value = this.value;
            }
        });
        
        // Validación al enviar el formulario
        form.addEventListener('submit', function(e) {
            if (fechaInicio.value && fechaFin.value) {
                if (new Date(fechaFin.value) <= new Date(fechaInicio.value)) {
                    e.preventDefault();
                    alert('La fecha de fin debe ser posterior a la fecha de inicio');
                    fechaFin.focus();
                }
            }
        });
    });
    </script>
</body>
</html>