<?php
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] != "admin") {
    header("Location: dashboard.php?error=No tienes permisos para cambiar el logo");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['logo'])) {
    $archivo = $_FILES['logo'];
    
    
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        header("Location: dashboard.php?error=Error al subir el archivo");
        exit();
    }
    

    $info_imagen = getimagesize($archivo['tmp_name']);
    if ($info_imagen === false) {
        header("Location: dashboard.php?error=El archivo no es una imagen válida");
        exit();
    }

    if ($archivo['size'] > 5 * 1024 * 1024) {
        header("Location: dashboard.php?error=El archivo es demasiado grande (máximo 5MB)");
        exit();
    }
    
    
    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($archivo['type'], $tipos_permitidos)) {
        header("Location: dashboard.php?error=Tipo de archivo no permitido. Use JPG, PNG o GIF");
        exit();
    }
    

    $directorio_assets = 'assets';
    if (!is_dir($directorio_assets)) {
        mkdir($directorio_assets, 0755, true);
    }
    
    
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $nombre_archivo = 'logo_cetis27.' . $extension;
    $ruta_destino = $directorio_assets . '/' . $nombre_archivo;
    
    // Eliminar logos anteriores
    $logos_anteriores = glob($directorio_assets . '/logo_cetis27.*');
    foreach ($logos_anteriores as $logo_anterior) {
        if (file_exists($logo_anterior)) {
            unlink($logo_anterior);
        }
    }
    
    // Mover archivo subido
    if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
        header("Location: dashboard.php?success=Logo actualizado correctamente");
    } else {
        header("Location: dashboard.php?error=Error al guardar el logo");
    }
} else {
    header("Location: dashboard.php?error=No se recibió ningún archivo");
}
exit();
?>
