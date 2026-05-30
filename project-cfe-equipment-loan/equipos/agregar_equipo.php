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

// Obtener el RPE del usuario logueado
$rpe_creador = $_SESSION["RPE"];

// Inicializar variables
$success = '';
$error = '';

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanitizar los datos
    $numero_serie = trim($conn->real_escape_string($_POST['numero_serie']));
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
    if (empty($numero_serie) || empty($estado) || empty($departamento) || empty($division) || empty($rpe_responsable) || empty($nombre_responsable) || empty($tipo) || empty($marca) || empty($requiere_mantenimiento)) {
        $error = "Por favor complete todos los campos obligatorios.";
    } else {
        // **VERIFICAR SI EL NÚMERO DE SERIE YA EXISTE**
        $sql_check = "SELECT Numero_serie FROM equipos WHERE Numero_serie = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $numero_serie);
        $stmt_check->execute();
        $stmt_check->store_result();
        
        if ($stmt_check->num_rows > 0) {
            $error = "El número de serie <strong>'$numero_serie'</strong> ya existe en el sistema. Por favor ingrese un número de serie único.";
            $stmt_check->close();
        } else {
            $stmt_check->close();
            
            if (empty($modelo)) {
                $error = "El campo Modelo es obligatorio.";
            } else {
                // Insertar en la base de datos usando consultas preparadas para mayor seguridad
                $sql = "INSERT INTO equipos (
                    Numero_serie, RPE_creador, Fecha_creacion, Estado, Departamento, Division, 
                    RPE_responsable, Nombre_responsable, Tipo, Marca, Modelo, Sistema_operativo, 
                    Velocidad, Requiere_mantenimiento, Fallas, Notas
                ) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "sssssssssssssss",
                    $numero_serie,
                    $rpe_creador,
                    $estado,
                    $departamento,
                    $division,
                    $rpe_responsable,
                    $nombre_responsable,
                    $tipo,
                    $marca,
                    $modelo,
                    $sistema_operativo,
                    $velocidad,
                    $requiere_mantenimiento,
                    $fallas,
                    $notas
                );
                
                if ($stmt->execute()) {
                    $success = "✅ Equipo agregado correctamente.";
                    // Redirigir después de 2 segundos
                    header("refresh:2;url=gestion_equipos.php");
                } else {
                    $error = "❌ Error al agregar el equipo: " . $stmt->error;
                }
                $stmt->close();
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
    <title>Agregar Equipo - Sistema de Inventarios | CFE</title>
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
                <h2>Agregar Nuevo Equipo</h2>
                <p>Complete todos los campos obligatorios (*) para registrar un nuevo equipo en el inventario.</p>
            </div>

            <!-- Mensajes de éxito/error -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <p>Redirigiendo a la gestión de equipos...</p>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="form-container">
                <form method="POST" action="" id="agregarEquipoForm">
                    <!-- Primera fila -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="numero_serie" class="required">Número de Serie</label>
                            <input type="text" id="numero_serie" name="numero_serie" 
                                   value="<?php echo htmlspecialchars($_POST['numero_serie'] ?? ''); ?>" 
                                   required maxlength="50" placeholder="Ingrese el número de serie único">
                            <small style="color: #666; font-size: 12px; margin-top: 5px; display: block;">
                                El número de serie debe ser único en el sistema.
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="estado" class="required">Estado</label>
                            <select id="estado" name="estado" required>
                                <option value="">Seleccionar estado</option>
                                <option value="Disponible" <?php echo ($_POST['estado'] ?? '') == 'Disponible' ? 'selected' : ''; ?>>Disponible</option>
                                <option value="En_uso" <?php echo ($_POST['estado'] ?? '') == 'En_uso' ? 'selected' : ''; ?>>En uso</option>
                                <option value="En_mantenimiento" <?php echo ($_POST['estado'] ?? '') == 'En_mantenimiento' ? 'selected' : ''; ?>>En mantenimiento</option>
                                <option value="Descompuesto" <?php echo ($_POST['estado'] ?? '') == 'Descompuesto' ? 'selected' : ''; ?>>Descompuesto</option> 
                            </select>
                        </div>
                    </div>

                    <!-- Segunda fila -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="departamento" class="required">Departamento</label>
                            <input type="text" id="departamento" name="departamento" 
                                   value="<?php echo htmlspecialchars($_POST['departamento'] ?? ''); ?>" 
                                   required maxlength="100" placeholder="Ingrese el departamento">
                        </div>
                        <div class="form-group">
                            <label for="division" class="required">División</label>
                            <input type="text" id="division" name="division" 
                                   value="<?php echo htmlspecialchars($_POST['division'] ?? ''); ?>" 
                                   required maxlength="100" placeholder="Ingrese la división">
                        </div>
                    </div>

                    <!-- Tercera fila -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rpe_responsable" class="required">RPE Responsable</label>
                            <input type="text" id="rpe_responsable" name="rpe_responsable" 
                                   value="<?php echo htmlspecialchars($_POST['rpe_responsable'] ?? ''); ?>" 
                                   required maxlength="20" placeholder="Ingrese el RPE del responsable">
                        </div>
                        <div class="form-group">
                            <label for="nombre_responsable" class="required">Nombre Responsable</label>
                            <input type="text" id="nombre_responsable" name="nombre_responsable" 
                                   value="<?php echo htmlspecialchars($_POST['nombre_responsable'] ?? ''); ?>" 
                                   required maxlength="100" placeholder="Ingrese el nombre del responsable">
                        </div>
                    </div>

                    <!-- Cuarta fila -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo" class="required">Tipo de Equipo</label>
                            <input type="text" id="tipo" name="tipo" 
                                   value="<?php echo htmlspecialchars($_POST['tipo'] ?? ''); ?>" 
                                   required maxlength="50" placeholder="Ej: Laptop, Tableta, Computadora de escritorio">
                        </div>
                        <div class="form-group">
                            <label for="marca" class="required">Marca</label>
                            <input type="text" id="marca" name="marca" 
                                   value="<?php echo htmlspecialchars($_POST['marca'] ?? ''); ?>" 
                                   required maxlength="50" placeholder="Ingrese la marca del equipo">
                        </div>
                    </div>

                    <!-- Quinta fila -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modelo" class="required">Modelo</label>
                            <input type="text" id="modelo" name="modelo" 
                                   value="<?php echo htmlspecialchars($_POST['modelo'] ?? ''); ?>" 
                                   required maxlength="50" placeholder="Ingrese el modelo del equipo">
                        </div>
                        <div class="form-group">
                            <label for="requiere_mantenimiento" class="required">Requiere Mantenimiento</label>
                            <select id="requiere_mantenimiento" name="requiere_mantenimiento" required>
                                <option value="">Seleccionar</option>
                                <option value="Si" <?php echo ($_POST['requiere_mantenimiento'] ?? '') == 'Si' ? 'selected' : ''; ?>>Sí</option>
                                <option value="No" <?php echo ($_POST['requiere_mantenimiento'] ?? '') == 'No' ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sexta fila - Campos opcionales -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sistema_operativo">Sistema Operativo</label>
                            <input type="text" id="sistema_operativo" name="sistema_operativo" 
                                   value="<?php echo htmlspecialchars($_POST['sistema_operativo'] ?? ''); ?>" 
                                   maxlength="50" placeholder="Ej: Windows 10, Android, Linux">
                        </div>
                        <div class="form-group">
                            <label for="velocidad">Velocidad</label>
                            <input type="text" id="velocidad" name="velocidad" 
                                   value="<?php echo htmlspecialchars($_POST['velocidad'] ?? ''); ?>" 
                                   maxlength="50" placeholder="Ej: 2.4GHz, Mayor a 3GHz">
                        </div>
                    </div>

                    <!-- Campos de texto largos opcionales -->
                    <div class="form-group">
                        <label for="fallas">Fallas Reportadas</label>
                        <textarea id="fallas" name="fallas" maxlength="500" 
                                  placeholder="Describa las fallas del equipo si las hay..."><?php echo htmlspecialchars($_POST['fallas'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="notas">Notas Adicionales</label>
                        <textarea id="notas" name="notas" maxlength="500" 
                                  placeholder="Agregue cualquier nota adicional sobre el equipo..."><?php echo htmlspecialchars($_POST['notas'] ?? ''); ?></textarea>
                    </div>

                    <!-- Botones de acción -->
                    <div class="form-actions">
                        <a href="gestion_equipos.php" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Agregar Equipo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('agregarEquipoForm');
        const numeroSerieInput = document.getElementById('numero_serie');
        
   
        
        // Prevenir envío duplicado
        let isSubmitting = false;
        
        if (form) {
            form.addEventListener('submit', function(e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return false;
                }
                
                // Validar campos requeridos
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#dc3545';
                        
                        // Crear mensaje de error si no existe
                        if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('error-message')) {
                            const errorMsg = document.createElement('small');
                            errorMsg.className = 'error-message';
                            errorMsg.style.color = '#dc3545';
                            errorMsg.style.fontSize = '12px';
                            errorMsg.style.display = 'block';
                            errorMsg.style.marginTop = '5px';
                            errorMsg.textContent = 'Este campo es obligatorio';
                            field.parentNode.insertBefore(errorMsg, field.nextSibling);
                        }
                    } else {
                        field.style.borderColor = '';
                        const errorMsg = field.nextElementSibling;
                        if (errorMsg && errorMsg.classList.contains('error-message')) {
                            errorMsg.remove();
                        }
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor complete todos los campos obligatorios.');
                    return false;
                }
                
                isSubmitting = true;
                // Cambiar texto del botón para indicar procesamiento
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = 'Procesando...';
                    submitBtn.disabled = true;
                }
                
                return true;
            });
        }
        
        // Limpiar mensajes de error cuando el usuario escribe
        if (numeroSerieInput) {
            numeroSerieInput.addEventListener('input', function() {
                this.style.borderColor = '';
                const errorMsg = this.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-message')) {
                    errorMsg.remove();
                }
            });
        }
    });
    </script>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
?>