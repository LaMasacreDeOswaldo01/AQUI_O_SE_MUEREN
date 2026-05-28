<?php
/**
 * ConsultorioController.php
 * Controlador para la gestión de consultorios
 */

class ConsultorioController {
    
    public function __construct() {
        // Verificar que el usuario esté autenticado y sea administrador
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
            if ($this->isAjax()) {
                jsonResponse(['error' => 'No autorizado'], 401);
            } else {
                redirect('login/administrador');
            }
            exit();
        }
    }
    
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    // ==================== VISTAS ====================
    
    /**
     * Vista: Listado de consultorios
     * GET /consultorios
     */
   public function index() {
    AuthHelper::checkRole('administrador', true);
    
    $options = [
        'title' => 'Consultorios Médicos - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
            ['label' => 'Consultorios']
        ],
        'active_page' => 'consultorios',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Administrador'
    ];
    
    ViewHelper::renderDashboard('administrador/adm_consultorios', $data, $options);
}
    
    /**
     * Vista: Detalle del consultorio
     * GET /consultorios/detalle?id=XXX
     */
   public function detalle() {
    // Obtener el ID desde la URL amigable (GET) o desde el parámetro tradicional
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    // Si no se encuentra en GET, buscar en el parámetro de la ruta
    if ($id <= 0 && isset($_GET['id_param'])) {
        $id = intval($_GET['id_param']);
    }
    
    if ($id <= 0) {
        redirect('consultorios');
    }
    
    // Verificar que exista la vista
    $viewFile = VIEW_PATH . '/administrador/adm_consultorio_detalle.php';
    if (file_exists($viewFile)) {
        renderView('administrador/adm_consultorio_detalle');
    } else {
        error_log("Vista no encontrada: " . $viewFile);
        die("Error: Vista de detalle no encontrada");
    }
}
    
    /**
     * Vista: Crear consultorio
     * GET /consultorios/crear
     */
   public function crear() {
    AuthHelper::checkRole('administrador', true);
    
    $options = [
        'title' => 'Crear Consultorio - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
            ['label' => 'Consultorios', 'url' => APP_URL . '/consultorios'],
            ['label' => 'Crear']
        ],
        'active_page' => 'consultorios',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Administrador'
    ];
    
    ViewHelper::renderDashboard('administrador/adm_consultorio_crear', $data, $options);
}
    
    /**
     * Vista: Editar consultorio
     * GET /consultorios/editar?id=XXX
     */
    public function editar() {
    AuthHelper::checkRole('administrador', true);
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        redirect('consultorios');
    }
    
    $options = [
        'title' => 'Editar Consultorio - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
            ['label' => 'Consultorios', 'url' => APP_URL . '/consultorios'],
            ['label' => 'Detalle', 'url' => APP_URL . '/consultorios/detalle?id=' . $id],
            ['label' => 'Editar']
        ],
        'active_page' => 'consultorios',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Administrador',
        'id_consultorio' => $id
    ];
    
    ViewHelper::renderDashboard('administrador/adm_consultorio_editar', $data, $options);
}
    
    /**
     * Vista: Horarios del consultorio
     * GET /consultorios/horarios?id=XXX
     */
   public function horarios() {
    AuthHelper::checkRole('administrador', true);
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        redirect('consultorios');
    }
    
    $options = [
        'title' => 'Horarios de Consultorio - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
            ['label' => 'Consultorios', 'url' => APP_URL . '/consultorios'],
            ['label' => 'Detalle', 'url' => APP_URL . '/consultorios/detalle?id=' . $id],
            ['label' => 'Horarios']
        ],
        'active_page' => 'consultorios',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Administrador',
        'id_consultorio' => $id
    ];
    
    ViewHelper::renderDashboard('administrador/adm_consultorio_horarios', $data, $options);
}
    
    // ==================== API - LISTAR ====================
    
    /**
     * API: Listar consultorios
     * POST /api/consultorios/listar
     */
    public function listar() {
        $busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
        
        $consultorio = new Consultorio();
        $consultorios = $consultorio->listar($busqueda);
        
        $resultado = array();
        foreach ($consultorios as $c) {
            $resultado[] = array(
                'id_consultorio' => $c->id_consultorio,
                'nombre' => $c->nombre,
                'descripcion' => $c->descripcion,
                'ciudad' => $c->ciudad,
                'telefono' => $c->telefono,
                'apertura_habitual' => $c->apertura_habitual,
                'cierre_habitual' => $c->cierre_habitual,
                'total_medicos' => $c->total_medicos ?? 0,
                'direccion_detallada' => $c->direccion_detallada
            );
        }
        
        jsonResponse($resultado);
    }
    
    /**
     * API: Obtener estadísticas
     * POST /api/consultorios/estadisticas
     */
    public function obtenerEstadisticas() {
        $consultorio = new Consultorio();
        $total_activos = $consultorio->totalActivos();
        
        jsonResponse([
            'total_consultorios' => $total_activos,
            'activos' => $total_activos
        ]);
    }
    
    // ==================== API - CRUD ====================
    
    /**
     * API: Obtener detalle de consultorio
     * POST /api/consultorios/obtener-detalle
     */
    public function obtenerDetalle() {
        $id_consultorio = isset($_POST['id_consultorio']) ? intval($_POST['id_consultorio']) : 0;
        
        if ($id_consultorio <= 0) {
            jsonResponse(['error' => 'ID de consultorio no válido'], 400);
            return;
        }
        
        $consultorio = new Consultorio();
        $datos = $consultorio->obtener($id_consultorio);
        
        if (empty($datos)) {
            jsonResponse(['error' => 'Consultorio no encontrado'], 404);
            return;
        }
        
        $consultorio_data = $datos[0];
        
        // Obtener especialidades
        $especialidades = $consultorio->obtenerEspecialidades($id_consultorio);
        $lista_especialidades = array();
        foreach ($especialidades as $esp) {
            $lista_especialidades[] = $esp->especialidad;
        }
        
        // Obtener médicos
        $medicos = $consultorio->obtenerMedicos($id_consultorio);
        $lista_medicos = array();
        foreach ($medicos as $med) {
            $lista_medicos[] = array(
                'id' => $med->id,
                'id_medico' => $med->id_medico,
                'nombre' => $med->nombre_medico . ' ' . $med->apellido_medico,
                'cedula' => $med->cedula_medico,
                'telefono' => $med->telefono_medico
            );
        }
        
        jsonResponse([
            'id_consultorio' => $consultorio_data->id_consultorio,
            'nombre' => $consultorio_data->nombre,
            'descripcion' => $consultorio_data->descripcion,
            'apertura' => $consultorio_data->apertura_habitual,
            'cierre' => $consultorio_data->cierre_habitual,
            'telefono' => $consultorio_data->telefono,
            'email' => $consultorio_data->email,
            'estado' => $consultorio_data->estado,
            'ciudad' => $consultorio_data->ciudad,
            'municipio' => $consultorio_data->municipio,
            'parroquia' => $consultorio_data->parroquia,
            'direccion_detallada' => $consultorio_data->direccion_detallada,
            'especialidades' => $lista_especialidades,
            'medicos' => $lista_medicos
        ]);
    }
    
    /**
     * API: Crear consultorio
     * POST /api/consultorios/crear
     */
    public function crearConsultorio() {
        // Verificar token CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            jsonResponse(['resultado' => 'error_csrf', 'error' => 'Token CSRF inválido']);
            return;
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $apertura = $_POST['apertura'] ?? '08:00';
        $cierre = $_POST['cierre'] ?? '17:00';
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $id_estado = intval($_POST['id_estado'] ?? 0);
        $id_ciudad = intval($_POST['id_ciudad'] ?? 0);
        $id_municipio = intval($_POST['id_municipio'] ?? 0);
        $id_parroquia = intval($_POST['id_parroquia'] ?? 0);
        $direccion = trim($_POST['direccion'] ?? '');
        $especialidades = isset($_POST['especialidades']) ? (array)$_POST['especialidades'] : array();
        
        if (empty($nombre) || $id_estado <= 0 || $id_ciudad <= 0 || empty($direccion)) {
            jsonResponse(['resultado' => 'error', 'error' => 'Campos requeridos incompletos']);
            return;
        }
        
        $consultorio = new Consultorio();
        ob_start();
        $consultorio->crear($nombre, $descripcion, $apertura, $cierre, $telefono, $email, 
                           $id_estado, $id_ciudad, $id_municipio, $id_parroquia, $direccion, $especialidades);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    /**
     * API: Editar consultorio
     * POST /api/consultorios/editar
     */
    public function editarConsultorio() {
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            jsonResponse(['resultado' => 'error_csrf']);
            return;
        }
        
        $id_consultorio = intval($_POST['id_consultorio'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $apertura = $_POST['apertura'] ?? '08:00';
        $cierre = $_POST['cierre'] ?? '17:00';
        $telefono = trim($_POST['telefono'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $estado = trim($_POST['estado'] ?? '');
        $ciudad = trim($_POST['ciudad'] ?? '');
        $municipio = trim($_POST['municipio'] ?? '');
        $parroquia = trim($_POST['parroquia'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $especialidades = isset($_POST['especialidades']) ? (array)$_POST['especialidades'] : array();
        
        if ($id_consultorio <= 0 || empty($nombre)) {
            jsonResponse(['resultado' => 'error']);
            return;
        }
        
        $consultorio = new Consultorio();
        ob_start();
        $consultorio->editar($id_consultorio, $nombre, $descripcion, $apertura, $cierre, 
                             $telefono, $email, $estado, $ciudad, $municipio, $parroquia, 
                             $direccion, $especialidades);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    /**
     * API: Eliminar consultorio
     * POST /api/consultorios/eliminar
     */
    public function eliminarConsultorio() {
        $id_consultorio = intval($_POST['id_consultorio'] ?? 0);
        
        if ($id_consultorio <= 0) {
            jsonResponse(['resultado' => 'error']);
            return;
        }
        
        $consultorio = new Consultorio();
        ob_start();
        $consultorio->eliminar($id_consultorio);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    // ==================== API - MÉDICOS ====================
    
    /**
     * API: Asignar médico a consultorio
     * POST /api/consultorios/asignar-medico
     */
    public function asignarMedico() {
        $id_consultorio = intval($_POST['id_consultorio'] ?? 0);
        $id_medico = intval($_POST['id_medico'] ?? 0);
        
        if ($id_consultorio <= 0 || $id_medico <= 0) {
            jsonResponse(['resultado' => 'error']);
            return;
        }
        
        $consultorio = new Consultorio();
        ob_start();
        $consultorio->asignarMedico($id_consultorio, $id_medico);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    /**
     * API: Remover médico de consultorio
     * POST /api/consultorios/remover-medico
     */
    public function removerMedico() {
        $id_asignacion = intval($_POST['id_asignacion'] ?? 0);
        
        if ($id_asignacion <= 0) {
            jsonResponse(['resultado' => 'error']);
            return;
        }
        
        $consultorio = new Consultorio();
        ob_start();
        $consultorio->removerMedico($id_asignacion);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    /**
     * API: Listar médicos disponibles
     * POST /api/consultorios/listar-medicos
     */
    public function listarMedicosDisponibles() {
        $consultorio = new Consultorio();
        $medicos = $consultorio->listarMedicos();
        
        $resultado = array();
        foreach ($medicos as $med) {
            $resultado[] = array(
                'id_medico' => $med->id_medico,
                'nombre_medico' => $med->nombre_medico,
                'apellido_medico' => $med->apellido_medico,
                'cedula_medico' => $med->cedula_medico
            );
        }
        
        jsonResponse($resultado);
    }
    
    // ==================== API - HORARIOS ====================
    
    /**
     * API: Obtener horarios del consultorio
     * POST /api/consultorios/obtener-horarios
     */
    public function obtenerHorarios() {
        $id_consultorio = intval($_POST['id_consultorio'] ?? 0);
        
        if ($id_consultorio <= 0) {
            jsonResponse(['error' => 'ID no válido'], 400);
            return;
        }
        
        $consultorio = new Consultorio();
        $horarios_db = $consultorio->obtenerHorarios($id_consultorio);
        $medicos = $consultorio->listarMedicos();
        
        // Organizar horarios por día y turno
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $turnos = ['Mañana', 'Tarde'];
        
        $horarios = array();
        foreach ($dias as $dia) {
            $horarios[$dia] = array();
            foreach ($turnos as $turno) {
                $horarios[$dia][$turno] = null;
            }
        }
        
        foreach ($horarios_db as $h) {
            $horarios[$h->dia_semana][$h->turno] = array(
                'hora_inicio' => substr($h->hora_inicio, 0, 5),
                'hora_fin' => substr($h->hora_fin, 0, 5),
                'id_medico' => $h->id_medico,
                'nombre_medico' => $h->nombre_medico ?? null
            );
        }
        
        $lista_medicos = array();
        foreach ($medicos as $med) {
            $lista_medicos[] = array(
                'id' => $med->id_medico,
                'nombre' => $med->nombre_medico . ' ' . $med->apellido_medico,
                'cedula' => $med->cedula_medico
            );
        }
        
        jsonResponse([
            'horarios' => $horarios,
            'medicos' => $lista_medicos
        ]);
    }
    
    /**
     * API: Guardar horario
     * POST /api/consultorios/guardar-horario
     */
    public function guardarHorario() {
        $id_consultorio = intval($_POST['id_consultorio'] ?? 0);
        $dia = $_POST['dia'] ?? '';
        $turno = $_POST['turno'] ?? '';
        $hora_inicio = $_POST['hora_inicio'] ?? '';
        $hora_fin = $_POST['hora_fin'] ?? '';
        $id_medico = !empty($_POST['id_medico']) ? intval($_POST['id_medico']) : null;
        
        // Validaciones
        if ($id_consultorio <= 0 || empty($dia) || empty($turno) || empty($hora_inicio) || empty($hora_fin)) {
            jsonResponse(['resultado' => 'error', 'mensaje' => 'Datos incompletos']);
            return;
        }
        
        // Validar que hora_fin sea mayor que hora_inicio
        if ($hora_inicio >= $hora_fin) {
            jsonResponse(['resultado' => 'error_horario', 'mensaje' => 'La hora de fin debe ser mayor que la hora de inicio']);
            return;
        }
        
        // Si se asigna médico, verificar que no tenga conflicto de horario
        if ($id_medico) {
            $consultorio = new Consultorio();
            $conflictos = $consultorio->verificarHorarioMedico($id_medico, $dia, $turno, $id_consultorio);
            
            if (!empty($conflictos)) {
                $conflicto = $conflictos[0];
                jsonResponse([
                    'resultado' => 'error_duplicado', 
                    'mensaje' => "El médico ya tiene un horario asignado en {$conflicto->consultorio_nombre} los {$conflicto->dia_semana} en el turno {$conflicto->turno}"
                ]);
                return;
            }
        }
        
        $consultorio = new Consultorio();
        ob_start();
        $consultorio->guardarHorario($id_consultorio, $dia, $turno, $hora_inicio, $hora_fin, $id_medico);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    // ==================== API - UBICACIÓN ====================
    
    /**
     * API: Listar estados
     * POST /api/ubicacion/estados
     */
    public function listarEstados() {
        $consultorio = new Consultorio();
        $estados = $consultorio->listarEstados();
        jsonResponse($estados);
    }
    
    /**
     * API: Listar ciudades por estado
     * POST /api/ubicacion/ciudades
     */
    public function listarCiudades() {
        $id_estado = intval($_POST['id_estado'] ?? 0);
        
        if ($id_estado <= 0) {
            jsonResponse([]);
            return;
        }
        
        $consultorio = new Consultorio();
        $ciudades = $consultorio->listarCiudades($id_estado);
        jsonResponse($ciudades);
    }
    
    /**
     * API: Listar municipios por estado
     * POST /api/ubicacion/municipios
     */
    public function listarMunicipios(){
        $id_estado = intval($_POST['id_estado'] ?? 0);
        
        if ($id_estado <= 0) {
            jsonResponse([]);
            return;
        }
        
        $consultorio = new Consultorio();
        $municipios = $consultorio->listarMunicipios($id_estado);
        jsonResponse($municipios);
    }
    
    /**
     * API: Listar parroquias por municipio
     * POST /api/ubicacion/parroquias
     */
    public function listarParroquias() {
        $id_municipio = intval($_POST['id_municipio'] ?? 0);
        
        if ($id_municipio <= 0) {
            jsonResponse([]);
            return;
        }
        
        $consultorio = new Consultorio();
        $parroquias = $consultorio->listarParroquias($id_municipio);
        jsonResponse($parroquias);
    }
    
    /**
     * API: Listar especialidades
     * POST /api/ubicacion/especialidades
     */
    public function listaEspecialidades() {
        $consultorio = new Consultorio();
        $especialidades = $consultorio->obtenerListaEspecialidades();
        jsonResponse($especialidades);
    }
}
?>
