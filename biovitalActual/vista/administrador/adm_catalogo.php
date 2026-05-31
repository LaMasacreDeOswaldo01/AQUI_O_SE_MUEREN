<?php
// vista/administrador/adm_catalogo.php
// Contenido principal del dashboard del administrador
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$stats_url = $stats_url ?? APP_URL . '/api/administradores/estadisticas-generales';
?>

<!-- Welcome Banner -->
<div class="bv-welcome-banner admin bv-animate">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-chalkboard-user me-2"></i> Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></h2>
            <p class="mb-0">Supervisa métricas operativas globales y gestiona el sistema.</p>
            <div class="bv-role-tag mt-2">
                <i class="fas fa-sliders-h"></i> Administrador
            </div>
        </div>
        <div class="d-none d-md-block">
            <i class="fas fa-chart-line fa-3x" style="opacity: 0.3;"></i>
        </div>
    </div>
</div>

<!-- Info Boxes - Estadísticas -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bv-animate bv-animate-delay-1">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-users"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">TOTAL USUARIOS</span>
                <span class="info-box-number" id="total_usuarios">0</span>
                <div class="progress">
                    <div class="progress-bar bg-info" style="width: 0%"></div>
                </div>
                <span class="progress-description">
                    <i class="fas fa-user-plus"></i> +<span id="usuarios_nuevos">0</span> este mes
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bv-animate bv-animate-delay-1">
            <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-prescription-bottle-alt"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">RECETAS</span>
                <span class="info-box-number" id="total_recetas">0</span>
                <div class="progress">
                    <div class="progress-bar bg-success" style="width: 0%"></div>
                </div>
                <span class="progress-description">
                    <i class="fas fa-calendar-week"></i> <span id="recetas_mes">0</span> este mes
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bv-animate bv-animate-delay-2">
            <span class="info-box-icon bg-warning elevation-1">
                <i class="fas fa-user-md"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">MÉDICOS</span>
                <span class="info-box-number" id="total_medicos">0</span>
                <div class="progress">
                    <div class="progress-bar bg-warning" style="width: 0%"></div>
                </div>
                <span class="progress-description">
                    <i class="fas fa-check-circle"></i> <span id="medicos_activos">0</span> activos
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bv-animate bv-animate-delay-2">
            <span class="info-box-icon bg-danger elevation-1">
                <i class="fas fa-user-friends"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">PACIENTES</span>
                <span class="info-box-number" id="total_pacientes">0</span>
                <div class="progress">
                    <div class="progress-bar bg-danger" style="width: 0%"></div>
                </div>
                <span class="progress-description">
                    <i class="fas fa-ambulance"></i> <span id="pacientes_atendidos">0</span> atendidos
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Segunda fila de estadísticas -->
<div class="row mt-3">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bv-animate bv-animate-delay-3">
            <span class="info-box-icon bg-primary elevation-1">
                <i class="fas fa-building"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">CONSULTORIOS</span>
                <span class="info-box-number" id="total_consultorios">0</span>
                <span class="progress-description">
                    <i class="fas fa-check-circle text-success"></i> <span id="consultorios_activos">0</span> activos
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bv-animate bv-animate-delay-3">
            <span class="info-box-icon bg-secondary elevation-1">
                <i class="fas fa-stethoscope"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">ESPECIALIDADES</span>
                <span class="info-box-number" id="total_especialidades">0</span>
                <span class="progress-description">
                    <i class="fas fa-check-circle text-success"></i> <span id="especialidades_activas">0</span> activas
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bv-animate bv-animate-delay-3">
            <span class="info-box-icon bg-purple elevation-1">
                <i class="fas fa-calendar-check"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">CITAS TOTALES</span>
                <span class="info-box-number" id="total_citas">0</span>
                <span class="progress-description">
                    <i class="fas fa-clock"></i> <span id="citas_pendientes">0</span> pendientes
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box bv-animate bv-animate-delay-3">
            <span class="info-box-icon bg-teal elevation-1">
                <i class="fas fa-chart-line"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">ACTIVIDAD</span>
                <span class="info-box-number" id="usuarios_activos_hoy">0</span>
                <span class="progress-description">
                    <i class="fas fa-user-clock"></i> conectados hoy
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access Cards - Grid de acceso rápido -->
<div class="bv-dash-grid mt-4">
    <a href="<?php echo APP_URL; ?>/administrador/usuarios" class="bv-dash-card admin bv-animate bv-animate-delay-1">
        <div class="bv-card-icon"><i class="fas fa-users-cog"></i></div>
        <h3>Gestión de Usuarios</h3>
        <p>Administra cuentas de pacientes, médicos y asistentes.</p>
        <span class="badge badge-light mt-2">Ver más <i class="fas fa-arrow-right ms-1"></i></span>
    </a>
    
    <a href="<?php echo APP_URL; ?>/consultorios" class="bv-dash-card admin bv-animate bv-animate-delay-2">
        <div class="bv-card-icon"><i class="fas fa-building"></i></div>
        <h3>Consultorios</h3>
        <p>Gestiona sedes, horarios y asignación de espacios.</p>
        <span class="badge badge-light mt-2">Ver más <i class="fas fa-arrow-right ms-1"></i></span>
    </a>
    
    <a href="<?php echo APP_URL; ?>/especialidades" class="bv-dash-card admin bv-animate bv-animate-delay-2">
        <div class="bv-card-icon"><i class="fas fa-stethoscope"></i></div>
        <h3>Especialidades</h3>
        <p>Administra especialidades médicas y tarifas.</p>
        <span class="badge badge-light mt-2">Ver más <i class="fas fa-arrow-right ms-1"></i></span>
    </a>
    
    <a href="<?php echo APP_URL; ?>/recetas" class="bv-dash-card admin bv-animate bv-animate-delay-3">
        <div class="bv-card-icon"><i class="fas fa-prescription"></i></div>
        <h3>Recetas Médicas</h3>
        <p>Supervisa y audita todas las recetas del sistema.</p>
        <span class="badge badge-light mt-2">Ver más <i class="fas fa-arrow-right ms-1"></i></span>
    </a>
</div>

<!-- Actividad Reciente y Acciones Rápidas -->
<div class="row mt-4">
    <div class="col-md-7">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock me-2"></i> Actividad Reciente
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="timeline timeline-inverse" id="actividad-reciente">
                    <div class="time-label">
                        <span class="bg-info">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="#" class="btn btn-sm btn-link">Ver todas las actividades</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-5">
        <!-- Acciones Rápidas -->
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt me-2"></i> Acciones Rápidas
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <button class="btn btn-outline-primary btn-block" onclick="window.location.href='<?php echo APP_URL; ?>/consultorios/crear'">
                            <i class="fas fa-plus-circle"></i> Nuevo Consultorio
                        </button>
                    </div>
                    <div class="col-6 mb-3">
                        <button class="btn btn-outline-success btn-block" onclick="window.location.href='<?php echo APP_URL; ?>/especialidades/crear'">
                            <i class="fas fa-plus-circle"></i> Nueva Especialidad
                        </button>
                    </div>
                    <div class="col-6 mb-3">
                        <button class="btn btn-outline-info btn-block" onclick="window.location.href='<?php echo APP_URL; ?>/administrador/usuarios'">
                            <i class="fas fa-user-plus"></i> Registrar Usuario
                        </button>
                    </div>
                    <div class="col-6 mb-3">
                        <button class="btn btn-outline-warning btn-block" onclick="window.location.href='<?php echo APP_URL; ?>/perfil'">
                            <i class="fas fa-user-edit"></i> Mi Perfil
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Notas del Sistema -->
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle me-2"></i> Información del Sistema
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-code-branch text-primary me-2"></i>
                        <strong>Versión:</strong> <?php echo APP_VERSION; ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-calendar-alt text-success me-2"></i>
                        <strong>Última actualización:</strong> <span id="fecha_actualizacion">Cargando...</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-database text-warning me-2"></i>
                        <strong>Base de datos:</strong> Operativa
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-shield-alt text-danger me-2"></i>
                        <strong>Seguridad:</strong> CSRF Protegido
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Cargar estadísticas usando la URL proporcionada
    cargarEstadisticas();
    cargarActividadReciente();
    
    function cargarEstadisticas() {
        $.ajax({
            url: '<?php echo $stats_url; ?>',
            type: 'POST',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Estadísticas recibidas:', response);
                
                // Manejar formato ApiResponse
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                // Actualizar números
                $('#total_usuarios').text(data.total_usuarios || 0);
                $('#total_recetas').text(data.total_recetas || 0);
                $('#total_medicos').text(data.total_medicos || 0);
                $('#total_pacientes').text(data.total_pacientes || 0);
                $('#total_consultorios').text(data.total_consultorios || 0);
                $('#total_especialidades').text(data.total_especialidades || 0);
                $('#total_citas').text(data.total_citas || 0);
                $('#usuarios_activos_hoy').text(data.usuarios_activos_hoy || 0);
                
                // Actualizar sub-estadísticas
                $('#usuarios_nuevos').text(data.usuarios_nuevos_mes || 0);
                $('#recetas_mes').text(data.recetas_mes || 0);
                $('#medicos_activos').text(data.medicos_activos || 0);
                $('#pacientes_atendidos').text(data.pacientes_atendidos_mes || 0);
                $('#consultorios_activos').text(data.consultorios_activos || 0);
                $('#especialidades_activas').text(data.especialidades_activas || 0);
                $('#citas_pendientes').text(data.citas_pendientes || 0);
                
                // Actualizar fecha de actualización
                $('#fecha_actualizacion').text(moment().format('DD/MM/YYYY HH:mm'));
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estadísticas:', error);
                // Datos de respaldo en caso de error
                $('#total_usuarios').text('0');
                $('#total_recetas').text('0');
                $('#total_medicos').text('0');
                $('#total_pacientes').text('0');
                $('#fecha_actualizacion').text('No disponible');
            }
        });
    }
    
    function cargarActividadReciente() {
        $.ajax({
            url: '<?php echo APP_URL; ?>/api/administradores/actividad-reciente',
            type: 'POST',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                var actividades = [];
                if (response.success && response.data) {
                    actividades = response.data;
                } else if (Array.isArray(response)) {
                    actividades = response;
                }
                
                var timelineHtml = '';
                
                if (actividades.length === 0) {
                    timelineHtml = `
                        <div class="time-label">
                            <span class="bg-secondary">Sin actividad</span>
                        </div>
                        <div class="text-center p-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>No hay actividad reciente para mostrar</p>
                        </div>
                    `;
                } else {
                    // Agrupar actividades por fecha
                    var grupos = {};
                    actividades.forEach(function(act) {
                        var fecha = act.fecha || act.created_at;
                        var fechaKey = fecha.split(' ')[0];
                        if (!grupos[fechaKey]) grupos[fechaKey] = [];
                        grupos[fechaKey].push(act);
                    });
                    
                    var fechas = Object.keys(grupos).sort().reverse();
                    
                    fechas.forEach(function(fecha) {
                        var fechaLabel = formatearFecha(fecha);
                        timelineHtml += `
                            <div class="time-label">
                                <span class="bg-info">${fechaLabel}</span>
                            </div>
                        `;
                        
                        grupos[fecha].slice(0, 5).forEach(function(act) {
                            var icono = getIconoActividad(act.tipo);
                            var color = getColorActividad(act.tipo);
                            var hora = act.fecha ? act.fecha.split(' ')[1].substring(0, 5) : '--:--';
                            
                            timelineHtml += `
                                <div>
                                    <i class="fas ${icono} ${color}"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> ${hora}</span>
                                        <h3 class="timeline-header">${act.usuario || 'Usuario'}</h3>
                                        <div class="timeline-body">
                                            ${act.descripcion || 'Realizó una acción en el sistema'}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    });
                    
                    if (fechas.length === 0) {
                        timelineHtml = `
                            <div class="time-label">
                                <span class="bg-secondary">Sin actividad</span>
                            </div>
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No hay actividad reciente para mostrar</p>
                            </div>
                        `;
                    }
                }
                
                $('#actividad-reciente').html(timelineHtml);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar actividad:', error);
                $('#actividad-reciente').html(`
                    <div class="time-label">
                        <span class="bg-danger">Error</span>
                    </div>
                    <div class="text-center p-4 text-muted">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>No se pudo cargar la actividad reciente</p>
                    </div>
                `);
            }
        });
    }
    
    function formatearFecha(fecha) {
        var hoy = moment().format('YYYY-MM-DD');
        var ayer = moment().subtract(1, 'days').format('YYYY-MM-DD');
        
        if (fecha === hoy) return 'HOY';
        if (fecha === ayer) return 'AYER';
        return moment(fecha).format('DD [de] MMMM');
    }
    
    function getIconoActividad(tipo) {
        var iconos = {
            'usuario': 'fa-user',
            'receta': 'fa-prescription-bottle-alt',
            'consultorio': 'fa-building',
            'especialidad': 'fa-stethoscope',
            'cita': 'fa-calendar-check',
            'default': 'fa-bell'
        };
        return iconos[tipo] || iconos['default'];
    }
    
    function getColorActividad(tipo) {
        var colores = {
            'usuario': 'bg-primary',
            'receta': 'bg-success',
            'consultorio': 'bg-info',
            'especialidad': 'bg-warning',
            'cita': 'bg-teal',
            'default': 'bg-secondary'
        };
        return colores[tipo] || colores['default'];
    }
});

// Función para actualizar estadísticas (puede llamarse desde otros componentes)
function actualizarEstadisticas() {
    location.reload();
}
</script>

<!-- Agregar Moment.js para formateo de fechas si no está disponible -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>