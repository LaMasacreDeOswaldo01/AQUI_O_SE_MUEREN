<?php
// vista/especialidades/esp_detalle.php
// Contenido principal para el detalle de especialidad
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
$id_especialidad = $id_especialidad ?? $_GET['id'] ?? 0;
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
    .color-indicador {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
        vertical-align: middle;
    }
    .requisitos-card, .observaciones-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .requisitos-card i, .observaciones-card i {
        color: var(--bv-primary);
        margin-right: 8px;
    }
    .action-buttons .btn {
        margin: 0 5px;
        border-radius: 10px;
        transition: all 0.3s;
    }
    .action-buttons .btn:hover {
        transform: translateY(-2px);
    }
    .badge-prioridad {
        font-size: 0.75rem;
        padding: 5px 12px;
        border-radius: 20px;
    }
    .badge-prioridad-alta { background-color: #fde68a; color: #92400e; }
    .badge-prioridad-media { background-color: #dbeafe; color: #1e40af; }
    .badge-prioridad-baja { background-color: #e5e7eb; color: #374151; }
    .badge-prioridad-urgente { background-color: #fee2e2; color: #991b1b; }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-stethoscope"></i> <span id="nombre_especialidad">Detalle de Especialidad</span></h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_especialidad" value="<?php echo $id_especialidad; ?>">
        
        <div class="row">
            <!-- Columna Izquierda - Información General -->
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <div class="info-box-icon-custom bg-primary mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%;">
                                <i class="fas fa-stethoscope fa-3x text-white"></i>
                            </div>
                        </div>
                        <h3 class="profile-username text-center" id="detalle_nombre">-</h3>
                        <p class="text-muted text-center" id="detalle_codigo">-</p>
                        
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b><i class="fas fa-clock"></i> Duración por Defecto</b>
                                <a class="float-right" id="detalle_duracion">-</a>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fas fa-palette"></i> Color</b>
                                <span id="detalle_color" class="float-right"></span>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fas fa-chart-line"></i> Prioridad</b>
                                <span id="detalle_prioridad" class="float-right"></span>
                            </li>
                            <li class="list-group-item">
                                <b><i class="fas fa-toggle-on"></i> Estado</b>
                                <span id="detalle_activo" class="float-right"></span>
                            </li>
                        </ul>
                        
                        <div class="action-buttons text-center">
                            <button class="btn btn-warning btn-sm" id="btnEditar">
                                <i class="fas fa-edit"></i> Editar Especialidad
                            </button>
                            <button class="btn btn-info btn-sm" id="btnAsignarMedico">
                                <i class="fas fa-user-plus"></i> Asignar Médico
                            </button>
                            <button class="btn btn-secondary btn-sm" id="btnVolver">
                                <i class="fas fa-arrow-left"></i> Volver
                            </button>
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
                                    <div class="estadistica-numero" id="total_citas">0</div>
                                    <div class="estadistica-label">Citas Totales</div>
                                </div>
                            </div>
                            <div class="col-6 mt-3">
                                <div class="estadistica-card">
                                    <div class="estadistica-numero" id="citas_pendientes">0</div>
                                    <div class="estadistica-label">Pendientes</div>
                                </div>
                            </div>
                            <div class="col-6 mt-3">
                                <div class="estadistica-card">
                                    <div class="estadistica-numero" id="duracion_min">0</div>
                                    <div class="estadistica-label">Duración (min)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha - Descripción y Médicos -->
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
                        <div id="requisitos_container" style="display:none;">
                            <div class="requisitos-card">
                                <i class="fas fa-clipboard-list"></i>
                                <strong>Requisitos para Citas</strong>
                                <p id="detalle_requisitos" class="text-muted mt-2 mb-0">-</p>
                            </div>
                        </div>
                        <div id="observaciones_container" style="display:none;">
                            <div class="observaciones-card">
                                <i class="fas fa-comment"></i>
                                <strong>Observaciones</strong>
                                <p id="detalle_observaciones" class="text-muted mt-2 mb-0">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-md"></i> Médicos Asignados</h3>
                        <div class="card-tools">
                            <button class="btn btn-primary btn-sm" id="btnAsignarMedicoHeader">
                                <i class="fas fa-plus"></i> Asignar Médico
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="contenedor_medicos">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary"></div>
                                <p class="mt-2">Cargando médicos asignados...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h3 class="card-title"><i class="fas fa-bolt"></i> Acciones Rápidas</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4 text-center">
                                <button class="btn btn-outline-success btn-block" id="btnNuevaCita" disabled>
                                    <i class="fas fa-calendar-plus"></i> Nueva Cita
                                </button>
                                <small class="text-muted">Agendar paciente aquí</small>
                            </div>
                            <div class="col-4 text-center">
                                <button class="btn btn-outline-info btn-block" id="btnAsignarMedicoFooter">
                                    <i class="fas fa-user-md"></i> Asignar Médico
                                </button>
                                <small class="text-muted">Ayuda profesional</small>
                            </div>
                            <div class="col-4 text-center">
                                <button class="btn btn-outline-secondary btn-block" id="btnVerReportes">
                                    <i class="fas fa-chart-bar"></i> Ver Reportes
                                </button>
                                <small class="text-muted">Estadísticas</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Asignar Médico -->
<div class="modal fade" id="modalAsignarMedico" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i> Asignar Médico a Especialidad
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Complete los datos del médico para asignarlo a esta especialidad.
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="required-field">Seleccionar Médico</label>
                            <select class="form-control" id="medico_seleccionado">
                                <option value="">Seleccione un médico...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tarifa ($)</label>
                            <input type="number" step="0.01" class="form-control" id="tarifa" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>EXP (AÑOS)</label>
                            <input type="number" class="form-control" id="exp_anios" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Extra ($)</label>
                            <input type="number" step="0.01" class="form-control" id="extra" placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="domicilio">
                            <label class="form-check-label" for="domicilio">
                                <i class="fas fa-home"></i> ¿Realiza consulta a domicilio?
                            </label>
                        </div>
                    </div>
                </div>
                <div id="mensaje_asignacion" class="alert mt-3" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarAsignacion">
                    <i class="fas fa-save"></i> Asignar Médico
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Remover Médico -->
<div class="modal fade" id="modalRemoverMedico" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header bg-danger text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Remover Médico
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea remover este médico de la especialidad?</p>
                <p class="text-muted">Esta acción no elimina al médico del sistema, solo lo desasigna de esta especialidad.</p>
                <input type="hidden" id="remover_medico_id">
                <input type="hidden" id="remover_asignacion_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarRemover">
                    <i class="fas fa-trash"></i> Remover
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo APP_URL; ?>/js/especialidades.js"></script>

<script>
$(document).ready(function() {
    console.log('=== CARGANDO DETALLE DE ESPECIALIDAD ===');
    console.log('ID Especialidad:', $('#id_especialidad').val());
    
    // Cargar datos de la especialidad
    cargarDetalleEspecialidad();
    
    // ==================== EVENTOS ====================
    
    // Botón volver
    $('#btnVolver').click(function() {
        window.location.href = APP_URL + '/especialidades';
    });
    
    // Botón editar
    $('#btnEditar').click(function() {
        var id = $('#id_especialidad').val();
        window.location.href = APP_URL + '/especialidades/editar?id=' + id;
    });
    
    // Botones asignar médico (varios)
    $('#btnAsignarMedico, #btnAsignarMedicoHeader, #btnAsignarMedicoFooter').click(function() {
        cargarListaMedicosDisponibles();
        $('#modalAsignarMedico').modal('show');
    });
    
    // Botón guardar asignación
    $('#btnGuardarAsignacion').click(function() {
        asignarMedicoEspecialidad();
    });
    
    // Botón ver reportes
    $('#btnVerReportes').click(function() {
        mostrarAlerta('Funcionalidad de reportes en desarrollo', 'info');
    });
    
    // Botón nueva cita
    $('#btnNuevaCita').click(function() {
        mostrarAlerta('Funcionalidad de nueva cita en desarrollo', 'info');
    });
    
    // Confirmar remover médico
    $('#confirmarRemover').click(function() {
        removerMedicoEspecialidad();
    });
    
    // ==================== FUNCIONES PRINCIPALES ====================
    
    function cargarDetalleEspecialidad() {
        let id = $('#id_especialidad').val();
        
        if (!id || id === '0') {
            $('#detalle_nombre').text('ID de especialidad no válido');
            return;
        }
        
        $.ajax({
            url: APP_URL + '/api/especialidades/obtener-detalle',
            type: 'POST',
            data: { id_especialidad: id },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Detalle especialidad:', response);
                
                // Manejar formato ApiResponse
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                if (data.error) {
                    $('#detalle_nombre').text('Error: ' + data.error);
                    return;
                }
                
                // Actualizar información básica
                $('#nombre_especialidad').text(data.nombre);
                $('#detalle_nombre').text(data.nombre);
                $('#detalle_codigo').text(data.codigo || 'Sin código');
                $('#detalle_descripcion').text(data.descripcion || 'Sin descripción');
                $('#detalle_duracion').text(data.duracion_defecto + ' minutos');
                $('#detalle_prioridad').html(getPrioridadBadge(data.prioridad));
                
                // Color con indicador
                let colorHex = getColorHex(data.color);
                $('#detalle_color').html(`<span class="color-indicador" style="background-color: ${colorHex}"></span> ${data.color}`);
                
                // Estado activo
                if (data.activo == 1) {
                    $('#detalle_activo').html('<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activa</span>');
                } else {
                    $('#detalle_activo').html('<span class="badge badge-secondary"><i class="fas fa-ban"></i> Inactiva</span>');
                }
                
                // Estadísticas
                $('#total_medicos').text(data.medicos ? data.medicos.length : 0);
                $('#total_citas').text(data.total_citas || 0);
                $('#citas_pendientes').text(data.citas_pendientes || 0);
                $('#duracion_min').text(data.duracion_defecto || 0);
                
                // Mostrar requisitos si existen
                if (data.requisitos && data.requisitos !== '') {
                    $('#requisitos_container').show();
                    $('#detalle_requisitos').text(data.requisitos);
                } else {
                    $('#requisitos_container').hide();
                }
                
                // Mostrar observaciones si existen
                if (data.observaciones && data.observaciones !== '') {
                    $('#observaciones_container').show();
                    $('#detalle_observaciones').text(data.observaciones);
                } else {
                    $('#observaciones_container').hide();
                }
                
                // Mostrar médicos asignados
                mostrarMedicosAsignados(data.medicos || []);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar detalle:', error);
                $('#detalle_nombre').text('Error al cargar datos');
                mostrarAlerta('Error al cargar los detalles de la especialidad', 'error');
            }
        });
    }
    
    function mostrarMedicosAsignados(medicos) {
        let medHtml = '';
        
        if (!medicos || medicos.length === 0) {
            medHtml = `
                <div class="text-center py-4">
                    <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay médicos asignados a esta especialidad</p>
                    <button class="btn btn-primary btn-sm" id="btnAsignarMedicoEmpty">
                        <i class="fas fa-plus"></i> Asignar Médico
                    </button>
                </div>
            `;
        } else {
            for (let i = 0; i < medicos.length; i++) {
                let med = medicos[i];
                medHtml += `
                    <div class="medico-item p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="fas fa-user-md text-info"></i> ${escapeHtml(med.nombre)}</strong>
                                <div class="small text-muted mt-1">
                                    <span class="badge badge-light">MPPS: ${med.mpps || 'N/A'}</span>
                                    ${med.tarifa ? `<span class="badge badge-info ml-1">Tarifa: $${med.tarifa}</span>` : ''}
                                    ${med.exp_anios ? `<span class="badge badge-secondary ml-1">${med.exp_anios} años exp.</span>` : ''}
                                </div>
                            </div>
                            <button class="btn btn-danger btn-sm btn-remover-medico" 
                                    data-id="${med.id_medico}" 
                                    data-asignacion="${med.id_asignacion || med.id}"
                                    data-nombre="${escapeHtml(med.nombre)}">
                                <i class="fas fa-user-minus"></i> Remover
                            </button>
                        </div>
                    </div>
                `;
            }
        }
        
        $('#contenedor_medicos').html(medHtml);
        
        // Evento para el botón de asignar médico (si está vacío)
        $('#btnAsignarMedicoEmpty').click(function() {
            cargarListaMedicosDisponibles();
            $('#modalAsignarMedico').modal('show');
        });
        
        // Evento para remover médico
        $('.btn-remover-medico').click(function() {
            let idMedico = $(this).data('id');
            let idAsignacion = $(this).data('asignacion');
            let nombreMedico = $(this).data('nombre');
            
            $('#remover_medico_id').val(idMedico);
            $('#remover_asignacion_id').val(idAsignacion);
            $('#modalRemoverMedico .modal-body p').first().html(
                `¿Está seguro que desea remover a <strong>${nombreMedico}</strong> de esta especialidad?`
            );
            $('#modalRemoverMedico').modal('show');
        });
    }
    
    function cargarListaMedicosDisponibles() {
        let id_especialidad = $('#id_especialidad').val();
        
        $('#medico_seleccionado').html('<option value="">Cargando médicos...</option>');
        
        $.ajax({
            url: APP_URL + '/api/especialidades/listar-medicos',
            type: 'POST',
            data: { id_especialidad: id_especialidad },
            dataType: 'json',
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
                        ${escapeHtml(med.nombre)} (Cédula: ${med.cedula})${med.mpps ? ' - MPPS: ' + med.mpps : ''}
                    </option>`;
                }
                $('#medico_seleccionado').html(options);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar médicos:', error);
                $('#medico_seleccionado').html('<option value="">Error al cargar médicos</option>');
                mostrarAlerta('Error al cargar la lista de médicos disponibles', 'error');
            }
        });
    }
    
    function asignarMedicoEspecialidad() {
        let id_especialidad = $('#id_especialidad').val();
        let id_medico = $('#medico_seleccionado').val();
        let tarifa = $('#tarifa').val() || 0;
        let exp_anios = $('#exp_anios').val() || 0;
        let extra = $('#extra').val() || 0;
        let domicilio = $('#domicilio').is(':checked') ? 1 : 0;
        
        if (!id_medico) {
            mostrarMensajeAsignacion('Debe seleccionar un médico', 'danger');
            return;
        }
        
        let $btn = $('#btnGuardarAsignacion');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Asignando...');
        
        $.ajax({
            url: APP_URL + '/api/especialidades/asignar-medico',
            type: 'POST',
            data: {
                id_especialidad: id_especialidad,
                id_medico: id_medico,
                tarifa: tarifa,
                exp_anios: exp_anios,
                extra: extra,
                domicilio: domicilio
            },
            dataType: 'json',
            success: function(response) {
                if (response.resultado === 'asignado') {
                    mostrarMensajeAsignacion('Médico asignado correctamente', 'success');
                    setTimeout(function() {
                        $('#modalAsignarMedico').modal('hide');
                        cargarDetalleEspecialidad();
                        // Limpiar formulario
                        $('#medico_seleccionado').val('');
                        $('#tarifa').val('');
                        $('#exp_anios').val('');
                        $('#extra').val('');
                        $('#domicilio').prop('checked', false);
                    }, 1500);
                } else if (response.resultado === 'ya_asignado') {
                    mostrarMensajeAsignacion('El médico ya está asignado a esta especialidad', 'warning');
                } else {
                    mostrarMensajeAsignacion('Error al asignar el médico', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarMensajeAsignacion('Error de conexión: ' + status, 'danger');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }
    
    function removerMedicoEspecialidad() {
        let id_asignacion = $('#remover_asignacion_id').val();
        
        if (!id_asignacion) {
            mostrarAlerta('ID de asignación no válido', 'error');
            return;
        }
        
        let $btn = $('#confirmarRemover');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Removiendo...');
        
        $.ajax({
            url: APP_URL + '/api/especialidades/remover-medico',
            type: 'POST',
            data: { id_asignacion: id_asignacion },
            dataType: 'json',
            success: function(response) {
                if (response.resultado === 'removido') {
                    mostrarAlerta('Médico removido de la especialidad', 'success');
                    $('#modalRemoverMedico').modal('hide');
                    cargarDetalleEspecialidad();
                } else {
                    mostrarAlerta('Error al remover el médico', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión: ' + status, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }
    
    function mostrarMensajeAsignacion(mensaje, tipo) {
        let alertClass = 'alert-' + tipo;
        let iconClass = tipo === 'success' ? 'fa-check-circle' : (tipo === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle');
        
        $('#mensaje_asignacion')
            .removeClass('alert-success alert-danger alert-warning')
            .addClass(alertClass)
            .html('<i class="fas ' + iconClass + '"></i> ' + mensaje)
            .show();
        
        setTimeout(function() {
            $('#mensaje_asignacion').fadeOut();
        }, 3000);
    }
    
    function mostrarAlerta(mensaje, tipo) {
        var alertDiv = $('<div>', {
            class: 'alert alert-' + (tipo === 'success' ? 'success' : tipo === 'error' ? 'danger' : 'info') + ' alert-dismissible fade show position-fixed',
            style: 'top: 70px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 12px;',
            role: 'alert'
        });
        
        var icon = tipo === 'success' ? 'fa-check-circle' : (tipo === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
        
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
    
    // ==================== FUNCIONES UTILITARIAS ====================
    
    function getPrioridadBadge(prioridad) {
        let prioridadClass = '';
        let prioridadTexto = prioridad || 'Media';
        
        switch(prioridadTexto) {
            case 'Alta': prioridadClass = 'badge-prioridad-alta'; break;
            case 'Media': prioridadClass = 'badge-prioridad-media'; break;
            case 'Baja': prioridadClass = 'badge-prioridad-baja'; break;
            case 'Urgente': prioridadClass = 'badge-prioridad-urgente'; break;
            default: prioridadClass = 'badge-prioridad-media';
        }
        
        return `<span class="badge-prioridad ${prioridadClass}">
                    <i class="fas fa-chart-line"></i> ${prioridadTexto}
                </span>`;
    }
    
    function getColorHex(color) {
        const colorMap = {
            'Azul Médico': '#007bff',
            'Verde Salud': '#28a745',
            'Rojo Urgencias': '#dc3545',
            'Amarillo Precaución': '#ffc107',
            'Púrpura Especial': '#6f42c1',
            'Naranja': '#fd7e14'
        };
        return colorMap[color] || '#007bff';
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
});
</script>