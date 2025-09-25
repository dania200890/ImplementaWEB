<?php
// Simulador de API para pruebas locales
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $usuario = $input['usuario'] ?? '';
    $password = $input['password'] ?? '';
    
    // Credenciales de prueba
    if ($usuario === 'test@example.com' && $password === '123456') {
        http_response_code(200);
        echo json_encode([
            'id' => 123,
            'usuario' => $usuario,
            'nombre' => 'Usuario de Prueba',
            'token' => 'jwt_token_de_prueba_' . time(),
            'rol' => 'admin'
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'message' => 'Usuario o contraseña incorrectos'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'message' => 'Método no permitido'
    ]);
}
?>