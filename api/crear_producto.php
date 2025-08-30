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
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $imagen = '';
    
    // Validar campos requeridos
    if(empty($nombre) || empty($descripcion) || $precio <= 0 || $stock < 0) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son requeridos y deben ser válidos']);
        exit();
    }
    
    // Procesar imagen si se subió
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
                $imagen = 'uploads/' . $new_filename;
            }
        }
    }
    
    // Si no se subió imagen, usar URL si se proporcionó
    if(empty($imagen) && !empty($_POST['url_imagen'])) {
        $imagen = trim($_POST['url_imagen']);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen]);
        
        echo json_encode(['success' => true, 'message' => 'Producto creado exitosamente']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al crear producto: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
