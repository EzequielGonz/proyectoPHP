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
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);
    
    if($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de producto inválido']);
        exit();
    }
    
    try {
        // Obtener información de la imagen antes de eliminar
        $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();
        
        // Eliminar el producto
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        
        if($stmt->rowCount() > 0) {
            // Eliminar imagen del servidor si existe
            if($producto && $producto['imagen'] && file_exists('../' . $producto['imagen']) && strpos($producto['imagen'], 'uploads/') === 0) {
                unlink('../' . $producto['imagen']);
            }
            
            echo json_encode(['success' => true, 'message' => 'Producto eliminado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el producto']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar producto: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
