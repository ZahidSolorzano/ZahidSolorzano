
<?php
session_start();
if (!isset($_SESSION["RPE"])) {
    header("Location: vistalogin.php");
    exit();
}

require_once '../includes/conexion.php';

// Obtener el tipo de usuario desde la sesión
$tipo_usuario = $_SESSION["rol"] ?? 'empleado'; // Por defecto empleado si no está definido
$es_administrador = ($tipo_usuario === 'administrador');
$rpe_usuario_actual = $_SESSION["RPE"]; // RPE del usuario logueado

// Obtener filtros
$filtro_busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$filtro_departamento = isset($_GET['departamento']) ? $_GET['departamento'] : '';
$filtro_responsable = isset($_GET['responsable']) ? $_GET['responsable'] : '';

// Configuración de paginación
$equipos_por_pagina = 20;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $equipos_por_pagina;

// Construir consulta base
$where_conditions = [];
$params = [];
$types = '';

// Aplicar filtros
if (!empty($filtro_busqueda)) {
    $where_conditions[] = "(Numero_serie LIKE ? OR Marca LIKE ? OR Modelo LIKE ? OR RPE_responsable LIKE ? OR Nombre_responsable LIKE ? OR Notas LIKE ?)";
    $busqueda_param = "%$filtro_busqueda%";
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $params[] = $busqueda_param;
    $types .= str_repeat('s', 6);
}

if (!empty($filtro_estado)) {
    $where_conditions[] = "Estado = ?";
    $params[] = $filtro_estado;
    $types .= 's';
}

if (!empty($filtro_tipo)) {
    $where_conditions[] = "Tipo = ?";
    $params[] = $filtro_tipo;
    $types .= 's';
}

if (!empty($filtro_departamento)) {
    $where_conditions[] = "Departamento = ?";
    $params[] = $filtro_departamento;
    $types .= 's';
}

if (!empty($filtro_responsable)) {
    $where_conditions[] = "RPE_responsable = ?";
    $params[] = $filtro_responsable;
    $types .= 's';
}

// Obtener opciones para select de filtros
$query_tipos = "SELECT DISTINCT Tipo FROM equipos WHERE Tipo IS NOT NULL AND Tipo != '' ORDER BY Tipo";
$result_tipos = $conn->query($query_tipos);
$tipos = $result_tipos->fetch_all(MYSQLI_ASSOC);

$query_departamentos = "SELECT DISTINCT Departamento FROM equipos WHERE Departamento IS NOT NULL AND Departamento != '' ORDER BY Departamento";
$result_departamentos = $conn->query($query_departamentos);
$departamentos = $result_departamentos->fetch_all(MYSQLI_ASSOC);

$query_responsables = "SELECT DISTINCT RPE_responsable, Nombre_responsable FROM equipos WHERE RPE_responsable IS NOT NULL AND RPE_responsable != '' ORDER BY Nombre_responsable";
$result_responsables = $conn->query($query_responsables);
$responsables = $result_responsables->fetch_all(MYSQLI_ASSOC);

// Obtener estados únicos de la base de datos
$query_estados = "SELECT DISTINCT Estado FROM equipos WHERE Estado IS NOT NULL AND Estado != '' ORDER BY Estado";
$result_estados = $conn->query($query_estados);
$estados_bd = $result_estados->fetch_all(MYSQLI_ASSOC);

// Construir consulta WHERE
$where_sql = '';
if (!empty($where_conditions)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Consulta para el total de equipos
$query_total = "SELECT COUNT(*) as total FROM equipos $where_sql";
$stmt_total = $conn->prepare($query_total);
if (!empty($params)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_equipos = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_equipos / $equipos_por_pagina);

// Consulta para los equipos de la página actual
$query = "SELECT * FROM equipos $where_sql ORDER BY Fecha_creacion DESC LIMIT ? OFFSET ?";
$params_paginated = $params;
$params_paginated[] = $equipos_por_pagina;
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
    <title>Gestión de Equipos - Sistema de Inventarios | CFE</title>
    <link rel="stylesheet" href="gestion_equipos.css">
    
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
                <a href="../prestamos/gestion_prestamos.php" class="nav-btn">Prestamos</a>
                <a href="../includes/logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="content">
            <div class="page-header">
                <h2>Gestión de Equipos</h2>
                <div class="actions">
                    <?php if ($es_administrador): ?>
                        <a href="agregar_equipo.php" class="btn-primary">
                            Agregar Nuevo Equipo
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Filtros de búsqueda -->
            <div class="filters-container">
                <form id="filterForm" method="GET" action="gestion_equipos.php">
                    <div class="filters-row">
                        <!-- Búsqueda general -->
                        <div class="filter-group">
                            <label class="filter-label" for="busqueda">Buscar</label>
                            <input type="text" 
                                   class="filter-input" 
                                   id="busqueda" 
                                   name="busqueda" 
                                   placeholder="N° serie, marca, modelo, responsable..."
                                   value="<?php echo htmlspecialchars($filtro_busqueda); ?>">
                        </div>

                        <!-- Filtro por estado -->
                        <div class="filter-group">
                            <label class="filter-label" for="estado">Estado</label>
                            <select class="filter-select" id="estado" name="estado">
                                <option value="">Todos los estados</option>
                                <?php foreach ($estados_bd as $estado_item): ?>
                                    <?php 
                                    $estado_valor = $estado_item['Estado'];
                                    $estado_texto = str_replace('_', ' ', $estado_valor);
                                    ?>
                                    <option value="<?php echo htmlspecialchars($estado_valor); ?>" 
                                        <?php echo $filtro_estado == $estado_valor ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($estado_texto); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtro por tipo -->
                        <div class="filter-group">
                            <label class="filter-label" for="tipo">Tipo de equipo</label>
                            <select class="filter-select" id="tipo" name="tipo">
                                <option value="">Todos los tipos</option>
                                <?php foreach ($tipos as $tipo_item): ?>
                                    <option value="<?php echo htmlspecialchars($tipo_item['Tipo']); ?>" 
                                        <?php echo $filtro_tipo == $tipo_item['Tipo'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tipo_item['Tipo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtro por departamento -->
                        <div class="filter-group">
                            <label class="filter-label" for="departamento">Departamento</label>
                            <select class="filter-select" id="departamento" name="departamento">
                                <option value="">Todos los departamentos</option>
                                <?php foreach ($departamentos as $depto): ?>
                                    <option value="<?php echo htmlspecialchars($depto['Departamento']); ?>" 
                                        <?php echo $filtro_departamento == $depto['Departamento'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($depto['Departamento']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Filtro por responsable -->
                        <div class="filter-group">
                            <label class="filter-label" for="responsable">Responsable</label>
                            <select class="filter-select" id="responsable" name="responsable">
                                <option value="">Todos los responsables</option>
                                <?php foreach ($responsables as $resp): ?>
                                    <option value="<?php echo htmlspecialchars($resp['RPE_responsable']); ?>" 
                                        <?php echo $filtro_responsable == $resp['RPE_responsable'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($resp['Nombre_responsable']); ?> (<?php echo htmlspecialchars($resp['RPE_responsable']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Mostrar filtros activos -->
                    <?php if (!empty($filtro_busqueda) || !empty($filtro_estado) || !empty($filtro_tipo) || !empty($filtro_departamento) || !empty($filtro_responsable)): ?>
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
                                    Estado: <?php echo str_replace('_', ' ', htmlspecialchars($filtro_estado)); ?>
                                    <span class="remove-filter" onclick="quitarFiltro('estado')">×</span>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($filtro_tipo)): ?>
                                <span class="active-filter-tag">
                                    Tipo: <?php echo htmlspecialchars($filtro_tipo); ?>
                                    <span class="remove-filter" onclick="quitarFiltro('tipo')">×</span>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($filtro_departamento)): ?>
                                <span class="active-filter-tag">
                                    Depto: <?php echo htmlspecialchars($filtro_departamento); ?>
                                    <span class="remove-filter" onclick="quitarFiltro('departamento')">×</span>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($filtro_responsable)): ?>
                                <span class="active-filter-tag">
                                    Responsable: <?php 
                                        $nombre_resp = '';
                                        foreach ($responsables as $resp) {
                                            if ($resp['RPE_responsable'] == $filtro_responsable) {
                                                $nombre_resp = $resp['Nombre_responsable'];
                                                break;
                                            }
                                        }
                                        echo htmlspecialchars($nombre_resp);
                                    ?>
                                    <span class="remove-filter" onclick="quitarFiltro('responsable')">×</span>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Acciones de filtros -->
                    <div class="filter-actions">
                        <!-- Contador de resultados -->
                        <div class="results-counter">
                            <strong><?php echo $total_equipos; ?></strong> equipos encontrados
                        </div>
                        
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

            <!-- Tabla de Equipos con scroll arriba y abajo -->
            <div class="table-scroll-container">
                <!-- Scroll superior -->
                <div class="scroll-top" id="scrollTop">
                    <div class="scroll-inner"></div>
                </div>
                
                <!-- Tabla -->
                <div class="table-container" id="tableContainer">
                    <?php if ($result->num_rows > 0): ?>
                        <table id="equiposTable" class="equipos-table">
                            <thead>
                                <tr>
                                    <th>Acciones</th>
                                    <th>N° Serie</th>
                                    <th>RPE del Creador</th>
                                    <th>Fecha de Creación</th>
                                    <th>RPE del Responsable</th>
                                    <th>Nombre del Responsable</th>
                                    <th>Estado</th>
                                    <th>Departamento</th>
                                    <th>División</th>
                                    <th>Tipo</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Sistema Operativo</th>
                                    <th>Velocidad</th>
                                    <th>Requiere Mantenimiento</th>
                                    <th>Fallas</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <?php
                                    // Determinar si el usuario actual es el responsable del equipo
                                    $es_responsable = ($row['RPE_responsable'] === $rpe_usuario_actual);
                                    // Determinar si el equipo está disponible (solo los con estado "Disponible" pueden prestarse)
                                    $esta_disponible = ($row['Estado'] === 'Disponible');
                                    ?>
                                    <tr>
                                        <!-- Acciones -->
                                        <td class="actions-cell">
                                            <div class="actions-row">
                                                <?php if ($es_administrador): ?>
                                                    <button class="btn-action btn-edit" onclick="editarEquipo('<?php echo $row['Numero_serie']; ?>')" title="Editar">
                                                        Editar
                                                    </button>
                                                    <button class="btn-action btn-delete" onclick="eliminarEquipo('<?php echo $row['Numero_serie']; ?>')" title="Eliminar">
                                                        Eliminar
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <!-- Botones de préstamo/solicitud - SOLO SI ESTÁ DISPONIBLE -->
                                                <?php if ($esta_disponible): ?>
                                                    <?php if ($es_responsable): ?>
                                                        <button class="btn-action btn-prestar" onclick="prestarEquipo('<?php echo $row['Numero_serie']; ?>')" title="Prestar este equipo">
                                                            Prestar
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn-action btn-solicitar" onclick="solicitarEquipo('<?php echo $row['Numero_serie']; ?>')" title="Solicitar este equipo">
                                                            Solicitar
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="no-disponible" title="Equipo no disponible para préstamo">
                                                        No disponible
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['Numero_serie']); ?></td>
                                        <td><?php echo htmlspecialchars($row['RPE_creador']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['Fecha_creacion'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['RPE_responsable']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Nombre_responsable']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($row['Estado']); ?>">
                                                <?php 
                                                $estado = $row['Estado'];
                                                echo str_replace('_', ' ', $estado);
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['Departamento']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Division']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Tipo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Marca']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Modelo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Sistema_operativo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Velocidad']); ?></td>
                                        <td>
                                            <span class="mantenimiento-badge <?php echo $row['Requiere_mantenimiento'] == 'Si' ? 'si-mantenimiento' : 'no-mantenimiento'; ?>">
                                                <?php echo htmlspecialchars($row['Requiere_mantenimiento']); ?>
                                            </span>
                                        </td>
                                        <td class="text-limited" title="<?php echo htmlspecialchars($row['Fallas']); ?>">
                                            <?php 
                                            $fallas = $row['Fallas'];
                                            echo strlen($fallas) > 50 ? substr($fallas, 0, 50) . '...' : $fallas; 
                                            ?>
                                        </td>
                                        <td class="text-limited" title="<?php echo htmlspecialchars($row['Notas']); ?>">
                                            <?php 
                                            $notas = $row['Notas'];
                                            echo strlen($notas) > 50 ? substr($notas, 0, 50) . '...' : $notas; 
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data">
                            <h3>No se encontraron equipos</h3>
                            <p><?php echo !empty($filtro_busqueda) || !empty($filtro_estado) || !empty($filtro_tipo) || !empty($filtro_departamento) || !empty($filtro_responsable) 
                                ? 'No hay equipos que coincidan con los filtros aplicados.' 
                                : 'No hay equipos registrados.'; ?></p>
                            <?php if (!empty($filtro_busqueda) || !empty($filtro_estado) || !empty($filtro_tipo) || !empty($filtro_departamento) || !empty($filtro_responsable)): ?>
                                <button onclick="limpiarFiltros()" class="btn-primary">Ver todos los equipos</button>
                            <?php elseif ($es_administrador): ?>
                                <a href="agregar_equipo.php" class="btn-primary">Agregar Primer Equipo</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Scroll inferior -->
                <div class="scroll-bottom" id="scrollBottom">
                    <div class="scroll-inner"></div>
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
                        // Mostrar páginas (máximo 5 páginas alrededor de la actual)
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
        function editarEquipo(numeroSerie) {
            window.location.href = 'editar_equipo.php?serie=' + numeroSerie;
        }

        function eliminarEquipo(numeroSerie) {
 
    if (confirm('⚠️ ¿Estás seguro de que deseas eliminar el equipo ' + numeroSerie + '?\n\nNOTA: Esta acción también eliminará TODOS los préstamos relacionados con este equipo (activos e históricos).')) {
        window.location.href = 'eliminar_equipo.php?serie=' + numeroSerie;
    }
}

        function prestarEquipo(numeroSerie) {
        window.location.href = 'prestar_equipo.php?serie=' + numeroSerie;
        }

        function solicitarEquipo(numeroSerie) {
        window.location.href = 'solicitar_prestamo.php?serie=' + numeroSerie;
        }

        // Funciones para filtros
        function limpiarFiltros() {
            window.location.href = 'gestion_equipos.php';
        }

        function quitarFiltro(tipo) {
            const form = document.getElementById('filterForm');
            const input = form.querySelector('[name="' + tipo + '"]');
            if (input) {
                if (input.type === 'text' || input.type === 'select-one') {
                    input.value = '';
                }
            }
            document.getElementById('paginaInput').value = 1;
            form.submit();
        }

        function irAPagina(pagina) {
            document.getElementById('paginaInput').value = pagina;
            document.getElementById('filterForm').submit();
        }

        // Sincronizar scroll horizontal superior e inferior
        document.addEventListener('DOMContentLoaded', function() {
            const tableContainer = document.getElementById('tableContainer');
            const scrollTop = document.getElementById('scrollTop');
            const scrollBottom = document.getElementById('scrollBottom');
            const scrollInnerTop = scrollTop.querySelector('.scroll-inner');
            const scrollInnerBottom = scrollBottom.querySelector('.scroll-inner');

            // Sincronizar scroll superior con la tabla
            scrollTop.addEventListener('scroll', function() {
                tableContainer.scrollLeft = scrollTop.scrollLeft;
                scrollBottom.scrollLeft = scrollTop.scrollLeft;
            });

            // Sincronizar scroll inferior con la tabla
            scrollBottom.addEventListener('scroll', function() {
                tableContainer.scrollLeft = scrollBottom.scrollLeft;
                scrollTop.scrollLeft = scrollBottom.scrollLeft;
            });

            // Sincronizar scroll de la tabla con los controles
            tableContainer.addEventListener('scroll', function() {
                scrollTop.scrollLeft = tableContainer.scrollLeft;
                scrollBottom.scrollLeft = tableContainer.scrollLeft;
            });

            // Ajustar el ancho del scroll interno según la tabla
            function ajustarScroll() {
                const table = document.getElementById('equiposTable');
                if (table) {
                    const tableWidth = table.scrollWidth;
                    scrollInnerTop.style.width = tableWidth + 'px';
                    scrollInnerBottom.style.width = tableWidth + 'px';
                }
            }

            // Ajustar al cargar y al redimensionar
            ajustarScroll();
            window.addEventListener('resize', ajustarScroll);
            
            // Permitir búsqueda con Enter
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
            
            // Aplicar filtros automáticamente al cambiar select
            const selects = document.querySelectorAll('.filter-select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    document.getElementById('paginaInput').value = 1;
                    document.getElementById('filterForm').submit();
                });
            });
        });
    </script>
</body>
</html>

<?php
// Cerrar conexiones
if (isset($stmt_total)) $stmt_total->close();
if (isset($stmt)) $stmt->close();
if (isset($result_tipos)) $result_tipos->free();
if (isset($result_departamentos)) $result_departamentos->free();
if (isset($result_responsables)) $result_responsables->free();
if (isset($result_estados)) $result_estados->free();
if (isset($conn)) $conn->close();
?>
