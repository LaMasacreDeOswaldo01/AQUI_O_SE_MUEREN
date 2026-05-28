<?php
// controlador/RecetaController.php

class RecetaController {
    
    private $receta;
    
    public function __construct() {
        // Verificar autenticación
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
            if ($this->isAjax()) {
                jsonResponse(['error' => 'No autorizado'], 401);
            } else {
                redirect('login');
            }
            exit();
        }
        
        // Cargar el modelo
        require_once MODEL_PATH . '/Receta.php';
        $this->receta = new Receta();
    }
    
    private function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    // ==================== VISTAS ====================
    
    /**
     * Vista principal de recetas (según el rol del usuario)
     */
  public function index() {
    $rol = $_SESSION['rol'];
    
    // Verificar permisos
    if (!in_array($rol, ['administrador', 'medico', 'asistente'])) {
        redirect('login');
        return;
    }
    
    // Determinar la vista según el rol
    switch($rol) {
        case 'administrador':
            $vista = 'administrador/adm_recetas';
            $titulo = 'Gestión de Recetas - BioVital';
            break;
        case 'medico':
            $vista = 'medico/med_recetas';
            $titulo = 'Mis Recetas - BioVital';
            break;
        case 'asistente':
            $vista = 'administrador/adm_recetas';  // Asistente usa la misma vista que administrador
            $titulo = 'Recetas - BioVital';
            break;
        default:
            redirect('login');
            return;
    }
    
    $options = [
        'title' => $titulo,
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/' . $rol],
            ['label' => 'Recetas']
        ],
        'active_page' => 'recetas',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario'
    ];
    
    ViewHelper::renderDashboard($vista, $data, $options);
}
    
    /**
     * Vista específica para administrador
     */
    public function administrador() {
        if ($_SESSION['rol'] !== 'administrador') {
            redirect('login/administrador');
            return;
        }
        renderView('administrador/adm_recetas');
    }
    
    // ==================== API - LISTAR RECETAS ====================
    
    /**
     * Listar todas las recetas (según el rol)
     */
    public function listar() {
        $rol = $_SESSION['rol'];
        $id_usuario = $_SESSION['usuario'];
        
        try {
            $receta = new Receta();
            
            if ($rol === 'medico') {
                $recetas = $receta->obtener_recetas($id_usuario);
            } else {
                $recetas = $receta->obtener_recetas();
            }
            
            $resultado = array();
            foreach ($recetas as $r) {
                $resultado[] = array(
                    'id_receta' => $r->id_receta,
                    'nombre_medicamento' => $r->nombre_medicamento,
                    'marca' => $r->marca,
                    'cantidad' => $r->cantidad,
                    'dosis' => $r->dosis,
                    'instrucciones' => $r->instrucciones,
                    'paciente' => $r->nombre_paciente ?? 'N/A',
                    'medico' => $r->nombre_medico ?? 'N/A',
                    'fecha_receta' => $r->fecha_receta
                );
            }
            
            jsonResponse($resultado);
        } catch(Exception $e) {
            error_log("Error en listar recetas: " . $e->getMessage());
            jsonResponse(['error' => 'Error al cargar recetas'], 500);
        }
    }
    
    /**
     * Listar recetas de un paciente específico
     */
    public function misRecetas() {
        $id_paciente = $_POST['id_paciente'] ?? $_SESSION['usuario'];
        
        // Verificar que sea el paciente o un médico autorizado
        if ($_SESSION['rol'] === 'paciente' && $id_paciente != $_SESSION['usuario']) {
            jsonResponse(['error' => 'No autorizado'], 403);
            return;
        }
        
        try {
            $receta = new Receta();
            $recetas = $receta->obtenerRecetasPorPaciente($id_paciente);
            
            $resultado = array();
            foreach ($recetas as $r) {
                $resultado[] = array(
                    'id_receta' => $r->id_receta,
                    'nombre_medicamento' => $r->nombre_medicamento,
                    'marca' => $r->marca,
                    'cantidad' => $r->cantidad,
                    'dosis' => $r->dosis,
                    'medico' => $r->nombre_medico ?? 'N/A',
                    'fecha_receta' => $r->fecha_receta
                );
            }
            
            jsonResponse($resultado);
        } catch(Exception $e) {
            error_log("Error en misRecetas: " . $e->getMessage());
            jsonResponse(['error' => 'Error al cargar recetas'], 500);
        }
    }
    
    /**
     * Obtener una receta específica
     */
    public function obtener() {
        $id_receta = $_POST['id_receta'] ?? 0;
        
        if (!$id_receta) {
            jsonResponse(['error' => 'ID de receta no válido'], 400);
            return;
        }
        
        try {
            $receta = new Receta();
            $datos = $receta->obtener_receta($id_receta);
            
            if (empty($datos)) {
                jsonResponse(['error' => 'Receta no encontrada'], 404);
                return;
            }
            
            $r = $datos[0];
            jsonResponse(array(
                'id_receta' => $r->id_receta,
                'nombre_medicamento' => $r->nombre_medicamento,
                'marca' => $r->marca,
                'cantidad' => $r->cantidad,
                'dosis' => $r->dosis,
                'instrucciones' => $r->instrucciones,
                'id_paciente' => $r->id_paciente,
                'fecha_receta' => $r->fecha_receta
            ));
        } catch(Exception $e) {
            error_log("Error en obtener receta: " . $e->getMessage());
            jsonResponse(['error' => 'Error al obtener receta'], 500);
        }
    }
    
    // ==================== API - CRUD ====================
    
    /**
     * Crear nueva receta
     */
    public function crear() {
        // Verificar CSRF (si usas token)
        // if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        //     jsonResponse(['success' => false, 'message' => 'Token CSRF inválido']);
        //     return;
        // }
        
        $nombre_medicamento = $_POST['nombre_medicamento'] ?? '';
        $marca = $_POST['marca'] ?? '';
        $cantidad = $_POST['cantidad'] ?? '';
        $dosis = $_POST['dosis'] ?? '';
        $instrucciones = $_POST['instrucciones'] ?? '';
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_medico = $_SESSION['usuario'];
        $fecha_receta = $_POST['fecha_receta'] ?? date('Y-m-d');
        
        // Validaciones
        if (empty($nombre_medicamento) || empty($marca) || empty($cantidad) || !$id_paciente) {
            jsonResponse(['success' => false, 'message' => 'Todos los campos requeridos deben estar llenos']);
            return;
        }
        
        if ($_SESSION['rol'] !== 'medico') {
            jsonResponse(['success' => false, 'message' => 'Solo los médicos pueden crear recetas']);
            return;
        }
        
        try {
            ob_start();
            $this->receta->crear_receta($nombre_medicamento, $marca, $cantidad, $dosis, $instrucciones, $id_paciente, $id_medico, $fecha_receta);
            $resultado = trim(ob_get_clean());
            
            if ($resultado === 'creado') {
                jsonResponse(['success' => true, 'message' => 'Receta creada exitosamente']);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al crear la receta: ' . $resultado]);
            }
        } catch(Exception $e) {
            error_log("Error en crear receta: " . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Error del servidor'], 500);
        }
    }
    
    /**
     * Editar receta existente
     */
    public function editar() {
        $id_receta = $_POST['id_receta'] ?? 0;
        $nombre_medicamento = $_POST['nombre_medicamento'] ?? '';
        $marca = $_POST['marca'] ?? '';
        $cantidad = $_POST['cantidad'] ?? '';
        $dosis = $_POST['dosis'] ?? '';
        $instrucciones = $_POST['instrucciones'] ?? '';
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $fecha_receta = $_POST['fecha_receta'] ?? date('Y-m-d');
        
        if (!$id_receta) {
            jsonResponse(['success' => false, 'message' => 'ID de receta no válido']);
            return;
        }
        
        if ($_SESSION['rol'] !== 'medico') {
            jsonResponse(['success' => false, 'message' => 'Solo los médicos pueden editar recetas']);
            return;
        }
        
        try {
            ob_start();
            $this->receta->editar_receta($id_receta, $nombre_medicamento, $marca, $cantidad, $dosis, $instrucciones, $id_paciente, $fecha_receta);
            $resultado = trim(ob_get_clean());
            
            if ($resultado === 'editado') {
                jsonResponse(['success' => true, 'message' => 'Receta actualizada exitosamente']);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al actualizar la receta']);
            }
        } catch(Exception $e) {
            error_log("Error en editar receta: " . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Error del servidor'], 500);
        }
    }
    
    /**
     * Eliminar receta (borrado lógico)
     */
    public function borrar() {
        $id_receta = $_POST['id_receta'] ?? 0;
        
        if (!$id_receta) {
            jsonResponse(['success' => false, 'message' => 'ID de receta no válido']);
            return;
        }
        
        if ($_SESSION['rol'] !== 'medico') {
            jsonResponse(['success' => false, 'message' => 'Solo los médicos pueden eliminar recetas']);
            return;
        }
        
        try {
            ob_start();
            $this->receta->borrar_receta($id_receta);
            $resultado = trim(ob_get_clean());
            
            if ($resultado === 'borrado') {
                jsonResponse(['success' => true, 'message' => 'Receta eliminada exitosamente']);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al eliminar la receta']);
            }
        } catch(Exception $e) {
            error_log("Error en borrar receta: " . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Error del servidor'], 500);
        }
    }
    
    // ==================== API - BÚSQUEDA DE PACIENTES ====================
    
    /**
     * Buscar pacientes por cédula o nombre
     */
    public function buscarPacientes() {
        $dato = $_POST['dato'] ?? '';
        
        if (strlen($dato) < 2) {
            jsonResponse([]);
            return;
        }
        
        try {
            $pacientes = $this->receta->buscar_pacientes($dato);
            
            $resultado = array();
            foreach ($pacientes as $p) {
                $nombre_completo = trim(($p->nombre_us ?? '') . ' ' . ($p->apellidos_us ?? ''));
                if (empty($nombre_completo)) {
                    $nombre_completo = $p->nombre_us ?? 'Usuario';
                }
                
                $resultado[] = array(
                    'id_usuario' => $p->id_usuario,
                    'nombre_completo' => $nombre_completo,
                    'cedula' => $p->cedula_us ?? '',
                    'fecha_nacimiento' => $p->fecha_nacimiento ?? null,
                    'sexo' => $p->sexo_us ?? ''
                );
            }
            
            jsonResponse($resultado);
        } catch(Exception $e) {
            error_log("Error en buscarPacientes: " . $e->getMessage());
            jsonResponse(['error' => 'Error al buscar pacientes'], 500);
        }
    }
    
    // ==================== DIAGNÓSTICOS Y ESTUDIOS ====================
    
    /**
     * Guardar diagnóstico
     */
    public function guardarDiagnostico() {
        $id_receta = $_POST['id_receta'] ?? 0;
        $diagnostico = $_POST['diagnostico'] ?? '';
        $tratamiento = $_POST['tratamiento'] ?? '';
        
        if (!$id_receta || empty($diagnostico)) {
            jsonResponse(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        try {
            $resultado = $this->receta->guardarDiagnostico($id_receta, $diagnostico, $tratamiento);
            jsonResponse($resultado);
        } catch(Exception $e) {
            error_log("Error en guardarDiagnostico: " . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Error del servidor'], 500);
        }
    }
    /**
 * Obtener estadísticas para el panel de administrador
 * POST /api/recetas/estadisticas
 */
public function estadisticas() {
    if ($_SESSION['rol'] !== 'administrador') {
        jsonResponse(['error' => 'No autorizado'], 403);
        return;
    }
    
    try {
        $db = new Conexion();
        $pdo = $db->pdo;
        
        // Total de recetas
        $sql_total = "SELECT COUNT(*) as total FROM recetas WHERE estado = 1";
        $query = $pdo->prepare($sql_total);
        $query->execute();
        $total_recetas = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
        
        // Total de médicos activos
        $sql_medicos = "SELECT COUNT(*) as total FROM registro_medico WHERE medico_tipo = 2";
        $query = $pdo->prepare($sql_medicos);
        $query->execute();
        $total_medicos = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
        
        // Total de pacientes
        $sql_pacientes = "SELECT COUNT(*) as total FROM registro_paciente WHERE paciente_tipo = 1";
        $query = $pdo->prepare($sql_pacientes);
        $query->execute();
        $total_pacientes = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
        
        // Recetas del mes actual
        $sql_mes = "SELECT COUNT(*) as total FROM recetas WHERE estado = 1 AND MONTH(fecha_receta) = MONTH(CURDATE()) AND YEAR(fecha_receta) = YEAR(CURDATE())";
        $query = $pdo->prepare($sql_mes);
        $query->execute();
        $recetas_mes = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
        
        jsonResponse([
            'total_recetas' => $total_recetas,
            'total_medicos' => $total_medicos,
            'total_pacientes' => $total_pacientes,
            'recetas_mes' => $recetas_mes
        ]);
    } catch(Exception $e) {
        error_log("Error en estadisticas: " . $e->getMessage());
        jsonResponse(['error' => 'Error al cargar estadísticas'], 500);
    }
}
    
    /**
     * Guardar estudio de laboratorio
     */
    public function guardarEstudio() {
        $id_receta = $_POST['id_receta'] ?? 0;
        $estudios = $_POST['estudios'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        
        if (!$id_receta || empty($estudios)) {
            jsonResponse(['success' => false, 'message' => 'Datos incompletos']);
            return;
        }
        
        try {
            $resultado = $this->receta->guardarEstudioLab($id_receta, $estudios, $observaciones);
            jsonResponse($resultado);
        } catch(Exception $e) {
            error_log("Error en guardarEstudio: " . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Error del servidor'], 500);
        }
    }
}
?>
