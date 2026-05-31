<?php
// controlador/PageController.php
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

        renderView('home');
    }
    
    /**
     * Redirige al home con parámetro para abrir el modal de login del rol específico
     * Ejemplo: /login/paciente -> home.php?openLogin=paciente
     */
    public function loginRedirect() {
        // Obtener el rol de los parámetros de la ruta (disponible en $_GET gracias al router)
        $rol = $_GET['rol'] ?? 'paciente';
        
        // Validar que sea un rol válido
        $rolesValidos = ['paciente', 'medico', 'asistente', 'administrador'];
        if (!in_array($rol, $rolesValidos)) {
            $rol = 'paciente';
        }
        
        // Redirigir al home con parámetro en la URL para abrir el modal
        // Esta es la forma más confiable, no depende de sesiones
        header('Location: ' . APP_URL . '/?openLogin=' . $rol);
        exit();
    }
}
?>