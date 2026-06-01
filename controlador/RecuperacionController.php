<?php

require_once __DIR__ . '/../modelo/UsuarioPorCorreo.php';
require_once __DIR__ . '/../helpers/ApiResponse.php';

class RecuperacionController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new UsuarioPorCorreo();
    }
    
    /**
     * Busca un usuario por correo electrónico
     */
    public function buscarUsuario() {
        $correo = $_POST['correo'] ?? '';
        
        if (empty($correo)) {
            ApiResponse::error('El correo electrónico es requerido', 'validation_error', [], 400);
            return;
        }
        
        $usuario = $this->usuarioModel->buscarPorCorreo($correo);
        
        if (!$usuario) {
            ApiResponse::error('No se encontró ninguna cuenta con ese correo electrónico', 'not_found', [], 404);
            return;
        }
        
        // Obtener las preguntas de seguridad del usuario
        $preguntas = $this->usuarioModel->obtenerPreguntasSeguridad($usuario['id']);
        
        if (count($preguntas) < 1) {
            ApiResponse::error('El usuario no tiene preguntas de seguridad configuradas', 'insufficient_questions', [], 400);
            return;
        }
        
        ApiResponse::success([
            'usuario_id' => $usuario['id'],
            'rol' => $usuario['rol'],
            'nombre' => $usuario['nombre'],
            'apellidos' => $usuario['apellidos'],
            'cedula' => $usuario['cedula'],
            'preguntas' => $preguntas
        ], 'Usuario encontrado');
    }
    
    /**
     * Verifica las respuestas de seguridad
     */
    public function verificarRespuestas() {
        $usuario_id = $_POST['usuario_id'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $respuestas_json = $_POST['respuestas_json'] ?? '';
        
        // Decodificar el JSON string
        $respuestas = json_decode($respuestas_json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            ApiResponse::error('Error al decodificar las respuestas', 'validation_error', [], 400);
            return;
        }
        
        if (empty($usuario_id) || empty($rol) || empty($respuestas)) {
            ApiResponse::error('Datos incompletos', 'validation_error', [], 400);
            return;
        }
        
        if (!is_array($respuestas)) {
            ApiResponse::error('Las respuestas deben ser un array', 'validation_error', [], 400);
            return;
        }
        
        $verificado = $this->usuarioModel->verificarRespuestas($usuario_id, $respuestas);
        
        if ($verificado) {
            ApiResponse::success([
                'usuario_id' => $usuario_id,
                'rol' => $rol
            ], 'Respuestas verificadas correctamente');
        } else {
            ApiResponse::error('Una o más respuestas son incorrectas', 'invalid_answers', [], 401);
        }
    }
    
    /**
     * Obtiene el nombre de usuario (cedula) del usuario
     */
    public function obtenerUsuario() {
        require_once __DIR__ . '/../config/database.php';
        
        $usuario_id = $_POST['usuario_id'] ?? '';
        $rol = $_POST['rol'] ?? '';
        
        if (empty($usuario_id) || empty($rol)) {
            ApiResponse::error('Datos incompletos', 'validation_error', [], 400);
            return;
        }
        
        // Buscar específicamente por ID y rol
        $tabla = '';
        $id_column = '';
        $cedula_column = '';
        
        switch ($rol) {
            case 'paciente':
                $tabla = 'registro_paciente';
                $id_column = 'id_paciente';
                $cedula_column = 'cedula_paciente';
                break;
            case 'medico':
                $tabla = 'registro_medico';
                $id_column = 'id_medico';
                $cedula_column = 'cedula_medico';
                break;
            case 'asistente':
                $tabla = 'registro_asistente';
                $id_column = 'id_asistente';
                $cedula_column = 'cedula_asistente';
                break;
            case 'administrador':
                $tabla = 'registro_administrador';
                $id_column = 'id_administrador';
                $cedula_column = 'cedula_administrador';
                break;
            default:
                ApiResponse::error('Rol no válido', 'validation_error', [], 400);
                return;
        }
        
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT {$cedula_column} as cedula FROM {$tabla} WHERE {$id_column} = :id LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $usuario_id);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            ApiResponse::error('Usuario no encontrado', 'not_found', [], 404);
            return;
        }
        
        ApiResponse::success([
            'cedula' => $usuario['cedula']
        ], 'Usuario encontrado');
    }
    
    /**
     * Cambia la contraseña del usuario
     */
    public function cambiarPassword() {
        $usuario_id = $_POST['usuario_id'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $nueva_password = $_POST['nueva_password'] ?? '';
        $confirmar_password = $_POST['confirmar_password'] ?? '';
        
        if (empty($usuario_id) || empty($rol) || empty($nueva_password) || empty($confirmar_password)) {
            ApiResponse::error('Todos los campos son requeridos', 'validation_error', [], 400);
            return;
        }
        
        if ($nueva_password !== $confirmar_password) {
            ApiResponse::error('Las contraseñas no coinciden', 'password_mismatch', [], 400);
            return;
        }
        
        if (strlen($nueva_password) < 6) {
            ApiResponse::error('La contraseña debe tener al menos 6 caracteres', 'password_too_short', [], 400);
            return;
        }
        
        // Hashear la nueva contraseña
        $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
        
        $actualizado = $this->usuarioModel->actualizarPassword($usuario_id, $rol, $password_hash);
        
        if ($actualizado) {
            ApiResponse::success([], 'Contraseña actualizada correctamente');
        } else {
            ApiResponse::error('Error al actualizar la contraseña', 'update_error', [], 500);
        }
    }
}
