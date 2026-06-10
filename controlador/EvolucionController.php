<?php
// controlador/EvolucionController.php

class EvolucionController {
    
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
    
    /**
     * Vista principal de evoluciones clínicas
     */
    public function index() {
        AuthHelper::checkRole('medico', true);
        
        $options = [
            'title' => 'Evoluciones Clínicas - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
                ['label' => 'Evoluciones Clínicas']
            ],
            'active_page' => 'evoluciones',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="' . APP_URL . '/js/medico_evoluciones.js"></script>'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_medico' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('medico/med_evoluciones', $data, $options);
    }
    
    /**
     * Obtener citas del médico
     */
    public function getCitas() {
        $id_medico = $_SESSION['usuario'];
        $busqueda = $_POST['busqueda'] ?? '';
        
        $evolucion = new Evolucion();
        $citas = $evolucion->obtenerCitasMedico($id_medico);
        
        // Filtrar por búsqueda si es necesario
        if (!empty($busqueda)) {
            $citas = array_filter($citas, function($cita) use ($busqueda) {
                $nombreCompleto = $cita->nombre_paciente . ' ' . $cita->apellido_paciente;
                return stripos($nombreCompleto, $busqueda) !== false || 
                       stripos($cita->cedula_paciente, $busqueda) !== false;
            });
            $citas = array_values($citas);
        }
        
        $resultado = [];
        foreach ($citas as $cita) {
            $evolucionExistente = $evolucion->obtenerEvolucionPorCita($cita->id_cita);
            $resultado[] = [
                'id_cita' => $cita->id_cita,
                'fecha' => date('d/m/Y', strtotime($cita->fecha_cita)),
                'hora' => substr($cita->hora_cita, 0, 5),
                'paciente_nombre' => $cita->nombre_paciente . ' ' . $cita->apellido_paciente,
                'paciente_cedula' => $cita->cedula_paciente,
                'especialidad' => $cita->especialidad_nombre,
                'consultorio' => $cita->consultorio_nombre ?? 'No asignado',
                'tipo_consulta' => $cita->tipo_consulta == 'primera_vez' ? 'Primera Vez' : 'Control',
                'estado' => $cita->estado,
                'tiene_evolucion' => !is_null($evolucionExistente),
                'id_evolucion' => $evolucionExistente->id_evolucion ?? null
            ];
        }
        
        ApiResponse::success($resultado, 'citas_listadas', 'Citas cargadas correctamente');
    }
    
    /**
     * Obtener detalle de cita y evolución existente
     */
    public function getDetalleCita() {
        $id_medico = $_SESSION['usuario'];
        $id_cita = $_POST['id_cita'] ?? 0;
        
        if ($id_cita <= 0) {
            ApiResponse::error('ID de cita no válido', 'validation_error', [], 400);
            return;
        }
        
        $evolucion = new Evolucion();
        
        // Obtener datos de la cita
        $cita = $evolucion->obtenerDetalleCita($id_cita, $id_medico);
        
        if (!$cita) {
            ApiResponse::notFound('Cita');
            return;
        }
        
        // Obtener evolución existente
        $evolucionExistente = $evolucion->obtenerEvolucionPorCita($id_cita);
        
        // Calcular edad
        $fechaNacimiento = new DateTime($cita->fecha_nacimiento_pac);
        $hoy = new DateTime();
        $edad = $fechaNacimiento->diff($hoy);
        
        $data = [
            'id_cita' => $cita->id_cita,
            'id_paciente' => $cita->id_paciente,
            'paciente_nombre' => $cita->nombre_paciente . ' ' . $cita->apellido_paciente,
            'paciente_cedula' => $cita->cedula_paciente,
            'paciente_edad' => $edad->y,
            'paciente_sexo' => $cita->sexo_paciente ?? 'No especificado',
            'paciente_tipo_sangre' => $cita->tipo_sangre ?? 'No registrado',
            'especialidad' => $cita->especialidad_nombre,
            'fecha_cita' => date('d/m/Y', strtotime($cita->fecha_cita)),
            'hora_cita' => substr($cita->hora_cita, 0, 5),
            'consultorio' => $cita->consultorio_nombre ?? 'No asignado',
            'consultorio_direccion' => $cita->consultorio_direccion ?? '',
            'tipo_consulta' => $cita->tipo_consulta == 'primera_vez' ? 'Primera Vez' : 'Control',
            'motivo_original' => $cita->motivo ?? '',
            'evolucion' => $evolucionExistente ? [
                'id_evolucion' => $evolucionExistente->id_evolucion,
                'peso' => $evolucionExistente->peso,
                'talla' => $evolucionExistente->talla,
                'imc' => $evolucionExistente->imc,
                'temperatura' => $evolucionExistente->temperatura,
                'tension_sistolica' => $evolucionExistente->tension_sistolica,
                'tension_diastolica' => $evolucionExistente->tension_diastolica,
                'frecuencia_cardiaca' => $evolucionExistente->frecuencia_cardiaca,
                'frecuencia_respiratoria' => $evolucionExistente->frecuencia_respiratoria,
                'saturacion_oxigeno' => $evolucionExistente->saturacion_oxigeno,
                'motivo_consulta' => $evolucionExistente->motivo_consulta,
                'enfermedad_actual' => $evolucionExistente->enfermedad_actual,
                'examen_fisico' => $evolucionExistente->examen_fisico,
                'diagnostico' => $evolucionExistente->diagnostico,
                'tratamiento' => $evolucionExistente->tratamiento,
                'recomendaciones' => $evolucionExistente->recomendaciones,
                'notas_adicionales' => $evolucionExistente->notas_adicionales,
                'created_at' => $evolucionExistente->created_at
            ] : null
        ];
        
        ApiResponse::success($data, 'detalle_cargado', 'Detalle cargado correctamente');
    }
    
    /**
     * Guardar evolución clínica
     */
    public function guardar() {
        $id_medico = $_SESSION['usuario'];
        
        if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            ApiResponse::csrfError();
            return;
        }
        
        $id_cita = $_POST['id_cita'] ?? 0;
        $id_paciente = $_POST['id_paciente'] ?? 0;
        
        if ($id_cita <= 0 || $id_paciente <= 0) {
            ApiResponse::error('Datos incompletos', 'validation_error', [], 400);
            return;
        }
        
        // Validar campos requeridos
        $motivo_consulta = trim($_POST['motivo_consulta'] ?? '');
        $enfermedad_actual = trim($_POST['enfermedad_actual'] ?? '');
        $tratamiento = trim($_POST['tratamiento'] ?? '');
        
        if (empty($motivo_consulta)) {
            ApiResponse::error('El motivo de consulta es requerido', 'validation_error', [], 400);
            return;
        }
        
        if (empty($enfermedad_actual)) {
            ApiResponse::error('La enfermedad actual es requerida', 'validation_error', [], 400);
            return;
        }
        
        if (empty($tratamiento)) {
            ApiResponse::error('El tratamiento es requerido', 'validation_error', [], 400);
            return;
        }
        
        $datos = [
            'id_cita' => $id_cita,
            'id_medico' => $id_medico,
            'id_paciente' => $id_paciente,
            'peso' => $_POST['peso'] ?? null,
            'talla' => $_POST['talla'] ?? null,
            'temperatura' => $_POST['temperatura'] ?? null,
            'tension_sistolica' => $_POST['tension_sistolica'] ?? null,
            'tension_diastolica' => $_POST['tension_diastolica'] ?? null,
            'frecuencia_cardiaca' => $_POST['frecuencia_cardiaca'] ?? null,
            'frecuencia_respiratoria' => $_POST['frecuencia_respiratoria'] ?? null,
            'saturacion_oxigeno' => $_POST['saturacion_oxigeno'] ?? null,
            'motivo_consulta' => $motivo_consulta,
            'enfermedad_actual' => $enfermedad_actual,
            'examen_fisico' => $_POST['examen_fisico'] ?? '',
            'diagnostico' => $_POST['diagnostico'] ?? '',
            'tratamiento' => $tratamiento,
            'recomendaciones' => $_POST['recomendaciones'] ?? '',
            'notas_adicionales' => $_POST['notas_adicionales'] ?? '',
            'marcar_completada' => isset($_POST['marcar_completada']) && $_POST['marcar_completada'] == '1'
        ];
        
        $evolucion = new Evolucion();
        $resultado = $evolucion->guardarEvolucion($datos);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'evolucion_guardada', $resultado['message']);
        } else {
            ApiResponse::error($resultado['message'], 'save_error', [], 500);
        }
    }
    
    /**
     * Listar evoluciones anteriores del paciente
     */
    public function listarEvolucionesPaciente() {
        $id_medico = $_SESSION['usuario'];
        $id_paciente = $_POST['id_paciente'] ?? 0;
        
        if ($id_paciente <= 0) {
            ApiResponse::error('ID de paciente no válido', 'validation_error', [], 400);
            return;
        }
        
        $evolucion = new Evolucion();
        
        try {
            $db = new Conexion();
            $sql = "SELECT 
                        e.*,
                        c.fecha_cita,
                        c.tipo_consulta,
                        esp.nombre as especialidad_nombre
                    FROM evoluciones_clinicas e
                    INNER JOIN citas c ON e.id_cita = c.id_cita
                    INNER JOIN especialidades esp ON c.id_especialidad = esp.id_especialidad
                    WHERE e.id_paciente = :id_paciente AND e.id_medico = :id_medico
                    ORDER BY c.fecha_cita DESC
                    LIMIT 10";
            
            $query = $db->pdo->prepare($sql);
            $query->execute([
                ':id_paciente' => $id_paciente,
                ':id_medico' => $id_medico
            ]);
            $evoluciones = $query->fetchAll();
            
            $resultado = [];
            foreach ($evoluciones as $ev) {
                $resultado[] = [
                    'id_evolucion' => $ev->id_evolucion,
                    'fecha' => date('d/m/Y', strtotime($ev->fecha_cita)),
                    'especialidad' => $ev->especialidad_nombre,
                    'diagnostico' => $ev->diagnostico,
                    'tratamiento' => $ev->tratamiento
                ];
            }
            
            ApiResponse::success($resultado, 'evoluciones_listadas', 'Historial cargado correctamente');
        } catch(PDOException $e) {
            error_log("Error en listarEvolucionesPaciente: " . $e->getMessage());
            ApiResponse::success([], 'sin_evoluciones', 'No hay evoluciones previas');
        }
    }
}
?>
