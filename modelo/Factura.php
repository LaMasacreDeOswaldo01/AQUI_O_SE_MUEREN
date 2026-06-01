<?php
// modelo/Factura.php

include_once 'Conexion.php';

class Factura {
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    /**
     * Genera el siguiente número de factura
     * @return string Número de factura en formato FACT-XXXX
     */
    public function generarNumeroFactura() {
        try {
            $sql = "SELECT MAX(CAST(SUBSTRING(numero_factura, 6) AS UNSIGNED)) as max_num 
                    FROM facturas 
                    WHERE numero_factura LIKE 'FACT-%'";
            $stmt = $this->acceso->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $next_num = ($result['max_num'] ?? 0) + 1;
            return 'FACT-' . str_pad($next_num, 4, '0', STR_PAD_LEFT);
        } catch(PDOException $e) {
            error_log("Error en generarNumeroFactura: " . $e->getMessage());
            return 'FACT-0001';
        }
    }
    
    /**
     * Crea una nueva factura desde una cita
     * @param array $datos Datos de la factura
     * @return array Resultado de la operación
     */
    public function crear($datos) {
        try {
            $numero_factura = $this->generarNumeroFactura();
            
            $sql = "INSERT INTO facturas (
                        numero_factura, id_cita, id_paciente, id_medico,
                        fecha_emision, estado, subtotal, iva, total,
                        datos_clinica, datos_beneficiario, detalles_factura
                    ) VALUES (
                        :numero_factura, :id_cita, :id_paciente, :id_medico,
                        :fecha_emision, :estado, :subtotal, :iva, :total,
                        :datos_clinica, :datos_beneficiario, :detalles_factura
                    )";
            
            $stmt = $this->acceso->prepare($sql);
            $resultado = $stmt->execute([
                ':numero_factura' => $numero_factura,
                ':id_cita' => $datos['id_cita'],
                ':id_paciente' => $datos['id_paciente'],
                ':id_medico' => $datos['id_medico'],
                ':fecha_emision' => date('Y-m-d H:i:s'),
                ':estado' => 'pendiente',
                ':subtotal' => $datos['subtotal'],
                ':iva' => $datos['iva'],
                ':total' => $datos['total'],
                ':datos_clinica' => json_encode($datos['datos_clinica']),
                ':datos_beneficiario' => json_encode($datos['datos_beneficiario']),
                ':detalles_factura' => json_encode($datos['detalles_factura'])
            ]);
            
            if ($resultado) {
                return [
                    'success' => true,
                    'id' => $this->acceso->lastInsertId(),
                    'numero_factura' => $numero_factura
                ];
            } else {
                return ['success' => false, 'message' => 'error_insert'];
            }
        } catch(PDOException $e) {
            error_log("Error en crear factura: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_db'];
        }
    }
    
    /**
     * Obtiene una factura por ID
     * @param int $id_factura ID de la factura
     * @return object|null Datos de la factura
     */
    public function obtenerPorId($id_factura) {
        try {
            $sql = "SELECT f.*, 
                           c.fecha_cita, c.hora_cita, c.tipo_consulta, c.motivo,
                           CONCAT(rp.nombre_paciente, ' ', rp.apellido_paciente) as paciente_nombre,
                           rp.cedula_paciente as paciente_cedula,
                           CONCAT(rm.nombre_medico, ' ', rm.apellido_medico) as medico_nombre,
                           e.nombre as especialidad_nombre
                    FROM facturas f
                    INNER JOIN citas c ON f.id_cita = c.id_cita
                    INNER JOIN registro_paciente rp ON f.id_paciente = rp.id_paciente
                    INNER JOIN registro_medico rm ON f.id_medico = rm.id_medico
                    INNER JOIN especialidades e ON c.id_especialidad = e.id_especialidad
                    WHERE f.id_factura = :id_factura";
            
            $stmt = $this->acceso->prepare($sql);
            $stmt->bindParam(':id_factura', $id_factura);
            $stmt->execute();
            
            $factura = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($factura) {
                $factura->datos_clinica = json_decode($factura->datos_clinica, true);
                $factura->datos_beneficiario = json_decode($factura->datos_beneficiario, true);
                $factura->detalles_factura = json_decode($factura->detalles_factura, true);
            }
            
            return $factura;
        } catch(PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lista facturas según el rol del usuario
     * @param int $usuario_id ID del usuario
     * @param string $rol Rol del usuario
     * @param array $filtros Filtros opcionales
     * @return array Lista de facturas
     */
    public function listarPorRol($usuario_id, $rol, $filtros = []) {
        try {
            $sql = "SELECT f.*, 
                           CONCAT(rp.nombre_paciente, ' ', rp.apellido_paciente) as paciente_nombre,
                           CONCAT(rm.nombre_medico, ' ', rm.apellido_medico) as medico_nombre
                    FROM facturas f
                    INNER JOIN registro_paciente rp ON f.id_paciente = rp.id_paciente
                    INNER JOIN registro_medico rm ON f.id_medico = rm.id_medico";
            
            // Filtro por rol
            switch ($rol) {
                case 'paciente':
                    $sql .= " WHERE f.id_paciente = :usuario_id";
                    break;
                case 'medico':
                    $sql .= " WHERE f.id_medico = :usuario_id";
                    break;
                case 'asistente':
                case 'administrador':
                    // Sin filtro, ve todas
                    break;
            }
            
            // Filtros adicionales
            if (!empty($filtros['estado'])) {
                $sql .= ($rol === 'asistente' || $rol === 'administrador') ? " WHERE" : " AND";
                $sql .= " f.estado = :estado";
            }
            
            if (!empty($filtros['busqueda'])) {
                $sql .= ($rol === 'asistente' || $rol === 'administrador' && empty($filtros['estado'])) ? " WHERE" : " AND";
                $sql .= " (f.numero_factura LIKE :busqueda OR paciente_nombre LIKE :busqueda)";
            }
            
            $sql .= " ORDER BY f.fecha_emision DESC";
            
            $stmt = $this->acceso->prepare($sql);
            
            if ($rol === 'paciente' || $rol === 'medico') {
                $stmt->bindParam(':usuario_id', $usuario_id);
            }
            
            if (!empty($filtros['estado'])) {
                $stmt->bindParam(':estado', $filtros['estado']);
            }
            
            if (!empty($filtros['busqueda'])) {
                $busqueda = '%' . $filtros['busqueda'] . '%';
                $stmt->bindParam(':busqueda', $busqueda);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error en listarPorRol: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Confirma el pago de una factura
     * @param int $id_factura ID de la factura
     * @param string $referencia Referencia del pago
     * @param int $usuario_id ID del usuario que confirma
     * @return array Resultado de la operación
     */
    public function confirmarPago($id_factura, $referencia, $usuario_id) {
        try {
            $sql = "UPDATE facturas 
                    SET estado = 'pagada', 
                        referencia_pago = :referencia,
                        fecha_pago = NOW(),
                        metodo_pago = 'pago_movil',
                        modificado_por = :usuario_id,
                        fecha_modificacion = NOW()
                    WHERE id_factura = :id_factura AND estado = 'pendiente'";
            
            $stmt = $this->acceso->prepare($sql);
            $resultado = $stmt->execute([
                ':referencia' => $referencia,
                ':usuario_id' => $usuario_id,
                ':id_factura' => $id_factura
            ]);
            
            if ($resultado && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'pago_confirmado'];
            } else {
                return ['success' => false, 'message' => 'no_actualizado'];
            }
        } catch(PDOException $e) {
            error_log("Error en confirmarPago: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_db'];
        }
    }
    
    /**
     * Actualiza una factura
     * @param int $id_factura ID de la factura
     * @param array $datos Datos a actualizar
     * @param int $usuario_id ID del usuario que modifica
     * @return array Resultado de la operación
     */
    public function actualizar($id_factura, $datos, $usuario_id) {
        try {
            $sql = "UPDATE facturas 
                    SET subtotal = :subtotal,
                        iva = :iva,
                        total = :total,
                        detalles_factura = :detalles_factura,
                        modificado_por = :usuario_id,
                        fecha_modificacion = NOW()
                    WHERE id_factura = :id_factura";
            
            $stmt = $this->acceso->prepare($sql);
            $resultado = $stmt->execute([
                ':subtotal' => $datos['subtotal'],
                ':iva' => $datos['iva'],
                ':total' => $datos['total'],
                ':detalles_factura' => json_encode($datos['detalles_factura']),
                ':usuario_id' => $usuario_id,
                ':id_factura' => $id_factura
            ]);
            
            if ($resultado) {
                return ['success' => true, 'message' => 'actualizada'];
            } else {
                return ['success' => false, 'message' => 'no_actualizada'];
            }
        } catch(PDOException $e) {
            error_log("Error en actualizar: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_db'];
        }
    }
    
    /**
     * Elimina una factura (solo administrador)
     * @param int $id_factura ID de la factura
     * @return array Resultado de la operación
     */
    public function eliminar($id_factura) {
        try {
            $sql = "DELETE FROM facturas WHERE id_factura = :id_factura";
            $stmt = $this->acceso->prepare($sql);
            $resultado = $stmt->execute([':id_factura' => $id_factura]);
            
            if ($resultado) {
                return ['success' => true, 'message' => 'eliminada'];
            } else {
                return ['success' => false, 'message' => 'no_encontrada'];
            }
        } catch(PDOException $e) {
            error_log("Error en eliminar: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_db'];
        }
    }
    
    /**
     * Verifica si una factura existe para una cita
     * @param int $id_cita ID de la cita
     * @return bool True si existe factura
     */
    public function existeFacturaParaCita($id_cita) {
        try {
            $sql = "SELECT COUNT(*) as total FROM facturas WHERE id_cita = :id_cita";
            $stmt = $this->acceso->prepare($sql);
            $stmt->execute([':id_cita' => $id_cita]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] > 0;
        } catch(PDOException $e) {
            error_log("Error en existeFacturaParaCita: " . $e->getMessage());
            return false;
        }
    }
}
?>
