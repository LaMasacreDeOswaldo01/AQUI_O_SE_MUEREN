<?php
include_once 'Conexion.php';
class Paciente {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    // ==================== MÉTODOS PRINCIPALES ====================    
    /**     * Obtiene los datos completos de un paciente por su ID     */
    function obtener_datos($id) {
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
    
   
    function obtenerDatosBasicos($id) {
        try {
            $sql = "SELECT id_paciente, nombre_paciente, apellido_paciente, 
                           cedula_paciente, telefono_paciente, correo_paciente,
                           fecha_nacimiento_pac, sexo_paciente, tipo_sangre, avatar_paciente
                    FROM registro_paciente 
                    WHERE id_paciente = :id AND paciente_tipo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            return $query->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error en obtenerDatosBasicos: " . $e->getMessage());
            return null;
        }
    }    
   
    function editar($id_paciente, $telefono, $direccion, $correo, $sexo, $tipo_sangre, $adicional) {
        try {
            $sql = "UPDATE registro_paciente SET 
                    telefono_paciente = :telefono,
                    direccion_paciente = :direccion,
                    correo_paciente = :correo,
                    sexo_paciente = :sexo,
                    tipo_sangre = :tipo_sangre,
                    adicional_paciente = :adicional 
                    WHERE id_paciente = :id";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':id' => $id_paciente,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':correo' => $correo,
                ':sexo' => $sexo,
                ':tipo_sangre' => $tipo_sangre,
                ':adicional' => $adicional
            ));
            
            if ($resultado) {
                return ['success' => true, 'message' => 'editado'];
            } else {
                return ['success' => false, 'message' => 'error_actualizacion'];
            }
        } catch(PDOException $e) {
            error_log("Error en editar paciente: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
    }    
    
    function cambiar_photo($id_paciente, $nombre) {
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
    }    
   
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
            $tipo_sangre = $datos['tipo_sangre'] ?? '';
            $adicional = $datos['adicional'] ?? '';
            $password_hash = $datos['password_hash'] ?? '';
            $tipo = $datos['tipo'] ?? 1;
            $avatar = $datos['avatar'] ?? 'avatarDES.jpg';
            
            // Validar datos requeridos
            if (empty($nombre) || empty($apellidos) || empty($cedula) || empty($password_hash)) {
                return ['success' => false, 'message' => 'datos_incompletos'];
            }
            
            // Verificar si ya existe
            $sql = "SELECT id_paciente FROM registro_paciente WHERE cedula_paciente = :cedula OR correo_paciente = :correo";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula, ':correo' => $correo));
            $existe = $query->fetchAll();
            
            if(!empty($existe)) {
                return ['success' => false, 'message' => 'existe'];
            }
            
            // Insertar el nuevo paciente
            $sql = "INSERT INTO registro_paciente(
                nombre_paciente, apellido_paciente, fecha_nacimiento_pac, 
                cedula_paciente, telefono_paciente, direccion_paciente, 
                correo_paciente, sexo_paciente, tipo_sangre, adicional_paciente, 
                avatar_paciente, paciente_tipo
            ) VALUES (
                :nombre, :apellidos, :fecha_nacimiento,
                :cedula, :telefono, :direccion,
                :correo, :sexo, :tipo_sangre, :adicional,
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
                ':tipo_sangre' => $tipo_sangre,
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
    
    /**     * Crea el registro de login para un paciente     */
    function crearLogin($id_paciente, $password_hash) {
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
    
    // ==================== MÉTODOS PARA ESTADÍSTICAS ====================    
    /**     * Cuenta el número de recetas de un paciente     */
    function contarRecetas($id_paciente) {
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
    
    /**     * Obtiene estadísticas completas del paciente     */
    function obtenerEstadisticas($id_paciente) {
        try {
            $stats = [];
            
            // Total de recetas
            $sql = "SELECT COUNT(*) as total FROM recetas 
                    WHERE id_paciente = :id_paciente AND estado = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_paciente' => $id_paciente));
            $stats['total_recetas'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Última receta
            $sql = "SELECT fecha_receta FROM recetas 
                    WHERE id_paciente = :id_paciente AND estado = 1 
                    ORDER BY fecha_receta DESC LIMIT 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_paciente' => $id_paciente));
            $ultima = $query->fetch(PDO::FETCH_OBJ);
            $stats['ultima_receta'] = $ultima ? $ultima->fecha_receta : null;
            
            // Médicos que lo han atendido
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
    
    // ==================== MÉTODOS DE BÚSQUEDA ====================    
    /**     * Busca pacientes por término de búsqueda     */
    function buscar($termino, $limit = 10) {
        try {
            $sql = "SELECT id_paciente, nombre_paciente, apellido_paciente, cedula_paciente, 
                           telefono_paciente, correo_paciente, fecha_nacimiento_pac, 
                           sexo_paciente, tipo_sangre
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
            $resultados = $query->fetchAll();
            
            // Calcular edad para cada paciente
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
    
    /**     * Obtiene un paciente por su cédula     */
    function obtenerPorCedula($cedula) {
        try {
            $sql = "SELECT id_paciente, nombre_paciente, apellido_paciente, cedula_paciente,
                           telefono_paciente, correo_paciente, direccion_paciente,
                           fecha_nacimiento_pac, sexo_paciente, tipo_sangre, avatar_paciente
                    FROM registro_paciente 
                    WHERE cedula_paciente = :cedula AND paciente_tipo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula));
            $paciente = $query->fetch(PDO::FETCH_OBJ);
            
            // Calcular edad
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
    
    /**     * Verifica si un paciente existe por cédula o correo     */
    function existe($cedula, $correo) {
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
    
    /**     * Obtiene el perfil completo del paciente     */
    function obtenerPerfil($id_paciente) {
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
    
    /**     * Actualiza el perfil del paciente     */
    function actualizarPerfil($id_paciente, $datos) {
        try {
            $campos = [];
            $params = [':id' => $id_paciente];
            
            $camposPermitidos = ['telefono_paciente', 'direccion_paciente', 'correo_paciente', 
                                  'sexo_paciente', 'tipo_sangre', 'adicional_paciente'];
            
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
}
?>