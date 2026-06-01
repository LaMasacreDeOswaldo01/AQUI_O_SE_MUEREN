<?php

return [
    // ==================== PÁGINAS PÚBLICAS ====================
    '' => ['controller' => 'PageController', 'action' => 'home', 'auth' => false],
    'home' => ['controller' => 'PageController', 'action' => 'home', 'auth' => false],
    
    // ==================== LOGIN ====================
<<<<<<< HEAD
    'login' => ['controller' => 'AuthController', 'action' => 'login', 'method' => 'POST', 'auth' => false],
=======
    'login' => ['controller' => 'AuthController', 'action' => 'login', 'auth' => false],
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
    'login/:rol' => ['controller' => 'PageController', 'action' => 'loginRedirect', 'auth' => false],
    'logout' => ['controller' => 'AuthController', 'action' => 'logout', 'auth' => true],
    
    // ==================== REGISTRO ====================
    'registro/paciente' => ['controller' => 'RegistroController', 'action' => 'showRegistroPaciente', 'auth' => false],
    'registro/medico' => ['controller' => 'RegistroController', 'action' => 'showRegistroMedico', 'auth' => false],
    'registro/asistente' => ['controller' => 'RegistroController', 'action' => 'showRegistroAsistente', 'auth' => false],
    'registro/administrador' => ['controller' => 'RegistroController', 'action' => 'showRegistroAdministrador', 'auth' => false],
    
    // ==================== API REGISTRO ====================
    'api/registro/paciente' => ['controller' => 'RegistroController', 'action' => 'crearPaciente', 'method' => 'POST', 'auth' => false],
    'api/registro/medico' => ['controller' => 'RegistroController', 'action' => 'crearMedico', 'method' => 'POST', 'auth' => false],
    'api/registro/asistente' => ['controller' => 'RegistroController', 'action' => 'crearAsistente', 'method' => 'POST', 'auth' => false],
    'api/registro/administrador' => ['controller' => 'RegistroController', 'action' => 'crearAdministrador', 'method' => 'POST', 'auth' => false],
    
<<<<<<< HEAD
    // ==================== API RECUPERACIÓN DE CUENTA ====================
    'api/recuperacion/buscar' => ['controller' => 'RecuperacionController', 'action' => 'buscarUsuario', 'method' => 'POST', 'auth' => false],
    'api/recuperacion/verificar' => ['controller' => 'RecuperacionController', 'action' => 'verificarRespuestas', 'method' => 'POST', 'auth' => false],
    'api/recuperacion/usuario' => ['controller' => 'RecuperacionController', 'action' => 'obtenerUsuario', 'method' => 'POST', 'auth' => false],
    'api/recuperacion/password' => ['controller' => 'RecuperacionController', 'action' => 'cambiarPassword', 'method' => 'POST', 'auth' => false],
    
=======
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
    // ==================== PANELES (usando ViewHelper) ====================
    'panel/paciente' => ['controller' => 'PanelController', 'action' => 'paciente', 'rol' => 'paciente'],
    'panel/medico' => ['controller' => 'PanelController', 'action' => 'medico', 'rol' => 'medico'],
    'panel/asistente' => ['controller' => 'PanelController', 'action' => 'asistente', 'rol' => 'asistente'],
    'panel/administrador' => ['controller' => 'PanelController', 'action' => 'administrador', 'rol' => 'administrador'],
    
    // ==================== PERFIL ====================
    'perfil' => ['controller' => 'PerfilController', 'action' => 'index', 'auth' => true],
    'perfil/editar' => ['controller' => 'PerfilController', 'action' => 'editar', 'method' => 'POST', 'auth' => true],
    'perfil/cambiar-foto' => ['controller' => 'PerfilController', 'action' => 'cambiarFoto', 'method' => 'POST', 'auth' => true],
    'perfil/cambiar-password' => ['controller' => 'PerfilController', 'action' => 'cambiarPassword', 'method' => 'POST', 'auth' => true],
    'api/perfil/datos' => ['controller' => 'PerfilController', 'action' => 'getDatos', 'method' => 'POST', 'auth' => true],
    
    // ==================== API UBICACIÓN ====================
    'api/ubicacion/estados' => ['controller' => 'UbicacionController', 'action' => 'listarEstados', 'method' => 'POST', 'auth' => false],
    'api/ubicacion/ciudades' => ['controller' => 'UbicacionController', 'action' => 'listarCiudades', 'method' => 'POST', 'auth' => false],
    'api/ubicacion/municipios' => ['controller' => 'UbicacionController', 'action' => 'listarMunicipios', 'method' => 'POST', 'auth' => false],
    'api/ubicacion/parroquias' => ['controller' => 'UbicacionController', 'action' => 'listarParroquias', 'method' => 'POST', 'auth' => false],
    
    // ==================== MÉDICO ====================
    'medico/pacientes' => ['controller' => 'MedicoController', 'action' => 'pacientes', 'rol' => 'medico'],
    'api/medicos/buscar' => ['controller' => 'MedicoController', 'action' => 'buscar', 'method' => 'POST', 'rol' => 'medico'],
    'api/medicos/capturar-datos' => ['controller' => 'MedicoController', 'action' => 'capturarDatos', 'method' => 'POST', 'rol' => 'medico'],
    'api/medicos/editar' => ['controller' => 'MedicoController', 'action' => 'editarUsuario', 'method' => 'POST', 'rol' => 'medico'],
    'api/medicos/cambiar-foto' => ['controller' => 'MedicoController', 'action' => 'cambiarFoto', 'method' => 'POST', 'rol' => 'medico'],
    'api/medicos/cambiar-password' => ['controller' => 'MedicoController', 'action' => 'cambiarPassword', 'method' => 'POST', 'rol' => 'medico'],
    'api/medicos/mis-estadisticas' => ['controller' => 'MedicoController', 'action' => 'misEstadisticas', 'method' => 'POST', 'rol' => 'medico'],
    'api/medicos/listar-pacientes' => ['controller' => 'MedicoController', 'action' => 'listarPacientes', 'method' => 'POST', 'rol' => 'medico'],
    'api/medicos/actividad-reciente' => ['controller' => 'MedicoController', 'action' => 'actividadReciente', 'method' => 'POST', 'rol' => 'medico'],
    'api/medicos/proximas-citas' => ['controller' => 'MedicoController', 'action' => 'proximasCitas', 'method' => 'POST', 'rol' => 'medico'],

    // ==================== PACIENTE ====================
    'paciente/recetas' => ['controller' => 'PacienteController', 'action' => 'recetas', 'rol' => 'paciente'],       
    'paciente/citas' => ['controller' => 'CitaController', 'action' => 'misCitas', 'rol' => 'paciente'],
    'paciente/citas/agendar' => ['controller' => 'CitaController', 'action' => 'agendarCitas', 'rol' => 'paciente'],
    'paciente/citas/agendar-propia' => ['controller' => 'CitaController', 'action' => 'agendarPropia', 'rol' => 'paciente'],
    'paciente/citas/agendar-tercero' => ['controller' => 'CitaController', 'action' => 'agendarTercero', 'rol' => 'paciente'],
    

     'api/citas/estadisticas' => ['controller' => 'CitaController', 'action' => 'estadisticas', 'method' => 'POST', 'rol' => 'paciente'],


    // API para gestionar citas
    'api/citas/listar' => ['controller' => 'CitaController', 'action' => 'listar', 'method' => 'POST', 'rol' => 'paciente'],
    'api/citas/crear' => ['controller' => 'CitaController', 'action' => 'crear', 'method' => 'POST', 'rol' => 'paciente'],
    'api/citas/cancelar' => ['controller' => 'CitaController', 'action' => 'cancelar', 'method' => 'POST', 'rol' => 'paciente'],
    'api/citas/obtener-horarios' => ['controller' => 'CitaController', 'action' => 'obtenerHorarios', 'method' => 'POST', 'auth' => true],
    'api/citas/obtener-detalle' => ['controller' => 'CitaController', 'action' => 'obtenerDetalle', 'method' => 'POST', 'rol' => 'paciente'],
    'api/pacientes/buscar' => ['controller' => 'PacienteController', 'action' => 'buscar', 'method' => 'POST', 'rol' => 'paciente'],
    'api/pacientes/capturar-datos' => ['controller' => 'PacienteController', 'action' => 'capturarDatos', 'method' => 'POST', 'rol' => 'paciente'],
    'api/pacientes/editar' => ['controller' => 'PacienteController', 'action' => 'editarUsuario', 'method' => 'POST', 'rol' => 'paciente'],
    'api/pacientes/cambiar-foto' => ['controller' => 'PacienteController', 'action' => 'cambiarFoto', 'method' => 'POST', 'rol' => 'paciente'],
    'api/pacientes/cambiar-password' => ['controller' => 'PacienteController', 'action' => 'cambiarPassword', 'method' => 'POST', 'rol' => 'paciente'],
    'api/pacientes/mis-estadisticas' => ['controller' => 'PacienteController', 'action' => 'misEstadisticas', 'method' => 'POST', 'rol' => 'paciente'],

    
    // ==================== ASISTENTE ====================
    'api/asistentes/buscar' => ['controller' => 'AsistenteController', 'action' => 'buscar', 'method' => 'POST', 'rol' => 'asistente'],
    'api/asistentes/capturar-datos' => ['controller' => 'AsistenteController', 'action' => 'capturarDatos', 'method' => 'POST', 'rol' => 'asistente'],
    'api/asistentes/editar' => ['controller' => 'AsistenteController', 'action' => 'editarUsuario', 'method' => 'POST', 'rol' => 'asistente'],
    'api/asistentes/cambiar-foto' => ['controller' => 'AsistenteController', 'action' => 'cambiarFoto', 'method' => 'POST', 'rol' => 'asistente'],
    'api/asistentes/cambiar-password' => ['controller' => 'AsistenteController', 'action' => 'cambiarPassword', 'method' => 'POST', 'rol' => 'asistente'],
    
    // ==================== ADMINISTRADOR ====================
    'administrador/usuarios' => ['controller' => 'AdministradorController', 'action' => 'listarUsuarios', 'rol' => 'administrador'],
    'api/administradores/buscar' => ['controller' => 'AdministradorController', 'action' => 'buscar', 'method' => 'POST', 'rol' => 'administrador'],
    'api/administradores/capturar-datos' => ['controller' => 'AdministradorController', 'action' => 'capturarDatos', 'method' => 'POST', 'rol' => 'administrador'],
    'api/administradores/editar' => ['controller' => 'AdministradorController', 'action' => 'editarUsuario', 'method' => 'POST', 'rol' => 'administrador'],
    'api/administradores/cambiar-foto' => ['controller' => 'AdministradorController', 'action' => 'cambiarFoto', 'method' => 'POST', 'rol' => 'administrador'],
    'api/administradores/cambiar-password' => ['controller' => 'AdministradorController', 'action' => 'cambiarPassword', 'method' => 'POST', 'rol' => 'administrador'],
     

    // ==================== CONSULTORIOS ====================
    'consultorios' => ['controller' => 'ConsultorioController', 'action' => 'index', 'rol' => 'administrador'],
    'consultorios/crear' => ['controller' => 'ConsultorioController', 'action' => 'crear', 'rol' => 'administrador'],
    'consultorios/editar' => ['controller' => 'ConsultorioController', 'action' => 'editar', 'rol' => 'administrador'],
<<<<<<< HEAD
    'consultorios/detalle/:id' => ['controller' => 'ConsultorioController', 'action' => 'detalle', 'rol' => 'administrador'],
    //'consultorios/horarios' => ['controller' => 'ConsultorioController', 'action' => 'horarios', 'rol' => 'administrador'],
=======
    'consultorios/detalle' => ['controller' => 'ConsultorioController', 'action' => 'detalle', 'rol' => 'administrador'],
    'consultorios/detalle/:id' => ['controller' => 'ConsultorioController', 'action' => 'detalle', 'rol' => 'administrador'],
    'consultorios/horarios' => ['controller' => 'ConsultorioController', 'action' => 'horarios', 'rol' => 'administrador'],
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
    'consultorios/horarios/:id' => ['controller' => 'ConsultorioController', 'action' => 'horarios', 'rol' => 'administrador'], 
    'api/consultorios/listar' => ['controller' => 'ConsultorioController', 'action' => 'listar', 'method' => 'POST', 'rol' => 'administrador'],
    'api/consultorios/crear' => ['controller' => 'ConsultorioController', 'action' => 'crearConsultorio', 'method' => 'POST', 'rol' => 'administrador'],
    'api/consultorios/editar' => ['controller' => 'ConsultorioController', 'action' => 'editarConsultorio', 'method' => 'POST', 'rol' => 'administrador'],
    'api/consultorios/eliminar' => ['controller' => 'ConsultorioController', 'action' => 'eliminarConsultorio', 'method' => 'POST', 'rol' => 'administrador'],
    'api/consultorios/asignar-medico' => ['controller' => 'ConsultorioController', 'action' => 'asignarMedico', 'method' => 'POST', 'rol' => 'administrador'],
    'api/consultorios/remover-medico' => ['controller' => 'ConsultorioController', 'action' => 'removerMedico', 'method' => 'POST', 'rol' => 'administrador'],
    'api/consultorios/guardar-horario' => ['controller' => 'ConsultorioController', 'action' => 'guardarHorario', 'method' => 'POST', 'rol' => 'administrador'],
    'api/consultorios/obtener-detalle' => ['controller' => 'ConsultorioController', 'action' => 'obtenerDetalle', 'method' => 'POST', 'auth' => true],
    'api/consultorios/obtener-horarios' => ['controller' => 'ConsultorioController', 'action' => 'obtenerHorarios', 'method' => 'POST', 'auth' => true],
    'api/consultorios/estadisticas' => ['controller' => 'ConsultorioController', 'action' => 'obtenerEstadisticas', 'method' => 'POST', 'rol' => 'administrador'],
    'api/consultorios/listar-medicos' => ['controller' => 'ConsultorioController', 'action' => 'listarMedicosDisponibles', 'method' => 'POST', 'rol' => 'administrador'],
    
  


    // ==================== ESPECIALIDADES ====================
    'especialidades' => ['controller' => 'EspecialidadController', 'action' => 'index', 'rol' => 'administrador'],
    'especialidades/crear' => ['controller' => 'EspecialidadController', 'action' => 'crear', 'rol' => 'administrador'],
<<<<<<< HEAD
=======
    'especialidades/editar' => ['controller' => 'EspecialidadController', 'action' => 'editar', 'rol' => 'administrador'],
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
    'especialidades/editar/:id' => ['controller' => 'EspecialidadController', 'action' => 'editar', 'rol' => 'administrador'], 
    'especialidades/detalle/:id' => ['controller' => 'EspecialidadController', 'action' => 'detalle', 'rol' => 'administrador'],
    'especialidades/asignar-medico/:id' => ['controller' => 'EspecialidadController', 'action' => 'asignarMedico', 'rol' => 'administrador'],
    'api/especialidades/listar' => ['controller' => 'EspecialidadController', 'action' => 'listar', 'method' => 'POST', 'rol' => 'administrador'],
    'api/especialidades/estadisticas' => ['controller' => 'EspecialidadController', 'action' => 'obtenerEstadisticas', 'method' => 'POST', 'rol' => 'administrador'],
    'api/especialidades/obtener-detalle' => ['controller' => 'EspecialidadController', 'action' => 'obtenerDetalle', 'method' => 'POST', 'rol' => 'administrador'],
    'api/especialidades/crear' => ['controller' => 'EspecialidadController', 'action' => 'crearEspecialidad', 'method' => 'POST', 'rol' => 'administrador'],
    'api/especialidades/editar' => ['controller' => 'EspecialidadController', 'action' => 'editarEspecialidad', 'method' => 'POST', 'rol' => 'administrador'],
    'api/especialidades/eliminar' => ['controller' => 'EspecialidadController', 'action' => 'eliminarEspecialidad', 'method' => 'POST', 'rol' => 'administrador'],
    'api/especialidades/asignar-medico' => ['controller' => 'EspecialidadController', 'action' => 'asignarMedicoEspecialidad', 'method' => 'POST', 'rol' => 'administrador'],
    'api/especialidades/remover-medico' => ['controller' => 'EspecialidadController', 'action' => 'removerMedicoEspecialidad', 'method' => 'POST', 'rol' => 'administrador'],
    'api/especialidades/listar-medicos' => ['controller' => 'EspecialidadController', 'action' => 'listarMedicosDisponibles', 'method' => 'POST', 'auth' => true],
    
    // ==================== RECETAS ====================
    'recetas' => ['controller' => 'RecetaController', 'action' => 'index', 'rol' => ['medico', 'asistente', 'administrador']],
    'api/recetas/listar' => ['controller' => 'RecetaController', 'action' => 'listar', 'method' => 'POST', 'rol' => ['medico', 'asistente', 'administrador']],
    'api/recetas/crear' => ['controller' => 'RecetaController', 'action' => 'crear', 'method' => 'POST', 'rol' => 'medico'],
    'api/recetas/editar' => ['controller' => 'RecetaController', 'action' => 'editar', 'method' => 'POST', 'rol' => 'medico'],
    'api/recetas/borrar' => ['controller' => 'RecetaController', 'action' => 'borrar', 'method' => 'POST', 'rol' => 'medico'],
    'api/recetas/obtener' => ['controller' => 'RecetaController', 'action' => 'obtener', 'method' => 'POST', 'rol' => ['medico', 'asistente', 'administrador']],
    'api/recetas/buscar-pacientes' => ['controller' => 'RecetaController', 'action' => 'buscarPacientes', 'method' => 'POST', 'rol' => ['medico', 'asistente']],
    'api/recetas/mis-recetas' => ['controller' => 'RecetaController', 'action' => 'misRecetas', 'method' => 'POST', 'rol' => 'paciente'],
    'api/recetas/estadisticas' => ['controller' => 'RecetaController', 'action' => 'estadisticas', 'method' => 'POST', 'rol' => 'administrador'],
    
<<<<<<< HEAD
    // ==================== FACTURAS ====================
    'facturas' => ['controller' => 'FacturaController', 'action' => 'listar', 'rol' => ['asistente', 'administrador']],
    'factura/ver' => ['controller' => 'FacturaController', 'action' => 'ver', 'auth' => true],
    'paciente/facturas' => ['controller' => 'FacturaController', 'action' => 'misFacturas', 'rol' => 'paciente'],
    'api/facturas/generar' => ['controller' => 'FacturaController', 'action' => 'generar', 'method' => 'POST', 'rol' => ['asistente', 'administrador']],
    'api/facturas/listar' => ['controller' => 'FacturaController', 'action' => 'listarAPI', 'method' => 'POST', 'auth' => true],
    'api/facturas/obtener' => ['controller' => 'FacturaController', 'action' => 'obtener', 'method' => 'POST', 'auth' => true],
    'api/facturas/confirmar-pago' => ['controller' => 'FacturaController', 'action' => 'confirmarPago', 'method' => 'POST', 'auth' => true],
    'api/facturas/actualizar' => ['controller' => 'FacturaController', 'action' => 'actualizar', 'method' => 'POST', 'rol' => ['asistente', 'administrador']],
    'api/facturas/eliminar' => ['controller' => 'FacturaController', 'action' => 'eliminar', 'method' => 'POST', 'rol' => 'administrador'],
    
=======
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
    // ==================== CSRF ====================
    'api/csrf/token' => ['controller' => 'CSRFController', 'action' => 'getToken', 'method' => 'POST', 'auth' => false],
];