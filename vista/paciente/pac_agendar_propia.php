<?php
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_paciente = $id_paciente ?? $_SESSION['usuario'] ?? 0;

// Obtener datos del paciente para mostrar en el resumen
$pacienteModel = new Paciente();
$pacienteData = $pacienteModel->obtenerDatosBasicos($id_paciente);
?>
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .form-section h4 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--bv-primary);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #eef2f6;
    }
    .resumen-card {
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border-radius: 16px;
        padding: 1.5rem;
        position: sticky;
        top: 20px;
    }
    .resumen-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #eef2f6;
    }
    .resumen-item:last-child {
        border-bottom: none;
    }
    .resumen-label {
        font-weight: 600;
        color: var(--bv-text-light);
        font-size: 0.8rem;
    }
    .resumen-value {
        font-weight: 700;
        color: var(--bv-dark);
        text-align: right;
    }
    .tarifa-total {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        padding: 1rem;
        border-radius: 12px;
        margin-top: 1rem;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
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
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-calendar-plus"></i> Agendar Cita Propia</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> Detalles de la Cita
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success alert-custom" id="alertExito" style="display:none;">
                            <i class="fas fa-check-circle"></i> <span id="exitoMensaje"></span>
                        </div>
                        <div class="alert alert-danger alert-custom" id="alertError" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                        </div>
                        
                        <form id="formAgendarCita">
                            <?php echo Security::campoCSRF(); ?>
                            <input type="hidden" name="es_tercero" value="0">
                            <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                            
                            <div class="form-section">
                                <h4><i class="fas fa-stethoscope"></i> 1. SELECCIONE ESPECIALIDAD</h4>
                                <div class="form-group">
                                    <label for="id_especialidad" class="required-field">Especialidad</label>
                                    <select class="form-control" id="id_especialidad" name="id_especialidad" required>
                                        <option value="">Seleccione una especialidad...</option>
                                        <?php
                                        $especialidadModel = new Especialidad();
                                        $especialidades = $especialidadModel->listar('', 'activas');
                                        foreach ($especialidades as $esp) {
                                            echo '<option value="' . $esp->id_especialidad . '">' . htmlspecialchars($esp->nombre) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4><i class="fas fa-calendar-alt"></i> 2. FECHA Y HORA</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fecha_cita" class="required-field">SELECCIONAR FECHA</label>
                                            <input type="date" class="form-control" id="fecha_cita" name="fecha" required>
                                            <small class="form-text text-muted">Seleccione un día disponible</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="hora_cita" class="required-field">HORARIOS</label>
                                            <select class="form-control" id="hora_cita" name="hora" required disabled>
                                                <option value="">Seleccione una fecha primero...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4><i class="fas fa-notes-medical"></i> 3. TIPO DE CONSULTA Y MOTIVO</h4>
                                <div class="form-group">
                                    <label class="d-block">Tipo de Consulta</label>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="primera_vez" name="tipo_consulta" value="primera_vez" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="primera_vez">
                                            <strong>Primera Vez</strong><br>
                                            <small>Apertura de historia clínica y evaluación médica inicial completa.</small>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="control" name="tipo_consulta" value="control" class="custom-control-input">
                                        <label class="custom-control-label" for="control">
                                            <strong>Control</strong><br>
                                            <small>Seguimiento de tratamiento, revisión de exámenes o evolución.</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="motivo" class="required-field">Motivo de Consulta</label>
                                    <textarea class="form-control" id="motivo" name="motivo" rows="3" 
                                              placeholder="Describa brevemente sus síntomas..." required></textarea>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-secondary" id="btnVolver">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnConfirmar">
                                    <i class="fas fa-check-circle"></i> Confirmar Cita
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="resumen-card">
                    <h5 class="mb-3"><i class="fas fa-file-invoice"></i> Resumen</h5>
                    <div class="resumen-item">
                        <span class="resumen-label">TIPO DE CITA</span>
                        <span class="resumen-value" id="resumen_tipo">Cita Propia</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">MODALIDAD</span>
                        <span class="resumen-value" id="resumen_modalidad">En Consultorio</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">ESPECIALIDAD</span>
                        <span class="resumen-value" id="resumen_especialidad">-</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">MÉDICO</span>
                        <span class="resumen-value" id="resumen_medico">-</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">CONSULTORIO</span>
                        <span class="resumen-value" id="resumen_consultorio">-</span>
                    </div>
                    <div class="resumen-item">
                        <span class="resumen-label">FECHA Y HORA</span>
                        <span class="resumen-value" id="resumen_fecha">-</span>
                    </div>
                    <div class="tarifa-total text-center">
                        <span class="resumen-label" style="color: rgba(255,255,255,0.8);">TARIFA TOTAL</span>
                        <div class="resumen-value" style="color: white; font-size: 1.5rem;" id="resumen_tarifa">$0.00</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    console.log('=== AGENDAR CITA PROPIA ===');
    
    // Variables
    let medicosDisponibles = [];
    let idMedicoSeleccionado = null;
    
    // Cargar médicos al seleccionar especialidad
    $('#id_especialidad').change(function() {
        let id_especialidad = $(this).val();
        let especialidadNombre = $('#id_especialidad option:selected').text();
        
        // Actualizar resumen
        $('#resumen_especialidad').text(especialidadNombre || '-');
        $('#resumen_medico').text('-');
        $('#resumen_consultorio').text('-');
        $('#resumen_tarifa').text('$0.00');
        
        // Resetear horarios
        $('#hora_cita').html('<option value="">Seleccione una fecha primero...</option>').prop('disabled', true);
        $('#fecha_cita').val('');
        
        if (id_especialidad) {
            cargarMedicosPorEspecialidad(id_especialidad);
        }
    });
    
    function cargarMedicosPorEspecialidad(id_especialidad) {
        console.log('Cargando médicos para especialidad:', id_especialidad);
        
        // Mostrar indicador de carga en el resumen
        $('#resumen_medico').html('<span class="text-muted">Cargando médicos...</span>');
        
        $.ajax({
            url: APP_URL + '/api/especialidades/listar-medicos',
            type: 'POST',
            data: { id_especialidad: id_especialidad },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta médicos:', response);
                
                // Manejar formato ApiResponse
                medicosDisponibles = [];
                if (response.success && response.data) {
                    medicosDisponibles = response.data;
                } else if (Array.isArray(response)) {
                    medicosDisponibles = response;
                }
                
                console.log('Médicos disponibles:', medicosDisponibles);
                
                if (medicosDisponibles.length > 0) {
                    // Seleccionar el primer médico disponible
                    let medico = medicosDisponibles[0];
                    idMedicoSeleccionado = medico.id_medico;
                    $('#resumen_medico').text(medico.nombre || '-');
                    
                    // Cargar consultorio del médico
                    cargarConsultorioPorMedico(medico.id_medico);
                    
                    // Habilitar selección de fecha
                    $('#fecha_cita').prop('disabled', false);
                    // Establecer fecha mínima (mañana)
                    let tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    $('#fecha_cita').attr('min', tomorrow.toISOString().split('T')[0]);
                    
                    // Opcional: Mostrar selector de médico si hay más de uno
                    if (medicosDisponibles.length > 1) {
                        mostrarSelectorMedico(medicosDisponibles);
                    }
                } else {
                    $('#resumen_medico').text('No hay médicos disponibles para esta especialidad');
                    $('#fecha_cita').prop('disabled', true);
                    mostrarError('No hay médicos disponibles para la especialidad seleccionada');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar médicos:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                $('#resumen_medico').text('Error al cargar médicos');
                mostrarError('Error al cargar los médicos disponibles');
            }
        });
    }
    
    function mostrarSelectorMedico(medicos) {
        // Crear un selector de médicos en el resumen o en un modal
        let selectHtml = '<select id="selector_medico" class="form-control form-control-sm mt-2">';
        for (let medico of medicos) {
            selectHtml += `<option value="${medico.id_medico}">${medico.nombre}</option>`;
        }
        selectHtml += '</select>';
        
        $('#resumen_medico').html(selectHtml);
        
        $('#selector_medico').change(function() {
            let id_medico = $(this).val();
            idMedicoSeleccionado = id_medico;
            let medicoNombre = $(this).find('option:selected').text();
            $('#resumen_medico').html(medicoNombre);
            // Recargar horarios si ya hay fecha seleccionada
            let fecha = $('#fecha_cita').val();
            if (fecha) {
                cargarHorarios(fecha);
            }
        });
    }
    
    function cargarConsultorioPorMedico(id_medico) {
        $.ajax({
            url: APP_URL + '/api/consultorios/listar-medicos-disponibles',
            type: 'POST',
            data: { id_medico: id_medico },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Consultorios del médico:', response);
                let consultorios = [];
                if (response.success && response.data) {
                    consultorios = response.data;
                } else if (Array.isArray(response)) {
                    consultorios = response;
                }
                
                if (consultorios.length > 0) {
                    $('#resumen_consultorio').text(consultorios[0].nombre || 'Consultorio Asignado');
                } else {
                    $('#resumen_consultorio').text('Consultorio asignado');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar consultorio:', error);
                $('#resumen_consultorio').text('-');
            }
        });
    }
    
    // Cargar horarios al seleccionar fecha
    $('#fecha_cita').change(function() {
        let fecha = $(this).val();
        if (fecha && idMedicoSeleccionado) {
            cargarHorarios(fecha);
        }
    });
    
    function cargarHorarios(fecha) {
        let id_especialidad = $('#id_especialidad').val();
        
        console.log('Cargando horarios para:', { id_especialidad, id_medico: idMedicoSeleccionado, fecha });
        
        $('#hora_cita').html('<option value="">Cargando horarios...</option>').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/citas/obtener-horarios',
            type: 'POST',
            data: {
                id_especialidad: id_especialidad,
                id_medico: idMedicoSeleccionado,
                fecha: fecha
            },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta horarios:', response);
                
                let horarios = [];
                if (response.success && response.data) {
                    horarios = response.data;
                } else if (Array.isArray(response)) {
                    horarios = response;
                }
                
                let options = '<option value="">Seleccione un horario...</option>';
                for (let hora of horarios) {
                    options += `<option value="${hora}">${hora}</option>`;
                }
                
                if (horarios.length === 0) {
                    options = '<option value="">No hay horarios disponibles para esta fecha</option>';
                }
                
                $('#hora_cita').html(options);
                $('#resumen_fecha').text(fecha + ' a las --:--');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar horarios:', error);
                $('#hora_cita').html('<option value="">Error al cargar horarios</option>');
                mostrarError('Error al cargar los horarios disponibles');
            }
        });
    }
    
    $('#hora_cita').change(function() {
        let fecha = $('#fecha_cita').val();
        let hora = $(this).val();
        if (fecha && hora) {
            $('#resumen_fecha').text(fecha + ' a las ' + hora);
            // Actualizar tarifa (simulada - puedes obtenerla desde el servidor)
            $('#resumen_tarifa').text('$45.00');
        }
    });
    
    // Tipo de consulta
    $('input[name="tipo_consulta"]').change(function() {
        let tipo = $(this).val();
        $('#resumen_modalidad').text(tipo === 'primera_vez' ? 'Primera Vez' : 'Control');
    });
    
    // Envío del formulario
    $('#formAgendarCita').submit(function(e) {
        e.preventDefault();
        
        let id_especialidad = $('#id_especialidad').val();
        let fecha = $('#fecha_cita').val();
        let hora = $('#hora_cita').val();
        let motivo = $('#motivo').val().trim();
        let tipo_consulta = $('input[name="tipo_consulta"]:checked').val();
        
        if (!id_especialidad) {
            mostrarError('Debe seleccionar una especialidad');
            return;
        }
        if (!fecha) {
            mostrarError('Debe seleccionar una fecha');
            return;
        }
        if (!hora) {
            mostrarError('Debe seleccionar un horario');
            return;
        }
        if (!motivo) {
            mostrarError('Debe describir el motivo de consulta');
            return;
        }
        
        if (!idMedicoSeleccionado) {
            mostrarError('No hay médico disponible para esta especialidad');
            return;
        }
        
        let datos = {
            id_especialidad: id_especialidad,
            id_medico: idMedicoSeleccionado,
            id_consultorio: 1,
            fecha: fecha,
            hora: hora,
            tipo_consulta: tipo_consulta,
            motivo: motivo,
            es_tercero: 0,
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        console.log('Enviando datos:', datos);
        
        let $btn = $('#btnConfirmar');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Confirmando...');
        
        $.ajax({
            url: APP_URL + '/api/citas/crear',
            type: 'POST',
            data: datos,
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta:', response);
                
                if (response.success) {
                    mostrarExito(response.message);
                    setTimeout(function() {
                        window.location.href = APP_URL + '/paciente/citas';
                    }, 2000);
                } else {
                    let errorMsg = response.message || 'Error al agendar la cita';
                    if (response.data && response.data.errors) {
                        errorMsg = Object.values(response.data.errors).join('. ');
                    }
                    mostrarError(errorMsg);
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                
                let errorMsg = 'Error de conexión: ' + status;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                mostrarError(errorMsg);
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    $('#btnVolver').click(function() {
        window.location.href = APP_URL + '/paciente/citas/agendar';
    });
    
    function mostrarError(mensaje) {
        $('#errorMensaje').text(mensaje);
        $('#alertError').fadeIn(300);
        setTimeout(function() { 
            $('#alertError').fadeOut(500); 
        }, 5000);
        
        // Scroll al error
        $('html, body').animate({
            scrollTop: $('#alertError').offset().top - 100
        }, 500);
    }
    
    function mostrarExito(mensaje) {
        $('#exitoMensaje').text(mensaje);
        $('#alertExito').fadeIn(300);
        setTimeout(function() { 
            $('#alertExito').fadeOut(500); 
        }, 3000);
    }
    
    // Inicializar: deshabilitar fecha hasta que se seleccione especialidad
    $('#fecha_cita').prop('disabled', true);
});
</script>
