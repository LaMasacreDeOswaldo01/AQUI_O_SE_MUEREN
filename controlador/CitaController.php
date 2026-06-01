<?php


class CitaController {
    
    private $cita;
    
    public function __construct() {
        $this->cita = new Cita();
    }
    
    // ==================== VISTAS ====================
    
    public function misCitas() {
        AuthHelper::checkRole('paciente', true);
        
        $options = [
            'title' => 'Mis Citas - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/paciente'],
                ['label' => 'Mis Citas']
            ],
            'active_page' => 'citas',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="' . APP_URL . '/js/citas.js"></script>'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_paciente' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('paciente/pac_mis_citas', $data, $options);
    }
    
    public function agendarCitas() {
        AuthHelper::checkRole('paciente', true);
        
        $options = [
            'title' => 'Agendar Cita - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/paciente'],
                ['label' => 'Mis Citas', 'url' => APP_URL . '/paciente/citas'],
                ['label' => 'Agendar Cita']
            ],
            'active_page' => 'citas',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="' . APP_URL . '/js/citas.js"></script>'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_paciente' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('paciente/pac_agendar_citas', $data, $options);
    }
    
    public function agendarPropia() {
        AuthHelper::checkRole('paciente', true);
        
        $options = [
            'title' => 'Agendar Cita Propia - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/paciente'],
                ['label' => 'Mis Citas', 'url' => APP_URL . '/paciente/citas'],
                ['label' => 'Agendar Cita Propia']
            ],
            'active_page' => 'citas',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="' . APP_URL . '/js/citas.js"></script>'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_paciente' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('paciente/pac_agendar_propia', $data, $options);
    }
    
    public function agendarTercero() {
        AuthHelper::checkRole('paciente', true);
        
        $options = [
            'title' => 'Agendar Cita para Tercero - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/paciente'],
                ['label' => 'Mis Citas', 'url' => APP_URL . '/paciente/citas'],
                ['label' => 'Agendar para Tercero']
            ],
            'active_page' => 'citas',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="' . APP_URL . '/js/citas.js"></script>'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_paciente' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('paciente/pac_agendar_tercero', $data, $options);
    }
    
    // ==================== API ====================   
    public function listar() {
        $id_paciente = $_SESSION['usuario'];
        $filtro = isset($_POST['filtro']) ? $_POST['filtro'] : 'proximas';
        
        $citas = $this->cita->listarPorPaciente($id_paciente, $filtro);
        
        // Formatear los datos para la vista
        $resultado = [];
        foreach ($citas as $c) {
            $resultado[] = [
                'id_cita' => $c->id_cita,
                'especialidad' => $c->especialidad_nombre,
                'medico' => $c->medico_nombre,
                'fecha' => date('d', strtotime($c->fecha_cita)),
                'mes' => $this->getMesAbreviado($c->fecha_cita),
                'fecha_completa' => date('d/m/Y', strtotime($c->fecha_cita)),
                'hora' => substr($c->hora_cita, 0, 5),
                'motivo' => $c->motivo,
                'estado' => $c->estado,
                'consultorio_nombre' => $c->consultorio_nombre,
                'consultorio_direccion' => $c->consultorio_direccion
            ];
        }
        
        ApiResponse::success($resultado);
    }
    
    public function crear() {
        $id_paciente = $_SESSION['usuario'];
        $es_tercero = isset($_POST['es_tercero']) ? (int)$_POST['es_tercero'] : 0;
        
        // Validar CSRF
        if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            ApiResponse::csrfError();
            return;
        }
        
        // Validar campos requeridos
        if (empty($_POST['id_especialidad']) || empty($_POST['id_medico']) || 
            empty($_POST['id_consultorio']) || empty($_POST['fecha']) || empty($_POST['hora'])) {
            ApiResponse::error('Todos los campos son requeridos', 'validation_error');
            return;
        }
        
        $datos = [
            ':id_especialidad' => (int)$_POST['id_especialidad'],
            ':id_medico' => (int)$_POST['id_medico'],
            ':id_paciente' => $id_paciente,
            ':id_consultorio' => (int)$_POST['id_consultorio'],
            ':fecha_cita' => $_POST['fecha'],
            ':hora_cita' => $_POST['hora'],
            ':tipo_consulta' => $_POST['tipo_consulta'] ?? 'primera_vez',
            ':motivo' => trim($_POST['motivo'] ?? ''),
            ':es_tercero' => $es_tercero,
            ':nombre_tercero' => $es_tercero ? trim($_POST['nombre_tercero'] ?? '') : null,
            ':cedula_tercero' => $es_tercero ? trim($_POST['cedula_tercero'] ?? '') : null,
            ':telefono_tercero' => $es_tercero ? trim($_POST['telefono_tercero'] ?? '') : null
        ];
        
        // Validar datos del tercero si es necesario
        if ($es_tercero) {
            if (empty($datos[':nombre_tercero']) || empty($datos[':cedula_tercero'])) {
                ApiResponse::error('Los datos del paciente son requeridos', 'validation_error');
                return;
            }
        }
        
        $resultado = $this->cita->crear($datos);
        
        if ($resultado['success']) {
            ApiResponse::created([
                'id_cita' => $resultado['id'],
                'redirect' => APP_URL . '/paciente/citas'
            ], 'Cita agendada exitosamente');
        } else {
            ApiResponse::error($resultado['message'], 'creation_error');
        }
    }
    
    public function cancelar() {
        $id_paciente = $_SESSION['usuario'];
        $id_cita = isset($_POST['id_cita']) ? (int)$_POST['id_cita'] : 0;
        
        if ($id_cita <= 0) {
            ApiResponse::error('ID de cita no válido', 'validation_error');
            return;
        }
        
        $resultado = $this->cita->cancelar($id_cita, $id_paciente);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'cita_cancelada', 'Cita cancelada correctamente');
        } else {
            ApiResponse::error($resultado['message'], 'cancel_error');
        }
    }
    
    public function obtenerHorarios() {
        $id_especialidad = isset($_POST['id_especialidad']) ? (int)$_POST['id_especialidad'] : 0;
        $id_medico = isset($_POST['id_medico']) ? (int)$_POST['id_medico'] : 0;
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
        
        if ($id_especialidad <= 0 || $id_medico <= 0 || empty($fecha)) {
            ApiResponse::error('Datos incompletos', 'validation_error');
            return;
        }
        
        $horarios = $this->cita->obtenerHorariosDisponibles($id_especialidad, $id_medico, $fecha);
        
        ApiResponse::success($horarios);
    }
    
    public function obtenerDetalle() {
        $id_paciente = $_SESSION['usuario'];
<<<<<<< HEAD
        $rol = $_SESSION['rol'] ?? '';
=======
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
        $id_cita = isset($_POST['id_cita']) ? (int)$_POST['id_cita'] : 0;
        
        if ($id_cita <= 0) {
            ApiResponse::error('ID de cita no válido', 'validation_error');
            return;
        }
        
<<<<<<< HEAD
        // Asistentes y administradores pueden ver cualquier cita
        if (in_array($rol, ['asistente', 'administrador'])) {
            $cita = $this->cita->obtenerDetalle($id_cita, 0);
        } else {
            $cita = $this->cita->obtenerDetalle($id_cita, $id_paciente);
        }
=======
        $cita = $this->cita->obtenerDetalle($id_cita, $id_paciente);
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
        
        if ($cita) {
            ApiResponse::success([
                'id_cita' => $cita->id_cita,
<<<<<<< HEAD
                'id_paciente' => $cita->id_paciente,
                'id_medico' => $cita->id_medico,
=======
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
                'especialidad' => $cita->especialidad_nombre,
                'medico' => $cita->medico_nombre,
                'fecha' => $cita->fecha_cita,
                'hora' => $cita->hora_cita,
                'tipo_consulta' => $cita->tipo_consulta,
                'motivo' => $cita->motivo,
                'estado' => $cita->estado,
                'consultorio_nombre' => $cita->consultorio_nombre,
                'consultorio_direccion' => $cita->consultorio_direccion,
                'consultorio_telefono' => $cita->consultorio_telefono,
<<<<<<< HEAD
                'consultorio_email' => $cita->consultorio_email,
                'rol_actual' => $rol
=======
                'consultorio_email' => $cita->consultorio_email
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
            ]);
        } else {
            ApiResponse::notFound('Cita');
        }
    }    
    
    public function estadisticas() {
        $id_paciente = $_SESSION['usuario'];
        $estadisticas = $this->cita->obtenerEstadisticas($id_paciente);
        ApiResponse::success($estadisticas);
    }
    
    private function getMesAbreviado($fecha) {
        $meses = [
            '01' => 'ENE', '02' => 'FEB', '03' => 'MAR', '04' => 'ABR',
            '05' => 'MAY', '06' => 'JUN', '07' => 'JUL', '08' => 'AGO',
            '09' => 'SEP', '10' => 'OCT', '11' => 'NOV', '12' => 'DIC'
        ];
        $mes_num = date('m', strtotime($fecha));
        return $meses[$mes_num];
    }
}
?>
