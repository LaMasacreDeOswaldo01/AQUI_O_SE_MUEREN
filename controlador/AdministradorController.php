<?php
class AdministradorController {    
    public function __construct() {
       
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
            if ($this->isAjax()) {
                ApiResponse::unauthorized('No autorizado. Debe iniciar sesión como administrador.');
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
    // ==================== PERFIL Y DATOS PERSONALES ====================
    public function buscar() {
        $id_administrador = $_POST['dato'] ?? $_POST['id_administrador'] ?? 0;
        $id_sesion = $_SESSION['usuario'];        
        error_log("AdministradorController::buscar - ID: $id_administrador, Sesión: $id_sesion");        
        if($id_administrador != $id_sesion) {
            ApiResponse::error('No autorizado para ver este perfil', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $administrador = new Administrador();
        $fecha_actual = new DateTime();
        $administrador->obtener_datos($id_administrador);        
        if(empty($administrador->objetos)) {
            ApiResponse::notFound('Administrador');
            return;
        }
        
        $json = array();
        foreach ($administrador->objetos as $objeto) {
            $fecha_nacimiento = $objeto->fecha_nacimiento_administrador;
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $nacimiento->diff($fecha_actual);
            
            $avatar_path = (!empty($objeto->avatar_administrador) && $objeto->avatar_administrador != 'avatarDES.jpg') 
                           ? APP_URL . '/img/' . $objeto->avatar_administrador 
                           : APP_URL . '/img/avatarDES.jpg';
            
            $json = array(
                'success' => true,
                'nombre' => $objeto->nombre_administrador ?? '',
                'apellidos' => $objeto->apellido_administrador ?? '',
                'fecha_nacimiento' => $edad->y,
                'cedula' => $objeto->cedula_administrador ?? '',
                'tipo' => $objeto->nombre_tipo ?? 'Administrador',
                'telefono' => $objeto->telefono_administrador ?? '',
                'direccion' => $objeto->direccion_administrador ?? '',
                'correo' => $objeto->correo_administrador ?? '',
                'sexo' => $objeto->sexo_administrador ?? '',
                'adicional' => $objeto->adicional_administrador ?? '',
                'avatar' => $avatar_path
            );
        }
        
        ApiResponse::success($json, 'datos_cargados', 'Datos del administrador cargados correctamente');
    }   
    public function capturarDatos() {
        $id_administrador = $_POST['id_administrador'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AdministradorController::capturarDatos - ID: $id_administrador, Sesión: $id_sesion");
        
        if($id_administrador != $id_sesion) {
            ApiResponse::error('No autorizado', ApiResponse::CODE_FORBIDDEN, [], 403);
            return;
        }
        
        $administrador = new Administrador();
        $administrador->obtener_datos($id_administrador);
        
        if(empty($administrador->objetos)) {
            ApiResponse::notFound('Administrador');
            return;
        }
        
        $json = array();
        foreach ($administrador->objetos as $objeto) {
            // Parsear la dirección para obtener sus componentes
            $direccion_completa = $objeto->direccion_administrador ?? '';
            $datos_ubicacion = $this->parsearDireccion($direccion_completa);
            
            $json = array(
                'telefono' => $objeto->telefono_administrador ?? '',
                'direccion' => $direccion_completa,
                'correo' => $objeto->correo_administrador ?? '',
                'sexo' => $objeto->sexo_administrador ?? '',
                'adicional' => $objeto->adicional_administrador ?? '',
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
        $id_administrador = $_POST['id_administrador'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AdministradorController::editarUsuario - ID: $id_administrador, Sesión: $id_sesion");
        
        if($id_administrador != $id_sesion) {
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
        
        error_log("=== EDITANDO ADMINISTRADOR ===");
        error_log("ID Administrador: " . $id_administrador);
        error_log("Dirección recibida: " . $direccion);
        
        $administrador = new Administrador();
        $resultado = $administrador->editar($id_administrador, $telefono, $direccion, $correo, $sexo, $adicional);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'usuario_actualizado', 'Datos actualizados correctamente');
        } else {
            ApiResponse::error($resultado['message'], 'update_error', [], 500);
        }
    }
    
    public function cambiarFoto() {
        $id_administrador = $_SESSION['usuario'];
        
        if (empty($id_administrador)) {
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
            $administrador = new Administrador();
            $avatar_anterior = $administrador->cambiar_photo($id_administrador, $nombre);
            
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
        $id_administrador = $_POST['id_administrador'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_administrador != $id_sesion) {
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
        
        $loginAdministrador = new LoginAdministrador();
        ob_start();
        $loginAdministrador->cambiar_contra($id_administrador, $oldpass, $newpass);
        $resultado = trim(ob_get_clean());
        
        if ($resultado === 'update') {
            ApiResponse::success([], 'password_updated', 'Contraseña actualizada correctamente');
        } else {
            ApiResponse::error('Contraseña actual incorrecta', ApiResponse::CODE_AUTH_ERROR, [], 401);
        }
    }    
    // ==================== ESTADÍSTICAS Y DASHBOARD ==================== 
    public function estadisticasGenerales() {
        $administrador = new Administrador();
        $stats = $administrador->obtenerEstadisticasGenerales();
        
        // Agregar estadísticas adicionales
        $stats['total_usuarios'] = ($stats['total_pacientes'] ?? 0) + 
                                   ($stats['total_medicos'] ?? 0) + 
                                   ($stats['total_asistentes'] ?? 0) + 
                                   ($stats['total_administradores'] ?? 0);
        $stats['usuarios_nuevos_mes'] = $this->contarUsuariosNuevosMes();
        $stats['recetas_mes'] = $this->contarRecetasMes();
        $stats['medicos_activos'] = $stats['total_medicos'] ?? 0;
        $stats['pacientes_atendidos_mes'] = $this->contarPacientesAtendidosMes();
        $stats['consultorios_activos'] = $stats['total_consultorios'] ?? 0;
        $stats['especialidades_activas'] = $stats['total_especialidades'] ?? 0;
        $stats['citas_pendientes'] = $this->contarCitasPendientes();
        $stats['total_citas'] = $this->contarTotalCitas();
        $stats['usuarios_activos_hoy'] = $this->contarUsuariosActivosHoy();
        
        ApiResponse::success($stats, 'estadisticas', 'Estadísticas generales cargadas correctamente');
    }
    
    public function actividadReciente() {
        $actividades = $this->obtenerActividadRecienteSistema();
        ApiResponse::success($actividades, 'actividad_cargada', 'Actividad reciente cargada correctamente');
    }

    private function contarUsuariosNuevosMes() {
        try {
            $db = new Conexion();
            $sql = "SELECT 
                        (SELECT COUNT(*) FROM registro_paciente WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())) +
                        (SELECT COUNT(*) FROM registro_medico WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())) +
                        (SELECT COUNT(*) FROM registro_asistente WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())) +
                        (SELECT COUNT(*) FROM registro_administrador WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()))
                        as total";
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarUsuariosNuevosMes: " . $e->getMessage());
            return 0;
        }
    }    
   
    private function contarRecetasMes() {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(*) as total FROM recetas 
                    WHERE estado = 1 
                    AND MONTH(fecha_receta) = MONTH(CURDATE()) 
                    AND YEAR(fecha_receta) = YEAR(CURDATE())";
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarRecetasMes: " . $e->getMessage());
            return 0;
        }
    }
    
    private function contarPacientesAtendidosMes() {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(DISTINCT id_paciente) as total FROM recetas 
                    WHERE estado = 1 
                    AND MONTH(fecha_receta) = MONTH(CURDATE()) 
                    AND YEAR(fecha_receta) = YEAR(CURDATE())";
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarPacientesAtendidosMes: " . $e->getMessage());
            return 0;
        }
    }
      
    private function contarCitasPendientes() {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(*) as total FROM citas WHERE estado = 'pendiente' OR estado = 'confirmada'";
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarCitasPendientes: " . $e->getMessage());
            return 0;
        }
    }    
    
    private function contarTotalCitas() {
        try {
            $db = new Conexion();
            $sql = "SELECT COUNT(*) as total FROM citas";
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarTotalCitas: " . $e->getMessage());
            return 0;
        }
    } 
   
    private function contarUsuariosActivosHoy() {
        try {
            $db = new Conexion();
            $sql = "SELECT 
                        (SELECT COUNT(DISTINCT id_paciente) FROM login_paciente WHERE DATE(ultimo_acceso) = CURDATE()) +
                        (SELECT COUNT(DISTINCT id_medico) FROM login_medico WHERE DATE(ultimo_acceso) = CURDATE()) +
                        (SELECT COUNT(DISTINCT id_asistente) FROM login_asistente WHERE DATE(ultimo_acceso) = CURDATE()) +
                        (SELECT COUNT(DISTINCT id_administrador) FROM login_administrador WHERE DATE(ultimo_acceso) = CURDATE())
                        as total";
            $query = $db->pdo->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarUsuariosActivosHoy: " . $e->getMessage());
            return 0;
        }
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
                        CONCAT(rm.nombre_medico, ' ', rm.apellido_medico) as medico,
                        'receta' as tipo
                    FROM recetas r
                    LEFT JOIN registro_paciente rp ON r.id_paciente = rp.id_paciente
                    LEFT JOIN registro_medico rm ON r.id_medico = rm.id_medico
                    WHERE r.estado = 1
                    ORDER BY r.fecha_receta DESC
                    LIMIT :limit";
            
            $query = $db->pdo->prepare($sql);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $actividades[] = array(
                    'id' => $row->id,
                    'titulo' => 'Nueva receta emitida',
                    'descripcion' => 'Medicamento: ' . ($row->titulo ?? 'N/A') . ' - Paciente: ' . ($row->paciente ?? 'N/A') . ' - Médico: ' . ($row->medico ?? 'N/A'),
                    'fecha' => $this->formatearFecha($row->fecha),
                    'tipo' => 'receta',
                    'usuario' => $row->medico ?? 'Médico'
                );
            }
            
            // Obtener nuevos registros de pacientes
            $sql = "SELECT 
                        id_paciente as id,
                        CONCAT(nombre_paciente, ' ', apellido_paciente) as nombre,
                        created_at as fecha,
                        'paciente' as tipo
                    FROM registro_paciente
                    ORDER BY created_at DESC
                    LIMIT :limit";
            
            $query = $db->pdo->prepare($sql);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $actividades[] = array(
                    'id' => $row->id,
                    'titulo' => 'Nuevo paciente registrado',
                    'descripcion' => 'Paciente: ' . ($row->nombre ?? 'N/A'),
                    'fecha' => $this->formatearFecha($row->fecha),
                    'tipo' => 'usuario',
                    'usuario' => $row->nombre ?? 'Paciente'
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
            error_log("Error en obtenerActividadRecienteSistema: " . $e->getMessage());
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
    // ==================== GESTIÓN DE USUARIOS ====================   
    public function apiListarUsuarios() {
        $busqueda = $_POST['busqueda'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $estado = $_POST['estado'] ?? '';
        
        $administrador = new Administrador();
        $usuarios = $administrador->listarUsuarios($busqueda, $rol, $estado);
        
        ApiResponse::success($usuarios, 'usuarios_listados', 'Usuarios listados correctamente');
    }    
  
    public function apiEditarUsuario() {
        // Verificar token CSRF
        if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            ApiResponse::csrfError();
            return;
        }
        
        $id_usuario = $_POST['id_usuario'] ?? 0;
        $rol = $_POST['rol'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $estado = $_POST['estado'] ?? 'activo';
        
        if ($id_usuario <= 0 || empty($rol)) {
            ApiResponse::error('Datos de usuario inválidos', 'invalid_data', [], 400);
            return;
        }
        
        $administrador = new Administrador();
        $resultado = $administrador->editarUsuario($id_usuario, $rol, $correo, $telefono, $estado);
        
        if ($resultado['success']) {
            ApiResponse::success([], 'usuario_actualizado', 'Usuario actualizado correctamente');
        } else {
            ApiResponse::error($resultado['message'], 'error_actualizacion', [], 500);
        }
    }    
  
    public function apiEliminarUsuario() {
        // Verificar token CSRF
        if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            ApiResponse::csrfError();
            return;
        }
        
        $id_usuario = $_POST['id_usuario'] ?? 0;
        $rol = $_POST['rol'] ?? '';
        
        if ($id_usuario <= 0) {
            ApiResponse::error('ID de usuario no válido', 'invalid_id', [], 400);
            return;
        }
        
        if (empty($rol)) {
            ApiResponse::error('Rol de usuario no especificado', 'invalid_role', [], 400);
            return;
        }
        
        $administrador = new Administrador();
        
        // Determinar las tablas según el rol
        $tablas = $this->getTablasPorRol($rol);
        
        if (!$tablas) {
            ApiResponse::error('Rol de usuario no válido', 'invalid_role', [], 400);
            return;
        }
        
        $resultado = $administrador->eliminarUsuario(
            $tablas['tabla_registro'],
            $tablas['tabla_login'],
            $tablas['id_field'],
            $id_usuario
        );
        
        if ($resultado['success']) {
            ApiResponse::success([], 'usuario_eliminado', 'Usuario eliminado correctamente');
        } else {
            ApiResponse::error($resultado['message'], 'error_eliminacion', [], 500);
        }
    }    
 
    private function getTablasPorRol($rol) {
        $tablas = [
            'paciente' => [
                'tabla_registro' => 'registro_paciente',
                'tabla_login' => 'login_paciente',
                'id_field' => 'id_paciente'
            ],
            'medico' => [
                'tabla_registro' => 'registro_medico',
                'tabla_login' => 'login_medico',
                'id_field' => 'id_medico'
            ],
            'asistente' => [
                'tabla_registro' => 'registro_asistente',
                'tabla_login' => 'login_asistente',
                'id_field' => 'id_asistente'
            ],
            'administrador' => [
                'tabla_registro' => 'registro_administrador',
                'tabla_login' => 'login_administrador',
                'id_field' => 'id_administrador'
            ]
        ];
        
        return $tablas[$rol] ?? false;
    }    
    // ==================== VISTAS (Rutas no-API) ====================   
    public function listarUsuarios() {
        AuthHelper::checkRole('administrador', true);
        
        $options = [
            'title' => 'Gestión de Usuarios - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/administrador'],
                ['label' => 'Usuarios']
            ],
            'active_page' => 'usuarios',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Administrador'
        ];
        
        ViewHelper::renderDashboard('administrador/adm_usuarios', $data, $options);
    }
}
?>