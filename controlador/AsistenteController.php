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