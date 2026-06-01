<?php
include_once 'Conexion.php';

class Factura {
    private $acceso;
    private $objetos;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
        $this->objetos = array();
    }
    
    /**
     * Generar el siguiente número de factura
     */
    public function generarNumeroFactura() {
        $sql = "SELECT MAX(CAST(SUBSTRING(numero_factura, 6) AS UNSIGNED)) as max_num 
                FROM facturas 
                WHERE numero_factura LIKE 'FACT-%'";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        $resultado = $query->fetch(PDO::FETCH_OBJ);
        
        $siguiente = ($resultado->max_num ?? 0) + 1;
        return 'FACT-' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Crear una nueva factura
     */
    public function crear($datos) {
        try {
            $numero_factura = $this->generarNumeroFactura();
            
            $sql = "INSERT INTO facturas (
                numero_factura, id_cita, id_paciente, id_medico, 
                fecha_cita, subtotal, iva, total, estado, 
                forma_pago, observaciones, creado_por
            ) VALUES (
                :numero_factura, :id_cita, :id_paciente, :id_medico,
                :fecha_cita, :subtotal, :iva, :total, :estado,
                :forma_pago, :observaciones, :creado_por
            )";
            
            $query = $this->acceso->prepare($sql);
            $query->execute([
                ':numero_factura' => $numero_factura,
                ':id_cita' => $datos['id_cita'],
                ':id_paciente' => $datos['id_paciente'],
                ':id_medico' => $datos['id_medico'],
                ':fecha_cita' => $datos['fecha_cita'],
                ':subtotal' => $datos['subtotal'],
                ':iva' => $datos['iva'],
                ':total' => $datos['total'],
                ':estado' => 'pendiente',
                ':forma_pago' => $datos['forma_pago'] ?? 'pago_movil',
                ':observaciones' => $datos['observaciones'] ?? null,
                ':creado_por' => $datos['creado_por']
            ]);
            
            $id_factura = $this->acceso->lastInsertId();
            
            // Agregar detalles de la factura
            if (isset($datos['detalles']) && is_array($datos['detalles'])) {
                foreach ($datos['detalles'] as $detalle) {
                    $this->agregarDetalle($id_factura, $detalle);
                }
            }
            
            // Registrar en auditoría
            $this->registrarAuditoria($id_factura, 'crear', null, null, null, $datos['creado_por'], $datos['rol_usuario']);
            
            return ['success' => true, 'id_factura' => $id_factura, 'numero_factura' => $numero_factura];
            
        } catch(PDOException $e) {
            error_log("Error en Factura::crear: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_db'];
        }
    }
    
    /**
     * Agregar detalle a una factura
     */
    private function agregarDetalle($id_factura, $detalle) {
        $sql = "INSERT INTO factura_detalles (
            id_factura, concepto, descripcion, cantidad, precio_unitario, subtotal
        ) VALUES (
            :id_factura, :concepto, :descripcion, :cantidad, :precio_unitario, :subtotal
        )";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([
            ':id_factura' => $id_factura,
            ':concepto' => $detalle['concepto'],
            ':descripcion' => $detalle['descripcion'] ?? null,
            ':cantidad' => $detalle['cantidad'],
            ':precio_unitario' => $detalle['precio_unitario'],
            ':subtotal' => $detalle['subtotal']
        ]);
    }
    
    /**
     * Obtener factura por ID
     */
    public function obtenerPorId($id_factura) {
        $sql = "SELECT f.*, 
                p.nombre_paciente, p.apellido_paciente, p.cedula_paciente,
                m.nombre_medico, m.apellido_medico, m.especialidad,
                c.fecha as fecha_cita, c.hora as hora_cita
                FROM facturas f
                LEFT JOIN registro_paciente p ON f.id_paciente = p.id_paciente
                LEFT JOIN registro_medico m ON f.id_medico = m.id_medico
                LEFT JOIN citas c ON f.id_cita = c.id_cita
                WHERE f.id_factura = :id_factura";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_factura' => $id_factura]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Obtener detalles de una factura
     */
    public function obtenerDetalles($id_factura) {
        $sql = "SELECT * FROM factura_detalles WHERE id_factura = :id_factura";
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_factura' => $id_factura]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Listar facturas por paciente
     */
    public function listarPorPaciente($id_paciente) {
        $sql = "SELECT f.*, 
                p.nombre_paciente, p.apellido_paciente,
                m.nombre_medico, m.apellido_medico
                FROM facturas f
                LEFT JOIN registro_paciente p ON f.id_paciente = p.id_paciente
                LEFT JOIN registro_medico m ON f.id_medico = m.id_medico
                WHERE f.id_paciente = :id_paciente
                ORDER BY f.fecha_emision DESC";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_paciente' => $id_paciente]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Listar facturas por médico
     */
    public function listarPorMedico($id_medico) {
        $sql = "SELECT f.*, 
                p.nombre_paciente, p.apellido_paciente,
                m.nombre_medico, m.apellido_medico
                FROM facturas f
                LEFT JOIN registro_paciente p ON f.id_paciente = p.id_paciente
                LEFT JOIN registro_medico m ON f.id_medico = m.id_medico
                WHERE f.id_medico = :id_medico
                ORDER BY f.fecha_emision DESC";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_medico' => $id_medico]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Listar todas las facturas (para asistente y administrador)
     */
    public function listarTodas($filtros = []) {
        $sql = "SELECT f.*, 
                p.nombre_paciente, p.apellido_paciente, p.cedula_paciente,
                m.nombre_medico, m.apellido_medico
                FROM facturas f
                LEFT JOIN registro_paciente p ON f.id_paciente = p.id_paciente
                LEFT JOIN registro_medico m ON f.id_medico = m.id_medico
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['numero'])) {
            $sql .= " AND f.numero_factura LIKE :numero";
            $params[':numero'] = '%' . $filtros['numero'] . '%';
        }
        
        if (!empty($filtros['paciente'])) {
            $sql .= " AND (p.nombre_paciente LIKE :paciente OR p.apellido_paciente LIKE :paciente)";
            $params[':paciente'] = '%' . $filtros['paciente'] . '%';
        }
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND f.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND f.fecha_emision >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND f.fecha_emision <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['fecha_hasta'];
        }
        
        $sql .= " ORDER BY f.fecha_emision DESC";
        
        $query = $this->acceso->prepare($sql);
        $query->execute($params);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Actualizar factura
     */
    public function actualizar($id_factura, $datos, $usuario_id, $rol_usuario) {
        try {
            $campos = [];
            $params = [':id_factura' => $id_factura];
            
            if (isset($datos['subtotal'])) {
                $campos[] = "subtotal = :subtotal";
                $params[':subtotal'] = $datos['subtotal'];
            }
            
            if (isset($datos['iva'])) {
                $campos[] = "iva = :iva";
                $params[':iva'] = $datos['iva'];
            }
            
            if (isset($datos['total'])) {
                $campos[] = "total = :total";
                $params[':total'] = $datos['total'];
            }
            
            if (isset($datos['observaciones'])) {
                $campos[] = "observaciones = :observaciones";
                $params[':observaciones'] = $datos['observaciones'];
            }
            
            if (isset($datos['forma_pago'])) {
                $campos[] = "forma_pago = :forma_pago";
                $params[':forma_pago'] = $datos['forma_pago'];
            }
            
            if (!empty($campos)) {
                $campos[] = "fecha_modificacion = NOW()";
                $campos[] = "modificado_por = :modificado_por";
                $params[':modificado_por'] = $usuario_id;
                
                $sql = "UPDATE facturas SET " . implode(', ', $campos) . " WHERE id_factura = :id_factura";
                $query = $this->acceso->prepare($sql);
                $query->execute($params);
                
                // Registrar en auditoría
                foreach ($campos as $campo) {
                    if (strpos($campo, '=') !== false) {
                        $nombre_campo = explode('=', trim($campo))[0];
                        $this->registrarAuditoria($id_factura, 'editar', $nombre_campo, null, null, $usuario_id, $rol_usuario);
                    }
                }
            }
            
            // Actualizar detalles si se proporcionan
            if (isset($datos['detalles']) && is_array($datos['detalles'])) {
                // Eliminar detalles existentes
                $sql_delete = "DELETE FROM factura_detalles WHERE id_factura = :id_factura";
                $query_delete = $this->acceso->prepare($sql_delete);
                $query_delete->execute([':id_factura' => $id_factura]);
                
                // Agregar nuevos detalles
                foreach ($datos['detalles'] as $detalle) {
                    $this->agregarDetalle($id_factura, $detalle);
                }
            }
            
            return ['success' => true];
            
        } catch(PDOException $e) {
            error_log("Error en Factura::actualizar: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_db'];
        }
    }
    
    /**
     * Marcar factura como pagada
     */
    public function marcarPagada($id_factura, $referencia_pago, $usuario_id, $rol_usuario) {
        try {
            $sql = "UPDATE facturas 
                    SET estado = 'pagada', 
                        referencia_pago = :referencia_pago,
                        fecha_modificacion = NOW(),
                        modificado_por = :modificado_por
                    WHERE id_factura = :id_factura";
            
            $query = $this->acceso->prepare($sql);
            $query->execute([
                ':referencia_pago' => $referencia_pago,
                ':modificado_por' => $usuario_id,
                ':id_factura' => $id_factura
            ]);
            
            // Registrar en auditoría
            $this->registrarAuditoria($id_factura, 'pagar', 'estado', 'pendiente', 'pagada', $usuario_id, $rol_usuario);
            $this->registrarAuditoria($id_factura, 'pagar', 'referencia_pago', null, $referencia_pago, $usuario_id, $rol_usuario);
            
            return ['success' => true];
            
        } catch(PDOException $e) {
            error_log("Error en Factura::marcarPagada: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_db'];
        }
    }
    
    /**
     * Cancelar factura
     */
    public function cancelar($id_factura, $usuario_id, $rol_usuario) {
        try {
            $sql = "UPDATE facturas 
                    SET estado = 'cancelada',
                        fecha_modificacion = NOW(),
                        modificado_por = :modificado_por
                    WHERE id_factura = :id_factura";
            
            $query = $this->acceso->prepare($sql);
            $query->execute([
                ':modificado_por' => $usuario_id,
                ':id_factura' => $id_factura
            ]);
            
            // Registrar en auditoría
            $this->registrarAuditoria($id_factura, 'cancelar', 'estado', null, 'cancelada', $usuario_id, $rol_usuario);
            
            return ['success' => true];
            
        } catch(PDOException $e) {
            error_log("Error en Factura::cancelar: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_db'];
        }
    }
    
    /**
     * Eliminar factura
     */
    public function eliminar($id_factura, $usuario_id, $rol_usuario) {
        try {
            $sql = "DELETE FROM facturas WHERE id_factura = :id_factura";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_factura' => $id_factura]);
            
            // Registrar en auditoría
            $this->registrarAuditoria($id_factura, 'eliminar', null, null, null, $usuario_id, $rol_usuario);
            
            return ['success' => true];
            
        } catch(PDOException $e) {
            error_log("Error en Factura::eliminar: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_db'];
        }
    }
    
    /**
     * Registrar auditoría
     */
    private function registrarAuditoria($id_factura, $accion, $campo_modificado, $valor_anterior, $valor_nuevo, $usuario_id, $rol_usuario) {
        $sql = "INSERT INTO factura_auditoria (
            id_factura, accion, campo_modificado, valor_anterior, valor_nuevo,
            realizado_por, rol_usuario, ip_address
        ) VALUES (
            :id_factura, :accion, :campo_modificado, :valor_anterior, :valor_nuevo,
            :realizado_por, :rol_usuario, :ip_address
        )";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([
            ':id_factura' => $id_factura,
            ':accion' => $accion,
            ':campo_modificado' => $campo_modificado,
            ':valor_anterior' => $valor_anterior,
            ':valor_nuevo' => $valor_nuevo,
            ':realizado_por' => $usuario_id,
            ':rol_usuario' => $rol_usuario,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }
    
    /**
     * Obtener auditoría de una factura
     */
    public function obtenerAuditoria($id_factura) {
        $sql = "SELECT fa.*, 
                CASE fa.rol_usuario
                    WHEN 'paciente' THEN p.nombre_paciente
                    WHEN 'medico' THEN m.nombre_medico
                    WHEN 'asistente' THEN a.nombre_asistente
                    WHEN 'administrador' THEN adm.nombre_administrador
                END as nombre_usuario
                FROM factura_auditoria fa
                LEFT JOIN registro_paciente p ON fa.realizado_por = p.id_paciente AND fa.rol_usuario = 'paciente'
                LEFT JOIN registro_medico m ON fa.realizado_por = m.id_medico AND fa.rol_usuario = 'medico'
                LEFT JOIN registro_asistente a ON fa.realizado_por = a.id_asistente AND fa.rol_usuario = 'asistente'
                LEFT JOIN registro_administrador adm ON fa.realizado_por = adm.id_administrador AND fa.rol_usuario = 'administrador'
                WHERE fa.id_factura = :id_factura
                ORDER BY fa.fecha_auditoria DESC";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_factura' => $id_factura]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Obtener configuración de pago móvil
     */
    public function obtenerConfigPagoMovil() {
        $sql = "SELECT * FROM pago_movil_config WHERE activo = 1 LIMIT 1";
        $query = $this->acceso->prepare($sql);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }
    
    /**
     * Obtener historial de pagos por paciente
     */
    public function historialPagosPaciente($id_paciente) {
        $sql = "SELECT f.*, 
                p.nombre_paciente, p.apellido_paciente,
                m.nombre_medico, m.apellido_medico
                FROM facturas f
                LEFT JOIN registro_paciente p ON f.id_paciente = p.id_paciente
                LEFT JOIN registro_medico m ON f.id_medico = m.id_medico
                WHERE f.id_paciente = :id_paciente 
                AND f.estado = 'pagada'
                ORDER BY f.fecha_modificacion DESC";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_paciente' => $id_paciente]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Buscar factura por ID de cita
     */
    public function buscarPorCita($id_cita) {
        $sql = "SELECT f.*, 
                p.nombre_paciente, p.apellido_paciente,
                m.nombre_medico, m.apellido_medico
                FROM facturas f
                LEFT JOIN registro_paciente p ON f.id_paciente = p.id_paciente
                LEFT JOIN registro_medico m ON f.id_medico = m.id_medico
                WHERE f.id_cita = :id_cita";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_cita' => $id_cita]);
        return $query->fetch(PDO::FETCH_OBJ);
    }
}
?>
