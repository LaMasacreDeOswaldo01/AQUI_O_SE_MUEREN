<?php
class PacienteController {
    
    public function __construct() {
        // Verificar autenticación y rol
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'paciente') {
            if ($this->isAjax()) {
                ApiResponse::unauthorized('No autorizado. Debe iniciar sesión como paciente.');
            } else {
                redirect('login/paciente');
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
        $id_paciente = $_POST['dato'] ?? $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::buscar - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
            ApiResponse::error('No autorizado para ver este perfil', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $paciente = new Paciente();
        $fecha_actual = new DateTime();
        $paciente->obtener_datos($id_paciente);
        
        if(empty($paciente->objetos)) {
            ApiResponse::notFound('Paciente');
            return;
        }
        
        $json = array();
        foreach ($paciente->objetos as $objeto) {
            $fecha_nacimiento = $objeto->fecha_nacimiento_pac;
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $nacimiento->diff($fecha_actual);
            
            $avatar_path = (!empty($objeto->avatar_paciente) && $objeto->avatar_paciente != 'avatarDES.jpg') 
                           ? APP_URL . '/img/' . $objeto->avatar_paciente 
                           : APP_URL . '/img/avatarDES.jpg';
            
            $json = array(
                'nombre' => $objeto->nombre_paciente ?? '',
                'apellidos' => $objeto->apellido_paciente ?? '',
                'fecha_nacimiento' => $edad->y,
                'cedula' => $objeto->cedula_paciente ?? '',
                'tipo' => $objeto->nombre_tipo ?? 'Paciente',
                'telefono' => $objeto->telefono_paciente ?? '',
                'direccion' => $objeto->direccion_paciente ?? '',
                'correo' => $objeto->correo_paciente ?? '',
                'sexo' => $objeto->sexo_paciente ?? '',
                'tipo_sangre' => $objeto->tipo_sangre ?? '',
                'adicional' => $objeto->adicional_paciente ?? '',
                'avatar' => $avatar_path,
                // Estadísticas adicionales
                'total_recetas' => $this->contarTotalRecetasPaciente($id_paciente),
                'total_citas' => $this->contarTotalCitasPaciente($id_paciente),
                'total_medicos' => $this->contarTotalMedicosAtendieron($id_paciente)
            );
        }
        
        ApiResponse::success($json, 'datos_cargados', 'Datos del paciente cargados correctamente');
    }
    
    private function contarTotalRecetasPaciente($id_paciente) {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(*) as total FROM recetas WHERE id_paciente = :id_paciente AND estado = 1";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id_paciente' => $id_paciente]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarTotalRecetasPaciente: " . $e->getMessage());
            return 0;
        }
    }
    
    private function contarTotalCitasPaciente($id_paciente) {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(*) as total FROM citas WHERE id_paciente = :id_paciente";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id_paciente' => $id_paciente]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarTotalCitasPaciente: " . $e->getMessage());
            return 0;
        }
    }    

    private function contarTotalMedicosAtendieron($id_paciente) {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(DISTINCT id_medico) as total FROM recetas WHERE id_paciente = :id_paciente AND estado = 1";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id_paciente' => $id_paciente]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarTotalMedicosAtendieron: " . $e->getMessage());
            return 0;
        }
    }
    
    public function capturarDatos() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::capturarDatos - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $paciente = new Paciente();
        $paciente->obtener_datos($id_paciente);
        
        if(empty($paciente->objetos)) {
            ApiResponse::notFound('Paciente');
            return;
        }
        
        $json = array();
        foreach ($paciente->objetos as $objeto) {
            // Parsear la dirección para obtener sus componentes
            $direccion_completa = $objeto->direccion_paciente ?? '';
            $datos_ubicacion = $this->parsearDireccion($direccion_completa);
            
            $json = array(
                'telefono' => $objeto->telefono_paciente ?? '',
                'direccion' => $direccion_completa,
                'correo' => $objeto->correo_paciente ?? '',
                'sexo' => $objeto->sexo_paciente ?? '',
                'tipo_sangre' => $objeto->tipo_sangre ?? '',
                'adicional' => $objeto->adicional_paciente ?? '',
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
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::editarUsuario - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
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
        $tipo_sangre = $_POST['tipo_sangre'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        error_log("=== EDITANDO PACIENTE ===");
        error_log("ID Paciente: " . $id_paciente);
        error_log("Dirección recibida: " . $direccion);
        error_log("Tipo de sangre: " . $tipo_sangre);
        
        $paciente = new Paciente();
        $resultado = $paciente->editar($id_paciente, $telefono, $direccion, $correo, $sexo, $tipo_sangre, $adicional);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'usuario_actualizado', 'Datos actualizados correctamente');
        } else {
            ApiResponse::error($resultado['message'], 'update_error', [], 500);
        }
    }
    
    public function cambiarFoto() {
        $id_paciente = $_SESSION['usuario'];
        
        if (empty($id_paciente)) {
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
            $paciente = new Paciente();
            $avatar_anterior = $paciente->cambiar_photo($id_paciente, $nombre);
            
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
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
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
        
        $loginPaciente = new LoginPaciente();
        ob_start();
        $loginPaciente->cambiar_contra($id_paciente, $oldpass, $newpass);
        $resultado = trim(ob_get_clean());
        
        if ($resultado === 'update') {
            ApiResponse::success([], 'password_updated', 'Contraseña actualizada correctamente');
        } else {
            ApiResponse::error('Contraseña actual incorrecta', ApiResponse::CODE_AUTH_ERROR, [], 401);
        }
    }    
    // ==================== ESTADÍSTICAS ====================    
  
    public function misEstadisticas() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $paciente = new Paciente();
        $estadisticas = $paciente->obtenerEstadisticas($id_paciente);
        
        // Agregar estadísticas adicionales
        $estadisticas['proximas_citas'] = $this->contarProximasCitas($id_paciente);
        $estadisticas['total_estudios'] = $this->contarEstudiosPaciente($id_paciente);
        $estadisticas['medicos_atendieron'] = $this->contarTotalMedicosAtendieron($id_paciente);
        
        ApiResponse::success($estadisticas, 'estadisticas', 'Estadísticas cargadas correctamente');
    }
    
    private function contarProximasCitas($id_paciente) {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(*) as total FROM citas 
                    WHERE id_paciente = :id_paciente 
                    AND fecha_cita >= CURDATE() 
                    AND estado NOT IN ('cancelada', 'completada')";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id_paciente' => $id_paciente]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarProximasCitas: " . $e->getMessage());
            return 0;
        }
    }

    private function contarEstudiosPaciente($id_paciente) {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(*) as total FROM recetas 
                    WHERE id_paciente = :id_paciente 
                    AND estado = 1 
                    AND nombre_medicamento LIKE '%ESTUDIOS%'";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id_paciente' => $id_paciente]);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarEstudiosPaciente: " . $e->getMessage());
            return 0;
        }
    }    
    // ==================== ACTIVIDAD RECIENTE ====================   
    public function actividadReciente() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $actividades = $this->obtenerActividadRecientePaciente($id_paciente);
        
        ApiResponse::success($actividades, 'actividad_cargada', 'Actividad reciente cargada correctamente');
    }    

    private function obtenerActividadRecientePaciente($id_paciente, $limit = 10) {
        try {
            $db = new Conexion();
            $actividades = array();
            
            // Obtener recetas recientes
            $sql = "SELECT 
                        id_receta as id,
                        nombre_medicamento as titulo,
                        fecha_receta as fecha,
                        'receta' as tipo
                    FROM recetas 
                    WHERE id_paciente = :id_paciente AND estado = 1
                    ORDER BY fecha_receta DESC
                    LIMIT :limit";
            
            $query = $db->pdo->prepare($sql);
            $query->bindValue(':id_paciente', $id_paciente, PDO::PARAM_INT);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $actividades[] = array(
                    'id' => $row->id,
                    'titulo' => 'Receta médica emitida',
                    'descripcion' => 'Medicamento: ' . ($row->titulo ?? 'N/A'),
                    'fecha' => $this->formatearFecha($row->fecha),
                    'tipo' => 'receta'
                );
            }
            
            // Obtener citas recientes
            $sql = "SELECT 
                        id_cita as id,
                        CONCAT('Cita con ', rm.nombre_medico, ' ', rm.apellido_medico) as titulo,
                        fecha_cita as fecha,
                        'cita' as tipo
                    FROM citas c
                    LEFT JOIN registro_medico rm ON c.id_medico = rm.id_medico
                    WHERE c.id_paciente = :id_paciente
                    ORDER BY c.fecha_cita DESC
                    LIMIT :limit";
            
            $query = $db->pdo->prepare($sql);
            $query->bindValue(':id_paciente', $id_paciente, PDO::PARAM_INT);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $actividades[] = array(
                    'id' => $row->id,
                    'titulo' => $row->titulo ?? 'Cita médica',
                    'descripcion' => '',
                    'fecha' => $this->formatearFecha($row->fecha),
                    'tipo' => 'cita'
                );
            }
            
            // Ordenar por fecha
            usort($actividades, function($a, $b) {
                return strtotime($b['fecha']) - strtotime($a['fecha']);
            });
            
            // Limitar resultados
            $actividades = array_slice($actividades, 0, $limit);
            
            return $actividades;
        } catch(PDOException $e) {
            error_log("Error en obtenerActividadRecientePaciente: " . $e->getMessage());
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
    // ==================== BÚSQUEDA (para citas de terceros) ====================    
    public function buscarPacientes() {
        $dato = $_POST['dato'] ?? '';
        
        if (strlen($dato) < 2) {
            ApiResponse::success([], 'sin_resultados', 'Ingrese al menos 2 caracteres para buscar');
            return;
        }
        
        $paciente = new Paciente();
        $pacientes = $paciente->buscar($dato);
        
        $resultado = array();
        foreach ($pacientes as $p) {
            $nombre_completo = trim(($p->nombre_paciente ?? '') . ' ' . ($p->apellido_paciente ?? ''));
            if (empty($nombre_completo)) {
                $nombre_completo = $p->nombre_paciente ?? 'Usuario';
            }
            
            $resultado[] = array(
                'id_usuario' => $p->id_paciente,
                'nombre_completo' => $nombre_completo,
                'cedula' => $p->cedula_paciente ?? '',
                'fecha_nacimiento' => $p->fecha_nacimiento_pac ?? null,
                'sexo' => $p->sexo_paciente ?? '',
                'telefono' => $p->telefono_paciente ?? '',
                'correo' => $p->correo_paciente ?? ''
            );
        }
        
        ApiResponse::success($resultado, 'pacientes_encontrados', 'Pacientes encontrados');
    }    
    // ==================== VISTAS (Rutas no-API) ====================    
    public function recetas() {
        AuthHelper::checkRole('paciente', true);
        
        $options = [
            'title' => 'Mis Recetas - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/paciente'],
                ['label' => 'Mis Recetas']
            ],
            'active_page' => 'recetas',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_paciente' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('paciente/pac_recetas', $data, $options);
    }
  
    public function dashboard() {
        AuthHelper::checkRole('paciente', true);
        
        $options = [
            'title' => 'Panel del Paciente - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/paciente'],
                ['label' => 'Dashboard']
            ],
            'active_page' => 'dashboard',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_paciente' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('paciente/pac_dashboard', $data, $options);
    }
}
?>