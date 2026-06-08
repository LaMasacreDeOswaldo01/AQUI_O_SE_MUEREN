<?php
// modelo/Evolucion.php

include_once 'Conexion.php';

class Evolucion {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    /**
     * Obtener citas del médico para seleccionar
     */
    function obtenerCitasMedico($id_medico, $estado = 'completada') {
        try {
            $sql = "SELECT 
                        c.id_cita,
                        c.fecha_cita,
                        c.hora_cita,
                        c.tipo_consulta,
                        c.motivo,
                        c.estado,
                        p.id_paciente,
                        p.nombre_paciente,
                        p.apellido_paciente,
                        p.cedula_paciente,
                        p.fecha_nacimiento_pac,
                        p.sexo_paciente,
                        p.tipo_sangre,
                        e.nombre as especialidad_nombre,
                        e.id_especialidad,
                        con.nombre as consultorio_nombre
                    FROM citas c
                    INNER JOIN registro_paciente p ON c.id_paciente = p.id_paciente
                    INNER JOIN especialidades e ON c.id_especialidad = e.id_especialidad
                    LEFT JOIN consultorios con ON c.id_consultorio = con.id_consultorio
                    WHERE c.id_medico = :id_medico
                    ORDER BY c.fecha_cita DESC, c.hora_cita DESC";
            
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_medico' => $id_medico]);
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en obtenerCitasMedico: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener detalle de una cita específica
     */
    function obtenerDetalleCita($id_cita, $id_medico) {
        try {
            $sql = "SELECT 
                        c.id_cita,
                        c.fecha_cita,
                        c.hora_cita,
                        c.tipo_consulta,
                        c.motivo,
                        c.estado,
                        p.id_paciente,
                        p.nombre_paciente,
                        p.apellido_paciente,
                        p.cedula_paciente,
                        p.fecha_nacimiento_pac,
                        p.sexo_paciente,
                        p.tipo_sangre,
                        e.nombre as especialidad_nombre,
                        e.id_especialidad,
                        con.nombre as consultorio_nombre,
                        con.direccion_detallada as consultorio_direccion
                    FROM citas c
                    INNER JOIN registro_paciente p ON c.id_paciente = p.id_paciente
                    INNER JOIN especialidades e ON c.id_especialidad = e.id_especialidad
                    LEFT JOIN consultorios con ON c.id_consultorio = con.id_consultorio
                    WHERE c.id_cita = :id_cita AND c.id_medico = :id_medico";
            
            $query = $this->acceso->prepare($sql);
            $query->execute([
                ':id_cita' => $id_cita,
                ':id_medico' => $id_medico
            ]);
            return $query->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error en obtenerDetalleCita: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener evolución existente para una cita
     */
    function obtenerEvolucionPorCita($id_cita) {
        try {
            $sql = "SELECT * FROM evoluciones_clinicas WHERE id_cita = :id_cita";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_cita' => $id_cita]);
            return $query->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error en obtenerEvolucionPorCita: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Guardar o actualizar evolución clínica
     */
    function guardarEvolucion($datos) {
        try {
            $id_cita = $datos['id_cita'];
            $id_medico = $datos['id_medico'];
            $id_paciente = $datos['id_paciente'];
            
            // Verificar si ya existe una evolución para esta cita
            $existe = $this->obtenerEvolucionPorCita($id_cita);
            
            if ($existe) {
                // Actualizar evolución existente
                $sql = "UPDATE evoluciones_clinicas SET 
                            peso = :peso,
                            talla = :talla,
                            imc = :imc,
                            temperatura = :temperatura,
                            tension_sistolica = :tension_sistolica,
                            tension_diastolica = :tension_diastolica,
                            frecuencia_cardiaca = :frecuencia_cardiaca,
                            frecuencia_respiratoria = :frecuencia_respiratoria,
                            saturacion_oxigeno = :saturacion_oxigeno,
                            motivo_consulta = :motivo_consulta,
                            enfermedad_actual = :enfermedad_actual,
                            examen_fisico = :examen_fisico,
                            diagnostico = :diagnostico,
                            tratamiento = :tratamiento,
                            recomendaciones = :recomendaciones,
                            notas_adicionales = :notas_adicionales,
                            updated_at = NOW()
                        WHERE id_cita = :id_cita";
            } else {
                // Insertar nueva evolución
                $sql = "INSERT INTO evoluciones_clinicas (
                            id_cita, id_medico, id_paciente,
                            peso, talla, imc, temperatura,
                            tension_sistolica, tension_diastolica,
                            frecuencia_cardiaca, frecuencia_respiratoria,
                            saturacion_oxigeno,
                            motivo_consulta, enfermedad_actual, examen_fisico,
                            diagnostico, tratamiento, recomendaciones, notas_adicionales
                        ) VALUES (
                            :id_cita, :id_medico, :id_paciente,
                            :peso, :talla, :imc, :temperatura,
                            :tension_sistolica, :tension_diastolica,
                            :frecuencia_cardiaca, :frecuencia_respiratoria,
                            :saturacion_oxigeno,
                            :motivo_consulta, :enfermedad_actual, :examen_fisico,
                            :diagnostico, :tratamiento, :recomendaciones, :notas_adicionales
                        )";
            }
            
            // Calcular IMC si se tienen peso y talla
            $peso = floatval($datos['peso'] ?? 0);
            $talla = floatval($datos['talla'] ?? 0);
            $imc = ($talla > 0) ? round($peso / (($talla/100) * ($talla/100)), 1) : null;
            
            $params = [
                ':id_cita' => $id_cita,
                ':id_medico' => $id_medico,
                ':id_paciente' => $id_paciente,
                ':peso' => !empty($datos['peso']) ? floatval($datos['peso']) : null,
                ':talla' => !empty($datos['talla']) ? floatval($datos['talla']) : null,
                ':imc' => $imc,
                ':temperatura' => !empty($datos['temperatura']) ? floatval($datos['temperatura']) : null,
                ':tension_sistolica' => !empty($datos['tension_sistolica']) ? intval($datos['tension_sistolica']) : null,
                ':tension_diastolica' => !empty($datos['tension_diastolica']) ? intval($datos['tension_diastolica']) : null,
                ':frecuencia_cardiaca' => !empty($datos['frecuencia_cardiaca']) ? intval($datos['frecuencia_cardiaca']) : null,
                ':frecuencia_respiratoria' => !empty($datos['frecuencia_respiratoria']) ? intval($datos['frecuencia_respiratoria']) : null,
                ':saturacion_oxigeno' => !empty($datos['saturacion_oxigeno']) ? intval($datos['saturacion_oxigeno']) : null,
                ':motivo_consulta' => $datos['motivo_consulta'] ?? '',
                ':enfermedad_actual' => $datos['enfermedad_actual'] ?? '',
                ':examen_fisico' => $datos['examen_fisico'] ?? '',
                ':diagnostico' => $datos['diagnostico'] ?? '',
                ':tratamiento' => $datos['tratamiento'] ?? '',
                ':recomendaciones' => $datos['recomendaciones'] ?? '',
                ':notas_adicionales' => $datos['notas_adicionales'] ?? ''
            ];
            
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute($params);
            
            if ($resultado) {
                // Actualizar estado de la cita a completada si se marca
                if (isset($datos['marcar_completada']) && $datos['marcar_completada']) {
                    $this->marcarCitaCompletada($id_cita);
                }
                return ['success' => true, 'message' => 'Evolución guardada correctamente'];
            } else {
                return ['success' => false, 'message' => 'Error al guardar la evolución'];
            }
        } catch(PDOException $e) {
            error_log("Error en guardarEvolucion: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Marcar cita como completada
     */
    function marcarCitaCompletada($id_cita) {
        try {
            $sql = "UPDATE citas SET estado = 'completada', updated_at = NOW() WHERE id_cita = :id_cita";
            $query = $this->acceso->prepare($sql);
            return $query->execute([':id_cita' => $id_cita]);
        } catch(PDOException $e) {
            error_log("Error en marcarCitaCompletada: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Listar evoluciones del médico
     */
    function listarEvoluciones($id_medico, $limit = 50) {
        try {
            $sql = "SELECT 
                        e.*,
                        c.fecha_cita,
                        c.hora_cita,
                        c.tipo_consulta,
                        p.nombre_paciente,
                        p.apellido_paciente,
                        p.cedula_paciente,
                        esp.nombre as especialidad_nombre
                    FROM evoluciones_clinicas e
                    INNER JOIN citas c ON e.id_cita = c.id_cita
                    INNER JOIN registro_paciente p ON e.id_paciente = p.id_paciente
                    INNER JOIN especialidades esp ON c.id_especialidad = esp.id_especialidad
                    WHERE e.id_medico = :id_medico
                    ORDER BY e.created_at DESC
                    LIMIT :limit";
            
            $query = $this->acceso->prepare($sql);
            $query->bindValue(':id_medico', $id_medico, PDO::PARAM_INT);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en listarEvoluciones: " . $e->getMessage());
            return [];
        }
    }
}
?>
