<?php
class FacturaController {
    
    // ==================== VISTAS ====================
    
    /**
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
        }
    }
    
    /**
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
        }
    }
    
    /**
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
    }
}
?>
