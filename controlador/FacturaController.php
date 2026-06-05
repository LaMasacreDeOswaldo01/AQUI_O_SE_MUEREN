<?php
<<<<<<< HEAD
// controlador/FacturaController.php

class FacturaController {
    private $factura;
    
    public function __construct() {
        // Verificar autenticación
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            if ($this->isAjax()) {
                jsonResponse(['error' => 'No autorizado'], 401);
            } else {
                redirect('login');
            }
            exit();
        }
        
        // Cargar el modelo
        require_once MODEL_PATH . '/Factura.php';
        $this->factura = new Factura();
    }
    
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
=======
class FacturaController {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    
    // ==================== VISTAS ====================
    
    /**
<<<<<<< HEAD
     * Vista principal de facturación
     */
    public function index() {
        $rol = $_SESSION['rol'];
        
        if (!in_array($rol, ['administrador', 'asistente', 'paciente'])) {
            redirect('login');
            return;
        }
        
        switch ($rol) {
            case 'administrador':
                $vista = 'administrador/adm_facturacion';
                $titulo = 'Administración de Facturación - BioVital';
                break;
            case 'asistente':
                $vista = 'asistente/asi_facturacion';
                $titulo = 'Gestión de Facturación - BioVital';
                break;
            case 'paciente':
                $vista = 'paciente/pac_facturacion';
                $titulo = 'Mis Facturas - BioVital';
                break;
            default:
                redirect('login');
                return;
        }
        
        // Cargar especialidades para el formulario de creación si es asistente
        $especialidades_list = [];
        if ($rol === 'asistente' || $rol === 'administrador') {
            require_once MODEL_PATH . '/Especialidad.php';
            $esp = new Especialidad();
            $especialidades_list = $esp->listar('', 'activas');
        }
        
        $options = [
            'title' => $titulo,
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/' . $rol],
                ['label' => 'Facturación']
            ],
            'active_page' => 'facturacion',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'especialidades' => $especialidades_list
        ];
        
        ViewHelper::renderDashboard($vista, $data, $options);
    }
    
    /**
     * Vista de detalle de factura (Imprimible)
     */
    public function verDetalle() {
        // El id viene del router dinámico de index.php que llena $_GET
        $id_factura = $_GET['id'] ?? 0;
        
        if (!$id_factura) {
            die("ID de factura no válido");
        }
        
        $factura_data = $this->factura->obtener($id_factura);
        if (!$factura_data) {
            http_response_code(404);
            die("Factura no encontrada");
        }
        
        // Si el usuario es un paciente, verificar que sea el propietario de la factura
        if ($_SESSION['rol'] === 'paciente' && $factura_data->id_paciente != $_SESSION['usuario']) {
            http_response_code(403);
            die("No tiene permisos para ver esta factura");
        }
        
        $detalles = $this->factura->obtenerDetalles($id_factura);
        
        $data = [
            'factura' => $factura_data,
            'detalles' => $detalles
        ];
        
        // Renderizar vista simple (sin cabecera del dashboard para poder imprimir con Ctrl+P)
        ViewHelper::renderSimple('documentos/factura_detalle', $data);
    }
    
    // ==================== API ENDPOINTS ====================
    
    /**
     * API para listar facturas
     */
    public function listar() {
        $rol = $_SESSION['rol'];
        $id_paciente = ($rol === 'paciente') ? $_SESSION['usuario'] : null;
        
        $busqueda = $_POST['busqueda'] ?? '';
        $fecha_inicio = $_POST['fecha_inicio'] ?? '';
        $fecha_fin = $_POST['fecha_fin'] ?? '';
        $estado = $_POST['estado'] ?? 'todos';
        
        try {
            $facturas = $this->factura->listar($busqueda, $fecha_inicio, $fecha_fin, $id_paciente, $estado);
            jsonResponse($facturas);
        } catch (Exception $e) {
            error_log("Error en API listar facturas: " . $e->getMessage());
            jsonResponse(['error' => 'Error al cargar las facturas'], 500);
        }
    }
    
    /**
     * API para obtener los datos completos de una factura
     */
    public function obtener() {
        $id_factura = $_POST['id_factura'] ?? 0;
        
        if (!$id_factura) {
            jsonResponse(['error' => 'ID de factura requerido'], 400);
            return;
        }
        
        try {
            $factura_data = $this->factura->obtener($id_factura);
            if (!$factura_data) {
                jsonResponse(['error' => 'Factura no encontrada'], 404);
                return;
            }
            
            // Si el usuario es un paciente, verificar que sea el propietario
            if ($_SESSION['rol'] === 'paciente' && $factura_data->id_paciente != $_SESSION['usuario']) {
                jsonResponse(['error' => 'No autorizado'], 403);
                return;
            }
            
            $detalles = $this->factura->obtenerDetalles($id_factura);
            
            jsonResponse([
                'factura' => $factura_data,
                'detalles' => $detalles
            ]);
        } catch (Exception $e) {
            error_log("Error en API obtener factura: " . $e->getMessage());
            jsonResponse(['error' => 'Error del servidor'], 500);
        }
    }
    
    /**
     * API para registrar una nueva factura
     */
    public function crear() {
        // Verificar permisos
        if (!in_array($_SESSION['rol'], ['asistente', 'administrador'])) {
            jsonResponse(['success' => false, 'message' => 'No autorizado para realizar esta acción']);
            return;
        }
        
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $subtotal = $_POST['subtotal'] ?? 0;
        $iva = $_POST['iva'] ?? 0;
        $descuento = $_POST['descuento'] ?? 0;
        $total = $_POST['total'] ?? 0;
        $metodo_pago = $_POST['metodo_pago'] ?? '';
        $estado_pago = $_POST['estado_pago'] ?? 'Pendiente';
        $notas = $_POST['notas'] ?? '';
        
        // Decodificar items
        $items_raw = $_POST['items'] ?? '[]';
        $items = json_decode($items_raw, true);
        
        if (!$id_paciente || empty($items) || !$metodo_pago) {
            jsonResponse(['success' => false, 'message' => 'Datos incompletos. Debe seleccionar un paciente, al menos un concepto y un método de pago.']);
            return;
        }
        
        $id_asistente = ($_SESSION['rol'] === 'asistente') ? $_SESSION['usuario'] : null;
        
        try {
            $resultado = $this->factura->crear(
                $id_paciente, $id_asistente, $subtotal, $iva, $descuento, $total, $metodo_pago, $estado_pago, $notas, $items
            );
            jsonResponse($resultado);
        } catch (Exception $e) {
            error_log("Error al crear factura en API: " . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Error al guardar la factura'], 500);
=======
     * Listado de facturas (asistente y administrador)
     */
    public function index() {
        $this->verificarRol(['asistente', 'administrador']);
        
        $factura = new Factura();
        $facturas = $factura->listarTodas();
        
        $options = [
            'title' => 'Gestión de Facturas - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/' . $_SESSION['rol']],
                ['label' => 'Gestión de Facturas']
            ],
            'active_page' => 'facturas'
        ];
        
        $data = [
            'facturas' => $facturas,
            'rol' => $_SESSION['rol']
        ];
        
        ViewHelper::renderDashboard('facturas/listado', $data, $options);
    }
    
    /**
     * Listado de facturas del paciente actual
     */
    public function misFacturas() {
        $this->verificarRol(['paciente']);
        
        $id_paciente = $_SESSION['usuario'];
        $factura = new Factura();
        $facturas = $factura->listarPorPaciente($id_paciente);
        
        $options = [
            'title' => 'Mis Facturas - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/paciente'],
                ['label' => 'Mis Facturas']
            ],
            'active_page' => 'mis-facturas'
        ];
        
        $data = [
            'facturas' => $facturas
        ];
        
        ViewHelper::renderDashboard('facturas/mis_facturas', $data, $options);
    }
    
    /**
     * Listado de facturas del médico actual
     * NOTA: Según nuevas reglas, el médico NO ve facturas, solo configura su tarifa
     */
    public function facturasMedico() {
        $this->verificarRol(['medico']);
        
        // Redirigir a configuración de tarifa
        redirect('/facturas/configurar-tarifa');
    }
    
    /**
     * Detalle de factura
     */
    public function detalle() {
        $id_factura = $_GET['id'] ?? 0;
        
        if (!$id_factura) {
            redirect('/');
        }
        
        $factura = new Factura();
        $facturaData = $factura->obtenerPorId($id_factura);
        
        if (!$facturaData) {
            redirect('/');
        }
        
        // Verificar permisos según rol
        $rol = $_SESSION['rol'] ?? '';
        $user_id = $_SESSION['usuario'] ?? 0;
        
        if ($rol === 'paciente' && $facturaData->id_paciente != $user_id) {
            redirect('/');
        }
        
        if ($rol === 'medico' && $facturaData->id_medico != $user_id) {
            redirect('/');
        }
        
        $detalles = $factura->obtenerDetalles($id_factura);
        $auditoria = $factura->obtenerAuditoria($id_factura);
        $configPagoMovil = $factura->obtenerConfigPagoMovil();
        
        // Determinar permisos
        $permisos = $this->obtenerPermisos($rol);
        
        renderView('facturas/detalle', [
            'factura' => $facturaData,
            'detalles' => $detalles,
            'auditoria' => $auditoria,
            'config_pago_movil' => $configPagoMovil,
            'permisos' => $permisos,
            'rol' => $rol
        ]);
    }
    
    /**
     * Formulario para crear factura desde cita
     */
    public function crearDesdeCita() {
        $this->verificarRol(['asistente', 'administrador']);
        
        $id_cita = $_GET['id_cita'] ?? 0;
        
        if (!$id_cita) {
            redirect('/');
        }
        
        // Obtener datos de la cita
        $cita = new Cita();
        $datosCita = $cita->obtenerPorId($id_cita);
        
        if (!$datosCita) {
            redirect('/');
        }
        
        renderView('facturas/crear', [
            'cita' => $datosCita
        ]);
    }
    
    // ==================== API ====================
    
    /**
     * Crear factura
     */
    public function crear() {
        $this->verificarRol(['asistente', 'administrador']);
        
        $id_cita = $_POST['id_cita'] ?? 0;
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_medico = $_POST['id_medico'] ?? 0;
        $fecha_cita = $_POST['fecha_cita'] ?? '';
        $subtotal = $_POST['subtotal'] ?? 0;
        $iva = $_POST['iva'] ?? 0;
        $total = $_POST['total'] ?? 0;
        $forma_pago = $_POST['forma_pago'] ?? 'pago_movil';
        $observaciones = $_POST['observaciones'] ?? '';
        
        $detalles = $_POST['detalles'] ?? [];
        
        if (empty($id_cita) || empty($id_paciente) || empty($id_medico)) {
            ApiResponse::error('Datos incompletos', 'validation_error', [], 400);
            return;
        }
        
        // Si no se proporcionan detalles, usar la tarifa del médico automáticamente
        if (empty($detalles) || empty($subtotal)) {
            $medico = new Medico();
            $tarifa = $medico->obtenerTarifa($id_medico);
            
            $detalles = [[
                'concepto' => 'Consulta médica',
                'descripcion' => 'Consulta con ' . ($_POST['nombre_medico'] ?? 'médico'),
                'cantidad' => 1,
                'precio_unitario' => $tarifa,
                'subtotal' => $tarifa
            ]];
            
            $subtotal = $tarifa;
            $iva = 0;
            $total = $tarifa;
        }
        
        $factura = new Factura();
        $resultado = $factura->crear([
            'id_cita' => $id_cita,
            'id_paciente' => $id_paciente,
            'id_medico' => $id_medico,
            'fecha_cita' => $fecha_cita,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total,
            'forma_pago' => $forma_pago,
            'observaciones' => $observaciones,
            'detalles' => $detalles,
            'creado_por' => $_SESSION['user_id'],
            'rol_usuario' => $_SESSION['rol']
        ]);
        
        if ($resultado['success']) {
            ApiResponse::created([
                'id_factura' => $resultado['id_factura'],
                'numero_factura' => $resultado['numero_factura'],
                'redirect' => APP_URL . '/facturas/detalle?id=' . $resultado['id_factura']
            ], 'Factura creada exitosamente');
        } else {
            ApiResponse::error('Error al crear factura', 'creation_error', [], 500);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
    
    /**
<<<<<<< HEAD
     * API para editar una factura existente (Autorizado para Asistente)
     */
    public function editar() {
        if (!in_array($_SESSION['rol'], ['asistente', 'administrador'])) {
            jsonResponse(['success' => false, 'message' => 'No autorizado para realizar esta acción']);
            return;
        }
        
        $id_factura = $_POST['id_factura'] ?? 0;
        $subtotal = $_POST['subtotal'] ?? 0;
        $iva = $_POST['iva'] ?? 0;
        $descuento = $_POST['descuento'] ?? 0;
        $total = $_POST['total'] ?? 0;
        $metodo_pago = $_POST['metodo_pago'] ?? '';
        $estado_pago = $_POST['estado_pago'] ?? '';
        $notas = $_POST['notas'] ?? '';
        
        $items_raw = $_POST['items'] ?? '[]';
        $items = json_decode($items_raw, true);
        
        if (!$id_factura || empty($items) || !$metodo_pago || !$estado_pago) {
            jsonResponse(['success' => false, 'message' => 'Datos de edición incompletos']);
            return;
        }
        
        try {
            $resultado = $this->factura->editar(
                $id_factura, $subtotal, $iva, $descuento, $total, $metodo_pago, $estado_pago, $notas, $items
            );
            jsonResponse($resultado);
        } catch (Exception $e) {
            error_log("Error al editar factura en API: " . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Error al actualizar la factura'], 500);
        }
    }
    
    /**
     * API para anular una factura
     */
    public function anular() {
        if (!in_array($_SESSION['rol'], ['asistente', 'administrador'])) {
            jsonResponse(['success' => false, 'message' => 'No autorizado para realizar esta acción']);
            return;
        }
        
        $id_factura = $_POST['id_factura'] ?? 0;
        if (!$id_factura) {
            jsonResponse(['success' => false, 'message' => 'ID de factura requerido']);
            return;
        }
        
        try {
            $resultado = $this->factura->anular($id_factura);
            jsonResponse($resultado);
        } catch (Exception $e) {
            error_log("Error al anular factura en API: " . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Error al anular la factura'], 500);
=======
     * Actualizar factura
     */
    public function actualizar() {
        $this->verificarRol(['asistente', 'administrador']);
        
        $id_factura = $_POST['id_factura'] ?? 0;
        
        if (!$id_factura) {
            ApiResponse::error('ID de factura requerido', 'validation_error', [], 400);
            return;
        }
        
        $factura = new Factura();
        $facturaData = $factura->obtenerPorId($id_factura);
        
        if (!$facturaData) {
            ApiResponse::error('Factura no encontrada', 'not_found', [], 404);
            return;
        }
        
        // Verificar que no esté pagada
        if ($facturaData->estado === 'pagada') {
            ApiResponse::error('No se puede modificar una factura pagada', 'invalid_state', [], 400);
            return;
        }
        
        $datos = [
            'subtotal' => $_POST['subtotal'] ?? null,
            'iva' => $_POST['iva'] ?? null,
            'total' => $_POST['total'] ?? null,
            'observaciones' => $_POST['observaciones'] ?? null,
            'forma_pago' => $_POST['forma_pago'] ?? null,
            'detalles' => $_POST['detalles'] ?? null
        ];
        
        $resultado = $factura->actualizar($id_factura, $datos, $_SESSION['user_id'], $_SESSION['rol']);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'Factura actualizada exitosamente');
        } else {
            ApiResponse::error('Error al actualizar factura', 'update_error', [], 500);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
    
    /**
<<<<<<< HEAD
     * API para obtener estadísticas de facturación (Administrador)
     */
    public function estadisticas() {
        if ($_SESSION['rol'] !== 'administrador') {
            jsonResponse(['error' => 'No autorizado'], 403);
            return;
        }
        
        try {
            $stats = $this->factura->obtenerEstadisticas();
            jsonResponse($stats);
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas de facturación: " . $e->getMessage());
            jsonResponse(['error' => 'Error al cargar las estadísticas'], 500);
        }
=======
     * Marcar factura como pagada (pago móvil)
     */
    public function marcarPagada() {
        $this->verificarRol(['paciente', 'asistente', 'administrador']);
        
        $id_factura = $_POST['id_factura'] ?? 0;
        $referencia_pago = trim($_POST['referencia_pago'] ?? '');
        
        if (!$id_factura) {
            ApiResponse::error('ID de factura requerido', 'validation_error', [], 400);
            return;
        }
        
        if (empty($referencia_pago)) {
            ApiResponse::error('La referencia de pago es requerida', 'validation_error', [], 400);
            return;
        }
        
        $factura = new Factura();
        $facturaData = $factura->obtenerPorId($id_factura);
        
        if (!$facturaData) {
            ApiResponse::error('Factura no encontrada', 'not_found', [], 404);
            return;
        }
        
        // Verificar permisos
        $rol = $_SESSION['rol'];
        $user_id = $_SESSION['user_id'];
        
        if ($rol === 'paciente' && $facturaData->id_paciente != $user_id) {
            ApiResponse::error('No tienes permiso para marcar esta factura como pagada', 'permission_denied', [], 403);
            return;
        }
        
        if ($facturaData->estado === 'pagada') {
            ApiResponse::error('La factura ya está pagada', 'invalid_state', [], 400);
            return;
        }
        
        $resultado = $factura->marcarPagada($id_factura, $referencia_pago, $user_id, $rol);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'Pago confirmado exitosamente');
        } else {
            ApiResponse::error('Error al confirmar pago', 'update_error', [], 500);
        }
    }
    
    /**
     * Cancelar factura
     */
    public function cancelar() {
        $this->verificarRol(['administrador']);
        
        $id_factura = $_POST['id_factura'] ?? 0;
        
        if (!$id_factura) {
            ApiResponse::error('ID de factura requerido', 'validation_error', [], 400);
            return;
        }
        
        $factura = new Factura();
        $resultado = $factura->cancelar($id_factura, $_SESSION['user_id'], $_SESSION['rol']);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'Factura cancelada exitosamente');
        } else {
            ApiResponse::error('Error al cancelar factura', 'update_error', [], 500);
        }
    }
    
    /**
     * Eliminar factura
     */
    public function eliminar() {
        $this->verificarRol(['administrador']);
        
        $id_factura = $_POST['id_factura'] ?? 0;
        
        if (!$id_factura) {
            ApiResponse::error('ID de factura requerido', 'validation_error', [], 400);
            return;
        }
        
        $factura = new Factura();
        $resultado = $factura->eliminar($id_factura, $_SESSION['user_id'], $_SESSION['rol']);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'Factura eliminada exitosamente');
        } else {
            ApiResponse::error('Error al eliminar factura', 'delete_error', [], 500);
        }
    }
    
    /**
     * Buscar facturas (API para asistente y administrador)
     */
    public function buscar() {
        $this->verificarRol(['asistente', 'administrador']);
        
        $filtros = [
            'numero' => $_POST['numero'] ?? '',
            'paciente' => $_POST['paciente'] ?? '',
            'estado' => $_POST['estado'] ?? '',
            'fecha_desde' => $_POST['fecha_desde'] ?? '',
            'fecha_hasta' => $_POST['fecha_hasta'] ?? ''
        ];
        
        $factura = new Factura();
        $facturas = $factura->listarTodas($filtros);
        
        ApiResponse::success(['facturas' => $facturas], 'Facturas encontradas');
    }
    
    /**
     * Obtener datos de factura para edición
     */
    public function obtenerDatos() {
        $id_factura = $_POST['id_factura'] ?? 0;
        
        if (!$id_factura) {
            ApiResponse::error('ID de factura requerido', 'validation_error', [], 400);
            return;
        }
        
        $factura = new Factura();
        $facturaData = $factura->obtenerPorId($id_factura);
        
        if (!$facturaData) {
            ApiResponse::error('Factura no encontrada', 'not_found', [], 404);
            return;
        }
        
        // Verificar permisos
        $rol = $_SESSION['rol'];
        $user_id = $_SESSION['user_id'];
        
        if ($rol === 'paciente' && $facturaData->id_paciente != $user_id) {
            ApiResponse::error('No tienes permiso para ver esta factura', 'permission_denied', [], 403);
            return;
        }
        
        if ($rol === 'medico' && $facturaData->id_medico != $user_id) {
            ApiResponse::error('No tienes permiso para ver esta factura', 'permission_denied', [], 403);
            return;
        }
        
        $detalles = $factura->obtenerDetalles($id_factura);
        
        ApiResponse::success([
            'factura' => $facturaData,
            'detalles' => $detalles
        ], 'Datos de factura');
    }
    
    /**
     * Vista para que el médico configure su tarifa
     */
    public function configurarTarifa() {
        $this->verificarRol(['medico']);
        
        $id_medico = $_SESSION['user_id'];
        $medico = new Medico();
        $tarifaActual = $medico->obtenerTarifa($id_medico);
        
        $options = [
            'title' => 'Configurar Tarifa - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
                ['label' => 'Configurar Tarifa']
            ],
            'active_page' => 'configurar-tarifa'
        ];
        
        $data = [
            'tarifa_actual' => $tarifaActual
        ];
        
        ViewHelper::renderDashboard('facturas/configurar_tarifa', $data, $options);
    }
    
    /**
     * API para actualizar la tarifa del médico
     */
    public function actualizarTarifa() {
        $this->verificarRol(['medico']);
        
        $tarifa = $_POST['tarifa'] ?? 0;
        
        if ($tarifa < 0) {
            ApiResponse::error('La tarifa no puede ser negativa', 'validation_error', [], 400);
            return;
        }
        
        $id_medico = $_SESSION['user_id'];
        $medico = new Medico();
        $resultado = $medico->actualizarTarifa($id_medico, $tarifa);
        
        if ($resultado) {
            ApiResponse::success(['tarifa' => $tarifa], 'Tarifa actualizada exitosamente');
        } else {
            ApiResponse::error('Error al actualizar tarifa', 'update_error', [], 500);
        }
    }
    
    /**
     * Vista para que el administrador vea todas las tarifas
     */
    public function verTarifas() {
        $this->verificarRol(['administrador']);
        
        $medico = new Medico();
        $tarifas = $medico->obtenerTodasLasTarifas();
        
        $options = [
            'title' => 'Tarifas de Médicos - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
                ['label' => 'Gestión de Facturas', 'url' => APP_URL . '/facturas'],
                ['label' => 'Tarifas de Médicos']
            ],
            'active_page' => 'ver-tarifas'
        ];
        
        $data = [
            'tarifas' => $tarifas
        ];
        
        ViewHelper::renderDashboard('facturas/ver_tarifas', $data, $options);
    }
    
    /**
     * API para obtener la tarifa de un médico
     */
    public function obtenerTarifaMedico() {
        $id_medico = $_POST['id_medico'] ?? 0;
        
        if (!$id_medico) {
            ApiResponse::error('ID de médico requerido', 'validation_error', [], 400);
            return;
        }
        
        $medico = new Medico();
        $tarifa = $medico->obtenerTarifa($id_medico);
        
        ApiResponse::success(['tarifa' => $tarifa], 'Tarifa obtenida');
    }
    
    /**
     * API para buscar factura por cita
     */
    public function buscarPorCita() {
        $id_cita = $_POST['id_cita'] ?? 0;
        
        if (!$id_cita) {
            ApiResponse::error('ID de cita requerido', 'validation_error', [], 400);
            return;
        }
        
        $factura = new Factura();
        $facturaData = $factura->buscarPorCita($id_cita);
        
        if ($facturaData) {
            ApiResponse::success(['factura' => $facturaData], 'Factura encontrada');
        } else {
            ApiResponse::success(['factura' => null], 'No existe factura para esta cita');
        }
    }
    
    /**
     * Demo del diseño de factura (sin base de datos)
     */
    public function demoDiseño() {
        renderView('facturas/demo_diseño');
    }
    
    // ==================== MÉTODOS AUXILIARES ====================
    
    private function verificarRol($rolesPermitidos) {
        if (!isset($_SESSION['rol'])) {
            redirect('/login');
        }
        
        if (!in_array($_SESSION['rol'], $rolesPermitidos)) {
            redirect('/');
        }
    }
    
    private function obtenerPermisos($rol) {
        $permisos = [
            'ver' => false,
            'editar' => false,
            'eliminar' => false,
            'marcar_pago' => false,
            'imprimir' => false,
            'guardar_pdf' => false,
            'ver_auditoria' => false
        ];
        
        switch ($rol) {
            case 'paciente':
                $permisos['ver'] = true;
                $permisos['marcar_pago'] = true;
                $permisos['imprimir'] = true;
                $permisos['guardar_pdf'] = true;
                break;
                
            case 'medico':
                $permisos['ver'] = true;
                $permisos['imprimir'] = true;
                $permisos['guardar_pdf'] = true;
                break;
                
            case 'asistente':
                $permisos['ver'] = true;
                $permisos['editar'] = true;
                $permisos['marcar_pago'] = true;
                $permisos['imprimir'] = true;
                $permisos['guardar_pdf'] = true;
                $permisos['ver_auditoria'] = true;
                break;
                
            case 'administrador':
                $permisos['ver'] = true;
                $permisos['editar'] = true;
                $permisos['eliminar'] = true;
                $permisos['marcar_pago'] = true;
                $permisos['imprimir'] = true;
                $permisos['guardar_pdf'] = true;
                $permisos['ver_auditoria'] = true;
                break;
        }
        
        return $permisos;
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    }
}
?>
