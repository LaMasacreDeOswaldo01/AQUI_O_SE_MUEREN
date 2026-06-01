<?php
include_once 'Conexion.php';

class RespuestaSeguridad {
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    /**
     * Guarda una respuesta de seguridad para un usuario
     * @param int $id_usuario ID del usuario (id_paciente, id_medico, etc.)
     * @param string $pregunta Pregunta de seguridad
     * @param string $respuesta Respuesta en texto plano (se hasheará internamente)
     * @return array Resultado de la operación
     */
    function crear($id_usuario, $pregunta, $respuesta) {
        try {
            // Hashear la respuesta para seguridad
            $respuesta_hash = password_hash($respuesta, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO respuestas_seguridad_usuario 
                    (id_usuario, pregunta, respuesta_hash) 
                    VALUES (:id_usuario, :pregunta, :respuesta_hash)";
            
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute([
                ':id_usuario' => $id_usuario,
                ':pregunta' => $pregunta,
                ':respuesta_hash' => $respuesta_hash
            ]);
            
            if ($resultado) {
                return ['success' => true, 'id' => $this->acceso->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'error_insert'];
            }
        } catch(PDOException $e) {
            error_log("Error en crear respuesta seguridad: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_db'];
        }
    }
    
    /**
     * Verifica si una respuesta coincide con la almacenada
     * @param int $id_usuario ID del usuario
     * @param string $pregunta Pregunta de seguridad
     * @param string $respuesta Respuesta a verificar
     * @return bool True si la respuesta es correcta
     */
    function verificar($id_usuario, $pregunta, $respuesta) {
        try {
            $sql = "SELECT respuesta_hash 
                    FROM respuestas_seguridad_usuario 
                    WHERE id_usuario = :id_usuario AND pregunta = :pregunta";
            
            $query = $this->acceso->prepare($sql);
            $query->execute([
                ':id_usuario' => $id_usuario,
                ':pregunta' => $pregunta
            ]);
            
            $registro = $query->fetch(PDO::FETCH_OBJ);
            
            if ($registro && password_verify($respuesta, $registro->respuesta_hash)) {
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Error en verificar respuesta seguridad: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene las preguntas de seguridad disponibles
     * @return array Lista de preguntas
     */
    function obtenerPreguntas() {
        return [
            '¿Cuál es el nombre de tu primera mascota?',
            '¿En qué ciudad naciste?',
            '¿Cuál es el apellido de soltera de tu madre?',
            '¿Cuál fue tu primer trabajo?',
            '¿Cuál es tu comida favorita?'
        ];
    }
    
    /**
     * Obtiene las preguntas ya configuradas por un usuario
     * @param int $id_usuario ID del usuario
     * @return array Lista de preguntas configuradas
     */
    function obtenerPreguntasUsuario($id_usuario) {
        try {
            $sql = "SELECT pregunta 
                    FROM respuestas_seguridad_usuario 
                    WHERE id_usuario = :id_usuario";
            
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_usuario' => $id_usuario]);
            
            $preguntas = [];
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $preguntas[] = $row->pregunta;
            }
            
            return $preguntas;
        } catch(PDOException $e) {
            error_log("Error en obtener preguntas usuario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Elimina todas las respuestas de seguridad de un usuario
     * @param int $id_usuario ID del usuario
     * @return bool True si se eliminaron correctamente
     */
    function eliminarTodas($id_usuario) {
        try {
            $sql = "DELETE FROM respuestas_seguridad_usuario 
                    WHERE id_usuario = :id_usuario";
            
            $query = $this->acceso->prepare($sql);
            return $query->execute([':id_usuario' => $id_usuario]);
        } catch(PDOException $e) {
            error_log("Error en eliminar respuestas usuario: " . $e->getMessage());
            return false;
        }
    }
}
?>
