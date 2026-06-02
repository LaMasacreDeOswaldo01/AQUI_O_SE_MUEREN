<?php
// vista/asistente/asi_catalogo.php
// Contenido principal para el dashboard del asistente
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_asistente = $id_asistente ?? $_SESSION['usuario'] ?? 0;
?>

<!-- CSS Adicional para esta vista -->
<style>
    .welcome-stats {
        background: linear-gradient(135deg, #9333ea, #7c3aed);
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
        color: #4c1d95;
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
        background: linear-gradient(135deg, rgba(147,51,234,0.1), rgba(124,58,237,0.1));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: #9333ea;
        transition: all 0.3s;
    }
    .quick-card:hover .quick-icon {
        background: linear-gradient(135deg, #9333ea, #7c3aed);
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
    .timeline-icon.consulta { background: #fef3c7; color: #92400e; }
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
    
    .info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .info-card h6 {
        font-size: 0.85rem;
        font-weight: 700;
        color: #9333ea;
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
    .badge-hoy {
        background: #e0e7ff;
        color: #4338ca;
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-clipboard-list"></i> Panel de Asistencia</h1>
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
                        <i class="fas fa-clipboard-list me-2"></i> 
                        Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?>
                    </h2>
                    <p class="mb-0 opacity-75">Gestión de consultorios y preparación de pacientes.</p>
                    <div class="mt-2">
                        <span class="badge bg-white text-dark px-3 py-1 rounded-pill">
                            <i class="fas fa-calendar-alt me-1"></i> 
                            <?php echo date('l, d \d\e F \d\e Y'); ?>
                        </span>
                        <span class="badge-hoy ms-2">
                            <i class="fas fa-chart-line"></i> Turno: Mañana
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
            <div class="col-lg-3 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-1">
                    <div class="stat-icon">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <div class="stat-value" id="total_recetas">0</div>
                    <div class="stat-label">Recetas Hoy</div>
                    <div class="stat-change up">
                        <i class="fas fa-chart-line"></i> <span>Gestión de recetas</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-2">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value" id="pacientes_hoy">0</div>
                    <div class="stat-label">Pacientes Atendidos</div>
                    <div class="stat-change up">
                        <i class="fas fa-user-plus"></i> <span>Hoy</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-3">
                    <div class="stat-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-value" id="medicos_activos">0</div>
                    <div class="stat-label">Médicos Activos</div>
                    <div class="stat-change up">
                        <i class="fas fa-stethoscope"></i> <span>En consulta</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-3">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value" id="promedio_espera">0</div>
                    <div class="stat-label">Tiempo Promedio</div>
                    <div class="stat-change up">
                        <i class="fas fa-hourglass-half"></i> <span>min por paciente</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Cards -->
        <div class="row mt-4">
            <div class="col-md-4">
                <a href="<?php echo APP_URL; ?>/recetas" class="quick-card bv-animate bv-animate-delay-1">
                    <div class="quick-icon">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <h3>Recetas</h3>
                    <p>Gestiona y visualiza las recetas médicas del sistema.</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="quick-card bv-animate bv-animate-delay-2" id="btnTriaje">
                    <div class="quick-icon">
                        <i class="fas fa-procedures"></i>
                    </div>
                    <h3>Triaje</h3>
                    <p>Registra signos vitales y motivos de consulta previos a la atención.</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?php echo APP_URL; ?>/perfil" class="quick-card bv-animate bv-animate-delay-3">
                    <div class="quick-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h3>Mi Perfil</h3>
                    <p>Actualiza tu información personal y datos de contacto.</p>
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
                            <i class="fas fa-calendar-alt me-2"></i> Citas de Hoy
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" id="refreshCitas">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0" id="citas_hoy">
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
                    <h6><i class="fas fa-info-circle"></i> Recordatorios del día</h6>
                    <p><i class="fas fa-check-circle text-success"></i> <strong>Consultorios disponibles:</strong> Todos los consultorios operativos</p>
                    <p><i class="fas fa-clock"></i> <strong>Horario especial:</strong> Hoy atención continua de 8am a 4pm</p>
                    <p><i class="fas fa-prescription"></i> <strong>Recetas pendientes:</strong> Verifica las recetas por entregar</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    console.log('=== PANEL DEL ASISTENTE ===');
    console.log('ID Asistente:', <?php echo $id_asistente; ?>);
    
    // ==================== CARGAR ESTADÍSTICAS ====================
    
    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + '/api/asistentes/mis-estadisticas',
            type: 'POST',
            data: { id_asistente: <?php echo $id_asistente; ?> },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Estadísticas recibidas:', response);
                
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                $('#total_recetas').text(data.recetas_hoy || 0);
                $('#pacientes_hoy').text(data.pacientes_hoy || 0);
                $('#medicos_activos').text(data.medicos_activos || 0);
                $('#promedio_espera').text(data.promedio_espera || 15);
                
                // Animación de conteo
                animarContador('total_recetas', data.recetas_hoy || 0);
                animarContador('pacientes_hoy', data.pacientes_hoy || 0);
                animarContador('medicos_activos', data.medicos_activos || 0);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estadísticas:', error);
                $('#total_recetas').text('0');
                $('#pacientes_hoy').text('0');
                $('#medicos_activos').text('0');
                $('#promedio_espera').text('15');
            }
        });
    }
    
    function animarContador(elementoId, valorFinal) {
        let elemento = $('#' + elementoId);
        let valorActual = 0;
        let incremento = Math.ceil(valorFinal / 30);
        
        if (valorFinal === 0) {
            elemento.text('0');
            return;
        }
        
        let intervalo = setInterval(function() {
            valorActual += incremento;
            if (valorActual >= valorFinal) {
                elemento.text(valorFinal);
                clearInterval(intervalo);
            } else {
                elemento.text(valorActual);
            }
        }, 30);
    }
    
    // ==================== CARGAR ACTIVIDAD RECIENTE ====================
    
    function cargarActividadReciente() {
        $.ajax({
            url: APP_URL + '/api/asistentes/actividad-reciente',
            type: 'POST',
            data: { id_asistente: <?php echo $id_asistente; ?> },
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
                            iconClass = 'consulta';
                            iconColor = 'fa-stethoscope';
                        }
                        
                        let titulo = act.titulo || 'Actividad registrada';
                        let fecha = act.fecha ? formatearFecha(act.fecha) : 'Fecha no disponible';
                        
                        html += `
                            <div class="timeline-item">
                                <div class="timeline-icon ${iconClass}">
                                    <i class="fas ${iconColor} fa-sm"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title">${escapeHtml(titulo)}</div>
                                    <div class="timeline-time">
                                        <i class="far fa-clock"></i> ${escapeHtml(fecha)}
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
    
    // ==================== CARGAR CITAS DE HOY ====================
    
    function cargarCitasHoy() {
        $.ajax({
            url: APP_URL + '/api/asistentes/citas-hoy',
            type: 'POST',
            data: { id_asistente: <?php echo $id_asistente; ?> },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Citas de hoy:', response);
                
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
                            <p>No hay citas programadas para hoy</p>
                        </div>
                    `;
                } else {
                    for (let i = 0; i < citas.length; i++) {
                        let cita = citas[i];
                        let estadoClass = cita.estado === 'completada' ? 'text-success' : 'text-warning';
                        let estadoIcono = cita.estado === 'completada' ? 'fa-check-circle' : 'fa-clock';
                        
                        html += `
                            <div class="timeline-item">
                                <div class="timeline-icon consulta">
                                    <i class="fas ${estadoIcono} fa-sm"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title">
                                        ${escapeHtml(cita.paciente_nombre || 'Paciente')}
                                        <span class="float-right ${estadoClass}">${cita.hora || '--:--'}</span>
                                    </div>
                                    <div class="timeline-time">
                                        <i class="fas fa-user-md"></i> Dr(a). ${escapeHtml(cita.medico_nombre || 'Médico asignado')}
                                        <span class="float-right">
                                            <i class="fas fa-stethoscope"></i> ${escapeHtml(cita.consultorio || 'Consultorio')}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                }
                
                $('#citas_hoy').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar citas:', error);
                $('#citas_hoy').html(`
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error al cargar las citas de hoy</p>
                    </div>
                `);
            }
        });
    }
    
    function formatearFecha(fecha) {
        if (!fecha) return '';
        let date = new Date(fecha);
        let ahora = new Date();
        let hoy = new Date(ahora.getFullYear(), ahora.getMonth(), ahora.getDate());
        let ayer = new Date(hoy);
        ayer.setDate(ayer.getDate() - 1);
        
        if (date >= hoy) {
            return 'Hoy, ' + date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        } else if (date >= ayer) {
            return 'Ayer, ' + date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        } else {
            return date.toLocaleDateString('es-ES');
        }
    }
    
    // ==================== EVENTOS ====================
    
    $('#refreshActividad').click(function() {
        cargarActividadReciente();
        cargarEstadisticas();
        mostrarToast('Datos actualizados', 'success');
    });
    
    $('#refreshCitas').click(function() {
        cargarCitasHoy();
        mostrarToast('Citas actualizadas', 'success');
    });
    
    $('#btnTriaje, #verTodasCitas').click(function(e) {
        e.preventDefault();
        mostrarToast('Funcionalidad en desarrollo', 'info');
    });
    
    // ==================== FUNCIONES UTILITARIAS ====================
    
    function mostrarToast(mensaje, tipo) {
        var toastHtml = `
            <div class="toast align-items-center text-white bg-${tipo === 'success' ? 'success' : 'info'} border-0 position-fixed" 
                 style="top: 70px; right: 20px; z-index: 9999; min-width: 250px; border-radius: 12px;" 
                 role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-info-circle'} me-2"></i>
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
    cargarCitasHoy();
    
    // Actualizar cada 60 segundos
    setInterval(function() {
        cargarEstadisticas();
        cargarActividadReciente();
        cargarCitasHoy();
    }, 60000);
});
</script>