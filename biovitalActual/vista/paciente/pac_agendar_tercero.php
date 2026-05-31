<?php
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_paciente = $id_paciente ?? $_SESSION['usuario'] ?? 0;
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
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-calendar-plus"></i> Agendar Cita para Tercero</h1>
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
                            <i class="fas fa-clipboard-list"></i> Datos del Representante y Paciente
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
                            <input type="hidden" name="es_tercero" value="1">
                            <input type="hidden" name="id_paciente_representante" value="<?php echo $id_paciente; ?>">
                            
                            <!-- Datos del Representante -->
                            <div class="form-section">
                                <h4><i class="fas fa-user-tie"></i> Datos del Representante</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>PRIMER NOMBRE</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['nombre_us'] ?? ''); ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>SEGUNDO NOMBRE</label>
                                            <input type="text" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>PRIMER APELLIDO</label>
                                            <input type="text" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>SEGUNDO APELLIDO</label>
                                            <input type="text" class="form-control" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>IDENTIFICACIÓN</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['cedula'] ?? ''); ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>TELÉFONO</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['telefono'] ?? ''); ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>PARENTESCO CON EL PACIENTE</label>
                                            <select class="form-control" name="parentesco" required>
                                                <option value="">Seleccionar parentesco...</option>
                                                <option value="Padre">Padre</option>
                                                <option value="Madre">Madre</option>
                                                <option value="Hijo">Hijo</option>
                                                <option value="Hija">Hija</option>
                                                <option value="Hermano">Hermano</option>
                                                <option value="Hermana">Hermana</option>
                                                <option value="Cónyuge">Cónyuge</option>
                                                <option value="Abuelo">Abuelo</option>
                                                <option value="Abuela">Abuela</option>
                                                <option value="Tío">Tío</option>
                                                <option value="Tía">Tía</option>
                                                <option value="Primo">Primo</option>
                                                <option value="Prima">Prima</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Datos del Paciente -->
                            <div class="form-section">
                                <h4><i class="fas fa-user-injured"></i> Datos del Paciente</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label>PRIMER NOMBRE</label>
                                            <input type="text" class="form-control" name="nombre_tercero" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>SEGUNDO NOMBRE</label>
                                            <input type="text" class="form-control" name="segundo_nombre_tercero">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label>PRIMER APELLIDO</label>
                                            <input type="text" class="form-control" name="apellido_tercero" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>SEGUNDO APELLIDO</label>
                                            <input type="text" class="form-control" name="segundo_apellido_tercero">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>CÉDULA / DOCUMENTO (Opcional)</label>
                                            <input type="text" class="form-control" name="cedula_tercero">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label>NÚMERO TELEFÓNICO</label>
                                            <input type="tel" class="form-control" name="telefono_tercero" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Detalles Médicos -->
                            <div class="form-section">
                                <h4><i class="fas fa-stethoscope"></i> Detalles Médicos</h4>
                                
                                <div class="form-group">
                                    <label class="required-field">1. SELECCIONE ESPECIALIDAD</label>
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
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required-field">SELECCIONAR FECHA</label>
                                            <input type="date" class="form-control" id="fecha_cita" name="fecha" required>
                                            <small class="form-text text-muted">Los días con disponibilidad se mostrarán activos.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="required-field">HORARIOS</label>
                                            <select class="form-control" id="hora_cita" name="hora" required disabled>
                                                <option value="">Seleccione una fecha primero...</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="required-field">Tipo de Consulta</label>
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
                        <span class="resumen-value">Cita para Terceros</span>
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
    console.log('=== AGENDAR CITA PARA TERCERO ===');
    
    // Variables
    let medicosDisponibles = [];
    let especialidadNombre = '';
    
    // Cargar médicos al seleccionar especialidad
    $('#id_especialidad').change(function() {
        let id_especialidad = $(this).val();
        especialidadNombre = $('#id_especialidad option:selected').text();
        
        $('#resumen_especialidad').text(especialidadNombre || '-');
        $('#resumen_medico').text('-');
        $('#resumen_consultorio').text('-');
        $('#hora_cita').html('<option value="">Seleccione una fecha primero...</option>').prop('disabled', true);
        $('#fecha_cita').val('');
        
        if (id_especialidad) {
            cargarMedicosPorEspecialidad(id_especialidad);
        }
    });
    
   function cargarMedicosPorEspecialidad(id_especialidad) {
    console.log('Cargando médicos para especialidad:', id_especialidad);
    
    $.ajax({
        url: APP_URL + '/api/especialidades/listar-medicos',
        type: 'POST',
        data: { id_especialidad: id_especialidad },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Respuesta médicos:', response);
            
            medicosDisponibles = [];
            if (response.success && response.data) {
                medicosDisponibles = response.data;
            } else if (Array.isArray(response)) {
                medicosDisponibles = response;
            }
            
            if (medicosDisponibles.length > 0) {
                let medico = medicosDisponibles[0];
                idMedicoSeleccionado = medico.id_medico;
                $('#resumen_medico').text(medico.nombre || '-');
                cargarConsultorioPorMedico(medico.id_medico);
                $('#fecha_cita').prop('disabled', false);
                let tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                $('#fecha_cita').attr('min', tomorrow.toISOString().split('T')[0]);
            } else {
                $('#resumen_medico').text('No hay médicos disponibles');
                $('#fecha_cita').prop('disabled', true);
                mostrarError('No hay médicos disponibles para la especialidad seleccionada');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar médicos:', error);
            $('#resumen_medico').text('Error al cargar médicos');
            mostrarError('Error al cargar los médicos disponibles');
        }
    });
}
    
    function cargarConsultorioPorMedico(id_medico) {
        $.ajax({
            url: APP_URL + '/api/consultorios/listar-medicos-disponibles',
            type: 'POST',
            data: { id_medico: id_medico },
            dataType: 'json',
            success: function(response) {
                $('#resumen_consultorio').text('Consultorio Asignado');
            },
            error: function() {
                $('#resumen_consultorio').text('-');
            }
        });
    }
    
    // Cargar horarios al seleccionar fecha
    $('#fecha_cita').change(function() {
        let fecha = $(this).val();
        let id_especialidad = $('#id_especialidad').val();
        let id_medico = medicosDisponibles.length > 0 ? medicosDisponibles[0].id_medico : 0;
        
        if (fecha && id_especialidad && id_medico) {
            $('#hora_cita').html('<option value="">Cargando horarios...</option>').prop('disabled', false);
            
            $.ajax({
                url: APP_URL + '/api/citas/obtener-horarios',
                type: 'POST',
                data: {
                    id_especialidad: id_especialidad,
                    id_medico: id_medico,
                    fecha: fecha
                },
                dataType: 'json',
                success: function(response) {
                    let horarios = [];
                    if (response.success && response.data) {
                        horarios = response.data;
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
                error: function() {
                    $('#hora_cita').html('<option value="">Error al cargar horarios</option>');
                }
            });
        }
    });
    
    $('#hora_cita').change(function() {
        let fecha = $('#fecha_cita').val();
        let hora = $(this).val();
        if (fecha && hora) {
            $('#resumen_fecha').text(fecha + ' a las ' + hora);
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
        
        // Validar datos del tercero
        let nombre_tercero = $('input[name="nombre_tercero"]').val().trim();
        let apellido_tercero = $('input[name="apellido_tercero"]').val().trim();
        let telefono_tercero = $('input[name="telefono_tercero"]').val().trim();
        
        if (!nombre_tercero) {
            mostrarError('Debe ingresar el primer nombre del paciente');
            return;
        }
        if (!apellido_tercero) {
            mostrarError('Debe ingresar el primer apellido del paciente');
            return;
        }
        if (!telefono_tercero) {
            mostrarError('Debe ingresar el teléfono del paciente');
            return;
        }
        
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
        
        let id_medico = medicosDisponibles.length > 0 ? medicosDisponibles[0].id_medico : 0;
        
        let datos = {
            id_especialidad: id_especialidad,
            id_medico: id_medico,
            id_consultorio: 1,
            fecha: fecha,
            hora: hora,
            tipo_consulta: tipo_consulta,
            motivo: motivo,
            es_tercero: 1,
            nombre_tercero: nombre_tercero + ' ' + ($('input[name="segundo_nombre_tercero"]').val() || ''),
            cedula_tercero: $('input[name="cedula_tercero"]').val() || null,
            telefono_tercero: telefono_tercero,
            parentesco: $('select[name="parentesco"]').val(),
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        let $btn = $('#btnConfirmar');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Confirmando...');
        
        $.ajax({
            url: APP_URL + '/api/citas/crear',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarExito(response.message);
                    setTimeout(function() {
                        window.location.href = APP_URL + '/paciente/citas';
                    }, 2000);
                } else {
                    mostrarError(response.message || 'Error al agendar la cita');
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarError('Error de conexión: ' + status);
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
        setTimeout(function() { $('#alertError').fadeOut(500); }, 5000);
    }
    
    function mostrarExito(mensaje) {
        $('#exitoMensaje').text(mensaje);
        $('#alertExito').fadeIn(300);
        setTimeout(function() { $('#alertExito').fadeOut(500); }, 3000);
    }
});
</script>
