if (typeof APP_URL === 'undefined') {
    console.error('ERROR: APP_URL no está definida');
    var APP_URL = '';
}

$(document).ready(function() {
    console.log('APP_URL en especialidades.js:', APP_URL);
    
    // ==================== LISTADO DE ESPECIALIDADES ====================
    if ($('#contenedor_especialidades').length) {
        cargarEstadisticas();
        cargarEspecialidades();
        
        $('#btnBuscar').click(function() {
            var busqueda = $('#buscar_especialidad').val();
            var estado = $('#filtro_estado').val();
            cargarEspecialidades(busqueda, estado);
        });
        
        $('#buscar_especialidad').keypress(function(e) {
            if (e.which == 13) {
                cargarEspecialidades($(this).val(), $('#filtro_estado').val());
            }
        });
        
        $('#filtro_estado').change(function() {
            cargarEspecialidades($('#buscar_especialidad').val(), $(this).val());
        });
        
        $(document).on('click', '.btn-eliminar', function() {
            $('#eliminar_id').val($(this).data('id'));
            $('#modalEliminar').modal('show');
        });
        
        $('#confirmarEliminar').click(function() {
            eliminarEspecialidad($('#eliminar_id').val());
        });
        
        $('#btnNuevaEspecialidad').click(function() {
            window.location.href = APP_URL + '/especialidades/crear';
        });
        
        $('#btnRefresh').click(function() {
            cargarEspecialidades('', 'todas');
            cargarEstadisticas();
        });
    }
    
    // ==================== DETALLE DE ESPECIALIDAD ====================
    if ($('#id_especialidad').length) {
        console.log('=== CARGANDO DETALLE DE ESPECIALIDAD ===');
        console.log('ID Especialidad:', $('#id_especialidad').val());
        
        cargarDetalleEspecialidad();
        
        // Botones para asignar médico (usando delegación de eventos)
        $(document).on('click', '#btnAsignarMedico, #btnAsignarMedicoHeader, #btnAsignarMedicoFooter, #btnAsignarMedicoEmpty', function() {
            console.log('Botón Asignar Médico clickeado');
            // Limpiar el modal antes de abrirlo
            $('#medico_seleccionado').html('<option value="">Cargando médicos...</option>');
            $('#tarifa').val('');
            $('#exp_anios').val('');
            $('#extra').val('');
            $('#domicilio').prop('checked', false);
            $('#mensaje_asignacion').hide();
            // Cargar la lista de médicos disponibles
            cargarListaMedicosDisponibles();
            $('#modalAsignarMedico').modal('show');
        });
        
        // Botón guardar asignación
        $('#btnGuardarAsignacion').off('click').on('click', function() {
            asignarMedicoEspecialidad();
        });
        
        // Botón editar
        $('#btnEditar').click(function() {
            var id = $('#id_especialidad').val();
            window.location.href = APP_URL + '/especialidades/editar?id=' + id;
        });
        
        // Botón volver
        $('#btnVolver').click(function() {
            window.location.href = APP_URL + '/especialidades';
        });
        
        // Remover médico (delegación de eventos)
        $(document).on('click', '.btn-remover-medico', function() {
            var idAsignacion = $(this).data('id');
            var nombreMedico = $(this).data('nombre');
            if (confirm(`¿Está seguro de remover a ${nombreMedico} de esta especialidad?`)) {
                removerMedicoEspecialidad(idAsignacion);
            }
        });
    }
    
    // ==================== CREAR ESPECIALIDAD ====================
    if ($('#formCrearEspecialidad').length) {
        actualizarPreview();
        
        $('#nombre, #descripcion, #duracion_defecto, #prioridad, #color').on('input change', function() {
            actualizarPreview();
        });
        
        $('#color').on('change', function() {
            var colorMap = {
                'Azul Médico': '#007bff',
                'Verde Salud': '#28a745',
                'Rojo Urgencias': '#dc3545',
                'Amarillo Precaución': '#ffc107',
                'Púrpura Especial': '#6f42c1',
                'Naranja': '#fd7e14'
            };
            var colorHex = colorMap[$(this).val()] || '#007bff';
            $('#color_preview').css('background-color', colorHex);
            actualizarPreview();
        });
        
        $('#btnCancelar').click(function() {
            if (confirm('¿Está seguro que desea cancelar? Los datos no guardados se perderán.')) {
                window.location.href = APP_URL + '/especialidades';
            }
        });
        
        $('#formCrearEspecialidad').submit(function(e) {
            e.preventDefault();
            crearEspecialidad();
        });
    }
    
    // ==================== EDITAR ESPECIALIDAD ====================
    if ($('#formEditarEspecialidad').length) {
        cargarDatosEspecialidad();
        
        $('#nombre, #descripcion, #duracion_defecto, #prioridad, #color, #activo').on('input change', function() {
            actualizarPreviewEditar();
        });
        
        $('#color').on('change', function() {
            var colorMap = {
                'Azul Médico': '#007bff',
                'Verde Salud': '#28a745',
                'Rojo Urgencias': '#dc3545',
                'Amarillo Precaución': '#ffc107',
                'Púrpura Especial': '#6f42c1',
                'Naranja': '#fd7e14'
            };
            var colorHex = colorMap[$(this).val()] || '#007bff';
            $('#color_preview').css('background-color', colorHex);
            actualizarPreviewEditar();
        });
        
        $('#activo').on('change', function() {
            var estadoTexto = $(this).is(':checked') ? 'Activo' : 'Inactivo';
            $('#estado_texto').text(estadoTexto);
            actualizarPreviewEditar();
        });
        
        $('#btnCancelar').click(function() {
            var id = $('#id_especialidad').val();
            if (confirm('¿Está seguro que desea cancelar? Los cambios no guardados se perderán.')) {
                window.location.href = APP_URL + '/especialidades/detalle/' + id;
            }
        });
        
        $('#formEditarEspecialidad').submit(function(e) {
            e.preventDefault();
            editarEspecialidad();
        });
    }
});

// ==================== FUNCIONES DE ESPECIALIDADES ====================

function cargarEstadisticas() {
    console.log('Cargando estadísticas desde:', APP_URL + '/api/especialidades/estadisticas');
    
    $.ajax({
        url: APP_URL + '/api/especialidades/estadisticas',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            var data = response;
            if (response.success && response.data) {
                data = response.data;
            }
            
            $('#total_especialidades').text(data.total_especialidades || 0);
            $('#total_activas').text(data.activas || 0);
            $('#total_medicos').text(data.total_medicos || 0);
            $('#citas_mes').text(data.citas_mes || 0);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar estadísticas:', error);
            $('#total_especialidades').text('0');
            $('#total_activas').text('0');
            $('#total_medicos').text('0');
            $('#citas_mes').text('0');
        }
    });
}

function cargarEspecialidades(busqueda = '', estado = 'todas') {
    $('#contenedor_especialidades').html('<div class="col-12 text-center"><div class="spinner-border text-primary"></div><p class="mt-2">Cargando especialidades...</p></div>');
    
    $.ajax({
        url: APP_URL + '/api/especialidades/listar',
        type: 'POST',
        data: { busqueda: busqueda, estado: estado },
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            var especialidades = [];
            
            if (response.success && response.data) {
                especialidades = response.data;
            } else if (Array.isArray(response)) {
                especialidades = response;
            } else if (response.especialidades && Array.isArray(response.especialidades)) {
                especialidades = response.especialidades;
            }
            
            if (!Array.isArray(especialidades)) {
                especialidades = [];
            }
            
            let html = '';
            
            if (especialidades.length === 0) {
                html = '<div class="col-12 text-center"><div class="alert alert-info">No se encontraron especialidades</div></div>';
            } else {
                for (let i = 0; i < especialidades.length; i++) {
                    let esp = especialidades[i];
                    let colorClass = getColorClass(esp.color);
                    let prioridadClass = getPrioridadClass(esp.prioridad);
                    let estadoClass = esp.activo == 1 ? '' : 'badge-estado-inactiva';
                    let estadoTexto = esp.activo == 1 ? 'Activa' : 'Inactiva';
                    
                    html += `
                        <div class="col-md-4 col-sm-6">
                            <div class="especialidad-card h-100">
                                <div class="card-header ${colorClass}">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-stethoscope"></i> ${escapeHtml(esp.nombre)}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="descripcion-text mb-3">
                                        ${escapeHtml(esp.descripcion || 'Sin descripción')}
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-user-md text-info"></i>
                                        <span class="badge-medicos">
                                            <i class="fas fa-stethoscope"></i> ${esp.total_medicos || 0} Médicos
                                        </span>
                                        &nbsp;
                                        <span class="badge-estado ${estadoClass}">
                                            <i class="fas ${esp.activo == 1 ? 'fa-check-circle' : 'fa-ban'}"></i> ${estadoTexto}
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-clock text-warning"></i>
                                        <span>${esp.duracion_defecto || 30} minutos por cita</span>
                                        &nbsp;
                                        <span class="prioridad-badge ${prioridadClass}">
                                            <i class="fas fa-chart-line"></i> ${esp.prioridad || 'Media'}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="color-indicador" style="background-color: ${getColorHex(esp.color)}"></span>
                                        <small class="text-muted">${esp.color || 'Azul Médico'}</small>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="${APP_URL}/especialidades/detalle/${esp.id_especialidad}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="${APP_URL}/especialidades/editar/${esp.id_especialidad}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        ${esp.activo == 1 ? `
                                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${esp.id_especialidad}">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
            
            $('#contenedor_especialidades').html(html);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar especialidades:', error);
            $('#contenedor_especialidades').html('<div class="col-12 text-center"><div class="alert alert-danger">Error al cargar especialidades</div></div>');
        }
    });
}

function getColorClass(color) {
    const colorMap = {
        'Azul Médico': 'bg-primary',
        'Verde Salud': 'bg-success',
        'Rojo Urgencias': 'bg-danger',
        'Amarillo Precaución': 'bg-warning',
        'Púrpura Especial': 'bg-purple',
        'Naranja': 'bg-orange'
    };
    return colorMap[color] || 'bg-primary';
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

function getPrioridadClass(prioridad) {
    const prioridadMap = {
        'Alta': 'prioridad-alta',
        'Media': 'prioridad-media',
        'Baja': 'prioridad-baja',
        'Urgente': 'prioridad-urgente'
    };
    return prioridadMap[prioridad] || 'prioridad-media';
}

function eliminarEspecialidad(id) {
    $.ajax({
        url: APP_URL + '/api/especialidades/eliminar',
        type: 'POST',
        data: { id_especialidad: id, csrf_token: $('input[name="csrf_token"]').val() },
        dataType: 'json',
        success: function(response) {
            if (response.resultado === 'eliminado' || response.success === true) {
                $('#modalEliminar').modal('hide');
                cargarEspecialidades('', 'todas');
                cargarEstadisticas();
                mostrarAlerta(response.message || 'Especialidad eliminada correctamente', 'success');
            } else {
                mostrarAlerta(response.message || 'Error al eliminar la especialidad', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al eliminar:', error);
            mostrarAlerta('Error de conexión al eliminar', 'error');
        }
    });
}

// ==================== FUNCIONES PARA DETALLE DE ESPECIALIDAD ====================

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
            var data = response;
            if (response.success && response.data) {
                data = response.data;
            }
            
            if (data.error) {
                $('#detalle_nombre').text('Error: ' + data.error);
                return;
            }
            
            $('#nombre_especialidad').text(data.nombre);
            $('#detalle_nombre').text(data.nombre);
            $('#detalle_codigo').text(data.codigo || 'Sin código');
            $('#detalle_descripcion').text(data.descripcion || 'Sin descripción');
            $('#detalle_duracion').text(data.duracion_defecto + ' minutos');
            $('#detalle_prioridad').html(getPrioridadBadge(data.prioridad));
            
            let colorHex = getColorHex(data.color);
            $('#detalle_color').html(`<span class="color-indicador" style="background-color: ${colorHex}"></span> ${data.color}`);
            
            if (data.activo == 1) {
                $('#detalle_activo').html('<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activa</span>');
            } else {
                $('#detalle_activo').html('<span class="badge badge-secondary"><i class="fas fa-ban"></i> Inactiva</span>');
            }
            
            $('#total_medicos').text(data.medicos ? data.medicos.length : 0);
            $('#total_citas').text(data.total_citas || 0);
            $('#citas_pendientes').text(data.citas_pendientes || 0);
            $('#duracion_min').text(data.duracion_defecto || 0);
            
            if (data.requisitos && data.requisitos !== '') {
                $('#requisitos_container').show();
                $('#detalle_requisitos').text(data.requisitos);
            } else {
                $('#requisitos_container').hide();
            }
            
            if (data.observaciones && data.observaciones !== '') {
                $('#observaciones_container').show();
                $('#detalle_observaciones').text(data.observaciones);
            } else {
                $('#observaciones_container').hide();
            }
            
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
                <div class="medico-item p-3 border-bottom">
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
                                data-id="${med.id_asignacion || med.id}"
                                data-nombre="${escapeHtml(med.nombre)}">
                            <i class="fas fa-user-minus"></i> Remover
                        </button>
                    </div>
                </div>
            `;
        }
    }
    
    $('#contenedor_medicos').html(medHtml);
}

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

// ==================== FUNCIONES PARA ASIGNAR MÉDICO (CORREGIDAS) ====================

function cargarListaMedicosDisponibles() {
    let id_especialidad = $('#id_especialidad').val();
    
    console.log('=== CARGANDO MÉDICOS DISPONIBLES ===');
    console.log('ID Especialidad:', id_especialidad);
    console.log('URL:', APP_URL + '/api/especialidades/listar-medicos');
    
    // Mostrar loading en el select
    $('#medico_seleccionado').html('<option value="">Cargando médicos...</option>');
    
    $.ajax({
        url: APP_URL + '/api/especialidades/listar-medicos',
        type: 'POST',
        data: { id_especialidad: id_especialidad },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Respuesta completa:', response);
            
            // Extraer médicos de la respuesta (manejar diferentes formatos)
            let medicos = [];
            
            // Formato ApiResponse
            if (response.success && Array.isArray(response.data)) {
                medicos = response.data;
                console.log('Médicos encontrados (ApiResponse.data):', medicos.length);
            } 
            // Array directo
            else if (Array.isArray(response)) {
                medicos = response;
                console.log('Médicos encontrados (array directo):', medicos.length);
            }
            // Otro formato
            else if (response.medicos && Array.isArray(response.medicos)) {
                medicos = response.medicos;
                console.log('Médicos encontrados (response.medicos):', medicos.length);
            }
            else {
                console.warn('Formato de respuesta no reconocido:', response);
            }
            
            let options = '<option value="">Seleccione un médico...</option>';
            
            if (medicos.length === 0) {
                options = '<option value="" disabled>⚠️ No hay médicos disponibles para asignar</option>';
                $('#medico_seleccionado').prop('disabled', true);
                mostrarMensajeAsignacion('No hay médicos disponibles para asignar a esta especialidad', 'warning');
            } else {
                $('#medico_seleccionado').prop('disabled', false);
                for (let i = 0; i < medicos.length; i++) {
                    let med = medicos[i];
                    let id = med.id_medico || med.id;
                    let nombre = med.nombre_medico || med.nombre || '';
                    let apellido = med.apellido_medico || med.apellidos || '';
                    let cedula = med.cedula_medico || med.cedula || '';
                    let nombreCompleto = `${nombre} ${apellido}`.trim();
                    
                    options += `<option value="${id}">
                        ${escapeHtml(nombreCompleto)} - Cédula: ${cedula || 'N/A'}
                    </option>`;
                }
            }
            
            $('#medico_seleccionado').html(options);
            console.log('Select de médicos actualizado con', medicos.length, 'opciones');
        },
        error: function(xhr, status, error) {
            console.error('Error en la petición AJAX:', error);
            console.error('Status:', status);
            console.error('Respuesta del servidor:', xhr.responseText);
            
            $('#medico_seleccionado').html('<option value="">❌ Error al cargar médicos</option>');
            mostrarMensajeAsignacion('Error al cargar la lista de médicos disponibles: ' + status, 'danger');
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
    
    console.log('=== ASIGNAR MÉDICO ===');
    console.log('ID Especialidad:', id_especialidad);
    console.log('ID Médico:', id_medico);
    
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
            domicilio: domicilio,
            csrf_token: $('input[name="csrf_token"]').val()
        },
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            console.log('Respuesta:', response);
            
            if (response.success && response.code === 'asignado') {
                mostrarMensajeAsignacion('✅ Médico asignado correctamente', 'success');
                
                setTimeout(function() {
                    $('#modalAsignarMedico').modal('hide');
                    // Recargar SOLO el contenido de la página (sin recargar todo)
                    cargarDetalleEspecialidad();
                    // Limpiar el formulario del modal
                    $('#medico_seleccionado').val('');
                    $('#tarifa').val('');
                    $('#exp_anios').val('');
                    $('#extra').val('');
                    $('#domicilio').prop('checked', false);
                }, 1000);
                
            } else if (response.code === 'ya_asignado') {
                mostrarMensajeAsignacion('⚠️ El médico ya está asignado a esta especialidad', 'warning');
                $btn.prop('disabled', false).html(originalText);
            } else {
                mostrarMensajeAsignacion('❌ ' + (response.message || 'Error al asignar'), 'danger');
                $btn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            mostrarMensajeAsignacion('❌ Error de conexión: ' + status, 'danger');
            $btn.prop('disabled', false).html(originalText);
        }
    });
}

function removerMedicoEspecialidad(id_asignacion) {
    console.log('Removiendo médico con asignación ID:', id_asignacion);
    
    $.ajax({
        url: APP_URL + '/api/especialidades/remover-medico',
        type: 'POST',
        data: { 
            id_asignacion: id_asignacion,
            csrf_token: $('input[name="csrf_token"]').val()
        },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta remover médico:', response);
            
            if (response.resultado === 'removido') {
                mostrarAlerta('✅ Médico removido de la especialidad', 'success');
                cargarDetalleEspecialidad();
            } else {
                mostrarAlerta(response.message || 'Error al remover el médico', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            mostrarAlerta('Error de conexión: ' + status, 'error');
        }
    });
}

function mostrarMensajeAsignacion(mensaje, tipo) {
    let alertClass = tipo === 'success' ? 'alert-success' : (tipo === 'warning' ? 'alert-warning' : 'alert-danger');
    let iconClass = tipo === 'success' ? 'fa-check-circle' : (tipo === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle');
    
    let $mensajeDiv = $('#mensaje_asignacion');
    if ($mensajeDiv.length === 0) {
        $('#modalAsignarMedico .modal-body').prepend('<div id="mensaje_asignacion" class="alert" style="display:none;"></div>');
        $mensajeDiv = $('#mensaje_asignacion');
    }
    
    $mensajeDiv
        .removeClass('alert-success alert-warning alert-danger')
        .addClass(alertClass)
        .html('<i class="fas ' + iconClass + '"></i> ' + mensaje)
        .fadeIn(300);
    
    setTimeout(function() {
        $mensajeDiv.fadeOut(500);
    }, 4000);
}

// ==================== FUNCIONES PARA CREAR/EDITAR ESPECIALIDAD ====================

function actualizarPreview() {
    let nombre = $('#nombre').val();
    $('#preview_nombre').text(nombre || 'Nombre de la Especialidad');
    
    let descripcion = $('#descripcion').val();
    if (descripcion) {
        $('#preview_descripcion').html(descripcion.length > 80 ? descripcion.substring(0, 80) + '...' : descripcion);
    } else {
        $('#preview_descripcion').html('<em class="text-muted">Sin descripción</em>');
    }
    
    let duracion = $('#duracion_defecto').val();
    $('#preview_duracion').text(duracion + ' minutos');
    
    let prioridad = $('#prioridad').val();
    $('#preview_prioridad').text(prioridad);
    
    let color = $('#color').val();
    $('#preview_color').text(color);
}

function actualizarPreviewEditar() {
    let nombre = $('#nombre').val();
    $('#preview_nombre').text(nombre || 'Nombre de la Especialidad');
    
    let descripcion = $('#descripcion').val();
    if (descripcion) {
        $('#preview_descripcion').html(descripcion.length > 80 ? descripcion.substring(0, 80) + '...' : descripcion);
    } else {
        $('#preview_descripcion').html('<em class="text-muted">Sin descripción</em>');
    }
    
    let duracion = $('#duracion_defecto').val();
    $('#preview_duracion').text(duracion + ' minutos');
    
    let prioridad = $('#prioridad').val();
    $('#preview_prioridad').text(prioridad);
    
    let color = $('#color').val();
    $('#preview_color').text(color);
    
    let activo = $('#activo').is(':checked');
    let estadoTexto = activo ? 'Activo' : 'Inactivo';
    $('#preview_estado').text(estadoTexto);
}

function crearEspecialidad() {
    let nombre = $('#nombre').val().trim();
    if (!nombre) {
        mostrarError('El nombre de la especialidad es requerido');
        $('#nombre').focus();
        return;
    }
    
    let datos = {
        nombre: nombre,
        descripcion: $('#descripcion').val().trim(),
        codigo: $('#codigo').val().trim(),
        duracion_defecto: $('#duracion_defecto').val(),
        color: $('#color').val(),
        prioridad: $('#prioridad').val(),
        orden_visualizacion: $('#orden_visualizacion').val(),
        requisitos: $('#requisitos').val().trim(),
        observaciones: $('#observaciones').val().trim(),
        csrf_token: $('input[name="csrf_token"]').val()
    };
    
    console.log('Enviando datos:', datos);
    
    let $btn = $('#btnGuardar');
    let originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    
    $.ajax({
        url: APP_URL + '/api/especialidades/crear',
        type: 'POST',
        data: datos,
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            
            // Verificar si la creación fue exitosa
            if (response.resultado === 'creado' || (response.success && response.code === 'created')) {
                mostrarExito('Especialidad creada exitosamente');
                
                // Redirigir a la página de listado después de 2 segundos
                setTimeout(function() {
                    window.location.href = APP_URL + '/especialidades';
                }, 2000);
            } else if (response.resultado === 'error_csrf') {
                mostrarError('Error de seguridad. Por favor, recargue la página.');
                $btn.prop('disabled', false).html(originalText);
            } else {
                let errorMsg = response.error || response.message || 'Error al crear la especialidad';
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
}

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
            var data = response;
            if (response.success && response.data) {
                data = response.data;
            }
            
            if (data.error) {
                mostrarError('Error al cargar datos: ' + data.error);
                return;
            }
            
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
            
            actualizarPreviewEditar();
            
            let colorMap = {
                'Azul Médico': '#007bff',
                'Verde Salud': '#28a745',
                'Rojo Urgencias': '#dc3545',
                'Amarillo Precaución': '#ffc107',
                'Púrpura Especial': '#6f42c1',
                'Naranja': '#fd7e14'
            };
            $('#color_preview').css('background-color', colorMap[data.color] || '#007bff');
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

function editarEspecialidad() {
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
            if (response.resultado === 'editado') {
                mostrarExito('Especialidad actualizada exitosamente');
                setTimeout(function() {
                    window.location.href = APP_URL + '/especialidades/detalle/' + id;
                }, 2000);
            } else {
                let errorMsg = response.error || response.message || 'Error al actualizar la especialidad';
                mostrarError(errorMsg);
                $btn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en AJAX:', error);
            mostrarError('Error de conexión: ' + status);
            $btn.prop('disabled', false).html(originalText);
        }
    });
}

// ==================== FUNCIONES UTILITARIAS ====================

function mostrarAlerta(mensaje, tipo) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: tipo === 'success' ? 'Éxito' : (tipo === 'error' ? 'Error' : 'Aviso'),
            text: mensaje,
            icon: tipo,
            confirmButtonText: 'Aceptar'
        });
    } else {
        alert((tipo === 'success' ? '✓ ' : '✗ ') + mensaje);
    }
}

function mostrarError(mensaje) {
    $('#errorMensaje').text(mensaje);
    $('#alertError').fadeIn(300);
    setTimeout(function() {
        $('#alertError').fadeOut(500);
    }, 5000);
    
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

function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}