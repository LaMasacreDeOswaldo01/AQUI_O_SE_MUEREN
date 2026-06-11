<?php
class AsistenteController {    
    public function __construct() {
        // Verificar autenticación y rol
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'asistente') {
            if ($this->isAjax()) {
                ApiResponse::unauthorized('No autorizado. Debe iniciar sesión como asistente.');
            } else {
                redirect('login/asistente');
            }
            exit();
        }
    }
    
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }    
    // ==================== PERFIL Y DATOS PERSONALES ====================  
    public function buscar() {
        $id_asistente = $_POST['dato'] ?? $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AsistenteController::buscar - ID: $id_asistente, Sesión: $id_sesion");
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado para ver este perfil', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $asistente = new Asistente();
        $fecha_actual = new DateTime();
        $asistente->obtener_datos($id_asistente);
        
        if(empty($asistente->objetos)) {
            ApiResponse::notFound('Asistente');
            return;
        }
        
        $json = array();
        foreach ($asistente->objetos as $objeto) {
            $fecha_nacimiento = $objeto->fecha_nacimiento_asistente;
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $nacimiento->diff($fecha_actual);
            
            $avatar_path = (!empty($objeto->avatar_asistente) && $objeto->avatar_asistente != 'avatarDES.jpg') 
                           ? APP_URL . '/img/' . $objeto->avatar_asistente 
                           : APP_URL . '/img/avatarDES.jpg';
            
            $json = array(
                'success' => true,
                'nombre' => $objeto->nombre_asistente ?? '',
                'apellidos' => $objeto->apellido_asistente ?? '',
                'fecha_nacimiento' => $edad->y,
                'cedula' => $objeto->cedula_asistente ?? '',
                'tipo' => $objeto->nombre_tipo ?? 'Asistente',
                'telefono' => $objeto->telefono_asistente ?? '',
                'direccion' => $objeto->direccion_asistente ?? '',
                'correo' => $objeto->correo_asistente ?? '',
                'sexo' => $objeto->sexo_asistente ?? '',
                'adicional' => $objeto->adicional_asistente ?? '',
                'avatar' => $avatar_path,
                // Estadísticas adicionales para el dashboard
                'total_recetas' => $this->contarTotalRecetas(),
                'total_pacientes' => $this->contarTotalPacientes(),
                'total_medicos' => $this->contarTotalMedicos()
            );
        }
        
        ApiResponse::success($json, 'datos_cargados', 'Datos del asistente cargados correctamente');
    }
    
    private function contarTotalRecetas() {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(*) as total FROM recetas WHERE estado = 1";
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarTotalRecetas: " . $e->getMessage());
            return 0;
        }
    }
    
    private function contarTotalPacientes() {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(*) as total FROM registro_paciente WHERE paciente_tipo = 1";
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarTotalPacientes: " . $e->getMessage());
            return 0;
        }
    }
    
    private function contarTotalMedicos() {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(*) as total FROM registro_medico WHERE medico_tipo = 2";
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarTotalMedicos: " . $e->getMessage());
            return 0;
        }
    }
    
    public function capturarDatos() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AsistenteController::capturarDatos - ID: $id_asistente, Sesión: $id_sesion");
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $asistente = new Asistente();
        $asistente->obtener_datos($id_asistente);
        
        if(empty($asistente->objetos)) {
            ApiResponse::notFound('Asistente');
            return;
        }
        
        $json = array();
        foreach ($asistente->objetos as $objeto) {
            // Parsear la dirección para obtener sus componentes
            $direccion_completa = $objeto->direccion_asistente ?? '';
            $datos_ubicacion = $this->parsearDireccion($direccion_completa);
            
            $json = array(
                'telefono' => $objeto->telefono_asistente ?? '',
                'direccion' => $direccion_completa,
                'correo' => $objeto->correo_asistente ?? '',
                'sexo' => $objeto->sexo_asistente ?? '',
                'adicional' => $objeto->adicional_asistente ?? '',
                // Datos de ubicación desglosados
                'estado' => $datos_ubicacion['estado'],
                'ciudad' => $datos_ubicacion['ciudad'],
                'municipio' => $datos_ubicacion['municipio'],
                'parroquia' => $datos_ubicacion['parroquia'],
                'direccion_detallada' => $datos_ubicacion['direccion_detallada']
            );
        }
        
        ApiResponse::success($json, 'datos_capturados', 'Datos cargados para edición');
    }
    
    private function parsearDireccion($direccion_completa) {
        $resultado = [
            'estado' => '',
            'ciudad' => '',
            'municipio' => '',
            'parroquia' => '',
            'direccion_detallada' => ''
        ];
        
        if (empty($direccion_completa)) {
            return $resultado;
        }
        
        // Separar dirección detallada de la ubicación
        $partes = explode(' - ', $direccion_completa, 2);
        $ubicacion = $partes[0];
        $resultado['direccion_detallada'] = $partes[1] ?? '';
        
        // Separar los componentes de la ubicación por comas
        $componentes = array_map('trim', explode(',', $ubicacion));
        
        // Asignar según la cantidad de componentes
        if (count($componentes) >= 1) {
            $resultado['estado'] = $componentes[0];
        }
        if (count($componentes) >= 2) {
            $resultado['ciudad'] = $componentes[1];
        }
        if (count($componentes) >= 3) {
            $resultado['municipio'] = $componentes[2];
        }
        if (count($componentes) >= 4) {
            $resultado['parroquia'] = $componentes[3];
        }
        
        return $resultado;
    }
    
    public function editarUsuario() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AsistenteController::editarUsuario - ID: $id_asistente, Sesión: $id_sesion");
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        // Verificar token CSRF
        if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            ApiResponse::csrfError();
            return;
        }
        
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        error_log("=== EDITANDO ASISTENTE ===");
        error_log("ID Asistente: " . $id_asistente);
        error_log("Dirección recibida: " . $direccion);
        
        $asistente = new Asistente();
        $resultado = $asistente->editar($id_asistente, $telefono, $direccion, $correo, $sexo, $adicional);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'usuario_actualizado', 'Datos actualizados correctamente');
        } else {
            ApiResponse::error($resultado['message'], 'update_error', [], 500);
        }
    }
    
    public function cambiarFoto() {
        $id_asistente = $_SESSION['usuario'];
        
        if (empty($id_asistente)) {
            ApiResponse::error('Sesión no válida', ApiResponse::CODE_AUTH_ERROR, [], 401);
            return;
        }
        
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            ApiResponse::error('No se recibió el archivo', 'upload_error', [], 400);
            return;
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['photo']['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types)) {
            ApiResponse::error('Tipo de archivo no permitido. Use JPG, PNG o GIF', 'invalid_type', [], 400);
            return;
        }
        
        // Generar nombre seguro para el archivo
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $nombre = bin2hex(random_bytes(16)) . '.' . $extension;
        $ruta_destino = dirname(__DIR__) . '/img/' . $nombre;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $ruta_destino)) {
            $asistente = new Asistente();
            $avatar_anterior = $asistente->cambiar_photo($id_asistente, $nombre);
            
            if ($avatar_anterior && $avatar_anterior !== 'avatarDES.jpg') {
                $ruta_anterior = dirname(__DIR__) . '/img/' . $avatar_anterior;
                if (file_exists($ruta_anterior)) {
                    @unlink($ruta_anterior);
                }
            }
            
            ApiResponse::success([
                'ruta' => APP_URL . '/img/' . $nombre,
                'alert' => 'edit'
            ], 'foto_actualizada', 'Foto de perfil actualizada correctamente');
        } else {
            ApiResponse::error('Error al mover el archivo', 'upload_error', [], 500);
        }
    }
    
    public function cambiarPassword() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        // Verificar token CSRF
        if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            ApiResponse::csrfError();
            return;
        }
        
        $oldpass = $_POST['oldpass'] ?? '';
        $newpass = $_POST['newpass'] ?? '';
        
        if(strlen($newpass) < 6) {
            ApiResponse::error('La contraseña debe tener al menos 6 caracteres', 'validation_error', [], 400);
            return;
        }
        
        $loginAsistente = new LoginAsistente();
        ob_start();
        $loginAsistente->cambiar_contra($id_asistente, $oldpass, $newpass);
        $resultado = trim(ob_get_clean());
        
        if ($resultado === 'update') {
            ApiResponse::success([], 'password_updated', 'Contraseña actualizada correctamente');
        } else {
            ApiResponse::error('Contraseña actual incorrecta', ApiResponse::CODE_AUTH_ERROR, [], 401);
        }
    }    
    // ==================== ESTADÍSTICAS Y DASHBOARD ====================  
    public function misEstadisticas() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $asistente = new Asistente();
        $estadisticas = $asistente->obtenerEstadisticas($id_asistente);
        
        // Agregar estadísticas adicionales del día
        $estadisticas['recetas_hoy'] = $asistente->contarRecetasHoy();
        $estadisticas['pacientes_hoy'] = $asistente->contarPacientesHoy();
        $estadisticas['medicos_activos'] = $asistente->contarMedicosActivos();
        $estadisticas['promedio_espera'] = 15; // Valor por defecto, se podría calcular
        
        ApiResponse::success($estadisticas, 'estadisticas', 'Estadísticas cargadas correctamente');
    }    
  
    public function actividadReciente() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $actividades = $this->obtenerActividadRecienteSistema();
        
        ApiResponse::success($actividades, 'actividad_cargada', 'Actividad reciente cargada correctamente');
    }    
   
    public function citasHoy() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $citas = $this->obtenerCitasHoy();
        
        ApiResponse::success($citas, 'citas_cargadas', 'Citas de hoy cargadas correctamente');
    }    
  
    private function obtenerActividadRecienteSistema($limit = 10) {
        try {
            $db = new Conexion();
            $actividades = array();
            
            // Obtener recetas recientes
            $sql = "SELECT 
                        r.id_receta as id,
                        r.nombre_medicamento as titulo,
                        r.fecha_receta as fecha,
                        CONCAT(rp.nombre_paciente, ' ', rp.apellido_paciente) as paciente,
                        'receta' as tipo
                    FROM recetas r
                    LEFT JOIN registro_paciente rp ON r.id_paciente = rp.id_paciente
                    WHERE r.estado = 1
                    ORDER BY r.fecha_receta DESC
                    LIMIT :limit";
            
            $query = $db->pdo->prepare($sql);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $actividades[] = array(
                    'id' => $row->id,
                    'titulo' => 'Receta emitida: ' . ($row->titulo ?? 'Medicamento'),
                    'descripcion' => 'Paciente: ' . ($row->paciente ?? 'N/A'),
                    'fecha' => $this->formatearFecha($row->fecha),
                    'tipo' => 'receta'
                );
            }
            
            return $actividades;
        } catch(PDOException $e) {
            error_log("Error en obtenerActividadRecienteSistema: " . $e->getMessage());
            return array();
        }
    }
    
    private function obtenerCitasHoy() {
        try {
            $db = new Conexion();
            $sql = "SELECT 
                        c.id_cita,
                        c.fecha_cita,
                        c.hora_cita,
                        c.estado,
                        CONCAT(rp.nombre_paciente, ' ', rp.apellido_paciente) as paciente_nombre,
                        CONCAT(rm.nombre_medico, ' ', rm.apellido_medico) as medico_nombre,
                        con.nombre as consultorio_nombre
                    FROM citas c
                    LEFT JOIN registro_paciente rp ON c.id_paciente = rp.id_paciente
                    LEFT JOIN registro_medico rm ON c.id_medico = rm.id_medico
                    LEFT JOIN consultorios con ON c.id_consultorio = con.id_consultorio
                    WHERE DATE(c.fecha_cita) = CURDATE()
                    ORDER BY c.hora_cita ASC";
            
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultados = $query->fetchAll(PDO::FETCH_OBJ);
            
            $citas = array();
            foreach ($resultados as $cita) {
                $citas[] = array(
                    'id_cita' => $cita->id_cita,
                    'fecha' => $cita->fecha_cita,
                    'hora' => substr($cita->hora_cita, 0, 5),
                    'estado' => $cita->estado,
                    'paciente_nombre' => $cita->paciente_nombre ?? 'Paciente',
                    'medico_nombre' => $cita->medico_nombre ?? 'Médico asignado',
                    'consultorio' => $cita->consultorio_nombre ?? 'Consultorio'
                );
            }
            
            return $citas;
        } catch(PDOException $e) {
            error_log("Error en obtenerCitasHoy: " . $e->getMessage());
            return array();
        }
    }
    
    private function formatearFecha($fecha) {
        if (empty($fecha)) return '';
        
        $timestamp = strtotime($fecha);
        $hoy = strtotime(date('Y-m-d'));
        $ayer = strtotime('-1 day', $hoy);
        
        if ($timestamp >= $hoy) {
            return 'Hoy, ' . date('g:i A', $timestamp);
        } elseif ($timestamp >= $ayer) {
            return 'Ayer, ' . date('g:i A', $timestamp);
        } else {
            return date('d/m/Y', $timestamp);
        }
    }    
    // ==================== REGISTRO DE PACIENTE ====================
    public function registrarPaciente() {
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
                'user_id' => $resultado['id'],
                'nombre_completo' => $nombre . ' ' . $apellidos
            ], "¡Paciente registrado exitosamente!");
        } else {
            $errorMessage = $this->getErrorMessage($resultado['message']);
            ApiResponse::error($errorMessage, 'creation_error', [], 500);
        }
    }
    
    public function listarPacientes() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        try {
            $db = new Conexion();
            $sql = "SELECT 
                        id_paciente,
                        nombre_paciente,
                        apellido_paciente,
                        cedula_paciente,
                        telefono_paciente,
                        correo_paciente,
                        fecha_registro
                    FROM registro_paciente 
                    WHERE paciente_tipo = 1
                    ORDER BY fecha_registro DESC
                    LIMIT 50";
            
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultados = $query->fetchAll(PDO::FETCH_OBJ);
            
            $pacientes = array();
            foreach ($resultados as $paciente) {
                $pacientes[] = array(
                    'id_paciente' => $paciente->id_paciente,
                    'nombre' => $paciente->nombre_paciente,
                    'apellidos' => $paciente->apellido_paciente,
                    'cedula' => $paciente->cedula_paciente,
                    'telefono' => $paciente->telefono_paciente,
                    'correo' => $paciente->correo_paciente,
                    'fecha_registro' => $paciente->fecha_registro
                );
            }
            
            ApiResponse::success($pacientes, 'pacientes_listados', 'Pacientes listados correctamente');
        } catch(PDOException $e) {
            error_log("Error en listarPacientes: " . $e->getMessage());
            ApiResponse::error('Error al listar pacientes', 'db_error', [], 500);
        }
    }
    
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
                $respuesta = strtolower(trim($item['respuesta'] ?? ''));

                if (empty($pregunta_texto) || empty($respuesta)) {
                    continue; // Saltar si falta pregunta o respuesta
                }

                // Hashear la respuesta en minúsculas para comparación case-insensitive
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
    
    private function construirDireccionCompleta($data) {
        $estado_nombre = $this->getNombreEstado($data['estado'] ?? '');
        $ciudad_nombre = $this->getNombreCiudad($data['ciudad'] ?? '');
        $municipio_nombre = $this->getNombreMunicipio($data['municipio'] ?? '');
        $parroquia_nombre = $this->getNombreParroquia($data['parroquia'] ?? '');
        $direccion_detallada = trim($data['direccion_detallada'] ?? '');
        
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
        }
    }
    
    // ==================== VISTAS (Rutas no-API) ====================   
    public function dashboard() {
        AuthHelper::checkRole('asistente', true);
        
        $options = [
            'title' => 'Panel del Asistente - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/asistente'],
                ['label' => 'Dashboard']
            ],
            'active_page' => 'dashboard',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_asistente' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('asistente/asi_dashboard', $data, $options);
    }
}
?>