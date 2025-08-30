<?php
session_start();

// Verificar si está logueado
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'config/db.php';

// Obtener productos
$productos = [];
try {
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
    $productos = $stmt->fetchAll();
} catch(PDOException $e) {
    // Si hay error, la tabla no existe aún
}

// Procesar formulario de nuevo producto
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $imagen = trim($_POST['imagen']);
    
    if(!empty($nombre) && !empty($descripcion) && $precio > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen]);
            header('Location: admin.php');
            exit();
        } catch(PDOException $e) {
            $error = "Error al crear producto";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Sistema de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .product-card {
            transition: transform 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h5>⚙️ Panel Admin</h5>
                        <small>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link text-white" href="admin.php">📦 Productos</a>
                        <a class="nav-link text-white" href="index.php" target="_blank">🏠 Ver Tienda</a>
                        <a class="nav-link text-white" href="logout.php">🚪 Cerrar Sesión</a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>📦 Gestión de Productos</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoProductoModal">
                            ➕ Nuevo Producto
                        </button>
                    </div>
                    
                    <!-- Lista de Productos -->
                    <?php if(empty($productos)): ?>
                        <div class="text-center py-5">
                            <h4 class="text-muted">No hay productos aún</h4>
                            <p>Comienza agregando tu primer producto</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach($productos as $producto): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card product-card h-100">
                                        <img src="<?php echo $producto['imagen'] ?: 'https://via.placeholder.com/300x200?text=Sin+Imagen'; ?>" 
                                             class="card-img-top" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                             style="height: 200px; object-fit: cover;">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                            <p class="card-text"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                            <div class="mt-auto">
                                                <span class="badge bg-success fs-6">$<?php echo number_format($producto['precio'], 2); ?></span>
                                                <p class="text-muted mt-2">Stock: <?php echo $producto['stock']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Producto -->
    <div class="modal fade" id="nuevoProductoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio" class="form-label">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="stock" name="stock" min="0" value="0" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagen" class="form-label">URL de Imagen (opcional)</label>
                            <input type="url" class="form-control" id="imagen" name="imagen" placeholder="https://ejemplo.com/imagen.jpg">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">💾 Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
