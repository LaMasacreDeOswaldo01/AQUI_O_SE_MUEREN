<?php
/**
 * CSRFController.php
 * Controlador para manejar tokens CSRF
 */

class CSRFController {
    
    /**
     * Genera y retorna un token CSRF
     * POST /api/csrf/token
     */
    public function getToken() {
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Generar token si no existe
        if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_expiry'] = time() + 3600;
        }
        
        // Renovar si está por expirar
        if (time() > $_SESSION['csrf_token_expiry'] - 600) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_expiry'] = time() + 3600;
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'csrf_token' => $_SESSION['csrf_token'],
            'expiry' => $_SESSION['csrf_token_expiry']
        ]);
    }
}
?>
