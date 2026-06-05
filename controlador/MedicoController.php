<?php
class MedicoController {
    
    public function __construct() {
        // Verificar que el usuario esté autenticado y sea médico
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'medico') {
            if ($this->isAjax()) {
                jsonResponse(['error' => 'No autorizado'], 401);
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
    
<<<<<<< HEAD
    // API: Buscar médico (para mostrar datos)
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
   public function buscar() {
    $id_medico = $_POST['dato'] ?? $_POST['id_medico'] ?? 0;
    $id_sesion = $_SESSION['usuario'];
    
    error_log("MedicoController::buscar - ID: $id_medico, Sesión: $id_sesion");
    
    if($id_medico != $id_sesion) {
        ApiResponse::error('No autorizado para ver este perfil', 'unauthorized', [], 403);
        return;
<<<<<<< HEAD
    }
    
    $medico = new Medico();
    $fecha_actual = new DateTime();
    $medico->obtener_datos($id_medico);
    
    if(empty($medico->objetos)) {
        ApiResponse::notFound('Médico');
        return;
    }
    
    $json = array();
    foreach ($medico->objetos as $objeto) {
        $fecha_nacimiento = $objeto->fecha_nacimiento_medico;
        $nacimiento = new DateTime($fecha_nacimiento);
        $edad = $nacimiento->diff($fecha_actual);
        
        $avatar_path = (!empty($objeto->avatar_medico) && $objeto->avatar_medico != 'avatarDES.jpg') 
                       ? APP_URL . '/img/' . $objeto->avatar_medico 
                       : APP_URL . '/img/avatarDES.jpg';
        
        $json = array(
            'nombre' => $objeto->nombre_medico ?? '',
            'apellidos' => $objeto->apellido_medico ?? '',
            'fecha_nacimiento' => $edad->y,
            'cedula' => $objeto->cedula_medico ?? '',
            'tipo' => $objeto->nombre_tipo ?? 'Médico',
            'telefono' => $objeto->telefono_medico ?? '',
            'direccion' => $objeto->direccion_medico ?? '',
            'correo' => $objeto->correo_medico ?? '',
            'sexo' => $objeto->sexo_medico ?? '',
            'adicional' => $objeto->adicional_medico ?? '',
            'avatar' => $avatar_path
        );
    }
    
    // Devolver en formato ApiResponse
    ApiResponse::success($json, 'datos_cargados', 'Datos del médico cargados correctamente');
}
    
=======
    }
    
    $medico = new Medico();
    $fecha_actual = new DateTime();
    $medico->obtener_datos($id_medico);
    
    if(empty($medico->objetos)) {
        ApiResponse::notFound('Médico');
        return;
    }
    
    $json = array();
    foreach ($medico->objetos as $objeto) {
        $fecha_nacimiento = $objeto->fecha_nacimiento_medico;
        $nacimiento = new DateTime($fecha_nacimiento);
        $edad = $nacimiento->diff($fecha_actual);
        
        $avatar_path = (!empty($objeto->avatar_medico) && $objeto->avatar_medico != 'avatarDES.jpg') 
                       ? APP_URL . '/img/' . $objeto->avatar_medico 
                       : APP_URL . '/img/avatarDES.jpg';
        
        $json = array(
            'nombre' => $objeto->nombre_medico ?? '',
            'apellidos' => $objeto->apellido_medico ?? '',
            'fecha_nacimiento' => $edad->y,
            'cedula' => $objeto->cedula_medico ?? '',
            'tipo' => $objeto->nombre_tipo ?? 'Médico',
            'telefono' => $objeto->telefono_medico ?? '',
            'direccion' => $objeto->direccion_medico ?? '',
            'correo' => $objeto->correo_medico ?? '',
            'sexo' => $objeto->sexo_medico ?? '',
            'adicional' => $objeto->adicional_medico ?? '',
            'avatar' => $avatar_path
        );
    }
    
    // Devolver en formato ApiResponse
    ApiResponse::success($json, 'datos_cargados', 'Datos del médico cargados correctamente');
}
    
=======
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function buscar() {
        $id_medico = $_POST['dato'] ?? $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
<<<<<<< HEAD
        // Si viene como 'dato' desde medico.js
        if ($id_medico == 0 && isset($_POST['dato'])) {
            $id_medico = $_POST['dato'];
        }
        
        if($id_medico != $id_sesion) {
            jsonResponse(['error' => 'No autorizado para ver este perfil']);
=======
        error_log("MedicoController::buscar - ID: $id_medico, Sesión: $id_sesion");
        
        if($id_medico != $id_sesion) {
            ApiResponse::error('No autorizado para ver este perfil', 'unauthorized', [], 403);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $medico = new Medico();
        $fecha_actual = new DateTime();
        $medico->obtener_datos($id_medico);
        
        if(empty($medico->objetos)) {
<<<<<<< HEAD
            jsonResponse(['error' => 'No se encontró el médico']);
=======
            ApiResponse::notFound('Médico');
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $json = array();
        foreach ($medico->objetos as $objeto) {
            $fecha_nacimiento = $objeto->fecha_nacimiento_medico;
            $nacimiento = new DateTime($fecha_nacimiento);
            $edad = $nacimiento->diff($fecha_actual);
            
            $avatar_path = (!empty($objeto->avatar_medico) && $objeto->avatar_medico != 'avatarDES.jpg') 
                           ? APP_URL . '/img/' . $objeto->avatar_medico 
                           : APP_URL . '/img/avatarDES.jpg';
            
            $json = array(
<<<<<<< HEAD
                'nombre' => $objeto->nombre_medico,
                'apellidos' => $objeto->apellido_medico,
                'fecha_nacimiento' => $edad->y,
                'cedula' => $objeto->cedula_medico,
                'tipo' => $objeto->nombre_tipo,
                'telefono' => $objeto->telefono_medico,
                'direccion' => $objeto->direccion_medico,
                'correo' => $objeto->correo_medico,
                'sexo' => $objeto->sexo_medico,
                'adicional' => $objeto->adicional_medico,
                'avatar' => $avatar_path
            );
        }
        jsonResponse($json);
    }
    
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    // API: Capturar datos para editar (CORREGIDO)
=======
                'nombre' => $objeto->nombre_medico ?? '',
                'apellidos' => $objeto->apellido_medico ?? '',
                'fecha_nacimiento' => $edad->y,
                'cedula' => $objeto->cedula_medico ?? '',
                'mpps_registro' => $objeto->mpps_registro ?? '',
                'tipo' => $objeto->nombre_tipo ?? 'Médico',
                'telefono' => $objeto->telefono_medico ?? '',
                'direccion' => $objeto->direccion_medico ?? '',
                'correo' => $objeto->correo_medico ?? '',
                'sexo' => $objeto->sexo_medico ?? '',
                'adicional' => $objeto->adicional_medico ?? '',
                'avatar' => $avatar_path
            );
        }
        
        ApiResponse::success($json, 'datos_cargados', 'Datos del médico cargados correctamente');
    }
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function capturarDatos() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("capturarDatos - ID recibido: " . $id_medico);
        error_log("capturarDatos - ID sesión: " . $id_sesion);
        
        if($id_medico != $id_sesion) {
            jsonResponse(['error' => 'No autorizado']);
            return;
        }
        
        $medico = new Medico();
        $medico->obtener_datos($id_medico);
        
        if(empty($medico->objetos)) {
            jsonResponse(['error' => 'No se encontró el médico']);
            return;
        }
        
        $json = array();
        foreach ($medico->objetos as $objeto) {
<<<<<<< HEAD
            $json = array(
                'telefono' => $objeto->telefono_medico ?? '',
                'direccion' => $objeto->direccion_medico ?? '',
                'correo' => $objeto->correo_medico ?? '',
                'sexo' => $objeto->sexo_medico ?? '',
                'adicional' => $objeto->adicional_medico ?? ''
=======
            // Parsear la dirección para obtener sus componentes
            $direccion_completa = $objeto->direccion_medico ?? '';
            $datos_ubicacion = $this->parsearDireccion($direccion_completa);
            
            $json = array(
                'telefono' => $objeto->telefono_medico ?? '',
                'direccion' => $direccion_completa,
                'correo' => $objeto->correo_medico ?? '',
                'sexo' => $objeto->sexo_medico ?? '',
                'mpps_registro' => $objeto->mpps_registro ?? '',
                'adicional' => $objeto->adicional_medico ?? '',
                // Datos de ubicación desglosados
                'estado' => $datos_ubicacion['estado'],
                'ciudad' => $datos_ubicacion['ciudad'],
                'municipio' => $datos_ubicacion['municipio'],
                'parroquia' => $datos_ubicacion['parroquia'],
                'direccion_detallada' => $datos_ubicacion['direccion_detallada']
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            );
        }
        jsonResponse($json);
    }
    
<<<<<<< HEAD
    // API: Editar médico
   // En controlador/MedicoController.php
public function editarUsuario() {
    $id_medico = $_POST['id_medico'] ?? 0;
    $id_sesion = $_SESSION['usuario'];
    
    if($id_medico != $id_sesion) {
        jsonResponse(['success' => false, 'error' => 'No autorizado']);
        return;
    }
    
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';  // ← Este es el campo que viene del formulario
    $correo = $_POST['correo'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $adicional = $_POST['adicional'] ?? '';
    
    // Agrega logs para depuración
    error_log("=== EDITANDO MÉDICO ===");
    error_log("ID Médico: " . $id_medico);
    error_log("Dirección recibida: " . $direccion);
    error_log("Teléfono: " . $telefono);
    error_log("Correo: " . $correo);
    
    $medico = new Medico();
    $medico->editar($id_medico, $telefono, $direccion, $correo, $sexo, $adicional);
    
    jsonResponse(['success' => true, 'message' => 'editado']);
}
    
    // API: Cambiar foto
=======
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
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_medico != $id_sesion) {
            jsonResponse(['success' => false, 'error' => 'No autorizado']);
            return;
        }
        
        $telefono = $_POST['telefono'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $sexo = $_POST['sexo'] ?? '';
        $mpps_registro = $_POST['mpps_registro'] ?? '';
        $adicional = $_POST['adicional'] ?? '';
        
        // Agrega logs para depuración
        error_log("=== EDITANDO MÉDICO ===");
        error_log("ID Médico: " . $id_medico);
        error_log("Dirección recibida: " . $direccion);
        error_log("MPPS Registro: " . $mpps_registro);
        
        $medico = new Medico();
        $resultado = $medico->editar($id_medico, $telefono, $direccion, $correo, $sexo, $adicional, $mpps_registro);
        
        if ($resultado['success']) {
            jsonResponse(['success' => true, 'message' => 'editado']);
        } else {
            jsonResponse(['success' => false, 'error' => $resultado['message']]);
        }
    }
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function cambiarFoto() {
        $id_medico = $_SESSION['usuario'];
        
        if(empty($id_medico)) {
            jsonResponse(['alert' => 'noedit', 'error' => 'Sesión no válida']);
            return;
        }
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
<<<<<<< HEAD
            if (in_array($_FILES['photo']['type'], $allowed_types)) {
                $nombre = uniqid() . '-' . $_FILES['photo']['name'];
                $ruta = '../img/' . $nombre;
                move_uploaded_file($_FILES['photo']['tmp_name'], $ruta);
                
                $medico = new Medico();
                $medico->cambiar_photo($id_medico, $nombre);
                
                foreach ($medico->objetos as $objeto) {
                    if($objeto->avatar_medico != 'avatarDES.jpg') {
                        @unlink('../img/' . $objeto->avatar_medico);
                    }
                }
                
                jsonResponse(['ruta' => APP_URL . '/img/' . $nombre, 'alert' => 'edit']);
            } else {
                jsonResponse(['alert' => 'noedit', 'error' => 'Tipo de archivo no permitido']);
=======
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);
            
            if (in_array($mime_type, $allowed_types)) {
                $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $nombre = uniqid() . '.' . $extension;
                $ruta_destino = dirname(__DIR__) . '/img/' . $nombre;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $ruta_destino)) {
                    $medico = new Medico();
                    $avatar_anterior = $medico->cambiar_photo($id_medico, $nombre);
                    
                    if ($avatar_anterior && $avatar_anterior != 'avatarDES.jpg') {
                        $ruta_anterior = dirname(__DIR__) . '/img/' . $avatar_anterior;
                        if (file_exists($ruta_anterior)) {
                            @unlink($ruta_anterior);
                        }
                    }
                    
                    jsonResponse(['ruta' => APP_URL . '/img/' . $nombre, 'alert' => 'edit']);
                } else {
                    jsonResponse(['alert' => 'noedit', 'error' => 'Error al mover el archivo']);
                }
            } else {
                jsonResponse(['alert' => 'noedit', 'error' => 'Tipo de archivo no permitido. Use JPG, PNG o GIF']);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            }
        } else {
            jsonResponse(['alert' => 'noedit', 'error' => 'No se recibió el archivo']);
        }
    }
    
<<<<<<< HEAD
    // API: Cambiar contraseña
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function cambiarPassword() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_medico != $id_sesion) {
<<<<<<< HEAD
            jsonResponse(['error' => 'No autorizado']);
=======
            jsonResponse(['resultado' => 'noupdate', 'error' => 'No autorizado']);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            return;
        }
        
        $oldpass = $_POST['oldpass'] ?? '';
        $newpass = $_POST['newpass'] ?? '';
        
        if(strlen($newpass) < 6) {
            jsonResponse(['resultado' => 'noupdate', 'error' => 'La contraseña debe tener al menos 6 caracteres']);
            return;
        }
        
        $loginMedico = new LoginMedico();
        ob_start();
        $loginMedico->cambiar_contra($id_medico, $oldpass, $newpass);
<<<<<<< HEAD
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    // API: Mis estadísticas
   public function misEstadisticas() {
=======
        $resultado = trim(ob_get_clean());
        
        jsonResponse(['resultado' => $resultado]);
    }
    
    public function misEstadisticas() {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("misEstadisticas - ID: $id_medico, Sesión: $id_sesion");
        
        if ($id_medico != $id_sesion) {
            ApiResponse::unauthorized('No autorizado');
            return;
        }
        
        $medico = new Medico();
        $estadisticas = $medico->obtenerEstadisticasCompletas($id_medico);
        
        ApiResponse::success($estadisticas, 'estadisticas', 'Estadísticas cargadas correctamente');
    }
    
<<<<<<< HEAD
    // API: Listar pacientes del médico
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function listarPacientes() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_medico != $id_sesion) {
            jsonResponse(['error' => 'No autorizado']);
            return;
        }
        
        $medico = new Medico();
        $pacientes = $medico->listarPacientes($id_medico);
        
        $resultado = array();
        foreach($pacientes as $paciente) {
            $resultado[] = array(
                'id_paciente' => $paciente->id_paciente,
                'nombre' => $paciente->nombre,
                'apellidos' => $paciente->apellidos,
                'cedula' => $paciente->cedula,
                'telefono' => $paciente->telefono,
<<<<<<< HEAD
                'correo' => $paciente->correo
=======
                'correo' => $paciente->correo,
                'total_recetas' => $paciente->total_recetas ?? 0,
                'ultima_receta' => $paciente->ultima_receta ?? null
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            );
        }
        jsonResponse($resultado);
    }
<<<<<<< HEAD
=======
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    public function actividadReciente() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("actividadReciente - ID: $id_medico, Sesión: $id_sesion");
        
        if ($id_medico != $id_sesion) {
            ApiResponse::unauthorized('No autorizado');
            return;
        }
        
        $medico = new Medico();
        $actividades = $medico->obtenerActividadReciente($id_medico);
        
        ApiResponse::success($actividades, 'actividad_cargada', 'Actividad reciente cargada correctamente');
    }
<<<<<<< HEAD
 public function pacientes() {
    AuthHelper::checkRole('medico', true);
    
    $options = [
        'title' => 'Mis Pacientes - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
            ['label' => 'Mis Pacientes']
        ],
        'active_page' => 'pacientes',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
        'scripts' => '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
        'id_medico' => $_SESSION['usuario'] ?? 0
    ];
    
    ViewHelper::renderDashboard('medico/med_pacientes', $data, $options);
}
 public function proximasCitas() {
=======
    
    public function proximasCitas() {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        error_log("proximasCitas - ID: $id_medico, Sesión: $id_sesion");
        
        if ($id_medico != $id_sesion) {
            ApiResponse::unauthorized('No autorizado');
            return;
        }
        
        $medico = new Medico();
        $citas = $medico->obtenerProximasCitas($id_medico);
        
        ApiResponse::success($citas, 'citas_cargadas', 'Próximas citas cargadas correctamente');
    }
<<<<<<< HEAD
=======
    
    public function pacientes() {
        AuthHelper::checkRole('medico', true);
        
        $options = [
            'title' => 'Mis Pacientes - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
                ['label' => 'Mis Pacientes']
            ],
            'active_page' => 'pacientes',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_medico' => $_SESSION['usuario'] ?? 0
        ];
        
        ViewHelper::renderDashboard('medico/med_pacientes', $data, $options);
    }
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
}
?>