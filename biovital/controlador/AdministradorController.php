<?php
// controlador/AdministradorController.php
class AdministradorController {
    
    public function __construct() {
        // Verificar autenticación y rol
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
            if ($this->isAjax()) {
                jsonResponse(['error' => 'No autorizado'], 401);
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
    
    // Buscar administrador (cargar datos)
   public function buscar() {
    $id_administrador = $_POST['dato'] ?? $_POST['id_administrador'] ?? 0;
    $id_sesion = $_SESSION['usuario'];
    
    error_log("AdministradorController::buscar - ID: $id_administrador, Sesión: $id_sesion");
    
    if($id_administrador != $id_sesion) {
        ApiResponse::error('No autorizado para ver este perfil', 'unauthorized', [], 403);
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
    
   /**
 * Capturar datos para edición (incluyendo ubicación desglosada)
 * POST /api/administradores/capturar-datos
 */
public function capturarDatos() {
    $id_administrador = $_POST['id_administrador'] ?? 0;
    $id_sesion = $_SESSION['usuario'];
    
    error_log("AdministradorController::capturarDatos - ID: $id_administrador, Sesión: $id_sesion");
    
    if($id_administrador != $id_sesion) {
        ApiResponse::error('No autorizado', 'unauthorized', [], 403);
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
    
    // Editar administrador
    public function editarUsuario() {
        $id_administrador = $_POST['id_administrador'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("AdministradorController::editarUsuario - ID: $id_administrador, Sesión: $id_sesion");
        
        if($id_administrador != $id_sesion) {
            jsonResponse(['success' => false, 'error' => 'No autorizado']);
            return;
        }
        
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        $administrador = new Administrador();
        $administrador->editar($id_administrador, $telefono, $direccion, $correo, $sexo, $adicional);
        
        jsonResponse(['success' => true, 'message' => 'editado']);
    }
    
    // API: Cambiar foto
public function cambiarFoto() {
    $id_administrador = $_SESSION['usuario'];
    
    if (empty($id_administrador)) {
        jsonResponse(['alert' => 'noedit', 'error' => 'Sesión no válida']);
        return;
    }
    
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        jsonResponse(['alert' => 'noedit', 'error' => 'No se recibió el archivo']);
        return;
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $_FILES['photo']['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        jsonResponse(['alert' => 'noedit', 'error' => 'Tipo de archivo no permitido. Use JPG, PNG o GIF']);
        return;
    }
    
    $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $nombre = uniqid() . '.' . $extension;
    $ruta_destino = dirname(__DIR__) . '/img/' . $nombre;
    
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $ruta_destino)) {
        $administrador = new Administrador();
        $avatar_anterior = $administrador->cambiar_photo($id_administrador, $nombre);
        
        // Eliminar avatar anterior si no es el default
        if ($avatar_anterior && $avatar_anterior !== 'avatarDES.jpg') {
            $ruta_anterior = dirname(__DIR__) . '/img/' . $avatar_anterior;
            if (file_exists($ruta_anterior)) {
                @unlink($ruta_anterior);
            }
        }
        
        // Devolver la ruta completa con APP_URL
        jsonResponse([
            'alert' => 'edit', 
            'ruta' => APP_URL . '/img/' . $nombre
        ]);
    } else {
        jsonResponse(['alert' => 'noedit', 'error' => 'Error al mover el archivo']);
    }
    $_SESSION['avatar'] = APP_URL . '/img/' . $nombre;
}

    
    // Cambiar contraseña
    public function cambiarPassword() {
        $id_administrador = $_POST['id_administrador'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_administrador != $id_sesion) {
            jsonResponse(['resultado' => 'noupdate']);
            return;
        }
        
        $oldpass = $_POST['oldpass'] ?? '';
        $newpass = $_POST['newpass'] ?? '';
        
        if(strlen($newpass) < 6) {
            jsonResponse(['resultado' => 'noupdate', 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }
        
        $loginAdministrador = new LoginAdministrador();
        ob_start();
        $loginAdministrador->cambiar_contra($id_administrador, $oldpass, $newpass);
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    // Listar usuarios para el panel de administración
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
public function apiListarUsuarios() {
    AuthHelper::checkRole('administrador', true);
    
    $busqueda = $_POST['busqueda'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $estado = $_POST['estado'] ?? '';
    
    $administrador = new Administrador();
    $usuarios = $administrador->listarUsuarios($busqueda, $rol, $estado);
    
    ApiResponse::success($usuarios);
}
public function apiEditarUsuario() {
    AuthHelper::checkRole('administrador', true);
    
    $id_usuario = $_POST['id_usuario'] ?? 0;
    $rol = $_POST['rol'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $estado = $_POST['estado'] ?? 'activo';
    
    // Validar CSRF
    if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        ApiResponse::csrfError();
        return;
    }
    
    $administrador = new Administrador();
    $resultado = $administrador->editarUsuario($id_usuario, $rol, $correo, $telefono, $estado);
    
    if ($resultado['success']) {
        ApiResponse::success([], 'usuario_actualizado', 'Usuario actualizado correctamente');
    } else {
        ApiResponse::error($resultado['message'], 'error_actualizacion');
    }
}

/**
 * API: Eliminar usuario
 * POST /api/administradores/eliminar-usuario
 */
public function apiEliminarUsuario() {
    AuthHelper::checkRole('administrador', true);
    
    $id_usuario = $_POST['id_usuario'] ?? 0;
    $rol = $_POST['rol'] ?? '';
    
    // Validar CSRF
    if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
        ApiResponse::csrfError();
        return;
    }
    

  // Validar datos
    if ($id_usuario <= 0) {
        ApiResponse::error('ID de usuario no válido', 'invalid_id');
        return;
    }
    
    if (empty($rol)) {
        ApiResponse::error('Rol de usuario no especificado', 'invalid_role');
        return;
    }
    
    $administrador = new Administrador();
    
    // Determinar las tablas según el rol
    $tablas = $this->getTablasPorRol($rol);
    
    if (!$tablas) {
        ApiResponse::error('Rol de usuario no válido', 'invalid_role');
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
        ApiResponse::error($resultado['message'], 'error_eliminacion');
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
}


?>
