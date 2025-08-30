<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario está logueado
if(!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once '../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $imagen = '';
    
    // Validar campos requeridos
    if($id <= 0 || empty($nombre) || empty($descripcion) || $precio <= 0 || $stock < 0) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos y deben ser válidos']);
        exit();
    }
    
    // Obtener imagen actual
    $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    $producto_actual = $stmt->fetch();
    $imagen = $producto_actual['imagen'];
    
    // Procesar nueva imagen si se subió
    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['imagen']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $upload_dir = '../uploads/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                // Eliminar imagen anterior si existe
                if($imagen && file_exists('../' . $imagen) && strpos($imagen, 'uploads/') === 0) {
                    unlink('../' . $imagen);
                }
                $imagen = 'uploads/' . $new_filename;
            }
        }
    }
    
    // Si no se subió imagen, usar URL si se proporcionó
    if(empty($_FILES['imagen']['name']) && !empty($_POST['url_imagen'])) {
        $imagen = trim($_POST['url_imagen']);
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen = ? WHERE id = ?");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen, $id]);
        
        if($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Producto actualizado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el producto']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar producto: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
