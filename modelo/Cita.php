<?php
// modelo/Cita.php

include_once 'Conexion.php';

class Cita {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    /**
     * Lista las citas de un paciente específico
     */
    function listarPorPaciente($id_paciente, $filtro = 'proximas') {
        try {
            $sql = "SELECT c.*, 
                           e.nombre as especialidad_nombre,
                           CONCAT(rm.nombre_medico, ' ', rm.apellido_medico) as medico_nombre,
                           con.nombre as consultorio_nombre,
                           con.direccion_detallada as consultorio_direccion
                    FROM citas c
                    INNER JOIN especialidades e ON c.id_especialidad = e.id_especialidad
                    INNER JOIN registro_medico rm ON c.id_medico = rm.id_medico
                    INNER JOIN consultorios con ON c.id_consultorio = con.id_consultorio
                    WHERE c.id_paciente = :id_paciente";
            
            if ($filtro === 'proximas') {
                $sql .= " AND c.fecha_cita >= CURDATE() AND c.estado NOT IN ('cancelada', 'completada')";
            } elseif ($filtro === 'completadas') {
                $sql .= " AND c.estado = 'completada'";
            } elseif ($filtro === 'canceladas') {
                $sql .= " AND c.estado = 'cancelada'";
            } else { // todas
                $sql .= " AND c.estado NOT IN ('cancelada', 'completada')";
            }
            
            $sql .= " ORDER BY c.fecha_cita ASC, c.hora_cita ASC";
            
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_paciente' => $id_paciente));
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en listarPorPaciente: " . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Obtener los detalles de una cita específica
     */
    function obtenerDetalle($id_cita, $id_paciente) {
        try {
            $sql = "SELECT c.*, 
                           e.nombre as especialidad_nombre,
                           CONCAT(rm.nombre_medico, ' ', rm.apellido_medico) as medico_nombre,
                           con.nombre as consultorio_nombre,
                           con.direccion_detallada as consultorio_direccion,
                           con.telefono as consultorio_telefono,
                           con.email as consultorio_email
                    FROM citas c
                    INNER JOIN especialidades e ON c.id_especialidad = e.id_especialidad
                    INNER JOIN registro_medico rm ON c.id_medico = rm.id_medico
                    INNER JOIN consultorios con ON c.id_consultorio = con.id_consultorio
                    WHERE c.id_cita = :id_cita AND c.id_paciente = :id_paciente";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_cita' => $id_cita, ':id_paciente' => $id_paciente));
            return $query->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error en obtenerDetalle: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Crear una nueva cita
     */
    function crear($datos) {
        try {
            $sql = "INSERT INTO citas(
                        id_especialidad, id_medico, id_paciente, id_consultorio,
                        fecha_cita, hora_cita, tipo_consulta, motivo, 
                        es_tercero, nombre_tercero, cedula_tercero, telefono_tercero,
                        estado
                    ) VALUES (
                        :id_especialidad, :id_medico, :id_paciente, :id_consultorio,
                        :fecha_cita, :hora_cita, :tipo_consulta, :motivo,
                        :es_tercero, :nombre_tercero, :cedula_tercero, :telefono_tercero,
                        'pendiente'
                    )";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute($datos);
            
            if ($resultado) {
                return ['success' => true, 'message' => 'creado', 'id' => $this->acceso->lastInsertId()];
            } else {
                return ['success' => false, 'message' => 'error_creacion'];
            }
        } catch(PDOException $e) {
            error_log("Error en crear cita: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
    }
    
    /**
     * Cancelar una cita
     */
    function cancelar($id_cita, $id_paciente) {
        try {
            $sql = "UPDATE citas SET estado = 'cancelada' WHERE id_cita = :id_cita AND id_paciente = :id_paciente";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(':id_cita' => $id_cita, ':id_paciente' => $id_paciente));
            
            if ($resultado && $query->rowCount() > 0) {
                return ['success' => true, 'message' => 'cancelada'];
            } else {
                return ['success' => false, 'message' => 'no_encontrada'];
            }
        } catch(PDOException $e) {
            error_log("Error en cancelar cita: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
    }
    
    /**
     * Obtener horarios disponibles para una especialidad, médico y fecha
     */
    function obtenerHorariosDisponibles($id_especialidad, $id_medico, $fecha) {
        try {
            // Primero, obtener la duración de la especialidad
            $sql_duracion = "SELECT duracion_defecto FROM especialidades WHERE id_especialidad = :id_especialidad";
            $query_duracion = $this->acceso->prepare($sql_duracion);
            $query_duracion->execute(array(':id_especialidad' => $id_especialidad));
            $duracion = $query_duracion->fetch(PDO::FETCH_OBJ);
            $duracion_minutos = $duracion ? $duracion->duracion_defecto : 30;
            
            // Segundo, obtener el horario del consultorio para ese médico y día de la semana
            $dia_semana = $this->getDiaSemana($fecha);
            $sql_horario = "SELECT ch.hora_inicio, ch.hora_fin 
                            FROM consultorio_horarios ch
                            INNER JOIN consultorio_medicos cm ON ch.id_consultorio = cm.id_consultorio
                            WHERE cm.id_medico = :id_medico 
                            AND ch.dia_semana = :dia_semana 
                            AND ch.activo = 1";
            $query_horario = $this->acceso->prepare($sql_horario);
            $query_horario->execute(array(':id_medico' => $id_medico, ':dia_semana' => $dia_semana));
            $horario = $query_horario->fetch(PDO::FETCH_OBJ);
            
            if (!$horario) {
                return [];
            }
            
            // Tercero, obtener las citas ya reservadas para ese médico y fecha
            $sql_citas = "SELECT hora_cita FROM citas 
                          WHERE id_medico = :id_medico 
                          AND fecha_cita = :fecha 
                          AND estado NOT IN ('cancelada')";
            $query_citas = $this->acceso->prepare($sql_citas);
            $query_citas->execute(array(':id_medico' => $id_medico, ':fecha' => $fecha));
            $citas_ocupadas = $query_citas->fetchAll();
            
            $horas_ocupadas = [];
            foreach ($citas_ocupadas as $cita) {
                $horas_ocupadas[] = $cita->hora_cita;
            }
            
            // Generar todos los horarios posibles
            $hora_inicio = new DateTime($horario->hora_inicio);
            $hora_fin = new DateTime($horario->hora_fin);
            $intervalo = new DateInterval('PT' . $duracion_minutos . 'M');
            $periodo = new DatePeriod($hora_inicio, $intervalo, $hora_fin);
            
            $horarios_disponibles = [];
            foreach ($periodo as $hora) {
                $hora_str = $hora->format('H:i');
                if (!in_array($hora_str, $horas_ocupadas) && $hora < $hora_fin) {
                    $horarios_disponibles[] = $hora_str;
                }
            }
            
            return $horarios_disponibles;
        } catch(PDOException $e) {
            error_log("Error en obtenerHorariosDisponibles: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de citas para un paciente
     */
    function obtenerEstadisticas($id_paciente) {
        try {
            $stats = [];
            
            // Próximas citas
            $sql = "SELECT COUNT(*) as total FROM citas 
                    WHERE id_paciente = :id_paciente 
                    AND fecha_cita >= CURDATE() 
                    AND estado NOT IN ('cancelada', 'completada')";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_paciente' => $id_paciente));
            $stats['proximas'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Completadas
            $sql = "SELECT COUNT(*) as total FROM citas 
                    WHERE id_paciente = :id_paciente AND estado = 'completada'";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_paciente' => $id_paciente));
            $stats['completadas'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Canceladas
            $sql = "SELECT COUNT(*) as total FROM citas 
                    WHERE id_paciente = :id_paciente AND estado = 'cancelada'";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_paciente' => $id_paciente));
            $stats['canceladas'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            return $stats;
        } catch(PDOException $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            return ['proximas' => 0, 'completadas' => 0, 'canceladas' => 0];
        }
    }
    
    /**
     * Obtener cita por ID
     */
    function obtenerPorId($id_cita) {
        try {
            $sql = "SELECT c.*, 
                           rp.nombre_paciente, rp.apellido_paciente, rp.cedula_paciente,
                           rm.nombre_medico, rm.apellido_medico, rm.especialidad,
                           e.nombre as especialidad_nombre,
                           con.nombre as consultorio_nombre
                    FROM citas c
                    LEFT JOIN registro_paciente rp ON c.id_paciente = rp.id_paciente
                    LEFT JOIN registro_medico rm ON c.id_medico = rm.id_medico
                    LEFT JOIN especialidades e ON c.id_especialidad = e.id_especialidad
                    LEFT JOIN consultorios con ON c.id_consultorio = con.id_consultorio
                    WHERE c.id_cita = :id_cita";
            
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_cita' => $id_cita));
            return $query->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return null;
        }
    }
    
    private function getDiaSemana($fecha) {
        $dias = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
        $nombre_ingles = date('l', strtotime($fecha));
        return $dias[$nombre_ingles];
    }
}
?>
