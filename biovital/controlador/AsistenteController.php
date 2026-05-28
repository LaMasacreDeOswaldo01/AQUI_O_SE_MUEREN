<?php
// controlador/AsistenteController.php - CORREGIDO
// Maneja correctamente ApiResponse y todos los métodos necesarios

class AsistenteController {
    
    public function __construct() {
        // Verificar autenticación y rol
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'asistente') {
            if ($this->isAjax()) {
                ApiResponse::unauthorized('No autorizado');
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
    
    // ==================== BUSCAR ASISTENTE (cargar datos para el perfil) ====================
    public function buscar() {
        $id_asistente = $_POST['dato'] ?? $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AsistenteController::buscar - ID: $id_asistente, Sesión: $id_sesion");
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado para ver este perfil', 'unauthorized', [], 403);
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
                'avatar' => $avatar_path
            );
        }
        
        ApiResponse::success($json, 'datos_cargados', 'Datos del asistente cargados correctamente');
    }
    
    // ==================== CAPTURAR DATOS PARA EDICIÓN ====================
    public function capturarDatos() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AsistenteController::capturarDatos - ID: $id_asistente, Sesión: $id_sesion");
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
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
    
    /**
     * Parsea una dirección completa para obtener sus componentes
     * @param string $direccion_completa Dirección en formato "Estado, Ciudad, Municipio, Parroquia - Dirección Detallada"
     * @return array Componentes de la dirección
     */
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
    
    // ==================== EDITAR ASISTENTE ====================
    public function editarUsuario() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AsistenteController::editarUsuario - ID: $id_asistente, Sesión: $id_sesion");
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
            return;
        }
        
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        $asistente = new Asistente();
        $resultado = $asistente->editar($id_asistente, $telefono, $direccion, $correo, $sexo, $adicional);
        
        if ($resultado['success']) {
            ApiResponse::updated([], 'Datos actualizados correctamente');
        } else {
            ApiResponse::error($resultado['message'], 'update_error', [], 500);
        }
    }
    
    // ==================== CAMBIAR FOTO ====================
    public function cambiarFoto() {
        $id_asistente = $_SESSION['usuario'];
        
        if (empty($id_asistente)) {
            ApiResponse::error('Sesión no válida', 'auth_error', [], 401);
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
        
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $nombre = uniqid() . '.' . $extension;
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
    
    // ==================== CAMBIAR CONTRASEÑA ====================
    public function cambiarPassword() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
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
            ApiResponse::error('Contraseña actual incorrecta', 'auth_error', [], 401);
        }
    }
    
    // ==================== MIS ESTADÍSTICAS ====================
    public function misEstadisticas() {
        $id_asistente = $_POST['id_asistente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_asistente != $id_sesion) {
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
            return;
        }
        
        $asistente = new Asistente();
        $estadisticas = $asistente->obtenerEstadisticas($id_asistente);
        
        ApiResponse::success($estadisticas, 'estadisticas', 'Estadísticas cargadas correctamente');
    }
}
?>
