<?php
// vista/medico/med_catalogo.php
// Contenido principal para el dashboard del médico
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_medico = $id_medico ?? $_SESSION['usuario'] ?? 0;
?>

<!-- CSS Adicional para esta vista -->
<style>
    .welcome-stats {
        background: linear-gradient(135deg, #0d9488, #0f766e);
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .welcome-stats::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -5%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .welcome-stats::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -5%;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s;
        border: 1px solid #eef2f6;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.1);
    }
    .stat-card .stat-icon {
        position: absolute;
        right: 1rem;
        top: 1rem;
        font-size: 2rem;
        opacity: 0.1;
    }
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--bv-dark);
        margin-bottom: 0.25rem;
    }
    .stat-card .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--bv-text-light);
        letter-spacing: 0.5px;
    }
    .stat-card .stat-change {
        font-size: 0.7rem;
        margin-top: 0.5rem;
    }
    .stat-card .stat-change.up { color: #10b981; }
    .stat-card .stat-change.down { color: #ef4444; }
    
    .quick-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        text-decoration: none;
        display: block;
        border: 1px solid #eef2f6;
    }
    .quick-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12);
        text-decoration: none;
    }
    .quick-card .quick-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, rgba(13,148,136,0.1), rgba(15,118,110,0.1));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: #0d9488;
        transition: all 0.3s;
    }
    .quick-card:hover .quick-icon {
        background: linear-gradient(135deg, #0d9488, #0f766e);
        color: white;
        transform: scale(1.08) rotate(-5deg);
    }
    .quick-card h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--bv-dark);
        margin-bottom: 0.5rem;
    }
    .quick-card p {
        font-size: 0.8rem;
        color: var(--bv-text-light);
        margin-bottom: 0;
    }
    
    .activity-timeline {
        max-height: 400px;
        overflow-y: auto;
    }
    .timeline-item {
        display: flex;
        align-items: flex-start;
        padding: 0.75rem 0;
        border-bottom: 1px solid #eef2f6;
    }
    .timeline-item:last-child {
        border-bottom: none;
    }
    .timeline-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    .timeline-icon.receta { background: #d1fae5; color: #065f46; }
    .timeline-icon.paciente { background: #dbeafe; color: #1e40af; }
    .timeline-icon.cita { background: #fef3c7; color: #92400e; }
    .timeline-content {
        flex: 1;
    }
    .timeline-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--bv-dark);
        margin-bottom: 0.25rem;
    }
    .timeline-time {
        font-size: 0.7rem;
        color: var(--bv-text-light);
    }
    
    .appointment-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
        border-left: 3px solid #0d9488;
    }
    .appointment-card:hover {
        background: #f0fdf4;
        transform: translateX(3px);
    }
    .appointment-time {
        font-weight: 700;
        font-size: 0.8rem;
        color: #0d9488;
        font-family: monospace;
    }
    .appointment-patient {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--bv-dark);
    }
    .appointment-type {
        font-size: 0.7rem;
        color: var(--bv-text-light);
    }
    
    .info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .info-card h6 {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0d9488;
        margin-bottom: 0.75rem;
    }
    .info-card p {
        font-size: 0.75rem;
        color: var(--bv-text-light);
        margin-bottom: 0.5rem;
    }
    .btn-ver-todas {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
    }
    .empty-state {
        text-align: center;
        padding: 2rem;
        background: #fafbfc;
        border-radius: 12px;
    }
    .empty-state i {
        font-size: 2rem;
        color: #cbd5e1;
        margin-bottom: 0.5rem;
    }
    .empty-state p {
        font-size: 0.8rem;
        color: #94a3b8;
        margin-bottom: 0;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-md"></i> Panel del Médico</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Welcome Banner -->
        <div class="welcome-stats text-white bv-animate">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-stethoscope me-2"></i> 
                        Bienvenido, Dr(a). <?php echo htmlspecialchars($nombre_usuario); ?>
                    </h2>
                    <p class="mb-0 opacity-75">Resumen de tus pacientes y actividad del día.</p>
                    <div class="mt-2">
                        <span class="badge bg-white text-dark px-3 py-1 rounded-pill">
                            <i class="fas fa-calendar-alt me-1"></i> 
                            <?php echo date('l, d \d\e F \d\e Y'); ?>
                        </span>
                    </div>
                </div>
                <div class="d-none d-md-block">
                    <i class="fas fa-chart-line fa-3x" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-1">
                    <div class="stat-icon">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <div class="stat-value" id="total_recetas">0</div>
                    <div class="stat-label">Recetas Creadas</div>
                    <div class="stat-change up" id="recetas_cambio">
                        <i class="fas fa-arrow-up"></i> <span>0%</span> vs mes anterior
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-2">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value" id="total_pacientes">0</div>
                    <div class="stat-label">Pacientes Atendidos</div>
                    <div class="stat-change up" id="pacientes_cambio">
                        <i class="fas fa-arrow-up"></i> <span>0%</span> vs mes anterior
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-3">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-value" id="citas_hoy">0</div>
                    <div class="stat-label">Citas Hoy</div>
                    <div class="stat-change" id="citas_estado">
                        <i class="fas fa-info-circle"></i> <span>Pendientes</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Cards -->
        <div class="row mt-4">
            <div class="col-md-4">
                <a href="<?php echo APP_URL; ?>/recetas" class="quick-card bv-animate bv-animate-delay-1">
                    <div class="quick-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <h3>Recetario Electrónico</h3>
                    <p>Emite recetas y órdenes de estudios de forma digital.</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?php echo APP_URL; ?>/medico/pacientes" class="quick-card bv-animate bv-animate-delay-2">
                    <div class="quick-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Directorio de Pacientes</h3>
                    <p>Accede al historial clínico de tus pacientes.</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?php echo APP_URL; ?>/perfil" class="quick-card bv-animate bv-animate-delay-3">
                    <div class="quick-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h3>Mi Perfil</h3>
                    <p>Actualiza tus datos profesionales y de contacto.</p>
                </a>
            </div>
        </div>

        <!-- Actividad Reciente y Próximas Citas -->
        <div class="row mt-4">
            <div class="col-md-7">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history me-2"></i> Actividad Reciente
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" id="refreshActividad">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-timeline" id="actividad_reciente">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary spinner-border-sm"></div>
                                <p class="mt-2 mb-0 text-muted small">Cargando actividad...</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?php echo APP_URL; ?>/recetas" class="btn btn-link btn-sm btn-ver-todas">
                            Ver todas las recetas <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt me-2"></i> Próximas Citas
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0" id="proximas_citas">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary spinner-border-sm"></div>
                            <p class="mt-2 mb-0 text-muted small">Cargando citas...</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="#" class="btn btn-link btn-sm btn-ver-todas" id="verTodasCitas">
                            Ver todas las citas <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <!-- Información de Ayuda -->
                <div class="info-card">
                    <h6><i class="fas fa-info-circle"></i> Recordatorios</h6>
                    <p><i class="fas fa-check-circle text-success"></i> Las recetas electrónicas tienen validez legal</p>
                    <p><i class="fas fa-clock"></i> Recuerda revisar tus próximas citas</p>
                    <p><i class="fas fa-chart-line"></i> Puedes ver estadísticas detalladas en "Mi Perfil"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    console.log('=== PANEL DEL MÉDICO ===');
    console.log('ID Médico:', <?php echo $id_medico; ?>);
    
    // ==================== CARGAR ESTADÍSTICAS ====================
    
    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + '/api/medicos/mis-estadisticas',
            type: 'POST',
            data: { id_medico: <?php echo $id_medico; ?> },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Estadísticas recibidas:', response);
                
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                $('#total_recetas').text(data.total_recetas || 0);
                $('#total_pacientes').text(data.total_pacientes || 0);
                $('#citas_hoy').text(data.citas_hoy || 0);
                
                // Calcular cambios (simulados - puedes implementar con datos reales)
                let cambioRecetas = ((data.total_recetas || 0) - (data.recetas_mes_anterior || 0));
                let cambioPacientes = ((data.total_pacientes || 0) - (data.pacientes_mes_anterior || 0));
                
                if (cambioRecetas >= 0) {
                    $('#recetas_cambio').html(`<i class="fas fa-arrow-up"></i> <span>${Math.abs(cambioRecetas)}%</span> vs mes anterior`);
                } else {
                    $('#recetas_cambio').removeClass('up').addClass('down')
                        .html(`<i class="fas fa-arrow-down"></i> <span>${Math.abs(cambioRecetas)}%</span> vs mes anterior`);
                }
                
                if (cambioPacientes >= 0) {
                    $('#pacientes_cambio').html(`<i class="fas fa-arrow-up"></i> <span>${Math.abs(cambioPacientes)}%</span> vs mes anterior`);
                } else {
                    $('#pacientes_cambio').removeClass('up').addClass('down')
                        .html(`<i class="fas fa-arrow-down"></i> <span>${Math.abs(cambioPacientes)}%</span> vs mes anterior`);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estadísticas:', error);
                $('#total_recetas').text('0');
                $('#total_pacientes').text('0');
                $('#citas_hoy').text('0');
            }
        });
    }
    
    // ==================== CARGAR ACTIVIDAD RECIENTE ====================
    
    function cargarActividadReciente() {
        $.ajax({
            url: APP_URL + '/api/medicos/actividad-reciente',
            type: 'POST',
            data: { id_medico: <?php echo $id_medico; ?> },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Actividad reciente:', response);
                
                var actividades = [];
                if (response.success && response.data) {
                    actividades = response.data;
                } else if (Array.isArray(response)) {
                    actividades = response;
                }
                
                let html = '';
                
                if (actividades.length === 0) {
                    html = `
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No hay actividad reciente</p>
                        </div>
                    `;
                } else {
                    for (let i = 0; i < Math.min(actividades.length, 10); i++) {
                        let act = actividades[i];
                        let iconClass = '';
                        let iconColor = '';
                        
                        if (act.tipo === 'receta') {
                            iconClass = 'receta';
                            iconColor = 'fa-prescription-bottle-alt';
                        } else if (act.tipo === 'paciente') {
                            iconClass = 'paciente';
                            iconColor = 'fa-user-injured';
                        } else {
                            iconClass = 'cita';
                            iconColor = 'fa-calendar-check';
                        }
                        
                        html += `
                            <div class="timeline-item">
                                <div class="timeline-icon ${iconClass}">
                                    <i class="fas ${iconColor} fa-sm"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title">${escapeHtml(act.titulo || 'Actividad')}</div>
                                    <div class="timeline-time">
                                        <i class="far fa-clock"></i> ${act.fecha || ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                }
                
                $('#actividad_reciente').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar actividad:', error);
                $('#actividad_reciente').html(`
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error al cargar actividad</p>
                    </div>
                `);
            }
        });
    }
    
    // ==================== CARGAR PRÓXIMAS CITAS ====================
    
    function cargarProximasCitas() {
        $.ajax({
            url: APP_URL + '/api/medicos/proximas-citas',
            type: 'POST',
            data: { id_medico: <?php echo $id_medico; ?> },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Próximas citas:', response);
                
                var citas = [];
                if (response.success && response.data) {
                    citas = response.data;
                } else if (Array.isArray(response)) {
                    citas = response;
                }
                
                let html = '';
                
                if (citas.length === 0) {
                    html = `
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>No hay citas programadas</p>
                        </div>
                    `;
                } else {
                    for (let i = 0; i < Math.min(citas.length, 5); i++) {
                        let cita = citas[i];
                        html += `
                            <div class="appointment-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="appointment-time">
                                            <i class="fas fa-clock"></i> ${cita.hora || '--:--'}
                                        </div>
                                        <div class="appointment-patient">${escapeHtml(cita.paciente_nombre || 'Paciente')}</div>
                                        <div class="appointment-type">
                                            <i class="fas fa-stethoscope"></i> ${cita.tipo || 'Consulta general'}
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary btn-ver-cita" data-id="${cita.id_cita}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    }
                    
                    if (citas.length > 5) {
                        html += `
                            <div class="text-center mt-2">
                                <small class="text-muted">+${citas.length - 5} citas más</small>
                            </div>
                        `;
                    }
                }
                
                $('#proximas_citas').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar citas:', error);
                $('#proximas_citas').html(`
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error al cargar citas</p>
                    </div>
                `);
            }
        });
    }
    
    // ==================== EVENTOS ====================
    
    $('#refreshActividad').click(function() {
        cargarActividadReciente();
        cargarProximasCitas();
        cargarEstadisticas();
        mostrarToast('Datos actualizados', 'success');
    });
    
    $('#verTodasCitas').click(function(e) {
        e.preventDefault();
        mostrarToast('Funcionalidad en desarrollo', 'info');
    });
    
    $(document).on('click', '.btn-ver-cita', function() {
        let id = $(this).data('id');
        mostrarToast('Detalle de cita en desarrollo', 'info');
    });
    
    // ==================== FUNCIONES UTILITARIAS ====================
    
    function mostrarToast(mensaje, tipo) {
        var toastHtml = `
            <div class="toast align-items-center text-white bg-${tipo === 'success' ? 'success' : tipo === 'error' ? 'danger' : 'info'} border-0 position-fixed" 
                 style="top: 70px; right: 20px; z-index: 9999; min-width: 250px; border-radius: 12px;" 
                 role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${tipo === 'success' ? 'fa-check-circle' : tipo === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} me-2"></i>
                        ${mensaje}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        $('body').append(toastHtml);
        var toast = $('.toast').last();
        setTimeout(function() { toast.fadeOut(300, function() { $(this).remove(); }); }, 3000);
    }
    
    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    
    // ==================== INICIALIZAR ====================
    cargarEstadisticas();
    cargarActividadReciente();
    cargarProximasCitas();
    
    // Actualizar cada 60 segundos
    setInterval(function() {
        cargarEstadisticas();
        cargarActividadReciente();
        cargarProximasCitas();
    }, 60000);
});
</script>