<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
// controlador/RegistroController.php
// Controlador para manejar el registro de todos los tipos de usuarios

class RegistroController {
    
    // ==================== VISTAS DE REGISTRO ====================
    
    /**
     * Muestra el formulario de registro para paciente
     * GET /registro/paciente
     */
<<<<<<< HEAD
=======
=======
class RegistroController {
    
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
class RegistroController {
    
    // ==================== VISTAS DE REGISTRO ====================     
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function showRegistroPaciente() {
        renderView('registro_pac');
    }
    
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    /**
     * Muestra el formulario de registro para médico
     * GET /registro/medico
     */
<<<<<<< HEAD
=======
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function showRegistroMedico() {
        renderView('med_registro');
    }
    
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    /**
     * Muestra el formulario de registro para asistente
     * GET /registro/asistente
     */
<<<<<<< HEAD
=======
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function showRegistroAsistente() {
        renderView('registro_asistente');
    }
    
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    /**
     * Muestra el formulario de registro para administrador
     * GET /registro/administrador
     */
<<<<<<< HEAD
=======
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function showRegistroAdministrador() {
        renderView('registro_administrador');
    }
    
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    // ==================== API DE REGISTRO ====================
    
    /**
     * API: Crear un nuevo paciente
     * POST /api/registro/paciente
     */
<<<<<<< HEAD
=======
    public function crearPaciente() {
        // Verificar token CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            ApiResponse::csrfError('Token CSRF inválido. Por favor, recargue la página.');
            return;
        }
        
        // ==================== OBTENER Y LIMPIAR DATOS ====================
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $cedula = trim($_POST['cedula'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $sexo = $_POST['sexo'] ?? '';
        $adicional = trim($_POST['adicional'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $confirm_pass = $_POST['confirm_pass'] ?? '';
        
        // ==================== OBTENER UBICACIÓN COMPLETA ====================
        $direccion_completa = $this->construirDireccionCompleta($_POST);
        
        // ==================== VALIDACIONES ====================
        $errores = $this->validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                                $telefono, $correo, $sexo, $pass, $confirm_pass, 
                                                $direccion_completa);
        
        // Validar campos específicos de ubicación
        if (empty($_POST['estado'] ?? '')) {
            $errores['estado'] = 'Debe seleccionar un estado';
        }
        if (empty($_POST['ciudad'] ?? '')) {
            $errores['ciudad'] = 'Debe seleccionar una ciudad';
        }
        
        // Si hay errores de validación, retornarlos
        if (!empty($errores)) {
            ApiResponse::validationError($errores, 'Por favor, corrija los siguientes errores');
            return;
        }
        
        // ==================== CREAR EL PACIENTE ====================
        $paciente = new Paciente();
        
        // Verificar si ya existe
        if ($paciente->existe($cedula, $correo)) {
            ApiResponse::error('Ya existe un usuario con esta cédula o correo electrónico', 'duplicate_entry', [], 409);
            return;
        }
        
        $password_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $resultado = $paciente->crear([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'direccion' => $direccion_completa,
            'correo' => $correo,
            'sexo' => $sexo,
            'adicional' => $adicional,
            'password_hash' => $password_hash,
            'tipo' => 1, // Tipo 1 = Paciente
            'avatar' => 'avatarDES.jpg'
        ]);
        
        // ==================== RESPUESTA ====================
        if ($resultado['success']) {
            ApiResponse::created([
                'redirect' => APP_URL . '/login/paciente',
                'user_id' => $resultado['id'],
                'nombre_completo' => $nombre . ' ' . $apellidos
            ], "¡Cuenta de paciente creada exitosamente! Ahora puede iniciar sesión.");
        } else {
            $errorMessage = $this->getErrorMessage($resultado['message']);
            ApiResponse::error($errorMessage, 'creation_error', [], 500);
        }
    }
    
    /**
     * API: Crear un nuevo médico
     * POST /api/registro/medico
     */
=======
    // ==================== API DE REGISTRO ====================    
    
   public function crearPaciente() {
    // Verificar token CSRF
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!Security::verificarTokenCSRF($csrf_token)) {
        ApiResponse::csrfError('Token CSRF inválido. Por favor, recargue la página.');
        return;
    }
    
    // ==================== OBTENER Y LIMPIAR DATOS ====================
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $cedula = trim($_POST['cedula'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $sexo = $_POST['sexo'] ?? '';
    $tipo_sangre = $_POST['tipo_sangre'] ?? '';
    $adicional = trim($_POST['adicional'] ?? '');
    $pass = $_POST['pass'] ?? '';
    $confirm_pass = $_POST['confirm_pass'] ?? '';
    
    // ==================== OBTENER RESPUESTAS DE SEGURIDAD ====================
    $pregunta1 = trim($_POST['pregunta1'] ?? '');
    $respuesta1 = trim($_POST['respuesta1'] ?? '');
    $pregunta2 = trim($_POST['pregunta2'] ?? '');
    $respuesta2 = trim($_POST['respuesta2'] ?? '');
    $pregunta3 = trim($_POST['pregunta3'] ?? '');
    $respuesta3 = trim($_POST['respuesta3'] ?? '');
    
    // ==================== OBTENER UBICACIÓN COMPLETA ====================
    $direccion_completa = $this->construirDireccionCompleta($_POST);
    
    // ==================== VALIDACIONES ====================
    $errores = $this->validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                            $telefono, $correo, $sexo, $pass, $confirm_pass, 
                                            $direccion_completa);
    
    // Validar campos específicos de ubicación
    if (empty($_POST['estado'] ?? '')) {
        $errores['estado'] = 'Debe seleccionar un estado';
    }
    if (empty($_POST['ciudad'] ?? '')) {
        $errores['ciudad'] = 'Debe seleccionar una ciudad';
    }
    
    // Si hay errores de validación, retornarlos
    if (!empty($errores)) {
        ApiResponse::validationError($errores, 'Por favor, corrija los siguientes errores');
        return;
    }
    
    // ==================== CREAR EL PACIENTE ====================
    $paciente = new Paciente();
    
    // Verificar si ya existe
    if ($paciente->existe($cedula, $correo)) {
        ApiResponse::error('Ya existe un usuario con esta cédula o correo electrónico', 'duplicate_entry', [], 409);
        return;
    }
    
    $password_hash = password_hash($pass, PASSWORD_DEFAULT);
    
    $resultado = $paciente->crear([
        'nombre' => $nombre,
        'apellidos' => $apellidos,
        'fecha_nacimiento' => $fecha_nacimiento,
        'cedula' => $cedula,
        'telefono' => $telefono,
        'direccion' => $direccion_completa,
        'correo' => $correo,
        'sexo' => $sexo,
        'tipo_sangre' => $tipo_sangre,
        'adicional' => $adicional,
        'password_hash' => $password_hash,
        'tipo' => 1, // Tipo 1 = Paciente
        'avatar' => 'avatarDES.jpg'
    ]);
    
    // ==================== RESPUESTA ====================
    if ($resultado['success']) {
        // Guardar respuestas de seguridad
        $this->guardarRespuestasSeguridad($resultado['id'], $pregunta1, $respuesta1, $pregunta2, $respuesta2, $pregunta3, $respuesta3);
        
        ApiResponse::created([
            'redirect' => APP_URL . '/login/paciente',
            'user_id' => $resultado['id'],
            'nombre_completo' => $nombre . ' ' . $apellidos
        ], "¡Cuenta de paciente creada exitosamente! Ahora puede iniciar sesión.");
    } else {
        $errorMessage = $this->getErrorMessage($resultado['message']);
        ApiResponse::error($errorMessage, 'creation_error', [], 500);
    }
}
    
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function crearMedico() {
        // Verificar token CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            ApiResponse::csrfError('Token CSRF inválido. Por favor, recargue la página.');
            return;
        }
        
        // ==================== OBTENER Y LIMPIAR DATOS ====================
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $cedula = trim($_POST['cedula'] ?? '');
<<<<<<< HEAD
=======
        $mpps_registro = trim($_POST['mpps_registro'] ?? '');
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        $telefono = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $sexo = $_POST['sexo'] ?? '';
        $adicional = trim($_POST['adicional'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $confirm_pass = $_POST['confirm_pass'] ?? '';
        
<<<<<<< HEAD
=======
        // ==================== OBTENER RESPUESTAS DE SEGURIDAD ====================
        $pregunta1 = trim($_POST['pregunta1'] ?? '');
        $respuesta1 = trim($_POST['respuesta1'] ?? '');
        $pregunta2 = trim($_POST['pregunta2'] ?? '');
        $respuesta2 = trim($_POST['respuesta2'] ?? '');
        $pregunta3 = trim($_POST['pregunta3'] ?? '');
        $respuesta3 = trim($_POST['respuesta3'] ?? '');
        
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        // ==================== OBTENER UBICACIÓN COMPLETA ====================
        $direccion_completa = $this->construirDireccionCompleta($_POST);
        
        // ==================== VALIDACIONES ====================
        $errores = $this->validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                                $telefono, $correo, $sexo, $pass, $confirm_pass, 
                                                $direccion_completa);
        
        // Validar campos específicos de ubicación
        if (empty($_POST['estado'] ?? '')) {
            $errores['estado'] = 'Debe seleccionar un estado';
        }
        if (empty($_POST['ciudad'] ?? '')) {
            $errores['ciudad'] = 'Debe seleccionar una ciudad';
        }
        
        // Si hay errores de validación, retornarlos
        if (!empty($errores)) {
            ApiResponse::validationError($errores, 'Por favor, corrija los siguientes errores');
            return;
        }
        
        // ==================== CREAR EL MÉDICO ====================
        $medico = new Medico();
        
        // Verificar si ya existe
        if ($this->medicoExiste($cedula, $correo)) {
            ApiResponse::error('Ya existe un médico con esta cédula o correo electrónico', 'duplicate_entry', [], 409);
            return;
        }
        
        $password_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $resultado = $medico->crear([
<<<<<<< HEAD
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'direccion' => $direccion_completa,
            'correo' => $correo,
            'sexo' => $sexo,
            'adicional' => $adicional,
            'password_hash' => $password_hash,
            'tipo' => 2, // Tipo 2 = Médico
            'avatar' => 'avatarDES.jpg'
=======
             'nombre' => $nombre,
    'apellidos' => $apellidos,
    'fecha_nacimiento' => $fecha_nacimiento,
    'cedula' => $cedula,
    'mpps_registro' => $mpps_registro,
    'telefono' => $telefono,
    'direccion' => $direccion_completa,
    'correo' => $correo,
    'sexo' => $sexo,
    'adicional' => $adicional,
    'password_hash' => $password_hash,
    'tipo' => 2, 
    'avatar' => 'avatarDES.jpg'
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        ]);
        
        // ==================== RESPUESTA ====================
        if ($resultado['success']) {
<<<<<<< HEAD
=======
            // Guardar respuestas de seguridad
            $this->guardarRespuestasSeguridad($resultado['id'], $pregunta1, $respuesta1, $pregunta2, $respuesta2, $pregunta3, $respuesta3);
            
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            ApiResponse::created([
                'redirect' => APP_URL . '/login/medico',
                'user_id' => $resultado['id'],
                'nombre_completo' => $nombre . ' ' . $apellidos
            ], "¡Cuenta de médico creada exitosamente! Ahora puede iniciar sesión.");
        } else {
            $errorMessage = $this->getErrorMessage($resultado['message']);
            ApiResponse::error($errorMessage, 'creation_error', [], 500);
        }
<<<<<<< HEAD
    }
    
    /**
     * API: Crear un nuevo asistente
     * POST /api/registro/asistente
     */
=======
    }    
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function crearAsistente() {
        // Verificar token CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            ApiResponse::csrfError('Token CSRF inválido. Por favor, recargue la página.');
            return;
        }
        
        // ==================== OBTENER Y LIMPIAR DATOS ====================
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $cedula = trim($_POST['cedula'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $sexo = $_POST['sexo'] ?? '';
        $adicional = trim($_POST['adicional'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $confirm_pass = $_POST['confirm_pass'] ?? '';
        
<<<<<<< HEAD
=======
        // ==================== OBTENER RESPUESTAS DE SEGURIDAD ====================
        $pregunta1 = trim($_POST['pregunta1'] ?? '');
        $respuesta1 = trim($_POST['respuesta1'] ?? '');
        $pregunta2 = trim($_POST['pregunta2'] ?? '');
        $respuesta2 = trim($_POST['respuesta2'] ?? '');
        $pregunta3 = trim($_POST['pregunta3'] ?? '');
        $respuesta3 = trim($_POST['respuesta3'] ?? '');
        
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        // ==================== OBTENER UBICACIÓN COMPLETA ====================
        $direccion_completa = $this->construirDireccionCompleta($_POST);
        
        // ==================== VALIDACIONES ====================
        $errores = $this->validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                                $telefono, $correo, $sexo, $pass, $confirm_pass, 
                                                $direccion_completa);
        
        // Validar campos específicos de ubicación
        if (empty($_POST['estado'] ?? '')) {
            $errores['estado'] = 'Debe seleccionar un estado';
        }
        if (empty($_POST['ciudad'] ?? '')) {
            $errores['ciudad'] = 'Debe seleccionar una ciudad';
        }
        
        // Si hay errores de validación, retornarlos
        if (!empty($errores)) {
            ApiResponse::validationError($errores, 'Por favor, corrija los siguientes errores');
            return;
        }
        
        // ==================== CREAR EL ASISTENTE ====================
        $asistente = new Asistente();
        
        // Verificar si ya existe
        if ($this->asistenteExiste($cedula, $correo)) {
            ApiResponse::error('Ya existe un asistente con esta cédula o correo electrónico', 'duplicate_entry', [], 409);
            return;
        }
        
        $password_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $resultado = $asistente->crear([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'direccion' => $direccion_completa,
            'correo' => $correo,
            'sexo' => $sexo,
            'adicional' => $adicional,
            'password_hash' => $password_hash,
            'tipo' => 3, // Tipo 3 = Asistente
            'avatar' => 'avatarDES.jpg'
        ]);
        
        // ==================== RESPUESTA ====================
        if ($resultado['success']) {
<<<<<<< HEAD
=======
            // Guardar respuestas de seguridad
            $this->guardarRespuestasSeguridad($resultado['id'], $pregunta1, $respuesta1, $pregunta2, $respuesta2, $pregunta3, $respuesta3);
            
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            ApiResponse::created([
                'redirect' => APP_URL . '/login/asistente',
                'user_id' => $resultado['id'],
                'nombre_completo' => $nombre . ' ' . $apellidos
            ], "¡Cuenta de asistente creada exitosamente! Ahora puede iniciar sesión.");
        } else {
            $errorMessage = $this->getErrorMessage($resultado['message']);
            ApiResponse::error($errorMessage, 'creation_error', [], 500);
        }
    }
    
<<<<<<< HEAD
    /**
     * API: Crear un nuevo administrador
     * POST /api/registro/administrador
     */
    public function crearAdministrador() {
        // Verificar token CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            ApiResponse::csrfError('Token CSRF inválido. Por favor, recargue la página.');
            return;
        }
        
        // ==================== OBTENER Y LIMPIAR DATOS ====================
=======
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    public function crearPaciente() {
        // Verificar token CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            ApiResponse::csrfError('Token CSRF inválido. Por favor, recargue la página.');
            return;
        }
        
<<<<<<< HEAD
        // ==================== OBTENER Y LIMPIAR DATOS ====================
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $cedula = trim($_POST['cedula'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
<<<<<<< HEAD
=======
<<<<<<< HEAD
        $correo = trim($_POST['correo'] ?? '');
        $sexo = $_POST['sexo'] ?? '';
        $adicional = trim($_POST['adicional'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $confirm_pass = $_POST['confirm_pass'] ?? '';
        
        // ==================== OBTENER UBICACIÓN COMPLETA ====================
        $direccion_completa = $this->construirDireccionCompleta($_POST);
        
        // ==================== VALIDACIONES ====================
        $errores = $this->validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                                $telefono, $correo, $sexo, $pass, $confirm_pass, 
                                                $direccion_completa);
        
        // Validar campos específicos de ubicación
        if (empty($_POST['estado'] ?? '')) {
            $errores['estado'] = 'Debe seleccionar un estado';
        }
        if (empty($_POST['ciudad'] ?? '')) {
            $errores['ciudad'] = 'Debe seleccionar una ciudad';
        }
        
        // Si hay errores de validación, retornarlos
        if (!empty($errores)) {
            ApiResponse::validationError($errores, 'Por favor, corrija los siguientes errores');
            return;
        }
        
        // ==================== CREAR EL ADMINISTRADOR ====================
        $administrador = new Administrador();
        
        // Verificar si ya existe
        if ($this->administradorExiste($cedula, $correo)) {
            ApiResponse::error('Ya existe un administrador con esta cédula o correo electrónico', 'duplicate_entry', [], 409);
=======
        $direccion = trim($_POST['direccion'] ?? '');
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        $correo = trim($_POST['correo'] ?? '');
        $sexo = $_POST['sexo'] ?? '';
        $adicional = trim($_POST['adicional'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $confirm_pass = $_POST['confirm_pass'] ?? '';
        
        // ==================== OBTENER UBICACIÓN COMPLETA ====================
        $direccion_completa = $this->construirDireccionCompleta($_POST);
        
        // ==================== VALIDACIONES ====================
        $errores = $this->validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                                $telefono, $correo, $sexo, $pass, $confirm_pass, 
                                                $direccion_completa);
        
        // Validar campos específicos de ubicación
        if (empty($_POST['estado'] ?? '')) {
            $errores['estado'] = 'Debe seleccionar un estado';
        }
        if (empty($_POST['ciudad'] ?? '')) {
            $errores['ciudad'] = 'Debe seleccionar una ciudad';
        }
        
        // Si hay errores de validación, retornarlos
        if (!empty($errores)) {
<<<<<<< HEAD
            ApiResponse::validationError($errores, 'Por favor, corrija los siguientes errores');
            return;
        }
        
        // ==================== CREAR EL PACIENTE ====================
        $paciente = new Paciente();
        
        // Verificar si ya existe
        if ($paciente->existe($cedula, $correo)) {
            ApiResponse::error('Ya existe un usuario con esta cédula o correo electrónico', 'duplicate_entry', [], 409);
=======
            jsonResponse(['success' => false, 'message' => implode(', ', $errores)]);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            return;
        }
        
        $password_hash = password_hash($pass, PASSWORD_DEFAULT);
<<<<<<< HEAD
=======
<<<<<<< HEAD
        
        $resultado = $administrador->crear([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'direccion' => $direccion_completa,
            'correo' => $correo,
            'sexo' => $sexo,
            'adicional' => $adicional,
            'password_hash' => $password_hash,
            'tipo' => 4, // Tipo 4 = Administrador
            'avatar' => 'avatarDES.jpg'
        ]);
        
        // ==================== RESPUESTA ====================
        if ($resultado['success']) {
            ApiResponse::created([
                'redirect' => APP_URL . '/login/administrador',
                'user_id' => $resultado['id'],
                'nombre_completo' => $nombre . ' ' . $apellidos
            ], "¡Cuenta de administrador creada exitosamente! Ahora puede iniciar sesión.");
        } else {
            $errorMessage = $this->getErrorMessage($resultado['message']);
            ApiResponse::error($errorMessage, 'creation_error', [], 500);
        }
    }
    
    // ==================== MÉTODOS AUXILIARES PRIVADOS ====================
    
    /**
     * Construye la dirección completa a partir de los datos del formulario
     * @param array $data Datos del formulario
     * @return string Dirección completa formateada
     */
    private function construirDireccionCompleta($data) {
        $estado_nombre = $this->getNombreEstado($data['estado'] ?? '');
        $ciudad_nombre = $this->getNombreCiudad($data['ciudad'] ?? '');
        $municipio_nombre = $this->getNombreMunicipio($data['municipio'] ?? '');
        $parroquia_nombre = $this->getNombreParroquia($data['parroquia'] ?? '');
        $direccion_detallada = trim($data['direccion'] ?? '');
        
        $partes = [];
        if ($estado_nombre && $estado_nombre !== 'Seleccione un estado...') {
            $partes[] = $estado_nombre;
        }
        if ($ciudad_nombre && $ciudad_nombre !== 'Seleccione una ciudad...') {
            $partes[] = $ciudad_nombre;
        }
        if ($municipio_nombre && $municipio_nombre !== 'Seleccione un municipio...') {
            $partes[] = $municipio_nombre;
        }
        if ($parroquia_nombre && $parroquia_nombre !== 'Seleccione una parroquia...') {
            $partes[] = $parroquia_nombre;
        }
        
        $ubicacion = implode(', ', $partes);
        
        if ($direccion_detallada) {
            return $ubicacion ? $ubicacion . ' - ' . $direccion_detallada : $direccion_detallada;
        }
        
        return $ubicacion;
    }
    
    /**
     * Valida los datos comunes del registro
     * @return array Array de errores
     */
    private function validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                          $telefono, $correo, $sexo, $pass, $confirm_pass, $direccion) {
        $errores = [];
        
        if (empty($nombre)) $errores['nombre'] = 'El nombre es requerido';
        if (empty($apellidos)) $errores['apellidos'] = 'Los apellidos son requeridos';
        if (empty($fecha_nacimiento)) $errores['fecha_nacimiento'] = 'La fecha de nacimiento es requerida';
        if (empty($cedula)) $errores['cedula'] = 'La cédula es requerida';
        if (empty($telefono)) $errores['telefono'] = 'El teléfono es requerido';
        if (empty($correo)) $errores['correo'] = 'El correo electrónico es requerido';
        if (empty($sexo)) $errores['sexo'] = 'El sexo es requerido';
        if (empty($pass)) $errores['pass'] = 'La contraseña es requerida';
        
        // Validar formato de correo
        if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = 'Ingrese un correo electrónico válido';
        }
        
        // Validar contraseña
        if (!empty($pass) && strlen($pass) < 6) {
            $errores['pass'] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Validar que las contraseñas coincidan
        if (!empty($pass) && $pass !== $confirm_pass) {
            $errores['confirm_pass'] = 'Las contraseñas no coinciden';
        }
        
        return $errores;
    }
    
    /**
     * Obtiene el mensaje de error correspondiente
     * @param string $codigo Código de error del modelo
     * @return string Mensaje de error amigable
     */
    private function getErrorMessage($codigo) {
        $mensajes = [
            'existe' => 'Ya existe un usuario con esta cédula o correo electrónico',
            'error_bd' => 'Error en la base de datos. Por favor, intente más tarde',
            'error_login' => 'Cuenta creada pero hubo un problema con el acceso. Contacte al administrador',
            'error_exception' => 'Error interno del servidor. Por favor, intente más tarde',
            'error_actualizacion' => 'Error al guardar los datos',
        ];
        
        return $mensajes[$codigo] ?? 'Error al crear la cuenta. Por favor, intente nuevamente';
    }
    
    /**
     * Verifica si ya existe un médico con la cédula o correo
     */
    private function medicoExiste($cedula, $correo) {
        try {
            $db = new Conexion();
            $sql = "SELECT id_medico FROM registro_medico WHERE cedula_medico = :cedula OR correo_medico = :correo";
            $query = $db->pdo->prepare($sql);
            $query->execute([':cedula' => $cedula, ':correo' => $correo]);
            return $query->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error en medicoExiste: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si ya existe un asistente con la cédula o correo
     */
    private function asistenteExiste($cedula, $correo) {
        try {
            $db = new Conexion();
            $sql = "SELECT id_asistente FROM registro_asistente WHERE cedula_asistente = :cedula OR correo_asistente = :correo";
            $query = $db->pdo->prepare($sql);
            $query->execute([':cedula' => $cedula, ':correo' => $correo]);
            return $query->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error en asistenteExiste: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica si ya existe un administrador con la cédula o correo
     */
    private function administradorExiste($cedula, $correo) {
        try {
            $db = new Conexion();
            $sql = "SELECT id_administrador FROM registro_administrador WHERE cedula_administrador = :cedula OR correo_administrador = :correo";
            $query = $db->pdo->prepare($sql);
            $query->execute([':cedula' => $cedula, ':correo' => $correo]);
            return $query->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error en administradorExiste: " . $e->getMessage());
            return false;
        }
    }
    
    // ==================== MÉTODOS PARA OBTENER NOMBRES DE UBICACIÓN ====================
    
    /**
     * Obtiene el nombre de un estado por su ID
     */
    private function getNombreEstado($id_estado) {
        if (!$id_estado) return '';
        
        try {
            $db = new Conexion();
            $sql = "SELECT estado FROM estados WHERE id_estado = :id";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id' => $id_estado]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado->estado : '';
        } catch(PDOException $e) {
            error_log("Error en getNombreEstado: " . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Obtiene el nombre de una ciudad por su ID
     */
    private function getNombreCiudad($id_ciudad) {
        if (!$id_ciudad) return '';
        
        try {
            $db = new Conexion();
            $sql = "SELECT ciudad FROM ciudades WHERE id_ciudad = :id";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id' => $id_ciudad]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado->ciudad : '';
        } catch(PDOException $e) {
            error_log("Error en getNombreCiudad: " . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Obtiene el nombre de un municipio por su ID
     */
    private function getNombreMunicipio($id_municipio) {
        if (!$id_municipio) return '';
        
        try {
            $db = new Conexion();
            $sql = "SELECT municipio FROM municipios WHERE id_municipio = :id";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id' => $id_municipio]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado->municipio : '';
        } catch(PDOException $e) {
            error_log("Error en getNombreMunicipio: " . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Obtiene el nombre de una parroquia por su ID
     */
    private function getNombreParroquia($id_parroquia) {
        if (!$id_parroquia) return '';
        
        try {
            $db = new Conexion();
            $sql = "SELECT parroquia FROM parroquias WHERE id_parroquia = :id";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id' => $id_parroquia]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado->parroquia : '';
        } catch(PDOException $e) {
            error_log("Error en getNombreParroquia: " . $e->getMessage());
            return '';
=======
        $avatar = 'avatarDES.jpg';
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        
        $resultado = $paciente->crear([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'direccion' => $direccion_completa,
            'correo' => $correo,
            'sexo' => $sexo,
            'adicional' => $adicional,
            'password_hash' => $password_hash,
            'tipo' => 1, // Tipo 1 = Paciente
            'avatar' => 'avatarDES.jpg'
        ]);
        
        // ==================== RESPUESTA ====================
        if ($resultado['success']) {
            ApiResponse::created([
                'redirect' => APP_URL . '/login/paciente',
                'user_id' => $resultado['id'],
                'nombre_completo' => $nombre . ' ' . $apellidos
            ], "¡Cuenta de paciente creada exitosamente! Ahora puede iniciar sesión.");
        } else {
<<<<<<< HEAD
            $errorMessage = $this->getErrorMessage($resultado['message']);
            ApiResponse::error($errorMessage, 'creation_error', [], 500);
        }
    }
    
    /**
     * API: Crear un nuevo médico
     * POST /api/registro/medico
     */
    public function crearMedico() {
        // Verificar token CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            ApiResponse::csrfError('Token CSRF inválido. Por favor, recargue la página.');
            return;
        }
        
        // ==================== OBTENER Y LIMPIAR DATOS ====================
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $cedula = trim($_POST['cedula'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $sexo = $_POST['sexo'] ?? '';
        $adicional = trim($_POST['adicional'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $confirm_pass = $_POST['confirm_pass'] ?? '';
        
        // ==================== OBTENER UBICACIÓN COMPLETA ====================
        $direccion_completa = $this->construirDireccionCompleta($_POST);
        
        // ==================== VALIDACIONES ====================
        $errores = $this->validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                                $telefono, $correo, $sexo, $pass, $confirm_pass, 
                                                $direccion_completa);
        
        // Validar campos específicos de ubicación
        if (empty($_POST['estado'] ?? '')) {
            $errores['estado'] = 'Debe seleccionar un estado';
        }
        if (empty($_POST['ciudad'] ?? '')) {
            $errores['ciudad'] = 'Debe seleccionar una ciudad';
        }
        
        // Si hay errores de validación, retornarlos
        if (!empty($errores)) {
            ApiResponse::validationError($errores, 'Por favor, corrija los siguientes errores');
            return;
        }
        
        // ==================== CREAR EL MÉDICO ====================
        $medico = new Medico();
        
        // Verificar si ya existe
        if ($this->medicoExiste($cedula, $correo)) {
            ApiResponse::error('Ya existe un médico con esta cédula o correo electrónico', 'duplicate_entry', [], 409);
            return;
        }
        
        $password_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $resultado = $medico->crear([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'direccion' => $direccion_completa,
            'correo' => $correo,
            'sexo' => $sexo,
            'adicional' => $adicional,
            'password_hash' => $password_hash,
            'tipo' => 2, // Tipo 2 = Médico
            'avatar' => 'avatarDES.jpg'
        ]);
        
        // ==================== RESPUESTA ====================
        if ($resultado['success']) {
            ApiResponse::created([
                'redirect' => APP_URL . '/login/medico',
                'user_id' => $resultado['id'],
                'nombre_completo' => $nombre . ' ' . $apellidos
            ], "¡Cuenta de médico creada exitosamente! Ahora puede iniciar sesión.");
        } else {
            $errorMessage = $this->getErrorMessage($resultado['message']);
            ApiResponse::error($errorMessage, 'creation_error', [], 500);
        }
    }
    
    /**
     * API: Crear un nuevo asistente
     * POST /api/registro/asistente
     */
    public function crearAsistente() {
        // Verificar token CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            ApiResponse::csrfError('Token CSRF inválido. Por favor, recargue la página.');
            return;
        }
        
        // ==================== OBTENER Y LIMPIAR DATOS ====================
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $cedula = trim($_POST['cedula'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $sexo = $_POST['sexo'] ?? '';
        $adicional = trim($_POST['adicional'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $confirm_pass = $_POST['confirm_pass'] ?? '';
        
        // ==================== OBTENER UBICACIÓN COMPLETA ====================
        $direccion_completa = $this->construirDireccionCompleta($_POST);
        
        // ==================== VALIDACIONES ====================
        $errores = $this->validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                                $telefono, $correo, $sexo, $pass, $confirm_pass, 
                                                $direccion_completa);
        
        // Validar campos específicos de ubicación
        if (empty($_POST['estado'] ?? '')) {
            $errores['estado'] = 'Debe seleccionar un estado';
        }
        if (empty($_POST['ciudad'] ?? '')) {
            $errores['ciudad'] = 'Debe seleccionar una ciudad';
        }
        
        // Si hay errores de validación, retornarlos
        if (!empty($errores)) {
            ApiResponse::validationError($errores, 'Por favor, corrija los siguientes errores');
            return;
        }
        
        // ==================== CREAR EL ASISTENTE ====================
        $asistente = new Asistente();
        
        // Verificar si ya existe
        if ($this->asistenteExiste($cedula, $correo)) {
            ApiResponse::error('Ya existe un asistente con esta cédula o correo electrónico', 'duplicate_entry', [], 409);
            return;
        }
        
        $password_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $resultado = $asistente->crear([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'direccion' => $direccion_completa,
            'correo' => $correo,
            'sexo' => $sexo,
            'adicional' => $adicional,
            'password_hash' => $password_hash,
            'tipo' => 3, // Tipo 3 = Asistente
            'avatar' => 'avatarDES.jpg'
        ]);
        
        // ==================== RESPUESTA ====================
        if ($resultado['success']) {
            ApiResponse::created([
                'redirect' => APP_URL . '/login/asistente',
                'user_id' => $resultado['id'],
                'nombre_completo' => $nombre . ' ' . $apellidos
            ], "¡Cuenta de asistente creada exitosamente! Ahora puede iniciar sesión.");
        } else {
            $errorMessage = $this->getErrorMessage($resultado['message']);
            ApiResponse::error($errorMessage, 'creation_error', [], 500);
        }
    }
    
    /**
     * API: Crear un nuevo administrador
     * POST /api/registro/administrador
     */
=======
  
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function crearAdministrador() {
        // Verificar token CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            ApiResponse::csrfError('Token CSRF inválido. Por favor, recargue la página.');
            return;
        }
        
        // ==================== OBTENER Y LIMPIAR DATOS ====================
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $cedula = trim($_POST['cedula'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $sexo = $_POST['sexo'] ?? '';
        $adicional = trim($_POST['adicional'] ?? '');
        $pass = $_POST['pass'] ?? '';
        $confirm_pass = $_POST['confirm_pass'] ?? '';
        
<<<<<<< HEAD
=======
        // ==================== OBTENER RESPUESTAS DE SEGURIDAD ====================
        $pregunta1 = trim($_POST['pregunta1'] ?? '');
        $respuesta1 = trim($_POST['respuesta1'] ?? '');
        $pregunta2 = trim($_POST['pregunta2'] ?? '');
        $respuesta2 = trim($_POST['respuesta2'] ?? '');
        $pregunta3 = trim($_POST['pregunta3'] ?? '');
        $respuesta3 = trim($_POST['respuesta3'] ?? '');
        
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        // ==================== OBTENER UBICACIÓN COMPLETA ====================
        $direccion_completa = $this->construirDireccionCompleta($_POST);
        
        // ==================== VALIDACIONES ====================
        $errores = $this->validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                                $telefono, $correo, $sexo, $pass, $confirm_pass, 
                                                $direccion_completa);
        
        // Validar campos específicos de ubicación
        if (empty($_POST['estado'] ?? '')) {
            $errores['estado'] = 'Debe seleccionar un estado';
        }
        if (empty($_POST['ciudad'] ?? '')) {
            $errores['ciudad'] = 'Debe seleccionar una ciudad';
        }
        
        // Si hay errores de validación, retornarlos
        if (!empty($errores)) {
            ApiResponse::validationError($errores, 'Por favor, corrija los siguientes errores');
            return;
        }
        
        // ==================== CREAR EL ADMINISTRADOR ====================
        $administrador = new Administrador();
        
        // Verificar si ya existe
        if ($this->administradorExiste($cedula, $correo)) {
            ApiResponse::error('Ya existe un administrador con esta cédula o correo electrónico', 'duplicate_entry', [], 409);
            return;
        }
        
        $password_hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $resultado = $administrador->crear([
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $fecha_nacimiento,
            'cedula' => $cedula,
            'telefono' => $telefono,
            'direccion' => $direccion_completa,
            'correo' => $correo,
            'sexo' => $sexo,
            'adicional' => $adicional,
            'password_hash' => $password_hash,
            'tipo' => 4, // Tipo 4 = Administrador
            'avatar' => 'avatarDES.jpg'
        ]);
        
        // ==================== RESPUESTA ====================
        if ($resultado['success']) {
<<<<<<< HEAD
=======
            // Guardar respuestas de seguridad
            $this->guardarRespuestasSeguridad($resultado['id'], $pregunta1, $respuesta1, $pregunta2, $respuesta2, $pregunta3, $respuesta3);
            
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            ApiResponse::created([
                'redirect' => APP_URL . '/login/administrador',
                'user_id' => $resultado['id'],
                'nombre_completo' => $nombre . ' ' . $apellidos
            ], "¡Cuenta de administrador creada exitosamente! Ahora puede iniciar sesión.");
        } else {
            $errorMessage = $this->getErrorMessage($resultado['message']);
            ApiResponse::error($errorMessage, 'creation_error', [], 500);
        }
    }
    
<<<<<<< HEAD
    // ==================== MÉTODOS AUXILIARES PRIVADOS ====================
    
    /**
     * Construye la dirección completa a partir de los datos del formulario
     * @param array $data Datos del formulario
     * @return string Dirección completa formateada
     */
=======
    // ==================== MÉTODOS AUXILIARES PRIVADOS ====================   
    
    private function guardarRespuestasSeguridad($id_usuario, $pregunta1, $respuesta1, $pregunta2, $respuesta2, $pregunta3, $respuesta3) {
        try {
            $db = new Conexion();
            
            // Arreglo de preguntas y respuestas seleccionadas por el usuario
            $preguntas_respuestas = [
                ['pregunta' => $pregunta1, 'respuesta' => $respuesta1],
                ['pregunta' => $pregunta2, 'respuesta' => $respuesta2],
                ['pregunta' => $pregunta3, 'respuesta' => $respuesta3]
            ];
            
            foreach ($preguntas_respuestas as $item) {
                $pregunta_texto = trim($item['pregunta'] ?? '');
                $respuesta = trim($item['respuesta'] ?? '');
                
                if (empty($pregunta_texto) || empty($respuesta)) {
                    continue; // Saltar si falta pregunta o respuesta
                }
                
                // Hashear la respuesta para seguridad
                $respuesta_hash = password_hash($respuesta, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO respuestas_seguridad_usuario (id_usuario, pregunta, respuesta_hash) 
                        VALUES (:id_usuario, :pregunta, :respuesta_hash)";
                $query = $db->pdo->prepare($sql);
                $query->execute([
                    ':id_usuario' => $id_usuario,
                    ':pregunta' => $pregunta_texto,
                    ':respuesta_hash' => $respuesta_hash
                ]);
            }
            
            return true;
        } catch(PDOException $e) {
            error_log("Error en guardarRespuestasSeguridad: " . $e->getMessage());
            return false;
        }
    }
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    private function construirDireccionCompleta($data) {
        $estado_nombre = $this->getNombreEstado($data['estado'] ?? '');
        $ciudad_nombre = $this->getNombreCiudad($data['ciudad'] ?? '');
        $municipio_nombre = $this->getNombreMunicipio($data['municipio'] ?? '');
        $parroquia_nombre = $this->getNombreParroquia($data['parroquia'] ?? '');
        $direccion_detallada = trim($data['direccion'] ?? '');
        
        $partes = [];
        if ($estado_nombre && $estado_nombre !== 'Seleccione un estado...') {
            $partes[] = $estado_nombre;
        }
        if ($ciudad_nombre && $ciudad_nombre !== 'Seleccione una ciudad...') {
            $partes[] = $ciudad_nombre;
        }
        if ($municipio_nombre && $municipio_nombre !== 'Seleccione un municipio...') {
            $partes[] = $municipio_nombre;
        }
        if ($parroquia_nombre && $parroquia_nombre !== 'Seleccione una parroquia...') {
            $partes[] = $parroquia_nombre;
        }
        
        $ubicacion = implode(', ', $partes);
        
        if ($direccion_detallada) {
            return $ubicacion ? $ubicacion . ' - ' . $direccion_detallada : $direccion_detallada;
        }
        
        return $ubicacion;
<<<<<<< HEAD
    }
    
    /**
     * Valida los datos comunes del registro
     * @return array Array de errores
     */
=======
    }    
 
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    private function validarDatosRegistro($nombre, $apellidos, $fecha_nacimiento, $cedula, 
                                          $telefono, $correo, $sexo, $pass, $confirm_pass, $direccion) {
        $errores = [];
        
        if (empty($nombre)) $errores['nombre'] = 'El nombre es requerido';
        if (empty($apellidos)) $errores['apellidos'] = 'Los apellidos son requeridos';
        if (empty($fecha_nacimiento)) $errores['fecha_nacimiento'] = 'La fecha de nacimiento es requerida';
        if (empty($cedula)) $errores['cedula'] = 'La cédula es requerida';
        if (empty($telefono)) $errores['telefono'] = 'El teléfono es requerido';
        if (empty($correo)) $errores['correo'] = 'El correo electrónico es requerido';
        if (empty($sexo)) $errores['sexo'] = 'El sexo es requerido';
        if (empty($pass)) $errores['pass'] = 'La contraseña es requerida';
        
        // Validar formato de correo
        if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = 'Ingrese un correo electrónico válido';
        }
        
        // Validar contraseña
        if (!empty($pass) && strlen($pass) < 6) {
            $errores['pass'] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Validar que las contraseñas coincidan
        if (!empty($pass) && $pass !== $confirm_pass) {
            $errores['confirm_pass'] = 'Las contraseñas no coinciden';
        }
        
        return $errores;
<<<<<<< HEAD
    }
    
    /**
     * Obtiene el mensaje de error correspondiente
     * @param string $codigo Código de error del modelo
     * @return string Mensaje de error amigable
     */
=======
    }    
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    private function getErrorMessage($codigo) {
        $mensajes = [
            'existe' => 'Ya existe un usuario con esta cédula o correo electrónico',
            'error_bd' => 'Error en la base de datos. Por favor, intente más tarde',
            'error_login' => 'Cuenta creada pero hubo un problema con el acceso. Contacte al administrador',
            'error_exception' => 'Error interno del servidor. Por favor, intente más tarde',
            'error_actualizacion' => 'Error al guardar los datos',
        ];
        
        return $mensajes[$codigo] ?? 'Error al crear la cuenta. Por favor, intente nuevamente';
    }
    
<<<<<<< HEAD
    /**
     * Verifica si ya existe un médico con la cédula o correo
     */
=======
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    private function medicoExiste($cedula, $correo) {
        try {
            $db = new Conexion();
            $sql = "SELECT id_medico FROM registro_medico WHERE cedula_medico = :cedula OR correo_medico = :correo";
            $query = $db->pdo->prepare($sql);
            $query->execute([':cedula' => $cedula, ':correo' => $correo]);
            return $query->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error en medicoExiste: " . $e->getMessage());
            return false;
        }
    }
    
<<<<<<< HEAD
    /**
     * Verifica si ya existe un asistente con la cédula o correo
     */
=======
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    private function asistenteExiste($cedula, $correo) {
        try {
            $db = new Conexion();
            $sql = "SELECT id_asistente FROM registro_asistente WHERE cedula_asistente = :cedula OR correo_asistente = :correo";
            $query = $db->pdo->prepare($sql);
            $query->execute([':cedula' => $cedula, ':correo' => $correo]);
            return $query->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error en asistenteExiste: " . $e->getMessage());
            return false;
        }
    }
    
<<<<<<< HEAD
    /**
     * Verifica si ya existe un administrador con la cédula o correo
     */
=======
  
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    private function administradorExiste($cedula, $correo) {
        try {
            $db = new Conexion();
            $sql = "SELECT id_administrador FROM registro_administrador WHERE cedula_administrador = :cedula OR correo_administrador = :correo";
            $query = $db->pdo->prepare($sql);
            $query->execute([':cedula' => $cedula, ':correo' => $correo]);
            return $query->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error en administradorExiste: " . $e->getMessage());
            return false;
        }
    }
    
<<<<<<< HEAD
    // ==================== MÉTODOS PARA OBTENER NOMBRES DE UBICACIÓN ====================
    
    /**
     * Obtiene el nombre de un estado por su ID
     */
=======
    // ==================== MÉTODOS PARA OBTENER NOMBRES DE UBICACIÓN ====================   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    private function getNombreEstado($id_estado) {
        if (!$id_estado) return '';
        
        try {
            $db = new Conexion();
            $sql = "SELECT estado FROM estados WHERE id_estado = :id";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id' => $id_estado]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado->estado : '';
        } catch(PDOException $e) {
            error_log("Error en getNombreEstado: " . $e->getMessage());
            return '';
        }
<<<<<<< HEAD
    }
    
    /**
     * Obtiene el nombre de una ciudad por su ID
     */
=======
    }    
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    private function getNombreCiudad($id_ciudad) {
        if (!$id_ciudad) return '';
        
        try {
            $db = new Conexion();
            $sql = "SELECT ciudad FROM ciudades WHERE id_ciudad = :id";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id' => $id_ciudad]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado->ciudad : '';
        } catch(PDOException $e) {
            error_log("Error en getNombreCiudad: " . $e->getMessage());
            return '';
        }
<<<<<<< HEAD
    }
    
    /**
     * Obtiene el nombre de un municipio por su ID
     */
=======
    }    
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    private function getNombreMunicipio($id_municipio) {
        if (!$id_municipio) return '';
        
        try {
            $db = new Conexion();
            $sql = "SELECT municipio FROM municipios WHERE id_municipio = :id";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id' => $id_municipio]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado->municipio : '';
        } catch(PDOException $e) {
            error_log("Error en getNombreMunicipio: " . $e->getMessage());
            return '';
        }
<<<<<<< HEAD
    }
    
    /**
     * Obtiene el nombre de una parroquia por su ID
     */
=======
    }    
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    private function getNombreParroquia($id_parroquia) {
        if (!$id_parroquia) return '';
        
        try {
            $db = new Conexion();
            $sql = "SELECT parroquia FROM parroquias WHERE id_parroquia = :id";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id' => $id_parroquia]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado->parroquia : '';
        } catch(PDOException $e) {
            error_log("Error en getNombreParroquia: " . $e->getMessage());
            return '';
<<<<<<< HEAD
=======
            jsonResponse(['success' => false, 'message' => "Error al crear la cuenta"]);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
}
?>