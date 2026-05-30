<?php
include_once 'Conexion.php';

class Medico {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    // ==================== MÉTODOS PRINCIPALES ====================   
    function obtener_datos($id) {
        try {
            $sql = "SELECT rm.*, tp.nombre_tipo 
                    FROM registro_medico rm
                    INNER JOIN tipo_paciente tp ON rm.medico_tipo = tp.id_tipo_us 
                    WHERE rm.id_medico = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        } catch(PDOException $e) {
            error_log("Error en obtener_datos: " . $e->getMessage());
            return array();
        }
    }    
   
    function obtenerDatosBasicos($id) {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, cedula_medico,
                           mpps_registro, telefono_medico, correo_medico, direccion_medico,
                           fecha_nacimiento_medico, sexo_medico, avatar_medico
                    FROM registro_medico 
                    WHERE id_medico = :id AND medico_tipo = 2";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            return $query->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error en obtenerDatosBasicos: " . $e->getMessage());
            return null;
        }
    }    
   
    function editar($id_medico, $telefono, $direccion, $correo, $sexo, $adicional, $mpps_registro = null) {
        try {
            $sql = "UPDATE registro_medico SET 
                    telefono_medico = :telefono,
                    direccion_medico = :direccion,
                    correo_medico = :correo,
                    sexo_medico = :sexo,
                    adicional_medico = :adicional";
            
            // Agregar mpps_registro solo si se proporciona
            if ($mpps_registro !== null) {
                $sql .= ", mpps_registro = :mpps_registro";
            }
            
            $sql .= " WHERE id_medico = :id";
            
            $params = array(
                ':id' => $id_medico,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':correo' => $correo,
                ':sexo' => $sexo,
                ':adicional' => $adicional
            );
            
            if ($mpps_registro !== null) {
                $params[':mpps_registro'] = $mpps_registro;
            }
            
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute($params);
            
            if ($resultado) {
                return ['success' => true, 'message' => 'editado'];
            } else {
                return ['success' => false, 'message' => 'error_actualizacion'];
            }
        } catch(PDOException $e) {
            error_log("Error en editar medico: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
    }    
   
    function cambiar_photo($id_medico, $nombre) {
        try {
            $sql = "SELECT avatar_medico FROM registro_medico WHERE id_medico = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_medico));
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            
            $avatar_anterior = $resultado ? $resultado->avatar_medico : 'avatarDES.jpg';
            
            $sql = "UPDATE registro_medico SET avatar_medico = :nombre WHERE id_medico = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_medico, ':nombre' => $nombre));
            
            return $avatar_anterior;
        } catch(PDOException $e) {
            error_log("Error en cambiar_photo: " . $e->getMessage());
            return 'avatarDES.jpg';
        }
    }    
  
    function crear($datos) {
        try {
            $nombre = $datos['nombre'] ?? '';
            $apellidos = $datos['apellidos'] ?? '';
            $fecha_nacimiento = $datos['fecha_nacimiento'] ?? '';
            $cedula = $datos['cedula'] ?? '';
            $mpps_registro = $datos['mpps_registro'] ?? '';
            $telefono = $datos['telefono'] ?? '';
            $direccion = $datos['direccion'] ?? '';
            $correo = $datos['correo'] ?? '';
            $sexo = $datos['sexo'] ?? '';
            $adicional = $datos['adicional'] ?? '';
            $password_hash = $datos['password_hash'] ?? '';
            $tipo = $datos['tipo'] ?? 2;
            $avatar = $datos['avatar'] ?? 'avatarDES.jpg';
            
            // Validar datos requeridos
            if (empty($nombre) || empty($apellidos) || empty($cedula) || empty($password_hash)) {
                return ['success' => false, 'message' => 'datos_incompletos'];
            }
            
            // Verificar si ya existe
            $sql = "SELECT id_medico FROM registro_medico WHERE cedula_medico = :cedula OR correo_medico = :correo";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula, ':correo' => $correo));
            $existe = $query->fetchAll();
            
            if(!empty($existe)) {
                return ['success' => false, 'message' => 'existe'];
            }
            
            // Insertar el nuevo médico
            $sql = "INSERT INTO registro_medico(
                nombre_medico, apellido_medico, fecha_nacimiento_medico, 
                cedula_medico, mpps_registro, telefono_medico, direccion_medico, 
                correo_medico, sexo_medico, adicional_medico, 
                avatar_medico, medico_tipo
            ) VALUES (
                :nombre, :apellidos, :fecha_nacimiento,
                :cedula, :mpps_registro, :telefono, :direccion,
                :correo, :sexo, :adicional,
                :avatar, :tipo
            )";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':nombre' => $nombre,
                ':apellidos' => $apellidos,
                ':fecha_nacimiento' => $fecha_nacimiento,
                ':cedula' => $cedula,
                ':mpps_registro' => $mpps_registro,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':correo' => $correo,
                ':sexo' => $sexo,
                ':adicional' => $adicional,
                ':avatar' => $avatar,
                ':tipo' => $tipo
            ));
            
            if($resultado) {
                $id_medico = $this->acceso->lastInsertId();
                $loginResult = $this->crearLogin($id_medico, $password_hash);
                
                if ($loginResult['success']) {
                    return ['success' => true, 'message' => 'add', 'id' => $id_medico];
                } else {
                    return ['success' => false, 'message' => 'error_login'];
                }
            } else {
                return ['success' => false, 'message' => 'error_bd'];
            }
        } catch(PDOException $e) {
            error_log("Error en crear medico: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_exception'];
        }
    }    
  
    function crearLogin($id_medico, $password_hash) {
        try {
            $sql = "INSERT INTO login_medico(id_medico, password_hash, status) 
                    VALUES (:id_medico, :password_hash, 'activo')";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':id_medico' => $id_medico,
                ':password_hash' => $password_hash
            ));
            
            if ($resultado) {
                return ['success' => true, 'message' => 'login_creado'];
            } else {
                return ['success' => false, 'message' => 'error_login'];
            }
        } catch(PDOException $e) {
            error_log("Error en crearLogin: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
    }
    
    // ==================== MÉTODOS PARA ESTADÍSTICAS ====================
    
    // * Cuenta el número de recetas de un médico     
    function contarRecetas($id_medico) {
        try {
            $sql = "SELECT COUNT(*) as total FROM recetas WHERE id_medico = :id_medico AND estado = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_medico' => $id_medico));
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarRecetas: " . $e->getMessage());
            return 0;
        }
    }
    
    /**     * Cuenta el número de pacientes de un médico    */
    function contarPacientes($id_medico) {
        try {
            $sql = "SELECT COUNT(DISTINCT id_paciente) as total 
                    FROM recetas 
                    WHERE id_medico = :id_medico AND estado = 1 AND id_paciente IS NOT NULL";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_medico' => $id_medico));
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarPacientes: " . $e->getMessage());
            return 0;
        }
    }
    
    /**    * Obtiene estadísticas completas del médico     */
    function obtenerEstadisticasCompletas($id_medico) {
        try {
            $stats = [];
            
            // Total de recetas
            $sql = "SELECT COUNT(*) as total FROM recetas WHERE id_medico = :id_medico AND estado = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_medico' => $id_medico]);
            $stats['total_recetas'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Total de pacientes
            $sql = "SELECT COUNT(DISTINCT id_paciente) as total FROM recetas 
                    WHERE id_medico = :id_medico AND estado = 1 AND id_paciente IS NOT NULL";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_medico' => $id_medico]);
            $stats['total_pacientes'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Recetas del mes
            $sql = "SELECT COUNT(*) as total FROM recetas 
                    WHERE id_medico = :id_medico AND estado = 1 
                    AND MONTH(fecha_receta) = MONTH(CURDATE()) 
                    AND YEAR(fecha_receta) = YEAR(CURDATE())";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_medico' => $id_medico]);
            $stats['recetas_mes'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            return $stats;
        } catch(PDOException $e) {
            error_log("Error en obtenerEstadisticasCompletas: " . $e->getMessage());
            return ['total_recetas' => 0, 'total_pacientes' => 0, 'recetas_mes' => 0];
        }
    }
    
    // ==================== LISTAR PACIENTES ====================
    
    /**     * Lista los pacientes atendidos por un médico     */
    function listarPacientes($id_medico) {
        try {
            $sql = "SELECT DISTINCT 
                        rp.id_paciente, 
                        rp.nombre_paciente as nombre, 
                        rp.apellido_paciente as apellidos, 
                        rp.cedula_paciente as cedula, 
                        rp.telefono_paciente as telefono, 
                        rp.correo_paciente as correo,
                        (SELECT COUNT(*) FROM recetas WHERE id_paciente = rp.id_paciente AND id_medico = :id_medico AND estado = 1) as total_recetas,
                        (SELECT MAX(fecha_receta) FROM recetas WHERE id_paciente = rp.id_paciente AND id_medico = :id_medico AND estado = 1) as ultima_receta
                    FROM recetas r
                    INNER JOIN registro_paciente rp ON r.id_paciente = rp.id_paciente
                    WHERE r.id_medico = :id_medico AND r.estado = 1
                    ORDER BY rp.nombre_paciente ASC";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_medico' => $id_medico));
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en listarPacientes: " . $e->getMessage());
            return array();
        }
    }
    
    // ==================== ACTIVIDAD RECIENTE ====================
    
    /**     * Obtiene la actividad reciente de un médico     */
    function obtenerActividadReciente($id_medico, $limit = 10) {
        try {
            $sql = "SELECT 
                        r.id_receta as id,
                        r.nombre_medicamento as titulo,
                        r.fecha_receta as fecha,
                        CONCAT(rp.nombre_paciente, ' ', rp.apellido_paciente) as paciente,
                        'receta' as tipo
                    FROM recetas r
                    LEFT JOIN registro_paciente rp ON r.id_paciente = rp.id_paciente
                    WHERE r.id_medico = :id_medico AND r.estado = 1
                    ORDER BY r.fecha_receta DESC
                    LIMIT :limit";
            
            $query = $this->acceso->prepare($sql);
            $query->bindValue(':id_medico', $id_medico, PDO::PARAM_INT);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            
            $resultados = $query->fetchAll();
            $actividades = [];
            
            foreach ($resultados as $row) {
                $actividades[] = [
                    'id' => $row->id,
                    'titulo' => 'Receta emitida: ' . ($row->titulo ?? 'Medicamento'),
                    'descripcion' => 'Paciente: ' . ($row->paciente ?? 'N/A'),
                    'fecha' => $this->formatearFecha($row->fecha),
                    'tipo' => 'receta'
                ];
            }
            
            return $actividades;
        } catch(PDOException $e) {
            error_log("Error en obtenerActividadReciente: " . $e->getMessage());
            return array();
        }
    }
    
    /**     * Obtiene las próximas citas de un médico     */
    function obtenerProximasCitas($id_medico, $limit = 5) {
        // Por ahora retornar array vacío
        return [];
    }
    
    /**
     * Formatea una fecha para mostrar
     */
    private function formatearFecha($fecha) {
        if (empty($fecha)) return '';
        
        $timestamp = strtotime($fecha);
        $hoy = strtotime(date('Y-m-d'));
        $ayer = strtotime('-1 day', $hoy);
        
        if ($timestamp >= $hoy) {
            return 'Hoy, ' . date('g:i A', $timestamp);
        } elseif ($timestamp >= $ayer) {
            return 'Ayer, ' . date('g:i A', $timestamp);
        } else {
            return date('d/m/Y', $timestamp);
        }
    }
    
    // ==================== MÉTODOS DE BÚSQUEDA ====================
    
    /**     * Busca médicos por término de búsqueda     */
    function buscar($termino, $limit = 10) {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, cedula_medico, 
                           mpps_registro, telefono_medico, correo_medico
                    FROM registro_medico 
                    WHERE (nombre_medico LIKE :termino 
                           OR apellido_medico LIKE :termino 
                           OR cedula_medico LIKE :termino)
                    AND medico_tipo = 2
                    LIMIT :limit";
            $query = $this->acceso->prepare($sql);
            $query->bindValue(':termino', "%$termino%", PDO::PARAM_STR);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en buscar: " . $e->getMessage());
            return array();
        }
    }
    
    /**     * Obtiene un médico por su cédula     */
    function obtenerPorCedula($cedula) {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, cedula_medico,
                           mpps_registro, telefono_medico, correo_medico, direccion_medico
                    FROM registro_medico 
                    WHERE cedula_medico = :cedula AND medico_tipo = 2";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula));
            return $query->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error en obtenerPorCedula: " . $e->getMessage());
            return null;
        }
    }
    
    /**     * Verifica si un médico existe por cédula o correo     */
    function existe($cedula, $correo) {
        try {
            $sql = "SELECT id_medico FROM registro_medico 
                    WHERE cedula_medico = :cedula OR correo_medico = :correo";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula, ':correo' => $correo));
            return $query->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error en existe: " . $e->getMessage());
            return false;
        }
    }
    
    // ==================== MÉTODOS PARA ESPECIALIDADES ====================
    
    /**     * Obtiene las especialidades de un médico     */
    function obtenerEspecialidades($id_medico) {
        try {
            $sql = "SELECT e.*, em.tarifa, em.exp_anios, em.domicilio, em.extra
                    FROM especialidad_medicos em
                    INNER JOIN especialidades e ON em.id_especialidad = e.id_especialidad
                    WHERE em.id_medico = :id_medico AND em.activo = 1 AND e.activo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_medico' => $id_medico));
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en obtenerEspecialidades: " . $e->getMessage());
            return array();
        }
    }
    
    // ==================== MÉTODOS PARA CONSULTORIOS ====================
    
    /**     * Obtiene los consultorios de un médico     */
    function obtenerConsultorios($id_medico) {
        try {
            $sql = "SELECT c.*, cm.fecha_asignacion
                    FROM consultorio_medicos cm
                    INNER JOIN consultorios c ON cm.id_consultorio = c.id_consultorio
                    WHERE cm.id_medico = :id_medico AND cm.activo = 1 AND c.activo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_medico' => $id_medico));
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en obtenerConsultorios: " . $e->getMessage());
            return array();
        }
    }
}
?>