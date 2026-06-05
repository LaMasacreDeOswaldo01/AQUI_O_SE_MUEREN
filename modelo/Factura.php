<?php
<<<<<<< HEAD
// modelo/Factura.php
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
include_once 'Conexion.php';

class Factura {
    private $acceso;
<<<<<<< HEAD
=======
    private $objetos;
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
<<<<<<< HEAD
    }
    
    /**
     * Genera un número de factura secuencial (ej: FAC-00001)
     */
    private function generarNumeroFactura() {
        try {
            $sql = "SELECT MAX(id_factura) as max_id FROM facturas";
            $query = $this->acceso->query($sql);
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            $next_id = ($resultado->max_id ?? 0) + 1;
            return 'FAC-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            error_log("Error generando número de factura: " . $e->getMessage());
            return 'FAC-' . uniqid();
        }
    }
    
    /**
     * Registra una nueva factura con sus detalles en una transacción
     */
    public function crear($id_paciente, $id_asistente, $subtotal, $iva, $descuento, $total, $metodo_pago, $estado_pago, $notas, $items) {
        try {
            $this->acceso->beginTransaction();
            
            $numero_factura = $this->generarNumeroFactura();
            $fecha_emision = date('Y-m-d');
            
            $sql = "INSERT INTO facturas (
                        numero_factura, id_paciente, id_asistente, fecha_emision,
                        subtotal, iva, descuento, total, metodo_pago, estado_pago, notas
                    ) VALUES (
                        :numero_factura, :id_paciente, :id_asistente, :fecha_emision,
                        :subtotal, :iva, :descuento, :total, :metodo_pago, :estado_pago, :notas
                    )";
            
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute([
                ':numero_factura' => $numero_factura,
                ':id_paciente' => $id_paciente,
                ':id_asistente' => $id_asistente,
                ':fecha_emision' => $fecha_emision,
                ':subtotal' => $subtotal,
                ':iva' => $iva,
                ':descuento' => $descuento,
                ':total' => $total,
                ':metodo_pago' => $metodo_pago,
                ':estado_pago' => $estado_pago,
                ':notas' => $notas
            ]);
            
            if (!$resultado) {
                throw new Exception("Error al insertar cabecera de factura");
            }
            
            $id_factura = $this->acceso->lastInsertId();
            
            // Insertar detalles
            $sql_detalle = "INSERT INTO factura_detalles (id_factura, descripcion, cantidad, precio_unitario, subtotal) 
                            VALUES (:id_factura, :descripcion, :cantidad, :precio_unitario, :subtotal)";
            $query_detalle = $this->acceso->prepare($sql_detalle);
            
            foreach ($items as $item) {
                $item_subtotal = $item['cantidad'] * $item['precio_unitario'];
                $query_detalle->execute([
                    ':id_factura' => $id_factura,
                    ':descripcion' => $item['descripcion'],
                    ':cantidad' => $item['cantidad'],
                    ':precio_unitario' => $item['precio_unitario'],
                    ':subtotal' => $item_subtotal
                ]);
            }
            
            $this->acceso->commit();
            return ['success' => true, 'id_factura' => $id_factura, 'numero_factura' => $numero_factura];
            
        } catch (Exception $e) {
            $this->acceso->rollBack();
            error_log("Error al crear factura: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
=======
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
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
    
    /**
<<<<<<< HEAD
     * Edita una factura existente y sus detalles
     */
    public function editar($id_factura, $subtotal, $iva, $descuento, $total, $metodo_pago, $estado_pago, $notas, $items) {
        try {
            $this->acceso->beginTransaction();
            
            // Actualizar cabecera
            $sql = "UPDATE facturas SET 
                        subtotal = :subtotal,
                        iva = :iva,
                        descuento = :descuento,
                        total = :total,
                        metodo_pago = :metodo_pago,
                        estado_pago = :estado_pago,
                        notas = :notas
=======
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
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    WHERE id_factura = :id_factura";
            
            $query = $this->acceso->prepare($sql);
            $query->execute([
<<<<<<< HEAD
                ':id_factura' => $id_factura,
                ':subtotal' => $subtotal,
                ':iva' => $iva,
                ':descuento' => $descuento,
                ':total' => $total,
                ':metodo_pago' => $metodo_pago,
                ':estado_pago' => $estado_pago,
                ':notas' => $notas
            ]);
            
            // Eliminar detalles anteriores
            $sql_del = "DELETE FROM factura_detalles WHERE id_factura = :id_factura";
            $query_del = $this->acceso->prepare($sql_del);
            $query_del->execute([':id_factura' => $id_factura]);
            
            // Insertar nuevos detalles
            $sql_detalle = "INSERT INTO factura_detalles (id_factura, descripcion, cantidad, precio_unitario, subtotal) 
                            VALUES (:id_factura, :descripcion, :cantidad, :precio_unitario, :subtotal)";
            $query_detalle = $this->acceso->prepare($sql_detalle);
            
            foreach ($items as $item) {
                $item_subtotal = $item['cantidad'] * $item['precio_unitario'];
                $query_detalle->execute([
                    ':id_factura' => $id_factura,
                    ':descripcion' => $item['descripcion'],
                    ':cantidad' => $item['cantidad'],
                    ':precio_unitario' => $item['precio_unitario'],
                    ':subtotal' => $item_subtotal
                ]);
            }
            
            $this->acceso->commit();
            return ['success' => true];
            
        } catch (Exception $e) {
            $this->acceso->rollBack();
            error_log("Error al editar factura: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
=======
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
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
    
    /**
<<<<<<< HEAD
     * Anula una factura lógicamente (cambia estado de pago a 'Anulado')
     */
    public function anular($id_factura) {
        try {
            $sql = "UPDATE facturas SET estado_pago = 'Anulado' WHERE id_factura = :id_factura";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute([':id_factura' => $id_factura]);
            return $resultado ? ['success' => true] : ['success' => false, 'message' => 'No se pudo actualizar la factura'];
        } catch (PDOException $e) {
            error_log("Error al anular factura: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
=======
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
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
    
    /**
<<<<<<< HEAD
     * Obtiene una factura por su ID junto con la información del paciente
     */
    public function obtener($id_factura) {
        try {
            $sql = "SELECT f.*, 
                           rp.nombre_paciente, rp.apellido_paciente, rp.cedula_paciente, rp.correo_paciente, rp.telefono_paciente, rp.direccion_paciente,
                           ra.nombre_asistente, ra.apellido_asistente
                    FROM facturas f
                    INNER JOIN registro_paciente rp ON f.id_paciente = rp.id_paciente
                    LEFT JOIN registro_asistente ra ON f.id_asistente = ra.id_asistente
                    WHERE f.id_factura = :id_factura";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_factura' => $id_factura]);
            return $query->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener factura: " . $e->getMessage());
            return null;
=======
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
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
    
    /**
<<<<<<< HEAD
     * Obtiene los detalles de una factura
     */
    public function obtenerDetalles($id_factura) {
        try {
            $sql = "SELECT * FROM factura_detalles WHERE id_factura = :id_factura";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_factura' => $id_factura]);
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error al obtener detalles de factura: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lista facturas con filtros aplicados (búsqueda, rango de fechas, id_paciente)
     */
    public function listar($busqueda = '', $fecha_inicio = '', $fecha_fin = '', $id_paciente = null, $estado = 'todos') {
        try {
            $sql = "SELECT f.*, 
                           CONCAT(rp.nombre_paciente, ' ', rp.apellido_paciente) as paciente_nombre, 
                           rp.cedula_paciente
                    FROM facturas f
                    INNER JOIN registro_paciente rp ON f.id_paciente = rp.id_paciente
                    WHERE 1=1";
            
            $params = [];
            
            if ($id_paciente !== null) {
                $sql .= " AND f.id_paciente = :id_paciente";
                $params[':id_paciente'] = $id_paciente;
            }
            
            if ($estado !== 'todos') {
                $sql .= " AND f.estado_pago = :estado";
                $params[':estado'] = $estado;
            }
            
            if (!empty($fecha_inicio)) {
                $sql .= " AND f.fecha_emision >= :fecha_inicio";
                $params[':fecha_inicio'] = $fecha_inicio;
            }
            
            if (!empty($fecha_fin)) {
                $sql .= " AND f.fecha_emision <= :fecha_fin";
                $params[':fecha_fin'] = $fecha_fin;
            }
            
            if (!empty($busqueda)) {
                $sql .= " AND (f.numero_factura LIKE :busqueda OR rp.nombre_paciente LIKE :busqueda OR rp.apellido_paciente LIKE :busqueda OR rp.cedula_paciente LIKE :busqueda)";
                $params[':busqueda'] = "%$busqueda%";
            }
            
            $sql .= " ORDER BY f.fecha_emision DESC, f.id_factura DESC";
            
            $query = $this->acceso->prepare($sql);
            $query->execute($params);
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error en listar facturas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene estadísticas financieras generales para el Administrador
     */
    public function obtenerEstadisticas() {
        try {
            $stats = [];
            
            // Total ingresado en el mes actual (excluyendo facturas anuladas)
            $sql = "SELECT SUM(total) as total_mes FROM facturas 
                    WHERE MONTH(fecha_emision) = MONTH(CURDATE()) 
                      AND YEAR(fecha_emision) = YEAR(CURDATE())
                      AND estado_pago = 'Pagado'";
            $query = $this->acceso->query($sql);
            $stats['ingresos_mes'] = $query->fetch(PDO::FETCH_OBJ)->total_mes ?? 0.00;
            
            // Cantidad de facturas emitidas en el mes actual
            $sql = "SELECT COUNT(*) as emitidas FROM facturas 
                    WHERE MONTH(fecha_emision) = MONTH(CURDATE()) 
                      AND YEAR(fecha_emision) = YEAR(CURDATE())";
            $query = $this->acceso->query($sql);
            $stats['facturas_mes'] = $query->fetch(PDO::FETCH_OBJ)->emitidas ?? 0;
            
            // Cantidad de facturas pendientes de pago
            $sql = "SELECT COUNT(*) as pendientes FROM facturas 
                    WHERE estado_pago = 'Pendiente'";
            $query = $this->acceso->query($sql);
            $stats['facturas_pendientes'] = $query->fetch(PDO::FETCH_OBJ)->pendientes ?? 0;
            
            // Desglose por método de pago (excluyendo anulados)
            $sql = "SELECT metodo_pago, SUM(total) as total, COUNT(*) as cantidad 
                    FROM facturas 
                    WHERE estado_pago = 'Pagado'
                    GROUP BY metodo_pago";
            $query = $this->acceso->query($sql);
            $stats['por_metodo'] = $query->fetchAll(PDO::FETCH_OBJ);
            
            // Ingresos mensuales históricos (últimos 6 meses)
            $sql = "SELECT DATE_FORMAT(fecha_emision, '%Y-%m') as mes, SUM(total) as total
                    FROM facturas
                    WHERE estado_pago = 'Pagado'
                      AND fecha_emision >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                    GROUP BY DATE_FORMAT(fecha_emision, '%Y-%m')
                    ORDER BY mes ASC";
            $query = $this->acceso->query($sql);
            $stats['historico'] = $query->fetchAll(PDO::FETCH_OBJ);
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error obteniendo estadísticas de facturación: " . $e->getMessage());
            return [
                'ingresos_mes' => 0.00,
                'facturas_mes' => 0,
                'facturas_pendientes' => 0,
                'por_metodo' => [],
                'historico' => []
            ];
        }
=======
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
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    }
}
?>
