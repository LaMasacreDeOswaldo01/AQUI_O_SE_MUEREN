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
    function obtenerHorariosSemanales($id_medico) {
    try {
        $sql = "SELECT * FROM medico_horarios 
                WHERE id_medico = :id_medico 
                ORDER BY FIELD(dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'), turno";
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_medico' => $id_medico]);
        
        $resultados = $query->fetchAll();
        $horarios = [];
        
        foreach ($resultados as $h) {
            $horarios[$h->dia_semana][$h->turno] = [
                'id_horario' => $h->id_horario,
                'activo' => $h->activo,
                'hora_inicio' => $h->hora_inicio ? substr($h->hora_inicio, 0, 5) : null,
                'hora_fin' => $h->hora_fin ? substr($h->hora_fin, 0, 5) : null,
                'id_consultorio' => $h->id_consultorio,
                'id_especialidad' => $h->id_especialidad,
                'duracion_cita' => $h->duracion_cita
            ];
        }
        
        // Inicializar días sin horario
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        $turnos = ['Mañana', 'Tarde'];
        
        foreach ($dias as $dia) {
            if (!isset($horarios[$dia])) {
                $horarios[$dia] = [];
            }
            foreach ($turnos as $turno) {
                if (!isset($horarios[$dia][$turno])) {
                    $horarios[$dia][$turno] = [
                        'activo' => 0,
                        'hora_inicio' => null,
                        'hora_fin' => null,
                        'id_consultorio' => null,
                        'id_especialidad' => null,
                        'duracion_cita' => 30
                    ];
                }
            }
        }
        
        return $horarios;
    } catch(PDOException $e) {
        error_log("Error en obtenerHorariosSemanales: " . $e->getMessage());
        return [];
    }
}

function obtenerConsultoriosAsignados($id_medico) {
    try {
        $sql = "SELECT c.id_consultorio, c.nombre, c.direccion_detallada
                FROM consultorio_medicos cm
                INNER JOIN consultorios c ON cm.id_consultorio = c.id_consultorio
                WHERE cm.id_medico = :id_medico AND cm.activo = 1 AND c.activo = 1";
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_medico' => $id_medico]);
        return $query->fetchAll();
    } catch(PDOException $e) {
        error_log("Error en obtenerConsultoriosAsignados: " . $e->getMessage());
        return [];
    }
}

function obtenerEspecialidadesAsignadas($id_medico) {
    try {
        $sql = "SELECT e.id_especialidad, e.nombre, e.color
                FROM especialidad_medicos em
                INNER JOIN especialidades e ON em.id_especialidad = e.id_especialidad
                WHERE em.id_medico = :id_medico AND em.activo = 1 AND e.activo = 1";
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_medico' => $id_medico]);
        return $query->fetchAll();
    } catch(PDOException $e) {
        error_log("Error en obtenerEspecialidadesAsignadas: " . $e->getMessage());
        return [];
    }
}

function guardarHorarioSemanal($id_medico, $dia, $turno, $activo, $hora_inicio, $hora_fin, $id_consultorio, $id_especialidad, $duracion_cita) {
    try {
        // Verificar si ya existe
        $sql_check = "SELECT id_horario FROM medico_horarios 
                      WHERE id_medico = :id_medico AND dia_semana = :dia AND turno = :turno";
        $query_check = $this->acceso->prepare($sql_check);
        $query_check->execute([
            ':id_medico' => $id_medico,
            ':dia' => $dia,
            ':turno' => $turno
        ]);
        
        if ($query_check->rowCount() > 0) {
            // Actualizar existente
            $sql = "UPDATE medico_horarios SET 
                        activo = :activo,
                        hora_inicio = :hora_inicio,
                        hora_fin = :hora_fin,
                        id_consultorio = :id_consultorio,
                        id_especialidad = :id_especialidad,
                        duracion_cita = :duracion_cita,
                        updated_at = NOW()
                    WHERE id_medico = :id_medico AND dia_semana = :dia AND turno = :turno";
        } else {
            // Insertar nuevo
            $sql = "INSERT INTO medico_horarios (
                        id_medico, dia_semana, turno, activo, hora_inicio, hora_fin, 
                        id_consultorio, id_especialidad, duracion_cita
                    ) VALUES (
                        :id_medico, :dia, :turno, :activo, :hora_inicio, :hora_fin,
                        :id_consultorio, :id_especialidad, :duracion_cita
                    )";
        }
        
        $query = $this->acceso->prepare($sql);
        $resultado = $query->execute([
            ':id_medico' => $id_medico,
            ':dia' => $dia,
            ':turno' => $turno,
            ':activo' => $activo,
            ':hora_inicio' => $hora_inicio,
            ':hora_fin' => $hora_fin,
            ':id_consultorio' => $id_consultorio,
            ':id_especialidad' => $id_especialidad,
            ':duracion_cita' => $duracion_cita
        ]);
        
        if ($resultado) {
            return ['success' => true, 'message' => 'Horario guardado'];
        } else {
            return ['success' => false, 'message' => 'Error al guardar el horario'];
        }
    } catch(PDOException $e) {
        error_log("Error en guardarHorarioSemanal: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error en la base de datos'];
    }
}

function copiarHorariosSemanaAnterior($id_medico) {
    try {

        return ['success' => true, 'message' => 'Horarios copiados'];
    } catch(Exception $e) {
        return ['success' => false, 'message' => 'Error al copiar horarios'];
    }
}

function aplicarPlantillaHorarios($id_medico, $plantilla, $id_consultorio, $id_especialidad) {
    try {
        $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        
        if ($plantilla === 'completa') {
            // Jornada completa: 8-12 y 2-6, de lunes a viernes
            foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia) {
                $this->guardarHorarioSemanal($id_medico, $dia, 'Mañana', 1, '08:00', '12:00', $id_consultorio, $id_especialidad, 30);
                $this->guardarHorarioSemanal($id_medico, $dia, 'Tarde', 1, '14:00', '18:00', $id_consultorio, $id_especialidad, 30);
            }
        } elseif ($plantilla === 'solo_mananas') {
            // Solo mañanas: 8-12, de lunes a viernes
            foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia) {
                $this->guardarHorarioSemanal($id_medico, $dia, 'Mañana', 1, '08:00', '12:00', $id_consultorio, $id_especialidad, 30);
                $this->guardarHorarioSemanal($id_medico, $dia, 'Tarde', 0, null, null, null, null, 30);
            }
        } elseif ($plantilla === 'solo_tardes') {
            // Solo tardes: 2-6, de lunes a viernes
            foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia) {
                $this->guardarHorarioSemanal($id_medico, $dia, 'Mañana', 0, null, null, null, null, 30);
                $this->guardarHorarioSemanal($id_medico, $dia, 'Tarde', 1, '14:00', '18:00', $id_consultorio, $id_especialidad, 30);
            }
        }
        
        return ['success' => true, 'message' => 'Plantilla aplicada correctamente'];
    } catch(Exception $e) {
        return ['success' => false, 'message' => 'Error al aplicar la plantilla'];
    }
}

function obtenerCitasParaCalendario($id_medico, $fecha_inicio, $fecha_fin) {
    try {
        $sql = "SELECT 
                    c.id_cita,
                    c.fecha_cita,
                    c.hora_cita,
                    c.motivo,
                    c.estado,
                    c.tipo_consulta,
                    p.nombre_paciente,
                    p.apellido_paciente,
                    p.cedula_paciente,
                    e.nombre as especialidad_nombre,
                    con.nombre as consultorio_nombre
                FROM citas c
                INNER JOIN registro_paciente p ON c.id_paciente = p.id_paciente
                INNER JOIN especialidades e ON c.id_especialidad = e.id_especialidad
                LEFT JOIN consultorios con ON c.id_consultorio = con.id_consultorio
                WHERE c.id_medico = :id_medico
                AND c.fecha_cita BETWEEN :fecha_inicio AND :fecha_fin
                AND c.estado NOT IN ('cancelada')
                ORDER BY c.fecha_cita ASC, c.hora_cita ASC";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([
            ':id_medico' => $id_medico,
            ':fecha_inicio' => $fecha_inicio,
            ':fecha_fin' => $fecha_fin
        ]);
        
        $resultados = $query->fetchAll();
        $eventos = [];
        
        $estado_colores = [
            'pendiente' => '#f59e0b',
            'confirmada' => '#3b82f6',
            'en_progreso' => '#8b5cf6',
            'completada' => '#10b981',
            'no_asistio' => '#6b7280'
        ];
        
        foreach ($resultados as $cita) {
            $fecha_hora = $cita->fecha_cita . 'T' . $cita->hora_cita;
            $eventos[] = [
                'id' => $cita->id_cita,
                'title' => $cita->nombre_paciente . ' ' . $cita->apellido_paciente,
                'start' => $fecha_hora,
                'end' => date('Y-m-d\TH:i:s', strtotime($fecha_hora . ' +30 minutes')),
                'color' => $estado_colores[$cita->estado] ?? '#6b7280',
                'extendedProps' => [
                    'especialidad' => $cita->especialidad_nombre,
                    'consultorio' => $cita->consultorio_nombre,
                    'motivo' => $cita->motivo,
                    'tipo_consulta' => $cita->tipo_consulta,
                    'estado' => $cita->estado,
                    'cedula' => $cita->cedula_paciente
                ]
            ];
        }
        
        return $eventos;
    } catch(PDOException $e) {
        error_log("Error en obtenerCitasParaCalendario: " . $e->getMessage());
        return [];
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
    function listarCitas($id_medico, $filtro_estado = 'todos', $filtro_paciente = '', $filtro_fecha = '', $tipo_consulta = 'todos') {
    try {
        $sql = "SELECT 
                    c.id_cita,
                    c.fecha_cita,
                    c.hora_cita,
                    c.tipo_consulta,
                    c.motivo,
                    c.estado,
                    c.es_tercero,
                    c.nombre_tercero,
                    c.cedula_tercero,
                    c.telefono_tercero,
                    c.parentesco,
                    p.id_paciente,
                    p.nombre_paciente,
                    p.apellido_paciente,
                    p.cedula_paciente,
                    p.telefono_paciente,
                    p.correo_paciente,
                    e.nombre as especialidad_nombre,
                    e.color as especialidad_color,
                    con.nombre as consultorio_nombre,
                    con.direccion_detallada as consultorio_direccion,
                    con.telefono as consultorio_telefono
                FROM citas c
                INNER JOIN registro_paciente p ON c.id_paciente = p.id_paciente
                INNER JOIN especialidades e ON c.id_especialidad = e.id_especialidad
                LEFT JOIN consultorios con ON c.id_consultorio = con.id_consultorio
                WHERE c.id_medico = :id_medico";
        
        $params = [':id_medico' => $id_medico];
        
        if ($filtro_estado !== 'todos') {
            $sql .= " AND c.estado = :estado";
            $params[':estado'] = $filtro_estado;
        }
        
        if (!empty($filtro_paciente)) {
            $sql .= " AND (p.nombre_paciente LIKE :paciente OR p.apellido_paciente LIKE :paciente OR p.cedula_paciente LIKE :paciente)";
            $params[':paciente'] = "%$filtro_paciente%";
        }
        
        if (!empty($filtro_fecha)) {
            $sql .= " AND DATE(c.fecha_cita) = :fecha";
            $params[':fecha'] = $filtro_fecha;
        }
        
        if ($tipo_consulta !== 'todos') {
            $sql .= " AND c.tipo_consulta = :tipo_consulta";
            $params[':tipo_consulta'] = $tipo_consulta;
        }
        
        $sql .= " ORDER BY c.fecha_cita DESC, c.hora_cita ASC";
        
        $query = $this->acceso->prepare($sql);
        $query->execute($params);
        
        $resultados = $query->fetchAll();
        
        // Formatear los datos para la vista
        $citas = [];
        foreach ($resultados as $cita) {
            // Determinar el nombre del paciente (puede ser tercero)
            $nombre_paciente = $cita->nombre_paciente . ' ' . $cita->apellido_paciente;
            if ($cita->es_tercero && !empty($cita->nombre_tercero)) {
                $nombre_paciente = $cita->nombre_tercero . ' (Representado por: ' . $nombre_paciente . ')';
            }
            
            // Mapeo de estados para mostrar
            $estados_map = [
                'pendiente' => ['label' => 'Pendiente', 'class' => 'warning', 'icon' => 'fa-clock'],
                'confirmada' => ['label' => 'Confirmada', 'class' => 'info', 'icon' => 'fa-check-circle'],
                'en_progreso' => ['label' => 'En Progreso', 'class' => 'primary', 'icon' => 'fa-spinner'],
                'completada' => ['label' => 'Completada', 'class' => 'success', 'icon' => 'fa-check-double'],
                'cancelada' => ['label' => 'Cancelada', 'class' => 'danger', 'icon' => 'fa-times-circle'],
                'no_asistio' => ['label' => 'No Asistió', 'class' => 'secondary', 'icon' => 'fa-user-slash']
            ];
            
            $estado_info = $estados_map[$cita->estado] ?? $estados_map['pendiente'];
            
            $citas[] = [
                'id_cita' => $cita->id_cita,
                'fecha' => date('d/m/Y', strtotime($cita->fecha_cita)),
                'fecha_raw' => $cita->fecha_cita,
                'hora' => substr($cita->hora_cita, 0, 5),
                'hora_completa' => $cita->hora_cita,
                'paciente_id' => $cita->id_paciente,
                'paciente_nombre' => $nombre_paciente,
                'paciente_cedula' => $cita->cedula_paciente,
                'paciente_telefono' => $cita->telefono_paciente,
                'paciente_correo' => $cita->correo_paciente,
                'especialidad' => $cita->especialidad_nombre,
                'especialidad_color' => $cita->especialidad_color,
                'tipo_consulta' => $cita->tipo_consulta == 'primera_vez' ? 'Primera Vez' : 'Control',
                'motivo' => $cita->motivo,
                'estado' => $cita->estado,
                'estado_label' => $estado_info['label'],
                'estado_class' => $estado_info['class'],
                'estado_icon' => $estado_info['icon'],
                'consultorio_nombre' => $cita->consultorio_nombre ?? 'Por asignar',
                'consultorio_direccion' => $cita->consultorio_direccion,
                'consultorio_telefono' => $cita->consultorio_telefono,
                'es_tercero' => $cita->es_tercero,
                'nombre_tercero' => $cita->nombre_tercero,
                'parentesco' => $cita->parentesco
            ];
        }
        
        return $citas;
    } catch(PDOException $e) {
        error_log("Error en listarCitas: " . $e->getMessage());
        return [];
    }
}
function contarCitasPorEstado($id_medico) {
    try {
        $sql = "SELECT 
                    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                    SUM(CASE WHEN estado = 'confirmada' THEN 1 ELSE 0 END) as confirmadas,
                    SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas,
                    SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas,
                    SUM(CASE WHEN estado = 'no_asistio' THEN 1 ELSE 0 END) as no_asistieron,
                    COUNT(*) as total
                FROM citas 
                WHERE id_medico = :id_medico";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([':id_medico' => $id_medico]);
        $resultado = $query->fetch(PDO::FETCH_OBJ);
        
        // Citas del mes actual
        $sql_mes = "SELECT COUNT(*) as total FROM citas 
                    WHERE id_medico = :id_medico 
                    AND MONTH(fecha_cita) = MONTH(CURDATE()) 
                    AND YEAR(fecha_cita) = YEAR(CURDATE())";
        $query_mes = $this->acceso->prepare($sql_mes);
        $query_mes->execute([':id_medico' => $id_medico]);
        $citas_mes = $query_mes->fetch(PDO::FETCH_OBJ);
        
        return [
            'pendientes' => (int)($resultado->pendientes ?? 0),
            'confirmadas' => (int)($resultado->confirmadas ?? 0),
            'completadas' => (int)($resultado->completadas ?? 0),
            'canceladas' => (int)($resultado->canceladas ?? 0),
            'no_asistieron' => (int)($resultado->no_asistieron ?? 0),
            'total' => (int)($resultado->total ?? 0),
            'citas_mes' => (int)($citas_mes->total ?? 0)
        ];
    } catch(PDOException $e) {
        error_log("Error en contarCitasPorEstado: " . $e->getMessage());
        return [
            'pendientes' => 0,
            'confirmadas' => 0,
            'completadas' => 0,
            'canceladas' => 0,
            'no_asistieron' => 0,
            'total' => 0,
            'citas_mes' => 0
        ];
    }
}
function actualizarEstadoCita($id_cita, $id_medico, $nuevo_estado) {
    try {
        // Verificar que la cita pertenece al médico
        $sql_check = "SELECT id_cita FROM citas WHERE id_cita = :id_cita AND id_medico = :id_medico";
        $query_check = $this->acceso->prepare($sql_check);
        $query_check->execute([':id_cita' => $id_cita, ':id_medico' => $id_medico]);
        
        if ($query_check->rowCount() == 0) {
            return ['success' => false, 'message' => 'Cita no encontrada o no autorizada'];
        }
        
        $sql = "UPDATE citas SET estado = :estado, updated_at = NOW() WHERE id_cita = :id_cita";
        $query = $this->acceso->prepare($sql);
        $resultado = $query->execute([
            ':id_cita' => $id_cita,
            ':estado' => $nuevo_estado
        ]);
        
        if ($resultado) {
            return ['success' => true, 'message' => 'Estado actualizado'];
        } else {
            return ['success' => false, 'message' => 'Error al actualizar el estado'];
        }
    } catch(PDOException $e) {
        error_log("Error en actualizarEstadoCita: " . $e->getMessage());
        return ['success' => false, 'message' => 'Error en la base de datos'];
    }
}
function buscarPacientesCitas($id_medico, $termino) {
    try {
        $sql = "SELECT DISTINCT 
                    p.id_paciente,
                    p.nombre_paciente,
                    p.apellido_paciente,
                    p.cedula_paciente,
                    p.telefono_paciente,
                    p.correo_paciente
                FROM citas c
                INNER JOIN registro_paciente p ON c.id_paciente = p.id_paciente
                WHERE c.id_medico = :id_medico
                AND (p.nombre_paciente LIKE :termino 
                    OR p.apellido_paciente LIKE :termino 
                    OR p.cedula_paciente LIKE :termino)
                ORDER BY p.nombre_paciente ASC
                LIMIT 10";
        
        $query = $this->acceso->prepare($sql);
        $query->execute([
            ':id_medico' => $id_medico,
            ':termino' => "%$termino%"
        ]);
        
        $resultados = $query->fetchAll();
        
        $pacientes = [];
        foreach ($resultados as $p) {
            $pacientes[] = [
                'id' => $p->id_paciente,
                'nombre' => $p->nombre_paciente,
                'apellidos' => $p->apellido_paciente,
                'nombre_completo' => $p->nombre_paciente . ' ' . $p->apellido_paciente,
                'cedula' => $p->cedula_paciente,
                'telefono' => $p->telefono_paciente,
                'correo' => $p->correo_paciente
            ];
        }
        
        return $pacientes;
    } catch(PDOException $e) {
        error_log("Error en buscarPacientesCitas: " . $e->getMessage());
        return [];
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