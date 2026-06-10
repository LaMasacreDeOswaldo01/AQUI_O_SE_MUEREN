<?php
class PageController {    
    public function home() {
        // Si el usuario ya está logueado, redirigir a su panel
        if (isset($_SESSION['usuario']) && isset($_SESSION['rol'])) {
            $redirects = [
                'paciente' => 'panel/paciente',
                'medico' => 'panel/medico',
                'asistente' => 'panel/asistente',
                'administrador' => 'panel/administrador'
            ];
            if (isset($redirects[$_SESSION['rol']])) {
                redirect($redirects[$_SESSION['rol']]);
                return;
            }
        }
        
        // Si hay una solicitud de login pendiente (después de registro o clic directo)
        if (isset($_SESSION['open_login'])) {
            $rol = $_SESSION['open_login'];
            unset($_SESSION['open_login']);
            // Pasar el rol a la vista mediante variable global
            echo '<script>var openLoginRol = "' . addslashes($rol) . '";</script>';
        }
        
        renderView('home');
    }
    
    public function loginRedirect() {
        // Obtener el rol de los parámetros de la ruta (disponible en $_GET gracias al router)
        $rol = $_GET['rol'] ?? 'paciente';
        
        // Validar que sea un rol válido
        $rolesValidos = ['paciente', 'medico', 'asistente', 'administrador'];
        if (!in_array($rol, $rolesValidos)) {
            $rol = 'paciente';
        }
        
        header('Location: ' . APP_URL . '/?openLogin=' . $rol);
        exit();
    }
}
?>