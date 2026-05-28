<?php
// vista/especialidades/esp_asignar_medico.php
// Contenido principal para asignar médicos a especialidades
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
$id_especialidad = $id_especialidad ?? $_GET['id'] ?? 0;
?>

<!-- CSS Adicional para esta vista -->
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .form-section h4 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--bv-primary);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--bv-border);
    }
    .form-section h4 i {
        margin-right: 0.5rem;
    }
    .info-especialidad {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .info-especialidad::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -5%;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .info-especialidad::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -5%;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .info-especialidad h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        position: relative;
        z-index: 1;
    }
    .info-especialidad p {
        opacity: 0.9;
        margin-bottom: 0;
        position: relative;
        z-index: 1;
    }
    .info-especialidad .badge {
        position: relative;
        z-index: 1;
        margin-top: 0.5rem;
        background: rgba(255,255,255,0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .medico-card {
        border-left: 3px solid var(--bv-primary);
        margin-bottom: 10px;
        transition: all 0.2s;
    }
    .medico-card:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    .alert-custom {
        border-radius: 12px;
        border: none;
        padding: 1rem;
    }
    .btn-asignar {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        border: none;
        border-radius: 12px;
        padding: 0.8rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-asignar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,119,182,0.3);
    }
    .btn-cancelar {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        border: none;
        border-radius: 12px;
        padding: 0.8rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-cancelar:hover {
        transform: translateY(-2px);
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
    .info-adicional {
        background: #f0fdf4;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .info-adicional i {
        color: #10b981;
        margin-right: 0.5rem;
    }
    .info-adicional p {
        font-size: 0.75rem;
        color: #065f46;
        margin-bottom: 0;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-md"></i> Asignar Médico</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_especialidad" value="<?php echo $id_especialidad; ?>">
        
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <!-- Información de la Especialidad -->
                <div class="info-especialidad" id="info_especialidad">
                    <div class="text-center">
                        <i class="fas fa-stethoscope fa-3x mb-2"></i>
                        <h3 id="especialidad_nombre">Cargando especialidad...</h3>
                        <p id="especialidad_descripcion" class="mb-2"></p>
                        <span class="badge" id="especialidad_badge">
                            <i class="fas fa-info-circle"></i> Especialidad Médica
                        </span>
                    </div>
                </div>

                <!-- Formulario Principal -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus"></i> Registrar Médico a Especialidad
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Alertas -->
                        <div class="alert alert-success alert-custom" id="alertExito" style="display:none;">
                            <i class="fas fa-check-circle"></i> <span id="exitoMensaje"></span>
                        </div>
                        <div class="alert alert-danger alert-custom" id="alertError" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                        </div>
                        <div class="alert alert-warning alert-custom" id="alertWarning" style="display:none;">
                            <i class="fas fa-exclamation-triangle"></i> <span id="warningMensaje"></span>
                        </div>
                        
                        <div id="loadingDatos" class="loading-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        
                        <form id="formAsignarMedico">
                            <?php echo Security::campoCSRF(); ?>
                            
                            <div class="form-section">
                                <h4><i class="fas fa-user-md"></i> Datos del Médico</h4>
                                
                                <div class="form-group">
                                    <label for="medico_seleccionado" class="required-field">Seleccionar Médico</label>
                                    <select class="form-control" id="medico_seleccionado" name="id_medico" required>
                                        <option value="">Seleccione un médico...</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Solo se muestran médicos no asignados a esta especialidad
                                    </small>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4><i class="fas fa-chart-line"></i> Datos Profesionales</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tarifa">Tarifa por Consulta ($)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" step="0.01" class="form-control" 
                                                       id="tarifa" name="tarifa" placeholder="0.00">
                                            </div>
                                            <small class="form-text text-muted">Monto base por consulta</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exp_anios">Años de Experiencia</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" 
                                                       id="exp_anios" name="exp_anios" placeholder="0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">años</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="extra">Costo Adicional ($)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" step="0.01" class="form-control" 
                                                       id="extra" name="extra" placeholder="0.00">
                                            </div>
                                            <small class="form-text text-muted">Por procedimientos especiales</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox mt-4 pt-2">
                                                <input type="checkbox" class="custom-control-input" id="domicilio" name="domicilio">
                                                <label class="custom-control-label" for="domicilio">
                                                    <i class="fas fa-home"></i> ¿Realiza consulta a domicilio?
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-secondary btn-cancelar" id="btnCancelar">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary btn-asignar" id="btnAsignar">
                                    <i class="fas fa-save"></i> Asignar Médico
                                </button>
                            </div>
                        </form>
                        
                        <div class="info-adicional mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información adicional:</strong>
                            <p class="mt-1">Los datos profesionales ayudan a calcular tarifas y disponibilidad del médico para esta especialidad. Puede editarlos más tarde desde el detalle de la especialidad.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    console.log('=== FORMULARIO DE ASIGNACIÓN DE MÉDICO ===');
    console.log('ID Especialidad:', $('#id_especialidad').val());
    
    // ==================== CARGAR DATOS DE LA ESPECIALIDAD ====================
    
    function cargarEspecialidad() {
        let id = $('#id_especialidad').val();
        
        if (!id || id === '0') {
            mostrarError('ID de especialidad no válido');
            return;
        }
        
        $.ajax({
            url: APP_URL + '/api/especialidades/obtener-detalle',
            type: 'POST',
            data: { id_especialidad: id },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Datos de especialidad:', response);
                
                // Manejar formato ApiResponse
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                if (data.error) {
                    $('#especialidad_nombre').text('Error: ' + data.error);
                    return;
                }
                
                $('#especialidad_nombre').text(data.nombre);
                $('#especialidad_descripcion').text(data.descripcion || 'Sin descripción registrada');
                
                // Color dinámico para el badge
                let colorMap = {
                    'Azul Médico': '#007bff',
                    'Verde Salud': '#28a745',
                    'Rojo Urgencias': '#dc3545',
                    'Amarillo Precaución': '#ffc107',
                    'Púrpura Especial': '#6f42c1',
                    'Naranja': '#fd7e14'
                };
                let colorHex = colorMap[data.color] || '#007bff';
                $('#especialidad_badge').css('background-color', colorHex);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar especialidad:', error);
                $('#especialidad_nombre').text('Error al cargar datos');
                mostrarError('No se pudo cargar la información de la especialidad');
            }
        });
    }
    
    // ==================== CARGAR MÉDICOS DISPONIBLES ====================
    
    function cargarMedicosDisponibles() {
        let id_especialidad = $('#id_especialidad').val();
        
        $('#medico_seleccionado').html('<option value="">Cargando médicos...</option>');
        
        $.ajax({
            url: APP_URL + '/api/especialidades/listar-medicos',
            type: 'POST',
            data: { id_especialidad: id_especialidad },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Médicos disponibles:', response);
                
                // Manejar formato ApiResponse
                var medicos = [];
                if (response.success && response.data) {
                    medicos = response.data;
                } else if (Array.isArray(response)) {
                    medicos = response;
                } else if (response.medicos && Array.isArray(response.medicos)) {
                    medicos = response.medicos;
                }
                
                let options = '<option value="">Seleccione un médico...</option>';
                
                if (medicos.length === 0) {
                    options = '<option value="">No hay médicos disponibles para asignar</option>';
                    $('#medico_seleccionado').prop('disabled', true);
                    mostrarWarning('No hay médicos disponibles para asignar a esta especialidad');
                } else {
                    $('#medico_seleccionado').prop('disabled', false);
                    for (let i = 0; i < medicos.length; i++) {
                        let med = medicos[i];
                        let infoExtra = med.mpps ? ` - MPPS: ${med.mpps}` : '';
                        options += `<option value="${med.id_medico}">
                            ${escapeHtml(med.nombre)} (Cédula: ${med.cedula})${infoExtra}
                        </option>`;
                    }
                }
                
                $('#medico_seleccionado').html(options);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar médicos:', error);
                $('#medico_seleccionado').html('<option value="">Error al cargar médicos</option>');
                mostrarError('Error al cargar la lista de médicos disponibles');
            }
        });
    }
    
    // ==================== ENVÍO DEL FORMULARIO ====================
    
    $('#formAsignarMedico').submit(function(e) {
        e.preventDefault();
        
        let id_especialidad = $('#id_especialidad').val();
        let id_medico = $('#medico_seleccionado').val();
        let tarifa = $('#tarifa').val() || 0;
        let exp_anios = $('#exp_anios').val() || 0;
        let extra = $('#extra').val() || 0;
        let domicilio = $('#domicilio').is(':checked') ? 1 : 0;
        
        // Validaciones
        if (!id_especialidad || id_especialidad === '0') {
            mostrarError('ID de especialidad no válido');
            return;
        }
        
        if (!id_medico) {
            mostrarError('Debe seleccionar un médico');
            $('#medico_seleccionado').focus();
            return;
        }
        
        // Validar valores numéricos
        if (tarifa < 0) {
            mostrarError('La tarifa no puede ser negativa');
            $('#tarifa').focus();
            return;
        }
        
        if (exp_anios < 0) {
            mostrarError('Los años de experiencia no pueden ser negativos');
            $('#exp_anios').focus();
            return;
        }
        
        if (extra < 0) {
            mostrarError('El costo adicional no puede ser negativo');
            $('#extra').focus();
            return;
        }
        
        let datos = {
            id_especialidad: id_especialidad,
            id_medico: id_medico,
            tarifa: tarifa,
            exp_anios: exp_anios,
            extra: extra,
            domicilio: domicilio,
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        console.log('Enviando datos de asignación:', datos);
        
        let $btn = $('#btnAsignar');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Asignando...');
        
        $.ajax({
            url: APP_URL + '/api/especialidades/asignar-medico',
            type: 'POST',
            data: datos,
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.resultado === 'asignado') {
                    mostrarExito('Médico asignado correctamente a la especialidad');
                    setTimeout(function() {
                        window.location.href = APP_URL + '/especialidades/detalle/' + id_especialidad;
                    }, 2000);
                } else if (response.resultado === 'ya_asignado') {
                    mostrarWarning('El médico ya está asignado a esta especialidad');
                    $btn.prop('disabled', false).html(originalText);
                } else if (response.resultado === 'error_csrf') {
                    mostrarError('Error de seguridad. Por favor, recargue la página.');
                    $btn.prop('disabled', false).html(originalText);
                } else {
                    let errorMsg = response.error || response.message || 'Error al asignar el médico';
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
    
    // ==================== BOTÓN CANCELAR ====================
    
    $('#btnCancelar').click(function() {
        let id = $('#id_especialidad').val();
        if (confirm('¿Está seguro que desea cancelar? Los datos no guardados se perderán.')) {
            window.location.href = APP_URL + '/especialidades/detalle/' + id;
        }
    });
    
    // ==================== FUNCIONES DE NOTIFICACIÓN ====================
    
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
    
    function mostrarWarning(mensaje) {
        $('#warningMensaje').text(mensaje);
        $('#alertWarning').fadeIn(300);
        setTimeout(function() {
            $('#alertWarning').fadeOut(500);
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
    cargarEspecialidad();
    cargarMedicosDisponibles();
});
</script>