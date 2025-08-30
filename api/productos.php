<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY fecha_creacion DESC");
    $productos = $stmt->fetchAll();
    
    echo json_encode($productos);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener productos: ' . $e->getMessage()]);
}
?>
