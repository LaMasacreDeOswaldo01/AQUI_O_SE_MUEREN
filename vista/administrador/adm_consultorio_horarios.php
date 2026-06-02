<?php
// vista/administrador/adm_consultorio_horarios.php
// Contenido principal para la gestión de horarios de consultorios
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
$id_consultorio = $id_consultorio ?? $_GET['id'] ?? 0;
?>

<!-- CSS Adicional para esta vista -->
<style>
    .horario-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .horario-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .horario-card h4 {
        border-bottom: 2px solid var(--bv-primary);
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        font-size: 1rem;
        font-weight: 700;
        color: var(--bv-dark);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .horario-card h4 i {
        color: var(--bv-primary);
        font-size: 0.9rem;
    }
    .horario-slot {
        background: white;
        border-radius: 10px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.2s;
    }
    .horario-slot:hover {
        background: #fef9e6;
    }
    .horario-slot.disponible {
        border-left: 4px solid #10b981;
    }
    .horario-slot.ocupado {
        border-left: 4px solid #f59e0b;
    }
    .horario-slot.sin-medico {
        border-left: 4px solid #9ca3af;
        opacity: 0.7;
    }
    .horario-slot .slot-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .horario-slot .slot-horario {
        font-weight: 700;
        font-size: 0.85rem;
        font-family: monospace;
    }
    .horario-slot .slot-medico {
        font-size: 0.75rem;
        color: var(--bv-text-light);
    }
    .horario-slot .slot-medico i {
        margin-right: 0.25rem;
    }
    .btn-horario {
        padding: 0.25rem 0.75rem;
        font-size: 0.7rem;
        border-radius: 8px;
    }
    .empty-slot {
        text-align: center;
        padding: 1rem;
        color: #9ca3af;
        font-size: 0.75rem;
        background: #f3f4f6;
        border-radius: 8px;
    }
    .modal-bv .modal-content {
        border-radius: 16px;
    }
    .modal-bv .modal-header {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        border-radius: 16px 16px 0 0;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .alert-custom {
        border-radius: 12px;
        border: none;
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
    .btn-refresh {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        border: none;
        border-radius: 8px;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
    }
    .btn-refresh:hover {
        transform: translateY(-1px);
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-calendar-alt"></i> Horarios del Consultorio</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_consultorio" value="<?php echo $id_consultorio; ?>">
        
        <!-- Welcome Banner -->
        <div class="bv-welcome-banner admin bv-animate">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-clock me-2"></i> <span id="consultorio_nombre">Cargando...</span></h2>
                    <p class="mb-0">Configure los horarios de atención y asigne médicos a cada turno.</p>
                    <div class="bv-role-tag mt-2">
                        <i class="fas fa-hospital-user"></i> Gestión de Horarios
                    </div>
                </div>
                <div class="d-none d-md-block">
                    <button class="btn btn-light btn-sm" id="btnRefresh">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <!-- Tabla de Horarios -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-week"></i> Horarios de Atención
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="loadingHorarios" class="loading-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        
                        <div class="alert alert-success alert-custom" id="alertExito" style="display:none;">
                            <i class="fas fa-check-circle"></i> <span id="exitoMensaje"></span>
                        </div>
                        <div class="alert alert-danger alert-custom" id="alertError" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                        </div>
                        
                        <div id="contenedor_horarios" class="row">
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando horarios...</span>
                                </div>
                                <p class="mt-2">Cargando horarios del consultorio...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <!-- Información de Ayuda -->
                <div class="info-card">
                    <h6><i class="fas fa-info-circle"></i> Información importante</h6>
                    <p><i class="fas fa-check-circle text-success"></i> Los horarios se dividen en turnos de Mañana y Tarde</p>
                    <p><i class="fas fa-user-md"></i> Puede asignar un médico específico a cada turno</p>
                    <p><i class="fas fa-clock"></i> Los horarios pueden superponerse entre diferentes días</p>
                    <p><i class="fas fa-exclamation-triangle text-warning"></i> Un médico no puede estar en dos consultorios simultáneamente</p>
                </div>
                
                <!-- Botones de navegación -->
                <div class="info-card text-center">
                    <a href="<?php echo APP_URL; ?>/consultorios/detalle?id=<?php echo $id_consultorio; ?>" class="btn btn-info btn-block btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver al Detalle
                    </a>
                    <button class="btn btn-outline-primary btn-block btn-sm mt-2" id="btnRefreshMobile">
                        <i class="fas fa-sync-alt"></i> Actualizar Horarios
                    </button>
                </div>
                
                <!-- Leyenda -->
                <div class="info-card">
                    <h6><i class="fas fa-palette"></i> Leyenda</h6>
                    <p><span style="display: inline-block; width: 12px; height: 12px; background: #10b981; border-radius: 2px;"></span> <span class="ml-1">Turno con médico asignado</span></p>
                    <p><span style="display: inline-block; width: 12px; height: 12px; background: #f59e0b; border-radius: 2px;"></span> <span class="ml-1">Turno sin médico asignado</span></p>
                    <p><span style="display: inline-block; width: 12px; height: 12px; background: #9ca3af; border-radius: 2px;"></span> <span class="ml-1">Consultorio cerrado</span></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Editar Horario -->
<div class="modal fade modal-bv" id="modalHorario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-clock"></i> Editar Horario
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="horario_dia">
                <input type="hidden" id="horario_turno">
                
                <div class="form-group">
                    <label>Día</label>
                    <input type="text" class="form-control" id="horario_dia_text" readonly>
                </div>
                
                <div class="form-group">
                    <label>Turno</label>
                    <input type="text" class="form-control" id="horario_turno_text" readonly>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hora_inicio" class="required-field">Hora de Inicio</label>
                            <input type="time" class="form-control" id="hora_inicio" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="hora_fin" class="required-field">Hora de Fin</label>
                            <input type="time" class="form-control" id="hora_fin" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="medico_asignado">Médico Asignado</label>
                    <select class="form-control" id="medico_asignado">
                        <option value="">Sin asignar (Consultorio cerrado)</option>
                    </select>
                    <small class="form-text text-muted">Seleccione un médico para este turno, o déjelo vacío si el consultorio no atiende en este horario</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarHorario">
                    <i class="fas fa-save"></i> Guardar Horario
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('=== GESTIÓN DE HORARIOS DE CONSULTORIO ===');
    console.log('ID Consultorio:', $('#id_consultorio').val());
    
    // Variables
    let listaMedicos = [];
    
    // ==================== FUNCIONES PRINCIPALES ====================
    
    function cargarNombreConsultorio() {
        let id = $('#id_consultorio').val();
        
        $.ajax({
            url: APP_URL + '/api/consultorios/obtener-detalle',
            type: 'POST',
            data: { id_consultorio: id },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                $('#consultorio_nombre').text(data.nombre || 'Consultorio');
            },
            error: function() {
                $('#consultorio_nombre').text('Consultorio');
            }
        });
    }
    
    function cargarHorarios() {
        let id = $('#id_consultorio').val();
        
        if (!id || id === '0') {
            $('#contenedor_horarios').html(`
                <div class="col-12 text-center">
                    <div class="alert alert-danger">ID de consultorio no válido</div>
                </div>
            `);
            return;
        }
        
        $('#loadingHorarios').show();
        
        $.ajax({
            url: APP_URL + '/api/consultorios/obtener-horarios',
            type: 'POST',
            data: { id_consultorio: id },
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Horarios recibidos:', response);
                
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                listaMedicos = data.medicos || [];
                let horarios = data.horarios || {};
                
                renderizarHorarios(horarios);
                $('#loadingHorarios').hide();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar horarios:', error);
                $('#contenedor_horarios').html(`
                    <div class="col-12 text-center">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los horarios: ${error}
                        </div>
                    </div>
                `);
                $('#loadingHorarios').hide();
            }
        });
    }
    
    function renderizarHorarios(horarios) {
        const dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        const turnos = ['Mañana', 'Tarde'];
        
        let html = '';
        
        for (let i = 0; i < dias.length; i++) {
            let dia = dias[i];
            
            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="horario-card">
                        <h4>
                            ${dia}
                            <i class="fas fa-calendar-day"></i>
                        </h4>
            `;
            
            for (let j = 0; j < turnos.length; j++) {
                let turno = turnos[j];
                let horario = horarios[dia] && horarios[dia][turno];
                
                if (horario && horario.hora_inicio && horario.hora_fin) {
                    let medicoNombre = horario.nombre_medico || 'Sin asignar';
                    let tieneMedico = horario.id_medico !== null && horario.id_medico !== '';
                    let slotClass = tieneMedico ? 'disponible' : 'ocupado';
                    let medicoInfo = tieneMedico ? `<i class="fas fa-user-md"></i> ${escapeHtml(medicoNombre)}` : '<i class="fas fa-user-slash"></i> Sin médico asignado';
                    
                    html += `
                        <div class="horario-slot ${slotClass}">
                            <div class="slot-header">
                                <span class="slot-horario">
                                    <i class="fas fa-clock"></i> ${horario.hora_inicio} - ${horario.hora_fin}
                                </span>
                                <button class="btn btn-primary btn-horario btn-editar-horario" 
                                        data-dia="${dia}" 
                                        data-turno="${turno}"
                                        data-hora-inicio="${horario.hora_inicio}"
                                        data-hora-fin="${horario.hora_fin}"
                                        data-medico-id="${horario.id_medico || ''}"
                                        data-medico-nombre="${escapeHtml(medicoNombre)}">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                            </div>
                            <div class="slot-medico">
                                ${medicoInfo}
                            </div>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="empty-slot">
                            <i class="fas fa-ban"></i>
                            <div>Sin horario configurado</div>
                            <button class="btn btn-outline-primary btn-horario mt-2 btn-editar-horario" 
                                    data-dia="${dia}" 
                                    data-turno="${turno}"
                                    data-hora-inicio=""
                                    data-hora-fin=""
                                    data-medico-id=""
                                    data-medico-nombre="">
                                <i class="fas fa-plus"></i> Configurar
                            </button>
                        </div>
                    `;
                }
            }
            
            html += `
                    </div>
                </div>
            `;
        }
        
        $('#contenedor_horarios').html(html);
    }
    
    function cargarListaMedicos() {
        let options = '<option value="">Sin asignar (Consultorio cerrado)</option>';
        
        for (let i = 0; i < listaMedicos.length; i++) {
            let medico = listaMedicos[i];
            options += `<option value="${medico.id}">${escapeHtml(medico.nombre)} (${medico.cedula})</option>`;
        }
        
        $('#medico_asignado').html(options);
    }
    
    // ==================== FUNCIÓN PARA GUARDAR HORARIO ====================
    
    function guardarHorario() {
        let id_consultorio = $('#id_consultorio').val();
        let dia = $('#horario_dia').val();
        let turno = $('#horario_turno').val();
        let hora_inicio = $('#hora_inicio').val();
        let hora_fin = $('#hora_fin').val();
        let id_medico = $('#medico_asignado').val() || null;
        
        // Validaciones
        if (!hora_inicio) {
            mostrarErrorModal('Debe ingresar la hora de inicio');
            return;
        }
        
        if (!hora_fin) {
            mostrarErrorModal('Debe ingresar la hora de fin');
            return;
        }
        
        if (hora_inicio >= hora_fin) {
            mostrarErrorModal('La hora de fin debe ser mayor que la hora de inicio');
            return;
        }
        
        let datos = {
            id_consultorio: id_consultorio,
            dia: dia,
            turno: turno,
            hora_inicio: hora_inicio,
            hora_fin: hora_fin,
            id_medico: id_medico,
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        console.log('Guardando horario:', datos);
        
        let $btn = $('#btnGuardarHorario');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/consultorios/guardar-horario',
            type: 'POST',
            data: datos,
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta:', response);
                
                if (response.resultado === 'guardado') {
                    $('#modalHorario').modal('hide');
                    mostrarExito('Horario guardado correctamente');
                    cargarHorarios();
                } else if (response.resultado === 'error_duplicado') {
                    mostrarErrorModal(response.mensaje || 'El médico ya tiene un horario asignado en este mismo día y turno en otro consultorio');
                } else if (response.resultado === 'error_horario') {
                    mostrarErrorModal(response.mensaje || 'La hora de fin debe ser mayor que la hora de inicio');
                } else {
                    mostrarErrorModal(response.mensaje || 'Error al guardar el horario');
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarErrorModal('Error de conexión: ' + status);
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }
    
    // ==================== EVENTOS ====================
    
    $(document).on('click', '.btn-editar-horario', function() {
        let dia = $(this).data('dia');
        let turno = $(this).data('turno');
        let horaInicio = $(this).data('hora-inicio');
        let horaFin = $(this).data('hora-fin');
        let medicoId = $(this).data('medico-id');
        
        $('#horario_dia').val(dia);
        $('#horario_turno').val(turno);
        $('#horario_dia_text').val(dia);
        $('#horario_turno_text').val(turno);
        $('#hora_inicio').val(horaInicio || '08:00');
        $('#hora_fin').val(horaFin || '17:00');
        
        if (medicoId && medicoId !== '') {
            $('#medico_asignado').val(medicoId);
        } else {
            $('#medico_asignado').val('');
        }
        
        cargarListaMedicos();
        $('#modalHorario').modal('show');
    });
    
    $('#btnGuardarHorario').click(function() {
        guardarHorario();
    });
    
    $('#btnRefresh, #btnRefreshMobile').click(function() {
        cargarHorarios();
        mostrarExito('Horarios actualizados');
    });
    
    // ==================== FUNCIONES DE NOTIFICACIÓN ====================
    
    function mostrarExito(mensaje) {
        $('#exitoMensaje').text(mensaje);
        $('#alertExito').fadeIn(300);
        setTimeout(function() {
            $('#alertExito').fadeOut(500);
        }, 3000);
    }
    
    function mostrarError(mensaje) {
        $('#errorMensaje').text(mensaje);
        $('#alertError').fadeIn(300);
        setTimeout(function() {
            $('#alertError').fadeOut(500);
        }, 5000);
    }
    
    function mostrarErrorModal(mensaje) {
        // Mostrar error en el modal o en una alerta
        if ($('#modalHorario').hasClass('show')) {
            // Si el modal está abierto, mostrar error dentro del modal
            let errorDiv = $('#modalHorario .modal-body .alert-danger');
            if (errorDiv.length === 0) {
                $('.modal-body').prepend('<div class="alert alert-danger alert-custom" id="modalError" style="display:none;"><i class="fas fa-exclamation-circle"></i> <span id="modalErrorMsg"></span></div>');
                errorDiv = $('#modalError');
            }
            $('#modalErrorMsg').text(mensaje);
            errorDiv.fadeIn(300);
            setTimeout(function() {
                errorDiv.fadeOut(500);
            }, 4000);
        } else {
            mostrarError(mensaje);
        }
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
    cargarNombreConsultorio();
    cargarHorarios();
});
</script>