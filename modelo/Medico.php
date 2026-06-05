<?php
<<<<<<< HEAD
<<<<<<< HEAD
// modelo/Medico.php - VERSIÓN COMPLETA

=======
<<<<<<< HEAD

=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
include_once 'Conexion.php';

class Medico {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
<<<<<<< HEAD
<<<<<<< HEAD
    
    // ==================== MÉTODOS PRINCIPALES ====================
    
    /**
     * Obtiene los datos completos de un médico por su ID
     * @param int $id ID del médico
     * @return array Array con los datos del médico o vacío si no existe
     */
    function obtener_datos($id) {
=======
<<<<<<< HEAD
    
    // ==================== MÉTODOS PRINCIPALES ====================
    
   
=======
    
    // ==================== MÉTODOS PRINCIPALES ====================   
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
    }
    
  
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
            
            // Retornar el nombre del avatar anterior como string
            return $avatar_anterior;
        } catch(PDOException $e) {
            error_log("Error en cambiar_photo: " . $e->getMessage());
            return 'avatarDES.jpg';
        }
    }
    
   
    function crear($datos) {
        try {
            // Extraer datos del array
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
            $tipo = $datos['tipo'] ?? 2; // Tipo 2 = Médico
            $avatar = $datos['avatar'] ?? 'avatarDES.jpg';
            
            // Verificar si ya existe un médico con esta cédula o correo
            $sql = "SELECT id_medico FROM registro_medico WHERE cedula_medico = :cedula OR correo_medico = :correo";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula, ':correo' => $correo));
            $existe = $query->fetchAll();
            
            if(!empty($existe)) {
                return ['success' => false, 'message' => 'existe'];
            }
            
            // Insertar el nuevo médico
=======
      function crear($nombre, $apellidos, $fecha_nacimiento, $cedula, $telefono, $direccion, $correo, $sexo, $adicional, $password_hash, $tipo, $avatar) {
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
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
=======
    }    
   
    function obtenerDatosBasicos($id) {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, cedula_medico,
                           mpps_registro, telefono_medico, correo_medico, direccion_medico,
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
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
=======
    }    
   
    function editar($id_medico, $telefono, $direccion, $correo, $sexo, $adicional, $mpps_registro = null) {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        try {
            $sql = "UPDATE registro_medico SET 
                    telefono_medico = :telefono,
                    direccion_medico = :direccion,
                    correo_medico = :correo,
                    sexo_medico = :sexo,
<<<<<<< HEAD
                    adicional_medico = :adicional 
                    WHERE id_medico = :id";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
=======
                    adicional_medico = :adicional";
            
            // Agregar mpps_registro solo si se proporciona
            if ($mpps_registro !== null) {
                $sql .= ", mpps_registro = :mpps_registro";
            }
            
            $sql .= " WHERE id_medico = :id";
            
            $params = array(
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                ':id' => $id_medico,
                ':telefono' => $telefono,
                ':direccion' => $direccion,
                ':correo' => $correo,
                ':sexo' => $sexo,
                ':adicional' => $adicional
<<<<<<< HEAD
            ));
=======
            );
            
            if ($mpps_registro !== null) {
                $params[':mpps_registro'] = $mpps_registro;
            }
            
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute($params);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            
            if ($resultado) {
                return ['success' => true, 'message' => 'editado'];
            } else {
                return ['success' => false, 'message' => 'error_actualizacion'];
            }
        } catch(PDOException $e) {
            error_log("Error en editar medico: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
<<<<<<< HEAD
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
=======
    }    
   
    function cambiar_photo($id_medico, $nombre) {
        try {
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            $sql = "SELECT avatar_medico FROM registro_medico WHERE id_medico = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_medico));
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            
            $avatar_anterior = $resultado ? $resultado->avatar_medico : 'avatarDES.jpg';
            
<<<<<<< HEAD
            // Actualizar con el nuevo avatar
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            $sql = "UPDATE registro_medico SET avatar_medico = :nombre WHERE id_medico = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_medico, ':nombre' => $nombre));
            
            return $avatar_anterior;
        } catch(PDOException $e) {
            error_log("Error en cambiar_photo: " . $e->getMessage());
            return 'avatarDES.jpg';
        }
<<<<<<< HEAD
    }
    
    /**
     * Crea un nuevo médico en el sistema
     * @param array $datos Datos del médico
     * @return array Resultado de la operación
     */
=======
    }    
  
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    function crear($datos) {
        try {
            $nombre = $datos['nombre'] ?? '';
            $apellidos = $datos['apellidos'] ?? '';
            $fecha_nacimiento = $datos['fecha_nacimiento'] ?? '';
            $cedula = $datos['cedula'] ?? '';
<<<<<<< HEAD
=======
            $mpps_registro = $datos['mpps_registro'] ?? '';
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            $telefono = $datos['telefono'] ?? '';
            $direccion = $datos['direccion'] ?? '';
            $correo = $datos['correo'] ?? '';
            $sexo = $datos['sexo'] ?? '';
            $adicional = $datos['adicional'] ?? '';
            $password_hash = $datos['password_hash'] ?? '';
            $tipo = $datos['tipo'] ?? 2;
            $avatar = $datos['avatar'] ?? 'avatarDES.jpg';
            
<<<<<<< HEAD
=======
            // Validar datos requeridos
            if (empty($nombre) || empty($apellidos) || empty($cedula) || empty($password_hash)) {
                return ['success' => false, 'message' => 'datos_incompletos'];
            }
            
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            // Verificar si ya existe
            $sql = "SELECT id_medico FROM registro_medico WHERE cedula_medico = :cedula OR correo_medico = :correo";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula, ':correo' => $correo));
            $existe = $query->fetchAll();
            
            if(!empty($existe)) {
                return ['success' => false, 'message' => 'existe'];
            }
            
<<<<<<< HEAD
<<<<<<< HEAD
            // Insertar
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            $sql = "INSERT INTO registro_medico(
                nombre_medico, apellido_medico, fecha_nacimiento_medico, 
                cedula_medico, telefono_medico, direccion_medico, 
=======
            // Insertar el nuevo médico
            $sql = "INSERT INTO registro_medico(
                nombre_medico, apellido_medico, fecha_nacimiento_medico, 
                cedula_medico, mpps_registro, telefono_medico, direccion_medico, 
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                correo_medico, sexo_medico, adicional_medico, 
                avatar_medico, medico_tipo
            ) VALUES (
                :nombre, :apellidos, :fecha_nacimiento,
<<<<<<< HEAD
                :cedula, :telefono, :direccion,
=======
                :cedula, :mpps_registro, :telefono, :direccion,
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                :correo, :sexo, :adicional,
                :avatar, :tipo
            )";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':nombre' => $nombre,
                ':apellidos' => $apellidos,
                ':fecha_nacimiento' => $fecha_nacimiento,
                ':cedula' => $cedula,
<<<<<<< HEAD
=======
                ':mpps_registro' => $mpps_registro,
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                $loginResult = $this->crearLogin($id_medico, $password_hash);
                
                if ($loginResult['success']) {
                    return ['success' => true, 'message' => 'add', 'id' => $id_medico];
                } else {
<<<<<<< HEAD
<<<<<<< HEAD
                    return ['success' => false, 'message' => 'error_login'];
                }
=======
                    // Si falla la creación del login, se podría revertir, pero por ahora solo registramos
                    error_log("Error al crear login para médico ID: $id_medico");
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    return ['success' => false, 'message' => 'error_login'];
                }
            } else {
                return ['success' => false, 'message' => 'error_bd'];
            }
        } catch(PDOException $e) {
            error_log("Error en crear medico: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_exception'];
        }
<<<<<<< HEAD
    }
    
=======
    }    
  
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
=======
    // * Cuenta el número de recetas de un médico     
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
=======
    /**     * Cuenta el número de pacientes de un médico    */
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
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
    
    // ==================== MÉTODOS DE BÚSQUEDA ====================
    
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
    
   
    function asignarEspecialidad($id_medico, $id_especialidad, $tarifa = 0, $exp_anios = 0, $domicilio = false, $extra = 0) {
        try {
            // Verificar si ya existe la asignación
            $sql = "SELECT id FROM especialidad_medicos 
                    WHERE id_medico = :id_medico AND id_especialidad = :id_especialidad AND activo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_medico' => $id_medico, ':id_especialidad' => $id_especialidad));
            
            if ($query->rowCount() > 0) {
                return ['success' => false, 'message' => 'ya_asignado'];
            }
            
            $sql = "INSERT INTO especialidad_medicos(
                        id_medico, id_especialidad, tarifa, exp_anios, domicilio, extra, fecha_asignacion
                    ) VALUES (
                        :id_medico, :id_especialidad, :tarifa, :exp_anios, :domicilio, :extra, CURDATE()
                    )";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':id_medico' => $id_medico,
                ':id_especialidad' => $id_especialidad,
                ':tarifa' => $tarifa,
                ':exp_anios' => $exp_anios,
                ':domicilio' => $domicilio ? 1 : 0,
                ':extra' => $extra
            ));
            
            if ($resultado) {
                return ['success' => true, 'message' => 'asignado'];
            } else {
                return ['success' => false, 'message' => 'error_asignacion'];
            }
        } catch(PDOException $e) {
            error_log("Error en asignarEspecialidad: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
    }
    
   
    function removerEspecialidad($id_asignacion) {
        try {
            $sql = "UPDATE especialidad_medicos SET activo = 0 WHERE id = :id";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(':id' => $id_asignacion));
            
            if ($resultado) {
                return ['success' => true, 'message' => 'removido'];
            } else {
                return ['success' => false, 'message' => 'error_remocion'];
            }
        } catch(PDOException $e) {
            error_log("Error en removerEspecialidad: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
    }
    
    // ==================== MÉTODOS PARA HORARIOS ====================
    
   
    function obtenerHorarios($id_medico) {
        try {
            $sql = "SELECT ch.*, c.nombre as consultorio_nombre
                    FROM consultorio_horarios ch
                    INNER JOIN consultorios c ON ch.id_consultorio = c.id_consultorio
                    WHERE ch.id_medico = :id_medico AND ch.activo = 1
                    ORDER BY FIELD(ch.dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'), ch.turno";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_medico' => $id_medico));
            return $query->fetchAll();
        } catch(PDOException $e) {
            error_log("Error en obtenerHorarios: " . $e->getMessage());
            return array();
        }
    }
    
    // ==================== MÉTODOS PARA CONSULTORIOS ====================
    
   
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
=======
                $this->crearLogin($id_medico, $password_hash);
                echo 'add';
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
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
=======
    /**    * Obtiene estadísticas completas del médico     */
    function obtenerEstadisticasCompletas($id_medico) {
        try {
            $stats = [];
            
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            // Total de recetas
            $sql = "SELECT COUNT(*) as total FROM recetas WHERE id_medico = :id_medico AND estado = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_medico' => $id_medico]);
<<<<<<< HEAD
            $total_recetas = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
=======
            $stats['total_recetas'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            
            // Total de pacientes
            $sql = "SELECT COUNT(DISTINCT id_paciente) as total FROM recetas 
                    WHERE id_medico = :id_medico AND estado = 1 AND id_paciente IS NOT NULL";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_medico' => $id_medico]);
<<<<<<< HEAD
            $total_pacientes = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
=======
            $stats['total_pacientes'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            
            // Recetas del mes
            $sql = "SELECT COUNT(*) as total FROM recetas 
                    WHERE id_medico = :id_medico AND estado = 1 
                    AND MONTH(fecha_receta) = MONTH(CURDATE()) 
                    AND YEAR(fecha_receta) = YEAR(CURDATE())";
            $query = $this->acceso->prepare($sql);
            $query->execute([':id_medico' => $id_medico]);
<<<<<<< HEAD
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
=======
            $stats['recetas_mes'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            return $stats;
        } catch(PDOException $e) {
            error_log("Error en obtenerEstadisticasCompletas: " . $e->getMessage());
            return ['total_recetas' => 0, 'total_pacientes' => 0, 'recetas_mes' => 0];
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
    }
    
    // ==================== LISTAR PACIENTES ====================
    
<<<<<<< HEAD
    /**
     * Lista los pacientes atendidos por un médico
     * @param int $id_medico ID del médico
     * @return array Lista de pacientes
     */
=======
    /**     * Lista los pacientes atendidos por un médico     */
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    function listarPacientes($id_medico) {
        try {
            $sql = "SELECT DISTINCT 
                        rp.id_paciente, 
                        rp.nombre_paciente as nombre, 
                        rp.apellido_paciente as apellidos, 
                        rp.cedula_paciente as cedula, 
                        rp.telefono_paciente as telefono, 
<<<<<<< HEAD
                        rp.correo_paciente as correo
=======
                        rp.correo_paciente as correo,
                        (SELECT COUNT(*) FROM recetas WHERE id_paciente = rp.id_paciente AND id_medico = :id_medico AND estado = 1) as total_recetas,
                        (SELECT MAX(fecha_receta) FROM recetas WHERE id_paciente = rp.id_paciente AND id_medico = :id_medico AND estado = 1) as ultima_receta
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
    /**
     * Obtiene la actividad reciente del médico
     * @param int $id_medico ID del médico
     * @param int $limit Límite de resultados
     * @return array Lista de actividades
     */
=======
    /**     * Obtiene la actividad reciente de un médico     */
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
    /**
     * Obtiene las próximas citas del médico
     * @param int $id_medico ID del médico
     * @param int $limit Límite de resultados
     * @return array Lista de citas
     */
    function obtenerProximasCitas($id_medico, $limit = 5) {
        // Por ahora retornar array vacío (funcionalidad para futura implementación)
=======
    /**     * Obtiene las próximas citas de un médico     */
    function obtenerProximasCitas($id_medico, $limit = 5) {
        // Por ahora retornar array vacío
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        return [];
    }
    
    /**
     * Formatea una fecha para mostrar
<<<<<<< HEAD
     * @param string $fecha Fecha en formato Y-m-d H:i:s o Y-m-d
     * @return string Fecha formateada
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
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
=======
    /**     * Busca médicos por término de búsqueda     */
    function buscar($termino, $limit = 10) {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, cedula_medico, 
                           mpps_registro, telefono_medico, correo_medico
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
    /**
     * Obtiene un médico por su cédula
     * @param string $cedula Cédula del médico
     * @return object|null Datos del médico
     */
    function obtenerPorCedula($cedula) {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, cedula_medico,
                           telefono_medico, correo_medico, direccion_medico
=======
    /**     * Obtiene un médico por su cédula     */
    function obtenerPorCedula($cedula) {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, cedula_medico,
                           mpps_registro, telefono_medico, correo_medico, direccion_medico
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
    /**
     * Verifica si un médico existe
     * @param string $cedula Cédula del médico
     * @param string $correo Correo del médico
     * @return bool True si existe
     */
=======
    /**     * Verifica si un médico existe por cédula o correo     */
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
    /**
     * Obtiene las especialidades de un médico
     * @param int $id_medico ID del médico
     * @return array Lista de especialidades
     */
=======
    /**     * Obtiene las especialidades de un médico     */
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    
<<<<<<< HEAD
    // ==================== MÉTODOS PARA CONSULTORIOS ====================
    
    /**
     * Obtiene los consultorios donde trabaja un médico
     * @param int $id_medico ID del médico
     * @return array Lista de consultorios
     */
=======
    // ==================== MÉTODOS PARA TARIFA ====================
    
    /**
     * Obtiene la tarifa de consulta de un médico
     */
    function obtenerTarifa($id_medico) {
        try {
            $sql = "SELECT tarifa_consulta FROM registro_medico WHERE id_medico = :id_medico";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id_medico' => $id_medico));
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado->tarifa_consulta : 50.00;
        } catch(PDOException $e) {
            error_log("Error en obtenerTarifa: " . $e->getMessage());
            return 50.00;
        }
    }
    
    /**
     * Actualiza la tarifa de consulta de un médico
     */
    function actualizarTarifa($id_medico, $tarifa) {
        try {
            $sql = "UPDATE registro_medico SET tarifa_consulta = :tarifa WHERE id_medico = :id_medico";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':tarifa' => $tarifa, ':id_medico' => $id_medico));
            return true;
        } catch(PDOException $e) {
            error_log("Error en actualizarTarifa: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todas las tarifas de los médicos (para administrador)
     */
    function obtenerTodasLasTarifas() {
        try {
            $sql = "SELECT id_medico, nombre_medico, apellido_medico, especialidad, tarifa_consulta
                    FROM registro_medico
                    WHERE medico_tipo = 2
                    ORDER BY apellido_medico, nombre_medico";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error en obtenerTodasLasTarifas: " . $e->getMessage());
            return array();
        }
    }
    
    // ==================== MÉTODOS PARA CONSULTORIOS ====================
    
    /**     * Obtiene los consultorios de un médico     */
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
<<<<<<< HEAD
?>
=======

// Contar pacientes del médico
function contarPacientes($id_medico) {
    try {
        $sql = "SELECT COUNT(DISTINCT id_paciente) as total FROM recetas WHERE id_medico = :id_medico AND estado = 1 AND id_paciente IS NOT NULL";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id_medico' => $id_medico));
        $resultado = $query->fetch();
        return $resultado->total ?? 0;
    } catch(PDOException $e) {
        return 0;
    }
}

// Listar pacientes del médico
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
        return array();
    }
}
}
?>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
?>
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
