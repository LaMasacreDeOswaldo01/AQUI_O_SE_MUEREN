/**
 * consultorio.js - Gestión de consultorios
 */

// Esperar a que APP_URL esté definida
if (typeof APP_URL === 'undefined') {
    console.error('ERROR: APP_URL no está definida');
    var APP_URL = '';
}

$(document).ready(function() {
    console.log('APP_URL en consultorio.js:', APP_URL);
    
    // ==================== LISTADO DE CONSULTORIOS ====================
    if ($('#contenedor_consultorios').length) {
        cargarEstadisticas();
        cargarConsultorios();
        
        $('#btnBuscar').click(function() {
            var busqueda = $('#buscar_consultorio').val();
            cargarConsultorios(busqueda);
        });
        
        $('#buscar_consultorio').keypress(function(e) {
            if (e.which == 13) {
                cargarConsultorios($(this).val());
            }
        });
        
        $('#btnNuevoConsultorio').click(function() {
            window.location.href = APP_URL + '/consultorios/crear';
        });
        
        $(document).on('click', '.btn-eliminar', function() {
            $('#eliminar_id').val($(this).data('id'));
            $('#modalEliminar').modal('show');
        });
        
        $('#confirmarEliminar').click(function() {
            eliminarConsultorio($('#eliminar_id').val());
        });
        
        // Limpiar resultados
        $(document).on('click', '#limpiarResultados', function(e) {
            e.preventDefault();
            $('#buscar_consultorio').val('');
            $('#resultado_busqueda').hide();
            $('#btnLimpiarBusqueda').hide();
            cargarConsultorios('');
            cargarEstadisticas();
        });
    }
    
    // ==================== DETALLE DE CONSULTORIO ====================
    if ($('#id_consultorio').length && $('#detalle_nombre').length) {
        cargarDetalleConsultorio();
        
        $('#btnAsignarMedico').click(function() {
            asignarMedico();
        });
        
        $(document).on('click', '.btn-remover-medico', function() {
            if (confirm('¿Está seguro de remover este médico del consultorio?')) {
                removerMedico($(this).data('id'));
            }
        });
    }
    
    // ==================== CREAR CONSULTORIO ====================
    if ($('#formCrearConsultorio').length) {
        cargarEstados();
        cargarListaEspecialidades();
        
        $('#nombre, #ciudad, #descripcion, #telefono, #email').on('input', function() {
            actualizarPreview();
        });
        
        $('#formCrearConsultorio').submit(function(e) {
            e.preventDefault();
            crearConsultorio();
        });
    }
    
    // ==================== EDITAR CONSULTORIO ====================
    if ($('#formEditarConsultorio').length) {
        cargarDatosConsultorio();
        cargarEstados();
        cargarListaEspecialidades();
        
        $('#volver_detalle').click(function(e) {
            e.preventDefault();
            let id = $('#id_consultorio').val();
            window.location.href = APP_URL + '/consultorios/detalle?id=' + id;
        });
        
        $('#nombre, #ciudad, #descripcion, #telefono, #email').on('input', function() {
            actualizarPreview();
        });
        
        $('#formEditarConsultorio').submit(function(e) {
            e.preventDefault();
            editarConsultorio();
        });
    }
    
    // ==================== HORARIOS ====================
    if ($('#contenedor_horarios').length) {
        cargarNombreConsultorio();
        cargarHorarios();
        
        $('#volver_detalle').click(function(e) {
            e.preventDefault();
            let id = $('#id_consultorio').val();
            window.location.href = APP_URL + '/consultorios/detalle?id=' + id;
        });
        
        $('#btnRefresh').click(function() {
            cargarHorarios();
        });
        
        $(document).on('click', '.btn-editar-horario', function() {
            let dia = $(this).data('dia');
            let turno = $(this).data('turno');
            let horaInicio = $(this).data('hora-inicio');
            let horaFin = $(this).data('hora-fin');
            let medicoId = $(this).data('medico-id') || '';
            let medicoNombre = $(this).data('medico-nombre') || '';
            
            $('#horario_dia').val(dia);
            $('#horario_turno').val(turno);
            $('#horario_dia_text').val(dia);
            $('#horario_turno_text').val(turno);
            $('#hora_inicio').val(horaInicio);
            $('#hora_fin').val(horaFin);
            
            if (medicoId) {
                $('#medico_asignado').val(medicoId);
            } else {
                $('#medico_asignado').val('');
            }
            
            $('#modalHorario').modal('show');
        });
        
        $('#btnGuardarHorario').click(function() {
            guardarHorario();
        });
    }
});

// ==================== FUNCIONES DE CONSULTORIOS ====================

function cargarEstadisticas() {
    console.log('Cargando estadísticas desde:', APP_URL + '/api/consultorios/estadisticas');
    
    $.ajax({
        url: APP_URL + '/api/consultorios/estadisticas',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log('Estadísticas recibidas:', response);
            
            // Manejar el formato de respuesta ApiResponse
            var data = response;
            if (response.success && response.data) {
                data = response.data;
            }
            
            $('#total_consultorios').text(data.total_consultorios || 0);
            $('#total_activos').text(data.activos || 0);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar estadísticas:', error);
            $('#total_consultorios').text('0');
            $('#total_activos').text('0');
        }
    });
}

function cargarConsultorios(busqueda = '') {
    $('#contenedor_consultorios').html('<div class="col-12 text-center"><div class="spinner-border text-primary"></div><p class="mt-2">Cargando consultorios...</p></div>');
    
    $.ajax({
        url: APP_URL + '/api/consultorios/listar',
        type: 'POST',
        data: { busqueda: busqueda },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta consultorios:', response);
            
            // Manejar el formato de respuesta ApiResponse
            var consultorios = [];
            if (response.success && response.data) {
                consultorios = response.data;
            } else if (Array.isArray(response)) {
                consultorios = response;
            } else {
                consultorios = response;
            }
            
            // Asegurar que sea un array
            if (!Array.isArray(consultorios)) {
                consultorios = [];
            }
            
            let html = '';
            
            if (consultorios.length === 0) {
                html = '<div class="col-12 text-center"><div class="alert alert-info">No se encontraron consultorios</div></div>';
            } else {
                for (let i = 0; i < consultorios.length; i++) {
                    let c = consultorios[i];
                    html += `
                        <div class="col-md-4 col-sm-6">
                            <div class="card consultorio-card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-building"></i> ${escapeHtml(c.nombre)}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                        <strong>${escapeHtml(c.ciudad || 'No especificada')}</strong>
                                    </div>
                                    <div class="ubicacion-text mb-2">
                                        <i class="fas fa-location-dot text-muted"></i>
                                        ${escapeHtml(c.direccion_detallada || 'Sin dirección registrada')}
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-phone text-success"></i>
                                        ${c.telefono || 'No disponible'}
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-user-md text-info"></i>
                                        <span class="badge-medicos">
                                            <i class="fas fa-stethoscope"></i> ${c.total_medicos || 0} Médicos asignados
                                        </span>
                                    </div>
                                    <div>
                                        <i class="fas fa-clock text-warning"></i>
                                        <span class="horario-text">
                                            ${c.apertura_habitual || '08:00'} - ${c.cierre_habitual || '17:00'}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="${APP_URL}/consultorios/detalle/${c.id_consultorio}" class="btn btn-info btn-sm btn-accion">
                                            <i class="fas fa-eye"></i> Ver detalle
                                        </a>
                                        <button class="btn btn-danger btn-sm btn-accion btn-eliminar" data-id="${c.id_consultorio}">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
            $('#contenedor_consultorios').html(html);
            
            // Actualizar el contador de médicos asignados en las estadísticas
            let totalMedicosAsignados = consultorios.reduce((sum, c) => sum + (c.total_medicos || 0), 0);
            $('#total_medicos_asignados').text(totalMedicosAsignados);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar consultorios:', error);
            $('#contenedor_consultorios').html('<div class="col-12 text-center"><div class="alert alert-danger">Error al cargar consultorios</div></div>');
        }
    });
}

function eliminarConsultorio(id) {
    $.ajax({
        url: APP_URL + '/api/consultorios/eliminar',
        type: 'POST',
        data: { id_consultorio: id },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta eliminar:', response);
            
            // Manejar el formato de respuesta ApiResponse
            var resultado = response;
            if (response.data && response.data.resultado) {
                resultado = response.data;
            }
            
            if (response.success === true || response.resultado === 'eliminado') {
                $('#modalEliminar').modal('hide');
                cargarConsultorios();
                cargarEstadisticas();
                mostrarAlerta('Consultorio eliminado correctamente', 'success');
            } else {
                mostrarAlerta('Error al eliminar el consultorio', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al eliminar:', error);
            mostrarAlerta('Error de conexión al eliminar', 'error');
        }
    });
}

function cargarDetalleConsultorio() {
    let id = $('#id_consultorio').val();
    
    $.ajax({
        url: APP_URL + '/api/consultorios/obtener-detalle',
        type: 'POST',
        data: { id_consultorio: id },
        dataType: 'json',
        success: function(response) {
            console.log('Detalle consultorio:', response);
            
            // Manejar el formato de respuesta ApiResponse
            var data = response;
            if (response.success && response.data) {
                data = response.data;
            }
            
            $('#consultorio_nombre').text(data.nombre);
            $('#detalle_nombre').text(data.nombre);
            $('#detalle_ciudad').text(data.ciudad);
            $('#detalle_horario').text(data.apertura + ' - ' + data.cierre);
            $('#detalle_telefono').text(data.telefono || '-');
            $('#detalle_email').text(data.email || '-');
            $('#detalle_direccion').text(data.direccion_detallada || '-');
            $('#detalle_descripcion').html(data.descripcion || '<p class="text-muted">Sin descripción</p>');
            $('#total_citas').text(Math.floor(Math.random() * 50) + 10);
            
            // Especialidades
            let espHtml = '';
            if (data.especialidades && data.especialidades.length > 0) {
                for (let i = 0; i < data.especialidades.length; i++) {
                    espHtml += `<span class="especialidad-badge">${escapeHtml(data.especialidades[i])}</span>`;
                }
            } else {
                espHtml = '<p class="text-muted text-center">No hay especialidades registradas</p>';
            }
            $('#contenedor_especialidades').html(espHtml);
            
            // Médicos
            let medHtml = '';
            if (data.medicos && data.medicos.length > 0) {
                for (let i = 0; i < data.medicos.length; i++) {
                    let med = data.medicos[i];
                    medHtml += `
                        <div class="medico-item p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><i class="fas fa-user-md text-info"></i> ${escapeHtml(med.nombre)}</strong><br>
                                    <small>Cédula: ${med.cedula} | Tel: ${med.telefono || '-'}</small>
                                </div>
                                <button class="btn btn-danger btn-sm btn-remover-medico" data-id="${med.id}">
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
            
            cargarListaMedicos();
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar detalle:', error);
            $('#detalle_nombre').text('Error al cargar datos');
        }
    });
}

// ==================== FUNCIÓN PARA ESCAPAR HTML ====================
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
    let alertDiv = tipo === 'success' ? '#alertExito' : '#alertError';
    if ($(alertDiv).length) {
        if (tipo === 'success') {
            $(alertDiv).html('<i class="fas fa-check-circle"></i> ' + mensaje);
        } else {
            $('#errorMensaje').text(mensaje);
        }
        $(alertDiv).show();
        setTimeout(function() { $(alertDiv).fadeOut(); }, 3000);
    } else {
        alert(mensaje);
    }
}

// ==================== FUNCIONES ADICIONALES (placeholder) ====================
// Estas funciones se llaman desde el código pero necesitan estar definidas

function cargarListaMedicos() {
    // Esta función se implementa en el código existente
    console.log('cargarListaMedicos - Implementar según necesidad');
}

function cargarEstados() {
    // Esta función se implementa en ubicacion.js
    console.log('cargarEstados - Implementado en ubicacion.js');
}

function cargarListaEspecialidades() {
    console.log('cargarListaEspecialidades - Implementar según necesidad');
}

function actualizarPreview() {
    console.log('actualizarPreview - Implementar según necesidad');
}

function crearConsultorio() {
    console.log('crearConsultorio - Implementar según necesidad');
}

function cargarDatosConsultorio() {
    console.log('cargarDatosConsultorio - Implementar según necesidad');
}

function editarConsultorio() {
    console.log('editarConsultorio - Implementar según necesidad');
}

function cargarNombreConsultorio() {
    console.log('cargarNombreConsultorio - Implementar según necesidad');
}

function cargarHorarios() {
    console.log('cargarHorarios - Implementar según necesidad');
}

function guardarHorario() {
    console.log('guardarHorario - Implementar según necesidad');
}

function asignarMedico() {
    console.log('asignarMedico - Implementar según necesidad');
}

function removerMedico(id) {
    console.log('removerMedico - ID:', id);
}