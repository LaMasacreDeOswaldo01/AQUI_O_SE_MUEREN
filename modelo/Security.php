<?php
/**
 * Clase de seguridad centralizada
 * Maneja CSRF, sanitización, etc.
 */
class Security {
    
    /**
     * Genera un token CSRF y lo almacena en sesión
     * @return string Token generado
     */
    public static function generarTokenCSRF() {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_expiry'] = time() + 3600; // 1 hora
        }
        
        // Renovar si está por expirar (menos de 10 minutos)
        if (time() > $_SESSION['csrf_token_expiry'] - 600) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_expiry'] = time() + 3600;
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verifica un token CSRF
     * @param string $token Token a verificar
     * @return bool True si es válido
     */
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
   public static function verificarTokenCSRF($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
        error_log("[CSRF] Verificación fallida: Token no existe en sesión");
        return false;
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    }
    
    if (time() > $_SESSION['csrf_token_expiry']) {
        error_log("[CSRF] Verificación fallida: Token expirado");
        return false;
    }
    
    $valid = hash_equals($_SESSION['csrf_token'], $token);
    if (!$valid) {
        error_log("[CSRF] Verificación fallida: Tokens no coinciden");
        error_log("[CSRF] Session token: " . substr($_SESSION['csrf_token'], 0, 20) . "...");
        error_log("[CSRF] Received token: " . substr($token, 0, 20) . "...");
    }
    
    return $valid;
}
    
<<<<<<< HEAD
=======
    }
    
    if (time() > $_SESSION['csrf_token_expiry']) {
        error_log("[CSRF] Verificación fallida: Token expirado");
        return false;
    }
    
    $valid = hash_equals($_SESSION['csrf_token'], $token);
    if (!$valid) {
        error_log("[CSRF] Verificación fallida: Tokens no coinciden");
        error_log("[CSRF] Session token: " . substr($_SESSION['csrf_token'], 0, 20) . "...");
        error_log("[CSRF] Received token: " . substr($token, 0, 20) . "...");
    }
    
    return $valid;
}
    
=======
    public static function verificarTokenCSRF($token) {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
            return false;
        }
        
        if (time() > $_SESSION['csrf_token_expiry']) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    /**
     * Obtiene el token CSRF actual (para usar en formularios)
     * @return string Token CSRF
     */
    public static function getTokenCSRF() {
        return self::generarTokenCSRF();
    }
    
    /**
     * Genera HTML para un campo CSRF oculto
     * @return string HTML del input hidden
     */
    public static function campoCSRF() {
        return '<input type="hidden" name="csrf_token" value="' . self::getTokenCSRF() . '">';
    }
    
    /**
     * Sanitiza una cadena para prevenir XSS
     * @param string $input Cadena a sanitizar
     * @return string Cadena sanitizada
     */
    public static function sanitizar($input) {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitiza un array completo
     * @param array $array Array a sanitizar
     * @return array Array sanitizado
     */
    public static function sanitizarArray($array) {
        if (!is_array($array)) {
            return self::sanitizar($array);
        }
        
        $resultado = [];
        foreach ($array as $key => $value) {
            $key_sanitizado = self::sanitizar($key);
            if (is_array($value)) {
                $resultado[$key_sanitizado] = self::sanitizarArray($value);
            } else {
                $resultado[$key_sanitizado] = self::sanitizar($value);
            }
        }
        return $resultado;
    }
}
?>
