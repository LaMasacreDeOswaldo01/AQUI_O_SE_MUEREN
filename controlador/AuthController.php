<?php
class AuthController {   
    public function showLoginPaciente() {
        header('Location: ' . APP_URL . '?openLogin=paciente');
        exit();
    }
    
    public function showLoginMedico() {
        header('Location: ' . APP_URL . '?openLogin=medico');
        exit();
    }
    
    public function showLoginAsistente() {
        header('Location: ' . APP_URL . '?openLogin=asistente');
        exit();
    }
    
    public function showLoginAdministrador() {
        header('Location: ' . APP_URL . '?openLogin=administrador');
        exit();
    }
    
    public function login() {
        // Obtener datos del formulario
        $user = trim($_POST['user'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $rol = $_POST['rol'] ?? '';
        
        error_log("[AuthController] Intento de login - User: $user, Rol: $rol");
        
        // Validar que los campos no estén vacíos
        if (empty($user) || empty($pass) || empty($rol)) {
            error_log("[AuthController] Error: Campos vacíos");
            
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'error' => 'Todos los campos son obligatorios']);
            } else {
                $this->redirectWithError('Todos los campos son obligatorios', $rol);
            }
            return;
        }
        
        // Mapeo de roles a clases y campos
        $loginMap = [
            'paciente' => [
                'class' => 'LoginPaciente', 
                'idField' => 'id_paciente', 
                'tipoField' => 'paciente_tipo', 
                'nameField' => 'nombre_paciente'
            ],
            'medico' => [
                'class' => 'LoginMedico', 
                'idField' => 'id_medico', 
                'tipoField' => 'medico_tipo', 
                'nameField' => 'nombre_medico'
            ],
            'asistente' => [
                'class' => 'LoginAsistente', 
                'idField' => 'id_asistente', 
                'tipoField' => 'asistente_tipo', 
                'nameField' => 'nombre_asistente'
            ],
            'administrador' => [
                'class' => 'LoginAdministrador', 
                'idField' => 'id_administrador', 
                'tipoField' => 'administrador_tipo', 
                'nameField' => 'nombre_administrador'
            ]
        ];
        
        // Validar rol
        if (!isset($loginMap[$rol])) {
            error_log("[AuthController] Error: Rol inválido - $rol");
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'error' => 'Rol inválido']);
            } else {
                $this->redirectWithError('Rol inválido', $rol);
            }
            return;
        }
        
        $map = $loginMap[$rol];
        
        // Verificar que la clase existe
        if (!class_exists($map['class'])) {
            error_log("[AuthController] Error: Clase no encontrada - " . $map['class']);
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'error' => 'Error interno del servidor']);
            } else {
                $this->redirectWithError('Error interno del servidor', $rol);
            }
            return;
        }
        
        // Intentar login
        $login = new $map['class']();
        $login->Loguearse($user, $pass);
        
        // Verificar si el login fue exitoso
        if (!empty($login->objetos)) {
            foreach ($login->objetos as $objeto) {
                // Guardar datos en sesión
                $_SESSION['usuario'] = $objeto->{$map['idField']};
                $_SESSION['us_tipo'] = $objeto->{$map['tipoField']};
                $_SESSION['nombre_us'] = $objeto->{$map['nameField']};
                $_SESSION['rol'] = $rol;
                
                // Guardar avatar si existe
                $avatarField = 'avatar_' . $rol;
                if (isset($objeto->$avatarField)) {
                    $_SESSION['avatar'] = APP_URL . '/img/' . $objeto->$avatarField;
                }
                
                // Guardar cédula para mostrar en el perfil
                $cedulaField = 'cedula_' . $rol;
                if (isset($objeto->$cedulaField)) {
                    $_SESSION['cedula'] = $objeto->$cedulaField;
                }
                
                // Guardar teléfono
                $telefonoField = 'telefono_' . $rol;
                if (isset($objeto->$telefonoField)) {
                    $_SESSION['telefono'] = $objeto->$telefonoField;
                }
                
                // Actualizar último acceso
                if (method_exists($login, 'actualizarUltimoAcceso')) {
                    $login->actualizarUltimoAcceso($objeto->{$map['idField']});
                }
            }
            
            error_log("[AuthController] Login exitoso - Usuario ID: " . $_SESSION['usuario'] . ", Rol: $rol");
            
            // Construir URL de redirección
            $redirectUrl = 'panel/' . $rol;
            
            if ($this->isAjax()) {
                jsonResponse(['success' => true, 'redirect' => $redirectUrl]);
            } else {
                redirect($redirectUrl);
            }
        } else {
            // Login fallido
            error_log("[AuthController] Login fallido - User: $user, Rol: $rol");
            
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'error' => 'Cédula o contraseña incorrecta']);
            } else {
                $this->redirectWithError('Cédula o contraseña incorrecta', $rol);
            }
        }
    }
    
    /**
     * Redirige al home con un mensaje de error - CORREGIDO
     */
    private function redirectWithError($errorMessage, $rol) {
        // Guardar el mensaje de error en sesión para mostrarlo al volver
        $_SESSION['login_error'] = $errorMessage;
        $_SESSION['login_rol'] = $rol;
        
        // CORRECCIÓN: Asegurar que APP_URL esté definido y no tenga barras dobles
        $baseUrl = rtrim(APP_URL, '/');
        $redirectUrl = $baseUrl . '/?openLogin=' . $rol . '&error=1';
        
        error_log("[AuthController] Redirigiendo a: " . $redirectUrl);
        
        header('Location: ' . $redirectUrl);
        exit();
    }
    
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la sesión
        session_destroy();
        
        // Redirigir al inicio
        redirect('');
    }
    
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}