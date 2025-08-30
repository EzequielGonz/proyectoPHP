<?php
echo "<h1>🔧 CONFIGURANDO BASE DE DATOS</h1>";
echo "<hr>";

try {
    // Conectar a MySQL (sin especificar base de datos)
    $pdo = new PDO("mysql:host=localhost", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ <strong>Conexión a MySQL exitosa</strong><br><br>";
    
    // Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `sistema_productos` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Base de datos 'sistema_productos' creada/verificada<br>";
    
    // Usar la base de datos
    $pdo->exec("USE `sistema_productos`");
    echo "✅ Base de datos seleccionada<br><br>";
    
    // Crear tabla usuarios
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabla 'usuarios' creada/verificada<br>";
    
    // Crear tabla productos
    $pdo->exec("CREATE TABLE IF NOT EXISTS productos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(200) NOT NULL,
        descripcion TEXT,
        precio DECIMAL(10,2) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        imagen VARCHAR(500),
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabla 'productos' creada/verificada<br><br>";
    
    // Crear usuario administrador
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute(['admin@admin.com']);
    
    if($stmt->fetch()) {
        // Actualizar contraseña si ya existe
        $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
        $stmt->execute([$password_hash, 'admin@admin.com']);
        echo "✅ Usuario administrador actualizado<br>";
    } else {
        // Crear nuevo usuario
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        $stmt->execute(['Administrador', 'admin@admin.com', $password_hash]);
        echo "✅ Usuario administrador creado<br>";
    }
    
    // Insertar productos de ejemplo
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos");
    if($stmt->fetch()['total'] == 0) {
        $productos_ejemplo = [
            ['Laptop HP Pavilion', 'Laptop de alto rendimiento con procesador Intel i7, 16GB RAM y 512GB SSD', 899.99, 15, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400'],
            ['Mouse Gaming Logitech', 'Mouse gaming con sensor óptico de alta precisión y 6 botones programables', 49.99, 30, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400'],
            ['Teclado Mecánico RGB', 'Teclado mecánico con switches Cherry MX y retroiluminación RGB personalizable', 129.99, 20, 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=400']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)");
        foreach($productos_ejemplo as $producto) {
            $stmt->execute($producto);
        }
        echo "✅ Productos de ejemplo insertados<br>";
    }
    
    echo "<br>🎉 <strong>¡Base de datos configurada exitosamente!</strong><br>";
    echo "<br><strong>Credenciales de acceso:</strong><br>";
    echo "📧 Email: <strong>admin@admin.com</strong><br>";
    echo "🔑 Contraseña: <strong>admin123</strong><br>";
    
    echo "<br><strong>Enlaces del sistema:</strong><br>";
    echo "<a href='index.php' style='color: #007bff; text-decoration: none;'>🏠 Página Principal</a> | ";
    echo "<a href='login.php' style='color: #007bff; text-decoration: none;'>🔑 Login</a> | ";
    echo "<a href='admin.php' style='color: #007bff; text-decoration: none;'>⚙️ Panel Admin</a>";
    
} catch(PDOException $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<br><strong>Posibles soluciones:</strong><br>";
    echo "1. Verifica que MySQL esté ejecutándose en XAMPP<br>";
    echo "2. Verifica que el usuario 'root' no tenga contraseña<br>";
    echo "3. Verifica que XAMPP esté funcionando correctamente<br>";
}

echo "<hr>";
echo "<p><strong>Nota:</strong> Después de que todo funcione, elimina este archivo por seguridad.</p>";
?>
