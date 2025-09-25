<?php
require_once 'config.php';

class ApiClient {
    private string $baseUrl;
    
    public function __construct(?string $baseUrl = null) {
        $this->baseUrl = $baseUrl ?? ApiConfig::API_BASE_URL;
    }
    
    private function makeRequest(string $endpoint, array $data = [], string $method = 'POST'): array {
        $url = $this->baseUrl . $endpoint;
        
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => ApiConfig::TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => false, // Para localhost
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new Exception('Error de conexión: ' . $error);
        }
        
        $decodedResponse = json_decode($response, true);
        
        return [
            'http_code' => $httpCode,
            'data' => $decodedResponse,
            'success' => $httpCode >= 200 && $httpCode < 300
        ];
    }
    
    public function login(string $usuario, string $password): array {
        try {
            $loginData = [
                'usuario' => $usuario,
                'password' => $password
            ];
            
            return $this->makeRequest(ApiConfig::LOGIN_ENDPOINT, $loginData);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'http_code' => 0
            ];
        }
    }
}