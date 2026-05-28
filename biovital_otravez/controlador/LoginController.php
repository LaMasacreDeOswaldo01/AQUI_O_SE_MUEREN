
<?php
class LoginController {
    
    public function index() {
        // Mostrar la página de login
        if (file_exists(VIEW_PATH . '/login/index.php')) {
            require_once VIEW_PATH . '/login/index.php';
        } else {
            // Si no existe la vista, mostrar tu index2.php
            require_once ROOT_PATH . '/index2.php';
        }
    }
    
    public function loginPaciente() {
        // Procesar login de paciente
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Aquí va tu lógica de autenticación
            echo "Procesando login de paciente";
        }
    }
    
    public function loginMedico() {
        echo "Procesando login de médico";
    }
    
    public function loginAsistente() {
        echo "Procesando login de asistente";
    }
    
    public function loginAdministrador() {
        echo "Procesando login de administrador";
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header('Location: ' . APP_URL);
        exit();
    }
}
?>