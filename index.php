<?php
// Iniciar output buffering para evitar problemas con headers
ob_start();

// Verificar e iniciar sesión si es necesario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'AuthManager.php';

$authManager = new AuthManager();
$error = '';
$success = '';

// Redirigir si ya está logueado
if ($authManager->isLoggedIn()) {
    ob_end_clean(); // Limpiar buffer antes de redireccionar
    header('Location: dashboard.php');
    exit;
}

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Verificar token CSRF
    if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido';
    } else {
        $result = $authManager->processLogin($usuario, $password);
        
        if ($result['success']) {
            $success = $result['message'];
            // Limpiar buffer antes de redireccionar
            ob_end_clean();
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Generar token CSRF
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

// Terminar output buffering y enviar contenido
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Iniciar Sesión</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 2rem;
            font-weight: 300;
            font-size: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        input[type="email"], 
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        input[type="text"]:focus, 
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        input[type="submit"] {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        input[type="submit"]:hover {
            transform: translateY(-2px);
        }
        
        input[type="submit"]:active {
            transform: translateY(0);
        }
        
        .alert {
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .error {
            background-color: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .success {
            background-color: #efe;
            color: #363;
            border: 1px solid #cfc;
        }
        
        .footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.8rem;
        }

        .debug {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-family: monospace;
            font-size: 0.8rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        
        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Debug info (removelo en producción) -->
        <div class="debug">
            <strong>Debug Info:</strong><br>
            API URL: <?= ApiConfig::API_BASE_URL ?><br>
            Endpoint: <?= ApiConfig::LOGIN_ENDPOINT ?><br>
            Session Status: <?= session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive' ?>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="usuario">Email:</label>
                <input type="text " class="form-control"
                       id="usuario" 
                       name="usuario" 
                       required 
                       placeholder="ejemplo@correo.com"
                       value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       placeholder="••••••••">
            </div>
            
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <input type="submit" value="Iniciar Sesión">
        </form>
        
        <div class="footer">
            Sistema de autenticación seguro
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>