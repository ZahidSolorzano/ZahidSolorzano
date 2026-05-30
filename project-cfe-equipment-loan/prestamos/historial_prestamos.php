<?php
session_start();
if (!isset($_SESSION["RPE"])) {
    header("Location: ../vistas/vistalogin.php");
    exit();
}

require_once '../includes/conexion.php';

// Obtener el tipo de usuario desde la sesión
$tipo_usuario = $_SESSION["rol"] ?? 'empleado';
$es_administrador = ($tipo_usuario === 'administrador');
$rpe_usuario_actual = $_SESSION["RPE"];

// Obtener filtros
$filtro_busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$filtro_fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$filtro_fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

// Configuración de paginación
$prestamos_por_pagina = 20;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $prestamos_por_pagina;

// Construir consulta base para préstamos finalizados
$where_conditions = ["p.estado_prestamo = 'finalizado'"];
$params = [];
$types = '';

// Aplicar filtros
if (!empty($filtro_busqueda)) {
    $where_conditions[] = "(p.numero_serie_equipo LIKE ? OR p.rpe_solicitante LIKE ? OR p.nombre_solicitante LIKE ? OR e.Marca LIKE ? OR e.Modelo LIKE ? OR e.Tipo LIKE ? OR p.nombre_responsable LIKE ?)";
    $busqueda_param = "%$filtro_busqueda%";
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $types .= str_repeat('s', 7);
}

if (!empty($filtro_fecha_inicio)) {
    $where_conditions[] = "DATE(p.fecha_solicitud) >= ?";
    $params[] = $filtro_fecha_inicio;
    $types .= 's';
}

if (!empty($filtro_fecha_fin)) {
    $where_conditions[] = "DATE(p.fecha_devolucion) <= ?";
    $params[] = $filtro_fecha_fin;
    $types .= 's';
}

// Construir consulta WHERE
$where_sql = 'WHERE ' . implode(' AND ', $where_conditions);

// Consulta para el total de préstamos 
$query_total = "SELECT COUNT(*) as total 
                FROM prestamos p
                LEFT JOIN equipos e ON p.numero_serie_equipo = e.Numero_serie
                $where_sql";

$stmt_total = $conn->prepare($query_total);
if (!empty($params)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_prestamos = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_prestamos / $prestamos_por_pagina);

// Consulta para los préstamos de la página actual 
$query = "SELECT p.*, 
                 e.Marca, e.Modelo, e.Tipo, e.Estado as estado_equipo,
                 e.RPE_responsable, e.Nombre_responsable,
                 DATEDIFF(COALESCE(p.fecha_devolucion, NOW()), p.fecha_inicio_prestamo) as dias_prestamo
          FROM prestamos p
          LEFT JOIN equipos e ON p.numero_serie_equipo = e.Numero_serie
          $where_sql 
          ORDER BY p.fecha_devolucion DESC, p.fecha_solicitud DESC 
          LIMIT ? OFFSET ?";

$params_paginated = $params;
$params_paginated[] = $prestamos_por_pagina;
$params_paginated[] = $offset;
$types_paginated = $types . 'ii';

$stmt = $conn->prepare($query);
if (!empty($params_paginated)) {
    $stmt->bind_param($types_paginated, ...$params_paginated);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Préstamos - Sistema de Inventarios | CFE</title>
    
    <link rel="stylesheet" href="historial_prestamos.css">  
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
                <a href="../vistas/dashboard.php" class="nav-btn">Dashboard</a>
                <a href="gestion_prestamos.php" class="nav-btn">Préstamos Activos</a>
                
                <a href="../includes/logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="content">
            <div class="page-header">
                <h2>Historial de Préstamos</h2>
                <p>Visualice todos los préstamos finalizados del sistema.</p>
            </div>

            <!-- Filtros de búsqueda -->
            <div class="filters-container-historial">
                <form id="filterForm" method="GET" action="historial_prestamos.php">
                    <div class="filters-row">
                        <!-- Búsqueda general -->
                        <div class="filter-group">
                            <label class="filter-label" for="busqueda">Buscar</label>
                            <input type="text" 
                                   class="filter-input" 
                                   id="busqueda" 
                                   name="busqueda" 
                                   placeholder="Buscar por: N° serie, solicitante, responsable, marca, modelo..."
                                   value="<?php echo htmlspecialchars($filtro_busqueda); ?>">
                        </div>

                        <!-- Filtro por fecha inicio -->
                        <div class="filter-group">
                            <label class="filter-label" for="fecha_inicio">Fecha desde</label>
                            <input type="date" 
                                   class="filter-input" 
                                   id="fecha_inicio" 
                                   name="fecha_inicio" 
                                   value="<?php echo htmlspecialchars($filtro_fecha_inicio); ?>">
                        </div>

                        <!-- Filtro por fecha fin -->
                        <div class="filter-group">
                            <label class="filter-label" for="fecha_fin">Fecha hasta</label>
                            <input type="date" 
                                   class="filter-input" 
                                   id="fecha_fin" 
                                   name="fecha_fin" 
                                   value="<?php echo htmlspecialchars($filtro_fecha_fin); ?>">
                        </div>

                        <!-- Contador de resultados -->
                        <div class="filter-group" style="flex: 0.5;">
                            <div class="results-counter" style="margin-top: 25px;">
                                <strong><?php echo $total_prestamos; ?></strong> préstamos encontrados
                            </div>
                        </div>
                    </div>

                    <!-- Mostrar filtros activos -->
                    <?php if (!empty($filtro_busqueda) || !empty($filtro_fecha_inicio) || !empty($filtro_fecha_fin)): ?>
                        <div class="active-filters">
                            <strong>Filtros activos:</strong>
                            <?php if (!empty($filtro_busqueda)): ?>
                                <span class="active-filter-tag">
                                    Buscar: "<?php echo htmlspecialchars($filtro_busqueda); ?>"
                                    <span class="remove-filter" onclick="quitarFiltro('busqueda')">×</span>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($filtro_fecha_inicio)): ?>
                                <span class="active-filter-tag">
                                    Desde: <?php echo htmlspecialchars($filtro_fecha_inicio); ?>
                                    <span class="remove-filter" onclick="quitarFiltro('fecha_inicio')">×</span>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($filtro_fecha_fin)): ?>
                                <span class="active-filter-tag">
                                    Hasta: <?php echo htmlspecialchars($filtro_fecha_fin); ?>
                                    <span class="remove-filter" onclick="quitarFiltro('fecha_fin')">×</span>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Acciones de filtros -->
                    <div class="filter-actions">
                        <div>
                            <button type="button" class="btn-filter btn-filter-secondary" onclick="limpiarFiltros()">
                                Limpiar todos
                            </button>
                            <button type="submit" class="btn-filter btn-filter-primary">
                                Aplicar filtros
                            </button>
                        </div>
                    </div>
                    
                    <!-- Campos ocultos para paginación -->
                    <input type="hidden" name="pagina" id="paginaInput" value="1">
                </form>
            </div>

           
            <!-- Tabla de Historial -->
            <div class="table-scroll-container">
                <div class="table-container">
                    <?php if ($result->num_rows > 0): ?>
                        <table class="equipos-table table-historial">
                            <thead>
                                <tr>
                                    <th>Equipo</th>
                                    <th>Solicitante</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Devolución</th>
                                    <th>Días</th>
                                    <th>Estado</th>
                                    <th>Responsable</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['numero_serie_equipo']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($row['Marca'] ?? 'N/A'); ?> <?php echo htmlspecialchars($row['Modelo'] ?? ''); ?></small>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($row['nombre_solicitante']); ?><br>
                                            <small>RPE: <?php echo htmlspecialchars($row['rpe_solicitante']); ?></small>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_solicitud'])); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_inicio_prestamo'])); ?></td>
                                        <td>
                                            <?php if ($row['fecha_devolucion']): ?>
                                                <?php echo date('d/m/Y H:i', strtotime($row['fecha_devolucion'])); ?>
                                            <?php else: ?>
                                                <span class="badge-historial">Sin devolución</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['dias_prestamo'] !== null): ?>
                                                <span class="badge-historial"><?php echo $row['dias_prestamo']; ?> días</span>
                                            <?php else: ?>
                                                <span>-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge-historial badge-<?php echo strtolower($row['estado_prestamo']); ?>">
                                                <?php 
                                                $estado = $row['estado_prestamo'];
                                                if ($estado == 'esperando_inicio') echo 'Esperando inicio';
                                                elseif ($estado == 'esperando_devolucion') echo 'Esperando devolución';
                                                else echo ucfirst($estado);
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($row['Nombre_responsable'] ?? 'N/A'); ?><br>
                                            <small>RPE: <?php echo htmlspecialchars($row['RPE_responsable'] ?? 'N/A'); ?></small>
                                        </td>
                                        <td>
                                            <?php if ($row['estado_prestamo'] === 'finalizado' && $es_administrador): ?>
                                                <button class="btn-eliminar" 
                                                        onclick="eliminarPrestamo(<?php echo $row['id_prestamo']; ?>)"
                                                        title="Eliminar préstamo del historial">
                                                    Eliminar
                                                </button>
                                            <?php elseif (!$es_administrador): ?>
                                                <span style="color: #6c757d; font-size: 0.85em;">Solo admin</span>
                                            <?php else: ?>
                                                <span style="color: #dc3545; font-size: 0.85em;">No finalizado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data-historial">
                            <h3>No se encontraron préstamos en el historial</h3>
                            <p><?php echo !empty($filtro_busqueda) || !empty($filtro_fecha_inicio) || !empty($filtro_fecha_fin) 
                                ? 'No hay préstamos que coincidan con los filtros aplicados.' 
                                : 'No hay préstamos finalizados registrados en el historial.'; ?></p>
                            <?php if (!empty($filtro_busqueda) || !empty($filtro_fecha_inicio) || !empty($filtro_fecha_fin)): ?>
                                <button onclick="limpiarFiltros()" class="btn-primary">Ver todos los préstamos</button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <!-- Flecha anterior -->
                    <?php if ($pagina_actual > 1): ?>
                        <a href="javascript:void(0)" onclick="irAPagina(<?php echo $pagina_actual - 1; ?>)" class="pagination-btn pagination-prev">
                            Anterior
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn pagination-disabled">Anterior</span>
                    <?php endif; ?>

                    <!-- Números de página -->
                    <div class="pagination-numbers">
                        <?php
                        $inicio = max(1, $pagina_actual - 2);
                        $fin = min($total_paginas, $pagina_actual + 2);
                        
                        for ($i = $inicio; $i <= $fin; $i++):
                        ?>
                            <?php if ($i == $pagina_actual): ?>
                                <span class="pagination-number pagination-current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="javascript:void(0)" onclick="irAPagina(<?php echo $i; ?>)" class="pagination-number">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>

                    <!-- Flecha siguiente -->
                    <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="javascript:void(0)" onclick="irAPagina(<?php echo $pagina_actual + 1; ?>)" class="pagination-btn pagination-next">
                            Siguiente
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn pagination-disabled">Siguiente</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Funciones para filtros
        function limpiarFiltros() {
            window.location.href = 'historial_prestamos.php';
        }

        function quitarFiltro(tipo) {
            const form = document.getElementById('filterForm');
            const input = form.querySelector('[name="' + tipo + '"]');
            if (input) {
                input.value = '';
            }
            document.getElementById('paginaInput').value = 1;
            form.submit();
        }

        function irAPagina(pagina) {
            document.getElementById('paginaInput').value = pagina;
            document.getElementById('filterForm').submit();
        }

        // Función para eliminar préstamo
        function eliminarPrestamo(id) {
            if (confirm('⚠️ ¿Estás seguro de que deseas eliminar este préstamo del historial?\n\nEsta acción es permanente y no se puede deshacer.')) {
                window.location.href = 'eliminar_prestamo.php?id=' + id;
            }
        }

        // Permitir búsqueda con Enter
        document.addEventListener('DOMContentLoaded', function() {
            const busquedaInput = document.getElementById('busqueda');
            if (busquedaInput) {
                busquedaInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        document.getElementById('paginaInput').value = 1;
                        document.getElementById('filterForm').submit();
                    }
                });
            }
        });
    </script>
</body>
</html>

<?php
// Cerrar conexiones
if (isset($stmt_total)) $stmt_total->close();
if (isset($stmt)) $stmt->close();
if (isset($result_equipos)) $result_equipos->free();
if (isset($result_estados_prestamos)) $result_estados_prestamos->free();
if (isset($conn)) $conn->close();
?>