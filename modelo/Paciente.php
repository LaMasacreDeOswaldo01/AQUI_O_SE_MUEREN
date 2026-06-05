<?php
<<<<<<< HEAD
// modelo/Paciente.php - Versión limpia sin conflictos
include_once 'Conexion.php';

class Paciente {
    public $objetos;
    public $acceso;
=======
include_once 'Conexion.php';
class Paciente {
    var $objetos;
    var $acceso;
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
<<<<<<< HEAD
    // ==================== MÉTODOS PRINCIPALES ====================
    
    public function obtener_datos($id) {
=======
    // ==================== MÉTODOS PRINCIPALES ====================    
    /**     * Obtiene los datos completos de un paciente por su ID     */
    function obtener_datos($id) {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        try {
            $sql = "SELECT rp.*, tp.nombre_tipo 
                    FROM registro_paciente rp
                    INNER JOIN tipo_paciente tp ON rp.paciente_tipo = tp.id_tipo_us 
                    WHERE rp.id_paciente = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        } catch(PDOException $e) {
            error_log("Error en obtener_datos: " . $e->getMessage());
            return array();
        }
    }
    
<<<<<<< HEAD
    public function obtenerDatosBasicos($id) {
        try {
            $sql = "SELECT id_paciente, nombre_paciente, apellido_paciente, 
                           cedula_paciente, telefono_paciente, correo_paciente,
                           fecha_nacimiento_pac, sexo_paciente, avatar_paciente
=======
   
    function obtenerDatosBasicos($id) {
        try {
            $sql = "SELECT id_paciente, nombre_paciente, apellido_paciente, 
                           cedula_paciente, telefono_paciente, correo_paciente,
                           fecha_nacimiento_pac, sexo_paciente, tipo_sangre, avatar_paciente
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    FROM registro_paciente 
                    WHERE id_paciente = :id AND paciente_tipo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            return $query->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error en obtenerDatosBasicos: " . $e->getMessage());
            return null;
        }
<<<<<<< HEAD
    }
    
    public function editar($id_paciente, $telefono, $direccion, $correo, $sexo, $adicional) {
=======
    }    
   
    function editar($id_paciente, $telefono, $direccion, $correo, $sexo, $tipo_sangre, $adicional) {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        try {
            $sql = "UPDATE registro_paciente SET 
                    telefono_paciente = :telefono,
                    direccion_paciente = :direccion,
                    correo_paciente = :correo,
                    sexo_paciente = :sexo,
<<<<<<< HEAD
                    adicional_paciente = :adicional 
                    WHERE id_paciente = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(
=======
                    tipo_sangre = :tipo_sangre,
                    adicional_paciente = :adicional 
                    WHERE id_paciente = :id";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                ':id' => $id_paciente,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':correo' => $correo,
                ':sexo' => $sexo,
<<<<<<< HEAD
                ':adicional' => $adicional
            ));
            return ['success' => true, 'message' => 'editado'];
=======
                ':tipo_sangre' => $tipo_sangre,
                ':adicional' => $adicional
            ));
            
            if ($resultado) {
                return ['success' => true, 'message' => 'editado'];
            } else {
                return ['success' => false, 'message' => 'error_actualizacion'];
            }
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        } catch(PDOException $e) {
            error_log("Error en editar paciente: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
<<<<<<< HEAD
    }
    
    public function cambiar_photo($id_paciente, $nombre) {
=======
    }    
    
    function cambiar_photo($id_paciente, $nombre) {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        try {
            $sql = "SELECT avatar_paciente FROM registro_paciente WHERE id_paciente = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_paciente));
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            
            $avatar_anterior = $resultado ? $resultado->avatar_paciente : 'avatarDES.jpg';
            
            $sql = "UPDATE registro_paciente SET avatar_paciente = :nombre WHERE id_paciente = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_paciente, ':nombre' => $nombre));
            
            return $avatar_anterior;
        } catch(PDOException $e) {
            error_log("Error en cambiar_photo: " . $e->getMessage());
            return 'avatarDES.jpg';
        }
<<<<<<< HEAD
    }
    
    public function crear($datos) {
=======
    }    
   
    function crear($datos) {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        try {
            $nombre = $datos['nombre'] ?? '';
            $apellidos = $datos['apellidos'] ?? '';
            $fecha_nacimiento = $datos['fecha_nacimiento'] ?? '';
            $cedula = $datos['cedula'] ?? '';
            $telefono = $datos['telefono'] ?? '';
            $direccion = $datos['direccion'] ?? '';
            $correo = $datos['correo'] ?? '';
            $sexo = $datos['sexo'] ?? '';
<<<<<<< HEAD
=======
            $tipo_sangre = $datos['tipo_sangre'] ?? '';
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            $adicional = $datos['adicional'] ?? '';
            $password_hash = $datos['password_hash'] ?? '';
            $tipo = $datos['tipo'] ?? 1;
            $avatar = $datos['avatar'] ?? 'avatarDES.jpg';
            
<<<<<<< HEAD
=======
            // Validar datos requeridos
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            if (empty($nombre) || empty($apellidos) || empty($cedula) || empty($password_hash)) {
                return ['success' => false, 'message' => 'datos_incompletos'];
            }
            
<<<<<<< HEAD
=======
            // Verificar si ya existe
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            $sql = "SELECT id_paciente FROM registro_paciente WHERE cedula_paciente = :cedula OR correo_paciente = :correo";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula, ':correo' => $correo));
            $existe = $query->fetchAll();
            
            if(!empty($existe)) {
                return ['success' => false, 'message' => 'existe'];
            }
            
<<<<<<< HEAD
            $sql = "INSERT INTO registro_paciente(
                nombre_paciente, apellido_paciente, fecha_nacimiento_pac, 
                cedula_paciente, telefono_paciente, direccion_paciente, 
                correo_paciente, sexo_paciente, adicional_paciente, 
=======
            // Insertar el nuevo paciente
            $sql = "INSERT INTO registro_paciente(
                nombre_paciente, apellido_paciente, fecha_nacimiento_pac, 
                cedula_paciente, telefono_paciente, direccion_paciente, 
                correo_paciente, sexo_paciente, tipo_sangre, adicional_paciente, 
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                avatar_paciente, paciente_tipo
            ) VALUES (
                :nombre, :apellidos, :fecha_nacimiento,
                :cedula, :telefono, :direccion,
<<<<<<< HEAD
                :correo, :sexo, :adicional,
=======
                :correo, :sexo, :tipo_sangre, :adicional,
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
=======
                ':tipo_sangre' => $tipo_sangre,
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                ':adicional' => $adicional,
                ':avatar' => $avatar,
                ':tipo' => $tipo
            ));
            
            if($resultado) {
                $id_paciente = $this->acceso->lastInsertId();
                $loginResult = $this->crearLogin($id_paciente, $password_hash);
                
                if ($loginResult['success']) {
                    return ['success' => true, 'message' => 'add', 'id' => $id_paciente];
                } else {
                    error_log("Error al crear login para paciente ID: $id_paciente");
                    return ['success' => false, 'message' => 'error_login'];
                }
            } else {
                return ['success' => false, 'message' => 'error_bd'];
            }
        } catch(PDOException $e) {
            error_log("Error en crear paciente: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_exception'];
        }
    }
    
<<<<<<< HEAD
    public function crearLogin($id_paciente, $password_hash) {
=======
    /**     * Crea el registro de login para un paciente     */
    function crearLogin($id_paciente, $password_hash) {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        try {
            $sql = "INSERT INTO login_paciente(id_paciente, password_hash, status) 
                    VALUES (:id_paciente, :password_hash, 'activo')";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':id_paciente' => $id_paciente,
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
    
<<<<<<< HEAD
    public function contarRecetas($id_paciente) {
=======
    // ==================== MÉTODOS PARA ESTADÍSTICAS ====================    
    /**     * Cuenta el número de recetas de un paciente     */
    function contarRecetas($id_paciente) {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        try {
            $sql = "SELECT COUNT(*) as total FROM recetas WHERE id_paciente = :id_paciente AND estado = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_paciente' => $id_paciente));
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado->total ?? 0;
        } catch(PDOException $e) {
            error_log("Error en contarRecetas: " . $e->getMessage());
            return 0;
        }
    }
    
<<<<<<< HEAD
    public function obtenerResumenRecetas($id_paciente, $meses = 6) {
        try {
            $sql = "SELECT 
                        DATE_FORMAT(fecha_receta, '%Y-%m') as mes,
                        COUNT(*) as total
                    FROM recetas 
                    WHERE id_paciente = :id_paciente 
                    AND estado = 1
                    AND fecha_receta >= DATE_SUB(CURDATE(), INTERVAL :meses MONTH)
                    GROUP BY DATE_FORMAT(fecha_receta, '%Y-%m')
                    ORDER BY mes DESC";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(
                ':id_paciente' => $id_paciente,
                ':meses' => $meses
            ));
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en obtenerResumenRecetas: " . $e->getMessage());
            return array();
        }
    }
    
    public function obtenerUltimasRecetas($id_paciente, $limit = 5) {
        try {
            $sql = "SELECT r.*, 
                           CONCAT(rm.nombre_medico, ' ', rm.apellido_medico) as medico_nombre
                    FROM recetas r
                    LEFT JOIN registro_medico rm ON r.id_medico = rm.id_medico
                    WHERE r.id_paciente = :id_paciente AND r.estado = 1
                    ORDER BY r.fecha_receta DESC
                    LIMIT :limit";
            $query = $this->acceso->prepare($sql);
            $query->bindValue(':id_paciente', $id_paciente, PDO::PARAM_INT);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en obtenerUltimasRecetas: " . $e->getMessage());
            return array();
        }
    }
    
    public function obtenerEstadisticas($id_paciente) {
        try {
            $stats = [];
            
=======
    /**     * Obtiene estadísticas completas del paciente     */
    function obtenerEstadisticas($id_paciente) {
        try {
            $stats = [];
            
            // Total de recetas
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            $sql = "SELECT COUNT(*) as total FROM recetas 
                    WHERE id_paciente = :id_paciente AND estado = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_paciente' => $id_paciente));
            $stats['total_recetas'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
<<<<<<< HEAD
=======
            // Última receta
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            $sql = "SELECT fecha_receta FROM recetas 
                    WHERE id_paciente = :id_paciente AND estado = 1 
                    ORDER BY fecha_receta DESC LIMIT 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_paciente' => $id_paciente));
            $ultima = $query->fetch(PDO::FETCH_OBJ);
            $stats['ultima_receta'] = $ultima ? $ultima->fecha_receta : null;
            
<<<<<<< HEAD
=======
            // Médicos que lo han atendido
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            $sql = "SELECT COUNT(DISTINCT id_medico) as total FROM recetas 
                    WHERE id_paciente = :id_paciente AND estado = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_paciente' => $id_paciente));
            $stats['medicos_atendieron'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            return $stats;
        } catch(PDOException $e) {
            error_log("Error en obtenerEstadisticas: " . $e->getMessage());
            return [
                'total_recetas' => 0,
                'ultima_receta' => null,
                'medicos_atendieron' => 0
            ];
        }
    }
    
<<<<<<< HEAD
    public function buscar($termino, $limit = 10) {
        try {
            $sql = "SELECT id_paciente, nombre_paciente, apellido_paciente, cedula_paciente, 
                           telefono_paciente, correo_paciente, fecha_nacimiento_pac, sexo_paciente
=======
    // ==================== MÉTODOS DE BÚSQUEDA ====================    
    /**     * Busca pacientes por término de búsqueda     */
    function buscar($termino, $limit = 10) {
        try {
            $sql = "SELECT id_paciente, nombre_paciente, apellido_paciente, cedula_paciente, 
                           telefono_paciente, correo_paciente, fecha_nacimiento_pac, 
                           sexo_paciente, tipo_sangre
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    FROM registro_paciente 
                    WHERE (nombre_paciente LIKE :termino 
                           OR apellido_paciente LIKE :termino 
                           OR cedula_paciente LIKE :termino)
                    AND paciente_tipo = 1
                    LIMIT :limit";
            $query = $this->acceso->prepare($sql);
            $query->bindValue(':termino', "%$termino%", PDO::PARAM_STR);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
<<<<<<< HEAD
            $query->execute();
            
            $resultados = $query->fetchAll();
            
=======
            $query->execute();            
            $resultados = $query->fetchAll();
            
            // Calcular edad para cada paciente
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            foreach ($resultados as $paciente) {
                if (isset($paciente->fecha_nacimiento_pac) && !empty($paciente->fecha_nacimiento_pac)) {
                    $nacimiento = new DateTime($paciente->fecha_nacimiento_pac);
                    $hoy = new DateTime();
                    $edad = $nacimiento->diff($hoy);
                    $paciente->edad = $edad->y;
                } else {
                    $paciente->edad = 0;
                }
            }
            
            return $resultados;
        } catch(PDOException $e) {
            error_log("Error en buscar: " . $e->getMessage());
            return array();
        }
    }
    
<<<<<<< HEAD
    public function buscarAutocompletar($termino, $limit = 10) {
        try {
            $sql = "SELECT id_paciente, 
                           CONCAT(nombre_paciente, ' ', apellido_paciente) as nombre_completo,
                           cedula_paciente
                    FROM registro_paciente 
                    WHERE (nombre_paciente LIKE :termino 
                           OR apellido_paciente LIKE :termino 
                           OR cedula_paciente LIKE :termino)
                    AND paciente_tipo = 1
                    LIMIT :limit";
            $query = $this->acceso->prepare($sql);
            $query->bindValue(':termino', "%$termino%", PDO::PARAM_STR);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en buscarAutocompletar: " . $e->getMessage());
            return array();
        }
    }
    
    public function obtenerPorCedula($cedula) {
        try {
            $sql = "SELECT id_paciente, nombre_paciente, apellido_paciente, cedula_paciente,
                           telefono_paciente, correo_paciente, direccion_paciente,
                           fecha_nacimiento_pac, sexo_paciente, avatar_paciente
=======
    /**     * Obtiene un paciente por su cédula     */
    function obtenerPorCedula($cedula) {
        try {
            $sql = "SELECT id_paciente, nombre_paciente, apellido_paciente, cedula_paciente,
                           telefono_paciente, correo_paciente, direccion_paciente,
                           fecha_nacimiento_pac, sexo_paciente, tipo_sangre, avatar_paciente
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    FROM registro_paciente 
                    WHERE cedula_paciente = :cedula AND paciente_tipo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula));
            $paciente = $query->fetch(PDO::FETCH_OBJ);
            
<<<<<<< HEAD
=======
            // Calcular edad
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            if ($paciente && isset($paciente->fecha_nacimiento_pac) && !empty($paciente->fecha_nacimiento_pac)) {
                $nacimiento = new DateTime($paciente->fecha_nacimiento_pac);
                $hoy = new DateTime();
                $edad = $nacimiento->diff($hoy);
                $paciente->edad = $edad->y;
            }
            
            return $paciente;
        } catch(PDOException $e) {
            error_log("Error en obtenerPorCedula: " . $e->getMessage());
            return null;
        }
    }
    
<<<<<<< HEAD
    public function existe($cedula, $correo) {
=======
    /**     * Verifica si un paciente existe por cédula o correo     */
    function existe($cedula, $correo) {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        try {
            $sql = "SELECT id_paciente FROM registro_paciente 
                    WHERE cedula_paciente = :cedula OR correo_paciente = :correo";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula, ':correo' => $correo));
            return $query->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error en existe: " . $e->getMessage());
            return false;
        }
    }
    
<<<<<<< HEAD
    public function obtenerIdPorCedula($cedula) {
        try {
            $sql = "SELECT id_paciente FROM registro_paciente WHERE cedula_paciente = :cedula";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula));
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado->id_paciente : null;
        } catch(PDOException $e) {
            error_log("Error en obtenerIdPorCedula: " . $e->getMessage());
            return null;
        }
    }
    
    public function obtenerPerfil($id_paciente) {
=======
    /**     * Obtiene el perfil completo del paciente     */
    function obtenerPerfil($id_paciente) {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        try {
            $sql = "SELECT rp.*, tp.nombre_tipo,
                           (SELECT COUNT(*) FROM recetas WHERE id_paciente = rp.id_paciente AND estado = 1) as total_recetas
                    FROM registro_paciente rp
                    INNER JOIN tipo_paciente tp ON rp.paciente_tipo = tp.id_tipo_us 
                    WHERE rp.id_paciente = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_paciente));
            $perfil = $query->fetch(PDO::FETCH_OBJ);
            
            if ($perfil && isset($perfil->fecha_nacimiento_pac) && !empty($perfil->fecha_nacimiento_pac)) {
                $nacimiento = new DateTime($perfil->fecha_nacimiento_pac);
                $hoy = new DateTime();
                $edad = $nacimiento->diff($hoy);
                $perfil->edad = $edad->y;
            }
            
            return $perfil;
        } catch(PDOException $e) {
            error_log("Error en obtenerPerfil: " . $e->getMessage());
            return null;
        }
    }
    
<<<<<<< HEAD
    public function actualizarPerfil($id_paciente, $datos) {
=======
    /**     * Actualiza el perfil del paciente     */
    function actualizarPerfil($id_paciente, $datos) {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        try {
            $campos = [];
            $params = [':id' => $id_paciente];
            
            $camposPermitidos = ['telefono_paciente', 'direccion_paciente', 'correo_paciente', 
<<<<<<< HEAD
                                  'sexo_paciente', 'adicional_paciente'];
=======
                                  'sexo_paciente', 'tipo_sangre', 'adicional_paciente'];
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            
            foreach ($camposPermitidos as $campo) {
                if (isset($datos[$campo])) {
                    $campos[] = "$campo = :$campo";
                    $params[":$campo"] = $datos[$campo];
                }
            }
            
            if (empty($campos)) {
                return ['success' => false, 'message' => 'sin_cambios'];
            }
            
            $sql = "UPDATE registro_paciente SET " . implode(', ', $campos) . " WHERE id_paciente = :id";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute($params);
            
            if ($resultado) {
                return ['success' => true, 'message' => 'perfil_actualizado'];
            } else {
                return ['success' => false, 'message' => 'error_actualizacion'];
            }
        } catch(PDOException $e) {
            error_log("Error en actualizarPerfil: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
    }
<<<<<<< HEAD
    
    public function obtenerProximasCitas($id_paciente, $limit = 5) {
        return [];
    }
    
    public function obtenerHistorialCitas($id_paciente, $limit = 10) {
        return [];
    }
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
}
?>