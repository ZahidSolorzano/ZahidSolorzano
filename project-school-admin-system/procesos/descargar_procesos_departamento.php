<?php
include '../conexion.php';
include '../funciones.php';

if (!isset($_POST['departamento_id'])) {
    header("Location: index.php");
    exit();
}

$departamento_id = intval($_POST['departamento_id']);

// Obtener información del departamento seleccionado
$sql_depto = "SELECT nombre FROM departamentos WHERE id = ?";
$stmt = $conn->prepare($sql_depto);
$stmt->bind_param("i", $departamento_id);
$stmt->execute();
$depto_info = $stmt->get_result()->fetch_assoc();

// Obtener todos los subdepartamentos (incluyendo el departamento seleccionado)
$departamentos_incluidos = obtenerSubdepartamentos($conn, $departamento_id);
$departamentos_incluidos[] = $departamento_id;

// Crear lista de parámetros para la consulta
$placeholders = implode(',', array_fill(0, count($departamentos_incluidos), '?'));
$types = str_repeat('i', count($departamentos_incluidos));

// Obtener procesos de estos departamentos (solo completos)
$sql = "SELECT p.titulo, p.descripcion, d.nombre as departamento, p.usuario, p.fecha 
        FROM procesos p
        JOIN departamentos d ON p.departamento_id = d.id
        WHERE p.departamento_id IN ($placeholders) AND p.estado = 'completo'
        ORDER BY d.nombre, p.fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$departamentos_incluidos);
$stmt->execute();
$result = $stmt->get_result();

// Recopilar todos los procesos para el índice
$todos_procesos = [];
$contador_proceso = 1;

while ($row = $result->fetch_assoc()) {
    $todos_procesos[] = [
        'titulo' => $row['titulo'],
        'numero' => $contador_proceso++,
        'departamento' => $row['departamento'],
        'data' => $row
    ];
}

// Generar HTML para el PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Procesos del Departamento: '.htmlspecialchars($depto_info['nombre']).'</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { 
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header-content {
            flex: 1;
            text-align: center;
        }
        .logo {
            max-width: 80px;
            max-height: 80px;
            margin-left: 20px;
        }
        h1 { color: #2c3e50; margin: 0; }
        .departamento { font-weight: bold; color: #3498db; margin: 20px 0 10px 0; }
        .proceso { margin: 10px 0 20px 20px; padding: 10px; border-left: 3px solid #e74c3c; page-break-inside: avoid; }
        .label { font-weight: bold; color: #2c3e50; display: inline-block; min-width: 100px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #7f8c8d; }
        .indice { 
            margin: 20px 0; 
            padding: 15px; 
            border: 1px solid #ddd; 
            background-color: #f9f9f9;
        }
        .indice h2 { color: #2c3e50; margin-bottom: 15px; }
        .indice-item { 
            margin: 5px 0; 
            padding: 3px 0; 
            border-bottom: 1px dotted #ccc; 
            display: flex;
            justify-content: space-between;
        }
        .indice-item a { 
            text-decoration: none; 
            color: #3498db; 
            flex: 1;
        }
        .indice-item a:hover { text-decoration: underline; }
        .indice-numero { 
            font-weight: bold; 
            color: #2c3e50; 
            margin-left: 10px; 
            min-width: 30px;
        }
        @media print {
            body { margin: 10px; }
            .no-print { display: none; }
        }
    </style>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
                window.onafterprint = function() {
                    setTimeout(function() { window.close(); }, 300);
                };
            }, 500);
        };
    </script>
</head>
<body>
    <div class="header">';
    
    $html .= '<div class="header-content">
            <h1>Procesos del Departamento: '.htmlspecialchars($depto_info['nombre']).'</h1>
        </div>';
    
    // Buscar logo con cualquier extensión
    $logos_existentes = glob('../assets/logo_cetis27.*');
    if (!empty($logos_existentes) && file_exists($logos_existentes[0])) {
        $logo_path = $logos_existentes[0];
        
        // Convertir imagen a base64 para embebida en el PDF
        $logo_data = file_get_contents($logo_path);
        $logo_base64 = base64_encode($logo_data);
        $logo_mime = mime_content_type($logo_path);
        
        $html .= "<img src='data:$logo_mime;base64,$logo_base64' alt='Logo CETIS 27' class='logo'>";
    }
    
    $html .= '</div>
    <div class="no-print" style="text-align: center; margin: 20px 0;">
        <p>Este documento se abrirá en el diálogo de impresión. Selecciona "Guardar como PDF" para descargarlo.</p>
        <button onclick="window.print()" style="padding: 10px 20px; background: #2c3e50; color: white; border: none; cursor: pointer;">
            Guardar como PDF
        </button>
    </div>';

// Crear índice
$html .= '<div class="indice">
    <h2>Índice de Procesos</h2>';

foreach ($todos_procesos as $proceso_info) {
    $html .= '<div class="indice-item">
        <a href="#proceso-' . $proceso_info['numero'] . '">' . htmlspecialchars($proceso_info['titulo']) . '</a>
        <span class="indice-numero">' . $proceso_info['numero'] . '</span>
    </div>';
}

$html .= '</div>';

$current_depto = '';
$total_procesos = 0;
$contador_proceso = 1;

foreach ($todos_procesos as $proceso_info) {
    $row = $proceso_info['data'];
    
    // Mostrar nombre del departamento cuando cambie
    if ($row['departamento'] != $current_depto) {
        $current_depto = $row['departamento'];
        $html .= '<div class="departamento">'.$current_depto.'</div>';
    }
    
    $html .= '
    <div class="proceso" id="proceso-'.$contador_proceso.'">
        <div><span class="label">Proceso #'.$contador_proceso.' - </span><strong>'.htmlspecialchars($row['titulo']).'</strong></div>
        <div><span class="label">Usuario:</span> '.htmlspecialchars($row['usuario']).'</div>
        <div><span class="label">Fecha:</span> '.htmlspecialchars($row['fecha']).'</div>
        <div><span class="label">Descripción:</span><br> '.nl2br(htmlspecialchars($row['descripcion'])).'</div>
    </div>';
    
    $contador_proceso++;
    $total_procesos++;
}

$html .= '
    <div class="footer">
        <p>Documento generado el: '.date('d/m/Y H:i:s').'</p>
        <p>Total de procesos: '.$total_procesos.'</p>
    </div>
</body>
</html>';

// Configurar encabezados para descarga HTML
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="procesos_'.str_replace(' ', '_', $depto_info['nombre']).'_'.date('Y-m-d_H-i-s').'.html"');
echo $html;

$conn->close();
?>
