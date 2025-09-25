<?php
session_start();
require_once 'VulnerableAuth.php';

$auth = new VulnerableAuth();
$error = '';
$success = '';
$user = null;
$showAllUsers = false;
$showSensitiveData = false;

// Procesar logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 🔴 VULNERABILIDAD: Sin validación, directamente a la consulta SQL
    $user = $auth->vulnerableLogin($usuario, $password);
    
    if ($user) {
        $_SESSION['vulnerable_user'] = $user;
        $success = "¡Login exitoso! Bienvenido " . htmlspecialchars($user['usuario']);
        
        // Determinar si mostrar datos adicionales (simulando roles)
        if ($user['usuario'] === 'admin' || $user['password'] === 'admin123') {
            $showAllUsers = true;
            $showSensitiveData = true;
        }
    } else {
        $error = "Credenciales incorrectas o consulta SQL inválida";
    }
}

// Verificar si ya hay sesión activa
if (isset($_SESSION['vulnerable_user'])) {
    $user = $_SESSION['vulnerable_user'];
    if ($user['role'] === 'admin') {
        $showAllUsers = true;
        $showSensitiveData = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔓 Login Vulnerable - Solo para Enseñanza</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .vulnerable-header {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }
        .vulnerability-badge {
            background: #dc3545;
        }
        .payload-example {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Header de advertencia -->
    <div class="vulnerable-header text-white p-3 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="h3 mb-1">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Sistema Vulnerable - Solo para Enseñanza
                    </h1>
                    <p class="mb-0">
                        <small>⚠️ Este código contiene vulnerabilidades intencionadas para fines educativos</small>
                    </p>
                </div>
                <?php if ($user): ?>
                <div class="col-auto">
                    <span class="me-3">Usuario: <?= htmlspecialchars($user['usuario']) ?></span>
                    <a href="?logout=1" class="btn btn-outline-light btn-sm">Logout</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Formulario de Login -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-shield-exclamation me-2"></i>
                            Login Vulnerable
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                <?= $success ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!$user): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario:</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="usuario" 
                                       name="usuario" 
                                       value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
                                       placeholder="Ingresa tu usuario">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña:</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Ingresa tu contraseña">
                            </div>
                            
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Iniciar Sesión
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <!-- Credenciales válidas -->
                        <div class="mt-4">
                            <h6>👤 Credenciales válidas para probar:</h6>
                            <ul class="list-unstyled small">
                                <li>• <code>admin</code> / <code>admin123</code></li>
                                <li>• <code>user1</code> / <code>password123</code></li>
                                <li>• <code>guest</code> / <code>guest</code></li>
                                <li>• <code>test</code> / <code>12345</code></li>
                            </ul>
                            
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Nota:</strong> Antes de usar, asegúrate de:
                                <ol class="mb-0 mt-2">
                                    <li>Cambiar el nombre de tu BD en <code>config_vulnerable.php</code></li>
                                    <li>Ejecutar <code>setup_database.php</code> para insertar usuarios de prueba</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ejemplos de Inyección SQL -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bug me-2"></i>
                            Ejemplos de Inyección SQL
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="small">Prueba estos payloads en el campo usuario:</p>
                        
                        <div class="payload-example p-3 mb-3 rounded">
                            <strong>1. Bypass de autenticación:</strong><br>
                            <code>' OR '1'='1' -- '</code><br>
                            <small class="text-muted">Inicia sesión sin contraseña válida</small>
                        </div>
                        
                        <div class="payload-example p-3 mb-3 rounded">
                            <strong>2. Login como admin específico:</strong><br>
                            <code>admin' -- '</code><br>
                            <small class="text-muted">Accede como admin sin contraseña</small>
                        </div>
                        
                        <div class="payload-example p-3 mb-3 rounded">
                            <strong>3. Bypass con OR:</strong><br>
                            <code>' OR 1=1 #</code><br>
                            <small class="text-muted">Alternativa usando comentario #</small>
                        </div>
                        
                        <div class="payload-example p-3 mb-3 rounded">
                            <strong>3. UNION injection (estructura simple):</strong><br>
                            <code>' UNION SELECT 1,'admin','secreto' -- '</code><br>
                            <small class="text-muted">Inyecta usuario falso con 3 campos</small>
                        </div>
                        
                        <div class="payload-example p-3 mb-3 rounded">
                            <strong>4. Información del sistema:</strong><br>
                            <code>' UNION SELECT database(),user(),version() -- '</code><br>
                            <small class="text-muted">Obtiene info de la base de datos</small>
                        </div>
                        
                        <div class="payload-example p-3 mb-3 rounded">
                            <strong>5. Listar tablas:</strong><br>
                            <code>' UNION SELECT 1,table_name,3 FROM information_schema.tables -- '</code><br>
                            <small class="text-muted">Muestra todas las tablas de la BD</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Tip:</strong> Deja la contraseña vacía cuando uses estos payloads.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($showAllUsers): ?>
        <!-- Lista de todos los usuarios -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-people-fill me-2"></i>
                            Todos los Usuarios en la tabla 'usuarios'
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Password</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($auth->getAllUsers() as $u): ?>
                                    <tr>
                                        <td><?= $u['id'] ?? 'N/A' ?></td>
                                        <td><?= htmlspecialchars($u['usuario']) ?></td>
                                        <td><code><?= htmlspecialchars($u['password']) ?></code></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($showSensitiveData): ?>
        <!-- Información de la base de datos -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-database me-2"></i>
                            Información de la Base de Datos (Obtenida via SQL Injection)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>¡INFORMACIÓN SENSIBLE EXPUESTA!</strong> Esta información no debería ser accesible.
                        </div>
                        
                        <?php 
                        $dbInfo = $auth->getDatabaseInfo();
                        if (isset($dbInfo['database'])): 
                        ?>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Base de Datos:</h6>
                                        <code><?= htmlspecialchars($dbInfo['database']['db_name']) ?></code>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Usuario MySQL:</h6>
                                        <code><?= htmlspecialchars($dbInfo['database']['db_user']) ?></code>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Versión MySQL:</h6>
                                        <code><?= htmlspecialchars($dbInfo['database']['db_version']) ?></code>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($dbInfo['table_structure'])): ?>
                        <h6>Estructura de la tabla 'usuarios':</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Campo</th>
                                        <th>Tipo</th>
                                        <th>Nulo</th>
                                        <th>Clave</th>
                                        <th>Default</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbInfo['table_structure'] as $column): ?>
                                    <tr>
                                        <td><code><?= htmlspecialchars($column['Field']) ?></code></td>
                                        <td><?= htmlspecialchars($column['Type']) ?></td>
                                        <td><?= htmlspecialchars($column['Null']) ?></td>
                                        <td><?= htmlspecialchars($column['Key']) ?></td>
                                        <td><?= htmlspecialchars($column['Default']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Explicación de las vulnerabilidades -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-journal-code me-2"></i>
                            🎓 Explicación Educativa
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6>🔴 Vulnerabilidades presentes en este código:</h6>
                        <ul>
                            <li><strong>Inyección SQL:</strong> Las consultas SQL se construyen concatenando strings sin validación</li>
                            <li><strong>Sin prepared statements:</strong> Los valores se insertan directamente en la consulta</li>
                            <li><strong>Sin validación de entrada:</strong> No se validan ni sanitizan los datos del usuario</li>
                            <li><strong>Sin escape de caracteres:</strong> Caracteres especiales no son escapados</li>
                            <li><strong>Información sensible expuesta:</strong> Datos confidenciales accesibles sin autorización</li>
                        </ul>
                        
                        <h6 class="mt-4">✅ Cómo corregir estas vulnerabilidades:</h6>
                        <ul>
                            <li><strong>Usar prepared statements:</strong> <code>$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?")</code></li>
                            <li><strong>Validar entrada:</strong> Verificar tipos de datos y longitudes</li>
                            <li><strong>Hash de contraseñas:</strong> Usar <code>password_hash()</code> y <code>password_verify()</code></li>
                            <li><strong>Principio de menor privilegio:</strong> Limitar acceso a datos sensibles</li>
                            <li><strong>Sanitización:</strong> Limpiar y escapar datos de entrada</li>
                        </ul>
                        
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Recordatorio:</strong> Este código es solo para fines educativos. 
                            NUNCA usar código vulnerable en sistemas de producción.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>