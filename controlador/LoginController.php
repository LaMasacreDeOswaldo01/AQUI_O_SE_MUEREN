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
    
    public function LoginPaciente() {
        // Procesar login de paciente
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::error('Método no permitido', 'method_not_allowed', [], 405);
            return;
        }

        $user = trim($_POST['user'] ?? '');
        $pass = $_POST['pass'] ?? '';

        if (empty($user) || empty($pass)) {
            ApiResponse::error('Todos los campos son obligatorios', 'validation_error');
            return;
        }

        $login = new LoginPaciente();
        $resultado = $login->Loguearse($user, $pass);

        if (!empty($resultado)) {
            $usuario = $resultado[0];
            $_SESSION['usuario'] = $usuario->id_paciente;
            $_SESSION['us_tipo'] = $usuario->paciente_tipo;
            $_SESSION['nombre_us'] = $usuario->nombre_paciente;
            $_SESSION['rol'] = 'paciente';

            $login->actualizarUltimoAcceso($usuario->id_paciente);
            
            ApiResponse::success(['redirect' => 'panel/paciente'], 'login_success', 'Bienvenido');
        } else {
            ApiResponse::error('Cédula o contraseña incorrecta', 'auth_error', [], 401);
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