<?php
<<<<<<< HEAD
<<<<<<< HEAD


=======
// controlador/EspecialidadController.php
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
class EspecialidadController {
    
   public function __construct() {
    if (!AuthHelper::isAuthenticated()) {
        if ($this->isAjax()) {
            jsonResponse(['error' => 'No autorizado'], 401);
        } else {
            AuthHelper::redirectToLogin();
        }
        exit();
    }
}
    
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    // ==================== VISTAS ====================
    
<<<<<<< HEAD
    /**
     * Vista: Listado de especialidades
     */
<<<<<<< HEAD
=======
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
   public function index() {
        AuthHelper::checkRole('administrador', true);
        
        $options = [
            'title' => 'Especialidades Médicas - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
                ['label' => 'Especialidades']
            ],
            'active_page' => 'especialidades',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Administrador',
            'api_url' => APP_URL . '/api/especialidades'
        ];
        
        ViewHelper::renderDashboard('especialidades/esp_listado', $data, $options);
<<<<<<< HEAD
    }
    
    
    /**
     * Vista: Detalle de especialidad
     */
=======
    }   
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
   public function detalle() {
    AuthHelper::checkRole('administrador', true);
    
    // Obtener el ID desde la URL amigable
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        redirect('especialidades');
    }
    
    $options = [
        'title' => 'Detalle de Especialidad - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
            ['label' => 'Especialidades', 'url' => APP_URL . '/especialidades'],
            ['label' => 'Detalle']
        ],
        'active_page' => 'especialidades'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Administrador',
        'id_especialidad' => $id
    ];
    
    ViewHelper::renderDashboard('especialidades/esp_detalle', $data, $options);
<<<<<<< HEAD
}
    
    /**
     * Vista: Crear especialidad
     */
=======
}    
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
   public function crear() {
    AuthHelper::checkRole('administrador', true);
    
    $options = [
        'title' => 'Crear Especialidad - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
            ['label' => 'Especialidades', 'url' => APP_URL . '/especialidades'],
            ['label' => 'Crear']
        ],
        'active_page' => 'especialidades',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Administrador'
    ];
    
    ViewHelper::renderDashboard('especialidades/esp_crear', $data, $options);
<<<<<<< HEAD
}
=======
    public function index() {
        $viewFile = VIEW_PATH . '/especialidades/esp_listado.php';
        if (file_exists($viewFile)) {
            renderView('especialidades/esp_listado');
        } else {
            error_log("Vista no encontrada: " . $viewFile);
            die("Error: Vista de especialidades no encontrada");
        }
    }
    
    /**
     * Vista: Detalle de especialidad
     */
    public function detalle() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            redirect('especialidades');
        }
        
        $viewFile = VIEW_PATH . '/especialidades/esp_detalle.php';
        if (file_exists($viewFile)) {
            renderView('especialidades/esp_detalle');
        } else {
            error_log("Vista no encontrada: " . $viewFile);
            die("Error: Vista de detalle no encontrada");
        }
    }
    
    /**
     * Vista: Crear especialidad
     */
    public function crear() {
        $viewFile = VIEW_PATH . '/especialidades/esp_crear.php';
        if (file_exists($viewFile)) {
            renderView('especialidades/esp_crear');
        } else {
            error_log("Vista no encontrada: " . $viewFile);
            die("Error: Vista de crear especialidad no encontrada");
        }
    }
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    
    /**
     * Vista: Editar especialidad
     */
    public function editar() {
<<<<<<< HEAD
    AuthHelper::checkRole('administrador', true);
    
=======
}    
    
    public function editar() {
    AuthHelper::checkRole('administrador', true);
    
    // Obtener ID de GET - MISMO PATRÓN QUE detalle()
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        redirect('especialidades');
    }
    
    $options = [
        'title' => 'Editar Especialidad - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
            ['label' => 'Especialidades', 'url' => APP_URL . '/especialidades'],
            ['label' => 'Detalle', 'url' => APP_URL . '/especialidades/detalle/' . $id],
            ['label' => 'Editar']
        ],
        'active_page' => 'especialidades',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Administrador',
        'id_especialidad' => $id
    ];
    
    ViewHelper::renderDashboard('especialidades/esp_editar', $data, $options);
<<<<<<< HEAD
}
    
    /**
     * Vista: Asignar médico a especialidad
     */
=======
}    
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
   public function asignarMedico() {
    AuthHelper::checkRole('administrador', true);
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($id <= 0) {
        redirect('especialidades');
    }
    
    $options = [
        'title' => 'Asignar Médico - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
            ['label' => 'Especialidades', 'url' => APP_URL . '/especialidades'],
            ['label' => 'Detalle', 'url' => APP_URL . '/especialidades/detalle/' . $id],
            ['label' => 'Asignar Médico']
        ],
        'active_page' => 'especialidades',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Administrador',
        'id_especialidad' => $id
    ];
    
    ViewHelper::renderDashboard('especialidades/esp_asignar_medico', $data, $options);
}
    
<<<<<<< HEAD
=======
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            redirect('especialidades');
        }
        
        $viewFile = VIEW_PATH . '/especialidades/esp_editar.php';
        if (file_exists($viewFile)) {
            renderView('especialidades/esp_editar');
        } else {
            error_log("Vista no encontrada: " . $viewFile);
            die("Error: Vista de editar especialidad no encontrada");
        }
    }
    
    /**
     * Vista: Asignar médico a especialidad
     */
    public function asignarMedico() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            redirect('especialidades');
        }
        
        $viewFile = VIEW_PATH . '/especialidades/esp_asignar_medico.php';
        if (file_exists($viewFile)) {
            renderView('especialidades/esp_asignar_medico');
        } else {
            error_log("Vista no encontrada: " . $viewFile);
            die("Error: Vista de asignar médico no encontrada");
        }
    }
    
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    // ==================== API - LISTAR ====================
    
    /**
     * API: Listar especialidades
     */
=======
    // ==================== API - LISTAR ====================   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function listar() {
        $busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
        $estado = isset($_POST['estado']) ? $_POST['estado'] : 'todas';
        
        $especialidad = new Especialidad();
        $especialidades = $especialidad->listar($busqueda, $estado);
        
        $resultado = array();
        foreach ($especialidades as $esp) {
            $resultado[] = array(
                'id_especialidad' => $esp->id_especialidad,
                'nombre' => $esp->nombre,
                'descripcion' => $esp->descripcion,
                'codigo' => $esp->codigo,
                'duracion_defecto' => $esp->duracion_defecto,
                'color' => $esp->color,
                'prioridad' => $esp->prioridad,
                'activo' => $esp->activo,
                'total_medicos' => $esp->total_medicos ?? 0,
                'citas_totales' => $esp->citas_totales ?? 0,
                'citas_pendientes' => $esp->citas_pendientes ?? 0
            );
        }
        
        jsonResponse($resultado);
<<<<<<< HEAD
    }
    
    /**
     * API: Obtener estadísticas para el dashboard
     */
=======
    }    
  
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function obtenerEstadisticas() {
        $especialidad = new Especialidad();
        $total_especialidades = $especialidad->totalActivos();
        $total_medicos = $especialidad->totalMedicosAsignados();
        $citas_mes = $especialidad->totalCitasMes();
        
        jsonResponse([
            'total_especialidades' => $total_especialidades,
            'activas' => $total_especialidades,
            'total_medicos' => $total_medicos,
            'citas_mes' => $citas_mes
        ]);
    }
    
<<<<<<< HEAD
    // ==================== API - CRUD ====================
    
    /**
     * API: Obtener detalle de especialidad
     */
=======
    // ==================== API - CRUD ====================    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function obtenerDetalle() {
        $id_especialidad = isset($_POST['id_especialidad']) ? intval($_POST['id_especialidad']) : 0;
        
        if ($id_especialidad <= 0) {
            jsonResponse(['error' => 'ID de especialidad no válido'], 400);
            return;
        }
        
        $especialidad = new Especialidad();
        $datos = $especialidad->obtener($id_especialidad);
        
        if (empty($datos)) {
            jsonResponse(['error' => 'Especialidad no encontrada'], 404);
            return;
        }
        
        $esp_data = $datos[0];
        
        // Obtener médicos asignados
        $medicos = $especialidad->obtenerMedicos($id_especialidad);
        $lista_medicos = array();
        foreach ($medicos as $med) {
            $lista_medicos[] = array(
                'id_medico' => $med->id_medico,
                'nombre' => $med->nombre_medico . ' ' . $med->apellido_medico,
                'mpps' => $med->mpps_registro ?? 'N/A'
            );
        }
        
        jsonResponse([
            'id_especialidad' => $esp_data->id_especialidad,
            'nombre' => $esp_data->nombre,
            'descripcion' => $esp_data->descripcion,
            'codigo' => $esp_data->codigo,
            'duracion_defecto' => $esp_data->duracion_defecto,
            'color' => $esp_data->color,
            'prioridad' => $esp_data->prioridad,
            'orden_visualizacion' => $esp_data->orden_visualizacion,
            'requisitos' => $esp_data->requisitos,
            'observaciones' => $esp_data->observaciones,
            'activo' => $esp_data->activo,
            'total_citas' => $esp_data->citas_totales ?? 0,
            'citas_pendientes' => $esp_data->citas_pendientes ?? 0,
            'duracion' => $esp_data->duracion_defecto,
            'medicos' => $lista_medicos
        ]);
<<<<<<< HEAD
    }
    
    /**
     * API: Crear especialidad
     */
=======
    }    
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function crearEspecialidad() {
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            jsonResponse(['resultado' => 'error_csrf', 'error' => 'Token CSRF inválido']);
            return;
        }
        
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $codigo = trim($_POST['codigo'] ?? '');
        $duracion_defecto = intval($_POST['duracion_defecto'] ?? 30);
        $color = trim($_POST['color'] ?? 'Azul Médico');
        $prioridad = trim($_POST['prioridad'] ?? 'Media');
        $orden_visualizacion = intval($_POST['orden_visualizacion'] ?? 0);
        $requisitos = trim($_POST['requisitos'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');
        
        if (empty($nombre)) {
            jsonResponse(['resultado' => 'error', 'error' => 'El nombre de la especialidad es requerido']);
            return;
        }
        
        $especialidad = new Especialidad();
        ob_start();
        $especialidad->crear($nombre, $descripcion, $codigo, $duracion_defecto, $color, $prioridad, $orden_visualizacion, $requisitos, $observaciones);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
<<<<<<< HEAD
    }
    
    /**
     * API: Editar especialidad
     */
=======
    }    
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function editarEspecialidad() {
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!Security::verificarTokenCSRF($csrf_token)) {
            jsonResponse(['resultado' => 'error_csrf']);
            return;
        }
        
        $id_especialidad = intval($_POST['id_especialidad'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $codigo = trim($_POST['codigo'] ?? '');
        $duracion_defecto = intval($_POST['duracion_defecto'] ?? 30);
        $color = trim($_POST['color'] ?? 'Azul Médico');
        $prioridad = trim($_POST['prioridad'] ?? 'Media');
        $orden_visualizacion = intval($_POST['orden_visualizacion'] ?? 0);
        $requisitos = trim($_POST['requisitos'] ?? '');
        $observaciones = trim($_POST['observaciones'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;
        
        if ($id_especialidad <= 0 || empty($nombre)) {
            jsonResponse(['resultado' => 'error']);
            return;
        }
        
        $especialidad = new Especialidad();
        ob_start();
        $especialidad->editar($id_especialidad, $nombre, $descripcion, $codigo, $duracion_defecto, $color, $prioridad, $orden_visualizacion, $requisitos, $observaciones, $activo);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
<<<<<<< HEAD
    }
    
    /**
     * API: Eliminar especialidad (borrado lógico)
     */
=======
    }    
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function eliminarEspecialidad() {
        $id_especialidad = intval($_POST['id_especialidad'] ?? 0);
        
        if ($id_especialidad <= 0) {
            jsonResponse(['resultado' => 'error']);
            return;
        }
        
        $especialidad = new Especialidad();
        ob_start();
        $especialidad->eliminar($id_especialidad);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
<<<<<<< HEAD
    // ==================== API - MÉDICOS ====================
    
    /**
     * API: Asignar médico a especialidad
     */
=======
    // ==================== API - MÉDICOS ====================    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function asignarMedicoEspecialidad() {
        $id_especialidad = intval($_POST['id_especialidad'] ?? 0);
        $id_medico = intval($_POST['id_medico'] ?? 0);
        $tarifa = floatval($_POST['tarifa'] ?? 0);
        $exp_anios = intval($_POST['exp_anios'] ?? 0);
        $domicilio = isset($_POST['domicilio']) ? 1 : 0;
        $extra = floatval($_POST['extra'] ?? 0);
        
        if ($id_especialidad <= 0 || $id_medico <= 0) {
            jsonResponse(['resultado' => 'error']);
            return;
        }
        
        $especialidad = new Especialidad();
        ob_start();
        $especialidad->asignarMedico($id_especialidad, $id_medico, $tarifa, $exp_anios, $domicilio, $extra);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
<<<<<<< HEAD
    }
    
    /**
     * API: Remover médico de especialidad
     */
=======
    }    
   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function removerMedicoEspecialidad() {
        $id_asignacion = intval($_POST['id_asignacion'] ?? 0);
        
        if ($id_asignacion <= 0) {
            jsonResponse(['resultado' => 'error']);
            return;
        }
        
        $especialidad = new Especialidad();
        ob_start();
        $especialidad->removerMedico($id_asignacion);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
<<<<<<< HEAD
    /**
     * API: Listar médicos disponibles para asignar
     */
    public function listarMedicosDisponibles() {
        $id_especialidad = intval($_POST['id_especialidad'] ?? 0);
        
        $especialidad = new Especialidad();
        $medicos = $especialidad->listarMedicosDisponibles($id_especialidad);
        
        $resultado = array();
        foreach ($medicos as $med) {
            $resultado[] = array(
                'id_medico' => $med->id_medico,
                'nombre' => $med->nombre_medico . ' ' . $med->apellido_medico,
                'cedula' => $med->cedula_medico,
                'mpps' => $med->mpps_registro ?? ''
            );
        }
        
        jsonResponse($resultado);
    }
=======
    
    public function listarMedicosDisponibles() {
    $id_especialidad = intval($_POST['id_especialidad'] ?? 0);
    
    $especialidad = new Especialidad();
    $medicos = $especialidad->listarMedicosDisponibles($id_especialidad);
    
    $resultado = array();
    foreach ($medicos as $med) {
        $resultado[] = array(
            'id_medico' => $med->id_medico,
            'nombre' => $med->nombre_medico . ' ' . $med->apellido_medico,
            'cedula' => $med->cedula_medico,
            'mpps' => $med->mpps_registro ?? ''
        );
    }
    
    jsonResponse($resultado);
}
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
}
?>