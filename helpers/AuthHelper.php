<?php
class AuthHelper {
    public static function checkRole($roles, $redirect = true) {
        // Verificar que la sesión existe
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            if ($redirect) {
                self::redirectToLogin();
            }
            return false;
        }
        
        $userRole = $_SESSION['rol'];
        
        // Normalizar a array para comparación
        $allowedRoles = is_array($roles) ? $roles : [$roles];
        
        $hasAccess = in_array($userRole, $allowedRoles);
        
        if (!$hasAccess && $redirect) {
            self::redirectToLogin();
        }
        
        return $hasAccess;
    }

    public static function checkUserType($tipos, $redirect = true) {
        if (!isset($_SESSION['us_tipo'])) {
            if ($redirect) {
                self::redirectToLogin();
            }
            return false;
        }
        
        $userType = (int)$_SESSION['us_tipo'];
        $allowedTypes = is_array($tipos) ? $tipos : [$tipos];
        
        $hasAccess = in_array($userType, $allowedTypes);
        
        if (!$hasAccess && $redirect) {
            self::redirectToLogin();
        }
        
        return $hasAccess;
    }
    
    public static function checkRoleAndType($roles, $tipos, $redirect = true) {
        $hasRole = self::checkRole($roles, false);
        $hasType = self::checkUserType($tipos, false);
        
        $hasAccess = $hasRole && $hasType;
        
        if (!$hasAccess && $redirect) {
            self::redirectToLogin();
        }
        
        return $hasAccess;
    }
    
    public static function getLoginUrl($rol = null) {
        $rol = $rol ?? ($_SESSION['rol'] ?? '');
        
        $loginUrls = [
            'paciente' => 'login/paciente',
            'medico' => 'login/medico',
            'asistente' => 'login/asistente',
            'administrador' => 'login/administrador'
        ];
        
        $url = $loginUrls[$rol] ?? 'login';
        return APP_URL . '/' . $url;
    }
        
    public static function redirectToLogin() {
        $url = self::getLoginUrl();
        header('Location: ' . $url);
        exit();
    }
    
    public static function isAuthenticated() {
        return isset($_SESSION['usuario']) && isset($_SESSION['rol']);
    }    
   
    public static function getCurrentRole() {
        return $_SESSION['rol'] ?? null;
    }    
    
    public static function getCurrentUserType() {
        return $_SESSION['us_tipo'] ?? null;
    }
}
