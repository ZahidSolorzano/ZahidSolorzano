<?php
session_start();
$idUsuario = $_SESSION['id_usuario'];

// HEADERS ANTI-CACHE MÁS AGGRESIVOS
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// Evitar caché en navegadores antiguos
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: private, no-store, no-cache, must-revalidate, post-check=0, pre-check=0");

if (!isset($_SESSION["RPE"])) {
    header("Location: vistalogin.php");
    exit();
}

// Conectar a la base de datos
require_once '../includes/conexion.php';

// Obtener parámetros de paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = 10;
$offset = ($pagina - 1) * $por_pagina;

// Obtener filtros
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
$filtro_fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$filtro_fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

// Construir consulta base - JOIN con equipos para obtener número de serie
$sql = "SELECT 
            p.id_prestamo,
            p.numero_serie_equipo,
            p.rpe_solicitante,
            p.nombre_solicitante,
            p.departamento_solicitante,
            p.division_solicitante,
            -- SIEMPRE mostrar el responsable original (antes del préstamo)
            p.rpe_responsable_original as rpe_responsable,
            p.nombre_responsable_original as nombre_responsable,
            -- También podemos mostrar el responsable actual si es necesario
            p.rpe_responsable as rpe_responsable_actual,
            p.nombre_responsable as nombre_responsable_actual,
            p.fecha_solicitud,
            p.fecha_inicio_prestamo,
            p.fecha_fin_prestamo,
            p.fecha_devolucion,
            p.estado_prestamo,
            p.observaciones,
            p.rpe_responsable_original,
            p.nombre_responsable_original,
            e.numero_serie 
        FROM prestamos p 
        LEFT JOIN equipos e ON p.numero_serie_equipo = e.numero_serie 
        WHERE p.estado_prestamo != 'finalizado'"; // EXCLUIR préstamos finalizados
$params = [];

// Aplicar filtros
if (!empty($filtro_estado)) {
    $sql .= " AND p.estado_prestamo = ?";
    $params[] = $filtro_estado;
}

if (!empty($filtro_busqueda)) {
    $sql .= " AND (p.numero_serie_equipo LIKE ? OR p.rpe_solicitante LIKE ? OR p.nombre_solicitante LIKE ? OR e.numero_serie LIKE ? OR p.observaciones LIKE ? OR p.nombre_responsable_original LIKE ?)";
    $busqueda_param = "%$filtro_busqueda%";
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
}

if (!empty($filtro_fecha_inicio)) {
    $sql .= " AND DATE(p.fecha_inicio_prestamo) >= ?";
    $params[] = $filtro_fecha_inicio;
}

if (!empty($filtro_fecha_fin)) {
    $sql .= " AND DATE(p.fecha_inicio_prestamo) <= ?";
    $params[] = $filtro_fecha_fin;
}

// Contar total de registros
$sql_count = "SELECT COUNT(*) as total FROM ($sql) AS count_table";
$stmt_count = $conn->prepare($sql_count);
if ($params) {
    $stmt_count->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_registros = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $por_pagina);

// Añadir orden y límite
$sql .= " ORDER BY p.fecha_solicitud DESC LIMIT ? OFFSET ?";
$params[] = $por_pagina;
$params[] = $offset;

// Ejecutar consulta principal
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$prestamos = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta tags anti-cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Gestión de Préstamos - Sistema de Inventarios</title>
    <link rel="stylesheet" href="gestion_prestamos.css?v=<?php echo time(); ?>">
    
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
                <a href="../equipos/gestion_equipos.php" class="nav-btn">Equipos</a>
                <a href="historial_prestamos.php" class="nav-btn">Historial</a>
                <a href="../includes/logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </header>

        <!-- Contenido Principal -->
        <div class="content">
            <!-- Encabezado de página -->
            <div class="page-header">
                <h2>Gestión de Préstamos Activos</h2>
                <div class="actions">
                    <a href="agregar_prestamo.php" class="btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Préstamo
                    </a>
                </div>
            </div>

        
            <!-- Filtros mejorados -->
            <div class="filters-container">
                <div class="filter-group">
                    <label class="filter-label" for="busqueda">Buscar</label>
                    <input type="text" 
                           class="filter-input" 
                           id="busqueda" 
                           placeholder="N° serie, solicitante, responsable, observaciones..."
                           value="<?php echo htmlspecialchars($filtro_busqueda); ?>">
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="estado">Estado</label>
                    <select class="filter-select" id="estado">
                        <option value="">Todos los estados</option>
                        <option value="activo" <?php echo $filtro_estado == 'activo' ? 'selected' : ''; ?>>Activos</option>
                        <option value="esperando_inicio" <?php echo $filtro_estado == 'esperando_inicio' ? 'selected' : ''; ?>>Esperando inicio</option>
                        <option value="esperando_devolucion" <?php echo $filtro_estado == 'esperando_devolucion' ? 'selected' : ''; ?>>Esperando devolución</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="fecha_inicio">Fecha desde</label>
                    <input type="date" 
                           class="filter-input" 
                           id="fecha_inicio" 
                           value="<?php echo htmlspecialchars($filtro_fecha_inicio); ?>">
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="fecha_fin">Fecha hasta</label>
                    <input type="date" 
                           class="filter-input" 
                           id="fecha_fin" 
                           value="<?php echo htmlspecialchars($filtro_fecha_fin); ?>">
                </div>

                <div class="filter-actions">
                    <button type="button" class="btn-clear" onclick="limpiarFiltros()">
                        Limpiar Filtros
                    </button>
                    <button type="button" class="btn-primary" onclick="aplicarFiltros()">
                        Aplicar Filtros
                    </button>
                </div>
            </div>

            <!-- Mostrar filtros activos -->
            <?php if (!empty($filtro_busqueda) || !empty($filtro_estado) || !empty($filtro_fecha_inicio) || !empty($filtro_fecha_fin)): ?>
                <div class="active-filters">
                    <strong>Filtros activos:</strong>
                    <?php if (!empty($filtro_busqueda)): ?>
                        <span class="active-filter-tag">
                            Buscar: "<?php echo htmlspecialchars($filtro_busqueda); ?>"
                            <span class="remove-filter" onclick="quitarFiltro('busqueda')">×</span>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($filtro_estado)): ?>
                        <span class="active-filter-tag">
                            Estado: <?php echo htmlspecialchars($filtro_estado); ?>
                            <span class="remove-filter" onclick="quitarFiltro('estado')">×</span>
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

            <!-- Información de paginación -->
            <div class="pagination-info">
                Mostrando <?php echo count($prestamos); ?> de <?php echo $total_registros; ?> préstamos activos
            </div>

            <!-- Tabla de préstamos -->
<div class="table-scroll-container">
    <!-- Scroll superior -->
    <div class="scroll-top">
        <div class="scroll-inner"></div>
    </div>

    <!-- Contenedor de tabla principal -->
    <div class="table-container">
        <table class="equipos-table">
            <!-- Encabezado de la tabla -->
            <thead>
                <tr>
                    <th>Acciones</th>
                    <th>Número de Serie</th>
                    <th>Estado</th>
                    <th>RPE del Solicitante</th>
                    <th>Solicitante</th>
                    <th>RPE del Responsable</th>
                    <th>Responsable</th>
                    <th>Departamento</th>
                    <th>División</th>
                    <th>Fecha Solicitud</th>
                    <th>Inicio Préstamo</th>
                    <th>Fin Préstamo</th>
                    <th>Devolución</th>
                    <th>Observaciones</th> <!-- Nueva columna -->
                </tr>
            </thead>

            <!-- Cuerpo de la tabla -->
            <tbody>
                <?php if (count($prestamos) > 0): ?>
                    <?php foreach ($prestamos as $prestamo): ?>
                        <tr>
                            <!-- Acciones -->
                            <td class="actions-cell">
                                <div class="actions-row">
                                    <?php 
                                    // Obtener información del usuario actual desde la sesión
                                    $rpe_usuario_actual = $_SESSION['RPE'];
                                    $rol_usuario_actual = $_SESSION['rol'];
                                    
                                    // Determinar permisos
                                    $es_solicitante = ($prestamo['rpe_solicitante'] == $rpe_usuario_actual);
                                    $es_responsable_original = ($prestamo['rpe_responsable_original'] == $rpe_usuario_actual);
                                    $es_responsable_actual = ($prestamo['rpe_responsable'] == $rpe_usuario_actual);
                                    $es_administrador = ($rol_usuario_actual == 'administrador');
                                    
                                    // El usuario tiene permisos si es:
                                    // 1. Solicitante 
                                    // 2. Responsable Original 
                                    // 3. Administrador
                                    $tiene_permisos = ($es_solicitante || $es_responsable_original || $es_administrador);
                                    
                                    // El usuario puede ver devolver si es:
                                    // 1. Solicitante (solo el solicitante puede devolver)
                                    $puede_devolver = $es_solicitante;
                                    ?>
                                    
                                    <?php if ($tiene_permisos): ?>
                                        <!-- El usuario TIENE permisos -->
                                        <?php if ($prestamo['estado_prestamo'] == 'esperando_inicio'): ?>
                                            <!-- Estado: ESPERANDO_INICIO - Editar y Eliminar -->
                                            <button type="button" class="btn-action btn-edit" 
                                                    onclick="editarPrestamo(<?php echo $prestamo['id_prestamo']; ?>)">
                                                Editar
                                            </button>
                                            <button type="button" class="btn-action btn-delete" 
                                                    onclick="eliminarPrestamo(<?php echo $prestamo['id_prestamo']; ?>)">
                                                Eliminar
                                            </button>
                                            
                                        <?php elseif ($prestamo['estado_prestamo'] == 'activo'): ?>
                                            <!-- Estado: ACTIVO -->
                                            <?php if ($puede_devolver): ?>
                                                <!-- Solo el SOLICITANTE puede devolver -->
                                                <button type="button" class="btn-action btn-prestar" 
                                                        onclick="marcarDevolucion(<?php echo $prestamo['id_prestamo']; ?>)">
                                                    Devolver
                                                </button>
                                            <?php endif; ?>
                                            
                                            <!-- Tanto solicitante, responsable original como administrador pueden Editar/Eliminar -->
                                            <button type="button" class="btn-action btn-edit" 
                                                    onclick="editarPrestamo(<?php echo $prestamo['id_prestamo']; ?>)">
                                                Editar
                                            </button>
                                            <button type="button" class="btn-action btn-delete" 
                                                    onclick="eliminarPrestamo(<?php echo $prestamo['id_prestamo']; ?>)">
                                                Eliminar
                                            </button>
                                            
                                        <?php elseif ($prestamo['estado_prestamo'] == 'esperando_devolucion'): ?>
                                            <!-- Estado: ESPERANDO_DEVOLUCION -->
                                            <?php if ($puede_devolver): ?>
                                                <!-- Solo el SOLICITANTE puede devolver -->
                                                <button type="button" class="btn-action btn-prestar" 
                                                        onclick="marcarDevolucion(<?php echo $prestamo['id_prestamo']; ?>)">
                                                    Devolver
                                                </button>
                                            <?php endif; ?>
                                            
                                            <!-- Tanto solicitante, responsable original como administrador pueden Editar/Eliminar -->
                                            <button type="button" class="btn-action btn-edit" 
                                                    onclick="editarPrestamo(<?php echo $prestamo['id_prestamo']; ?>)">
                                                Editar
                                            </button>
                                            <button type="button" class="btn-action btn-delete" 
                                                    onclick="eliminarPrestamo(<?php echo $prestamo['id_prestamo']; ?>)">
                                                Eliminar
                                            </button>
                                            
                                        <?php else: ?>
                                            <!-- Estado desconocido -->
                                            <span class="no-disponible"><?php echo $prestamo['estado_prestamo']; ?></span>
                                        <?php endif; ?>
                                        
                                    <?php else: ?>
                                        <!-- El usuario NO tiene permisos -->
                                        <!-- Mostrar solo el estado, sin botones -->
                                        <span class="no-disponible">
                                            <?php 
                                            switch($prestamo['estado_prestamo']) {
                                                case 'esperando_inicio':
                                                    echo 'Pendiente';
                                                    break;
                                                case 'activo':
                                                    echo 'En uso';
                                                    break;
                                                case 'esperando_devolucion':
                                                    echo 'Pendiente devolución';
                                                    break;
                                                default:
                                                    echo $prestamo['estado_prestamo'];
                                            }
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Número de Serie del Equipo -->
                            <td>
                                <?php if (!empty($prestamo['numero_serie'])): ?>
                                    <?php echo htmlspecialchars($prestamo['numero_serie']); ?>
                                <?php else: ?>
                                    <em>No disponible</em>
                                <?php endif; ?>
                            </td>

                            <!-- Estado -->
                            <td>
                                <?php
                                $estado_class = '';
                                switch ($prestamo['estado_prestamo']) {
                                    case 'esperando_inicio':
                                        $estado_class = 'status-esperando_inicio';
                                        break;
                                    case 'activo':
                                        $estado_class = 'status-activo';
                                        break;
                                    case 'finalizado':
                                        $estado_class = 'status-finalizado';
                                        break;
                                    case 'esperando_devolucion':
                                        $estado_class = 'status-esperando_devolucion';
                                        break;
                                    default:
                                        $estado_class = 'status-desconocido';
                                }
                                ?>
                                <span class="status-badge <?php echo $estado_class; ?>">
                                    <?php echo $prestamo['estado_prestamo']; ?>
                                </span>
                            </td>
                            
                            <!-- RPE del Solicitante -->
                            <td><?php echo $prestamo['rpe_solicitante']; ?></td>
                            
                            <!-- Solicitante -->
                            <td><?php echo htmlspecialchars($prestamo['nombre_solicitante']); ?></td>

                            <!-- RPE del Responsable -->
                            <td><?php echo htmlspecialchars($prestamo['rpe_responsable']); ?></td>
                            
                            <!-- Responsable -->
                            <td><?php echo htmlspecialchars($prestamo['nombre_responsable']); ?></td>

                            <!-- Departamento -->
                            <td><?php echo htmlspecialchars($prestamo['departamento_solicitante']); ?></td>

                            <!-- División -->
                            <td><?php echo htmlspecialchars($prestamo['division_solicitante']); ?></td>

                            <!-- Fecha Solicitud -->
                            <td><?php echo date('d/m/Y H:i', strtotime($prestamo['fecha_solicitud'])); ?></td>

                            <!-- Inicio Préstamo -->
                            <td><?php echo date('d/m/Y H:i', strtotime($prestamo['fecha_inicio_prestamo'])); ?></td>

                            <!-- Fin Préstamo -->
                            <td><?php echo date('d/m/Y H:i', strtotime($prestamo['fecha_fin_prestamo'])); ?></td>

                            <!-- Devolución -->
                            <td>
                                <?php if ($prestamo['fecha_devolucion']): ?>
                                    <?php echo date('d/m/Y H:i', strtotime($prestamo['fecha_devolucion'])); ?>
                                <?php else: ?>
                                    <em>Pendiente</em>
                                <?php endif; ?>
                            </td>

                            <!-- Observaciones -->
                            <td class="observaciones-cell" title="<?php echo htmlspecialchars($prestamo['observaciones'] ?? ''); ?>">
                                <?php if (!empty($prestamo['observaciones'])): ?>
                                    <?php 
                                    $observaciones = htmlspecialchars($prestamo['observaciones']);
                                    if (strlen($observaciones) > 50) {
                                        echo substr($observaciones, 0, 50) . '...';
                                    } else {
                                        echo $observaciones;
                                    }
                                    ?>
                                <?php else: ?>
                                    <em>Sin observaciones</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="14">
                            <div class="no-data">
                                <h3>No se encontraron préstamos activos</h3>
                                <p>No hay préstamos activos que coincidan con los criterios de búsqueda.</p>
                                <button onclick="limpiarFiltros()" class="btn-primary">Ver todos los préstamos activos</button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Scroll inferior -->
    <div class="scroll-bottom">
        <div class="scroll-inner"></div>
    </div>
</div>

            <!-- Controles de paginación -->
            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <!-- Botón Anterior -->
                    <?php if ($pagina > 1): ?>
                        <a href="javascript:void(0)" onclick="irAPagina(<?php echo $pagina - 1; ?>)" 
                           class="pagination-btn">
                            Anterior
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn pagination-disabled">Anterior</span>
                    <?php endif; ?>

                    <!-- Números de página -->
                    <div class="pagination-numbers">
                        <?php
                        $inicio = max(1, $pagina - 2);
                        $fin = min($total_paginas, $pagina + 2);
                        
                        for ($i = $inicio; $i <= $fin; $i++):
                        ?>
                            <a href="javascript:void(0)" onclick="irAPagina(<?php echo $i; ?>)" 
                               class="pagination-number <?php echo $i == $pagina ? 'pagination-current' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>

                    <!-- Botón Siguiente -->
                    <?php if ($pagina < $total_paginas): ?>
                        <a href="javascript:void(0)" onclick="irAPagina(<?php echo $pagina + 1; ?>)" 
                           class="pagination-btn">
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
    // Función para aplicar filtros
    function aplicarFiltros() {
        const busqueda = document.getElementById('busqueda').value;
        const estado = document.getElementById('estado').value;
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        
        const params = new URLSearchParams();
        if (busqueda) params.append('busqueda', busqueda);
        if (estado) params.append('estado', estado);
        if (fechaInicio) params.append('fecha_inicio', fechaInicio);
        if (fechaFin) params.append('fecha_fin', fechaFin);
        params.append('pagina', '1');
        
        window.location.href = 'gestion_prestamos.php?' + params.toString();
    }

    // Función para limpiar filtros
    function limpiarFiltros() {
        window.location.href = 'gestion_prestamos.php';
    }

    // Función para quitar un filtro específico
    function quitarFiltro(tipo) {
        const params = new URLSearchParams(window.location.search);
        params.delete(tipo);
        params.set('pagina', '1');
        window.location.href = 'gestion_prestamos.php?' + params.toString();
    }

    // Función para ir a una página específica
    function irAPagina(pagina) {
        const params = new URLSearchParams(window.location.search);
        params.set('pagina', pagina);
        window.location.href = 'gestion_prestamos.php?' + params.toString();
    }

    // Funciones para acciones de préstamos
    function editarPrestamo(id) {
        window.location.href = 'editar_prestamo.php?id=' + id;
    }

   function eliminarPrestamo(id) {
    // Mostrar confirmación 
    if (confirm('¿Estás seguro de que deseas eliminar este préstamo?\n\n Esta acción es irreversible y eliminará permanentemente el registro del préstamo.')) {
        window.location.href = 'eliminar_prestamo.php?id=' + id;
    }
    // Si el usuario cancela, no hacer nada
}

    function marcarDevolucion(id) {
        if (confirm('¿Estás seguro de que deseas marcar este préstamo para devolución?')) {
            window.location.href = 'procesar_devolucion.php?id=' + id;
        }
    }

    // Sincronización de scroll horizontal
    function syncScroll() {
        const topScroll = document.querySelector('.scroll-top');
        const bottomScroll = document.querySelector('.scroll-bottom');
        const tableContainer = document.querySelector('.table-container');
        
        if (!topScroll || !bottomScroll || !tableContainer) {
            return;
        }
        
        // OCULTAR SCROLLBAR NATIVO DEL TABLE-CONTAINER
        tableContainer.style.overflowX = 'hidden';
        
        // Función para sincronizar scroll
        function syncAllScrolls(source, target1, target2) {
            const scrollLeft = source.scrollLeft;
            if (target1 && target1.scrollLeft !== scrollLeft) target1.scrollLeft = scrollLeft;
            if (target2 && target2.scrollLeft !== scrollLeft) target2.scrollLeft = scrollLeft;
        }
        
        // Sincronizar scroll superior
        topScroll.addEventListener('scroll', function() {
            syncAllScrolls(this, tableContainer, bottomScroll);
        });
        
        // Sincronizar scroll inferior
        bottomScroll.addEventListener('scroll', function() {
            syncAllScrolls(this, tableContainer, topScroll);
        });
        
        // Sincronizar scroll de tabla
        tableContainer.addEventListener('scroll', function() {
            syncAllScrolls(this, topScroll, bottomScroll);
        });
    }

    // Configurar ancho de los scrolls
    function setupScrollWidth() {
        const table = document.querySelector('.equipos-table');
        if (table) {
            const tableWidth = table.scrollWidth;
            
            const topInner = document.querySelector('.scroll-top .scroll-inner');
            const bottomInner = document.querySelector('.scroll-bottom .scroll-inner');
            
            if (topInner) {
                topInner.style.width = tableWidth + 'px';
                topInner.style.minWidth = tableWidth + 'px';
            }
            if (bottomInner) {
                bottomInner.style.width = tableWidth + 'px';
                bottomInner.style.minWidth = tableWidth + 'px';
            }
        }
    }

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar ancho de scrolls
        setupScrollWidth();
        
        // Inicializar sincronización de scroll
        syncScroll();
        
        // Permitir búsqueda con Enter
        const searchInput = document.getElementById('busqueda');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    aplicarFiltros();
                }
            });
        }
        
        // Aplicar filtros automáticamente al cambiar select
        const estadoSelect = document.getElementById('estado');
        if (estadoSelect) {
            estadoSelect.addEventListener('change', function() {
                aplicarFiltros();
            });
        }
        
        // Aplicar filtros automáticamente al cambiar fechas
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const fechaFinInput = document.getElementById('fecha_fin');
        
        if (fechaInicioInput) {
            fechaInicioInput.addEventListener('change', function() {
                aplicarFiltros();
            });
        }
        
        if (fechaFinInput) {
            fechaFinInput.addEventListener('change', function() {
                aplicarFiltros();
            });
        }
        
        // Mostrar observaciones completas al hacer clic
        const observacionesCells = document.querySelectorAll('.observaciones-cell');
        observacionesCells.forEach(cell => {
            cell.addEventListener('click', function(e) {
                e.stopPropagation();
                const title = this.getAttribute('title');
                if (title) {
                    const observacionCompleta = prompt('Observaciones completas:', title);
                }
            });
        });
        
        // Reajustar en caso de que la tabla cargue dinámicamente
        setTimeout(setupScrollWidth, 100);
    });

    // Redimensionar scrolls cuando cambie el tamaño de la ventana
    window.addEventListener('resize', function() {
        setTimeout(setupScrollWidth, 50);
    });

    // También ajustar después de que todas las imágenes carguen
    window.addEventListener('load', function() {
        setTimeout(setupScrollWidth, 100);
    });
</script>
</body>
</html>