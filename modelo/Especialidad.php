<?php
// modelo/Especialidad.php
include_once 'Conexion.php';

class Especialidad {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    // ==================== CONSULTAS PRINCIPALES ====================
    
  
    function listar($busqueda = '', $estado = 'todas') {
        try {
            $sql = "SELECT e.*, 
                    (SELECT COUNT(*) FROM especialidad_medicos em WHERE em.id_especialidad = e.id_especialidad AND em.activo = 1) as total_medicos,
                    (SELECT COUNT(*) FROM citas c WHERE c.id_especialidad = e.id_especialidad AND c.estado IN ('completada', 'cancelada')) as citas_totales,
                    (SELECT COUNT(*) FROM citas c WHERE c.id_especialidad = e.id_especialidad AND c.estado = 'pendiente') as citas_pendientes
                    FROM especialidades e 
                    WHERE 1=1";
            
            if($estado !== 'todas') {
                $sql .= " AND e.activo = " . ($estado === 'activas' ? 1 : 0);
            } else {
                $sql .= " AND e.activo = 1";
            }
            
            if(!empty($busqueda)) {
                $sql .= " AND (e.nombre LIKE :busqueda OR e.descripcion LIKE :busqueda OR e.codigo LIKE :busqueda)";
                $query = $this->acceso->prepare($sql);
                $query->execute(array(':busqueda' => "%$busqueda%"));
            } else {
                $query = $this->acceso->prepare($sql);
                $query->execute();
            }
            
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        } catch(PDOException $e) {
            error_log("Error en listar especialidades: " . $e->getMessage());
            return array();
        }
    }
    
   
    function obtener($id_especialidad) {
        try {
            $sql = "SELECT e.*,
                    (SELECT COUNT(*) FROM especialidad_medicos em WHERE em.id_especialidad = e.id_especialidad AND em.activo = 1) as total_medicos,
                    (SELECT COUNT(*) FROM citas c WHERE c.id_especialidad = e.id_especialidad AND c.estado IN ('completada', 'cancelada')) as citas_totales,
                    (SELECT COUNT(*) FROM citas c WHERE c.id_especialidad = e.id_especialidad AND c.estado = 'pendiente') as citas_pendientes
                    FROM especialidades e 
                    WHERE e.id_especialidad = :id AND e.activo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_especialidad));
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        } catch(PDOException $e) {
            return array();
        }
    }
    
   
    function totalActivos() {
        try {
            $sql = "SELECT COUNT(*) as total FROM especialidades WHERE activo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $resultado = $query->fetch();
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            return 0;
        }
    }
    
   
    function totalMedicosAsignados() {
        try {
            $sql = "SELECT COUNT(DISTINCT id_medico) as total FROM especialidad_medicos WHERE activo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $resultado = $query->fetch();
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            return 0;
        }
    }
    
   
    function totalCitasMes() {
        try {
            $sql = "SELECT COUNT(*) as total FROM citas WHERE MONTH(fecha_cita) = MONTH(CURDATE()) AND YEAR(fecha_cita) = YEAR(CURDATE())";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $resultado = $query->fetch();
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            return 0;
        }
    }
    
    // ==================== CRUD ====================
    
    
    function crear($nombre, $descripcion, $codigo, $duracion_defecto, $color, $prioridad, $orden_visualizacion, $requisitos, $observaciones) {
        try {
            $sql = "INSERT INTO especialidades(
                        nombre, descripcion, codigo, duracion_defecto, 
                        color, prioridad, orden_visualizacion, requisitos, observaciones
                    ) VALUES (
                        :nombre, :descripcion, :codigo, :duracion_defecto,
                        :color, :prioridad, :orden_visualizacion, :requisitos, :observaciones
                    )";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':codigo' => $codigo,
                ':duracion_defecto' => $duracion_defecto,
                ':color' => $color,
                ':prioridad' => $prioridad,
                ':orden_visualizacion' => $orden_visualizacion,
                ':requisitos' => $requisitos,
                ':observaciones' => $observaciones
            ));
            
            if($resultado) {
                echo 'creado';
            } else {
                echo 'error';
            }
        } catch(PDOException $e) {
            error_log("Error en crear especialidad: " . $e->getMessage());
            echo 'error_bd';
        }
    }
   
    function editar($id_especialidad, $nombre, $descripcion, $codigo, $duracion_defecto, $color, $prioridad, $orden_visualizacion, $requisitos, $observaciones, $activo) {
        try {
            $sql = "UPDATE especialidades SET 
                    nombre = :nombre,
                    descripcion = :descripcion,
                    codigo = :codigo,
                    duracion_defecto = :duracion_defecto,
                    color = :color,
                    prioridad = :prioridad,
                    orden_visualizacion = :orden_visualizacion,
                    requisitos = :requisitos,
                    observaciones = :observaciones,
                    activo = :activo
                    WHERE id_especialidad = :id";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':id' => $id_especialidad,
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':codigo' => $codigo,
                ':duracion_defecto' => $duracion_defecto,
                ':color' => $color,
                ':prioridad' => $prioridad,
                ':orden_visualizacion' => $orden_visualizacion,
                ':requisitos' => $requisitos,
                ':observaciones' => $observaciones,
                ':activo' => $activo
            ));
            
            if($resultado) {
                echo 'editado';
            } else {
                echo 'error';
            }
        } catch(PDOException $e) {
            error_log("Error en editar especialidad: " . $e->getMessage());
            echo 'error_bd';
        }
    }
    
    
    function eliminar($id_especialidad) {
        try {
            $sql = "UPDATE especialidades SET activo = 0 WHERE id_especialidad = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_especialidad));
            echo 'eliminado';
        } catch(PDOException $e) {
            echo 'error';
        }
    }
    
    // ==================== MÉDICOS ====================
    
    function obtenerMedicos($id_especialidad) {
        try {
            $sql = "SELECT em.*, rm.nombre_medico, rm.apellido_medico, rm.mpps_registro
                    FROM especialidad_medicos em
                    INNER JOIN registro_medico rm ON em.id_medico = rm.id_medico
                    WHERE em.id_especialidad = :id AND em.activo = 1
                    ORDER BY em.exp_anios DESC";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_especialidad));
            return $query->fetchAll();
        } catch(PDOException $e) {
            return array();
        }
    }
    
    function listarMedicosDisponibles($id_especialidad = null) {
        try {
            $sql = "SELECT rm.id_medico, rm.nombre_medico, rm.apellido_medico, rm.cedula_medico, rm.mpps_registro
                    FROM registro_medico rm
                    WHERE rm.medico_tipo = 2 AND rm.activo = 1";
            
            if($id_especialidad) {
                $sql .= " AND rm.id_medico NOT IN (
                            SELECT id_medico FROM especialidad_medicos 
                            WHERE id_especialidad = :id_especialidad AND activo = 1
                         )";
                $query = $this->acceso->prepare($sql);
                $query->execute(array(':id_especialidad' => $id_especialidad));
            } else {
                $query = $this->acceso->prepare($sql);
                $query->execute();
            }
            
            return $query->fetchAll();
        } catch(PDOException $e) {
            return array();
        }
    }
    
  
    function asignarMedico($id_especialidad, $id_medico, $tarifa, $exp_anios, $domicilio, $extra) {
        try {
            // Verificar si ya existe
            $sql_check = "SELECT id FROM especialidad_medicos 
                          WHERE id_especialidad = :id_especialidad AND id_medico = :id_medico AND activo = 1";
            $query_check = $this->acceso->prepare($sql_check);
            $query_check->execute(array(':id_especialidad' => $id_especialidad, ':id_medico' => $id_medico));
            
            if($query_check->rowCount() > 0) {
                echo 'ya_asignado';
                return;
            }
            
            $sql = "INSERT INTO especialidad_medicos(
                        id_especialidad, id_medico, tarifa, exp_anios, domicilio, extra, fecha_asignacion
                    ) VALUES (
                        :id_especialidad, :id_medico, :tarifa, :exp_anios, :domicilio, :extra, CURDATE()
                    )";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(
                ':id_especialidad' => $id_especialidad,
                ':id_medico' => $id_medico,
                ':tarifa' => $tarifa,
                ':exp_anios' => $exp_anios,
                ':domicilio' => $domicilio,
                ':extra' => $extra
            ));
            echo 'asignado';
        } catch(PDOException $e) {
            echo 'error';
        }
    }
    
   
    function removerMedico($id_asignacion) {
        try {
            $sql = "UPDATE especialidad_medicos SET activo = 0 WHERE id = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_asignacion));
            echo 'removido';
        } catch(PDOException $e) {
            echo 'error';
        }
    }
}
?>
