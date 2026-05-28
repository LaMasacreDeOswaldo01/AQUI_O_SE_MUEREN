<?php
/**
 * Definición de rutas de la aplicación.
 *
 * Formato de cada entrada:
 *   'patron' => [
 *       'controller' => 'NombreController',
 *       'action'     => 'metodo',
 *       'method'     => 'GET' | 'POST',          // opcional, por defecto GET
 *       'auth'       => true | false,            // opcional, por defecto true
 *       'rol'        => 'rol' | ['rol1', 'rol2'] // opcional; implica auth=true
 *   ]
 *
 * Patrones soportan parámetros con ':' (ej: 'consultorios/detalle/:id').
 * El router en index.php itera este array y empareja contra la URL.
 */

// Helper local: genera las rutas estándar de perfil de usuario por rol
// (buscar, capturar-datos, editar, cambiar-foto, cambiar-password) y mezcla
// extras opcionales. Evita repetir el mismo bloque para cada rol.
$crudUsuario = function (string $recurso, string $controller, string $rol, array $extras = []): array {
    $base = [
        "api/{$recurso}/buscar"           => ['action' => 'buscar'],
        "api/{$recurso}/capturar-datos"   => ['action' => 'capturarDatos'],
        "api/{$recurso}/editar"           => ['action' => 'editarUsuario'],
        "api/{$recurso}/cambiar-foto"     => ['action' => 'cambiarFoto'],
        "api/{$recurso}/cambiar-password" => ['action' => 'cambiarPassword'],
    ];
    $rutas = [];
    foreach (array_merge($base, $extras) as $path => $cfg) {
        $rutas[$path] = array_merge(
            ['controller' => $controller, 'method' => 'POST', 'rol' => $rol],
            $cfg
        );
    }
    return $rutas;
};

return array_merge(

    // ==================== PÁGINAS PÚBLICAS ====================
    [
        ''     => ['controller' => 'PageController', 'action' => 'home', 'auth' => false],
        'home' => ['controller' => 'PageController', 'action' => 'home', 'auth' => false],
    ],

    // ==================== AUTH ====================
    [
        'login'      => ['controller' => 'AuthController', 'action' => 'login',         'method' => 'POST', 'auth' => false],
        'login/:rol' => ['controller' => 'PageController', 'action' => 'loginRedirect',                     'auth' => false],
        'logout'     => ['controller' => 'AuthController', 'action' => 'logout',                            'auth' => true],
    ],

    // ==================== REGISTRO (vistas + API) ====================
    [
        'registro/paciente'      => ['controller' => 'RegistroController', 'action' => 'showRegistroPaciente',      'auth' => false],
        'registro/medico'        => ['controller' => 'RegistroController', 'action' => 'showRegistroMedico',        'auth' => false],
        'registro/asistente'     => ['controller' => 'RegistroController', 'action' => 'showRegistroAsistente',     'auth' => false],
        'registro/administrador' => ['controller' => 'RegistroController', 'action' => 'showRegistroAdministrador', 'auth' => false],

        'api/registro/paciente'      => ['controller' => 'RegistroController', 'action' => 'crearPaciente',      'method' => 'POST', 'auth' => false],
        'api/registro/medico'        => ['controller' => 'RegistroController', 'action' => 'crearMedico',        'method' => 'POST', 'auth' => false],
        'api/registro/asistente'     => ['controller' => 'RegistroController', 'action' => 'crearAsistente',     'method' => 'POST', 'auth' => false],
        'api/registro/administrador' => ['controller' => 'RegistroController', 'action' => 'crearAdministrador', 'method' => 'POST', 'auth' => false],
    ],

    // ==================== PANELES POR ROL ====================
    [
        'panel/paciente'      => ['controller' => 'PanelController', 'action' => 'paciente',      'rol' => 'paciente'],
        'panel/medico'        => ['controller' => 'PanelController', 'action' => 'medico',        'rol' => 'medico'],
        'panel/asistente'     => ['controller' => 'PanelController', 'action' => 'asistente',     'rol' => 'asistente'],
        'panel/administrador' => ['controller' => 'PanelController', 'action' => 'administrador', 'rol' => 'administrador'],
    ],

    // ==================== PERFIL (común a todos los roles autenticados) ====================
    [
        'perfil'                  => ['controller' => 'PerfilController', 'action' => 'index',                                'auth' => true],
        'perfil/editar'           => ['controller' => 'PerfilController', 'action' => 'editar',           'method' => 'POST', 'auth' => true],
        'perfil/cambiar-foto'     => ['controller' => 'PerfilController', 'action' => 'cambiarFoto',      'method' => 'POST', 'auth' => true],
        'perfil/cambiar-password' => ['controller' => 'PerfilController', 'action' => 'cambiarPassword',  'method' => 'POST', 'auth' => true],
        'api/perfil/datos'        => ['controller' => 'PerfilController', 'action' => 'getDatos',         'method' => 'POST', 'auth' => true],
    ],

    // ==================== API UBICACIÓN ====================
    [
        'api/ubicacion/estados'        => ['controller' => 'UbicacionController', 'action' => 'listarEstados',       'method' => 'POST', 'auth' => false],
        'api/ubicacion/ciudades'       => ['controller' => 'UbicacionController', 'action' => 'listarCiudades',      'method' => 'POST', 'auth' => false],
        'api/ubicacion/municipios'     => ['controller' => 'UbicacionController', 'action' => 'listarMunicipios',    'method' => 'POST', 'auth' => false],
        'api/ubicacion/parroquias'     => ['controller' => 'UbicacionController', 'action' => 'listarParroquias',    'method' => 'POST', 'auth' => false],
        'api/ubicacion/especialidades' => ['controller' => 'UbicacionController', 'action' => 'listaEspecialidades', 'method' => 'POST', 'auth' => false],
    ],

    // ==================== MÉDICO ====================
    ['medico/pacientes' => ['controller' => 'MedicoController', 'action' => 'pacientes', 'rol' => 'medico']],
    $crudUsuario('medicos', 'MedicoController', 'medico', [
        'api/medicos/mis-estadisticas'   => ['action' => 'misEstadisticas'],
        'api/medicos/listar-pacientes'   => ['action' => 'listarPacientes'],
        'api/medicos/actividad-reciente' => ['action' => 'actividadReciente'],
        'api/medicos/proximas-citas'     => ['action' => 'proximasCitas'],
    ]),

    // ==================== PACIENTE ====================
    ['paciente/recetas' => ['controller' => 'PacienteController', 'action' => 'recetas', 'rol' => 'paciente']],
    $crudUsuario('pacientes', 'PacienteController', 'paciente', [
        'api/pacientes/mis-estadisticas' => ['action' => 'misEstadisticas'],
    ]),

    // ==================== ASISTENTE ====================
    $crudUsuario('asistentes', 'AsistenteController', 'asistente'),

    // ==================== ADMINISTRADOR ====================
    ['administrador/usuarios' => ['controller' => 'AdministradorController', 'action' => 'listarUsuarios', 'rol' => 'administrador']],
    $crudUsuario('administradores', 'AdministradorController', 'administrador'),

    // ==================== CONSULTORIOS (administrador) ====================
    [
        'consultorios'             => ['controller' => 'ConsultorioController', 'action' => 'index',    'rol' => 'administrador'],
        'consultorios/crear'       => ['controller' => 'ConsultorioController', 'action' => 'crear',    'rol' => 'administrador'],
        'consultorios/editar'      => ['controller' => 'ConsultorioController', 'action' => 'editar',   'rol' => 'administrador'],
        'consultorios/detalle/:id' => ['controller' => 'ConsultorioController', 'action' => 'detalle',  'rol' => 'administrador'],
        'consultorios/horarios'    => ['controller' => 'ConsultorioController', 'action' => 'horarios', 'rol' => 'administrador'],

        'api/consultorios/listar'           => ['controller' => 'ConsultorioController', 'action' => 'listar',                    'method' => 'POST', 'rol'  => 'administrador'],
        'api/consultorios/crear'            => ['controller' => 'ConsultorioController', 'action' => 'crearConsultorio',          'method' => 'POST', 'rol'  => 'administrador'],
        'api/consultorios/editar'           => ['controller' => 'ConsultorioController', 'action' => 'editarConsultorio',         'method' => 'POST', 'rol'  => 'administrador'],
        'api/consultorios/eliminar'         => ['controller' => 'ConsultorioController', 'action' => 'eliminarConsultorio',       'method' => 'POST', 'rol'  => 'administrador'],
        'api/consultorios/asignar-medico'   => ['controller' => 'ConsultorioController', 'action' => 'asignarMedico',             'method' => 'POST', 'rol'  => 'administrador'],
        'api/consultorios/remover-medico'   => ['controller' => 'ConsultorioController', 'action' => 'removerMedico',             'method' => 'POST', 'rol'  => 'administrador'],
        'api/consultorios/guardar-horario'  => ['controller' => 'ConsultorioController', 'action' => 'guardarHorario',            'method' => 'POST', 'rol'  => 'administrador'],
        'api/consultorios/obtener-detalle'  => ['controller' => 'ConsultorioController', 'action' => 'obtenerDetalle',            'method' => 'POST', 'auth' => true],
        'api/consultorios/obtener-horarios' => ['controller' => 'ConsultorioController', 'action' => 'obtenerHorarios',           'method' => 'POST', 'auth' => true],
        'api/consultorios/estadisticas'     => ['controller' => 'ConsultorioController', 'action' => 'obtenerEstadisticas',       'method' => 'POST', 'rol'  => 'administrador'],
        'api/consultorios/listar-medicos'   => ['controller' => 'ConsultorioController', 'action' => 'listarMedicosDisponibles',  'method' => 'POST', 'rol'  => 'administrador'],
    ],

    // ==================== ESPECIALIDADES (administrador) ====================
    [
        'especialidades'                    => ['controller' => 'EspecialidadController', 'action' => 'index',          'rol' => 'administrador'],
        'especialidades/crear'              => ['controller' => 'EspecialidadController', 'action' => 'crear',          'rol' => 'administrador'],
        'especialidades/editar'             => ['controller' => 'EspecialidadController', 'action' => 'editar',         'rol' => 'administrador'],
        'especialidades/detalle/:id'        => ['controller' => 'EspecialidadController', 'action' => 'detalle',        'rol' => 'administrador'],
        'especialidades/asignar-medico/:id' => ['controller' => 'EspecialidadController', 'action' => 'asignarMedico',  'rol' => 'administrador'],

        'api/especialidades/listar'          => ['controller' => 'EspecialidadController', 'action' => 'listar',                     'method' => 'POST', 'rol' => 'administrador'],
        'api/especialidades/estadisticas'    => ['controller' => 'EspecialidadController', 'action' => 'obtenerEstadisticas',        'method' => 'POST', 'rol' => 'administrador'],
        'api/especialidades/obtener-detalle' => ['controller' => 'EspecialidadController', 'action' => 'obtenerDetalle',             'method' => 'POST', 'rol' => 'administrador'],
        'api/especialidades/crear'           => ['controller' => 'EspecialidadController', 'action' => 'crearEspecialidad',          'method' => 'POST', 'rol' => 'administrador'],
        'api/especialidades/editar'          => ['controller' => 'EspecialidadController', 'action' => 'editarEspecialidad',         'method' => 'POST', 'rol' => 'administrador'],
        'api/especialidades/eliminar'        => ['controller' => 'EspecialidadController', 'action' => 'eliminarEspecialidad',       'method' => 'POST', 'rol' => 'administrador'],
        'api/especialidades/asignar-medico'  => ['controller' => 'EspecialidadController', 'action' => 'asignarMedicoEspecialidad',  'method' => 'POST', 'rol' => 'administrador'],
        'api/especialidades/remover-medico'  => ['controller' => 'EspecialidadController', 'action' => 'removerMedicoEspecialidad',  'method' => 'POST', 'rol' => 'administrador'],
        'api/especialidades/listar-medicos'  => ['controller' => 'EspecialidadController', 'action' => 'listarMedicosDisponibles',   'method' => 'POST', 'rol' => 'administrador'],
    ],

    // ==================== RECETAS ====================
    [
        'recetas'                      => ['controller' => 'RecetaController', 'action' => 'index',                                'rol' => ['medico', 'asistente', 'administrador']],
        'api/recetas/listar'           => ['controller' => 'RecetaController', 'action' => 'listar',           'method' => 'POST', 'rol' => ['medico', 'asistente', 'administrador']],
        'api/recetas/crear'            => ['controller' => 'RecetaController', 'action' => 'crear',            'method' => 'POST', 'rol' => 'medico'],
        'api/recetas/editar'           => ['controller' => 'RecetaController', 'action' => 'editar',           'method' => 'POST', 'rol' => 'medico'],
        'api/recetas/borrar'           => ['controller' => 'RecetaController', 'action' => 'borrar',           'method' => 'POST', 'rol' => 'medico'],
        'api/recetas/obtener'          => ['controller' => 'RecetaController', 'action' => 'obtener',          'method' => 'POST', 'rol' => ['medico', 'asistente', 'administrador']],
        'api/recetas/buscar-pacientes' => ['controller' => 'RecetaController', 'action' => 'buscarPacientes',  'method' => 'POST', 'rol' => ['medico', 'asistente']],
        'api/recetas/mis-recetas'      => ['controller' => 'RecetaController', 'action' => 'misRecetas',       'method' => 'POST', 'rol' => 'paciente'],
        'api/recetas/estadisticas'     => ['controller' => 'RecetaController', 'action' => 'estadisticas',     'method' => 'POST', 'rol' => 'administrador'],
    ],

    // ==================== DOCUMENTOS MÉDICOS ====================
    [
        'documentos'               => ['controller' => 'DocumentoController', 'action' => 'index', 'rol' => ['paciente', 'asistente']],
        'documentos/ver/:tipo/:id' => ['controller' => 'DocumentoController', 'action' => 'ver',   'rol' => ['paciente', 'asistente']],
    ],

    // ==================== CSRF ====================
    [
        'api/csrf/token' => ['controller' => 'CSRFController', 'action' => 'getToken', 'method' => 'POST', 'auth' => false],
    ]
);
