<?php
// modelo/Medico.php - VERSIÓN COMPLETA

include_once 'Conexion.php';

class Medico {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    // ==================== MÉTODOS PRINCIPALES ====================
    
    /**
     * Obtiene los datos completos de un médico por su ID
     * @param int $id ID del médico
     * @return array Array con los datos del médico o vacío si no existe
     */
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
    
    /**
     * Obtiene los datos básicos de un médico por su ID
     * @param int $id ID del médico
     * @return object|null Datos del médico o null si no existe
     */
    function obtenerDatosBasicos($id) {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, cedula_medico,
                           telefono_medico, correo_medico, direccion_medico,
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
    
    /**
     * Edita los datos de un médico
     * @param int $id_medico ID del médico
     * @param string $telefono Teléfono del médico
     * @param string $direccion Dirección del médico
     * @param string $correo Correo electrónico
     * @param string $sexo Sexo del médico
     * @param string $adicional Información adicional
     * @return array Resultado de la operación
     */
    function editar($id_medico, $telefono, $direccion, $correo, $sexo, $adicional) {
        try {
            $sql = "UPDATE registro_medico SET 
                    telefono_medico = :telefono,
                    direccion_medico = :direccion,
                    correo_medico = :correo,
                    sexo_medico = :sexo,
                    adicional_medico = :adicional 
                    WHERE id_medico = :id";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':id' => $id_medico,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':correo' => $correo,
                ':sexo' => $sexo,
                ':adicional' => $adicional
            ));
            
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
    
    /**
     * Cambia la foto de perfil del médico
     * @param int $id_medico ID del médico
     * @param string $nombre Nombre del nuevo archivo de avatar
     * @return string Nombre del avatar anterior
     */
    function cambiar_photo($id_medico, $nombre) {
        try {
            // Primero obtener el avatar actual
            $sql = "SELECT avatar_medico FROM registro_medico WHERE id_medico = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_medico));
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            
            $avatar_anterior = $resultado ? $resultado->avatar_medico : 'avatarDES.jpg';
            
            // Actualizar con el nuevo avatar
            $sql = "UPDATE registro_medico SET avatar_medico = :nombre WHERE id_medico = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_medico, ':nombre' => $nombre));
            
            return $avatar_anterior;
        } catch(PDOException $e) {
            error_log("Error en cambiar_photo: " . $e->getMessage());
            return 'avatarDES.jpg';
        }
    }
    
    /**
     * Crea un nuevo médico en el sistema
     * @param array $datos Datos del médico
     * @return array Resultado de la operación
     */
    function crear($datos) {
        try {
            $nombre = $datos['nombre'] ?? '';
            $apellidos = $datos['apellidos'] ?? '';
            $fecha_nacimiento = $datos['fecha_nacimiento'] ?? '';
            $cedula = $datos['cedula'] ?? '';
            $telefono = $datos['telefono'] ?? '';
            $direccion = $datos['direccion'] ?? '';
            $correo = $datos['correo'] ?? '';
            $sexo = $datos['sexo'] ?? '';
            $adicional = $datos['adicional'] ?? '';
            $password_hash = $datos['password_hash'] ?? '';
            $tipo = $datos['tipo'] ?? 2;
            $avatar = $datos['avatar'] ?? 'avatarDES.jpg';
            
            // Verificar si ya existe
            $sql = "SELECT id_medico FROM registro_medico WHERE cedula_medico = :cedula OR correo_medico = :correo";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula, ':correo' => $correo));
            $existe = $query->fetchAll();
            
            if(!empty($existe)) {
                return ['success' => false, 'message' => 'existe'];
            }
            
            // Insertar
            $sql = "INSERT INTO registro_medico(
                nombre_medico, apellido_medico, fecha_nacimiento_medico, 
                cedula_medico, telefono_medico, direccion_medico, 
                correo_medico, sexo_medico, adicional_medico, 
                avatar_medico, medico_tipo
            ) VALUES (
                :nombre, :apellidos, :fecha_nacimiento,
                :cedula, :telefono, :direccion,
                :correo, :sexo, :adicional,
                :avatar, :tipo
            )";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':nombre' => $nombre,
                ':apellidos' => $apellidos,
                ':fecha_nacimiento' => $fecha_nacimiento,
                ':cedula' => $cedula,
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
    
    /**
     * Crea el registro de login para un médico
     * @param int $id_medico ID del médico
     * @param string $password_hash Hash de la contraseña
     * @return array Resultado de la operación
     */
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
    
    /**
     * Cuenta el número de recetas de un médico
     * @param int $id_medico ID del médico
     * @return int Número de recetas
     */
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
    
    /**
     * Cuenta el número de pacientes únicos atendidos por un médico
     * @param int $id_medico ID del médico
     * @return int Número de pacientes
     */
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
    
    /**
     * Obtiene estadísticas completas del médico
     * @param int $id_medico ID del médico
     * @return array Estadísticas
     */
    function obtenerEstadisticasCompletas($id_medico) {
        try {
            // Total de recetas
            $sql = "SELECT COUNT(*) as total FROM recetas WHERE id_medico = :id_medico AND estado = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_medico' => $id_medico]);
            $total_recetas = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Total de pacientes
            $sql = "SELECT COUNT(DISTINCT id_paciente) as total FROM recetas 
                    WHERE id_medico = :id_medico AND estado = 1 AND id_paciente IS NOT NULL";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_medico' => $id_medico]);
            $total_pacientes = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Recetas del mes
            $sql = "SELECT COUNT(*) as total FROM recetas 
                    WHERE id_medico = :id_medico AND estado = 1 
                    AND MONTH(fecha_receta) = MONTH(CURDATE()) 
                    AND YEAR(fecha_receta) = YEAR(CURDATE())";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_medico' => $id_medico]);
            $recetas_mes = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            return [
                'total_recetas' => $total_recetas,
                'total_pacientes' => $total_pacientes,
                'recetas_mes' => $recetas_mes,
                'citas_hoy' => 0
            ];
        } catch(PDOException $e) {
            error_log("Error en obtenerEstadisticasCompletas: " . $e->getMessage());
            return [
                'total_recetas' => 0,
                'total_pacientes' => 0,
                'recetas_mes' => 0,
                'citas_hoy' => 0
            ];
        }
    }
    
    // ==================== LISTAR PACIENTES ====================
    
    /**
     * Lista los pacientes atendidos por un médico
     * @param int $id_medico ID del médico
     * @return array Lista de pacientes
     */
    function listarPacientes($id_medico) {
        try {
            $sql = "SELECT DISTINCT 
                        rp.id_paciente, 
                        rp.nombre_paciente as nombre, 
                        rp.apellido_paciente as apellidos, 
                        rp.cedula_paciente as cedula, 
                        rp.telefono_paciente as telefono, 
                        rp.correo_paciente as correo
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
    
    /**
     * Obtiene la actividad reciente del médico
     * @param int $id_medico ID del médico
     * @param int $limit Límite de resultados
     * @return array Lista de actividades
     */
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
    
    /**
     * Obtiene las próximas citas del médico
     * @param int $id_medico ID del médico
     * @param int $limit Límite de resultados
     * @return array Lista de citas
     */
    function obtenerProximasCitas($id_medico, $limit = 5) {
        // Por ahora retornar array vacío (funcionalidad para futura implementación)
        return [];
    }
    
    /**
     * Formatea una fecha para mostrar
     * @param string $fecha Fecha en formato Y-m-d H:i:s o Y-m-d
     * @return string Fecha formateada
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
    
    /**
     * Busca médicos por término de búsqueda
     * @param string $termino Término de búsqueda
     * @param int $limit Límite de resultados
     * @return array Lista de médicos
     */
    function buscar($termino, $limit = 10) {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, cedula_medico, 
                           telefono_medico, correo_medico
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
    
    /**
     * Obtiene un médico por su cédula
     * @param string $cedula Cédula del médico
     * @return object|null Datos del médico
     */
    function obtenerPorCedula($cedula) {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, cedula_medico,
                           telefono_medico, correo_medico, direccion_medico
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
    
    /**
     * Verifica si un médico existe
     * @param string $cedula Cédula del médico
     * @param string $correo Correo del médico
     * @return bool True si existe
     */
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
    
    /**
     * Obtiene las especialidades de un médico
     * @param int $id_medico ID del médico
     * @return array Lista de especialidades
     */
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
    
    /**
     * Obtiene los consultorios donde trabaja un médico
     * @param int $id_medico ID del médico
     * @return array Lista de consultorios
     */
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