<?php
function obtenerSubdepartamentos($conn, $departamento_id) {
    $subdepartamentos = [];
    $query = "SELECT id FROM departamentos WHERE parent_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $departamento_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($fila = $result->fetch_assoc()) {
        $subdepartamentos[] = $fila['id'];
        $subdepartamentos = array_merge($subdepartamentos, obtenerSubdepartamentos($conn, $fila['id']));
    }

    return $subdepartamentos;
}

function puedeCrearUsuario($conn, $usuario_actual, $departamento_destino_id) {
    if ($usuario_actual['rol'] == 'admin') {
        return true;
    }
    
    if ($usuario_actual['rol'] == 'encargado') {
        // Obtener todos los departamentos bajo el encargado (incluyendo el suyo)
        $departamentos_permitidos = obtenerSubdepartamentos($conn, $usuario_actual['departamento_id']);
        $departamentos_permitidos[] = $usuario_actual['departamento_id'];
        
        // Verificar si el departamento destino está en la lista permitida
        return in_array($departamento_destino_id, $departamentos_permitidos);
    }
    
    return false;
}
?>