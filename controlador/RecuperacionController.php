<?php
class RecuperacionController {
    
    // ==================== VISTA DE RECUPERACIÓN ====================
    public function showRecuperarCuenta() {
        renderView('recuperar_cuenta');
    }
    
    // ==================== API DE RECUPERACIÓN ====================
    
    /**
     * Busca un usuario por correo electrónico en todas las tablas
     */
    public function buscarUsuario() {
        $correo = trim($_POST['correo'] ?? '');
        
        if (empty($correo)) {
            ApiResponse::error('El correo es requerido', 'validation_error', [], 400);
            return;
        }
        
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            ApiResponse::error('Correo inválido', 'validation_error', [], 400);
            return;
        }
        
        try {
            $db = new Conexion();
            
            // Buscar en todas las tablas de usuarios
            $tablas = [
                'registro_paciente' => ['id' => 'id_paciente', 'nombre' => 'nombre_paciente', 'apellido' => 'apellido_paciente', 'cedula' => 'cedula_paciente', 'rol' => 'paciente'],
                'registro_medico' => ['id' => 'id_medico', 'nombre' => 'nombre_medico', 'apellido' => 'apellido_medico', 'cedula' => 'cedula_medico', 'rol' => 'medico'],
                'registro_asistente' => ['id' => 'id_asistente', 'nombre' => 'nombre_asistente', 'apellido' => 'apellido_asistente', 'cedula' => 'cedula_asistente', 'rol' => 'asistente'],
                'registro_administrador' => ['id' => 'id_administrador', 'nombre' => 'nombre_administrador', 'apellido' => 'apellido_administrador', 'cedula' => 'cedula_administrador', 'rol' => 'administrador']
            ];
            
            $usuarioEncontrado = null;
            
            foreach ($tablas as $tabla => $campos) {
                $sql = "SELECT {$campos['id']} as id, {$campos['nombre']} as nombre, 
                               {$campos['apellido']} as apellido, {$campos['cedula']} as cedula
                        FROM {$tabla}
                        WHERE correo_{$campos['rol']} = :correo
                        LIMIT 1";
                
                $query = $db->pdo->prepare($sql);
                $query->execute([':correo' => $correo]);
                $resultado = $query->fetch(PDO::FETCH_OBJ);
                
                if ($resultado) {
                    $usuarioEncontrado = [
                        'id' => $resultado->id,
                        'nombre' => $resultado->nombre,
                        'apellido' => $resultado->apellido,
                        'cedula' => $resultado->cedula,
                        'rol' => ucfirst($campos['rol']),
                        'tabla' => $tabla
                    ];
                    break;
                }
            }
            
            if (!$usuarioEncontrado) {
                ApiResponse::error('No se encontró ningún usuario con ese correo electrónico', 'not_found', [], 404);
                return;
            }
            
            // Obtener las preguntas de seguridad del usuario
            $sql = "SELECT pregunta FROM respuestas_seguridad_usuario 
                    WHERE id_usuario = :id_usuario 
                    ORDER BY id_respuesta ASC";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id_usuario' => $usuarioEncontrado['id']]);
            $preguntas = $query->fetchAll(PDO::FETCH_OBJ);
            
            if (count($preguntas) === 0) {
                ApiResponse::error('El usuario no tiene preguntas de seguridad configuradas', 'no_security_questions', [], 400);
                return;
            }
            
            $preguntasArray = [];
            foreach ($preguntas as $pregunta) {
                $preguntasArray[] = [
                    'pregunta' => $pregunta->pregunta
                ];
            }
            
            ApiResponse::success([
                'usuario' => $usuarioEncontrado,
                'preguntas' => $preguntasArray
            ], 'Usuario encontrado');
            
        } catch(PDOException $e) {
            error_log("Error en buscarUsuario: " . $e->getMessage());
            ApiResponse::error('Error al buscar usuario', 'database_error', [], 500);
        }
    }
    
    /**
     * Verifica las respuestas de seguridad
     */
    public function verificarRespuestas() {
        $id_usuario = $_POST['id_usuario'] ?? '';
        $respuestas = $_POST['respuestas'] ?? [];
        
        if (empty($id_usuario)) {
            ApiResponse::error('ID de usuario requerido', 'validation_error', [], 400);
            return;
        }
        
        if (!is_array($respuestas) || count($respuestas) === 0) {
            ApiResponse::error('Las respuestas son requeridas', 'validation_error', [], 400);
            return;
        }
        
        try {
            $db = new Conexion();
            
            // Obtener las preguntas y respuestas hasheadas del usuario
            $sql = "SELECT pregunta, respuesta_hash 
                    FROM respuestas_seguridad_usuario 
                    WHERE id_usuario = :id_usuario 
                    ORDER BY id_respuesta ASC";
            $query = $db->pdo->prepare($sql);
            $query->execute([':id_usuario' => $id_usuario]);
            $respuestasGuardadas = $query->fetchAll(PDO::FETCH_OBJ);
            
            if (count($respuestasGuardadas) === 0) {
                ApiResponse::error('No se encontraron preguntas de seguridad', 'no_security_questions', [], 400);
                return;
            }
            
            // Verificar cada respuesta
            $respuestasCorrectas = 0;
            $totalPreguntas = count($respuestasGuardadas);
            
            foreach ($respuestasGuardadas as $index => $respuestaGuardada) {
                if (isset($respuestas[$index])) {
                    $respuestaUsuario = trim($respuestas[$index]);
                    if (password_verify($respuestaUsuario, $respuestaGuardada->respuesta_hash)) {
                        $respuestasCorrectas++;
                    }
                }
            }
            
            // Considerar correcto si al menos 2 de 3 respuestas coinciden
            if ($respuestasCorrectas >= 2) {
                ApiResponse::success([
                    'respuestas_correctas' => $respuestasCorrectas,
                    'total_preguntas' => $totalPreguntas
                ], 'Respuestas verificadas correctamente');
            } else {
                ApiResponse::error('Las respuestas no coinciden. Intente nuevamente.', 'incorrect_answers', [], 401);
            }
            
        } catch(PDOException $e) {
            error_log("Error en verificarRespuestas: " . $e->getMessage());
            ApiResponse::error('Error al verificar respuestas', 'database_error', [], 500);
        }
    }
    
    /**
     * Cambia la contraseña del usuario
     */
    public function cambiarPassword() {
        $id_usuario = $_POST['id_usuario'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $nueva_password = $_POST['nueva_password'] ?? '';
        
        error_log("DEBUG cambiarPassword - id_usuario: " . $id_usuario);
        error_log("DEBUG cambiarPassword - rol: " . $rol);
        error_log("DEBUG cambiarPassword - nueva_password length: " . strlen($nueva_password));
        
        if (empty($id_usuario)) {
            ApiResponse::error('ID de usuario requerido', 'validation_error', [], 400);
            return;
        }
        
        if (empty($rol)) {
            ApiResponse::error('Rol requerido', 'validation_error', [], 400);
            return;
        }
        
        if (strlen($nueva_password) < 6) {
            ApiResponse::error('La contraseña debe tener al menos 6 caracteres', 'validation_error', [], 400);
            return;
        }
        
        try {
            $db = new Conexion();
            
            // Mapeo de roles a tablas de login y campos ID
            $mapa = [
                'Paciente' => ['tabla_login' => 'login_paciente', 'campo_id' => 'id_paciente'],
                'Medico' => ['tabla_login' => 'login_medico', 'campo_id' => 'id_medico'],
                'Asistente' => ['tabla_login' => 'login_asistente', 'campo_id' => 'id_asistente'],
                'Administrador' => ['tabla_login' => 'login_administrador', 'campo_id' => 'id_administrador']
            ];
            
            $rolCapitalizado = ucfirst(strtolower($rol));
            error_log("DEBUG cambiarPassword - rolCapitalizado: " . $rolCapitalizado);
            
            if (!isset($mapa[$rolCapitalizado])) {
                error_log("ERROR: Rol no válido - " . $rolCapitalizado);
                ApiResponse::error('Rol no válido: ' . $rolCapitalizado, 'validation_error', [], 400);
                return;
            }
            
            $config = $mapa[$rolCapitalizado];
            $tablaLogin = $config['tabla_login'];
            $campoId = $config['campo_id'];
            
            error_log("DEBUG cambiarPassword - tablaLogin: " . $tablaLogin);
            error_log("DEBUG cambiarPassword - campoId: " . $campoId);
            
            // Verificar si el usuario existe en la tabla de login
            $sql_check = "SELECT {$campoId} FROM {$tablaLogin} WHERE {$campoId} = :id_usuario";
            $query_check = $db->pdo->prepare($sql_check);
            $query_check->execute([':id_usuario' => $id_usuario]);
            $existe = $query_check->fetch();
            error_log("DEBUG cambiarPassword - usuario existe en login: " . ($existe ? 'true' : 'false'));
            
            if (!$existe) {
                error_log("ERROR: Usuario no encontrado en tabla de login " . $tablaLogin);
                ApiResponse::error('Usuario no encontrado en tabla de login. Contacte al administrador.', 'user_not_found', [], 404);
                return;
            }
            
            // Hashear la nueva contraseña
            $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            
            // Actualizar la contraseña en la tabla de login
            $sql = "UPDATE {$tablaLogin} 
                    SET password_hash = :password_hash 
                    WHERE {$campoId} = :id_usuario";
            error_log("DEBUG cambiarPassword - SQL: " . $sql);
            
            $query = $db->pdo->prepare($sql);
            $resultado = $query->execute([
                ':password_hash' => $password_hash,
                ':id_usuario' => $id_usuario
            ]);
            
            error_log("DEBUG cambiarPassword - resultado: " . ($resultado ? 'true' : 'false'));
            
            if ($resultado) {
                ApiResponse::success([], 'Contraseña actualizada exitosamente');
            } else {
                error_log("ERROR: No se actualizó la contraseña");
                ApiResponse::error('Error al actualizar la contraseña', 'update_error', [], 500);
            }
            
        } catch(PDOException $e) {
            error_log("Error en cambiarPassword: " . $e->getMessage());
            ApiResponse::error('Error al cambiar contraseña: ' . $e->getMessage(), 'database_error', [], 500);
        }
    }
}
?>
