<?php
<<<<<<< HEAD
// controlador/PacienteController.php - Versión limpia sin conflictos

class PacienteController {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'paciente') {
            if ($this->isAjax()) {
                if (class_exists('ApiResponse')) {
                    ApiResponse::unauthorized('No autorizado');
                } else {
                    jsonResponse(['error' => 'No autorizado'], 401);
                }
=======
class PacienteController {
    
    public function __construct() {
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'paciente') {
            if ($this->isAjax()) {
                ApiResponse::unauthorized('No autorizado');
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
    // ==================== BUSCAR PACIENTE ====================
=======
    // ==================== BUSCAR PACIENTE (cargar datos para el perfil) ====================
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function buscar() {
        $id_paciente = $_POST['dato'] ?? $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::buscar - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::error('No autorizado para ver este perfil', 'unauthorized', [], 403);
            } else {
                jsonResponse(['error' => 'No autorizado'], 403);
            }
=======
            ApiResponse::error('No autorizado para ver este perfil', 'unauthorized', [], 403);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $paciente = new Paciente();
        $fecha_actual = new DateTime();
        $paciente->obtener_datos($id_paciente);
        
        if(empty($paciente->objetos)) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::notFound('Paciente');
            } else {
                jsonResponse(['error' => 'No se encontró el paciente'], 404);
            }
=======
            ApiResponse::notFound('Paciente');
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
=======
                'tipo_sangre' => $objeto->tipo_sangre ?? '',
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                'adicional' => $objeto->adicional_paciente ?? '',
                'avatar' => $avatar_path
            );
        }
        
<<<<<<< HEAD
        if (class_exists('ApiResponse')) {
            ApiResponse::success($json, 'datos_cargados', 'Datos del paciente cargados correctamente');
        } else {
            jsonResponse($json);
        }
=======
        ApiResponse::success($json, 'datos_cargados', 'Datos del paciente cargados correctamente');
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    }
    
    // ==================== CAPTURAR DATOS PARA EDICIÓN ====================
    public function capturarDatos() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::capturarDatos - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::error('No autorizado', 'unauthorized', [], 403);
            } else {
                jsonResponse(['error' => 'No autorizado'], 403);
            }
=======
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $paciente = new Paciente();
        $paciente->obtener_datos($id_paciente);
        
        if(empty($paciente->objetos)) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::notFound('Paciente');
            } else {
                jsonResponse(['error' => 'No se encontró el paciente'], 404);
            }
=======
            ApiResponse::notFound('Paciente');
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $json = array();
        foreach ($paciente->objetos as $objeto) {
<<<<<<< HEAD
            $json = array(
                'telefono' => $objeto->telefono_paciente ?? '',
                'direccion' => $objeto->direccion_paciente ?? '',
                'correo' => $objeto->correo_paciente ?? '',
                'sexo' => $objeto->sexo_paciente ?? '',
                'adicional' => $objeto->adicional_paciente ?? ''
            );
        }
        
        if (class_exists('ApiResponse')) {
            ApiResponse::success($json, 'datos_capturados', 'Datos cargados para edición');
        } else {
            jsonResponse($json);
        }
=======
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
    
    /**
     * Parsea una dirección completa para obtener sus componentes
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
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    }
    
    // ==================== EDITAR PACIENTE ====================
    public function editarUsuario() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::editarUsuario - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::error('No autorizado', 'unauthorized', [], 403);
            } else {
                jsonResponse(['success' => false, 'error' => 'No autorizado'], 403);
            }
=======
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $telefono = $_POST['telefono'] ?? '';
<<<<<<< HEAD
        $direccion = $_POST['direccion'] ?? ''; 
        $correo = $_POST['correo'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        $paciente = new Paciente();
        $paciente->editar($id_paciente, $telefono, $direccion, $correo, $sexo, $adicional);
        
        if (class_exists('ApiResponse')) {
            ApiResponse::updated([], 'Datos actualizados correctamente');
        } else {
            jsonResponse(['success' => true, 'message' => 'editado']);
=======
        $direccion = $_POST['direccion'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $tipo_sangre = $_POST['tipo_sangre'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        $paciente = new Paciente();
        $resultado = $paciente->editar($id_paciente, $telefono, $direccion, $correo, $sexo, $tipo_sangre, $adicional);
        
        if ($resultado['success']) {
            ApiResponse::updated([], 'Datos actualizados correctamente');
        } else {
            ApiResponse::error($resultado['message'], 'update_error', [], 500);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
    
    // ==================== CAMBIAR FOTO DE PERFIL ====================
    public function cambiarFoto() {
        $id_paciente = $_SESSION['usuario'];
        
        if (empty($id_paciente)) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::error('Sesión no válida', 'auth_error', [], 401);
            } else {
                jsonResponse(['alert' => 'noedit', 'error' => 'Sesión no válida'], 401);
            }
=======
            ApiResponse::error('Sesión no válida', 'auth_error', [], 401);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::error('No se recibió el archivo', 'upload_error', [], 400);
            } else {
                jsonResponse(['alert' => 'noedit', 'error' => 'No se recibió el archivo'], 400);
            }
=======
            ApiResponse::error('No se recibió el archivo', 'upload_error', [], 400);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['photo']['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types)) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::error('Tipo de archivo no permitido. Use JPG, PNG o GIF', 'invalid_type', [], 400);
            } else {
                jsonResponse(['alert' => 'noedit', 'error' => 'Tipo de archivo no permitido'], 400);
            }
=======
            ApiResponse::error('Tipo de archivo no permitido. Use JPG, PNG o GIF', 'invalid_type', [], 400);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $nombre = uniqid() . '.' . $extension;
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
            
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::success([
                    'ruta' => APP_URL . '/img/' . $nombre,
                    'alert' => 'edit'
                ], 'foto_actualizada', 'Foto de perfil actualizada correctamente');
            } else {
                jsonResponse(['ruta' => APP_URL . '/img/' . $nombre, 'alert' => 'edit']);
            }
        } else {
            if (class_exists('ApiResponse')) {
                ApiResponse::error('Error al mover el archivo', 'upload_error', [], 500);
            } else {
                jsonResponse(['error' => 'Error al mover el archivo'], 500);
            }
=======
            ApiResponse::success([
                'ruta' => APP_URL . '/img/' . $nombre,
                'alert' => 'edit'
            ], 'foto_actualizada', 'Foto de perfil actualizada correctamente');
        } else {
            ApiResponse::error('Error al mover el archivo', 'upload_error', [], 500);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
    
    // ==================== CAMBIAR CONTRASEÑA ====================
    public function cambiarPassword() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::error('No autorizado', 'unauthorized', [], 403);
            } else {
                jsonResponse(['resultado' => 'noupdate'], 403);
            }
=======
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $oldpass = $_POST['oldpass'] ?? '';
        $newpass = $_POST['newpass'] ?? '';
        
        if(strlen($newpass) < 6) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::error('La contraseña debe tener al menos 6 caracteres', 'validation_error', [], 400);
            } else {
                jsonResponse(['resultado' => 'noupdate', 'error' => 'La contraseña debe tener al menos 6 caracteres'], 400);
            }
=======
            ApiResponse::error('La contraseña debe tener al menos 6 caracteres', 'validation_error', [], 400);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $loginPaciente = new LoginPaciente();
        ob_start();
        $loginPaciente->cambiar_contra($id_paciente, $oldpass, $newpass);
        $resultado = trim(ob_get_clean());
        
        if ($resultado === 'update') {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::success([], 'password_updated', 'Contraseña actualizada correctamente');
            } else {
                jsonResponse(['resultado' => 'update']);
            }
        } else {
            if (class_exists('ApiResponse')) {
                ApiResponse::error('Contraseña actual incorrecta', 'auth_error', [], 401);
            } else {
                jsonResponse(['resultado' => 'noupdate', 'error' => 'Contraseña actual incorrecta'], 401);
            }
=======
            ApiResponse::success([], 'password_updated', 'Contraseña actualizada correctamente');
        } else {
            ApiResponse::error('Contraseña actual incorrecta', 'auth_error', [], 401);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
    
    // ==================== MIS ESTADÍSTICAS ====================
    public function misEstadisticas() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
<<<<<<< HEAD
            if (class_exists('ApiResponse')) {
                ApiResponse::error('No autorizado', 'unauthorized', [], 403);
            } else {
                jsonResponse(['error' => 'No autorizado'], 403);
            }
=======
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $paciente = new Paciente();
        $total_recetas = $paciente->contarRecetas($id_paciente);
        
<<<<<<< HEAD
        if (class_exists('ApiResponse')) {
            ApiResponse::success([
                'total_recetas' => $total_recetas,
                'proximas_citas' => 0
            ], 'estadisticas', 'Estadísticas cargadas correctamente');
        } else {
            jsonResponse([
                'total_recetas' => $total_recetas,
                'proximas_citas' => 0
            ]);
        }
=======
        ApiResponse::success([
            'total_recetas' => $total_recetas,
            'proximas_citas' => 0
        ], 'estadisticas', 'Estadísticas cargadas correctamente');
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    }
    
    // ==================== VISTA: MIS RECETAS ====================
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
}
?>