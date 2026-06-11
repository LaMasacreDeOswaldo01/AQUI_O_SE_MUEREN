if (typeof APP_URL === 'undefined') {
    console.error('ERROR: APP_URL no está definida');
    var APP_URL = '';
}

$(document).ready(function() {
    console.log('=== CONSULTORIO JS - MIGRADO A API ===');
    console.log('APP_URL:', APP_URL);    
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
        timeout: 10000,
        success: function(response) {
            console.log('Estadísticas recibidas:', response);
            
            // Manejar el formato de respuesta ApiResponse
            var data = response;
            if (response.success && response.data) {
                data = response.data;
            }
            
            $('#total_consultorios').text(data.total_consultorios || 0);
            $('#total_activos').text(data.activos || 0);
            
            // Actualizar médicos asignados si existe el elemento
            if ($('#total_medicos_asignados').length) {
                $('#total_medicos_asignados').text(data.total_medicos_asignados || 0);
            }
            if ($('#citas_hoy').length) {
                $('#citas_hoy').text(data.citas_hoy || 0);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar estadísticas:', error);
            $('#total_consultorios').text('0');
            $('#total_activos').text('0');
            if ($('#total_medicos_asignados').length) $('#total_medicos_asignados').text('0');
            if ($('#citas_hoy').length) $('#citas_hoy').text('0');
        }
    });
}

function cargarConsultorios(busqueda = '') {
    console.log('Cargando consultorios con búsqueda:', busqueda);
    
    $('#contenedor_consultorios').html('<div class="col-12 text-center"><div class="spinner-border text-primary"></div><p class="mt-2">Cargando consultorios...</p></div>');
    
    $.ajax({
        url: APP_URL + '/api/consultorios/listar',
        type: 'POST',
        data: { busqueda: busqueda },
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            console.log('Respuesta consultorios:', response);
            
            // Manejar el formato de respuesta ApiResponse
            var consultorios = [];
            if (response.success && response.data) {
                consultorios = response.data;
            } else if (Array.isArray(response)) {
                consultorios = response;
            } else if (response.consultorios && Array.isArray(response.consultorios)) {
                consultorios = response.consultorios;
            }
            
            // Asegurar que sea un array
            if (!Array.isArray(consultorios)) {
                consultorios = [];
            }
            
            console.log('Consultorios procesados:', consultorios.length);
            
            let html = '';
            
            if (consultorios.length === 0) {
                html = '<div class="col-12 text-center"><div class="alert alert-info">No se encontraron consultorios</div></div>';
            } else {
                for (let i = 0; i < consultorios.length; i++) {
                    let c = consultorios[i];
                    let direccionMostrar = c.direccion_detallada || 'Sin dirección registrada';
                    if (direccionMostrar.length > 60) {
                        direccionMostrar = direccionMostrar.substring(0, 60) + '...';
                    }
                    
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
                                        ${escapeHtml(direccionMostrar)}
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
                                        <a href="${APP_URL}/consultorios/detalle?id=${c.id_consultorio}" class="btn btn-info btn-sm btn-accion">
                                            <i class="fas fa-eye"></i> Ver detalle
                                        </a>
                                        <a href="${APP_URL}/consultorios/horarios?id=${c.id_consultorio}" class="btn btn-warning btn-sm btn-accion">
                                            <i class="fas fa-clock"></i> Horarios
                                        </a>
                                        <button class="btn btn-danger btn-sm btn-accion btn-eliminar" data-id="${c.id_consultorio}" data-nombre="${escapeHtml(c.nombre)}">
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
            
            // Recalcular total de médicos asignados desde los datos
            let totalMedicos = consultorios.reduce((sum, c) => sum + (c.total_medicos || 0), 0);
            if ($('#total_medicos_asignados').length) {
                $('#total_medicos_asignados').text(totalMedicos);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar consultorios:', error);
            $('#contenedor_consultorios').html('<div class="col-12 text-center"><div class="alert alert-danger">Error al cargar consultorios</div></div>');
        }
    });
}

function eliminarConsultorio(id) {
    console.log('Eliminando consultorio ID:', id);
    
    $.ajax({
        url: APP_URL + '/api/consultorios/eliminar',
        type: 'POST',
        data: { 
            id_consultorio: id,
            csrf_token: $('input[name="csrf_token"]').val()
        },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Respuesta eliminar:', response);
            
            // Manejar el formato de respuesta ApiResponse
            var resultado = response;
            if (response.success && response.data) {
                resultado = response.data;
            }
            
            if (response.success === true || response.resultado === 'eliminado' || resultado.resultado === 'eliminado') {
                $('#modalEliminar').modal('hide');
                cargarConsultorios();
                cargarEstadisticas();
                mostrarAlerta(response.message || 'Consultorio eliminado correctamente', 'success');
            } else {
                mostrarAlerta(response.message || 'Error al eliminar el consultorio', 'error');
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
    console.log('Cargando detalle consultorio ID:', id);
    
    if (!id || id === '0') {
        $('#detalle_nombre').text('ID de consultorio no válido');
        return;
    }
    
    $.ajax({
        url: APP_URL + '/api/consultorios/obtener-detalle',
        type: 'POST',
        data: { id_consultorio: id },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Detalle consultorio:', response);
            
            // Manejar el formato de respuesta ApiResponse
            var data = response;
            if (response.success && response.data) {
                data = response.data;
            }
            
            if (data.error) {
                $('#detalle_nombre').text('Error: ' + data.error);
                return;
            }
            
            // Actualizar información básica
            $('#consultorio_nombre').text(data.nombre);
            $('#detalle_nombre').text(data.nombre);
            $('#detalle_ciudad').text(data.ciudad || 'No especificada');
            $('#detalle_horario').text(data.apertura + ' - ' + data.cierre);
            $('#detalle_telefono').text(data.telefono || '-');
            $('#detalle_email').text(data.email || '-');
            $('#detalle_direccion').text(data.direccion_detallada || '-');
            $('#detalle_descripcion').html(data.descripcion || '<p class="text-muted">Sin descripción</p>');
            
            // Horario resumen
            if ($('#horario_apertura').length) $('#horario_apertura').text(data.apertura || '08:00');
            if ($('#horario_cierre').length) $('#horario_cierre').text(data.cierre || '17:00');
            
            // ==================== UBICACIÓN COMPLETA ====================
            let ubicacion = '';
            if (data.estado) ubicacion += data.estado;
            if (data.ciudad) ubicacion += (ubicacion ? ', ' : '') + data.ciudad;
            if (data.municipio) ubicacion += (ubicacion ? ', ' : '') + data.municipio;
            if (data.parroquia) ubicacion += (ubicacion ? ', ' : '') + data.parroquia;
            
            if ($('#ubicacion_completa').length) {
                if (data.direccion_detallada) {
                    $('#ubicacion_completa').html(ubicacion + '<br><strong>Dirección:</strong> ' + data.direccion_detallada);
                } else {
                    $('#ubicacion_completa').text(ubicacion || 'No especificada');
                }
            }
            // ==================== FIN UBICACIÓN ====================            
            // Estadísticas
            if ($('#total_medicos').length) $('#total_medicos').text(data.medicos ? data.medicos.length : 0);
            if ($('#total_especialidades').length) $('#total_especialidades').text(data.especialidades ? data.especialidades.length : 0);
            if ($('#total_citas').length) $('#total_citas').text(data.total_citas || 0);
            if ($('#citas_mes').length) $('#citas_mes').text(data.citas_mes || 0);            
            // Especialidades
            mostrarEspecialidades(data.especialidades || []);            
            // Médicos
            mostrarMedicos(data.medicos || []);            
            // Cargar lista de médicos disponibles para asignación
            if ($('#medico_seleccionado').length) {
                cargarListaMedicosDisponibles();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar detalle:', error);
            $('#detalle_nombre').text('Error al cargar datos');
            mostrarAlerta('Error al cargar los detalles del consultorio', 'error');
        }
    });
}

function mostrarEspecialidades(especialidades) {
    let html = '';
    
    if (!especialidades || especialidades.length === 0) {
        html = '<p class="text-muted text-center">No hay especialidades registradas</p>';
    } else {
        for (let i = 0; i < especialidades.length; i++) {
            html += `<span class="especialidad-badge">${escapeHtml(especialidades[i])}</span>`;
        }
    }
    
    $('#contenedor_especialidades').html(html);
}

function mostrarMedicos(medicos) {
    let html = '';
    
    if (!medicos || medicos.length === 0) {
        html = `
            <div class="text-center py-4">
                <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                <p class="text-muted">No hay médicos asignados a este consultorio</p>
                <button class="btn btn-primary btn-sm btn-asignar-medico" data-toggle="modal" data-target="#modalAsignarMedico">
                    <i class="fas fa-plus"></i> Asignar Médico
                </button>
            </div>
        `;
    } else {
        for (let i = 0; i < medicos.length; i++) {
            let med = medicos[i];
            html += `
                <div class="medico-item p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><i class="fas fa-user-md text-info"></i> ${escapeHtml(med.nombre)}</strong><br>
                            <small><i class="fas fa-id-card"></i> Cédula: ${med.cedula || 'N/A'}</small><br>
                            <small><i class="fas fa-phone"></i> Teléfono: ${med.telefono || 'N/A'}</small>
                        </div>
                        <button class="btn btn-danger btn-sm btn-remover-medico" 
                                data-id="${med.id}" 
                                data-nombre="${escapeHtml(med.nombre)}">
                            <i class="fas fa-user-minus"></i> Remover
                        </button>
                    </div>
                </div>
            `;
        }
    }
    
    $('#contenedor_medicos').html(html);
    
    // Rebind event for remove button
    $('.btn-remover-medico').off('click').on('click', function() {
        let idAsignacion = $(this).data('id');
        let nombreMedico = $(this).data('nombre');
        if (confirm(`¿Está seguro de remover a ${nombreMedico} de este consultorio?`)) {
            removerMedico(idAsignacion);
        }
    });
}
// ==================== FUNCIONES DE UBICACIÓN ====================
function cargarEstados() {
    console.log('Cargando estados...');
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/estados',
        type: 'POST',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Estados cargados:', response);
            
            var estados = [];
            if (response.success && response.data) {
                estados = response.data;
            } else if (Array.isArray(response)) {
                estados = response;
            } else if (response.estados) {
                estados = response.estados;
            } else {
                estados = response;
            }
            
            if (!Array.isArray(estados)) {
                estados = [];
            }
            
            let options = '<option value="">Seleccione un estado...</option>';
            for (let i = 0; i < estados.length; i++) {
                let estado = estados[i];
                let id = estado.id_estado || estado.id;
                let nombre = estado.estado || estado.nombre;
                options += `<option value="${id}">${nombre}</option>`;
            }
            $('#estado').html(options).prop('disabled', false);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar estados:', error);
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
    for (let i = 0; i < estados.length; i++) {
        options += `<option value="${estados[i].id_estado}">${estados[i].estado}</option>`;
    }
    $('#estado').html(options).prop('disabled', false);
}

function cargarCiudades(id_estado) {
    if (!id_estado) {
        $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
        return;
    }
    
    $('#ciudad').html('<option value="">Cargando ciudades...</option>').prop('disabled', false);
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/ciudades',
        type: 'POST',
        data: { id_estado: id_estado },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            var ciudades = [];
            if (response.success && response.data) {
                ciudades = response.data;
            } else if (Array.isArray(response)) {
                ciudades = response;
            } else if (response.ciudades) {
                ciudades = response.ciudades;
            } else {
                ciudades = response;
            }
            
            if (!Array.isArray(ciudades)) {
                ciudades = [];
            }
            
            let options = '<option value="">Seleccione una ciudad...</option>';
            for (let i = 0; i < ciudades.length; i++) {
                let ciudad = ciudades[i];
                let id = ciudad.id_ciudad || ciudad.id;
                let nombre = ciudad.ciudad || ciudad.nombre;
                options += `<option value="${id}">${nombre}</option>`;
            }
            $('#ciudad').html(options).prop('disabled', false);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar ciudades:', error);
            $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
        }
    });
}

function cargarMunicipios(id_estado) {
    if (!id_estado) {
        $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
        $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        return;
    }
    
    $('#municipio').html('<option value="">Cargando municipios...</option>').prop('disabled', false);
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/municipios',
        type: 'POST',
        data: { id_estado: id_estado },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            var municipios = [];
            if (response.success && response.data) {
                municipios = response.data;
            } else if (Array.isArray(response)) {
                municipios = response;
            } else if (response.municipios) {
                municipios = response.municipios;
            } else {
                municipios = response;
            }
            
            if (!Array.isArray(municipios)) {
                municipios = [];
            }
            
            let options = '<option value="">Seleccione un municipio...</option>';
            for (let i = 0; i < municipios.length; i++) {
                let municipio = municipios[i];
                let id = municipio.id_municipio || municipio.id;
                let nombre = municipio.municipio || municipio.nombre;
                options += `<option value="${id}">${nombre}</option>`;
            }
            $('#municipio').html(options).prop('disabled', false);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar municipios:', error);
            $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
        }
    });
}

function cargarParroquias(id_municipio) {
    if (!id_municipio) {
        $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        return;
    }
    
    $('#parroquia').html('<option value="">Cargando parroquias...</option>').prop('disabled', false);
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/parroquias',
        type: 'POST',
        data: { id_municipio: id_municipio },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            var parroquias = [];
            if (response.success && response.data) {
                parroquias = response.data;
            } else if (Array.isArray(response)) {
                parroquias = response;
            } else if (response.parroquias) {
                parroquias = response.parroquias;
            } else {
                parroquias = response;
            }
            
            if (!Array.isArray(parroquias)) {
                parroquias = [];
            }
            
            let options = '<option value="">Seleccione una parroquia...</option>';
            for (let i = 0; i < parroquias.length; i++) {
                let parroquia = parroquias[i];
                let id = parroquia.id_parroquia || parroquia.id;
                let nombre = parroquia.parroquia || parroquia.nombre;
                options += `<option value="${id}">${nombre}</option>`;
            }
            $('#parroquia').html(options).prop('disabled', false);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar parroquias:', error);
            $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
        }
    });
}
// ==================== FUNCIONES DE ESPECIALIDADES ====================
function cargarListaEspecialidades() {
    $('#especialidades_container').html(`
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <span class="ml-2">Cargando especialidades...</span>
        </div>
    `);
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/especialidades',
        type: 'POST',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Especialidades cargadas:', response);
            
            var especialidades = [];
            if (response.success && response.data) {
                especialidades = response.data;
            } else if (Array.isArray(response)) {
                especialidades = response;
            } else if (response.especialidades) {
                especialidades = response.especialidades;
            } else {
                especialidades = response;
            }
            
            if (!Array.isArray(especialidades)) {
                especialidades = [];
            }
            
            let html = '';
            for (let i = 0; i < especialidades.length; i++) {
                let esp = especialidades[i];
                let nombre = esp.especialidad || esp.nombre || esp;
                let id = esp.id_especialidad || esp.id || i;
                html += `
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="esp_${id}" value="${escapeHtml(nombre)}">
                        <label class="form-check-label" for="esp_${id}">${escapeHtml(nombre)}</label>
                    </div>
                `;
            }
            $('#especialidades_container').html(html || '<p class="text-muted text-center">No hay especialidades disponibles</p>');
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar especialidades:', error);
            $('#especialidades_container').html('<p class="text-danger text-center">Error al cargar especialidades</p>');
        }
    });
}
// ==================== FUNCIONES PARA MÉDICOS ====================
function cargarListaMedicosDisponibles() {
    let id_consultorio = $('#id_consultorio').val();
    
    console.log('=== CARGANDO MÉDICOS DISPONIBLES ===');
    console.log('ID Consultorio:', id_consultorio);
    console.log('URL:', APP_URL + '/api/consultorios/listar-medicos');
    
    $('#medico_seleccionado').html('<option value="">Cargando médicos...</option>');
    
    $.ajax({
        url: APP_URL + '/api/consultorios/listar-medicos',
        type: 'POST',
        data: { id_consultorio: id_consultorio },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            
            // Extraer médicos de la respuesta (formato ApiResponse)
            let medicos = [];
            if (response.success && Array.isArray(response.data)) {
                medicos = response.data;
            } else if (Array.isArray(response)) {
                medicos = response;
            }
            
            console.log('Médicos encontrados:', medicos.length);
            
            let options = '<option value="">Seleccione un médico...</option>';
            
            if (medicos.length === 0) {
                options = '<option value="" disabled>⚠️ No hay médicos disponibles</option>';
                $('#medico_seleccionado').prop('disabled', true);
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
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar médicos:', error);
            $('#medico_seleccionado').html('<option value="">❌ Error al cargar médicos</option>');
            mostrarMensajeAsignacion('Error al cargar la lista de médicos', 'danger');
        }
    });
}
function mostrarMensajeAsignacion(mensaje, tipo) {
    let alertClass = tipo === 'success' ? 'alert-success' : (tipo === 'warning' ? 'alert-warning' : 'alert-danger');
    let iconClass = tipo === 'success' ? 'fa-check-circle' : (tipo === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle');
    
    let $mensajeDiv = $('#mensaje_asignacion');
    if ($mensajeDiv.length === 0) {
        // Crear el div si no existe
        $('.modal-body').prepend('<div id="mensaje_asignacion" class="alert" style="display:none;"></div>');
        $mensajeDiv = $('#mensaje_asignacion');
    }
    
    $mensajeDiv
        .removeClass('alert-success alert-warning alert-danger')
        .addClass(alertClass)
        .html('<i class="fas ' + iconClass + '"></i> ' + mensaje)
        .fadeIn(300);
    
    setTimeout(function() {
        $mensajeDiv.fadeOut(500);
    }, 3000);
}

function asignarMedico() {
    let id_consultorio = $('#id_consultorio').val();
    let id_medico = $('#medico_seleccionado').val();
    
    console.log('=== ASIGNAR MÉDICO A CONSULTORIO ===');
    console.log('ID Consultorio:', id_consultorio);
    console.log('ID Médico:', id_medico);
    console.log('URL:', APP_URL + '/api/consultorios/asignar-medico');
    
    if (!id_medico) {
        mostrarMensajeAsignacion('Debe seleccionar un médico', 'danger');
        return;
    }
    
    let $btn = $('#btnAsignarMedico');
    let originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Asignando...');
    
    // Obtener el token CSRF
    let csrfToken = $('input[name="csrf_token"]').val();
    if (!csrfToken) {
        csrfToken = $('meta[name="csrf-token"]').attr('content');
    }
    
    $.ajax({
        url: APP_URL + '/api/consultorios/asignar-medico',
        type: 'POST',
        data: {
            id_consultorio: id_consultorio,
            id_medico: id_medico,
            csrf_token: csrfToken
        },
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            console.log('Respuesta completa:', response);
            
            // Manejar diferentes formatos de respuesta
            let resultado = response.resultado;
            let mensaje = response.message;
            
            // Si la respuesta tiene formato ApiResponse
            if (response.success && response.data) {
                resultado = response.data.resultado;
                mensaje = response.data.message;
            }
            
            if (resultado === 'asignado') {
                mostrarMensajeAsignacion('✅ Médico asignado correctamente', 'success');
                setTimeout(function() {
                    $('#modalAsignarMedico').modal('hide');
                    cargarDetalleConsultorio(); // Recargar la lista de médicos
                }, 1500);
            } else if (resultado === 'ya_asignado') {
                mostrarMensajeAsignacion('⚠️ El médico ya está asignado a este consultorio', 'warning');
                $btn.prop('disabled', false).html(originalText);
            } else {
                let errorMsg = mensaje || response.error || 'Error al asignar el médico';
                mostrarMensajeAsignacion('❌ ' + errorMsg, 'danger');
                $btn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en AJAX:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
            
            let errorMsg = 'Error de conexión: ' + status;
            try {
                let responseJson = JSON.parse(xhr.responseText);
                if (responseJson.message) {
                    errorMsg = responseJson.message;
                }
            } catch(e) {}
            
            mostrarMensajeAsignacion('❌ ' + errorMsg, 'danger');
            $btn.prop('disabled', false).html(originalText);
        }
    });
}

function removerMedico(id_asignacion) {
    console.log('Removiendo médico con asignación ID:', id_asignacion);
    
    $.ajax({
        url: APP_URL + '/api/consultorios/remover-medico',
        type: 'POST',
        data: { 
            id_asignacion: id_asignacion,
            csrf_token: $('input[name="csrf_token"]').val()
        },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Respuesta remover médico:', response);
            
            if (response.resultado === 'removido') {
                mostrarAlerta('Médico removido del consultorio', 'success');
                cargarDetalleConsultorio();
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

// ==================== FUNCIONES DE HORARIOS ====================

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
        error: function(xhr, status, error) {
            console.error('Error al cargar nombre del consultorio:', error);
            $('#consultorio_nombre').text('Consultorio #' + id);
        }
    });
}

function cargarHorarios() {
    let id = $('#id_consultorio').val();
    
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
            
            let listaMedicos = data.medicos || [];
            let horarios = data.horarios || {};
            
            renderizarHorarios(horarios, listaMedicos);
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

function renderizarHorarios(horarios, listaMedicos) {
    const dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    const turnos = ['Mañana', 'Tarde'];
    
    let html = '';
    
    for (let i = 0; i < dias.length; i++) {
        let dia = dias[i];
        
        html += `
            <div class="col-md-6 col-lg-4">
                <div class="horario-card">
                    <h4>
                        ${escapeHtml(dia)}
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
                let medicoInfo = tieneMedico ? '<i class="fas fa-user-md"></i> ' + escapeHtml(medicoNombre) : '<i class="fas fa-user-slash"></i> Sin médico asignado';
                
                html += `
                    <div class="horario-slot ${slotClass}">
                        <div class="slot-header">
                            <span class="slot-horario">
                                <i class="fas fa-clock"></i> ${escapeHtml(horario.hora_inicio)} - ${escapeHtml(horario.hora_fin)}
                            </span>
                            <button class="btn btn-primary btn-horario btn-editar-horario" 
                                    data-dia="${escapeHtml(dia)}" 
                                    data-turno="${escapeHtml(turno)}"
                                    data-hora-inicio="${escapeHtml(horario.hora_inicio)}"
                                    data-hora-fin="${escapeHtml(horario.hora_fin)}"
                                    data-medico-id="${horario.id_medico || ''}"
                                    data-medico-nombre="${escapeHtml(medicoNombre)}">
                                <i class="fas fa-edit"></i>
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
                                data-dia="${escapeHtml(dia)}" 
                                data-turno="${escapeHtml(turno)}"
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
    
    // Guardar lista de médicos para usar en el modal
    window.listaMedicosHorarios = listaMedicos;
}

function cargarListaMedicosHorarios() {
    let options = '<option value="">Sin asignar (Consultorio cerrado)</option>';
    
    if (window.listaMedicosHorarios && window.listaMedicosHorarios.length > 0) {
        for (let i = 0; i < window.listaMedicosHorarios.length; i++) {
            let medico = window.listaMedicosHorarios[i];
            options += `<option value="${escapeHtml(medico.id)}">${escapeHtml(medico.nombre)} (${escapeHtml(medico.cedula)})</option>`;
        }
    } else {
        options += '<option value="" disabled>No hay médicos disponibles</option>';
    }
    
    $('#medico_asignado').html(options);
}

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
        $('#hora_inicio').focus();
        return;
    }
    
    if (!hora_fin) {
        mostrarErrorModal('Debe ingresar la hora de fin');
        $('#hora_fin').focus();
        return;
    }
    
    if (hora_inicio >= hora_fin) {
        mostrarErrorModal('La hora de fin debe ser mayor que la hora de inicio');
        $('#hora_fin').focus();
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
            console.log('Respuesta guardar horario:', response);
            
            if (response.resultado === 'guardado') {
                $('#modalHorario').modal('hide');
                mostrarAlerta('Horario guardado correctamente', 'success');
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
            console.error('Error al guardar horario:', error);
            let errorMsg = 'Error de conexión: ' + status;
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            mostrarErrorModal(errorMsg);
            $btn.prop('disabled', false).html(originalText);
        }
    });
}

// ==================== FUNCIONES DE VISTA PREVIA ====================

function actualizarPreview() {
    $('#preview_nombre').text($('#nombre').val() || 'Nombre del Consultorio');
    
    var ciudadNombre = $('#ciudad option:selected').text();
    if (ciudadNombre && ciudadNombre !== 'Seleccione una ciudad...' && ciudadNombre !== 'Primero seleccione un estado...') {
        $('#preview_ciudad').text(ciudadNombre);
    } else {
        $('#preview_ciudad').text('Ciudad no seleccionada');
    }
    
    var descripcion = $('#descripcion').val();
    if (descripcion) {
        $('#preview_descripcion').html(descripcion.substring(0, 80) + (descripcion.length > 80 ? '...' : ''));
    } else {
        $('#preview_descripcion').html('<em class="text-muted">Sin descripción</em>');
    }
    
    $('#preview_telefono').text($('#telefono').val() || '-');
    $('#preview_email').text($('#email').val() || '-');
    $('#preview_horario').text($('#apertura').val() + ' - ' + $('#cierre').val());
}

// ==================== FUNCIONES DE FORMULARIOS ====================

function crearConsultorio() {
    let nombre = $('#nombre').val().trim();
    let descripcion = $('#descripcion').val().trim();
    let apertura = $('#apertura').val();
    let cierre = $('#cierre').val();
    let telefono = $('#telefono').val().trim();
    let email = $('#email').val().trim();
    let id_estado = $('#estado').val();
    let id_ciudad = $('#ciudad').val();
    let id_municipio = $('#municipio').val() || 0;
    let id_parroquia = $('#parroquia').val() || 0;
    let direccion = $('#direccion').val().trim();
    
    // Recopilar especialidades seleccionadas
    let especialidades = [];
    $('.checkbox-group input:checked').each(function() {
        especialidades.push($(this).val());
    });
    
    if (!nombre) {
        mostrarAlerta('El nombre del consultorio es requerido', 'error');
        $('#nombre').focus();
        return;
    }
    if (!id_estado) {
        mostrarAlerta('Debe seleccionar un estado', 'error');
        $('#estado').focus();
        return;
    }
    if (!id_ciudad) {
        mostrarAlerta('Debe seleccionar una ciudad', 'error');
        $('#ciudad').focus();
        return;
    }
    if (!direccion) {
        mostrarAlerta('La dirección detallada es requerida', 'error');
        $('#direccion').focus();
        return;
    }
    
    let datos = {
        nombre: nombre,
        descripcion: descripcion,
        apertura: apertura,
        cierre: cierre,
        telefono: telefono,
        email: email,
        id_estado: id_estado,
        id_ciudad: id_ciudad,
        id_municipio: id_municipio,
        id_parroquia: id_parroquia,
        direccion: direccion,
        especialidades: especialidades,
        csrf_token: $('input[name="csrf_token"]').val()
    };
    
    let $btn = $('#btnGuardar');
    let originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    
    $.ajax({
        url: APP_URL + '/api/consultorios/crear',
        type: 'POST',
        data: datos,
        dataType: 'json',
        timeout: 20000,
        success: function(response) {
            console.log('Respuesta crear consultorio:', response);
            
            if (response.resultado === 'creado') {
                mostrarAlerta('Consultorio creado exitosamente', 'success');
                setTimeout(function() {
                    window.location.href = APP_URL + '/consultorios';
                }, 2000);
            } else if (response.resultado === 'error_csrf') {
                mostrarAlerta('Error de seguridad. Por favor, recargue la página.', 'error');
                $btn.prop('disabled', false).html(originalText);
            } else {
                let errorMsg = response.error || response.message || 'Error al crear el consultorio';
                mostrarAlerta(errorMsg, 'error');
                $btn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al crear consultorio:', error);
            let errorMsg = 'Error de conexión: ' + status;
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            mostrarAlerta(errorMsg, 'error');
            $btn.prop('disabled', false).html(originalText);
        }
    });
}

function cargarDatosConsultorio() {
    let id = $('#id_consultorio').val();
    
    if (!id || id === '0') {
        mostrarAlerta('ID de consultorio no válido', 'error');
        return;
    }
    
    $('#loadingDatos').show();
    
    $.ajax({
        url: APP_URL + '/api/consultorios/obtener-detalle',
        type: 'POST',
        data: { id_consultorio: id },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Datos del consultorio:', response);
            
            var data = response;
            if (response.success && response.data) {
                data = response.data;
            }
            
            if (data.error) {
                mostrarAlerta('Error al cargar datos: ' + data.error, 'error');
                $('#loadingDatos').hide();
                return;
            }
            
            // Llenar formulario
            $('#nombre').val(data.nombre);
            $('#descripcion').val(data.descripcion || '');
            $('#apertura').val(data.apertura || '08:00');
            $('#cierre').val(data.cierre || '17:00');
            $('#telefono').val(data.telefono || '');
            $('#email').val(data.email || '');
            $('#direccion').val(data.direccion_detallada || '');
            
            // Cargar ubicación con selección
            if (data.estado) {
                cargarEstadosConSeleccion(data);
            } else {
                cargarEstados();
            }
            
            // Cargar especialidades con selección
            cargarListaEspecialidadesConSeleccion(data.especialidades || []);
            
            // Actualizar vista previa
            actualizarPreview();
            
            $('#loadingDatos').hide();
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar datos:', error);
            $('#loadingDatos').hide();
            mostrarAlerta('Error al cargar los datos del consultorio', 'error');
        }
    });
}

function cargarEstadosConSeleccion(data) {
    $.ajax({
        url: APP_URL + '/api/ubicacion/estados',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            var estados = [];
            if (response.success && response.data) {
                estados = response.data;
            } else if (Array.isArray(response)) {
                estados = response;
            } else if (response.estados) {
                estados = response.estados;
            } else {
                estados = response;
            }
            
            if (!Array.isArray(estados)) {
                estados = [];
            }
            
            let options = '<option value="">Seleccione un estado...</option>';
            let estadoId = null;
            let estadoSeleccionado = data.estado || '';
            
            for (let i = 0; i < estados.length; i++) {
                let estado = estados[i];
                let id = estado.id_estado || estado.id;
                let nombre = estado.estado || estado.nombre;
                options += `<option value="${id}">${nombre}</option>`;
                if (nombre === estadoSeleccionado) {
                    estadoId = id;
                }
            }
            $('#estado').html(options).prop('disabled', false);
            
            if (estadoId) {
                $('#estado').val(estadoId);
                cargarCiudadesConSeleccion(estadoId, data.ciudad || '');
                cargarMunicipiosConSeleccion(estadoId, data.municipio || '');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar estados:', error);
            cargarEstadosFallback();
        }
    });
}

function cargarCiudadesConSeleccion(id_estado, ciudadSeleccionada) {
    if (!id_estado) return;
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/ciudades',
        type: 'POST',
        data: { id_estado: id_estado },
        dataType: 'json',
        success: function(response) {
            var ciudades = [];
            if (response.success && response.data) {
                ciudades = response.data;
            } else if (Array.isArray(response)) {
                ciudades = response;
            } else if (response.ciudades) {
                ciudades = response.ciudades;
            } else {
                ciudades = response;
            }
            
            if (!Array.isArray(ciudades)) {
                ciudades = [];
            }
            
            let options = '<option value="">Seleccione una ciudad...</option>';
            let ciudadId = null;
            
            for (let i = 0; i < ciudades.length; i++) {
                let ciudad = ciudades[i];
                let id = ciudad.id_ciudad || ciudad.id;
                let nombre = ciudad.ciudad || ciudad.nombre;
                options += `<option value="${id}">${nombre}</option>`;
                if (nombre === ciudadSeleccionada) {
                    ciudadId = id;
                }
            }
            $('#ciudad').html(options).prop('disabled', false);
            
            if (ciudadId) {
                $('#ciudad').val(ciudadId);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar ciudades:', error);
            $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
        }
    });
}

function cargarMunicipiosConSeleccion(id_estado, municipioSeleccionado) {
    if (!id_estado) return;
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/municipios',
        type: 'POST',
        data: { id_estado: id_estado },
        dataType: 'json',
        success: function(response) {
            var municipios = [];
            if (response.success && response.data) {
                municipios = response.data;
            } else if (Array.isArray(response)) {
                municipios = response;
            } else if (response.municipios) {
                municipios = response.municipios;
            } else {
                municipios = response;
            }
            
            if (!Array.isArray(municipios)) {
                municipios = [];
            }
            
            let options = '<option value="">Seleccione un municipio...</option>';
            let municipioId = null;
            
            for (let i = 0; i < municipios.length; i++) {
                let municipio = municipios[i];
                let id = municipio.id_municipio || municipio.id;
                let nombre = municipio.municipio || municipio.nombre;
                options += `<option value="${id}">${nombre}</option>`;
                if (nombre === municipioSeleccionado) {
                    municipioId = id;
                }
            }
            $('#municipio').html(options).prop('disabled', false);
            
            if (municipioId) {
                $('#municipio').val(municipioId);
                if (municipioSeleccionado) {
                    cargarParroquiasConSeleccion(municipioId, data.parroquia || '');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar municipios:', error);
            $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
        }
    });
}

function cargarParroquiasConSeleccion(id_municipio, parroquiaSeleccionada) {
    if (!id_municipio) return;
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/parroquias',
        type: 'POST',
        data: { id_municipio: id_municipio },
        dataType: 'json',
        success: function(response) {
            var parroquias = [];
            if (response.success && response.data) {
                parroquias = response.data;
            } else if (Array.isArray(response)) {
                parroquias = response;
            } else if (response.parroquias) {
                parroquias = response.parroquias;
            } else {
                parroquias = response;
            }
            
            if (!Array.isArray(parroquias)) {
                parroquias = [];
            }
            
            let options = '<option value="">Seleccione una parroquia...</option>';
            let parroquiaId = null;
            
            for (let i = 0; i < parroquias.length; i++) {
                let parroquia = parroquias[i];
                let id = parroquia.id_parroquia || parroquia.id;
                let nombre = parroquia.parroquia || parroquia.nombre;
                options += `<option value="${id}">${nombre}</option>`;
                if (nombre === parroquiaSeleccionada) {
                    parroquiaId = id;
                }
            }
            $('#parroquia').html(options).prop('disabled', false);
            
            if (parroquiaId) {
                $('#parroquia').val(parroquiaId);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar parroquias:', error);
            $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
        }
    });
}

function cargarListaEspecialidadesConSeleccion(especialidadesSeleccionadas) {
    $('#especialidades_container').html(`
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary"></div>
            <span class="ml-2">Cargando especialidades...</span>
        </div>
    `);
    
    $.ajax({
        url: APP_URL + '/api/ubicacion/especialidades',
        type: 'POST',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            var especialidades = [];
            if (response.success && response.data) {
                especialidades = response.data;
            } else if (Array.isArray(response)) {
                especialidades = response;
            } else if (response.especialidades) {
                especialidades = response.especialidades;
            } else {
                especialidades = response;
            }
            
            if (!Array.isArray(especialidades)) {
                especialidades = [];
            }
            
            let html = '';
            for (let i = 0; i < especialidades.length; i++) {
                let esp = especialidades[i];
                let nombre = esp.especialidad || esp.nombre || esp;
                let id = esp.id_especialidad || esp.id || i;
                let checked = especialidadesSeleccionadas && especialidadesSeleccionadas.includes(nombre) ? 'checked' : '';
                html += `
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="esp_${id}" value="${escapeHtml(nombre)}" ${checked}>
                        <label class="form-check-label" for="esp_${id}">${escapeHtml(nombre)}</label>
                    </div>
                `;
            }
            $('#especialidades_container').html(html || '<p class="text-muted text-center">No hay especialidades disponibles</p>');
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar especialidades:', error);
            $('#especialidades_container').html('<p class="text-danger text-center">Error al cargar especialidades</p>');
        }
    });
}

function editarConsultorio() {
    let id = $('#id_consultorio').val();
    let nombre = $('#nombre').val().trim();
    let descripcion = $('#descripcion').val().trim();
    let apertura = $('#apertura').val();
    let cierre = $('#cierre').val();
    let telefono = $('#telefono').val().trim();
    let email = $('#email').val().trim();
    let direccion = $('#direccion').val().trim();
    
    // Recopilar especialidades seleccionadas
    let especialidades = [];
    $('.checkbox-group input:checked').each(function() {
        especialidades.push($(this).val());
    });
    
    if (!nombre) {
        mostrarAlerta('El nombre del consultorio es requerido', 'error');
        $('#nombre').focus();
        return;
    }
    
    let datos = {
        id_consultorio: id,
        nombre: nombre,
        descripcion: descripcion,
        apertura: apertura,
        cierre: cierre,
        telefono: telefono,
        email: email,
        estado: $('#estado option:selected').text(),
        ciudad: $('#ciudad option:selected').text(),
        municipio: $('#municipio option:selected').text() || '',
        parroquia: $('#parroquia option:selected').text() || '',
        direccion: direccion,
        especialidades: especialidades,
        csrf_token: $('input[name="csrf_token"]').val()
    };
    
    let $btn = $('#btnGuardar');
    let originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
    
    $.ajax({
        url: APP_URL + '/api/consultorios/editar',
        type: 'POST',
        data: datos,
        dataType: 'json',
        timeout: 20000,
        success: function(response) {
            console.log('Respuesta editar consultorio:', response);
            
            if (response.resultado === 'editado') {
                mostrarAlerta('Consultorio actualizado exitosamente', 'success');
                setTimeout(function() {
                    window.location.href = APP_URL + '/consultorios/detalle?id=' + id;
                }, 2000);
            } else if (response.resultado === 'error_csrf') {
                mostrarAlerta('Error de seguridad. Por favor, recargue la página.', 'error');
                $btn.prop('disabled', false).html(originalText);
            } else {
                let errorMsg = response.error || response.message || 'Error al actualizar el consultorio';
                mostrarAlerta(errorMsg, 'error');
                $btn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al editar consultorio:', error);
            let errorMsg = 'Error de conexión: ' + status;
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            mostrarAlerta(errorMsg, 'error');
            $btn.prop('disabled', false).html(originalText);
        }
    });
}

// ==================== FUNCIONES UTILITARIAS ====================

function mostrarAlerta(mensaje, tipo) {
    var alertDiv = $('<div>', {
        class: 'alert alert-' + (tipo === 'success' ? 'success' : tipo === 'error' ? 'danger' : 'warning') + ' alert-dismissible fade show position-fixed',
        style: 'top: 70px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 12px;',
        role: 'alert'
    });
    
    var icon = tipo === 'success' ? 'fa-check-circle' : (tipo === 'error' ? 'fa-exclamation-circle' : 'fa-exclamation-triangle');
    
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

function mostrarErrorModal(mensaje) {
    if ($('#modalHorario').hasClass('show')) {
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
        mostrarAlerta(mensaje, 'error');
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/\\/g, '&#92;')
        .replace(/`/g, '&#96;')
        .replace(/\$/g, '&#36;');
}  