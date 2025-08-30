<?php
session_start();
require_once 'config/db.php';

// Obtener productos
$productos = [];
try {
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
    $productos = $stmt->fetchAll();
} catch(PDOException $e) {
    // Si hay error, la tabla no existe aún
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
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
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🏪 Mi Tienda</a>
            <div class="navbar-nav ms-auto">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="admin.php">⚙️ Panel Admin</a>
                    <a class="nav-link" href="logout.php">🚪 Cerrar Sesión</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">🔑 Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-center">
            <h1 class="display-4">Bienvenido a Nuestra Tienda</h1>
            <p class="lead">Descubre nuestros increíbles productos</p>
        </div>
    </section>

    <!-- Productos -->
    <div class="container my-5">
        <h2 class="text-center mb-5">Nuestros Productos</h2>
        
        <?php if(empty($productos)): ?>
            <div class="text-center">
                <p class="text-muted">No hay productos disponibles aún.</p>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="admin.php" class="btn btn-primary">Agregar Primer Producto</a>
                <?php endif; ?>
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

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; 2024 Sistema de Productos</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
