/**
 * especialidades.js - Gestión de especialidades médicas
 */

// Esperar a que APP_URL esté definida
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
    }
    
    // ==================== DETALLE DE ESPECIALIDAD ====================
    if ($('#id_especialidad').length && $('#detalle_nombre').length) {
        cargarDetalleEspecialidad();
        
        $('#btnAsignarMedico').click(function() {
            var id = $('#id_especialidad').val();
            window.location.href = APP_URL + '/especialidades/asignar-medico/' + id;
        });
        
        $(document).on('click', '.btn-remover-medico', function() {
            if (confirm('¿Está seguro de remover este médico de la especialidad?')) {
                removerMedico($(this).data('id'));
            }
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
            console.log('Respuesta estadísticas:', response);
            
            // Manejar formato ApiResponse
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
        success: function(response) {
            console.log('Respuesta completa del servidor:', response);
            
            // ========== MANEJAR FORMATO ApiResponse ==========
            var especialidades = [];
            
            // Si la respuesta tiene el formato ApiResponse (success + data)
            if (response.success && response.data) {
                especialidades = response.data;
                console.log('Especialidades extraídas de ApiResponse.data:', especialidades);
            } 
            // Si es un array directo
            else if (Array.isArray(response)) {
                especialidades = response;
                console.log('Especialidades es un array directo:', especialidades);
            }
            // Si tiene propiedad especialidades
            else if (response.especialidades && Array.isArray(response.especialidades)) {
                especialidades = response.especialidades;
                console.log('Especialidades extraídas de response.especialidades:', especialidades);
            }
            // Otro formato
            else {
                console.warn('Formato de respuesta no reconocido:', response);
                especialidades = [];
            }
            
            // Asegurar que sea un array
            if (!Array.isArray(especialidades)) {
                console.error('especialidades no es un array:', especialidades);
                especialidades = [];
            }
            
            console.log('Especialidades procesadas (cantidad):', especialidades.length);
            
            let html = '';
            
            if (especialidades.length === 0) {
                html = '<div class="col-12 text-center"><div class="alert alert-info">No se encontraron especialidades</div></div>';
            } else {
                for (let i = 0; i < especialidades.length; i++) {
                    let esp = especialidades[i];
                    let colorClass = getColorClass(esp.color);
                    
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
                                    </div>
                                    <div>
                                        <i class="fas fa-clock text-warning"></i>
                                        <span class="horario-text">
                                            ${esp.duracion_defecto || 30} minutos por cita
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="${APP_URL}/especialidades/detalle/${esp.id_especialidad}" class="btn btn-info btn-sm btn-accion">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <button class="btn btn-danger btn-sm btn-accion btn-eliminar" data-id="${esp.id_especialidad}">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
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
            console.error('Respuesta del servidor:', xhr.responseText);
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
function eliminarEspecialidad(id) {
    $.ajax({
        url: APP_URL + '/api/especialidades/eliminar',
        type: 'POST',
        data: { id_especialidad: id },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta eliminar:', response);
            
            // Manejar formato ApiResponse
            if (response.success === true || response.resultado === 'eliminado') {
                $('#modalEliminar').modal('hide');
                cargarEspecialidades();
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
function cargarDetalleEspecialidad() {
    let id = $('#id_especialidad').val();
    
    $.ajax({
        url: APP_URL + '/api/especialidades/obtener-detalle',
        type: 'POST',
        data: { id_especialidad: id },
        dataType: 'json',
        success: function(data) {
            console.log('Detalle especialidad:', data);
            
            $('#nombre_especialidad').text(data.nombre);
            $('#detalle_nombre').text(data.nombre);
            $('#detalle_codigo').text(data.codigo || 'Sin código');
            $('#detalle_descripcion').text(data.descripcion || 'Sin descripción');
            $('#detalle_duracion').text(data.duracion + ' minutos');
            $('#detalle_prioridad').text(data.prioridad);
            
            // Mostrar color con indicador
            let colorClass = getColorClass(data.color);
            $('#detalle_color').html(`<span class="color-indicador" style="background-color: ${getColorHex(data.color)}"></span> ${data.color}`);
            
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
            $('#duracion_min').text(data.duracion || 0);
            
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
            
            // Médicos asignados
            let medHtml = '';
            if (data.medicos && data.medicos.length > 0) {
                for (let med of data.medicos) {
                    medHtml += `
                        <div class="medico-item p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><i class="fas fa-user-md text-info"></i> ${escapeHtml(med.nombre)}</strong><br>
                                    <small>MPPS: ${med.mpps || 'N/A'}</small>
                                </div>
                                <button class="btn btn-danger btn-sm btn-remover-medico" data-id="${med.id_medico}">
                                    <i class="fas fa-user-minus"></i> Remover
                                </button>
                            </div>
                        </div>
                    `;
                }
            } else {
                medHtml = '<p class="text-muted text-center">No hay médicos asignados</p>';
            }
            $('#contenedor_medicos').html(medHtml);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar detalle:', error);
            $('#detalle_nombre').text('Error al cargar datos');
        }
    });
}

function removerMedico(id_medico) {
    let id_especialidad = $('#id_especialidad').val();
    
    $.ajax({
        url: APP_URL + '/api/especialidades/remover-medico',
        type: 'POST',
        data: { id_especialidad: id_especialidad, id_medico: id_medico },
        dataType: 'json',
        success: function(response) {
            if (response.resultado === 'removido') {
                mostrarAlerta('Médico removido de la especialidad', 'success');
                cargarDetalleEspecialidad();
            } else {
                mostrarAlerta('Error al remover el médico', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al remover médico:', error);
            mostrarAlerta('Error de conexión', 'error');
        }
    });
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

// ==================== UTILIDADES ====================

function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function mostrarAlerta(mensaje, tipo) {
    // Usar SweetAlert si está disponible, si no, alert normal
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: tipo === 'success' ? 'Éxito' : 'Error',
            text: mensaje,
            icon: tipo,
            confirmButtonText: 'Aceptar'
        });
    } else {
        alert((tipo === 'success' ? '✓ ' : '✗ ') + mensaje);
    }
}