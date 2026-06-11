<?php
include_once 'Conexion.php';

class Alerta {
    private $acceso;

    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }

    public function listar() {
        try {
            $sql = "SELECT * FROM alertas ORDER BY fecha_registro DESC";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en Alerta::listar: " . $e->getMessage());
            return [];
        }
    }

    public function registrar($datos) {
        try {
            $sql = "INSERT INTO alertas (tipo_amenaza, nombre_paciente, cedula_paciente, nivel_riesgo, descripcion_breve, id_medico) 
                    VALUES (:tipo, :nombre, :cedula, :riesgo, :descripcion, :id_medico)";
            $query = $this->acceso->prepare($sql);
            return $query->execute([
                ':tipo' => $datos['tipo_amenaza'],
                ':nombre' => $datos['nombre_paciente'],
                ':cedula' => $datos['cedula_paciente'],
                ':riesgo' => $datos['nivel_riesgo'],
                ':descripcion' => $datos['descripcion_breve'],
                ':id_medico' => $datos['id_medico']
            ]);
        } catch (PDOException $e) {
            error_log("Error en Alerta::registrar: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id) {
        try {
            $sql = "DELETE FROM alertas WHERE id_alerta = :id";
            $query = $this->acceso->prepare($sql);
            return $query->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error en Alerta::eliminar: " . $e->getMessage());
            return false;
        }
    }
}