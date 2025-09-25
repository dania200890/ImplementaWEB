<?php
require_once 'config_vulnerable.php';

function setupExistingDatabase() {
    try {
        $pdo = new PDO(
            "mysql:host=" . VulnerableConfig::DB_HOST . ";dbname=" . VulnerableConfig::DB_NAME, 
            VulnerableConfig::DB_USER, 
            VulnerableConfig::DB_PASS
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Verificar si la tabla 'usuarios' existe
        $result = $pdo->query("SHOW TABLES LIKE 'usuarios'");
        if ($result->rowCount() == 0) {
            // Crear la tabla si no existe
            $pdo->exec("
                CREATE TABLE usuarios (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    usuario VARCHAR(50) NOT NULL,
                    password VARCHAR(255) NOT NULL
                )
            ");
            echo "✅ Tabla 'usuarios' creada\n";
        } else {
            echo "✅ Tabla 'usuarios' ya existe\n";
        }
        
        // Insertar usuarios de prueba
        $pdo->exec("
            INSERT IGNORE INTO usuarios (usuario, password) VALUES
            ('admin', 'admin123'),
            ('user1', 'password123'),
            ('guest', 'guest'),
            ('test', '12345')
        ");
        
        echo "✅ Usuarios de prueba insertados:\n";
        echo "   - admin / admin123\n";
        echo "   - user1 / password123\n";
        echo "   - guest / guest\n";
        echo "   - test / 12345\n\n";
        
        return $pdo;
        
    } catch (PDOException $e) {
        die("❌ Error configurando la base de datos: " . $e->getMessage());
    }
}

// Ejecutar setup si se accede directamente
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    setupExistingDatabase();
}
?>