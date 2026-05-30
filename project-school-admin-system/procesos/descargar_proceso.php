<?php
session_start();
include '../conexion.php';


if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID no especificado.");
}

$id = intval($_GET['id']);

try {
    // Consultar el proceso por ID, excluyendo los que están en progreso
    $sql = "SELECT p.*, d.nombre as departamento_nombre, u.usuario as usuario_nombre 
            FROM procesos p 
            LEFT JOIN departamentos d ON p.departamento_id = d.id 
            LEFT JOIN usuarios u ON p.usuario = u.id 
            WHERE p.id = ? AND p.estado != 'en_progreso'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Extraer datos
        $titulo = $row['titulo'] ?? 'Sin título';
        $descripcion = $row['descripcion'] ?? 'Sin descripción';
        $departamento = $row['departamento_nombre'] ?? $row['departamento'] ?? 'Sin departamento';
        $usuario = $row['usuario_nombre'] ?? $row['usuario'] ?? 'Sin usuario';
        $fecha = $row['fecha_creacion'] ?? $row['fecha'] ?? date('Y-m-d H:i:s');
        
        // Formatear fecha
        if ($fecha && $fecha != '0000-00-00 00:00:00') {
            $fecha_formateada = date('d/m/Y H:i:s', strtotime($fecha));
        } else {
            $fecha_formateada = 'Fecha no disponible';
        }
        
        // Limpiar datos para HTML
        $titulo_html = htmlspecialchars($titulo);
        $descripcion_html = nl2br(htmlspecialchars($descripcion));
        $departamento_html = htmlspecialchars($departamento);
        $usuario_html = htmlspecialchars($usuario);
        
        // Limpiar título para nombre de archivo
        $titulo_limpio = preg_replace('/[^a-zA-Z0-9_-]/', '_', $titulo);
        $titulo_limpio = substr($titulo_limpio, 0, 30);
        
        
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
        
        // Crear HTML para PDF e impresión automática
        $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Proceso #$id</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 40px; 
            line-height: 1.6;
            color: #333;
        }
        .header { 
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #2c3e50; 
            padding-bottom: 20px; 
            margin-bottom: 30px;
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
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 28px;
        }
        .content { 
            margin: 20px 0; 
        }
        .field { 
            margin: 15px 0; 
            padding: 10px;
            border-left: 4px solid #3498db;
            background-color: #f8f9fa;
        }
        .label { 
            font-weight: bold; 
            color: #2c3e50;
            display: inline-block;
            min-width: 120px;
        }
        .value {
            color: #34495e;
        }
        .descripcion {
            margin-top: 10px;
            padding: 15px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .footer { 
            margin-top: 40px; 
            font-size: 12px; 
            color: #7f8c8d; 
            text-align: center;
            border-top: 1px solid #bdc3c7;
            padding-top: 15px;
        }
        .no-print { display: block; }
        @media print { .no-print { display: none !important; } }
    </style>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
                window.onafterprint = function() {
                    window.close();
                };
            }, 300);
        };
    </script>
</head>
<body>
    <div class='header'>
        <div class='header-content'>
            <h1>PROCESO #$id</h1>
            <p style='margin: 10px 0; color: #7f8c8d;'>Documento de Proceso</p>
        </div>
        $logo_html
    </div>

    <div class='no-print' style='text-align: center; margin: 20px;'>
        <button onclick='window.print()' style='padding: 10px 20px; background: #2c3e50; color: white; border: none; cursor: pointer;'>
            Guardar como PDF
        </button>
    </div>
    
    <div class='content'>
        <div class='field'>
            <span class='label'>Título:</span> 
            <span class='value'>$titulo_html</span>
        </div>
        
        <div class='field'>
            <span class='label'>Departamento:</span> 
            <span class='value'>$departamento_html</span>
        </div>
        
        <div class='field'>
            <span class='label'>Usuario:</span> 
            <span class='value'>$usuario_html</span>
        </div>
        
        <div class='field'>
            <span class='label'>Fecha:</span> 
            <span class='value'>$fecha_formateada</span>
        </div>
        
        <div class='field'>
            <div class='label'>Descripción:</div>
            <div class='descripcion'>$descripcion_html</div>
        </div>
    </div>
    
    <div class='footer'>
        <p>Documento generado el: " . date('d/m/Y H:i:s') . "</p>
        <p>Descargado por: " . htmlspecialchars($_SESSION['usuario']) . "</p>
    </div>
</body>
</html>";

        // Forzar descarga como HTML que se puede imprimir como PDF
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="proceso_' . $id . '_' . $titulo_limpio . '.html"');
        echo $html;
        exit();
        
    } else {
        die("Proceso no encontrado o aún está en progreso.");
    }
    
} catch (Exception $e) {
    error_log("Error en descarga de proceso: " . $e->getMessage());
    die("Error al procesar la descarga. Inténtalo de nuevo.");
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
