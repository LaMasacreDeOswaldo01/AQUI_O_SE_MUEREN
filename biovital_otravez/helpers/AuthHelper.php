<?php

class AuthHelper {
    
    /**
     * Verifica que el usuario tenga un rol específico
     * @param string|array $roles Rol o array de roles permitidos
     * @param bool $redirect Si es true, redirige si no tiene permiso
     * @return bool True si tiene permiso, False si no
     */
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
    
    /**
     * Verifica que el usuario tenga un tipo específico (us_tipo)
     * @param int|array $tipos Tipo o array de tipos permitidos
     * @param bool $redirect Si es true, redirige si no tiene permiso
     * @return bool True si tiene permiso, False si no
     */
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
    
    /**
     * Verifica rol y tipo simultáneamente (para máxima seguridad)
     * @param string|array $roles Rol o array de roles permitidos
     * @param int|array $tipos Tipo o array de tipos permitidos
     * @param bool $redirect Si es true, redirige si no tiene permiso
     * @return bool True si tiene permiso, False si no
     */
    public static function checkRoleAndType($roles, $tipos, $redirect = true) {
        $hasRole = self::checkRole($roles, false);
        $hasType = self::checkUserType($tipos, false);
        
        $hasAccess = $hasRole && $hasType;
        
        if (!$hasAccess && $redirect) {
            self::redirectToLogin();
        }
        
        return $hasAccess;
    }
    
    /**
     * Obtiene la URL de login según el rol
     * @param string $rol Rol del usuario
     * @return string URL de login correspondiente
     */
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
    
    /**
     * Redirige al login correspondiente
     */
    public static function redirectToLogin() {
        $url = self::getLoginUrl();
        header('Location: ' . $url);
        exit();
    }
    
    /**
     * Verifica si el usuario está autenticado
     * @return bool
     */
    public static function isAuthenticated() {
        return isset($_SESSION['usuario']) && isset($_SESSION['rol']);
    }
    
    /**
     * Obtiene el rol del usuario actual
     * @return string|null
     */
    public static function getCurrentRole() {
        return $_SESSION['rol'] ?? null;
    }
    
    /**
     * Obtiene el tipo del usuario actual
     * @return int|null
     */
    public static function getCurrentUserType() {
        return $_SESSION['us_tipo'] ?? null;
    }
}
