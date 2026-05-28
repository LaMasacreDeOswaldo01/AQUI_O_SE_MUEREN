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
    
    // API: Buscar médico (para mostrar datos)
   public function buscar() {
    $id_medico = $_POST['dato'] ?? $_POST['id_medico'] ?? 0;
    $id_sesion = $_SESSION['usuario'];
    
    error_log("MedicoController::buscar - ID: $id_medico, Sesión: $id_sesion");
    
    if($id_medico != $id_sesion) {
        ApiResponse::error('No autorizado para ver este perfil', 'unauthorized', [], 403);
        return;
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
    
    // API: Capturar datos para editar (CORREGIDO)
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
            $json = array(
                'telefono' => $objeto->telefono_medico ?? '',
                'direccion' => $objeto->direccion_medico ?? '',
                'correo' => $objeto->correo_medico ?? '',
                'sexo' => $objeto->sexo_medico ?? '',
                'adicional' => $objeto->adicional_medico ?? ''
            );
        }
        jsonResponse($json);
    }
    
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
    public function cambiarFoto() {
        $id_medico = $_SESSION['usuario'];
        
        if(empty($id_medico)) {
            jsonResponse(['alert' => 'noedit', 'error' => 'Sesión no válida']);
            return;
        }
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
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
            }
        } else {
            jsonResponse(['alert' => 'noedit', 'error' => 'No se recibió el archivo']);
        }
    }
    
    // API: Cambiar contraseña
    public function cambiarPassword() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_medico != $id_sesion) {
            jsonResponse(['error' => 'No autorizado']);
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
        $resultado = ob_get_clean();
        
        jsonResponse(['resultado' => trim($resultado)]);
    }
    
    // API: Mis estadísticas
   public function misEstadisticas() {
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
    
    // API: Listar pacientes del médico
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
                'correo' => $paciente->correo
            );
        }
        jsonResponse($resultado);
    }
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
}
?>