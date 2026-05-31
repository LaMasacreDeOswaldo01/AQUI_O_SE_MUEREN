<?php
// vista/administrador/adm_recetas.php
// Contenido principal para la gestión de recetas médicas (Administrador)
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
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
        color: var(--bv-primary);
    }
    .stats-card .stats-label {
        font-size: 0.7rem;
        color: var(--bv-text-light);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table-actions {
        white-space: nowrap;
        width: 80px;
    }
    .badge-receta {
        background-color: #28a745;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 11px;
    }
    .modal-lg-custom {
        max-width: 800px;
    }
    .receta-card {
        transition: transform 0.2s;
        margin-bottom: 20px;
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
    }
    .btn-ver-detalle {
        background: none;
        border: none;
        color: #007bff;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-ver-detalle:hover {
        color: #0056b3;
        transform: scale(1.05);
    }
    .receta-detalle {
        font-size: 0.9rem;
    }
    .receta-detalle h3 {
        font-size: 1.2rem;
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
    .filtro-fecha {
        max-width: 200px;
        display: inline-block;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-prescription-bottle-alt"></i> Recetas Médicas</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Welcome Banner -->
        <div class="bv-welcome-banner admin bv-animate">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-prescription me-2"></i> Gestión de Recetas</h2>
                    <p class="mb-0">Supervisa y audita todas las recetas médicas del sistema.</p>
                    <div class="bv-role-tag mt-2">
                        <i class="fas fa-clinic-medical"></i> Módulo de Recetas
                    </div>
                </div>
                <div class="d-none d-md-block">
                    <i class="fas fa-chart-line fa-3x" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-1">
                    <div class="stats-number" id="total_recetas">0</div>
                    <div class="stats-label">Total Recetas</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-1">
                    <div class="stats-number" id="total_medicos">0</div>
                    <div class="stats-label">Médicos Activos</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-2">
                    <div class="stats-number" id="total_pacientes">0</div>
                    <div class="stats-label">Pacientes</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-2">
                    <div class="stats-number" id="recetas_mes">0</div>
                    <div class="stats-label">Este Mes</div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="row mt-3">
            <div class="col-md-8">
                <div class="filter-card p-3">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="buscar_receta" class="form-control" 
                                       placeholder="Buscar por medicamento, paciente o médico...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="filtro_tipo">
                                <option value="todas">Todas las recetas</option>
                                <option value="medicamento">Medicamentos</option>
                                <option value="estudio">Estudios</option>
                                <option value="diagnostico">Diagnósticos</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control filtro-fecha" id="filtro_fecha" 
                                   placeholder="Filtrar por fecha">
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

        <!-- Tabla de Recetas -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Listado de Recetas
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Medicamento</th>
                                    <th>Marca</th>
                                    <th>Cantidad</th>
                                    <th>Dosis</th>
                                    <th>Paciente</th>
                                    <th>Médico</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_recetas">
                                <tr><td colspan="9" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando recetas...</p>
                                 </td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_info" id="info_recetas">
                                    Mostrando <span id="desde">0</span> a <span id="hasta">0</span> de <span id="total_registros">0</span> registros
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-end" id="paginacion">
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Ver Detalle Receta -->
<div class="modal fade modal-bv" id="modalDetalleReceta" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg-custom" role="document">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header bg-info text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-file-prescription"></i> Detalle de Receta
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalle_receta_content">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('=== GESTIÓN DE RECETAS ===');
    
    // Variables
    let recetasData = [];
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let filtroBusqueda = '';
    let filtroTipo = 'todas';
    let filtroFecha = '';
    
    // ==================== CARGAR DATOS ====================
    
    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + '/api/recetas/estadisticas',
            type: 'POST',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Estadísticas recibidas:', response);
                
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
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
            </td></tr>
        `);
        
        $.ajax({
            url: APP_URL + '/api/recetas/listar',
            type: 'POST',
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta recetas:', response);
                
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
                    </td></tr>
                `);
            }
        });
    }
    
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
                </td></tr>
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
                
                html += `
                    <tr>
                        <td><span class="badge badge-secondary">${receta.id_receta || ''}</span></td>
                        <td><strong>${escapeHtml(receta.nombre_medicamento || '')}</strong> ${tipoBadge}</td>
                        <td>${escapeHtml(receta.marca || '')}</td>
                        <td>${escapeHtml(receta.cantidad || '')}</td>
                        <td>${escapeHtml(receta.dosis || '-')}</td>
                        <td><i class="fas fa-user-injured text-info"></i> ${escapeHtml(receta.paciente || 'N/A')}</td>
                        <td><i class="fas fa-user-md text-success"></i> ${escapeHtml(receta.medico || 'N/A')}</td>
                        <td><i class="fas fa-calendar-alt"></i> ${receta.fecha_receta || ''}</td>
                        <td class="table-actions">
                            <button class="btn btn-info btn-sm btn-ver-detalle" data-id="${receta.id_receta}">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                        </td>
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
                        <a class="page-link" href="#" data-pagina="${paginaActual - 1}">« Anterior</a>
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
                        <a class="page-link" href="#" data-pagina="${paginaActual + 1}">Siguiente »</a>
                    </li>`;
        }
        
        $('#paginacion').html(html);
    }
    
    // ==================== DETALLE DE RECETA ====================
    
    $(document).on('click', '.btn-ver-detalle', function() {
        let id = $(this).data('id');
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
                    
                    let html = `
                        <div class="receta-detalle">
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
                                    <p><strong><i class="fas fa-calendar-day"></i> Fecha:</strong> ${receta.fecha_receta}</p>
                                    <p><strong><i class="fas fa-user-injured"></i> Paciente ID:</strong> ${receta.id_paciente || 'N/A'}</p>
                                    <p><strong><i class="fas fa-user-md"></i> Médico ID:</strong> ${receta.id_medico || 'N/A'}</p>
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
    });
    
    // ==================== FILTROS Y EVENTOS ====================
    
    $('#buscar_receta').on('keyup', function() {
        filtroBusqueda = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    $('#filtro_tipo').change(function() {
        filtroTipo = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    $('#filtro_fecha').change(function() {
        filtroFecha = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    $('#btnRefresh').click(function() {
        filtroBusqueda = '';
        filtroTipo = 'todas';
        filtroFecha = '';
        paginaActual = 1;
        $('#buscar_receta').val('');
        $('#filtro_tipo').val('todas');
        $('#filtro_fecha').val('');
        cargarRecetas();
        cargarEstadisticas();
        mostrarAlerta('Datos actualizados', 'success');
    });
    
    $('#btnExportar').click(function() {
        exportarDatos();
    });
    
    // Paginación
    $(document).on('click', '#paginacion .page-link', function(e) {
        e.preventDefault();
        let nuevaPagina = $(this).data('pagina');
        if (nuevaPagina && !$(this).parent().hasClass('disabled')) {
            paginaActual = nuevaPagina;
            aplicarFiltros();
        }
    });
    
    function exportarDatos() {
        let dataToExport = [];
        
        if (filtroBusqueda || filtroTipo !== 'todas' || filtroFecha) {
            // Exportar datos filtrados
            let filtrados = [...recetasData];
            if (filtroBusqueda) {
                let busquedaLower = filtroBusqueda.toLowerCase();
                filtrados = filtrados.filter(receta => 
                    (receta.nombre_medicamento && receta.nombre_medicamento.toLowerCase().includes(busquedaLower)) ||
                    (receta.paciente && receta.paciente.toLowerCase().includes(busquedaLower)) ||
                    (receta.medico && receta.medico.toLowerCase().includes(busquedaLower))
                );
            }
            if (filtroFecha) {
                filtrados = filtrados.filter(receta => receta.fecha_receta === filtroFecha);
            }
            dataToExport = filtrados;
        } else {
            dataToExport = recetasData;
        }
        
        if (dataToExport.length === 0) {
            mostrarAlerta('No hay datos para exportar', 'warning');
            return;
        }
        
        // Crear contenido CSV
        let csvContent = "ID,Medicamento,Marca,Cantidad,Dosis,Paciente,Médico,Fecha\n";
        
        for (let receta of dataToExport) {
            csvContent += `"${receta.id_receta || ''}","${escapeCsv(receta.nombre_medicamento || '')}","${escapeCsv(receta.marca || '')}","${escapeCsv(receta.cantidad || '')}","${escapeCsv(receta.dosis || '')}","${escapeCsv(receta.paciente || '')}","${escapeCsv(receta.medico || '')}","${receta.fecha_receta || ''}"\n`;
        }
        
        // Descargar archivo
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
        var alertDiv = $('<div>', {
            class: 'alert alert-' + (tipo === 'success' ? 'success' : tipo === 'warning' ? 'warning' : 'danger') + ' alert-dismissible fade show position-fixed',
            style: 'top: 70px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 12px;',
            role: 'alert'
        });
        
        var icon = tipo === 'success' ? 'fa-check-circle' : (tipo === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle');
        
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
    cargarRecetas();
});
</script>