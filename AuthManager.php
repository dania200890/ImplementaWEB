<?php
require_once 'ApiClient.php';

class AuthManager {
    private ApiClient $apiClient;
    
    public function __construct() {
        $this->apiClient = new ApiClient();
        $this->startSession();
    }
    
    private function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function processLogin(string $usuario, string $password): array {
        // Validaciones básicas
        if (empty($usuario) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Usuario y contraseña son requeridos'
            ];
        }
        
        // Validar formato de usuario (email)
        if (!filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Formato de usuario inválido'
            ];
        }
        
        // Intentar login con la API
        $apiResponse = $this->apiClient->login($usuario, $password);
        
        if ($apiResponse['success']) {
            // Login exitoso
            $userData = $apiResponse['data'];
            
            // Guardar datos en sesión
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $userData['id'] ?? null;
            $_SESSION['usuario'] = $usuario;
            $_SESSION['user_data'] = $userData;
            $_SESSION['login_time'] = time();
            
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);
            
            return [
                'success' => true,
                'message' => 'Login exitoso',
                'user_data' => $userData
            ];
            
        } else {
            // Login fallido - personalizar mensaje según código HTTP
            $errorMessage = match($apiResponse['http_code']) {
                401 => 'Usuario o contraseña incorrectos',
                403 => 'Acceso denegado',
                404 => 'Servicio no encontrado',
                0 => $apiResponse['error'] ?? 'Error de conexión con el servidor',
                default => $apiResponse['data']['message'] ?? 'Credenciales inválidas'
            };
            
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }
    
    public function isLoggedIn(): bool {
        return $_SESSION['user_logged_in'] ?? false;
    }
    
    public function getUserData(): ?array {
        return $_SESSION['user_data'] ?? null;
    }
    
    public function logout(): void {
        session_unset();
        session_destroy();
    }
    
    public function checkSessionTimeout(int $timeoutMinutes = 60): bool {
        if ($this->isLoggedIn()) {
            $loginTime = $_SESSION['login_time'] ?? 0;
            $sessionDuration = (time() - $loginTime) / 60;
            
            if ($sessionDuration > $timeoutMinutes) {
                $this->logout();
                return false;
            }
        }
        return $this->isLoggedIn();
    }
}