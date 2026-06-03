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
            );
        }
        jsonResponse($json);
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
    
    public function cambiarFoto() {
        $id_medico = $_SESSION['usuario'];
        
        if(empty($id_medico)) {
            jsonResponse(['alert' => 'noedit', 'error' => 'Sesión no válida']);
            return;
        }
        
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
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
            }
        } else {
            jsonResponse(['alert' => 'noedit', 'error' => 'No se recibió el archivo']);
        }
    }
    
    public function cambiarPassword() {
        $id_medico = $_POST['id_medico'] ?? 0;
        $id_sesion = $_SESSION['usuario'];
        
        if($id_medico != $id_sesion) {
            jsonResponse(['resultado' => 'noupdate', 'error' => 'No autorizado']);
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
        $resultado = trim(ob_get_clean());
        
        jsonResponse(['resultado' => $resultado]);
    }
    
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
                'correo' => $paciente->correo,
                'total_recetas' => $paciente->total_recetas ?? 0,
                'ultima_receta' => $paciente->ultima_receta ?? null
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
    } public function mostrarAlertas() {
        // Forzar y validar el rol mediante tu Helper de Autenticación
        AuthHelper::checkRole('medico', true);
        
        $options = [
            'title' => 'Gestión de Alertas Médicas - BioVital',
            'breadcrumbs' => [
                ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
                ['label' => 'Alertas Epidemiológicas']
            ],
            'active_page' => 'alertas',
            'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">',
            'scripts' => '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
            'id_medico' => $_SESSION['usuario'] ?? 0
        ];
        
        // Renderizar usando la ruta de la vista limpia que organizamos
        ViewHelper::renderDashboard('medico/med_alerta', $data, $options);
    }
}
?>