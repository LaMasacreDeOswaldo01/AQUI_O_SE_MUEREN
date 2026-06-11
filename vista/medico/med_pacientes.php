<?php
// vista/medico/med_pacientes.php
// Contenido principal para la gestión de pacientes del médico
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_medico = $id_medico ?? $_SESSION['usuario'] ?? 0;
?>

<!-- CSS Adicional para esta vista -->
<style>
    .stats-card {
        background: white;
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s;
        border: 1px solid #eef2f6;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .stats-card .stats-number {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0d9488;
    }
    .stats-card .stats-label {
        font-size: 0.7rem;
        color: var(--bv-text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .paciente-card {
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 1rem;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #eef2f6;
        background: white;
    }
    .paciente-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .paciente-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #0d9488;
    }
    .paciente-avatar-placeholder {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #64748b;
        font-weight: bold;
    }
    .paciente-nombre {
        font-weight: 700;
        color: var(--bv-dark);
        margin-bottom: 0.25rem;
    }
    .paciente-detalle {
        font-size: 0.7rem;
        color: var(--bv-text-light);
    }
    .btn-recetas {
        background: linear-gradient(135deg, #0d9488, #0f766e);
        border: none;
        border-radius: 8px;
        padding: 0.25rem 0.75rem;
        font-size: 0.7rem;
        transition: all 0.2s;
    }
    .btn-recetas:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(13,148,136,0.3);
    }
    .search-box {
        position: relative;
    }
    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .search-box input {
        padding-left: 35px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
    }
    .filter-card {
        border-radius: 16px;
        border: 1px solid #eef2f6;
        background: white;
    }
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: #fafbfc;
        border-radius: 16px;
    }
    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }
    .pagination-custom {
        margin-bottom: 0;
    }
    .pagination-custom .page-item.active .page-link {
        background-color: #0d9488;
        border-color: #0d9488;
    }
    .pagination-custom .page-link {
        color: #0d9488;
        border-radius: 8px;
        margin: 0 2px;
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
        color: #0d9488;
        margin-bottom: 0.75rem;
    }
    .info-card p {
        font-size: 0.75rem;
        color: var(--bv-text-light);
        margin-bottom: 0.5rem;
    }
    .badre-ultima-visita {
        font-size: 0.65rem;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 20px;
        display: inline-block;
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
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-users"></i> Mis Pacientes</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_medico" value="<?php echo $id_medico; ?>">
        
        <!-- Welcome Banner -->
        <div class="welcome-stats text-white" style="background: linear-gradient(135deg, #0d9488, #0f766e); border-radius: 20px; padding: 1.5rem; margin-bottom: 1.5rem;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-users me-2"></i> 
                        Directorio de Pacientes
                    </h2>
                    <p class="mb-0 opacity-75">Gestiona y consulta el historial de tus pacientes atendidos.</p>
                </div>
                <div class="d-none d-md-block">
                    <i class="fas fa-chart-line fa-3x" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-1">
                    <div class="stats-number" id="total_pacientes">0</div>
                    <div class="stats-label">Total Pacientes Atendidos</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-2">
                    <div class="stats-number" id="total_recetas">0</div>
                    <div class="stats-label">Recetas Emitidas</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-3">
                    <div class="stats-number" id="ultima_visita">-</div>
                    <div class="stats-label">Última Visita</div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="row mt-3">
            <div class="col-md-8">
                <div class="filter-card p-3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="buscar_paciente" class="form-control" 
                                       placeholder="Buscar por nombre, cédula o correo...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="filtro_orden">
                                <option value="nombre">Ordenar por nombre</option>
                                <option value="fecha">Ordenar por última visita</option>
                                <option value="recetas">Ordenar por número de recetas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="filtro_limit">
                                <option value="10">10 por página</option>
                                <option value="25">25 por página</option>
                                <option value="50">50 por página</option>
                                <option value="100">100 por página</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="filter-card p-3 text-right">
                    <button class="btn btn-primary btn-sm" id="btnRefresh">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                    <button class="btn btn-success btn-sm ml-2" id="btnExportar">
                        <i class="fas fa-file-excel"></i> Exportar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Pacientes -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Listado de Pacientes
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="loadingPacientes" class="loading-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        <div id="contenedor_pacientes">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando pacientes...</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_info" id="info_pacientes">
                                    Mostrando <span id="desde">0</span> a <span id="hasta">0</span> de <span id="total_registros">0</span> pacientes
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-end pagination-custom" id="paginacion">
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Adicional -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="info-card">
                    <h6><i class="fas fa-info-circle"></i> Información de Pacientes</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <p><i class="fas fa-chart-line text-success"></i> <strong>Estadísticas:</strong> Visualiza el número total de pacientes atendidos</p>
                        </div>
                        <div class="col-md-4">
                            <p><i class="fas fa-prescription-bottle-alt text-info"></i> <strong>Recetas:</strong> Accede al historial de recetas de cada paciente</p>
                        </div>
                        <div class="col-md-4">
                            <p><i class="fas fa-calendar-alt text-warning"></i> <strong>Última visita:</strong> Controla la frecuencia de atención de tus pacientes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Ver Recetas del Paciente -->
<div class="modal fade modal-bv" id="modalRecetasPaciente" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0d9488, #0f766e); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-prescription-bottle-alt"></i> Recetas de <span id="modal_paciente_nombre"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="recetas_paciente_content">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2">Cargando recetas...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnNuevaRecetaPaciente">
                    <i class="fas fa-plus"></i> Nueva Receta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('=== LISTADO DE PACIENTES DEL MÉDICO ===');
    console.log('ID Médico:', $('#id_medico').val());
    
    // Variables
    let pacientesData = [];
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let busquedaActual = '';
    let ordenActual = 'nombre';
    let pacienteSeleccionadoId = null;
    let pacienteSeleccionadoNombre = '';
    
    // ==================== CARGAR DATOS ====================
    
    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + '/api/medicos/mis-estadisticas',
            type: 'POST',
            data: { id_medico: <?php echo $id_medico; ?> },
            dataType: 'json',
            success: function(response) {
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                $('#total_recetas').text(data.total_recetas || 0);
            },
            error: function() {
                $('#total_recetas').text('0');
            }
        });
    }
    
    function cargarPacientes() {
        $('#loadingPacientes').show();
        
        $.ajax({
            url: APP_URL + '/api/medicos/listar-pacientes',
            type: 'POST',
            data: { id_medico: <?php echo $id_medico; ?> },
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Pacientes recibidos:', response);
                
                var pacientes = [];
                if (response.success && response.data) {
                    pacientes = response.data;
                } else if (Array.isArray(response)) {
                    pacientes = response;
                }
                
                pacientesData = pacientes;
                $('#total_pacientes').text(pacientes.length);
                $('#ultima_visita').text(pacientes.length > 0 ? 'Activo' : 'Sin pacientes');
                
                aplicarFiltros();
                $('#loadingPacientes').hide();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar pacientes:', error);
                $('#contenedor_pacientes').html(`
                    <div class="text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los pacientes: ${error}
                        </div>
                    </div>
                `);
                $('#loadingPacientes').hide();
            }
        });
    }
    
    function aplicarFiltros() {
        let filtrados = [...pacientesData];
        
        // Filtro por búsqueda
        if (busquedaActual) {
            let busquedaLower = busquedaActual.toLowerCase();
            filtrados = filtrados.filter(paciente => {
                return (paciente.nombre && paciente.nombre.toLowerCase().includes(busquedaLower)) ||
                       (paciente.apellidos && paciente.apellidos.toLowerCase().includes(busquedaLower)) ||
                       (paciente.cedula && paciente.cedula.includes(busquedaActual)) ||
                       (paciente.correo && paciente.correo.toLowerCase().includes(busquedaLower));
            });
        }
        
        // Ordenamiento
        if (ordenActual === 'nombre') {
            filtrados.sort((a, b) => (a.nombre || '').localeCompare(b.nombre || ''));
        } else if (ordenActual === 'fecha') {
            filtrados.sort((a, b) => new Date(b.ultima_receta || 0) - new Date(a.ultima_receta || 0));
        } else if (ordenActual === 'recetas') {
            filtrados.sort((a, b) => (b.total_recetas || 0) - (a.total_recetas || 0));
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
        let pacientesPagina = filtrados.slice(inicio, fin);
        
        renderizarPacientes(pacientesPagina);
        renderizarPaginacion(total);
    }
    
    function renderizarPacientes(pacientes) {
        let html = '';
        
        if (pacientes.length === 0) {
            html = `
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <p>No se encontraron pacientes</p>
                    <p class="text-muted small">Intente con otros criterios de búsqueda</p>
                </div>
            `;
        } else {
            html = `<div class="table-responsive">`;
            html += `
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Cédula</th>
                            <th>Contacto</th>
                            <th>Recetas</th>
                            <th>Última Visita</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            for (let i = 0; i < pacientes.length; i++) {
                let p = pacientes[i];
                let inicial = (p.nombre || 'P').charAt(0).toUpperCase();
                let nombreCompleto = (p.nombre || '') + ' ' + (p.apellidos || '');
                let ultimaVisita = p.ultima_receta ? formatearFecha(p.ultima_receta) : 'Nunca';
                
                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="paciente-avatar-placeholder mr-3">
                                    ${inicial}
                                </div>
                                <div>
                                    <div class="paciente-nombre">${escapeHtml(nombreCompleto)}</div>
                                    <div class="paciente-detalle">
                                        <i class="fas fa-envelope"></i> ${escapeHtml(p.correo || 'No registrado')}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-light">${p.cedula || 'N/A'}</span>
                        </td>
                        <td>
                            <i class="fas fa-phone text-success"></i> ${p.telefono || 'No registrado'}<br>
                            <small class="text-muted">${p.correo || ''}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">${p.total_recetas || 0}</span>
                        </td>
                        <td>
                            <span class="badre-ultima-visita">
                                <i class="fas fa-calendar-alt"></i> ${ultimaVisita}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-recetas btn-sm btn-ver-recetas" 
                                    data-id="${p.id_paciente}" 
                                    data-nombre="${escapeHtml(nombreCompleto)}">
                                <i class="fas fa-prescription-bottle-alt"></i> Recetas
                            </button>
                        </td>
                    </tr>
                `;
            }
            
            html += `
                    </tbody>
                </table>
            </div>`;
        }
        
        $('#contenedor_pacientes').html(html);
    }
    
    function renderizarPaginacion(total) {
        let totalPaginas = Math.ceil(total / registrosPorPagina);
        let html = '';
        
        if (totalPaginas <= 1) {
            html = '';
        } else {
            html += `<li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-pagina="${paginaActual - 1}">«</a>
                    </li>`;
            
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
            
            html += `<li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
                        <a class="page-link" href="#" data-pagina="${paginaActual + 1}">»</a>
                    </li>`;
        }
        
        $('#paginacion').html(html);
    }
    
    // ==================== VER RECETAS DEL PACIENTE ====================
    
    $(document).on('click', '.btn-ver-recetas', function() {
        let id_paciente = $(this).data('id');
        let nombre_paciente = $(this).data('nombre');
        
        pacienteSeleccionadoId = id_paciente;
        pacienteSeleccionadoNombre = nombre_paciente;
        
        $('#modal_paciente_nombre').text(nombre_paciente);
        $('#recetas_paciente_content').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Cargando recetas...</p>
            </div>
        `);
        $('#modalRecetasPaciente').modal('show');
        
        $.ajax({
            url: APP_URL + '/api/recetas/mis-recetas',
            type: 'POST',
            data: { id_paciente: id_paciente },
            dataType: 'json',
            success: function(response) {
                var recetas = [];
                if (response.success && response.data) {
                    recetas = response.data;
                } else if (Array.isArray(response)) {
                    recetas = response;
                }
                
                let html = '';
                
                if (recetas.length === 0) {
                    html = `
                        <div class="empty-state">
                            <i class="fas fa-prescription-bottle-alt"></i>
                            <p>No hay recetas registradas para este paciente</p>
                            <button class="btn btn-primary btn-sm" id="btnCrearRecetaDesdeModal">
                                <i class="fas fa-plus"></i> Crear primera receta
                            </button>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Medicamento</th>
                                        <th>Marca</th>
                                        <th>Cantidad</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    for (let i = 0; i < recetas.length; i++) {
                        let r = recetas[i];
                        html += `
                            <tr>
                                <td>${r.id_receta}</td>
                                <td><strong>${escapeHtml(r.nombre_medicamento)}</strong></td>
                                <td>${escapeHtml(r.marca)}</td>
                                <td>${escapeHtml(r.cantidad)}</td>
                                <td>${r.fecha_receta}</td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-ver-receta-detalle" data-id="${r.id_receta}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                 </td>
                            </tr>
                        `;
                    }
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                $('#recetas_paciente_content').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#recetas_paciente_content').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Error al cargar las recetas
                    </div>
                `);
            }
        });
    });
    
    // Ver detalle de receta desde el modal
    $(document).on('click', '.btn-ver-receta-detalle', function() {
        let id_receta = $(this).data('id');
        
        $.ajax({
            url: APP_URL + '/api/recetas/obtener',
            type: 'POST',
            data: { id_receta: id_receta },
            dataType: 'json',
            success: function(response) {
                var receta = response;
                if (response.success && response.data) {
                    receta = response.data;
                }
                
                let html = `
                    <div class="receta-detalle p-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-capsules"></i> Medicamento:</strong> ${escapeHtml(receta.nombre_medicamento)}</p>
                                <p><strong><i class="fas fa-trademark"></i> Marca:</strong> ${escapeHtml(receta.marca)}</p>
                                <p><strong><i class="fas fa-cubes"></i> Cantidad:</strong> ${escapeHtml(receta.cantidad)}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-clock"></i> Dosis:</strong> ${escapeHtml(receta.dosis || 'No especificada')}</p>
                                <p><strong><i class="fas fa-calendar-day"></i> Fecha:</strong> ${receta.fecha_receta}</p>
                            </div>
                        </div>
                        <div class="row mt-2">
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
                    </div>
                `;
                
                Swal.fire({
                    title: 'Detalle de Receta',
                    html: html,
                    width: '700px',
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#0d9488'
                });
            },
            error: function() {
                mostrarAlerta('Error al cargar el detalle de la receta', 'error');
            }
        });
    });
    
    // Botón nueva receta desde modal
    $('#btnNuevaRecetaPaciente, #btnCrearRecetaDesdeModal').click(function() {
        $('#modalRecetasPaciente').modal('hide');
        // Redirigir a la página de recetas con el paciente seleccionado
        window.location.href = APP_URL + '/recetas?paciente_id=' + pacienteSeleccionadoId + '&paciente_nombre=' + encodeURIComponent(pacienteSeleccionadoNombre);
    });
    
    // ==================== FILTROS Y EVENTOS ====================
    
    $('#buscar_paciente').on('keyup', function() {
        busquedaActual = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    $('#filtro_orden').change(function() {
        ordenActual = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    $('#filtro_limit').change(function() {
        registrosPorPagina = parseInt($(this).val());
        paginaActual = 1;
        aplicarFiltros();
    });
    
    $('#btnRefresh').click(function() {
        busquedaActual = '';
        ordenActual = 'nombre';
        registrosPorPagina = 10;
        paginaActual = 1;
        $('#buscar_paciente').val('');
        $('#filtro_orden').val('nombre');
        $('#filtro_limit').val('10');
        cargarPacientes();
        cargarEstadisticas();
        mostrarAlerta('Datos actualizados', 'success');
    });
    
    $('#btnExportar').click(function() {
        exportarDatos();
    });
    
    function exportarDatos() {
        let dataToExport = [...pacientesData];
        
        if (busquedaActual) {
            let busquedaLower = busquedaActual.toLowerCase();
            dataToExport = dataToExport.filter(paciente => 
                (paciente.nombre && paciente.nombre.toLowerCase().includes(busquedaLower)) ||
                (paciente.apellidos && paciente.apellidos.toLowerCase().includes(busquedaLower)) ||
                (paciente.cedula && paciente.cedula.includes(busquedaActual))
            );
        }
        
        if (dataToExport.length === 0) {
            mostrarAlerta('No hay datos para exportar', 'warning');
            return;
        }
        
        let csvContent = "ID,Nombre,Apellidos,Cédula,Teléfono,Correo,Recetas\n";
        
        for (let paciente of dataToExport) {
            csvContent += `"${paciente.id_paciente || ''}","${escapeCsv(paciente.nombre || '')}","${escapeCsv(paciente.apellidos || '')}","${paciente.cedula || ''}","${paciente.telefono || ''}","${paciente.correo || ''}","${paciente.total_recetas || 0}"\n`;
        }
        
        let blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
        let link = document.createElement("a");
        let url = URL.createObjectURL(blob);
        link.href = url;
        link.setAttribute("download", `pacientes_${new Date().toISOString().slice(0, 19)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        
        mostrarAlerta('Exportación completada', 'success');
    }
    
    // Paginación
    $(document).on('click', '#paginacion .page-link', function(e) {
        e.preventDefault();
        let nuevaPagina = $(this).data('pagina');
        if (nuevaPagina && !$(this).parent().hasClass('disabled')) {
            paginaActual = nuevaPagina;
            aplicarFiltros();
        }
    });
    
    // ==================== FUNCIONES UTILITARIAS ====================
    
    function formatearFecha(fecha) {
        if (!fecha) return 'Nunca';
        let date = new Date(fecha);
        return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }
    
    function mostrarAlerta(mensaje, tipo) {
        var alertDiv = $('<div>', {
            class: 'alert alert-' + (tipo === 'success' ? 'success' : 'warning') + ' alert-dismissible fade show position-fixed',
            style: 'top: 70px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 12px;',
            role: 'alert'
        });
        
        var icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
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
    
    function escapeCsv(str) {
        if (!str) return '';
        return str.replace(/"/g, '""');
    }
    
    // ==================== INICIALIZAR ====================
    cargarEstadisticas();
    cargarPacientes();
});
</script>