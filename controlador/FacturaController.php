<?php

require_once __DIR__ . '/../modelo/Factura.php';
require_once __DIR__ . '/../modelo/Cita.php';
require_once __DIR__ . '/../helpers/ApiResponse.php';

class FacturaController {
    private $factura;
    private $cita;
    
    public function __construct() {
        $this->factura = new Factura();
        $this->cita = new Cita();
    }
    
    // ==================== VISTAS ====================
    
    /**
     * Vista de detalle de factura
     */
    public function ver() {
        $id_factura = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $rol = $_SESSION['rol'] ?? '';
        $usuario_id = $_SESSION['usuario'] ?? 0;
        
        if ($id_factura <= 0) {
            header('Location: ' . APP_URL . '/panel/' . $rol);
            exit;
        }
        
        $factura = $this->factura->obtenerPorId($id_factura);
        
        if (!$factura) {
            header('Location: ' . APP_URL . '/panel/' . $rol);
            exit;
        }
        
        // Verificar permisos por rol
        if (!$this->verificarPermisoVer($factura, $rol, $usuario_id)) {
            header('Location: ' . APP_URL . '/panel/' . $rol);
            exit;
        }
        
        $options = [
            'title' => 'Factura ' . $factura->numero_factura . ' - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/' . $rol],
                ['label' => 'Facturas', 'url' => APP_URL . '/facturas'],
                ['label' => $factura->numero_factura]
            ],
            'active_page' => 'facturas',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/factura.css">'
        ];
        
        $data = [
            'factura' => $factura,
            'rol' => $rol,
            'usuario_id' => $usuario_id,
            'puede_editar' => $this->puedeEditar($rol),
            'puede_eliminar' => $this->puedeEliminar($rol),
            'puede_confirmar_pago' => $this->puedeConfirmarPago($rol),
            'puede_imprimir' => $this->puedeImprimir($rol)
        ];
        
        ViewHelper::renderDashboard('facturas/factura_detalle', $data, $options);
    }
    
    /**
     * Vista de listado de facturas (asistente/admin)
     */
    public function listar() {
        $rol = $_SESSION['rol'] ?? '';
        
        if (!in_array($rol, ['asistente', 'administrador'])) {
            header('Location: ' . APP_URL . '/panel/' . $rol);
            exit;
        }
        
        $options = [
            'title' => 'Facturas - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/' . $rol],
                ['label' => 'Facturas']
            ],
            'active_page' => 'facturas',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="' . APP_URL . '/js/facturas.js"></script>'
        ];
        
        $data = [
            'rol' => $rol,
            'puede_editar' => $this->puedeEditar($rol),
            'puede_eliminar' => $this->puedeEliminar($rol)
        ];
        
        ViewHelper::renderDashboard('facturas/facturas_listado', $data, $options);
    }
    
    /**
     * Vista de historial de facturas del paciente
     */
    public function misFacturas() {
        AuthHelper::checkRole('paciente', true);
        
        $options = [
            'title' => 'Mis Facturas - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/paciente'],
                ['label' => 'Mis Facturas']
            ],
            'active_page' => 'facturas',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="' . APP_URL . '/js/facturas.js"></script>'
        ];
        
        $data = [
            'usuario_id' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('facturas/facturas_paciente', $data, $options);
    }
    
    // ==================== API ====================
    
    /**
     * Genera una factura desde una cita
     */
    public function generar() {
        $rol = $_SESSION['rol'] ?? '';
        
        // Solo asistente y administrador pueden generar facturas
        if (!in_array($rol, ['asistente', 'administrador'])) {
            ApiResponse::error('No tiene permisos para generar facturas', 'forbidden', [], 403);
            return;
        }
        
        $id_cita = isset($_POST['id_cita']) ? (int)$_POST['id_cita'] : 0;
        
        if ($id_cita <= 0) {
            ApiResponse::error('ID de cita no válido', 'validation_error', [], 400);
            return;
        }
        
        // Verificar que no exista factura para esta cita
        if ($this->factura->existeFacturaParaCita($id_cita)) {
            ApiResponse::error('Ya existe una factura para esta cita', 'duplicate', [], 409);
            return;
        }
        
        // Obtener detalles de la cita
        $cita = $this->cita->obtenerDetalle($id_cita, 0); // 0 para no filtrar por paciente
        
        if (!$cita) {
            ApiResponse::error('Cita no encontrada', 'not_found', [], 404);
            return;
        }
        
        // Datos de la clínica
        $datos_clinica = [
            'nombre' => 'BioVital Clínica',
            'direccion' => 'Av. Principal, Edificio Médico',
            'telefono' => '+58-212-1234567',
            'email' => 'contacto@biovital.com'
        ];
        
        // Datos del beneficiario (pago móvil)
        $datos_beneficiario = [
            'banco' => 'Banco de Venezuela',
            'celular' => '0412-1234567',
            'cedula' => '12345678',
            'tipo_cuenta' => 'Ahorros'
        ];
        
        // Detalles de la factura
        $subtotal = 50.00; // Precio base de consulta
        $iva = 0.00; // IVA 0%
        $total = $subtotal + $iva;
        
        $detalles_factura = [
            'items' => [
                [
                    'concepto' => 'Consulta médica - ' . $cita->especialidad_nombre,
                    'cantidad' => 1,
                    'precio_unitario' => $subtotal,
                    'subtotal' => $subtotal
                ]
            ]
        ];
        
        $datos = [
            'id_cita' => $id_cita,
            'id_paciente' => $cita->id_paciente,
            'id_medico' => $cita->id_medico,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total,
            'datos_clinica' => $datos_clinica,
            'datos_beneficiario' => $datos_beneficiario,
            'detalles_factura' => $detalles_factura
        ];
        
        $resultado = $this->factura->crear($datos);
        
        if ($resultado['success']) {
            ApiResponse::created([
                'id_factura' => $resultado['id'],
                'numero_factura' => $resultado['numero_factura'],
                'redirect' => APP_URL . '/factura/ver?id=' . $resultado['id']
            ], 'Factura generada exitosamente');
        } else {
            ApiResponse::error($resultado['message'], 'creation_error', [], 500);
        }
    }
    
    /**
     * Lista facturas según el rol
     */
    public function listarAPI() {
        $rol = $_SESSION['rol'] ?? '';
        $usuario_id = $_SESSION['usuario'] ?? 0;
        
        $filtros = [
            'estado' => $_POST['estado'] ?? '',
            'busqueda' => $_POST['busqueda'] ?? ''
        ];
        
        $facturas = $this->factura->listarPorRol($usuario_id, $rol, $filtros);
        
        $resultado = [];
        foreach ($facturas as $f) {
            $resultado[] = [
                'id_factura' => $f->id_factura,
                'numero_factura' => $f->numero_factura,
                'paciente_nombre' => $f->paciente_nombre,
                'medico_nombre' => $f->medico_nombre,
                'fecha_emision' => date('d/m/Y H:i', strtotime($f->fecha_emision)),
                'estado' => $f->estado,
                'total' => number_format($f->total, 2),
                'referencia_pago' => $f->referencia_pago
            ];
        }
        
        ApiResponse::success($resultado);
    }
    
    /**
     * Obtiene detalles de una factura
     */
    public function obtener() {
        $id_factura = isset($_POST['id_factura']) ? (int)$_POST['id_factura'] : 0;
        $rol = $_SESSION['rol'] ?? '';
        $usuario_id = $_SESSION['usuario'] ?? 0;
        
        if ($id_factura <= 0) {
            ApiResponse::error('ID de factura no válido', 'validation_error', [], 400);
            return;
        }
        
        $factura = $this->factura->obtenerPorId($id_factura);
        
        if (!$factura) {
            ApiResponse::error('Factura no encontrada', 'not_found', [], 404);
            return;
        }
        
        // Verificar permisos
        if (!$this->verificarPermisoVer($factura, $rol, $usuario_id)) {
            ApiResponse::error('No tiene permisos para ver esta factura', 'forbidden', [], 403);
            return;
        }
        
        ApiResponse::success($factura);
    }
    
    /**
     * Confirma el pago de una factura
     */
    public function confirmarPago() {
        $rol = $_SESSION['rol'] ?? '';
        $usuario_id = $_SESSION['usuario'] ?? 0;
        
        // Solo paciente, asistente y administrador pueden confirmar pago
        if (!in_array($rol, ['paciente', 'asistente', 'administrador'])) {
            ApiResponse::error('No tiene permisos para confirmar pagos', 'forbidden', [], 403);
            return;
        }
        
        $id_factura = isset($_POST['id_factura']) ? (int)$_POST['id_factura'] : 0;
        $referencia = trim($_POST['referencia'] ?? '');
        
        if ($id_factura <= 0) {
            ApiResponse::error('ID de factura no válido', 'validation_error', [], 400);
            return;
        }
        
        if (empty($referencia)) {
            ApiResponse::error('La referencia de pago es requerida', 'validation_error', [], 400);
            return;
        }
        
        $factura = $this->factura->obtenerPorId($id_factura);
        
        if (!$factura) {
            ApiResponse::error('Factura no encontrada', 'not_found', [], 404);
            return;
        }
        
        // Paciente solo puede confirmar sus propias facturas
        if ($rol === 'paciente' && $factura->id_paciente != $usuario_id) {
            ApiResponse::error('Solo puede confirmar pagos de sus propias facturas', 'forbidden', [], 403);
            return;
        }
        
        $resultado = $this->factura->confirmarPago($id_factura, $referencia, $usuario_id);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'Pago confirmado exitosamente');
        } else {
            ApiResponse::error($resultado['message'], 'payment_error', [], 500);
        }
    }
    
    /**
     * Actualiza una factura
     */
    public function actualizar() {
        $rol = $_SESSION['rol'] ?? '';
        $usuario_id = $_SESSION['usuario'] ?? 0;
        
        // Solo asistente y administrador pueden editar
        if (!in_array($rol, ['asistente', 'administrador'])) {
            ApiResponse::error('No tiene permisos para editar facturas', 'forbidden', [], 403);
            return;
        }
        
        $id_factura = isset($_POST['id_factura']) ? (int)$_POST['id_factura'] : 0;
        
        if ($id_factura <= 0) {
            ApiResponse::error('ID de factura no válido', 'validation_error', [], 400);
            return;
        }
        
        $datos = [
            'subtotal' => (float)$_POST['subtotal'],
            'iva' => (float)$_POST['iva'],
            'total' => (float)$_POST['total'],
            'detalles_factura' => json_decode($_POST['detalles_factura'], true) ?? []
        ];
        
        $resultado = $this->factura->actualizar($id_factura, $datos, $usuario_id);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'Factura actualizada exitosamente');
        } else {
            ApiResponse::error($resultado['message'], 'update_error', [], 500);
        }
    }
    
    /**
     * Elimina una factura
     */
    public function eliminar() {
        $rol = $_SESSION['rol'] ?? '';
        
        // Solo administrador puede eliminar
        if ($rol !== 'administrador') {
            ApiResponse::error('No tiene permisos para eliminar facturas', 'forbidden', [], 403);
            return;
        }
        
        $id_factura = isset($_POST['id_factura']) ? (int)$_POST['id_factura'] : 0;
        
        if ($id_factura <= 0) {
            ApiResponse::error('ID de factura no válido', 'validation_error', [], 400);
            return;
        }
        
        $resultado = $this->factura->eliminar($id_factura);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'Factura eliminada exitosamente');
        } else {
            ApiResponse::error($resultado['message'], 'delete_error', [], 500);
        }
    }
    
    // ==================== MÉTODOS DE VERIFICACIÓN DE PERMISOS ====================
    
    private function verificarPermisoVer($factura, $rol, $usuario_id) {
        switch ($rol) {
            case 'paciente':
                return $factura->id_paciente == $usuario_id;
            case 'medico':
                return $factura->id_medico == $usuario_id;
            case 'asistente':
            case 'administrador':
                return true;
            default:
                return false;
        }
    }
    
    private function puedeEditar($rol) {
        return in_array($rol, ['asistente', 'administrador']);
    }
    
    private function puedeEliminar($rol) {
        return $rol === 'administrador';
    }
    
    private function puedeConfirmarPago($rol) {
        return in_array($rol, ['paciente', 'asistente', 'administrador']);
    }
    
    private function puedeImprimir($rol) {
        return in_array($rol, ['paciente', 'medico', 'asistente', 'administrador']);
    }
}
?>
