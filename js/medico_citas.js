// js/medico_citas.js
$(document).ready(function() {
    console.log('=== MIS CITAS - MÉDICO ===');
    
    var id_medico = $('#id_medico').val();
    var citasData = [];
    var paginaActual = 1;
    var registrosPorPagina = 10;
    
    // Filtros
    var filtros = {
        estado: 'todos',
        fecha: '',
        paciente: '',
        tipo_consulta: 'todos'
    };
    
    var pacienteSeleccionado = null;
    var timeoutBusqueda;
    
    // ==================== INICIALIZAR ====================
    
    function cargarCitas() {
        $('#loadingCitas').show();
        
        var data = {
            id_medico: id_medico,
            estado: filtros.estado,
            fecha: filtros.fecha,
            paciente: filtros.paciente,
            tipo_consulta: filtros.tipo_consulta
        };
        
        $.ajax({
            url: APP_URL + '/api/medicos/mis-citas',
            type: 'POST',
            data: data,
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Citas recibidas:', response);
                
                if (response.success) {
                    citasData = response.data.citas || [];
                    var estadisticas = response.data.estadisticas || {};
                    
                    // Actualizar estadísticas
                    $('#total-citas').text(estadisticas.total || 0);
                    $('#pendientes-count').text(estadisticas.pendientes || 0);
                    $('#confirmadas-count').text(estadisticas.confirmadas || 0);
                    $('#completadas-count').text(estadisticas.citas_mes || 0);
                    $('#canceladas-count').text(estadisticas.canceladas || 0);
                    
                    aplicarFiltrosYRenderizar();
                } else {
                    mostrarAlerta(response.message || 'Error al cargar citas', 'error');
                }
                $('#loadingCitas').hide();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar citas:', error);
                $('#contenedor_citas').html(`
                    <div class="text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar las citas: ${error}
                        </div>
                    </div>
                `);
                $('#loadingCitas').hide();
            }
        });
    }
    
    function aplicarFiltrosYRenderizar() {
        let filtrados = [...citasData];
        
        // Aplicar filtros adicionales si es necesario
        if (filtros.estado !== 'todos') {
            filtrados = filtrados.filter(cita => cita.estado === filtros.estado);
        }
        
        if (filtros.fecha) {
            filtrados = filtrados.filter(cita => cita.fecha_raw === filtros.fecha);
        }
        
        if (filtros.paciente && pacienteSeleccionado) {
            filtrados = filtrados.filter(cita => cita.paciente_id == pacienteSeleccionado);
        }
        
        if (filtros.tipo_consulta !== 'todos') {
            filtrados = filtrados.filter(cita => 
                (filtros.tipo_consulta === 'primera_vez' && cita.tipo_consulta === 'Primera Vez') ||
                (filtros.tipo_consulta === 'control' && cita.tipo_consulta === 'Control')
            );
        }
        
        // Paginación
        var total = filtrados.length;
        var desde = (paginaActual - 1) * registrosPorPagina + 1;
        var hasta = Math.min(paginaActual * registrosPorPagina, total);
        
        $('#total_registros').text(total);
        $('#desde').text(total > 0 ? desde : 0);
        $('#hasta').text(hasta);
        
        var inicio = (paginaActual - 1) * registrosPorPagina;
        var fin = inicio + registrosPorPagina;
        var citasPagina = filtrados.slice(inicio, fin);
        
        renderizarCitas(citasPagina);
        renderizarPaginacion(total);
    }
    
    function renderizarCitas(citas) {
        var html = '';
        
        if (citas.length === 0) {
            html = `
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p>No se encontraron citas</p>
                    <p class="text-muted small">Intenta ajustar los filtros de búsqueda</p>
                </div>
            `;
        } else {
            for (var i = 0; i < citas.length; i++) {
                var c = citas[i];
                var estadoClass = getEstadoClass(c.estado);
                var estadoIcono = getEstadoIcono(c.estado);
                
                // Inicial del paciente
                var inicial = (c.paciente_nombre || 'P').charAt(0).toUpperCase();
                
                html += `
                    <div class="cita-card" data-id="${c.id_cita}">
                        <div class="cita-header">
                            <div class="cita-fecha">
                                <span class="dia">${c.fecha}</span>
                                <span class="hora"><i class="fas fa-clock"></i> ${c.hora} hs</span>
                            </div>
                            <div>
                                <span class="badge-estado-cita ${estadoClass}">
                                    <i class="fas ${estadoIcono}"></i> ${c.estado_label}
                                </span>
                            </div>
                        </div>
                        <div class="cita-body">
                            <div class="cita-paciente">
                                <div class="paciente-avatar">${inicial}</div>
                                <div class="paciente-info">
                                    <h4>${escapeHtml(c.paciente_nombre)}</h4>
                                    <p><i class="fas fa-id-card"></i> ${c.paciente_cedula || 'N/A'} | <i class="fas fa-phone"></i> ${c.paciente_telefono || 'N/A'}</p>
                                </div>
                            </div>
                            <div class="cita-detalle">
                                <div class="detalle-item">
                                    <i class="fas fa-stethoscope"></i>
                                    <span><strong>Especialidad:</strong> ${escapeHtml(c.especialidad)}</span>
                                </div>
                                <div class="detalle-item">
                                    <i class="fas fa-building"></i>
                                    <span><strong>Consultorio:</strong> ${escapeHtml(c.consultorio_nombre)}</span>
                                </div>
                                <div class="detalle-item">
                                    <i class="fas fa-notes-medical"></i>
                                    <span><strong>Tipo:</strong> ${c.tipo_consulta}</span>
                                </div>
                            </div>
                        </div>
                        <div class="cita-footer">
                            <div>
                                ${c.es_tercero ? `<small class="text-muted"><i class="fas fa-users"></i> Cita para: ${escapeHtml(c.nombre_tercero)} (${c.parentesco})</small>` : ''}
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm btn-ver-detalle" data-id="${c.id_cita}">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </button>
                                <div class="dropdown d-inline-block ml-2">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                        <i class="fas fa-edit"></i> Estado
                                    </button>
                                    <div class="dropdown-menu dropdown-estado">
                                        <a class="dropdown-item cambiar-estado" data-estado="pendiente" data-id="${c.id_cita}">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </a>
                                        <a class="dropdown-item cambiar-estado" data-estado="confirmada" data-id="${c.id_cita}">
                                            <i class="fas fa-check-circle"></i> Confirmada
                                        </a>
                                        <a class="dropdown-item cambiar-estado" data-estado="en_progreso" data-id="${c.id_cita}">
                                            <i class="fas fa-spinner"></i> En Progreso
                                        </a>
                                        <a class="dropdown-item cambiar-estado" data-estado="completada" data-id="${c.id_cita}">
                                            <i class="fas fa-check-double"></i> Completada
                                        </a>
                                        <a class="dropdown-item cambiar-estado" data-estado="cancelada" data-id="${c.id_cita}">
                                            <i class="fas fa-times-circle"></i> Cancelada
                                        </a>
                                        <a class="dropdown-item cambiar-estado" data-estado="no_asistio" data-id="${c.id_cita}">
                                            <i class="fas fa-user-slash"></i> No Asistió
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
        
        $('#contenedor_citas').html(html);
    }
    
    function renderizarPaginacion(total) {
        var totalPaginas = Math.ceil(total / registrosPorPagina);
        var html = '';
        
        if (totalPaginas <= 1) {
            html = '';
        } else {
            html += `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-pagina="${paginaActual - 1}">«</a>
                    </li>`;
            
            var inicioPagina = Math.max(1, paginaActual - 2);
            var finPagina = Math.min(totalPaginas, paginaActual + 2);
            
            if (inicioPagina > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" data-pagina="1">1</a></li>`;
                if (inicioPagina > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            
            for (var i = inicioPagina; i <= finPagina; i++) {
                html += `<li class="page-item ${paginaActual === i ? 'active' : ''}">
                            <a class="page-link" href="#" data-pagina="${i}">${i}</a>
                        </li>`;
            }
            
            if (finPagina < totalPaginas) {
                if (finPagina < totalPaginas - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                html += `<li class="page-item"><a class="page-link" href="#" data-pagina="${totalPaginas}">${totalPaginas}</a></li>`;
            }
            
            html += `<li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-pagina="${paginaActual + 1}">»</a>
                    </li>`;
        }
        
        $('#paginacion').html(html);
    }
    
    // ==================== FUNCIONES AUXILIARES ====================
    
    function getEstadoClass(estado) {
        var classes = {
            'pendiente': 'badge-estado-pendiente',
            'confirmada': 'badge-estado-confirmada',
            'en_progreso': 'badge-estado-en_progreso',
            'completada': 'badge-estado-completada',
            'cancelada': 'badge-estado-cancelada',
            'no_asistio': 'badge-estado-no_asistio'
        };
        return classes[estado] || 'badge-estado-pendiente';
    }
    
    function getEstadoIcono(estado) {
        var iconos = {
            'pendiente': 'fa-clock',
            'confirmada': 'fa-check-circle',
            'en_progreso': 'fa-spinner',
            'completada': 'fa-check-double',
            'cancelada': 'fa-times-circle',
            'no_asistio': 'fa-user-slash'
        };
        return iconos[estado] || 'fa-clock';
    }
    
    // ==================== CAMBIAR ESTADO ====================
    
    $(document).on('click', '.cambiar-estado', function(e) {
        e.preventDefault();
        var id_cita = $(this).data('id');
        var nuevo_estado = $(this).data('estado');
        
        if (confirm('¿Está seguro de cambiar el estado de esta cita?')) {
            cambiarEstadoCita(id_cita, nuevo_estado);
        }
    });
    
    function cambiarEstadoCita(id_cita, nuevo_estado) {
        $.ajax({
            url: APP_URL + '/api/medicos/cambiar-estado-cita',
            type: 'POST',
            data: {
                id_cita: id_cita,
                estado: nuevo_estado,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarAlerta(response.message, 'success');
                    cargarCitas();
                } else {
                    mostrarAlerta(response.message || 'Error al cambiar el estado', 'error');
                }
            },
            error: function(xhr, status, error) {
                mostrarAlerta('Error de conexión: ' + status, 'error');
            }
        });
    }
    
    // ==================== VER DETALLE ====================
    
    $(document).on('click', '.btn-ver-detalle', function() {
        var id_cita = $(this).data('id');
        var cita = citasData.find(c => c.id_cita == id_cita);
        
        if (cita) {
            var estadoClass = getEstadoClass(cita.estado);
            var html = `
                <div class="p-3">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-calendar-alt"></i> Fecha:</strong> ${cita.fecha}</p>
                            <p><strong><i class="fas fa-clock"></i> Hora:</strong> ${cita.hora} hs</p>
                            <p><strong><i class="fas fa-stethoscope"></i> Especialidad:</strong> ${escapeHtml(cita.especialidad)}</p>
                            <p><strong><i class="fas fa-building"></i> Consultorio:</strong> ${escapeHtml(cita.consultorio_nombre)}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-user-injured"></i> Paciente:</strong> ${escapeHtml(cita.paciente_nombre)}</p>
                            <p><strong><i class="fas fa-id-card"></i> Cédula:</strong> ${cita.paciente_cedula || 'N/A'}</p>
                            <p><strong><i class="fas fa-phone"></i> Teléfono:</strong> ${cita.paciente_telefono || 'N/A'}</p>
                            <p><strong><i class="fas fa-envelope"></i> Correo:</strong> ${cita.paciente_correo || 'N/A'}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <strong><i class="fas fa-notes-medical"></i> Motivo de Consulta</strong>
                                </div>
                                <div class="card-body">
                                    ${escapeHtml(cita.motivo) || '<em class="text-muted">No especificado</em>'}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert ${estadoClass} text-center">
                                <i class="fas ${getEstadoIcono(cita.estado)}"></i>
                                <strong>Estado actual:</strong> ${cita.estado_label}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#detalle_cita_content').html(html);
            $('#dropdown-estado-modal .dropdown-item').off('click').on('click', function() {
                var nuevo_estado = $(this).data('estado');
                $('#modalDetalleCita').modal('hide');
                cambiarEstadoCita(id_cita, nuevo_estado);
            });
            $('#modalDetalleCita').modal('show');
        }
    });
    
    // ==================== BUSCAR PACIENTES ====================
    
    $('#buscar_paciente').on('input', function() {
        var termino = $(this).val().trim();
        
        clearTimeout(timeoutBusqueda);
        
        if (termino.length >= 2) {
            timeoutBusqueda = setTimeout(function() {
                buscarPacientes(termino);
            }, 500);
        } else {
            $('#resultados_pacientes').hide();
            pacienteSeleccionado = null;
            filtros.paciente = '';
            cargarCitas();
        }
    });
    
    function buscarPacientes(termino) {
        $('#resultados_pacientes').html('<div class="list-group-item text-center">Buscando...</div>').show();
        
        $.ajax({
            url: APP_URL + '/api/medicos/buscar-pacientes-citas',
            type: 'POST',
            data: { termino: termino },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    var html = '';
                    for (var i = 0; i < response.data.length; i++) {
                        var p = response.data[i];
                        html += `
                            <a href="#" class="list-group-item list-group-item-action paciente-item" 
                               data-id="${p.id}" 
                               data-nombre="${escapeHtml(p.nombre_completo)}"
                               data-cedula="${p.cedula}">
                                <strong>${escapeHtml(p.nombre_completo)}</strong><br>
                                <small><i class="fas fa-id-card"></i> Cédula: ${p.cedula || 'N/A'}</small>
                            </a>
                        `;
                    }
                    $('#resultados_pacientes').html(html);
                } else {
                    $('#resultados_pacientes').html('<div class="list-group-item text-center">No se encontraron pacientes</div>');
                }
            },
            error: function() {
                $('#resultados_pacientes').html('<div class="list-group-item text-center text-danger">Error al buscar</div>');
            }
        });
    }
    
    $(document).on('click', '.paciente-item', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        
        pacienteSeleccionado = id;
        filtros.paciente = nombre;
        $('#buscar_paciente').val(nombre);
        $('#resultados_pacientes').hide();
        paginaActual = 1;
        cargarCitas();
    });
    
    // ==================== FILTROS ====================
    
    $('#filtro_estado').change(function() {
        filtros.estado = $(this).val();
        paginaActual = 1;
        cargarCitas();
    });
    
    $('#filtro_tipo_consulta').change(function() {
        filtros.tipo_consulta = $(this).val();
        paginaActual = 1;
        cargarCitas();
    });
    
    $('#filtro_fecha').change(function() {
        filtros.fecha = $(this).val();
        paginaActual = 1;
        cargarCitas();
    });
    
    $('.stat-card-citas').click(function() {
        var estado = $(this).data('estado');
        $('#filtro_estado').val(estado);
        filtros.estado = estado;
        paginaActual = 1;
        cargarCitas();
        
        // Resaltar tarjeta activa
        $('.stat-card-citas').removeClass('active');
        $(this).addClass('active');
    });
    
    $('#btnRefresh').click(function() {
        cargarCitas();
    });
    
    $('#btnLimpiarFiltros').click(function() {
        filtros = {
            estado: 'todos',
            fecha: '',
            paciente: '',
            tipo_consulta: 'todos'
        };
        pacienteSeleccionado = null;
        paginaActual = 1;
        $('#filtro_estado').val('todos');
        $('#filtro_tipo_consulta').val('todos');
        $('#filtro_fecha').val('');
        $('#buscar_paciente').val('');
        $('.stat-card-citas').removeClass('active');
        cargarCitas();
    });
    
    // Paginación
    $(document).on('click', '#paginacion .page-link', function(e) {
        e.preventDefault();
        var nuevaPagina = $(this).data('pagina');
        if (nuevaPagina && !$(this).parent().hasClass('disabled')) {
            paginaActual = nuevaPagina;
            aplicarFiltrosYRenderizar();
        }
    });
    
    // Ocultar resultados al hacer clic fuera
    $(document).click(function(e) {
        if (!$(e.target).closest('#buscar_paciente, #resultados_pacientes').length) {
            $('#resultados_pacientes').hide();
        }
    });
    
    // ==================== FUNCIONES UTILITARIAS ====================
    
    function mostrarAlerta(mensaje, tipo) {
        var alertDiv = $('<div>', {
            class: 'alert alert-' + (tipo === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show position-fixed',
            style: 'top: 70px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 12px;',
            role: 'alert'
        });
        
        var icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
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
    cargarCitas();
});
