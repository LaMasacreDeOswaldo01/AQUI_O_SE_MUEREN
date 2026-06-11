if (typeof APP_URL === 'undefined') {
    console.error('ERROR: APP_URL no está definida');
    var APP_URL = '';
}

$(document).ready(function() {
    console.log('=== RECETAS JS - MIGRADO A API ===');
    console.log('APP_URL:', APP_URL);    
    // ==================== VARIABLES GLOBALES ====================
    let recetasData = [];
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let filtroBusqueda = '';
    let filtroTipo = 'todas';
    let filtroFecha = '';
    let filtroOrden = 'fecha_desc';    
    // ==================== INICIALIZAR ====================
    cargarEstadisticas();
    cargarRecetas();    
    // ==================== EVENTOS PRINCIPALES ====================
    
    // Botón nueva receta
    $('#btnNuevaReceta').click(function() {
        resetFormulario();
        $('#modalTitle').text('Nueva Receta');
        $('#modalReceta').modal('show');
    });
    
    // Buscar pacientes con debounce
    let timeoutId;
    $('#buscar_paciente').on('input', function() {
        let dato = $(this).val().trim();
        
        clearTimeout(timeoutId);
        
        if (dato.length >= 2) {
            timeoutId = setTimeout(function() {
                buscarPacientes(dato);
            }, 500);
        } else {
            $('#resultados_pacientes').hide();
            $('#resultados_pacientes').html('');
        }
    });
    
    // Ocultar resultados al hacer clic fuera
    $(document).click(function(e) {
        if (!$(e.target).closest('#buscar_paciente, #resultados_pacientes').length) {
            $('#resultados_pacientes').hide();
        }
    });
    
    // Buscar en la tabla
    $('#buscar_receta').on('keyup', function() {
        filtroBusqueda = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    // Filtro por tipo
    $('#filtro_tipo').change(function() {
        filtroTipo = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    // Filtro por fecha
    $('#filtro_fecha').change(function() {
        filtroFecha = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    // Filtro por orden (si existe)
    if ($('#filtro_orden').length) {
        $('#filtro_orden').change(function() {
            filtroOrden = $(this).val();
            paginaActual = 1;
            aplicarFiltros();
        });
    }
    
    // Botón refrescar
    $('#btnRefresh').click(function() {
        filtroBusqueda = '';
        filtroTipo = 'todas';
        filtroFecha = '';
        filtroOrden = 'fecha_desc';
        paginaActual = 1;
        $('#buscar_receta').val('');
        $('#filtro_tipo').val('todas');
        $('#filtro_fecha').val('');
        if ($('#filtro_orden').length) $('#filtro_orden').val('fecha_desc');
        cargarRecetas();
        cargarEstadisticas();
        mostrarAlerta('Datos actualizados', 'success');
    });
    
    // Botón exportar
    $('#btnExportar').click(function() {
        exportarDatos();
    });
    
    // Guardar receta
    $('#btnGuardar').click(function() {
        guardarReceta();
    });    
    // ==================== EVENTOS DELEGADOS ====================    
    // Editar receta (delegado)
    $(document).on('click', '.btn-editar', function() {
        let id = $(this).data('id');
        console.log('Click editar - ID:', id);
        editarReceta(id);
    });
    
    // Borrar receta (delegado)
    $(document).on('click', '.btn-borrar', function() {
        let id = $(this).data('id');
        console.log('Click borrar - ID:', id);
        if (confirm('¿Está seguro de que desea eliminar esta receta?')) {
            borrarReceta(id);
        }
    });
    
    // Ver detalle receta (delegado)
    $(document).on('click', '.btn-ver-detalle', function() {
        let id = $(this).data('id');
        verDetalleReceta(id);
    });
    
    // Seleccionar paciente de la lista
    $(document).on('click', '.paciente-item', function(e) {
        e.preventDefault();
        
        let id_paciente = $(this).data('id');
        let nombre_completo = $(this).data('nombre');
        let cedula = $(this).data('cedula');
        
        console.log('Paciente seleccionado:', { id_paciente, nombre_completo, cedula });
        
        $('#buscar_paciente').val(nombre_completo);
        $('#id_paciente').val(id_paciente);
        $('#resultados_pacientes').hide();
        $('#resultados_pacientes').html('');
        
        mostrarAlerta('Paciente seleccionado: ' + nombre_completo, 'success');
    });
    
    // Paginación (delegado)
    $(document).on('click', '#paginacion .page-link', function(e) {
        e.preventDefault();
        let nuevaPagina = $(this).data('pagina');
        if (nuevaPagina && !$(this).parent().hasClass('disabled')) {
            paginaActual = nuevaPagina;
            aplicarFiltros();
        }
    });    
    // ==================== FUNCIONES DE CARGA DE DATOS ====================    
    function cargarEstadisticas() {
        console.log('Cargando estadísticas desde:', APP_URL + '/api/recetas/estadisticas');
        
        $.ajax({
            url: APP_URL + '/api/recetas/estadisticas',
            type: 'POST',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Estadísticas recibidas:', response);
                
                // Manejar formato ApiResponse
                var data = {};
                if (response.success && response.data) {
                    data = response.data;
                } else {
                    data = response;
                }
                
                $('#total_recetas').text(data.total_recetas || 0);
                $('#total_medicos').text(data.total_medicos || 0);
                $('#total_pacientes').text(data.total_pacientes || 0);
                $('#recetas_mes').text(data.recetas_mes || 0);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estadísticas:', error);
                $('#total_recetas').text('0');
                $('#total_medicos').text('0');
                $('#total_pacientes').text('0');
                $('#recetas_mes').text('0');
            }
        });
    }
    
    function cargarRecetas() {
        $('#tabla_recetas').html(`
            <tr><td colspan="9" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-2">Cargando recetas...</p>
             </td
            </tr>
        `);
        
        $.ajax({
            url: APP_URL + '/api/recetas/listar',
            type: 'POST',
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta recetas:', response);
                
                // Manejar formato ApiResponse
                var recetas = [];
                if (response.success && response.data) {
                    recetas = response.data;
                } else if (Array.isArray(response)) {
                    recetas = response;
                } else if (response.recetas && Array.isArray(response.recetas)) {
                    recetas = response.recetas;
                }
                
                if (!Array.isArray(recetas)) {
                    recetas = [];
                }
                
                recetasData = recetas;
                aplicarFiltros();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar recetas:', error);
                $('#tabla_recetas').html(`
                    <tr><td colspan="9" class="text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar las recetas: ${error}
                        </div>
                     </td
                    </tr>
                `);
            }
        });
    }    
    // ==================== FUNCIONES DE FILTRADO Y PAGINACIÓN ====================    
    function aplicarFiltros() {
        let filtrados = [...recetasData];        
        // Filtro por búsqueda
        if (filtroBusqueda) {
            let busquedaLower = filtroBusqueda.toLowerCase();
            filtrados = filtrados.filter(receta => {
                return (receta.nombre_medicamento && receta.nombre_medicamento.toLowerCase().includes(busquedaLower)) ||
                       (receta.marca && receta.marca.toLowerCase().includes(busquedaLower)) ||
                       (receta.paciente && receta.paciente.toLowerCase().includes(busquedaLower)) ||
                       (receta.medico && receta.medico.toLowerCase().includes(busquedaLower));
            });
        }
        
        // Filtro por tipo
        if (filtroTipo !== 'todas') {
            if (filtroTipo === 'medicamento') {
                filtrados = filtrados.filter(receta => 
                    receta.nombre_medicamento && !receta.nombre_medicamento.includes('ESTUDIOS') && !receta.nombre_medicamento.includes('DIAGNÓSTICO')
                );
            } else if (filtroTipo === 'estudio') {
                filtrados = filtrados.filter(receta => 
                    receta.nombre_medicamento && receta.nombre_medicamento.includes('ESTUDIOS')
                );
            } else if (filtroTipo === 'diagnostico') {
                filtrados = filtrados.filter(receta => 
                    receta.nombre_medicamento && receta.nombre_medicamento.includes('DIAGNÓSTICO')
                );
            }
        }
        
        // Filtro por fecha
        if (filtroFecha) {
            filtrados = filtrados.filter(receta => receta.fecha_receta === filtroFecha);
        }
        
        // Ordenamiento
        if (filtroOrden === 'fecha_desc') {
            filtrados.sort((a, b) => new Date(b.fecha_receta || 0) - new Date(a.fecha_receta || 0));
        } else if (filtroOrden === 'fecha_asc') {
            filtrados.sort((a, b) => new Date(a.fecha_receta || 0) - new Date(b.fecha_receta || 0));
        } else if (filtroOrden === 'medicamento') {
            filtrados.sort((a, b) => (a.nombre_medicamento || '').localeCompare(b.nombre_medicamento || ''));
        }
        
        // Actualizar info de paginación
        let total = filtrados.length;
        let desde = (paginaActual - 1) * registrosPorPagina + 1;
        let hasta = Math.min(paginaActual * registrosPorPagina, total);
        
        $('#total_registros').text(total);
        $('#desde').text(total > 0 ? desde : 0);
        $('#hasta').text(hasta);
        
        // Paginar
        let inicio = (paginaActual - 1) * registrosPorPagina;
        let fin = inicio + registrosPorPagina;
        let recetasPagina = filtrados.slice(inicio, fin);
        
        renderizarTabla(recetasPagina);
        renderizarPaginacion(total);
    }
    
    function renderizarTabla(recetas) {
        let html = '';
        
        if (recetas.length === 0) {
            html = `
                <tr><td colspan="9" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-prescription-bottle-alt"></i>
                        <p>No se encontraron recetas</p>
                        <p class="text-muted small">Intente con otros criterios de búsqueda</p>
                    </div>
                 </td
                </tr>
            `;
        } else {
            for (let i = 0; i < recetas.length; i++) {
                let receta = recetas[i];
                let tipoBadge = '';
                
                if (receta.nombre_medicamento && receta.nombre_medicamento.includes('ESTUDIOS')) {
                    tipoBadge = '<span class="badge badge-info mr-1"><i class="fas fa-flask"></i> Estudio</span>';
                } else if (receta.nombre_medicamento && receta.nombre_medicamento.includes('DIAGNÓSTICO')) {
                    tipoBadge = '<span class="badge badge-primary mr-1"><i class="fas fa-stethoscope"></i> Diagnóstico</span>';
                } else {
                    tipoBadge = '<span class="badge badge-success mr-1"><i class="fas fa-capsules"></i> Medicamento</span>';
                }
                
                // Formatear fecha
                let fechaFormateada = receta.fecha_receta || '';
                if (receta.fecha_receta) {
                    let fecha = new Date(receta.fecha_receta);
                    fechaFormateada = fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                }
                
                html += `
                    <tr>
                        <td><span class="badge badge-secondary">${receta.id_receta || ''}</span></td>
                        <td><strong>${escapeHtml(receta.nombre_medicamento || '')}</strong> ${tipoBadge}</td
                        <td>${escapeHtml(receta.marca || '')}</td
                        <td>${escapeHtml(receta.cantidad || '')}</td
                        <td>${escapeHtml(receta.dosis || '-')}</td
                        <td><i class="fas fa-user-injured text-info"></i> ${escapeHtml(receta.paciente || 'N/A')}</td
                        <td><i class="fas fa-user-md text-success"></i> ${escapeHtml(receta.medico || 'N/A')}</td
                        <td><i class="fas fa-calendar-alt"></i> ${fechaFormateada}</td
                        <td class="table-actions">
                            <button class="btn btn-info btn-sm btn-ver-detalle" data-id="${receta.id_receta}" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${receta.editable !== false ? `
                            <button class="btn btn-warning btn-sm btn-editar" data-id="${receta.id_receta}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-borrar" data-id="${receta.id_receta}" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            ` : ''}
                         </td
                    </tr>
                `;
            }
        }
        
        $('#tabla_recetas').html(html);
    }
    
    function renderizarPaginacion(total) {
        let totalPaginas = Math.ceil(total / registrosPorPagina);
        let html = '';
        
        if (totalPaginas <= 1) {
            html = '';
        } else {
            // Botón anterior
            html += `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-pagina="${paginaActual - 1}">«</a>
                    </li>`;
            
            // Números de página
            let inicioPagina = Math.max(1, paginaActual - 2);
            let finPagina = Math.min(totalPaginas, paginaActual + 2);
            
            if (inicioPagina > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" data-pagina="1">1</a></li>`;
                if (inicioPagina > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            
            for (let i = inicioPagina; i <= finPagina; i++) {
                html += `<li class="page-item ${paginaActual === i ? 'active' : ''}">
                            <a class="page-link" href="#" data-pagina="${i}">${i}</a>
                        </li>`;
            }
            
            if (finPagina < totalPaginas) {
                if (finPagina < totalPaginas - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                html += `<li class="page-item"><a class="page-link" href="#" data-pagina="${totalPaginas}">${totalPaginas}</a></li>`;
            }
            
            // Botón siguiente
            html += `<li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-pagina="${paginaActual + 1}">»</a>
                    </li>`;
        }
        
        $('#paginacion').html(html);
    }    
    // ==================== FUNCIONES DE BÚSQUEDA DE PACIENTES ====================    
    function buscarPacientes(dato) {
        console.log('Buscando pacientes con dato:', dato);
        console.log('URL:', APP_URL + '/api/recetas/buscar-pacientes');
        
        // Mostrar indicador de carga
        $('#resultados_pacientes').html('<div class="list-group-item list-group-item-action text-center">Buscando...</div>').show();
        
        $.ajax({
            url: APP_URL + '/api/recetas/buscar-pacientes',
            type: 'POST',
            data: { dato: dato },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta búsqueda pacientes:', response);
                
                // Manejar formato ApiResponse
                var pacientes = [];
                if (response.success && response.data) {
                    pacientes = response.data;
                } else if (Array.isArray(response)) {
                    pacientes = response;
                }
                
                console.log('Pacientes encontrados:', pacientes.length);
                
                let html = '';
                
                if (!pacientes || pacientes.length === 0) {
                    html = '<a href="#" class="list-group-item list-group-item-action disabled">No se encontraron pacientes</a>';
                } else {
                    for (let i = 0; i < pacientes.length; i++) {
                        let paciente = pacientes[i];
                        let nombreCompleto = paciente.nombre_completo || paciente.nombre_us || '';
                        let cedula = paciente.cedula || paciente.cedula_us || '';
                        let id = paciente.id_usuario || paciente.id_paciente;
                        
                        html += `
                            <a href="#" class="list-group-item list-group-item-action paciente-item" 
                               data-id="${id}" 
                               data-nombre="${escapeHtml(nombreCompleto)}" 
                               data-cedula="${escapeHtml(cedula)}">
                                <strong>${escapeHtml(nombreCompleto)}</strong><br>
                                <small><i class="fas fa-id-card"></i> Cédula: ${escapeHtml(cedula)}</small>
                            </a>
                        `;
                    }
                }
                
                $('#resultados_pacientes').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error en búsqueda de pacientes:', error);
                $('#resultados_pacientes').html('<a href="#" class="list-group-item list-group-item-action disabled">Error al buscar pacientes</a>');
            }
        });
    }    
    function cargarDatosPaciente(id_paciente) {
        console.log('Cargando datos del paciente ID:', id_paciente);
        
        $.ajax({
            url: APP_URL + '/api/recetas/buscar-pacientes',
            type: 'POST',
            data: { dato: '' },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                var pacientes = [];
                if (response.success && response.data) {
                    pacientes = response.data;
                } else if (Array.isArray(response)) {
                    pacientes = response;
                }
                
                if (pacientes && Array.isArray(pacientes)) {
                    let paciente = pacientes.find(p => (p.id_usuario || p.id_paciente) == id_paciente);
                    if (paciente) {
                        let nombreCompleto = paciente.nombre_completo || paciente.nombre_us || '';
                        $('#buscar_paciente').val(nombreCompleto);
                        $('#id_paciente').val(paciente.id_usuario || paciente.id_paciente);
                        console.log('Paciente cargado:', nombreCompleto);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar datos del paciente:', error);
            }
        });
    }    
    // ==================== FUNCIONES CRUD DE RECETAS ====================    
    function resetFormulario() {
        $('#id_receta').val('');
        $('#nombre_medicamento').val('');
        $('#marca').val('');
        $('#cantidad').val('');
        $('#dosis').val('');
        $('#instrucciones').val('');
        $('#buscar_paciente').val('');
        $('#id_paciente').val('');
        $('#resultados_pacientes').hide();
        let hoy = new Date();
        let fecha = hoy.toISOString().split('T')[0];
        $('#fecha_receta').val(fecha);
    }
    
    function guardarReceta() {
        let id_receta = $('#id_receta').val();
        let nombre_medicamento = $('#nombre_medicamento').val().trim();
        let marca = $('#marca').val().trim();
        let cantidad = $('#cantidad').val().trim();
        let dosis = $('#dosis').val().trim();
        let instrucciones = $('#instrucciones').val().trim();
        let id_paciente = $('#id_paciente').val();
        let fecha_receta = $('#fecha_receta').val();
        
        // Validaciones
        if (!nombre_medicamento) {
            mostrarAlerta('Debe ingresar el nombre del medicamento', 'error');
            $('#nombre_medicamento').focus();
            return;
        }
        if (!marca) {
            mostrarAlerta('Debe ingresar la marca del medicamento', 'error');
            $('#marca').focus();
            return;
        }
        if (!cantidad) {
            mostrarAlerta('Debe ingresar la cantidad del medicamento', 'error');
            $('#cantidad').focus();
            return;
        }
        if (!id_paciente || id_paciente === '') {
            mostrarAlerta('Debe seleccionar un paciente', 'error');
            $('#buscar_paciente').focus();
            return;
        }
        if (!fecha_receta) {
            mostrarAlerta('Debe seleccionar la fecha de la receta', 'error');
            $('#fecha_receta').focus();
            return;
        }
        
        let url = id_receta ? APP_URL + '/api/recetas/editar' : APP_URL + '/api/recetas/crear';
        let datos = {
            nombre_medicamento: nombre_medicamento,
            marca: marca,
            cantidad: cantidad,
            dosis: dosis,
            instrucciones: instrucciones,
            id_paciente: id_paciente,
            fecha_receta: fecha_receta,
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        if (id_receta) {
            datos.id_receta = id_receta;
        }
        
        console.log('Guardando receta:', { url, datos });
        
        let $btn = $('#btnGuardar');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: datos,
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta guardar:', response);
                
                if (response.success) {
                    mostrarAlerta(response.message || 'Receta guardada exitosamente', 'success');
                    $('#modalReceta').modal('hide');
                    cargarRecetas();
                    cargarEstadisticas();
                    resetFormulario();
                } else {
                    let errorMsg = response.message || 'Error al guardar la receta';
                    if (response.data && response.data.errors) {
                        errorMsg = Object.values(response.data.errors).join('. ');
                    }
                    mostrarAlerta(errorMsg, 'error');
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr, status, error) {
                console.error('Error al guardar receta:', error);
                let errorMsg = 'Error de conexión: ' + status;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                mostrarAlerta(errorMsg, 'error');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }
    
    function editarReceta(id) {
        console.log('Editando receta ID:', id);
        
        if (!id) {
            mostrarAlerta('ID de receta no válido', 'error');
            return;
        }
        
        $.ajax({
            url: APP_URL + '/api/recetas/obtener',
            type: 'POST',
            data: { id_receta: id },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Receta obtenida:', response);
                
                var receta = response;
                if (response.success && response.data) {
                    receta = response.data;
                }
                
                if (receta && receta.id_receta) {
                    $('#id_receta').val(receta.id_receta);
                    $('#nombre_medicamento').val(receta.nombre_medicamento);
                    $('#marca').val(receta.marca);
                    $('#cantidad').val(receta.cantidad);
                    $('#dosis').val(receta.dosis || '');
                    $('#instrucciones').val(receta.instrucciones || '');
                    $('#fecha_receta').val(receta.fecha_receta);
                    
                    if (receta.id_paciente) {
                        cargarDatosPaciente(receta.id_paciente);
                    }
                    
                    $('#modalTitle').text('Editar Receta');
                    $('#modalReceta').modal('show');
                } else {
                    mostrarAlerta('Error al cargar los datos de la receta', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al editar receta:', error);
                mostrarAlerta('Error al cargar los datos de la receta', 'error');
            }
        });
    }
    
    function borrarReceta(id) {
        console.log('Borrando receta ID:', id);
        
        if (!id) {
            mostrarAlerta('ID de receta no válido', 'error');
            return;
        }
        
        $.ajax({
            url: APP_URL + '/api/recetas/borrar',
            type: 'POST',
            data: { 
                id_receta: id,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta borrar:', response);
                
                if (response.success) {
                    mostrarAlerta(response.message || 'Receta eliminada exitosamente', 'success');
                    cargarRecetas();
                    cargarEstadisticas();
                } else {
                    mostrarAlerta(response.message || 'Error al eliminar la receta', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al borrar receta:', error);
                mostrarAlerta('Error de conexión al borrar la receta', 'error');
            }
        });
    }
    
    function verDetalleReceta(id) {
        console.log('Ver detalle receta ID:', id);
        
        $('#detalle_receta_content').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Cargando detalles...</p>
            </div>
        `);
        $('#modalDetalleReceta').modal('show');
        
        $.ajax({
            url: APP_URL + '/api/recetas/obtener',
            type: 'POST',
            data: { id_receta: id },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Detalle receta:', response);
                
                var receta = response;
                if (response.success && response.data) {
                    receta = response.data;
                }
                
                if (receta && receta.id_receta) {
                    let tipo = '';
                    if (receta.nombre_medicamento && receta.nombre_medicamento.includes('ESTUDIOS')) {
                        tipo = '<span class="badge badge-info"><i class="fas fa-flask"></i> Estudio de Laboratorio</span>';
                    } else if (receta.nombre_medicamento && receta.nombre_medicamento.includes('DIAGNÓSTICO')) {
                        tipo = '<span class="badge badge-primary"><i class="fas fa-stethoscope"></i> Diagnóstico Médico</span>';
                    } else {
                        tipo = '<span class="badge badge-success"><i class="fas fa-capsules"></i> Medicamento</span>';
                    }
                    
                    let fechaFormateada = receta.fecha_receta || '';
                    if (receta.fecha_receta) {
                        let fecha = new Date(receta.fecha_receta);
                        fechaFormateada = fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' });
                    }
                    
                    let html = `
                        <div class="receta-detalle p-3">
                            <div class="row mb-3">
                                <div class="col-md-12 text-center">
                                    <h3 class="text-primary">RECETA MÉDICA</h3>
                                    <p>${tipo}</p>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-id-badge"></i> ID Receta:</strong> ${receta.id_receta}</p>
                                    <p><strong><i class="fas fa-capsules"></i> Medicamento:</strong> ${escapeHtml(receta.nombre_medicamento)}</p>
                                    <p><strong><i class="fas fa-trademark"></i> Marca:</strong> ${escapeHtml(receta.marca)}</p>
                                    <p><strong><i class="fas fa-cubes"></i> Cantidad:</strong> ${escapeHtml(receta.cantidad)}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-clock"></i> Dosis:</strong> ${escapeHtml(receta.dosis || 'No especificada')}</p>
                                    <p><strong><i class="fas fa-calendar-day"></i> Fecha:</strong> ${fechaFormateada}</p>
                                    <p><strong><i class="fas fa-user-injured"></i> Paciente:</strong> ${escapeHtml(receta.paciente || 'N/A')}</p>
                                    <p><strong><i class="fas fa-user-md"></i> Médico:</strong> ${escapeHtml(receta.medico || 'N/A')}</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <strong><i class="fas fa-stethoscope"></i> Instrucciones</strong>
                                        </div>
                                        <div class="card-body">
                                            ${escapeHtml(receta.instrucciones) || '<em class="text-muted">Sin instrucciones adicionales</em>'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12 text-muted text-center">
                                    <small>Documento generado electrónicamente por BioVital - Sistema de Gestión Médica</small>
                                    <br>
                                    <small>Fecha de emisión: ${new Date().toLocaleString()}</small>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#detalle_receta_content').html(html);
                } else {
                    $('#detalle_receta_content').html('<div class="alert alert-danger">Error al cargar los detalles de la receta</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener detalle:', error);
                $('#detalle_receta_content').html('<div class="alert alert-danger">Error al cargar los detalles de la receta</div>');
            }
        });
    }
    
    // ==================== FUNCIÓN DE EXPORTACIÓN ====================
    
    function exportarDatos() {
        let dataToExport = [...recetasData];
        
        if (filtroBusqueda) {
            let busquedaLower = filtroBusqueda.toLowerCase();
            dataToExport = dataToExport.filter(receta => 
                (receta.nombre_medicamento && receta.nombre_medicamento.toLowerCase().includes(busquedaLower)) ||
                (receta.paciente && receta.paciente.toLowerCase().includes(busquedaLower)) ||
                (receta.medico && receta.medico.toLowerCase().includes(busquedaLower))
            );
        }
        if (filtroTipo !== 'todas') {
            if (filtroTipo === 'medicamento') {
                dataToExport = dataToExport.filter(receta => 
                    receta.nombre_medicamento && !receta.nombre_medicamento.includes('ESTUDIOS') && !receta.nombre_medicamento.includes('DIAGNÓSTICO')
                );
            } else if (filtroTipo === 'estudio') {
                dataToExport = dataToExport.filter(receta => 
                    receta.nombre_medicamento && receta.nombre_medicamento.includes('ESTUDIOS')
                );
            } else if (filtroTipo === 'diagnostico') {
                dataToExport = dataToExport.filter(receta => 
                    receta.nombre_medicamento && receta.nombre_medicamento.includes('DIAGNÓSTICO')
                );
            }
        }
        if (filtroFecha) {
            dataToExport = dataToExport.filter(receta => receta.fecha_receta === filtroFecha);
        }
        
        if (dataToExport.length === 0) {
            mostrarAlerta('No hay datos para exportar', 'warning');
            return;
        }
        
        let csvContent = "ID,Medicamento,Marca,Cantidad,Dosis,Paciente,Médico,Fecha\n";
        
        for (let receta of dataToExport) {
            csvContent += `"${receta.id_receta || ''}","${escapeCsv(receta.nombre_medicamento || '')}","${escapeCsv(receta.marca || '')}","${escapeCsv(receta.cantidad || '')}","${escapeCsv(receta.dosis || '')}","${escapeCsv(receta.paciente || '')}","${escapeCsv(receta.medico || '')}","${receta.fecha_receta || ''}"\n`;
        }
        
        let blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
        let link = document.createElement("a");
        let url = URL.createObjectURL(blob);
        link.href = url;
        link.setAttribute("download", `recetas_${new Date().toISOString().slice(0, 19)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        
        mostrarAlerta('Exportación completada', 'success');
    }
    
    // ==================== FUNCIONES UTILITARIAS ====================
    
    function mostrarAlerta(mensaje, tipo) {
        // Usar SweetAlert si está disponible
        if (typeof Swal !== 'undefined') {
            let icono = tipo === 'success' ? 'success' : (tipo === 'error' ? 'error' : 'warning');
            Swal.fire({
                title: tipo === 'success' ? 'Éxito' : (tipo === 'error' ? 'Error' : 'Aviso'),
                text: mensaje,
                icon: icono,
                confirmButtonText: 'Aceptar',
                timer: tipo === 'success' ? 2000 : undefined,
                showConfirmButton: tipo !== 'success'
            });
        } else {
            // Fallback a toast
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
    
    function escapeCsv(str) {
        if (!str) return '';
        return str.replace(/"/g, '""');
    }
});