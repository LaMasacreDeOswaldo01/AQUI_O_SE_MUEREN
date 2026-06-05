/**
 * consultorio.js - Gestión de consultorios
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
 
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        success: function(response) {
            console.log('Estadísticas recibidas:', response);
            
            // Manejar el formato de respuesta ApiResponse
            var data = response;
            if (response.success && response.data) {
                data = response.data;
            }
            
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
        success: function(data) {
            console.log('Estadísticas recibidas:', data);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
            
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
        success: function(consultorios) {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            let html = '';
            
            if (consultorios.length === 0) {
                html = '<div class="col-12 text-center"><div class="alert alert-info">No se encontraron consultorios</div></div>';
            } else {
<<<<<<< HEAD
<<<<<<< HEAD
                for (let i = 0; i < consultorios.length; i++) {
                    let c = consultorios[i];
=======
<<<<<<< HEAD
                for (let i = 0; i < consultorios.length; i++) {
                    let c = consultorios[i];
=======
                for (let c of consultorios) {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                for (let i = 0; i < consultorios.length; i++) {
                    let c = consultorios[i];
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
<<<<<<< HEAD
        error: function(xhr, status, error) {
            console.error('Error al cargar consultorios:', error);
=======
<<<<<<< HEAD
        error: function(xhr, status, error) {
            console.error('Error al cargar consultorios:', error);
=======
        error: function() {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
        error: function(xhr, status, error) {
            console.error('Error al cargar consultorios:', error);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            
            // Manejar el formato de respuesta ApiResponse
            var resultado = response;
            if (response.data && response.data.resultado) {
                resultado = response.data;
            }
            
            if (response.success === true || response.resultado === 'eliminado') {
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
            if (response.resultado === 'eliminado') {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        success: function(response) {
            console.log('Detalle consultorio:', response);
            
            // Manejar el formato de respuesta ApiResponse
            var data = response;
            if (response.success && response.data) {
                data = response.data;
            }
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
        success: function(data) {
            console.log('Detalle consultorio:', data);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            
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
<<<<<<< HEAD
<<<<<<< HEAD
                for (let i = 0; i < data.especialidades.length; i++) {
                    espHtml += `<span class="especialidad-badge">${escapeHtml(data.especialidades[i])}</span>`;
=======
<<<<<<< HEAD
                for (let i = 0; i < data.especialidades.length; i++) {
                    espHtml += `<span class="especialidad-badge">${escapeHtml(data.especialidades[i])}</span>`;
=======
                for (let esp of data.especialidades) {
                    espHtml += `<span class="especialidad-badge">${escapeHtml(esp)}</span>`;
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                for (let i = 0; i < data.especialidades.length; i++) {
                    espHtml += `<span class="especialidad-badge">${escapeHtml(data.especialidades[i])}</span>`;
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                }
            } else {
                espHtml = '<p class="text-muted text-center">No hay especialidades registradas</p>';
            }
            $('#contenedor_especialidades').html(espHtml);
            
            // Médicos
            let medHtml = '';
            if (data.medicos && data.medicos.length > 0) {
<<<<<<< HEAD
<<<<<<< HEAD
                for (let i = 0; i < data.medicos.length; i++) {
                    let med = data.medicos[i];
=======
<<<<<<< HEAD
                for (let i = 0; i < data.medicos.length; i++) {
                    let med = data.medicos[i];
=======
                for (let med of data.medicos) {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                for (let i = 0; i < data.medicos.length; i++) {
                    let med = data.medicos[i];
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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

<<<<<<< HEAD
<<<<<<< HEAD
// ==================== FUNCIÓN PARA ESCAPAR HTML ====================
=======
<<<<<<< HEAD
// ==================== FUNCIÓN PARA ESCAPAR HTML ====================
=======
function cargarListaMedicos() {
    $.ajax({
        url: APP_URL + '/api/consultorios/listar-medicos',
        type: 'POST',
        dataType: 'json',
        success: function(medicos) {
            console.log('Médicos disponibles:', medicos);
            let options = '<option value="">Seleccione un médico...</option>';
            for (let med of medicos) {
                options += `<option value="${med.id_medico}">${escapeHtml(med.nombre_medico)} ${escapeHtml(med.apellido_medico)} (${med.cedula_medico})</option>`;
            }
            $('#medico_seleccionado').html(options);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar médicos:', error);
        }
    });
}

function asignarMedico() {
    let id_consultorio = $('#id_consultorio').val();
    let id_medico = $('#medico_seleccionado').val();
    
    if (!id_medico) {
        mostrarMensaje('Seleccione un médico', 'error');
        return;
    }
    
    $.ajax({
        url: APP_URL + '/api/consultorios/asignar-medico',
        type: 'POST',
        data: { id_consultorio: id_consultorio, id_medico: id_medico },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta asignar médico:', response);
            if (response.resultado === 'asignado') {
                mostrarMensaje('Médico asignado correctamente', 'success');
                setTimeout(function() {
                    $('#modalAsignarMedico').modal('hide');
                    cargarDetalleConsultorio();
                }, 1500);
            } else if (response.resultado === 'ya_asignado') {
                mostrarMensaje('El médico ya está asignado a este consultorio', 'error');
            } else {
                mostrarMensaje('Error al asignar el médico', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al asignar médico:', error);
            mostrarMensaje('Error de conexión', 'error');
        }
    });
}

function removerMedico(id_asignacion) {
    $.ajax({
        url: APP_URL + '/api/consultorios/remover-medico',
        type: 'POST',
        data: { id_asignacion: id_asignacion },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta remover médico:', response);
            if (response.resultado === 'removido') {
                mostrarAlerta('Médico removido del consultorio', 'success');
                cargarDetalleConsultorio();
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

// ==================== FUNCIONES DE UBICACIÓN ====================

function cargarEstados() {
    $.ajax({
        url: APP_URL + '/api/ubicacion/estados',
        type: 'POST',
        dataType: 'json',
        success: function(estados) {
            let options = '<option value="">Seleccione un estado...</option>';
            for (let estado of estados) {
                options += `<option value="${estado.id_estado}">${estado.estado}</option>`;
            }
            $('#estado').html(options);
        },
        error: function(xhr) {
            console.error('Error cargando estados:', xhr.responseText);
            cargarEstadosFallback();
        }
    });
}

function cargarEstadosFallback() {
    const estados = [
        {id_estado: 1, estado: 'Amazonas'}, {id_estado: 2, estado: 'Anzoátegui'},
        {id_estado: 3, estado: 'Apure'}, {id_estado: 4, estado: 'Aragua'},
        {id_estado: 5, estado: 'Barinas'}, {id_estado: 6, estado: 'Bolívar'},
        {id_estado: 7, estado: 'Carabobo'}, {id_estado: 8, estado: 'Cojedes'},
        {id_estado: 9, estado: 'Delta Amacuro'}, {id_estado: 10, estado: 'Falcón'},
        {id_estado: 11, estado: 'Guárico'}, {id_estado: 12, estado: 'Lara'},
        {id_estado: 13, estado: 'Mérida'}, {id_estado: 14, estado: 'Miranda'},
        {id_estado: 15, estado: 'Monagas'}, {id_estado: 16, estado: 'Nueva Esparta'},
        {id_estado: 17, estado: 'Portuguesa'}, {id_estado: 18, estado: 'Sucre'},
        {id_estado: 19, estado: 'Táchira'}, {id_estado: 20, estado: 'Trujillo'},
        {id_estado: 21, estado: 'La Guaira'}, {id_estado: 22, estado: 'Yaracuy'},
        {id_estado: 23, estado: 'Zulia'}, {id_estado: 24, estado: 'Distrito Capital'}
    ];
    let options = '<option value="">Seleccione un estado...</option>';
    for (let estado of estados) {
        options += `<option value="${estado.id_estado}">${estado.estado}</option>`;
    }
    $('#estado').html(options);
}

function cargarListaEspecialidades() {
    $.ajax({
        url: APP_URL + '/api/ubicacion/especialidades',
        type: 'POST',
        dataType: 'json',
        success: function(especialidades) {
            let html = '';
            for (let esp of especialidades) {
                html += `
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input especialidad-check" value="${esp}" id="esp_${esp.replace(/\s/g, '_')}">
                        <label class="form-check-label" for="esp_${esp.replace(/\s/g, '_')}">${esp}</label>
                    </div>
                `;
            }
            $('#especialidades_container').html(html);
        },
        error: function(xhr) {
            console.error('Error cargando especialidades:', xhr.responseText);
            $('#especialidades_container').html('<div class="text-danger">Error al cargar especialidades</div>');
        }
    });
}

function obtenerEspecialidadesSeleccionadas() {
    let especialidades = [];
    $('.especialidad-check:checked').each(function() {
        especialidades.push($(this).val());
    });
    return especialidades;
}

function actualizarPreview() {
    $('#preview_nombre').text($('#nombre').val() || 'Nombre del Consultorio');
    $('#preview_ciudad').text($('#ciudad').val() || 'Ciudad');
    $('#preview_descripcion').text($('#descripcion').val() || 'Descripción');
    $('#preview_telefono').text($('#telefono').val() || '-');
    $('#preview_email').text($('#email').val() || '-');
}

function crearConsultorio() {
    var especialidades = obtenerEspecialidadesSeleccionadas();
    
    var datos = {
        nombre: $('#nombre').val(),
        descripcion: $('#descripcion').val(),
        apertura: $('#apertura').val(),
        cierre: $('#cierre').val(),
        telefono: $('#telefono').val(),
        email: $('#email').val(),
        id_estado: $('#estado').val(),
        id_ciudad: $('#ciudad').val(),
        id_municipio: $('#municipio').val(),
        id_parroquia: $('#parroquia').val(),
        direccion: $('#direccion').val(),
        especialidades: especialidades,
        csrf_token: CSRF.getToken()
    };
    
    console.log('Datos a enviar:', datos); // Para depuración
    
    if (!datos.nombre || !datos.id_estado || !datos.id_ciudad || !datos.direccion) {
        $('#errorMensaje').text('Complete los campos requeridos (*)');
        $('#alertError').show();
        setTimeout(function() { $('#alertError').hide(); }, 3000);
        return;
    }
    
    $.ajax({
        url: APP_URL + '/api/consultorios/crear',
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta crear consultorio:', response);
            if (response.resultado === 'creado') {
                $('#alertExito').show();
                setTimeout(function() {
                    window.location.href = APP_URL + '/consultorios';
                }, 2000);
            } else {
                $('#errorMensaje').text('Error al crear el consultorio: ' + response.resultado);
                $('#alertError').show();
                setTimeout(function() { $('#alertError').hide(); }, 3000);
            }
        },
        error: function(xhr) {
            console.error('Error crear consultorio:', xhr.responseText);
            $('#errorMensaje').text('Error de conexión: ' + xhr.status);
            $('#alertError').show();
            setTimeout(function() { $('#alertError').hide(); }, 3000);
        }
    });
}

function cargarDatosConsultorio() {
    let id = $('#id_consultorio').val();
    
    $.ajax({
        url: APP_URL + '/api/consultorios/obtener-detalle',
        type: 'POST',
        data: { id_consultorio: id },
        dataType: 'json',
        success: function(data) {
            console.log('Datos consultorio para editar:', data);
            
            $('#nombre').val(data.nombre);
            $('#descripcion').val(data.descripcion || '');
            $('#apertura').val(data.apertura);
            $('#cierre').val(data.cierre);
            $('#telefono').val(data.telefono || '');
            $('#email').val(data.email || '');
            $('#direccion').val(data.direccion_detallada || '');
            
            actualizarPreview();
            
            // Marcar especialidades seleccionadas
            if (data.especialidades && data.especialidades.length > 0) {
                setTimeout(function() {
                    for (let esp of data.especialidades) {
                        let idCheck = `#esp_${esp.replace(/\s/g, '_')}`;
                        $(idCheck).prop('checked', true);
                    }
                }, 500);
            }
        },
        error: function(xhr) {
            console.error('Error cargando datos para editar:', xhr.responseText);
        }
    });
}

function editarConsultorio() {
    let especialidades = obtenerEspecialidadesSeleccionadas();
    let id = $('#id_consultorio').val();
    
    let datos = {
        id_consultorio: id,
        nombre: $('#nombre').val(),
        descripcion: $('#descripcion').val(),
        apertura: $('#apertura').val(),
        cierre: $('#cierre').val(),
        telefono: $('#telefono').val(),
        email: $('#email').val(),
        estado: $('#estado').val(),
        ciudad: $('#ciudad').val(),
        municipio: $('#municipio').val(),
        parroquia: $('#parroquia').val(),
        direccion: $('#direccion').val(),
        especialidades: especialidades,
        csrf_token: CSRF.getToken()
    };
    
    $.ajax({
        url: APP_URL + '/api/consultorios/editar',
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta editar consultorio:', response);
            if (response.resultado === 'editado') {
                $('#alertExito').show();
                setTimeout(function() {
                    window.location.href = APP_URL + '/consultorios/detalle?id=' + id;
                }, 2000);
            } else {
                $('#errorMensaje').text('Error al actualizar el consultorio');
                $('#alertError').show();
                setTimeout(function() { $('#alertError').hide(); }, 3000);
            }
        },
        error: function(xhr) {
            console.error('Error editar consultorio:', xhr.responseText);
            $('#errorMensaje').text('Error de conexión: ' + xhr.status);
            $('#alertError').show();
            setTimeout(function() { $('#alertError').hide(); }, 3000);
        }
    });
}

// ==================== FUNCIONES DE HORARIOS ====================

function cargarNombreConsultorio() {
    let id = $('#id_consultorio').val();
    
    $.ajax({
        url: APP_URL + '/api/consultorios/obtener-detalle',
        type: 'POST',
        data: { id_consultorio: id },
        dataType: 'json',
        success: function(data) {
            $('#consultorio_nombre').text(data.nombre);
        },
        error: function(xhr) {
            console.error('Error cargando nombre consultorio:', xhr.responseText);
        }
    });
}

function cargarHorarios() {
    let id = $('#id_consultorio').val();
    
    $.ajax({
        url: APP_URL + '/api/consultorios/obtener-horarios',
        type: 'POST',
        data: { id_consultorio: id },
        dataType: 'json',
        success: function(response) {
            console.log('Horarios recibidos:', response);
            
            let horarios = response.horarios;
            let medicos = response.medicos;
            
            let options = '<option value="">Sin asignar</option>';
            for (let med of medicos) {
                options += `<option value="${med.id}">${escapeHtml(med.nombre)}</option>`;
            }
            $('#medico_asignado').html(options);
            
            let dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            let html = '';
            
            for (let dia of dias) {
                html += `
                    <div class="col-md-4 col-sm-6">
                        <div class="horario-card">
                            <h4 class="text-center">${dia}</h4>
                `;
                
                let manana = horarios[dia]['Mañana'];
                html += `
                    <div class="horario-slot ${manana ? 'ocupado' : 'disponible'}">
                        <strong><i class="fas fa-sun"></i> Mañana</strong><br>
                `;
                if (manana) {
                    html += `
                        <small>${manana.hora_inicio} - ${manana.hora_fin}</small><br>
                        <small class="text-info"><i class="fas fa-user-md"></i> ${escapeHtml(manana.nombre_medico || 'Sin médico')}</small>
                    `;
                } else {
                    html += `<small class="text-muted">Disponible</small>`;
                }
                html += `
                        <div class="text-center mt-2">
                            <button class="btn btn-sm btn-primary btn-editar-horario" 
                                    data-dia="${dia}" data-turno="Mañana"
                                    data-hora-inicio="${manana ? manana.hora_inicio : '08:00'}"
                                    data-hora-fin="${manana ? manana.hora_fin : '12:00'}"
                                    data-medico-id="${manana && manana.id_medico ? manana.id_medico : ''}"
                                    data-medico-nombre="${manana ? escapeHtml(manana.nombre_medico || '') : ''}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>
                `;
                
                let tarde = horarios[dia]['Tarde'];
                html += `
                    <div class="horario-slot ${tarde ? 'ocupado' : 'disponible'} mt-2">
                        <strong><i class="fas fa-moon"></i> Tarde</strong><br>
                `;
                if (tarde) {
                    html += `
                        <small>${tarde.hora_inicio} - ${tarde.hora_fin}</small><br>
                        <small class="text-info"><i class="fas fa-user-md"></i> ${escapeHtml(tarde.nombre_medico || 'Sin médico')}</small>
                    `;
                } else {
                    html += `<small class="text-muted">Disponible</small>`;
                }
                html += `
                        <div class="text-center mt-2">
                            <button class="btn btn-sm btn-primary btn-editar-horario" 
                                    data-dia="${dia}" data-turno="Tarde"
                                    data-hora-inicio="${tarde ? tarde.hora_inicio : '13:00'}"
                                    data-hora-fin="${tarde ? tarde.hora_fin : '17:00'}"
                                    data-medico-id="${tarde && tarde.id_medico ? tarde.id_medico : ''}"
                                    data-medico-nombre="${tarde ? escapeHtml(tarde.nombre_medico || '') : ''}">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>
                `;
                
                html += `</div></div>`;
            }
            
            $('#contenedor_horarios').html(html);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar horarios:', error);
            $('#contenedor_horarios').html('<div class="col-12 text-center"><div class="alert alert-danger">Error al cargar horarios</div></div>');
        }
    });
}

function guardarHorario() {
    let id_consultorio = $('#id_consultorio').val();
    let dia = $('#horario_dia').val();
    let turno = $('#horario_turno').val();
    let hora_inicio = $('#hora_inicio').val();
    let hora_fin = $('#hora_fin').val();
    let id_medico = $('#medico_asignado').val();
    
    if (!hora_inicio || !hora_fin) {
        mostrarErrorHorario('Complete los horarios de inicio y fin');
        return;
    }
    
    if (hora_inicio >= hora_fin) {
        mostrarErrorHorario('La hora de fin debe ser mayor que la hora de inicio');
        return;
    }
    
    let btn = $('#btnGuardarHorario');
    let originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    
    $.ajax({
        url: APP_URL + '/api/consultorios/guardar-horario',
        type: 'POST',
        data: {
            id_consultorio: id_consultorio,
            dia: dia,
            turno: turno,
            hora_inicio: hora_inicio,
            hora_fin: hora_fin,
            id_medico: id_medico || ''
        },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta guardar horario:', response);
            if (response.resultado === 'guardado') {
                $('#modalHorario').modal('hide');
                mostrarExitoHorario('Horario guardado correctamente');
                cargarHorarios();
            } else if (response.resultado === 'error_horario') {
                mostrarErrorHorario(response.mensaje || 'La hora de fin debe ser mayor que la hora de inicio');
            } else if (response.resultado === 'error_duplicado') {
                mostrarErrorHorario(response.mensaje || 'El médico ya tiene un horario asignado en este día y turno');
            } else {
                mostrarErrorHorario('Error al guardar el horario');
            }
        },
        error: function(xhr) {
            console.error('Error al guardar horario:', xhr.responseText);
            mostrarErrorHorario('Error de conexión al guardar el horario');
        },
        complete: function() {
            btn.prop('disabled', false).html(originalText);
        }
    });
}

function mostrarErrorHorario(mensaje) {
    $('#errorMensaje').text(mensaje);
    $('#alertError').show();
    setTimeout(function() { $('#alertError').fadeOut(); }, 4000);
}

function mostrarExitoHorario(mensaje) {
    $('#alertExito').show();
    setTimeout(function() { $('#alertExito').fadeOut(); }, 3000);
}

// ==================== UTILIDADES ====================

>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
// ==================== FUNCIÓN PARA ESCAPAR HTML ====================
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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

<<<<<<< HEAD
<<<<<<< HEAD
// ==================== FUNCIONES ADICIONALES (placeholder) ====================
// Estas funciones se llaman desde el código pero necesitan estar definidas
=======
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
=======
function mostrarMensaje(mensaje, tipo) {
    $('#mensaje_asignacion').removeClass('alert-success alert-danger')
        .addClass(tipo === 'success' ? 'alert-success' : 'alert-danger')
        .text(mensaje).show();
    setTimeout(function() { $('#mensaje_asignacion').fadeOut(); }, 2000);
}
// ==================== FUNCIONES DE UBICACIÓN PARA CONSULTORIO ====================
// Estas funciones faltan en consultorio.js pero son llamadas desde el formulario
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852

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

<<<<<<< HEAD
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
=======
function cargarParroquias(id_municipio) {
    if (!id_municipio) {
        $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        return;
    }
    
    $('#parroquia').html('<option value="">Cargando parroquias...</option>').prop('disabled', true);
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/parroquias',
        type: 'POST',
        data: { id_municipio: id_municipio },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            var parroquias = Array.isArray(response) ? response : (response.data || response.parroquias || []);
            if (!Array.isArray(parroquias) || parroquias.length === 0) {
                $('#parroquia').html('<option value="">No hay parroquias disponibles</option>').prop('disabled', false);
                return;
            }
            var options = '<option value="">Seleccione una parroquia...</option>';
            for (var i = 0; i < parroquias.length; i++) {
                var parroquia = parroquias[i];
                var id = parroquia.id_parroquia || parroquia.id || '';
                var nombre = parroquia.parroquia || parroquia.nombre || '';
                options += '<option value="' + id + '">' + nombre + '</option>';
            }
            $('#parroquia').html(options).prop('disabled', false);
        },
        error: function(xhr) {
            console.error('Error cargando parroquias:', xhr.responseText);
            $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
        }
    });
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
}