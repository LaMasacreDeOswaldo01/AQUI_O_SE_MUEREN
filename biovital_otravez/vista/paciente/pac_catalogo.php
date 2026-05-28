<?php
// vista/paciente/pac_catalogo.php
// Contenido principal para el dashboard del paciente
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_paciente = $id_paciente ?? $_SESSION['usuario'] ?? 0;
?>

<!-- CSS Adicional para esta vista -->
<style>
    .welcome-stats {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
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
        background: linear-gradient(135deg, rgba(0,119,182,0.1), rgba(67,97,238,0.1));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: var(--bv-primary);
        transition: all 0.3s;
    }
    .quick-card:hover .quick-icon {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
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
    .timeline-icon.cita { background: #dbeafe; color: #1e40af; }
    .timeline-icon.recordatorio { background: #fef3c7; color: #92400e; }
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
        color: var(--bv-primary);
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
    .btn-receta-rapida {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-receta-rapida:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,119,182,0.3);
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-injured"></i> Panel del Paciente</h1>
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
                        <i class="fas fa-smile-wink me-2"></i> 
                        Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?>
                    </h2>
                    <p class="mb-0 opacity-75">Gestiona tus citas, historial y estudios desde un solo lugar.</p>
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
            <div class="col-lg-3 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-1">
                    <div class="stat-icon">
                        <i class="fas fa-prescription-bottle-alt"></i>
                    </div>
                    <div class="stat-value" id="total_recetas">0</div>
                    <div class="stat-label">Mis Recetas</div>
                    <div class="stat-change up" id="recetas_cambio">
                        <i class="fas fa-chart-line"></i> <span>Historial médico</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-2">
                    <div class="stat-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-value" id="total_medicos">0</div>
                    <div class="stat-label">Médicos Atendidos</div>
                    <div class="stat-change up" id="medicos_cambio">
                        <i class="fas fa-chart-line"></i> <span>Especialistas</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-3">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-value" id="proximas_citas">0</div>
                    <div class="stat-label">Próximas Citas</div>
                    <div class="stat-change" id="citas_cambio">
                        <i class="fas fa-clock"></i> <span>Pendientes</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="stat-card bv-animate bv-animate-delay-3">
                    <div class="stat-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="stat-value" id="total_estudios">0</div>
                    <div class="stat-label">Estudios Realizados</div>
                    <div class="stat-change up" id="estudios_cambio">
                        <i class="fas fa-flask"></i> <span>Laboratorio</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Cards -->
        <div class="row mt-4">
            <div class="col-md-4">
                <a href="<?php echo APP_URL; ?>/paciente/recetas" class="quick-card bv-animate bv-animate-delay-1">
                    <div class="quick-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <h3>Historial Médico</h3>
                    <p>Accede a tus reportes, recetas e informes médicos previos.</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="quick-card bv-animate bv-animate-delay-2" id="btnMisCitas">
                    <div class="quick-icon">
                        <i class="far fa-calendar-check"></i>
                    </div>
                    <h3>Mis Citas</h3>
                    <p>Revisa tus próximas citas o programa una nueva consulta.</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?php echo APP_URL; ?>/perfil" class="quick-card bv-animate bv-animate-delay-3">
                    <div class="quick-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h3>Datos Personales</h3>
                    <p>Actualiza tu información personal y datos de contacto.</p>
                </a>
            </div>
        </div>

        <!-- Actividad Reciente y Recomendaciones -->
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
                        <a href="<?php echo APP_URL; ?>/paciente/recetas" class="btn btn-link btn-sm btn-ver-todas">
                            Ver todas las recetas <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="card card-info card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-lightbulb me-2"></i> Recomendaciones
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Mantén tu perfil actualizado</strong>
                                <p class="text-muted small mt-1">Revisa que tus datos de contacto estén correctos.</p>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-prescription-bottle-alt text-info me-2"></i>
                                <strong>Tus recetas electrónicas</strong>
                                <p class="text-muted small mt-1">Puedes ver e imprimir tus recetas desde el historial.</p>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-calendar-alt text-warning me-2"></i>
                                <strong>Programa tus citas</strong>
                                <p class="text-muted small mt-1">Agenda consultas con tus médicos de confianza.</p>
                            </li>
                            <li>
                                <i class="fas fa-shield-alt text-danger me-2"></i>
                                <strong>Tus datos están seguros</strong>
                                <p class="text-muted small mt-1">Tu información médica está protegida.</p>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-receta-rapida btn-sm" id="btnIrRecetas">
                            <i class="fas fa-prescription"></i> Ver mis recetas
                        </button>
                    </div>
                </div>

                <!-- Acceso Rápido a Recetas -->
                <div class="info-card">
                    <h6><i class="fas fa-prescription"></i> Acceso rápido</h6>
                    <p><i class="fas fa-check-circle text-success"></i> Últimas recetas disponibles en tu historial</p>
                    <p><i class="fas fa-print"></i> Puedes imprimir tus recetas desde cualquier dispositivo</p>
                    <p><i class="fas fa-envelope"></i> Recibe notificaciones sobre tus citas y recetas</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    console.log('=== PANEL DEL PACIENTE ===');
    console.log('ID Paciente:', <?php echo $id_paciente; ?>);
    
    // ==================== CARGAR ESTADÍSTICAS ====================
    
    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + '/api/pacientes/mis-estadisticas',
            type: 'POST',
            data: { id_paciente: <?php echo $id_paciente; ?> },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Estadísticas recibidas:', response);
                
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                $('#total_recetas').text(data.total_recetas || 0);
                $('#total_medicos').text(data.medicos_atendieron || 0);
                $('#proximas_citas').text(data.proximas_citas || 0);
                $('#total_estudios').text(data.total_estudios || 0);
                
                // Animación de conteo
                animarContador('total_recetas', data.total_recetas || 0);
                animarContador('total_medicos', data.medicos_atendieron || 0);
                animarContador('proximas_citas', data.proximas_citas || 0);
                animarContador('total_estudios', data.total_estudios || 0);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estadísticas:', error);
                $('#total_recetas').text('0');
                $('#total_medicos').text('0');
                $('#proximas_citas').text('0');
                $('#total_estudios').text('0');
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
            url: APP_URL + '/api/pacientes/actividad-reciente',
            type: 'POST',
            data: { id_paciente: <?php echo $id_paciente; ?> },
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
                        } else if (act.tipo === 'cita') {
                            iconClass = 'cita';
                            iconColor = 'fa-calendar-check';
                        } else {
                            iconClass = 'recordatorio';
                            iconColor = 'fa-bell';
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
    
    // ==================== EVENTOS ====================
    
    $('#refreshActividad').click(function() {
        cargarActividadReciente();
        cargarEstadisticas();
        mostrarToast('Datos actualizados', 'success');
    });
    
    $('#btnIrRecetas, #btnMisCitas').click(function(e) {
        e.preventDefault();
        let btnId = $(this).attr('id');
        if (btnId === 'btnIrRecetas') {
            window.location.href = APP_URL + '/paciente/recetas';
        } else {
            mostrarToast('Próximamente disponible', 'info');
        }
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
    
    // Actualizar cada 60 segundos
    setInterval(function() {
        cargarEstadisticas();
        cargarActividadReciente();
    }, 60000);
});
</script>