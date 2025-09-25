<?php
// Iniciar output buffering
ob_start();

// Verificar e iniciar sesión si es necesario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'AuthManager.php';

$authManager = new AuthManager();

// Verificar autenticación y timeout
if (!$authManager->checkSessionTimeout(60)) {
    ob_end_clean();
    header('Location: index.php');
    exit;
}

$userData = $authManager->getUserData();

// Procesar logout
if (isset($_GET['logout'])) {
    $authManager->logout();
    ob_end_clean();
    header('Location: index.php');
    exit;
}

// Terminar output buffering
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar h1 {
            font-weight: 300;
            font-size: 1.5rem;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
            font-size: 0.9rem;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .card h2 {
            color: #333;
            margin-bottom: 1rem;
            font-weight: 400;
        }
        
        .user-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .info-item strong {
            color: #333;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .api-data {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.9rem;
            white-space: pre-wrap;
            overflow-x: auto;
            margin-top: 1rem;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .debug-section {
            background: #e9ecef;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-family: monospace;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Bienvenido, <?= htmlspecialchars($_SESSION['usuario']) ?></h1>
        <a href="?logout=1" class="logout-btn">Cerrar Sesión</a>
    </nav>
    
    <div class="container">
        <div class="card">
            <h2>Panel de Control</h2>
            <p>Has iniciado sesión exitosamente. Este es tu panel personalizado.</p>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?= date('H:i') ?></div>
                    <div class="stat-label">Hora actual</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= round((time() - $_SESSION['login_time']) / 60) ?></div>
                    <div class="stat-label">Minutos conectado</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $_SESSION['user_id'] ?? 'N/A' ?></div>
                    <div class="stat-label">ID de Usuario</div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Información del Usuario</h2>
            <div class="user-info">
                <div class="info-item">
                    <strong>Email:</strong>
                    <?= htmlspecialchars($_SESSION['usuario']) ?>
                </div>
                <div class="info-item">
                    <strong>Inicio de Sesión:</strong>
                    <?= date('d/m/Y H:i:s', $_SESSION['login_time']) ?>
                </div>
                <div class="info-item">
                    <strong>Estado:</strong>
                    <span style="color: #28a745; font-weight: bold;">Conectado</span>
                </div>
            </div>
            
            <?php if ($userData): ?>
                <h3 style="margin-top: 2rem; color: #333;">Datos de la API:</h3>
                <div class="api-data"><?= htmlspecialchars(json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></div>
            <?php else: ?>
                <div class="debug-section">
                    <strong>Debug - Sin datos de API</strong><br>
                    Esto es normal si tu API aún no está configurada.<br>
                    Los datos de sesión se están guardando correctamente.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>