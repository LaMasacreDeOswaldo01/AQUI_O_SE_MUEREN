<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
// controlador/PageController.php
class PageController {
    
    public function home() {
        // Si el usuario ya está logueado, redirigir a su panel
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
class PageController {
    
    public function home() {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        
        // Si hay una solicitud de login pendiente (después de registro o clic directo)
        if (isset($_SESSION['open_login'])) {
            $rol = $_SESSION['open_login'];
            unset($_SESSION['open_login']);
            // Pasar el rol a la vista mediante variable global
            echo '<script>var openLoginRol = "' . addslashes($rol) . '";</script>';
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
<<<<<<< HEAD
=======
        renderView('home');
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        
        // Si hay una solicitud de login pendiente (después de registro o clic directo)
        if (isset($_SESSION['open_login'])) {
            $rol = $_SESSION['open_login'];
            unset($_SESSION['open_login']);
            // Pasar el rol a la vista mediante variable global
            echo '<script>var openLoginRol = "' . addslashes($rol) . '";</script>';
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
<<<<<<< HEAD
        
        // Validar que sea un rol válido
        $rolesValidos = ['paciente', 'medico', 'asistente', 'administrador'];
        if (!in_array($rol, $rolesValidos)) {
            $rol = 'paciente';
        }
        
        // Redirigir al home con parámetro en la URL para abrir el modal
        // Esta es la forma más confiable, no depende de sesiones
        header('Location: ' . APP_URL . '/?openLogin=' . $rol);
        exit();
=======

        // Establecer una variable de sesión para que home.php sepa qué modal abrir
        $_SESSION['open_login'] = $rol;

        // Finalmente, cargar la vista home, que al iniciarse leerá esta variable
        $this->home();
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    }
}
?>