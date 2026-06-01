<?php
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_paciente = $id_paciente ?? $_SESSION['usuario'] ?? 0;
?>
<style>
    .stats-card {
        background: white;
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s;
        border: 1px solid #eef2f6;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .stats-card .stats-number {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--bv-primary);
    }
    .stats-card .stats-label {
        font-size: 0.7rem;
        color: var(--bv-text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .cita-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #eef2f6;
        transition: all 0.3s;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .cita-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .cita-fecha {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        text-align: center;
        padding: 1rem;
        min-width: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .cita-fecha .dia {
        font-size: 1.8rem;
        font-weight: 800;
        line-height: 1;
    }
    .cita-fecha .mes {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .cita-info {
        padding: 1rem;
        flex: 1;
    }
    .cita-especialidad {
        font-weight: 700;
        color: var(--bv-dark);
        margin-bottom: 0.25rem;
    }
    .cita-medico {
        font-size: 0.85rem;
        color: var(--bv-text-light);
        margin-bottom: 0.5rem;
    }
    .cita-medico i {
        color: #0d9488;
        margin-right: 0.25rem;
    }
    .cita-detalle {
        font-size: 0.75rem;
        color: var(--bv-text-light);
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #eef2f6;
    }
    .badge-estado {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .badge-pendiente { background: #fef3c7; color: #92400e; }
    .badge-confirmada { background: #dbeafe; color: #1e40af; }
    .badge-completada { background: #d1fae5; color: #065f46; }
    .badge-cancelada { background: #fee2e2; color: #991b1b; }
    
    .filtro-btn {
        background: none;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-weight: 600;
        transition: all 0.2s;
        cursor: pointer;
    }
    .filtro-btn.active {
        background: var(--bv-primary);
        color: white;
    }
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: #fafbfc;
        border-radius: 16px;
    }
    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-calendar-alt"></i> Mis Citas</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Welcome Banner -->
        <div class="bv-welcome-banner bv-animate">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-calendar-check me-2"></i> Mis Citas Médicas</h2>
                    <p class="mb-0">Agenda y gestiona tus consultas médicas de forma fácil y rápida.</p>
                    <div class="bv-role-tag mt-2">
                        <i class="fas fa-user-injured"></i> Paciente
                    </div>
                </div>
                <div class="d-none d-md-block">
                    <a href="<?php echo APP_URL; ?>/paciente/citas/agendar" class="btn btn-light btn-lg">
                        <i class="fas fa-plus-circle"></i> Nueva Cita
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-1">
                    <div class="stats-number" id="proximas">0</div>
                    <div class="stats-label">Próximas</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-2">
                    <div class="stats-number" id="completadas">0</div>
                    <div class="stats-label">Completadas</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-3">
                    <div class="stats-number" id="canceladas">0</div>
                    <div class="stats-label">Canceladas</div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-2">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="filtro-btn active" data-filtro="proximas">
                                <i class="fas fa-calendar-week"></i> Próximas
                            </button>
                            <button class="filtro-btn" data-filtro="completadas">
                                <i class="fas fa-check-circle"></i> Completadas
                            </button>
                            <button class="filtro-btn" data-filtro="canceladas">
                                <i class="fas fa-ban"></i> Canceladas
                            </button>
                            <button class="filtro-btn" data-filtro="todas">
                                <i class="fas fa-list"></i> Todas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de Citas -->
        <div class="row mt-3">
            <div class="col-12">
                <div id="contenedor_citas">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando tus citas...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón Nueva Cita (visible en móvil) -->
        <div class="row mt-3 d-md-none">
            <div class="col-12">
                <a href="<?php echo APP_URL; ?>/paciente/citas/agendar" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-plus-circle"></i> Nueva Cita
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Modal Detalle Cita -->
<div class="modal fade modal-bv" id="modalDetalleCita" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent)); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check"></i> Detalle de Cita
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="detalle_cita_content">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger btn-cancelar-cita" style="display: none;">
                    <i class="fas fa-ban"></i> Cancelar Cita
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('=== MIS CITAS ===');
    
    let filtroActual = 'proximas';
    let citasData = [];
    let citaSeleccionada = null;
    
    // Cargar estadísticas
    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + '/api/citas/estadisticas',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                $('#proximas').text(data.proximas || 0);
                $('#completadas').text(data.completadas || 0);
                $('#canceladas').text(data.canceladas || 0);
            },
            error: function() {
                $('#proximas').text('0');
                $('#completadas').text('0');
                $('#canceladas').text('0');
            }
        });
    }
    
    // Cargar citas
    function cargarCitas() {
        $('#contenedor_citas').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-2">Cargando tus citas...</p>
            </div>
        `);
        
        $.ajax({
            url: APP_URL + '/api/citas/listar',
            type: 'POST',
            data: { filtro: filtroActual },
            dataType: 'json',
            success: function(response) {
                var citas = [];
                if (response.success && response.data) {
                    citas = response.data;
                } else if (Array.isArray(response)) {
                    citas = response;
                }
                
                citasData = citas;
                renderizarCitas(citas);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar citas:', error);
                $('#contenedor_citas').html(`
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error al cargar tus citas</p>
                    </div>
                `);
            }
        });
    }
    
    function renderizarCitas(citas) {
        let html = '';
        
        if (citas.length === 0) {
            html = `
                <div class="empty-state">
                    <i class="far fa-calendar-times"></i>
                    <p>No tienes citas ${filtroActual === 'proximas' ? 'próximas' : filtroActual}</p>
                    <a href="${APP_URL}/paciente/citas/agendar" class="btn btn-primary mt-3">
                        <i class="fas fa-plus-circle"></i> Agendar Cita
                    </a>
                </div>
            `;
        } else {
            for (let cita of citas) {
                let estadoClass = '';
                let estadoTexto = '';
                switch(cita.estado) {
                    case 'pendiente': estadoClass = 'badge-pendiente'; estadoTexto = 'Programada'; break;
                    case 'confirmada': estadoClass = 'badge-confirmada'; estadoTexto = 'Confirmada'; break;
                    case 'completada': estadoClass = 'badge-completada'; estadoTexto = 'Completada'; break;
                    case 'cancelada': estadoClass = 'badge-cancelada'; estadoTexto = 'Cancelada'; break;
                    default: estadoClass = 'badge-pendiente'; estadoTexto = 'Programada';
                }
                
                html += `
                    <div class="cita-card d-flex flex-column flex-sm-row" data-id="${cita.id_cita}">
                        <div class="cita-fecha text-center">
                            <div class="dia">${cita.fecha}</div>
                            <div class="mes">${cita.mes}</div>
                        </div>
                        <div class="cita-info">
                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                <div>
                                    <div class="cita-especialidad">
                                        <i class="fas fa-stethoscope"></i> ${escapeHtml(cita.especialidad)}
                                    </div>
                                    <div class="cita-medico">
                                        <i class="fas fa-user-md"></i> Dr(a). ${escapeHtml(cita.medico)}
                                    </div>
                                </div>
                                <div class="text-sm-right mt-2 mt-sm-0">
                                    <span class="badge-estado ${estadoClass}">${estadoTexto}</span>
                                </div>
                            </div>
                            <div class="cita-detalle d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <i class="fas fa-clock"></i> ${cita.hora} hs
                                </div>
                                <div class="mt-2 mt-sm-0">
                                    <button class="btn btn-info btn-sm btn-ver-detalle" data-id="${cita.id_cita}">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </button>
                                    ${cita.estado !== 'cancelada' ? `
                                    <button class="btn btn-success btn-sm btn-ver-factura" data-id="${cita.id_cita}">
                                        <i class="fas fa-file-invoice-dollar"></i> Ver Factura
                                    </button>
                                    ` : ''}
                                    ${cita.estado !== 'cancelada' && cita.estado !== 'completada' ? `
                                    <button class="btn btn-danger btn-sm btn-cancelar" data-id="${cita.id_cita}">
                                        <i class="fas fa-ban"></i> Cancelar
                                    </button>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
        
        $('#contenedor_citas').html(html);
    }
    
    // Ver detalle de cita
    $(document).on('click', '.btn-ver-detalle', function() {
        let id = $(this).data('id');
        citaSeleccionada = id;
        
        $('#detalle_cita_content').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Cargando detalles...</p>
            </div>
        `);
        $('#modalDetalleCita').modal('show');
        
        $.ajax({
            url: APP_URL + '/api/citas/obtener-detalle',
            type: 'POST',
            data: { id_cita: id },
            dataType: 'json',
            success: function(response) {
                var cita = response;
                if (response.success && response.data) {
                    cita = response.data;
                }
                
                let estadoClass = '';
                let estadoTexto = '';
                switch(cita.estado) {
                    case 'pendiente': estadoClass = 'badge-pendiente'; estadoTexto = 'Programada'; break;
                    case 'confirmada': estadoClass = 'badge-confirmada'; estadoTexto = 'Confirmada'; break;
                    case 'completada': estadoClass = 'badge-completada'; estadoTexto = 'Completada'; break;
                    case 'cancelada': estadoClass = 'badge-cancelada'; estadoTexto = 'Cancelada'; break;
                    default: estadoClass = 'badge-pendiente'; estadoTexto = 'Programada';
                }
                
                let html = `
                    <div class="p-3">
                        <div class="row mb-3">
                            <div class="col-12 text-center">
                                <span class="badge-estado ${estadoClass}">${estadoTexto}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-stethoscope"></i> Especialidad:</strong><br>${escapeHtml(cita.especialidad)}</p>
                                <p><strong><i class="fas fa-user-md"></i> Médico:</strong><br>Dr(a). ${escapeHtml(cita.medico)}</p>
                                <p><strong><i class="fas fa-calendar-alt"></i> Fecha:</strong><br>${cita.fecha}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-clock"></i> Hora:</strong><br>${cita.hora} hs</p>
                                <p><strong><i class="fas fa-building"></i> Consultorio:</strong><br>${escapeHtml(cita.consultorio_nombre)}</p>
                                <p><strong><i class="fas fa-map-marker-alt"></i> Dirección:</strong><br>${escapeHtml(cita.consultorio_direccion)}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <strong><i class="fas fa-notes-medical"></i> Motivo de Consulta</strong>
                                    </div>
                                    <div class="card-body">
                                        ${escapeHtml(cita.motivo) || '<em class="text-muted">No especificado</em>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#detalle_cita_content').html(html);
                
                // Mostrar botón de cancelar si la cita está pendiente o confirmada
                if (cita.estado !== 'cancelada' && cita.estado !== 'completada') {
                    $('.btn-cancelar-cita').show().data('id', cita.id_cita);
                } else {
                    $('.btn-cancelar-cita').hide();
                }
            },
            error: function() {
                $('#detalle_cita_content').html('<div class="alert alert-danger">Error al cargar los detalles de la cita</div>');
            }
        });
    });
    
    // Cancelar cita desde modal
    $(document).on('click', '.btn-cancelar-cita', function() {
        let id = $(this).data('id');
        if (id && confirm('¿Está seguro que desea cancelar esta cita?')) {
            cancelarCita(id);
        }
    });
    
    // Cancelar cita desde tarjeta
    $(document).on('click', '.btn-cancelar', function() {
        let id = $(this).data('id');
        if (confirm('¿Está seguro que desea cancelar esta cita?')) {
            cancelarCita(id);
        }
    });
    
    // Ver factura desde cita
    $(document).on('click', '.btn-ver-factura', function() {
        let id = $(this).data('id');
        buscarFacturaDeCita(id);
    });
    
    function buscarFacturaDeCita(idCita) {
        $.ajax({
            url: APP_URL + '/api/facturas/buscar-por-cita',
            type: 'POST',
            data: { id_cita: idCita },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.factura) {
                    // Ya existe factura, redirigir al detalle
                    window.location.href = APP_URL + '/facturas/detalle?id=' + response.data.factura.id_factura;
                } else {
                    // No existe factura, mostrar mensaje
                    alert('Esta cita aún no tiene factura generada. Contacte al asistente o administrador para generarla.');
                }
            },
            error: function() {
                alert('Error al buscar factura de la cita');
            }
        });
    }
    
    function cancelarCita(id) {
        $.ajax({
            url: APP_URL + '/api/citas/cancelar',
            type: 'POST',
            data: { id_cita: id, csrf_token: $('input[name="csrf_token"]').val() },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarToast('Cita cancelada correctamente', 'success');
                    cargarEstadisticas();
                    cargarCitas();
                    $('#modalDetalleCita').modal('hide');
                } else {
                    mostrarToast(response.message || 'Error al cancelar la cita', 'error');
                }
            },
            error: function() {
                mostrarToast('Error de conexión al cancelar la cita', 'error');
            }
        });
    }
    
    // Filtros
    $('.filtro-btn').click(function() {
        $('.filtro-btn').removeClass('active');
        $(this).addClass('active');
        filtroActual = $(this).data('filtro');
        cargarCitas();
    });
    
    // Funciones auxiliares
    function mostrarToast(mensaje, tipo) {
        var toastHtml = `
            <div class="toast align-items-center text-white bg-${tipo === 'success' ? 'success' : 'danger'} border-0 position-fixed" 
                 style="top: 70px; right: 20px; z-index: 9999; min-width: 250px; border-radius: 12px;" 
                 role="alert" aria-live="assertive" aria-atomic="true" data-autohide="true" data-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                        ${mensaje}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast">×</button>
                </div>
            </div>
        `;
        $('body').append(toastHtml);
        setTimeout(function() { $('.toast').last().fadeOut(300, function() { $(this).remove(); }); }, 3000);
    }
    
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    
    // Inicializar
    cargarEstadisticas();
    cargarCitas();
});
</script>
