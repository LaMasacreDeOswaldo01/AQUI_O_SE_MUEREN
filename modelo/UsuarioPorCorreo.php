<?php

include_once 'Conexion.php';

class UsuarioPorCorreo {
    private $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    /**
     * Busca un usuario por correo electrónico en todas las tablas
     * @param string $correo Correo electrónico a buscar
     * @return array|null Información del usuario encontrado o null si no existe
     */
    public function buscarPorCorreo($correo) {
        $correo = trim($correo);
        
        if (empty($correo)) {
            return null;
        }
        
        // Buscar en tabla de pacientes
        $sql = "SELECT id_paciente as id, nombre_paciente as nombre, apellido_paciente as apellidos, cedula_paciente as cedula, correo_paciente as correo, 'paciente' as rol 
                FROM registro_paciente 
                WHERE correo_paciente = :correo LIMIT 1";
        $stmt = $this->acceso->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            return $usuario;
        }
        
        // Buscar en tabla de médicos
        $sql = "SELECT id_medico as id, nombre_medico as nombre, apellido_medico as apellidos, cedula_medico as cedula, correo_medico as correo, 'medico' as rol 
                FROM registro_medico 
                WHERE correo_medico = :correo LIMIT 1";
        $stmt = $this->acceso->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            return $usuario;
        }
        
        // Buscar en tabla de asistentes
        $sql = "SELECT id_asistente as id, nombre_asistente as nombre, apellido_asistente as apellidos, cedula_asistente as cedula, correo_asistente as correo, 'asistente' as rol 
                FROM registro_asistente 
                WHERE correo_asistente = :correo LIMIT 1";
        $stmt = $this->acceso->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            return $usuario;
        }
        
        // Buscar en tabla de administradores
        $sql = "SELECT id_administrador as id, nombre_administrador as nombre, apellido_administrador as apellidos, cedula_administrador as cedula, correo_administrador as correo, 'administrador' as rol 
                FROM registro_administrador 
                WHERE correo_administrador = :correo LIMIT 1";
        $stmt = $this->acceso->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            return $usuario;
        }
        
        return null;
    }
    
    /**
     * Obtiene las preguntas de seguridad de un usuario
     * @param int $usuario_id ID del usuario
     * @return array Array con las preguntas de seguridad
     */
    public function obtenerPreguntasSeguridad($usuario_id) {
        // Obtener las últimas 3 preguntas (asumiendo que los IDs más altos son los más recientes)
        $sql = "SELECT pregunta FROM respuestas_seguridad_usuario 
                WHERE id_usuario = :usuario_id 
                ORDER BY id_respuesta DESC 
                LIMIT 3";
        $stmt = $this->acceso->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuario_id);
        $stmt->execute();
        
        $preguntas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $preguntas[] = $row['pregunta'];
        }
        
        // Invertir el array para mantener el orden original
        $preguntas = array_reverse($preguntas);
        
        return $preguntas;
    }
    
    /**
     * Verifica las respuestas de seguridad de un usuario
     * @param int $usuario_id ID del usuario
     * @param array $respuestas Array asociativo con pregunta => respuesta
     * @return bool True si todas las respuestas son correctas
     */
    public function verificarRespuestas($usuario_id, $respuestas) {
        $correctas = 0;
        $total = count($respuestas);
        
        foreach ($respuestas as $pregunta => $respuesta) {
            $sql = "SELECT respuesta_hash FROM respuestas_seguridad_usuario 
                    WHERE id_usuario = :usuario_id AND pregunta = :pregunta 
                    LIMIT 1";
            $stmt = $this->acceso->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':pregunta', $pregunta);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row && password_verify($respuesta, $row['respuesta_hash'])) {
                $correctas++;
            }
        }
        
        return $correctas === $total;
    }
    
    /**
     * Actualiza la contraseña de un usuario
     * @param int $usuario_id ID del usuario
     * @param string $rol Rol del usuario (paciente, medico, asistente, administrador)
     * @param string $nueva_password Nueva contraseña hasheada
     * @return bool True si se actualizó correctamente
     */
    public function actualizarPassword($usuario_id, $rol, $nueva_password) {
        $tabla_login = '';
        $id_column = '';
        
        switch ($rol) {
            case 'paciente':
                $tabla_login = 'login_paciente';
                $id_column = 'id_paciente';
                break;
            case 'medico':
                $tabla_login = 'login_medico';
                $id_column = 'id_medico';
                break;
            case 'asistente':
                $tabla_login = 'login_asistente';
                $id_column = 'id_asistente';
                break;
            case 'administrador':
                $tabla_login = 'login_administrador';
                $id_column = 'id_administrador';
                break;
            default:
                return false;
        }
        
        $sql = "UPDATE {$tabla_login} SET password_hash = :password_hash WHERE {$id_column} = :id";
        $stmt = $this->acceso->prepare($sql);
        $stmt->bindParam(':password_hash', $nueva_password);
        $stmt->bindParam(':id', $usuario_id);
        
        return $stmt->execute();
    }
}
