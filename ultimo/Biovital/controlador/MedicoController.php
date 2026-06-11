<?php
class MedicoController {    
    public function __construct() {
                if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'medico') {
            if ($this->isAjax()) {
                ApiResponse::unauthorized('No autorizado. Debe iniciar sesión como médico.');
            } else {
                redirect('login/medico');
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
        $id_medico = $_POST['dato'] ?? $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("MedicoController::buscar - ID: $id_medico, Sesión: $id_sesion");
        
        if($id_medico != $id_sesion) {
            ApiResponse::error('No autorizado para ver este perfil', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $medico = new Medico();
        $fecha_actual = new DateTime();
        $medico->obtener_datos($id_medico);
        
        if(empty($medico->objetos)) {
            ApiResponse::notFound('Médico');
            return;
        }
        
        $json = array();
        foreach ($medico->objetos as $objeto) {
            $fecha_nacimiento = $objeto->fecha_nacimiento_medico;
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $nacimiento->diff($fecha_actual);
            
            $avatar_path = (!empty($objeto->avatar_medico) && $objeto->avatar_medico != 'avatarDES.jpg') 
                           ? APP_URL . '/img/' . $objeto->avatar_medico 
                           : APP_URL . '/img/avatarDES.jpg';
            
            $json = array(
                'nombre' => $objeto->nombre_medico ?? '',
                'apellidos' => $objeto->apellido_medico ?? '',
                'fecha_nacimiento' => $edad->y,
                'cedula' => $objeto->cedula_medico ?? '',
                'mpps_registro' => $objeto->mpps_registro ?? '',
                'tipo' => $objeto->nombre_tipo ?? 'Médico',
                'telefono' => $objeto->telefono_medico ?? '',
                'direccion' => $objeto->direccion_medico ?? '',
                'correo' => $objeto->correo_medico ?? '',
                'sexo' => $objeto->sexo_medico ?? '',
                'adicional' => $objeto->adicional_medico ?? '',
                'avatar' => $avatar_path
            );
        }
        
        ApiResponse::success($json, 'datos_cargados', 'Datos del médico cargados correctamente');
    }  
    
    public function capturarDatos() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("MedicoController::capturarDatos - ID: $id_medico, Sesión: $id_sesion");
        
        if($id_medico != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $medico = new Medico();
        $medico->obtener_datos($id_medico);
        
        if(empty($medico->objetos)) {
            ApiResponse::notFound('Médico');
            return;
        }
        
        $json = array();
        foreach ($medico->objetos as $objeto) {
            // Parsear la dirección para obtener sus componentes
            $direccion_completa = $objeto->direccion_medico ?? '';
            $datos_ubicacion = $this->parsearDireccion($direccion_completa);
            
            $json = array(
                'telefono' => $objeto->telefono_medico ?? '',
                'direccion' => $direccion_completa,
                'correo' => $objeto->correo_medico ?? '',
                'sexo' => $objeto->sexo_medico ?? '',
                'mpps_registro' => $objeto->mpps_registro ?? '',
                'adicional' => $objeto->adicional_medico ?? '',
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
    public function citas() {
    AuthHelper::checkRole('medico', true);
    
    $options = [
        'title' => 'Mis Citas - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
            ['label' => 'Mis Citas']
        ],
        'active_page' => 'citas',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
        'scripts' => '<script src="' . APP_URL . '/js/medico_citas.js"></script>'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
        'id_medico' => $_SESSION['usuario'] ?? 0
    ];
    
    ViewHelper::renderDashboard('medico/med_mis_citas', $data, $options);
}

/**
 * API: Listar citas del médico
 * POST /api/medicos/mis-citas
 */
public function listarCitas() {
    $id_medico = $_POST['id_medico'] ?? $_SESSION['usuario'] ?? 0;
    $id_sesion = $_SESSION['usuario'];
    
    if ($id_medico != $id_sesion) {
        ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
        return;
    }
    
    $filtro_estado = $_POST['estado'] ?? 'todos';
    $filtro_paciente = $_POST['paciente'] ?? '';
    $filtro_fecha = $_POST['fecha'] ?? '';
    $tipo_consulta = $_POST['tipo_consulta'] ?? 'todos';
    
    $medico = new Medico();
    $citas = $medico->listarCitas($id_medico, $filtro_estado, $filtro_paciente, $filtro_fecha, $tipo_consulta);
    
    // Agregar estadísticas de conteo
    $estadisticas = $medico->contarCitasPorEstado($id_medico);
    
    ApiResponse::success([
        'citas' => $citas,
        'estadisticas' => $estadisticas
    ], 'citas_listadas', 'Citas cargadas correctamente');
}

/**
 * API: Cambiar estado de una cita
 * POST /api/medicos/cambiar-estado-cita
 */
public function cambiarEstadoCita() {
    $id_medico = $_SESSION['usuario'];
    $id_cita = $_POST['id_cita'] ?? 0;
    $nuevo_estado = $_POST['estado'] ?? '';
    
    if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        ApiResponse::csrfError();
        return;
    }
    
    $estados_validos = ['pendiente', 'confirmada', 'en_progreso', 'completada', 'cancelada', 'no_asistio'];
    if (!in_array($nuevo_estado, $estados_validos)) {
        ApiResponse::error('Estado no válido', 'validation_error', [], 400);
        return;
    }
    
    $medico = new Medico();
    $resultado = $medico->actualizarEstadoCita($id_cita, $id_medico, $nuevo_estado);
    
    if ($resultado['success']) {
        ApiResponse::success([], 'estado_actualizado', 'Estado de la cita actualizado correctamente');
    } else {
        ApiResponse::error($resultado['message'], 'update_error', [], 500);
    }
}
public function buscarPacientesCitas() {
    $id_medico = $_SESSION['usuario'];
    $termino = $_POST['termino'] ?? '';
    
    if (strlen($termino) < 2) {
        ApiResponse::success([], 'sin_resultados', 'Ingrese al menos 2 caracteres');
        return;
    }
    
    $medico = new Medico();
    $pacientes = $medico->buscarPacientesCitas($id_medico, $termino);
    
    ApiResponse::success($pacientes, 'pacientes_encontrados', 'Pacientes encontrados');
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
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("MedicoController::editarUsuario - ID: $id_medico, Sesión: $id_sesion");
        
        if($id_medico != $id_sesion) {
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
        $mpps_registro = $_POST['mpps_registro'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        error_log("=== EDITANDO MÉDICO ===");
        error_log("ID Médico: " . $id_medico);
        error_log("Dirección recibida: " . $direccion);
        error_log("MPPS Registro: " . $mpps_registro);
        
        $medico = new Medico();
        $resultado = $medico->editar($id_medico, $telefono, $direccion, $correo, $sexo, $adicional, $mpps_registro);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'usuario_actualizado', 'Datos actualizados correctamente');
        } else {
            ApiResponse::error($resultado['message'], 'update_error', [], 500);
        }
    }    

    public function cambiarFoto() {
        $id_medico = $_SESSION['usuario'];
        
        if (empty($id_medico)) {
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
        
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $nombre = bin2hex(random_bytes(16)) . '.' . $extension;
        $ruta_destino = dirname(__DIR__) . '/img/' . $nombre;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $ruta_destino)) {
            $medico = new Medico();
            $avatar_anterior = $medico->cambiar_photo($id_medico, $nombre);
            
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
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_medico != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
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
        
        $loginMedico = new LoginMedico();
        ob_start();
        $loginMedico->cambiar_contra($id_medico, $oldpass, $newpass);
        $resultado = trim(ob_get_clean());
        
        if ($resultado === 'update') {
            ApiResponse::success([], 'password_updated', 'Contraseña actualizada correctamente');
        } else {
            ApiResponse::error('Contraseña actual incorrecta', ApiResponse::CODE_AUTH_ERROR, [], 401);
        }
    }        
    // ==================== ESTADÍSTICAS ====================        
    public function misEstadisticas() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("MedicoController::misEstadisticas - ID: $id_medico, Sesión: $id_sesion");
        
        if ($id_medico != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }        
        $medico = new Medico();
        $estadisticas = $medico->obtenerEstadisticasCompletas($id_medico);        
        ApiResponse::success($estadisticas, 'estadisticas', 'Estadísticas cargadas correctamente');
    }        
    // ==================== PACIENTES ====================      
    public function listarPacientes() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_medico != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $medico = new Medico();
        $pacientes = $medico->listarPacientes($id_medico);
        
        $resultado = array();
        foreach($pacientes as $paciente) {
            $resultado[] = array(
                'id_paciente' => $paciente->id_paciente,
                'nombre' => $paciente->nombre,
                'apellidos' => $paciente->apellidos,
                'cedula' => $paciente->cedula,
                'telefono' => $paciente->telefono,
                'correo' => $paciente->correo,
                'total_recetas' => $paciente->total_recetas ?? 0,
                'ultima_receta' => $paciente->ultima_receta ?? null
            );
        }
        
        ApiResponse::success($resultado, 'pacientes_listados', 'Lista de pacientes cargada correctamente');
    }        
    // ==================== ACTIVIDAD RECIENTE ====================      
    public function actividadReciente() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("MedicoController::actividadReciente - ID: $id_medico, Sesión: $id_sesion");
        
        if ($id_medico != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $medico = new Medico();
        $actividades = $medico->obtenerActividadReciente($id_medico);
        
        ApiResponse::success($actividades, 'actividad_cargada', 'Actividad reciente cargada correctamente');
    }    
    
    public function proximasCitas() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("MedicoController::proximasCitas - ID: $id_medico, Sesión: $id_sesion");
        
        if ($id_medico != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $medico = new Medico();
        $citas = $medico->obtenerProximasCitas($id_medico);
        
        ApiResponse::success($citas, 'citas_cargadas', 'Próximas citas cargadas correctamente');
    }  
    public function agenda() {
    AuthHelper::checkRole('medico', true);
    
    $options = [
        'title' => 'Mi Agenda - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
            ['label' => 'Mi Agenda']
        ],
        'active_page' => 'agenda',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">
                  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">',
        'scripts' => '<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
                      <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js"></script>
                      <script src="' . APP_URL . '/js/medico_agenda.js"></script>'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
        'id_medico' => $_SESSION['usuario'] ?? 0
    ];
    
    ViewHelper::renderDashboard('medico/med_mi_agenda', $data, $options);
}

public function editarHorarios() {
    AuthHelper::checkRole('medico', true);
    
    $options = [
        'title' => 'Editar Horarios - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
            ['label' => 'Mi Agenda', 'url' => APP_URL . '/medico/agenda'],
            ['label' => 'Editar Horarios']
        ],
        'active_page' => 'agenda',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
        'scripts' => '<script src="' . APP_URL . '/js/medico_editar_horarios.js"></script>'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
        'id_medico' => $_SESSION['usuario'] ?? 0
    ];
    
    ViewHelper::renderDashboard('medico/med_editar_horarios', $data, $options);
}

public function obtenerHorarios() {
    $id_medico = $_SESSION['usuario'];
    
    $medico = new Medico();
    $horarios = $medico->obtenerHorariosSemanales($id_medico);
    $consultorios = $medico->obtenerConsultoriosAsignados($id_medico);
    $especialidades = $medico->obtenerEspecialidadesAsignadas($id_medico);
    
    ApiResponse::success([
        'horarios' => $horarios,
        'consultorios' => $consultorios,
        'especialidades' => $especialidades
    ], 'horarios_cargados', 'Horarios cargados correctamente');
}

public function guardarHorario() {
    $id_medico = $_SESSION['usuario'];
    
    if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        ApiResponse::csrfError();
        return;
    }
    
    $dia = $_POST['dia'] ?? '';
    $turno = $_POST['turno'] ?? '';
    $activo = isset($_POST['activo']) ? 1 : 0;
    $hora_inicio = $_POST['hora_inicio'] ?? null;
    $hora_fin = $_POST['hora_fin'] ?? null;
    $id_consultorio = $_POST['id_consultorio'] ?? null;
    $id_especialidad = $_POST['id_especialidad'] ?? null;
    $duracion_cita = $_POST['duracion_cita'] ?? 30;
    
    $dias_validos = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    $turnos_validos = ['Mañana', 'Tarde'];
    
    if (!in_array($dia, $dias_validos) || !in_array($turno, $turnos_validos)) {
        ApiResponse::error('Datos inválidos', 'validation_error', [], 400);
        return;
    }
    
    $medico = new Medico();
    $resultado = $medico->guardarHorarioSemanal($id_medico, $dia, $turno, $activo, $hora_inicio, $hora_fin, $id_consultorio, $id_especialidad, $duracion_cita);
    
    if ($resultado['success']) {
        ApiResponse::success([], 'horario_guardado', 'Horario guardado correctamente');
    } else {
        ApiResponse::error($resultado['message'], 'save_error', [], 500);
    }
}

public function copiarHorarios() {
    $id_medico = $_SESSION['usuario'];
    
    if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        ApiResponse::csrfError();
        return;
    }
    
    $medico = new Medico();
    $resultado = $medico->copiarHorariosSemanaAnterior($id_medico);
    
    if ($resultado['success']) {
        ApiResponse::success([], 'horarios_copiados', 'Horarios copiados correctamente');
    } else {
        ApiResponse::error($resultado['message'], 'copy_error', [], 500);
    }
}

public function aplicarPlantilla() {
    $id_medico = $_SESSION['usuario'];
    
    if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        ApiResponse::csrfError();
        return;
    }
    
    $plantilla = $_POST['plantilla'] ?? '';
    $id_consultorio = $_POST['id_consultorio'] ?? null;
    $id_especialidad = $_POST['id_especialidad'] ?? null;
    
    $plantillas_validas = ['completa', 'solo_mananas', 'solo_tardes'];
    
    if (!in_array($plantilla, $plantillas_validas)) {
        ApiResponse::error('Plantilla no válida', 'validation_error', [], 400);
        return;
    }
    
    $medico = new Medico();
    $resultado = $medico->aplicarPlantillaHorarios($id_medico, $plantilla, $id_consultorio, $id_especialidad);
    
    if ($resultado['success']) {
        ApiResponse::success([], 'plantilla_aplicada', 'Plantilla aplicada correctamente');
    } else {
        ApiResponse::error($resultado['message'], 'apply_error', [], 500);
    }
}
public function citasCalendario() {
    $id_medico = $_SESSION['usuario'];
    $fecha_inicio = $_POST['start'] ?? date('Y-m-d');
    $fecha_fin = $_POST['end'] ?? date('Y-m-d', strtotime('+7 days'));
    
    $medico = new Medico();
    $citas = $medico->obtenerCitasParaCalendario($id_medico, $fecha_inicio, $fecha_fin);
    
    ApiResponse::success($citas, 'citas_cargadas', 'Citas cargadas correctamente');}  
    // ==================== VISTAS (Rutas no-API) ====================    
    public function pacientes() {
        AuthHelper::checkRole('medico', true);
        
        $options = [
            'title' => 'Mis Pacientes - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
                ['label' => 'Mis Pacientes']
            ],
            'active_page' => 'pacientes',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_medico' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('medico/med_pacientes', $data, $options);
    }

    public function mostrarAlertas() {
        AuthHelper::checkRole('medico', true);

        $options = [
            'title' => 'Gestión de Alertas Médicas - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
                ['label' => 'Alertas Epidemiológicas']
            ],
            'active_page' => 'alertas',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>'
        ];

        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_medico' => $_SESSION['usuario'] ?? 0
        ];

        ViewHelper::renderDashboard('medico/med_alerta', $data, $options);
    }
}
?>