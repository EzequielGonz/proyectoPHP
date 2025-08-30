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

if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();
        
        if($producto) {
            echo json_encode(['success' => true, 'producto' => $producto]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener producto: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
}
?>
