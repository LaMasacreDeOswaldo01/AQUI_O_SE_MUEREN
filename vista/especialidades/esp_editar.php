<?php
// vista/especialidades/esp_editar.php
// Contenido principal para la edición de especialidades
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
$id_especialidad = $id_especialidad ?? $_GET['id'] ?? 0;
?>

<!-- CSS Adicional para esta vista -->
<style>
    .preview-card {
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border-radius: 16px;
        border: 1px solid #eef2f6;
        transition: all 0.3s;
        position: sticky;
        top: 20px;
    }
    .preview-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .csrf-info {
        font-size: 12px;
        color: #6c757d;
        margin-top: 15px;
        text-align: center;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .color-preview {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 10px;
        vertical-align: middle;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .color-preview:hover {
        transform: scale(1.1);
    }
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .form-section h4 {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--bv-primary);
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .preview-nombre {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--bv-primary);
        word-break: break-word;
    }
    .preview-info {
        font-size: 0.8rem;
        color: var(--bv-text-light);
        margin-bottom: 0.5rem;
    }
    .badge-preview {
        background: #e8f4f8;
        color: #0d9488;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
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
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #28a745;
    }
    input:checked + .slider:before {
        transform: translateX(26px);
    }
    .estado-label {
        margin-left: 15px;
        font-weight: 500;
        vertical-align: middle;
    }
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 16px;
    }
    .card {
        position: relative;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-edit"></i> Editar Especialidad</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_especialidad" value="<?php echo $id_especialidad; ?>">
        
        <div class="row">
            <div class="col-md-8">
                <!-- Formulario Principal -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit"></i> Editar Información
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Alertas -->
                        <div class="alert alert-success alert-dismissible fade show" id="alertExito" style="display:none;">
                            <i class="fas fa-check-circle"></i> <span id="exitoMensaje"></span>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <div class="alert alert-danger alert-dismissible fade show" id="alertError" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        
                        <div id="loadingDatos" class="loading-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        
                        <form id="formEditarEspecialidad">
                            <?php echo Security::campoCSRF(); ?>
                            
                            <div class="form-group">
                                <label for="nombre" class="required-field">Nombre de la Especialidad</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       placeholder="Ej: Cardiología, Pediatría, Radiología..." required>
                                <small class="form-text text-muted">Nombre descriptivo de la especialidad médica</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="codigo">Código Interno</label>
                                <input type="text" class="form-control" id="codigo" name="codigo" 
                                       placeholder="Ej: CARD-01, PED-01">
                                <small class="form-text text-muted">Código para identificación rápida (opcional)</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" 
                                          rows="3" placeholder="Ej: Especialidad dedicada al diagnóstico y tratamiento de enfermedades del corazón..."></textarea>
                            </div>

                            <div class="form-section">
                                <h4><i class="fas fa-cog"></i> Configuración de Atención</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duracion_defecto" class="required-field">Duración por Defecto</label>
                                            <select class="form-control" id="duracion_defecto" name="duracion_defecto">
                                                <option value="15">15 minutos</option>
                                                <option value="20">20 minutos</option>
                                                <option value="30">30 minutos</option>
                                                <option value="45">45 minutos</option>
                                                <option value="60">60 minutos</option>
                                            </select>
                                            <small class="form-text text-muted">Tiempo estándar para cada cita</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="color">Color Identificador</label>
                                            <div class="input-group">
                                                <select class="form-control" id="color" name="color">
                                                    <option value="Azul Médico">Azul Médico</option>
                                                    <option value="Verde Salud">Verde Salud</option>
                                                    <option value="Rojo Urgencias">Rojo Urgencias</option>
                                                    <option value="Amarillo Precaución">Amarillo Precaución</option>
                                                    <option value="Púrpura Especial">Púrpura Especial</option>
                                                    <option value="Naranja">Naranja</option>
                                                </select>
                                                <span id="color_preview" class="color-preview"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="prioridad">Prioridad</label>
                                            <select class="form-control" id="prioridad" name="prioridad">
                                                <option value="Baja">Baja</option>
                                                <option value="Media">Media</option>
                                                <option value="Alta">Alta</option>
                                                <option value="Urgente">Urgente</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="orden_visualizacion">Orden de Visualización</label>
                                            <input type="number" class="form-control" id="orden_visualizacion" 
                                                   name="orden_visualizacion" value="0">
                                            <small class="form-text text-muted">Números más pequeños aparecen primero</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4><i class="fas fa-clipboard-list"></i> Información Adicional</h4>
                                
                                <div class="form-group">
                                    <label for="requisitos">Requisitos para Citas</label>
                                    <textarea class="form-control" id="requisitos" name="requisitos" 
                                              rows="3" placeholder="Ej: Ayuno de 8 horas, resultados de laboratorio previos, etc."></textarea>
                                    <small class="form-text text-muted">Indicaciones que debe cumplir el paciente antes de la cita</small>
                                </div>

                                <div class="form-group">
                                    <label for="observaciones">Observaciones Internas</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" 
                                              rows="3" placeholder="Notas internas sobre la especialidad..."></textarea>
                                    <small class="form-text text-muted">Información para uso interno del personal médico</small>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4><i class="fas fa-toggle-on"></i> Estado de la Especialidad</h4>
                                <div class="d-flex align-items-center">
                                    <label class="switch">
                                        <input type="checkbox" id="activo" name="activo">
                                        <span class="slider"></span>
                                    </label>
                                    <span class="estado-label" id="estado_texto">Activo</span>
                                </div>
                                <small class="form-text text-muted">Desactivar la especialidad la ocultará del catálogo</small>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-secondary" id="btnCancelar">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-warning" id="btnGuardar">
                                    <i class="fas fa-save"></i> Actualizar Especialidad
                                </button>
                            </div>
                            
                            <div class="csrf-info">
                                <i class="fas fa-shield-alt"></i> Formulario protegido contra CSRF - Todos los cambios son seguros
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Vista Previa en Tiempo Real -->
                <div class="preview-card p-3">
                    <div class="text-center mb-3">
                        <i class="fas fa-stethoscope fa-2x" style="color: var(--bv-primary);"></i>
                        <h5 class="mt-2 mb-0">Vista Previa</h5>
                        <small class="text-muted">Así se verá en el catálogo</small>
                    </div>
                    <hr>
                    <div class="preview-nombre text-center" id="preview_nombre">
                        Nombre de la Especialidad
                    </div>
                    <div class="preview-info text-center mt-2" id="preview_descripcion">
                        <em class="text-muted">Sin descripción</em>
                    </div>
                    <hr>
                    <div class="preview-info">
                        <i class="fas fa-clock text-warning"></i>
                        <span id="preview_duracion">30 minutos</span>
                    </div>
                    <div class="preview-info">
                        <i class="fas fa-chart-line text-info"></i>
                        <span id="preview_prioridad">Media</span>
                    </div>
                    <div class="preview-info">
                        <i class="fas fa-palette"></i>
                        <span id="preview_color">Azul Médico</span>
                    </div>
                    <div class="preview-info">
                        <i class="fas fa-toggle-on"></i>
                        <span id="preview_estado">Activo</span>
                    </div>
                    <hr>
                    <div class="text-center">
                        <span class="badge-preview" id="preview_badge">
                            <i class="fas fa-check-circle"></i> Especialidad activa
                        </span>
                    </div>
                </div>

                <!-- Información de Ayuda -->
                <div class="info-card">
                    <h6><i class="fas fa-info-circle"></i> Información importante</h6>
                    <p><i class="fas fa-check-circle text-success"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios</p>
                    <p><i class="fas fa-clock"></i> La duración afecta la programación de citas</p>
                    <p><i class="fas fa-palette"></i> El color ayuda en la identificación visual</p>
                    <p><i class="fas fa-user-md"></i> Puede asignar médicos desde la página de detalle</p>
                </div>
                
                <!-- Botón Volver al Detalle -->
                <div class="info-card text-center">
                    <a href="<?php echo APP_URL; ?>/especialidades/detalle/<?php echo $id_especialidad; ?>" class="btn btn-info btn-block">
                        <i class="fas fa-arrow-left"></i> Volver al Detalle
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    console.log('=== FORMULARIO DE EDICIÓN DE ESPECIALIDAD ===');
    console.log('ID Especialidad:', $('#id_especialidad').val());
    
    // ==================== CARGAR DATOS DE LA ESPECIALIDAD ====================
    
    function cargarDatosEspecialidad() {
        let id = $('#id_especialidad').val();
        
        if (!id || id === '0') {
            mostrarError('ID de especialidad no válido');
            return;
        }
        
        $('#loadingDatos').show();
        
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
                    mostrarError('Error al cargar datos: ' + data.error);
                    return;
                }
                
                // Llenar formulario con los datos
                $('#nombre').val(data.nombre);
                $('#codigo').val(data.codigo || '');
                $('#descripcion').val(data.descripcion || '');
                $('#duracion_defecto').val(data.duracion_defecto || 30);
                $('#color').val(data.color || 'Azul Médico');
                $('#prioridad').val(data.prioridad || 'Media');
                $('#orden_visualizacion').val(data.orden_visualizacion || 0);
                $('#requisitos').val(data.requisitos || '');
                $('#observaciones').val(data.observaciones || '');
                $('#activo').prop('checked', data.activo == 1);
                
                // Actualizar vista previa
                actualizarPreview();
                
                // Actualizar color preview
                let colorMap = {
                    'Azul Médico': '#007bff',
                    'Verde Salud': '#28a745',
                    'Rojo Urgencias': '#dc3545',
                    'Amarillo Precaución': '#ffc107',
                    'Púrpura Especial': '#6f42c1',
                    'Naranja': '#fd7e14'
                };
                $('#color_preview').css('background-color', colorMap[data.color] || '#007bff');
                
                // Actualizar texto del estado
                $('#estado_texto').text(data.activo == 1 ? 'Activo' : 'Inactivo');
                
                $('#loadingDatos').hide();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar datos:', error);
                $('#loadingDatos').hide();
                mostrarError('Error al cargar los datos de la especialidad: ' + status);
            }
        });
    }
    
    // ==================== VISTA PREVIA EN TIEMPO REAL ====================
    
    function actualizarPreview() {
        // Nombre
        let nombre = $('#nombre').val();
        $('#preview_nombre').text(nombre || 'Nombre de la Especialidad');
        
        // Descripción
        let descripcion = $('#descripcion').val();
        if (descripcion) {
            $('#preview_descripcion').html(descripcion.length > 80 ? 
                descripcion.substring(0, 80) + '...' : descripcion);
        } else {
            $('#preview_descripcion').html('<em class="text-muted">Sin descripción</em>');
        }
        
        // Duración
        let duracion = $('#duracion_defecto').val();
        $('#preview_duracion').text(duracion + ' minutos');
        
        // Prioridad
        let prioridad = $('#prioridad').val();
        let prioridadBadge = getPrioridadBadge(prioridad);
        $('#preview_prioridad').html(prioridadBadge);
        
        // Color
        let color = $('#color').val();
        $('#preview_color').text(color);
        
        // Estado
        let activo = $('#activo').is(':checked');
        let estadoTexto = activo ? 'Activo' : 'Inactivo';
        let estadoColor = activo ? '#28a745' : '#dc3545';
        $('#preview_estado').html(`<span style="color: ${estadoColor};">${estadoTexto}</span>`);
        
        // Badge
        if (activo) {
            $('#preview_badge').html('<i class="fas fa-check-circle"></i> Especialidad activa');
            $('#preview_badge').css({'background': '#e8f4f8', 'color': '#0d9488'});
        } else {
            $('#preview_badge').html('<i class="fas fa-ban"></i> Especialidad inactiva');
            $('#preview_badge').css({'background': '#f8d7da', 'color': '#721c24'});
        }
    }
    
    function getPrioridadBadge(prioridad) {
        let bgColor = '';
        let textColor = '';
        switch(prioridad) {
            case 'Alta': bgColor = '#fde68a'; textColor = '#92400e'; break;
            case 'Media': bgColor = '#dbeafe'; textColor = '#1e40af'; break;
            case 'Baja': bgColor = '#e5e7eb'; textColor = '#374151'; break;
            case 'Urgente': bgColor = '#fee2e2'; textColor = '#991b1b'; break;
            default: bgColor = '#dbeafe'; textColor = '#1e40af';
        }
        return `<span style="background-color: ${bgColor}; color: ${textColor}; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem;">${prioridad}</span>`;
    }
    
    // Eventos para actualizar vista previa
    $('#nombre, #descripcion, #duracion_defecto, #prioridad, #color, #activo').on('input change', function() {
        actualizarPreview();
    });
    
    // Evento para el switch de estado
    $('#activo').on('change', function() {
        let estadoTexto = $(this).is(':checked') ? 'Activo' : 'Inactivo';
        $('#estado_texto').text(estadoTexto);
        actualizarPreview();
    });
    
    // Evento para cambio de color
    $('#color').on('change', function() {
        let colorMap = {
            'Azul Médico': '#007bff',
            'Verde Salud': '#28a745',
            'Rojo Urgencias': '#dc3545',
            'Amarillo Precaución': '#ffc107',
            'Púrpura Especial': '#6f42c1',
            'Naranja': '#fd7e14'
        };
        let colorHex = colorMap[$(this).val()] || '#007bff';
        $('#color_preview').css('background-color', colorHex);
        actualizarPreview();
    });
    
    // ==================== BOTÓN CANCELAR ====================
    $('#btnCancelar').click(function() {
        let id = $('#id_especialidad').val();
        if (confirm('¿Está seguro que desea cancelar? Los cambios no guardados se perderán.')) {
            window.location.href = APP_URL + '/especialidades/detalle/' + id;
        }
    });
    
    // ==================== ENVÍO DEL FORMULARIO ====================
    $('#formEditarEspecialidad').submit(function(e) {
        e.preventDefault();
        
        // Validaciones
        let nombre = $('#nombre').val().trim();
        if (!nombre) {
            mostrarError('El nombre de la especialidad es requerido');
            $('#nombre').focus();
            return;
        }
        
        let id = $('#id_especialidad').val();
        if (!id || id === '0') {
            mostrarError('ID de especialidad no válido');
            return;
        }
        
        // Recopilar datos
        let datos = {
            id_especialidad: id,
            nombre: nombre,
            descripcion: $('#descripcion').val().trim(),
            codigo: $('#codigo').val().trim(),
            duracion_defecto: $('#duracion_defecto').val(),
            color: $('#color').val(),
            prioridad: $('#prioridad').val(),
            orden_visualizacion: $('#orden_visualizacion').val(),
            requisitos: $('#requisitos').val().trim(),
            observaciones: $('#observaciones').val().trim(),
            activo: $('#activo').is(':checked') ? 1 : 0,
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        console.log('Enviando datos de actualización:', datos);
        
        let $btn = $('#btnGuardar');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
        
        $.ajax({
            url: APP_URL + '/api/especialidades/editar',
            type: 'POST',
            data: datos,
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.resultado === 'editado') {
                    mostrarExito('Especialidad actualizada exitosamente');
                    setTimeout(function() {
                        window.location.href = APP_URL + '/especialidades/detalle/' + id;
                    }, 2000);
                } else if (response.resultado === 'error_csrf') {
                    mostrarError('Error de seguridad. Por favor, recargue la página.');
                    $btn.prop('disabled', false).html(originalText);
                } else {
                    let errorMsg = response.error || response.message || 'Error al actualizar la especialidad';
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
    
    // ==================== INICIALIZAR ====================
    cargarDatosEspecialidad();
});

</script>