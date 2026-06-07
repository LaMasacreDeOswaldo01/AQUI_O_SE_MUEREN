<?php
$nombre_usuario = $nombre_usuario ?? 'Administrador';
$id_consultorio = $id_consultorio ?? $_GET['id'] ?? 0;
?>

<!-- CSS Adicional para esta vista -->
<style>
    .info-box-icon-custom {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 15px;
    }
    .medico-item {
        border-left: 3px solid #17a2b8;
        margin-bottom: 10px;
        transition: all 0.2s;
        border-radius: 8px;
    }
    .medico-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    .especialidad-badge {
        display: inline-block;
        background: #e9ecef;
        padding: 5px 12px;
        border-radius: 20px;
        margin: 3px;
        font-size: 12px;
        transition: all 0.2s;
    }
    .especialidad-badge:hover {
        background: var(--bv-primary);
        color: white;
        transform: translateY(-2px);
    }
    .estadistica-card {
        text-align: center;
        padding: 15px;
        border-radius: 12px;
        background: #f8f9fa;
        transition: all 0.3s;
    }
    .estadistica-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .estadistica-numero {
        font-size: 2rem;
        font-weight: 700;
        color: var(--bv-primary);
    }
    .estadistica-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 5px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eef2f6;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: var(--bv-text-light);
        font-size: 0.8rem;
    }
    .info-value {
        font-weight: 500;
        color: var(--bv-dark);
        text-align: right;
    }
    .action-buttons .btn {
        margin: 0 5px;
        border-radius: 10px;
        transition: all 0.3s;
    }
    .action-buttons .btn:hover {
        transform: translateY(-2px);
    }
    .horario-tag {
        background: #e8f4f8;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        display: inline-block;
    }
    .modal-bv .modal-content {
        border-radius: 16px;
    }
    .modal-bv .modal-header {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        border-radius: 16px 16px 0 0;
    }
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 16px;
    }
    .card {
        position: relative;
    }
    .direccion-text {
        word-break: break-word;
        font-size: 0.85rem;
    }
    .btn-asignar {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        border: none;
        border-radius: 8px;
        transition: all 0.3s;
    }
    .btn-asignar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,119,182,0.3);
    }
    .ubicacion-completa {
        font-size: 0.85rem;
        line-height: 1.5;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-building"></i> <span id="consultorio_nombre">Detalle del Consultorio</span></h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_consultorio" value="<?php echo $id_consultorio; ?>">
        
        <div class="row">
            <!-- Columna Izquierda - Información General -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <div class="info-box-icon-custom bg-primary mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%;">
                                <i class="fas fa-hospital-user fa-3x text-white"></i>
                            </div>
                        </div>
                        <h3 class="profile-username text-center" id="detalle_nombre">-</h3>
                        <p class="text-muted text-center" id="detalle_ciudad">-</p>
                        
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b><i class="fas fa-clock"></i> Horario</b>
                                <a class="float-right" id="detalle_horario">-</a>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fas fa-phone"></i> Teléfono</b>
                                <a class="float-right" id="detalle_telefono">-</a>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fas fa-envelope"></i> Email</b>
                                <a class="float-right" id="detalle_email">-</a>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fas fa-map-marker-alt"></i> Dirección</b>
                                <div class="float-right text-right direccion-text" id="detalle_direccion">-</div>
                            </li>
                        </ul>
                        
                       <div class="action-buttons text-center">
    <a href="<?php echo APP_URL; ?>/consultorios/horarios/<?php echo $id_consultorio; ?>" class="btn btn-info btn-sm">
        <i class="fas fa-calendar-alt"></i> Gestionar Horarios
    </a>
    <a href="<?php echo APP_URL; ?>/consultorios/editar/<?php echo $id_consultorio; ?>" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i> Editar
    </a>
   <a href="<?php echo APP_URL; ?>/consultorios" class="btn btn-secondary btn-sm">
    <i class="fas fa-arrow-left"></i> Volver al listado
</a>
</div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-line"></i> Estadísticas</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="estadistica-card">
                                    <div class="estadistica-numero" id="total_medicos">0</div>
                                    <div class="estadistica-label">Médicos Asignados</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="estadistica-card">
                                    <div class="estadistica-numero" id="total_especialidades">0</div>
                                    <div class="estadistica-label">Especialidades</div>
                                </div>
                            </div>
                            <div class="col-6 mt-3">
                                <div class="estadistica-card">
                                    <div class="estadistica-numero" id="total_citas">0</div>
                                    <div class="estadistica-label">Citas Históricas</div>
                                </div>
                            </div>
                            <div class="col-6 mt-3">
                                <div class="estadistica-card">
                                    <div class="estadistica-numero" id="citas_mes">0</div>
                                    <div class="estadistica-label">Citas este Mes</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Horario Resumen -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clock"></i> Horario de Atención</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-sun"></i> Apertura</span>
                            <span class="info-value" id="horario_apertura">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-moon"></i> Cierre</span>
                            <span class="info-value" id="horario_cierre">-</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar-week"></i> Días laborales</span>
                            <span class="info-value">Lunes a Viernes</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha - Descripción, Especialidades y Médicos -->
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información General</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label><i class="fas fa-align-left"></i> Descripción</label>
                            <p id="detalle_descripcion" class="text-muted">-</p>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-tag"></i> Ubicación completa</label>
                            <p id="ubicacion_completa" class="text-muted ubicacion-completa">-</p>
                        </div>
                    </div>
                </div>

                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-stethoscope"></i> Especialidades Admitidas</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="contenedor_especialidades">
                        <div class="text-center py-3">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <p class="mt-2 mb-0">Cargando especialidades...</p>
                        </div>
                    </div>
                </div>

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-md"></i> Médicos Asignados</h3>
                        <div class="card-tools">
                            <button class="btn btn-primary btn-sm btn-asignar" data-toggle="modal" data-target="#modalAsignarMedico">
                                <i class="fas fa-plus"></i> Asignar Médico
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="contenedor_medicos">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary"></div>
                                <p class="mt-2 mb-0">Cargando médicos asignados...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Asignar Médico -->
<div class="modal fade modal-bv" id="modalAsignarMedico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Asignar Médico</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="required-field">Seleccionar Médico</label>
                    <select class="form-control" id="medico_seleccionado">
                        <option value="">Seleccione un médico...</option>
                    </select>
                </div>
                <div id="mensaje_asignacion" class="alert" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnAsignarMedico">Asignar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Remover Médico -->
<div class="modal fade modal-bv" id="modalRemoverMedico" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Remover Médico</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea remover este médico del consultorio?</p>
                <p class="text-muted">Esta acción no elimina al médico del sistema, solo lo desasigna de este consultorio.</p>
                <input type="hidden" id="remover_asignacion_id">
                <input type="hidden" id="remover_medico_nombre">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarRemover">Remover</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('=== DETALLE DE CONSULTORIO ===');
    console.log('ID Consultorio:', $('#id_consultorio').val());
    
    // ==================== CARGAR DATOS DEL CONSULTORIO ====================
    
    function cargarDetalleConsultorio() {
        let id = $('#id_consultorio').val();
        
        if (!id || id === '0') {
            $('#detalle_nombre').text('ID de consultorio no válido');
            return;
        }
        
        $.ajax({
            url: APP_URL + '/api/consultorios/obtener-detalle',
            type: 'POST',
            data: { id_consultorio: id },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Detalle consultorio:', response);
                
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                if (data.error) {
                    $('#detalle_nombre').text('Error: ' + data.error);
                    return;
                }
                
                // Actualizar información básica
                $('#consultorio_nombre').text(data.nombre);
                $('#detalle_nombre').text(data.nombre);
                $('#detalle_ciudad').text(data.ciudad || 'No especificada');
                $('#detalle_horario').html(`<span class="horario-tag"><i class="fas fa-clock"></i> ${data.apertura || '08:00'} - ${data.cierre || '17:00'}</span>`);
                $('#detalle_telefono').text(data.telefono || '-');
                $('#detalle_email').text(data.email || '-');
                $('#detalle_direccion').text(data.direccion_detallada || '-');
                $('#detalle_descripcion').html(data.descripcion || '<p class="text-muted">Sin descripción</p>');
                
                // Horario
                $('#horario_apertura').text(data.apertura || '08:00');
                $('#horario_cierre').text(data.cierre || '17:00');
                
                // ==================== UBICACIÓN COMPLETA CORREGIDA ====================
                let ubicacion = '';
                if (data.estado) ubicacion += data.estado;
                if (data.ciudad) ubicacion += (ubicacion ? ', ' : '') + data.ciudad;
                if (data.municipio) ubicacion += (ubicacion ? ', ' : '') + data.municipio;
                if (data.parroquia) ubicacion += (ubicacion ? ', ' : '') + data.parroquia;
                
                if (data.direccion_detallada) {
                    $('#ubicacion_completa').html(ubicacion + '<br><strong>Dirección:</strong> ' + data.direccion_detallada);
                } else {
                    $('#ubicacion_completa').text(ubicacion || 'No especificada');
                }
                // ==================== FIN UBICACIÓN CORREGIDA ====================
                
                // Estadísticas
                $('#total_medicos').text(data.medicos ? data.medicos.length : 0);
                $('#total_especialidades').text(data.especialidades ? data.especialidades.length : 0);
                $('#total_citas').text(data.total_citas || 0);
                $('#citas_mes').text(data.citas_mes || 0);
                
                // Especialidades
                mostrarEspecialidades(data.especialidades || []);
                
                // Médicos
                mostrarMedicos(data.medicos || []);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar detalle:', error);
                $('#detalle_nombre').text('Error al cargar datos');
                mostrarAlerta('Error al cargar los detalles del consultorio', 'error');
            }
        });
    }
    
    function mostrarEspecialidades(especialidades) {
        let html = '';
        
        if (!especialidades || especialidades.length === 0) {
            html = '<p class="text-muted text-center">No hay especialidades registradas</p>';
        } else {
            for (let i = 0; i < especialidades.length; i++) {
                html += `<span class="especialidad-badge">${escapeHtml(especialidades[i])}</span>`;
            }
        }
        
        $('#contenedor_especialidades').html(html);
    }
    
    function mostrarMedicos(medicos) {
        let html = '';
        
        if (!medicos || medicos.length === 0) {
            html = `
                <div class="text-center py-4">
                    <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay médicos asignados a este consultorio</p>
                    <button class="btn btn-primary btn-sm btn-asignar" data-toggle="modal" data-target="#modalAsignarMedico">
                        <i class="fas fa-plus"></i> Asignar Médico
                    </button>
                </div>
            `;
        } else {
            for (let i = 0; i < medicos.length; i++) {
                let med = medicos[i];
                html += `
                    <div class="medico-item p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="fas fa-user-md text-info"></i> ${escapeHtml(med.nombre)}</strong><br>
                                <small><i class="fas fa-id-card"></i> Cédula: ${med.cedula || 'N/A'}</small><br>
                                <small><i class="fas fa-phone"></i> Teléfono: ${med.telefono || 'N/A'}</small>
                            </div>
                            <button class="btn btn-danger btn-sm btn-remover-medico" 
                                    data-id="${med.id}" 
                                    data-nombre="${escapeHtml(med.nombre)}">
                                <i class="fas fa-user-minus"></i> Remover
                            </button>
                        </div>
                    </div>
                `;
            }
        }
        
        $('#contenedor_medicos').html(html);
    }
    
    // ==================== LISTA DE MÉDICOS DISPONIBLES ====================
    
    function cargarListaMedicosDisponibles() {
        let id_consultorio = $('#id_consultorio').val();
        
        $('#medico_seleccionado').html('<option value="">Cargando médicos...</option>');
        
        $.ajax({
            url: APP_URL + '/api/consultorios/listar-medicos',
            type: 'POST',
            data: { id_consultorio: id_consultorio },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Médicos disponibles:', response);
                
                var medicos = [];
                if (response.success && response.data) {
                    medicos = response.data;
                } else if (Array.isArray(response)) {
                    medicos = response;
                }
                
                let options = '<option value="">Seleccione un médico...</option>';
                for (let i = 0; i < medicos.length; i++) {
                    let med = medicos[i];
                    options += `<option value="${med.id_medico}">
                        ${escapeHtml(med.nombre_medico)} ${escapeHtml(med.apellido_medico)} (${med.cedula_medico})
                    </option>`;
                }
                $('#medico_seleccionado').html(options);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar médicos:', error);
                $('#medico_seleccionado').html('<option value="">Error al cargar médicos</option>');
                mostrarMensajeAsignacion('Error al cargar la lista de médicos disponibles', 'danger');
            }
        });
    }
    
    // ==================== ASIGNAR MÉDICO ====================
    
    $('#btnAsignarMedico').click(function() {
        let id_consultorio = $('#id_consultorio').val();
        let id_medico = $('#medico_seleccionado').val();
        
        if (!id_medico) {
            mostrarMensajeAsignacion('Debe seleccionar un médico', 'danger');
            return;
        }
        
        let $btn = $(this);
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Asignando...');
        
        $.ajax({
            url: APP_URL + '/api/consultorios/asignar-medico',
            type: 'POST',
            data: {
                id_consultorio: id_consultorio,
                id_medico: id_medico,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.resultado === 'asignado') {
                    mostrarMensajeAsignacion('Médico asignado correctamente', 'success');
                    setTimeout(function() {
                        $('#modalAsignarMedico').modal('hide');
                        cargarDetalleConsultorio();
                    }, 1500);
                } else if (response.resultado === 'ya_asignado') {
                    mostrarMensajeAsignacion('El médico ya está asignado a este consultorio', 'warning');
                } else {
                    mostrarMensajeAsignacion('Error al asignar el médico', 'danger');
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarMensajeAsignacion('Error de conexión: ' + status, 'danger');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== REMOVER MÉDICO ====================
    
    $(document).on('click', '.btn-remover-medico', function() {
        let id_asignacion = $(this).data('id');
        let nombre_medico = $(this).data('nombre');
        
        $('#remover_asignacion_id').val(id_asignacion);
        $('#remover_medico_nombre').val(nombre_medico);
        $('#modalRemoverMedico .modal-body p').first().html(`¿Está seguro que desea remover a <strong>${nombre_medico}</strong> de este consultorio?`);
        $('#modalRemoverMedico').modal('show');
    });
    
    $('#confirmarRemover').click(function() {
        let id_asignacion = $('#remover_asignacion_id').val();
        
        let $btn = $(this);
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Removiendo...');
        
        $.ajax({
            url: APP_URL + '/api/consultorios/remover-medico',
            type: 'POST',
            data: { id_asignacion: id_asignacion },
            dataType: 'json',
            success: function(response) {
                if (response.resultado === 'removido') {
                    $('#modalRemoverMedico').modal('hide');
                    mostrarAlerta('Médico removido del consultorio', 'success');
                    cargarDetalleConsultorio();
                } else {
                    mostrarAlerta('Error al remover el médico', 'error');
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión: ' + status, 'error');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== MODAL ASIGNAR MÉDICO ====================
    
    $('#modalAsignarMedico').on('show.bs.modal', function() {
        cargarListaMedicosDisponibles();
        $('#mensaje_asignacion').hide();
    });
    
    function mostrarMensajeAsignacion(mensaje, tipo) {
        let alertClass = tipo === 'success' ? 'alert-success' : (tipo === 'warning' ? 'alert-warning' : 'alert-danger');
        let iconClass = tipo === 'success' ? 'fa-check-circle' : (tipo === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle');
        
        $('#mensaje_asignacion')
            .removeClass('alert-success alert-warning alert-danger')
            .addClass(alertClass)
            .html('<i class="fas ' + iconClass + '"></i> ' + mensaje)
            .show();
        
        setTimeout(function() {
            $('#mensaje_asignacion').fadeOut();
        }, 3000);
    }
    
    // ==================== BOTÓN VOLVER ====================
    
    $('#btnVolver').click(function() {
        window.location.href = APP_URL + '/consultorios';
    });
    
    // ==================== FUNCIONES UTILITARIAS ====================
    
    function mostrarAlerta(mensaje, tipo) {
        var alertDiv = $('<div>', {
            class: 'alert alert-' + (tipo === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show position-fixed',
            style: 'top: 70px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 12px;',
            role: 'alert'
        });
        
        var icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        alertDiv.html(`
            <i class="fas ${icon}"></i>
            ${mensaje}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `);
        
        $('body').append(alertDiv);
        
        setTimeout(function() {
            alertDiv.fadeOut(300, function() { $(this).remove(); });
        }, 4000);
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
    cargarDetalleConsultorio();
});
</script>