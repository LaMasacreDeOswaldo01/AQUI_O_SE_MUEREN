<?php
// controlador/PacienteController.php - CORREGIDO
// Basado en el funcionamiento de AdministradorController.php

class PacienteController {
    
    public function __construct() {
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'paciente') {
            if ($this->isAjax()) {
                ApiResponse::unauthorized('No autorizado');
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
    
    // ==================== BUSCAR PACIENTE (cargar datos para el perfil) ====================
    public function buscar() {
        $id_paciente = $_POST['dato'] ?? $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::buscar - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
            ApiResponse::error('No autorizado para ver este perfil', 'unauthorized', [], 403);
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
                'adicional' => $objeto->adicional_paciente ?? '',
                'avatar' => $avatar_path
            );
        }
        
        ApiResponse::success($json, 'datos_cargados', 'Datos del paciente cargados correctamente');
    }
    
    // ==================== CAPTURAR DATOS PARA EDICIÓN ====================
    public function capturarDatos() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::capturarDatos - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
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
            $json = array(
                'telefono' => $objeto->telefono_paciente ?? '',
                'direccion' => $objeto->direccion_paciente ?? '',
                'correo' => $objeto->correo_paciente ?? '',
                'sexo' => $objeto->sexo_paciente ?? '',
                'adicional' => $objeto->adicional_paciente ?? ''
            );
        }
        
        ApiResponse::success($json, 'datos_capturados', 'Datos cargados para edición');
    }
    
    // ==================== EDITAR PACIENTE ====================
   public function editarUsuario() {
    $id_paciente = $_POST['id_paciente'] ?? 0;
    $id_sesion = $_SESSION['usuario'];
        
        error_log("PacienteController::editarUsuario - ID: $id_paciente, Sesión: $id_sesion");
        
        if($id_paciente != $id_sesion) {
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
            return;
        }
        
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? ''; 
        $correo = $_POST['correo'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        $paciente = new Paciente();
        $paciente->editar($id_paciente, $telefono, $direccion, $correo, $sexo, $adicional);
        
        ApiResponse::updated([], 'Datos actualizados correctamente');
    }
    
    // ==================== CAMBIAR FOTO DE PERFIL ====================
    public function cambiarFoto() {
        $id_paciente = $_SESSION['usuario'];
        
        if (empty($id_paciente)) {
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
    
    // ==================== CAMBIAR CONTRASEÑA ====================
    public function cambiarPassword() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
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
            ApiResponse::error('Contraseña actual incorrecta', 'auth_error', [], 401);
        }
    }
    
    // ==================== MIS ESTADÍSTICAS ====================
    public function misEstadisticas() {
        $id_paciente = $_POST['id_paciente'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_paciente != $id_sesion) {
            ApiResponse::error('No autorizado', 'unauthorized', [], 403);
            return;
        }
        
        $paciente = new Paciente();
        $total_recetas = $paciente->contarRecetas($id_paciente);
        
        ApiResponse::success([
            'total_recetas' => $total_recetas,
            'proximas_citas' => 0
        ], 'estadisticas', 'Estadísticas cargadas correctamente');
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