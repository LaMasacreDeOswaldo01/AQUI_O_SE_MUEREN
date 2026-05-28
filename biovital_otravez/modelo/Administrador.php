<?php

include_once 'Conexion.php';

class Administrador {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }    
    // ==================== MÉTODOS PRINCIPALES ====================
    
    function obtener_datos($id) {
        try {
            $sql = "SELECT ra.*, tp.nombre_tipo 
                    FROM registro_administrador ra
                    INNER JOIN tipo_paciente tp ON ra.administrador_tipo = tp.id_tipo_us 
                    WHERE ra.id_administrador = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id));
            $this->objetos = $query->fetchAll();
            return $this->objetos;
        } catch(PDOException $e) {
            error_log("Error en obtener_datos: " . $e->getMessage());
            return array();
        }
    }
    
   
    function editar($id_administrador, $telefono, $direccion, $correo, $sexo, $adicional) {
        try {
            $sql = "UPDATE registro_administrador SET 
                    telefono_administrador = :telefono,
                    direccion_administrador = :direccion,
                    correo_administrador = :correo,
                    sexo_administrador = :sexo,
                    adicional_administrador = :adicional 
                    WHERE id_administrador = :id";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':id' => $id_administrador,
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
            error_log("Error en editar administrador: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
    }
    
 
    function cambiar_photo($id_administrador, $nombre) {
        try {
            // Primero obtener el avatar actual
            $sql = "SELECT avatar_administrador FROM registro_administrador WHERE id_administrador = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_administrador));
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            
            $avatar_anterior = $resultado ? $resultado->avatar_administrador : 'avatarDES.jpg';
            
            // Actualizar con el nuevo avatar
            $sql = "UPDATE registro_administrador SET avatar_administrador = :nombre WHERE id_administrador = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_administrador, ':nombre' => $nombre));
            
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
            $tipo = $datos['tipo'] ?? 4; // Tipo 4 = Administrador
            $avatar = $datos['avatar'] ?? 'avatarDES.jpg';
            
            // Verificar si ya existe un administrador con esta cédula o correo
            $sql = "SELECT id_administrador FROM registro_administrador 
                    WHERE cedula_administrador = :cedula OR correo_administrador = :correo";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula, ':correo' => $correo));
            $existe = $query->fetchAll();
            
            if(!empty($existe)) {
                return ['success' => false, 'message' => 'existe'];
            }
            
            // Insertar el nuevo administrador
            $sql = "INSERT INTO registro_administrador(
                nombre_administrador, apellido_administrador, fecha_nacimiento_administrador, 
                cedula_administrador, telefono_administrador, direccion_administrador, 
                correo_administrador, sexo_administrador, adicional_administrador, 
                avatar_administrador, administrador_tipo
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
                $id_administrador = $this->acceso->lastInsertId();
                $loginResult = $this->crearLogin($id_administrador, $password_hash);
                
                if ($loginResult['success']) {
                    return ['success' => true, 'message' => 'add', 'id' => $id_administrador];
                } else {
                    // Si falla la creación del login, se podría revertir, pero por ahora solo registramos
                    error_log("Error al crear login para administrador ID: $id_administrador");
                    return ['success' => false, 'message' => 'error_login'];
                }
            } else {
                return ['success' => false, 'message' => 'error_bd'];
            }
        } catch(PDOException $e) {
            error_log("Error en crear administrador: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_exception'];
        }
    }
    
 
    function crearLogin($id_administrador, $password_hash) {
        try {
            $sql = "INSERT INTO login_administrador(id_administrador, password_hash, status) 
                    VALUES (:id_administrador, :password_hash, 'activo')";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':id_administrador' => $id_administrador,
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
    

    function obtenerEstadisticasGenerales() {
        try {
            $stats = [];
            
            // Total de pacientes
            $sql = "SELECT COUNT(*) as total FROM registro_paciente WHERE paciente_tipo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $stats['total_pacientes'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Total de médicos
            $sql = "SELECT COUNT(*) as total FROM registro_medico WHERE medico_tipo = 2";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $stats['total_medicos'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Total de asistentes
            $sql = "SELECT COUNT(*) as total FROM registro_asistente WHERE asistente_tipo = 3";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $stats['total_asistentes'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Total de administradores
            $sql = "SELECT COUNT(*) as total FROM registro_administrador WHERE administrador_tipo = 4";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $stats['total_administradores'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Total de usuarios activos (últimos 30 días)
            $sql = "SELECT COUNT(DISTINCT id_paciente) as total FROM login_paciente 
                    WHERE ultimo_acceso >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    UNION
                    SELECT COUNT(DISTINCT id_medico) FROM login_medico 
                    WHERE ultimo_acceso >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    UNION
                    SELECT COUNT(DISTINCT id_asistente) FROM login_asistente 
                    WHERE ultimo_acceso >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    UNION
                    SELECT COUNT(DISTINCT id_administrador) FROM login_administrador 
                    WHERE ultimo_acceso >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $total_activos = 0;
            while($row = $query->fetch(PDO::FETCH_OBJ)) {
                $total_activos += $row->total;
            }
            $stats['usuarios_activos'] = $total_activos;
            
            // Total de consultorios activos
            $sql = "SELECT COUNT(*) as total FROM consultorios WHERE activo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $stats['total_consultorios'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            // Total de especialidades activas
            $sql = "SELECT COUNT(*) as total FROM especialidades WHERE activo = 1";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $stats['total_especialidades'] = $query->fetch(PDO::FETCH_OBJ)->total ?? 0;
            
            return $stats;
        } catch(PDOException $e) {
            error_log("Error en obtenerEstadisticasGenerales: " . $e->getMessage());
            return [
                'total_pacientes' => 0,
                'total_medicos' => 0,
                'total_asistentes' => 0,
                'total_administradores' => 0,
                'usuarios_activos' => 0,
                'total_consultorios' => 0,
                'total_especialidades' => 0
            ];
        }
    }
    
   
    function totalUsuarios() {
        try {
            $sql = "SELECT 
                        (SELECT COUNT(*) FROM registro_paciente) as pacientes,
                        (SELECT COUNT(*) FROM registro_medico) as medicos,
                        (SELECT COUNT(*) FROM registro_asistente) as asistentes,
                        (SELECT COUNT(*) FROM registro_administrador) as administradores";
            $query = $this->acceso->prepare($sql);
            $query->execute();
            $resultado = $query->fetch(PDO::FETCH_OBJ);
            
            return ($resultado->pacientes ?? 0) + 
                   ($resultado->medicos ?? 0) + 
                   ($resultado->asistentes ?? 0) + 
                   ($resultado->administradores ?? 0);
        } catch(PDOException $e) {
            error_log("Error en totalUsuarios: " . $e->getMessage());
            return 0;
        }
    }
    
    // ==================== MÉTODOS DE GESTIÓN DE USUARIOS ====================
    
   function listarUsuarios($busqueda = '', $rol = '', $estado = '') {
    try {
        $usuarios = [];
        
        // Definición de las consultas por cada rol
        $consultas = [
            'paciente' => [
                'tabla' => 'registro_paciente',
                'tabla_login' => 'login_paciente',
                'id_field' => 'id_paciente',
                'nombre_field' => 'nombre_paciente',
                'apellido_field' => 'apellido_paciente',
                'cedula_field' => 'cedula_paciente',
                'telefono_field' => 'telefono_paciente',
                'correo_field' => 'correo_paciente'
            ],
            'medico' => [
                'tabla' => 'registro_medico',
                'tabla_login' => 'login_medico',
                'id_field' => 'id_medico',
                'nombre_field' => 'nombre_medico',
                'apellido_field' => 'apellido_medico',
                'cedula_field' => 'cedula_medico',
                'telefono_field' => 'telefono_medico',
                'correo_field' => 'correo_medico'
            ],
            'asistente' => [
                'tabla' => 'registro_asistente',
                'tabla_login' => 'login_asistente',
                'id_field' => 'id_asistente',
                'nombre_field' => 'nombre_asistente',
                'apellido_field' => 'apellido_asistente',
                'cedula_field' => 'cedula_asistente',
                'telefono_field' => 'telefono_asistente',
                'correo_field' => 'correo_asistente'
            ],
            'administrador' => [
                'tabla' => 'registro_administrador',
                'tabla_login' => 'login_administrador',
                'id_field' => 'id_administrador',
                'nombre_field' => 'nombre_administrador',
                'apellido_field' => 'apellido_administrador',
                'cedula_field' => 'cedula_administrador',
                'telefono_field' => 'telefono_administrador',
                'correo_field' => 'correo_administrador'
            ]
        ];
        
        // Filtrar por rol si se especifica
        if (!empty($rol) && isset($consultas[$rol])) {
            $roles = [$rol];
        } else {
            $roles = array_keys($consultas);
        }
        
        foreach ($roles as $rolActual) {
            $consulta = $consultas[$rolActual];
            
            $sql = "SELECT 
                        {$consulta['id_field']} as id,
                        {$consulta['nombre_field']} as nombre,
                        {$consulta['apellido_field']} as apellidos,
                        {$consulta['cedula_field']} as cedula,
                        {$consulta['telefono_field']} as telefono,
                        {$consulta['correo_field']} as correo,
                        '{$rolActual}' as tipo,
                        lp.status as activo
                    FROM {$consulta['tabla']} r
                    LEFT JOIN {$consulta['tabla_login']} lp ON r.{$consulta['id_field']} = lp.{$consulta['id_field']}
                    WHERE 1=1";
            
            $params = [];
            
            // Filtro por búsqueda
            if (!empty($busqueda)) {
                $sql .= " AND (
                    {$consulta['nombre_field']} LIKE :busqueda 
                    OR {$consulta['apellido_field']} LIKE :busqueda 
                    OR {$consulta['cedula_field']} LIKE :busqueda
                    OR {$consulta['correo_field']} LIKE :busqueda
                )";
                $params[':busqueda'] = "%$busqueda%";
            }
            
            // Filtro por estado
            if (!empty($estado) && $estado !== 'todos') {
                $statusValue = ($estado === 'activo') ? 'activo' : 'inactivo';
                $sql .= " AND lp.status = :estado";
                $params[':estado'] = $statusValue;
            }
            
            $sql .= " ORDER BY {$consulta['nombre_field']} ASC";
            
            $query = $this->acceso->prepare($sql);
            $query->execute($params);
            
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                $row->activo = ($row->activo === 'activo') ? 1 : 0;
                $usuarios[] = $row;
            }
        }
        
        return $usuarios;
    } catch(PDOException $e) {
        error_log("Error en listarUsuarios: " . $e->getMessage());
        return array();
    }
}
    
 
    function cambiarEstadoUsuario($tabla, $id_field, $id, $estado) {
        try {
            $sql = "UPDATE $tabla SET status = :estado WHERE $id_field = :id";
            $query = $this->acceso->prepare($sql);
            $resultado = $query->execute(array(
                ':id' => $id,
                ':estado' => $estado
            ));
            
            if ($resultado) {
                return ['success' => true, 'message' => 'estado_actualizado'];
            } else {
                return ['success' => false, 'message' => 'error_actualizacion'];
            }
        } catch(PDOException $e) {
            error_log("Error en cambiarEstadoUsuario: " . $e->getMessage());
            return ['success' => false, 'message' => 'error_bd'];
        }
    }
    
function editarUsuario($id, $rol, $correo, $telefono, $estado) {
    try {
        // Determinar las tablas según el rol
        $tablas = $this->getTablasPorRol($rol);
        
        if (!$tablas) {
            return ['success' => false, 'message' => 'rol_invalido'];
        }
        
        // Actualizar datos de registro
        $sql = "UPDATE {$tablas['tabla_registro']} SET 
                correo_{$rol} = :correo,
                telefono_{$rol} = :telefono
                WHERE {$tablas['id_field']} = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            ':id' => $id,
            ':correo' => $correo,
            ':telefono' => $telefono
        ));
        
        // Actualizar estado en tabla de login
        $sql = "UPDATE {$tablas['tabla_login']} SET status = :estado WHERE {$tablas['id_field']} = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(
            ':id' => $id,
            ':estado' => $estado
        ));
        
        return ['success' => true, 'message' => 'actualizado'];
    } catch(PDOException $e) {
        error_log("Error en editarUsuario: " . $e->getMessage());
        return ['success' => false, 'message' => 'error_bd'];
    }
}
    private function getTablasPorRol($rol) {
    $tablas = [
        'paciente' => [
            'tabla_registro' => 'registro_paciente',
            'tabla_login' => 'login_paciente',
            'id_field' => 'id_paciente'
        ],
        'medico' => [
            'tabla_registro' => 'registro_medico',
            'tabla_login' => 'login_medico',
            'id_field' => 'id_medico'
        ],
        'asistente' => [
            'tabla_registro' => 'registro_asistente',
            'tabla_login' => 'login_asistente',
            'id_field' => 'id_asistente'
        ],
        'administrador' => [
            'tabla_registro' => 'registro_administrador',
            'tabla_login' => 'login_administrador',
            'id_field' => 'id_administrador'
        ]
    ];
    
    return $tablas[$rol] ?? false;
}
   function eliminarUsuario($tabla_registro, $tabla_login, $id_field, $id) {
    try {
        // Iniciar transacción
        $this->acceso->beginTransaction();
        
        // Eliminar de la tabla de login
        $sql = "DELETE FROM $tabla_login WHERE $id_field = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id));
        
        // Eliminar de la tabla de registro
        $sql = "DELETE FROM $tabla_registro WHERE $id_field = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id));
        
        // Confirmar transacción
        $this->acceso->commit();
        
        return ['success' => true, 'message' => 'eliminado'];
    } catch(PDOException $e) {
        // Revertir transacción en caso de error
        $this->acceso->rollBack();
        error_log("Error en eliminarUsuario: " . $e->getMessage());
        return ['success' => false, 'message' => 'error_bd'];
    }
}
    
    // ==================== MÉTODOS DE BÚSQUEDA ====================
    
 
    function buscar($termino, $limit = 10) {
        try {
            $sql = "SELECT id_administrador, nombre_administrador, apellido_administrador, 
                           cedula_administrador, telefono_administrador, correo_administrador
                    FROM registro_administrador 
                    WHERE (nombre_administrador LIKE :termino 
                           OR apellido_administrador LIKE :termino 
                           OR cedula_administrador LIKE :termino)
                    AND administrador_tipo = 4
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
            $sql = "SELECT id_administrador, nombre_administrador, apellido_administrador, 
                           cedula_administrador, telefono_administrador, correo_administrador, 
                           direccion_administrador
                    FROM registro_administrador 
                    WHERE cedula_administrador = :cedula AND administrador_tipo = 4";
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
            $sql = "SELECT id_administrador FROM registro_administrador 
                    WHERE cedula_administrador = :cedula OR correo_administrador = :correo";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':cedula' => $cedula, ':correo' => $correo));
            return $query->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error en existe: " . $e->getMessage());
            return false;
        }
    }
    
    // ==================== MÉTODOS PARA ACTIVIDAD RECIENTE ====================
    
  
    function obtenerActividadReciente($limit = 10) {
        try {
            $actividades = [];
            
            // Obtener últimos accesos de pacientes
            $sql = "SELECT lp.ultimo_acceso, rp.nombre_paciente as nombre, rp.apellido_paciente as apellidos, 'paciente' as tipo
                    FROM login_paciente lp
                    INNER JOIN registro_paciente rp ON lp.id_paciente = rp.id_paciente
                    WHERE lp.ultimo_acceso IS NOT NULL
                    ORDER BY lp.ultimo_acceso DESC
                    LIMIT :limit";
            $query = $this->acceso->prepare($sql);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            
            while($row = $query->fetch(PDO::FETCH_OBJ)) {
                $actividades[] = $row;
            }
            
            return $actividades;
        } catch(PDOException $e) {
            error_log("Error en obtenerActividadReciente: " . $e->getMessage());
            return array();
        }
    }
}
?>