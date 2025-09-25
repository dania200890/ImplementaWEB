<?php
require_once 'config_vulnerable.php';

class VulnerableAuth {
    private $connection;
    
    public function __construct() {
        try {
            // Conexión a la base de datos
            $this->connection = new mysqli(
                VulnerableConfig::DB_HOST,
                VulnerableConfig::DB_USER,
                VulnerableConfig::DB_PASS,
                VulnerableConfig::DB_NAME
            );
            
            if ($this->connection->connect_error) {
                throw new Exception("Error de conexión: " . $this->connection->connect_error);
            }
            
        } catch (Exception $e) {
            die("Error conectando a la base de datos: " . $e->getMessage());
        }
    }
    
    /**
     * ⚠️ MÉTODO VULNERABLE A INYECCIÓN SQL ⚠️
     * Adaptado para tu tabla 'usuarios' con campos 'usuario' y 'password'
     */
    public function vulnerableLogin($usuario, $password) {
        // 🔴 VULNERABILIDAD: Consulta SQL sin preparar usando tu estructura
        $query = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND password = '$password'";
        
        echo "<div class='alert alert-info'>";
        echo "<strong>🔍 Query ejecutada:</strong><br>";
        echo "<code>" . htmlspecialchars($query) . "</code>";
        echo "</div>";
        
        try {
            $result = $this->connection->query($query);
            
            if ($result === false) {
                throw new Exception($this->connection->error);
            }
            
            echo "<div class='alert alert-success'>";
            echo "<strong>✅ Query ejecutada exitosamente</strong><br>";
            echo "Filas encontradas: " . $result->num_rows;
            echo "</div>";
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                return $user;
            }
            
            return false;
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>";
            echo "<strong>❌ Error SQL:</strong><br>";
            echo htmlspecialchars($e->getMessage());
            echo "<br><strong>Query que falló:</strong><br>";
            echo "<code>" . htmlspecialchars($query) . "</code>";
            echo "<br><br><small><strong>💡 Tip:</strong> Verifica que la tabla 'usuarios' exista y tenga los campos 'usuario' y 'password'.</small>";
            echo "</div>";
            return false;
        }
    }
    
    /**
     * Método para obtener todos los usuarios de tu tabla
     */
    public function getAllUsers() {
        $query = "SELECT * FROM usuarios";
        $result = $this->connection->query($query);
        
        if ($result === false) {
            return [];
        }
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    /**
     * Método para mostrar información de la base de datos
     */
    public function getDatabaseInfo() {
        $info = [];
        
        try {
            // Información básica
            $result = $this->connection->query("SELECT DATABASE() as db_name, USER() as db_user, VERSION() as db_version");
            if ($result) {
                $info['database'] = $result->fetch_assoc();
            }
            
            // Estructura de la tabla usuarios
            $result = $this->connection->query("DESCRIBE usuarios");
            if ($result) {
                $info['table_structure'] = [];
                while ($row = $result->fetch_assoc()) {
                    $info['table_structure'][] = $row;
                }
            }
            
        } catch (Exception $e) {
            $info['error'] = $e->getMessage();
        }
        
        return $info;
    }
}
?>