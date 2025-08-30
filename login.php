<?php
session_start();

// Si ya está logueado, redirigir al admin
if(isset($_SESSION['user_id'])) {
    header('Location: admin.php');
    exit();
}

$error = '';

// Procesar login
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/db.php';
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if(empty($email) || empty($password)) {
        $error = "Por favor complete todos los campos";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, nombre, password FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                header('Location: admin.php');
                exit();
            } else {
                $error = "Email o contraseña incorrectos";
            }
        } catch(PDOException $e) {
            $error = "Error en el sistema";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="login-header text-center">
                        <h3>🔑 Iniciar Sesión</h3>
                        <p class="mb-0">Accede a tu panel de administración</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if($error): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">📧 Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">🔒 Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">🚀 Iniciar Sesión</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="index.php" class="text-decoration-none">🏠 Volver al inicio</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
