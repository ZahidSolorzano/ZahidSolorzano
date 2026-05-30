<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../conexion.php';
include '../funciones.php'; // Incluimos el archivo con las funciones

// Obtener todos los departamentos principales (sin padre)
$sql_departamentos = "SELECT id, nombre FROM departamentos WHERE parent_id IS NULL ORDER BY nombre";
$result_departamentos = $conn->query($sql_departamentos);

if ($result_departamentos->num_rows > 0) {
    // Primero recopilar todos los procesos para crear el índice
    $todos_procesos = [];
    $contador_proceso = 1;
    $total_procesos = 0;
    
    // Reset del resultado
    $result_departamentos->data_seek(0);
    
    while ($depto_principal = $result_departamentos->fetch_assoc()) {
        // Obtener procesos del departamento principal (completos)
        $sql_procesos = "SELECT p.titulo, p.descripcion, p.usuario, p.fecha 
                         FROM procesos p 
                         WHERE p.departamento_id = ? AND p.estado = 'completo'
                         ORDER BY p.fecha DESC";
        $stmt = $conn->prepare($sql_procesos);
        $stmt->bind_param("i", $depto_principal['id']);
        $stmt->execute();
        $result_procesos = $stmt->get_result();
        
        while ($proceso = $result_procesos->fetch_assoc()) {
            $todos_procesos[] = [
                'titulo' => $proceso['titulo'],
                'numero' => $contador_proceso++,
                'departamento' => $depto_principal['nombre'],
                'nivel' => 1,
                'data' => $proceso
            ];
            $total_procesos++;
        }
        
        // Obtener subdepartamentos de primer nivel
        $subdepartamentos = obtenerSubdepartamentos($conn, $depto_principal['id'], 1);
        
        foreach ($subdepartamentos as $subdepto_id) {
            // Obtener info del subdepartamento
            $sql_subdepto = "SELECT nombre FROM departamentos WHERE id = ?";
            $stmt = $conn->prepare($sql_subdepto);
            $stmt->bind_param("i", $subdepto_id);
            $stmt->execute();
            $subdepto = $stmt->get_result()->fetch_assoc();
            
            // Procesos del subdepartamento
            $sql_procesos = "SELECT p.titulo, p.descripcion, p.usuario, p.fecha 
                             FROM procesos p 
                             WHERE p.departamento_id = ? AND p.estado = 'completo'
                             ORDER BY p.fecha DESC";
            $stmt = $conn->prepare($sql_procesos);
            $stmt->bind_param("i", $subdepto_id);
            $stmt->execute();
            $result_procesos = $stmt->get_result();
            
            while ($proceso = $result_procesos->fetch_assoc()) {
                $todos_procesos[] = [
                    'titulo' => $proceso['titulo'],
                    'numero' => $contador_proceso++,
                    'departamento' => $subdepto['nombre'],
                    'nivel' => 2,
                    'data' => $proceso
                ];
                $total_procesos++;
            }
            
            // Obtener sub-subdepartamentos (nivel 3)
            $subsubdepartamentos = obtenerSubdepartamentos($conn, $subdepto_id, 1);
            
            foreach ($subsubdepartamentos as $subsubdepto_id) {
                // Obtener info del sub-subdepartamento
                $sql_subsubdepto = "SELECT nombre FROM departamentos WHERE id = ?";
                $stmt = $conn->prepare($sql_subsubdepto);
                $stmt->bind_param("i", $subsubdepto_id);
                $stmt->execute();
                $subsubdepto = $stmt->get_result()->fetch_assoc();
                
                // Procesos del sub-subdepartamento
                $sql_procesos = "SELECT p.titulo, p.descripcion, p.usuario, p.fecha 
                                 FROM procesos p 
                                 WHERE p.departamento_id = ? AND p.estado = 'completo'
                                 ORDER BY p.fecha DESC";
                $stmt = $conn->prepare($sql_procesos);
                $stmt->bind_param("i", $subsubdepto_id);
                $stmt->execute();
                $result_procesos = $stmt->get_result();
                
                while ($proceso = $result_procesos->fetch_assoc()) {
                    $todos_procesos[] = [
                        'titulo' => $proceso['titulo'],
                        'numero' => $contador_proceso++,
                        'departamento' => $subsubdepto['nombre'],
                        'nivel' => 3,
                        'data' => $proceso
                    ];
                    $total_procesos++;
                }
            }
        }
    }
    
    // Crear HTML para la lista de procesos
    $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de Todos los Procesos Administrativos</title>
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
        .departamento { margin-bottom: 30px; }
        .depto-nivel-1 { font-size: 20px; font-weight: bold; color: #2c3e50; border-bottom: 2px solid #2c3e50; padding-bottom: 5px; margin-top: 30px; }
        .depto-nivel-2 { font-size: 16px; font-weight: bold; color: #3498db; margin-left: 20px; margin-top: 20px; }
        .depto-nivel-3 { font-size: 14px; font-style: italic; color: #7f8c8d; margin-left: 40px; margin-top: 15px; }
        .proceso { margin: 10px 0 20px 60px; padding: 10px; border-left: 3px solid #e74c3c; page-break-inside: avoid; }
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
            <h1>Lista de Todos los Procesos Administrativos</h1>
        </div>';
    
    // Buscar logo con cualquier extensión para embebir en el PDF
        $logo_html = '';
        $logos_existentes = glob('../assets/logo_cetis27.*');
        if (!empty($logos_existentes) && file_exists($logos_existentes[0])) {
            $logo_path = $logos_existentes[0];
            
            // Convertir imagen a base64 para embebida en el PDF
            $logo_data = file_get_contents($logo_path);
            $logo_base64 = base64_encode($logo_data);
            $logo_mime = mime_content_type($logo_path);
            
            $logo_html = "<img src='data:$logo_mime;base64,$logo_base64' alt='Logo CETIS 27' class='logo'>";
        }
    
    $html .= $logo_html . '</div>
    <div class="no-print" style="text-align: center; margin: 20px 0;">
        <p>Este documento se abrirá en el diálogo de impresión. Selecciona "Guardar como PDF" para descargarlo.</p>
        <button onclick="window.print()" style="padding: 10px 20px; background: #2c3e50; color: white; border: none; cursor: pointer;">
             Guardar como PDF
        </button>
    </div>';

    // Crear índice
    $html .= '<div class="indice">
        <h2> Índice de Procesos</h2>';
    
    foreach ($todos_procesos as $proceso_info) {
        $html .= '<div class="indice-item">
            <a href="#proceso-' . $proceso_info['numero'] . '">' . htmlspecialchars($proceso_info['titulo']) . '</a>
            <span class="indice-numero">' . $proceso_info['numero'] . '</span>
        </div>';
    }
    
    $html .= '</div>';

    // Reset y generar contenido
    $result_departamentos->data_seek(0);
    $contador_proceso = 1;

    while ($depto_principal = $result_departamentos->fetch_assoc()) {
        $html .= '<div class="departamento">';
        $html .= '<div class="depto-nivel-1">'.$depto_principal['nombre'].'</div>';
        
        // Obtener procesos del departamento principal (completos)
        $sql_procesos = "SELECT p.titulo, p.descripcion, p.usuario, p.fecha 
                         FROM procesos p 
                         WHERE p.departamento_id = ? AND p.estado = 'completo'
                         ORDER BY p.fecha DESC";
        $stmt = $conn->prepare($sql_procesos);
        $stmt->bind_param("i", $depto_principal['id']);
        $stmt->execute();
        $result_procesos = $stmt->get_result();
        
        // Mostrar procesos del departamento principal
        while ($proceso = $result_procesos->fetch_assoc()) {
            $html .= mostrarProceso($proceso, 1, $contador_proceso++);
        }
        
        // Obtener subdepartamentos de primer nivel
        $subdepartamentos = obtenerSubdepartamentos($conn, $depto_principal['id'], 1);
        
        foreach ($subdepartamentos as $subdepto_id) {
            // Obtener info del subdepartamento
            $sql_subdepto = "SELECT nombre FROM departamentos WHERE id = ?";
            $stmt = $conn->prepare($sql_subdepto);
            $stmt->bind_param("i", $subdepto_id);
            $stmt->execute();
            $subdepto = $stmt->get_result()->fetch_assoc();
            
            $html .= '<div class="depto-nivel-2">'.$subdepto['nombre'].'</div>';
            
            // Procesos del subdepartamento
            $sql_procesos = "SELECT p.titulo, p.descripcion, p.usuario, p.fecha 
                             FROM procesos p 
                             WHERE p.departamento_id = ? AND p.estado = 'completo'
                             ORDER BY p.fecha DESC";
            $stmt = $conn->prepare($sql_procesos);
            $stmt->bind_param("i", $subdepto_id);
            $stmt->execute();
            $result_procesos = $stmt->get_result();
            
            while ($proceso = $result_procesos->fetch_assoc()) {
                $html .= mostrarProceso($proceso, 2, $contador_proceso++);
            }
            
            // Obtener sub-subdepartamentos (nivel 3)
            $subsubdepartamentos = obtenerSubdepartamentos($conn, $subdepto_id, 1);
            
            foreach ($subsubdepartamentos as $subsubdepto_id) {
                // Obtener info del sub-subdepartamento
                $sql_subsubdepto = "SELECT nombre FROM departamentos WHERE id = ?";
                $stmt = $conn->prepare($sql_subsubdepto);
                $stmt->bind_param("i", $subsubdepto_id);
                $stmt->execute();
                $subsubdepto = $stmt->get_result()->fetch_assoc();
                
                $html .= '<div class="depto-nivel-3">'.$subsubdepto['nombre'].'</div>';
                
                // Procesos del sub-subdepartamento
                $sql_procesos = "SELECT p.titulo, p.descripcion, p.usuario, p.fecha 
                                 FROM procesos p 
                                 WHERE p.departamento_id = ? AND p.estado = 'completo'
                                 ORDER BY p.fecha DESC";
                $stmt = $conn->prepare($sql_procesos);
                $stmt->bind_param("i", $subsubdepto_id);
                $stmt->execute();
                $result_procesos = $stmt->get_result();
                
                while ($proceso = $result_procesos->fetch_assoc()) {
                    $html .= mostrarProceso($proceso, 3, $contador_proceso++);
                }
            }
        }
        
        $html .= '</div>'; // Cierre del departamento principal
    }

    $html .= '
    <div class="footer">
        <p>Documento generado el: '.date('d/m/Y H:i:s').'</p>
        <p>Total de procesos completos: '.$total_procesos.'</p>
    </div>
</body>
</html>';

    // Configurar encabezados para descarga HTML
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="procesos_por_departamento_'.date('Y-m-d_H-i-s').'.html"');
    echo $html;
} else {
    echo "<script>alert('No se encontraron departamentos.'); window.history.back();</script>";
}

// Función auxiliar para mostrar un proceso con el nivel de indentación adecuado
function mostrarProceso($proceso, $nivel, $numero) {
    $margin = $nivel * 20;
    $titulo = htmlspecialchars($proceso['titulo']);
    $descripcion = nl2br(htmlspecialchars($proceso['descripcion']));
    $usuario = htmlspecialchars($proceso['usuario']);
    $fecha = htmlspecialchars($proceso['fecha']);
    
    return '
    <div class="proceso" id="proceso-'.$numero.'" style="margin-left: '.$margin.'px;">
        <div><span class="label">Proceso #'.$numero.' - </span><strong>'.$titulo.'</strong></div>
        <div><span class="label">Usuario:</span> '.$usuario.'</div>
        <div><span class="label">Fecha:</span> '.$fecha.'</div>
        <div><span class="label">Descripción:</span><br> '.$descripcion.'</div>
    </div>';
}

$conn->close();
?>
