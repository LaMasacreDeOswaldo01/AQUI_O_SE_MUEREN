<?php
// vista/especialidades/esp_listado.php
// Contenido principal para la gestión de especialidades
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
$api_url = $api_url ?? APP_URL . '/api/especialidades';
?>

<style>
    .especialidad-card {
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 20px;
        border-radius: 16px;
        overflow: hidden;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .especialidad-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12);
    }
    .especialidad-card .card-header {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        border: none;
        padding: 1rem 1.25rem;
    }
    .especialidad-card .card-header h5 {
        font-weight: 600;
        margin: 0;
    }
    .especialidad-card .card-body {
        padding: 1.25rem;
    }
    .especialidad-card .card-footer {
        background: #f8f9fa;
        border-top: 1px solid #eef2f6;
        padding: 0.75rem 1.25rem;
    }
    .badge-medicos {
        background-color: #e8f4f8;
        color: #0d9488;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-estado {
        background-color: #d4edda;
        color: #155724;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-estado-inactiva {
        background-color: #f8d7da;
        color: #721c24;
    }
    .search-box {
        max-width: 350px;
    }
    .info-box-icon {
        transition: transform 0.3s;
    }
    .info-box:hover .info-box-icon {
        transform: scale(1.1);
    }
    .btn-accion {
        transition: all 0.2s;
    }
    .btn-accion:hover {
        transform: translateY(-2px);
    }
    .descripcion-text {
        font-size: 0.85rem;
        color: #6c757d;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;  /* Propiedad estándar CSS */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .color-indicador {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        vertical-align: middle;
    }
    .prioridad-badge {
        font-size: 0.7rem;
        padding: 3px 8px;
        border-radius: 12px;
    }
    .prioridad-alta { background-color: #fde68a; color: #92400e; }
    .prioridad-media { background-color: #dbeafe; color: #1e40af; }
    .prioridad-baja { background-color: #e5e7eb; color: #374151; }
    .prioridad-urgente { background-color: #fee2e2; color: #991b1b; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .descripcion-text {
            -webkit-line-clamp: 2;
            line-clamp: 2;
        }
    }
</style>

<!-- Welcome Banner -->
<div class="bv-welcome-banner admin bv-animate">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-stethoscope me-2"></i> Especialidades Médicas</h2>
            <p class="mb-0">Gestiona las especialidades, tarifas y asignación de médicos.</p>
            <div class="bv-role-tag mt-2">
                <i class="fas fa-stethoscope"></i> Catálogo de Especialidades
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
        <div class="info-box bv-animate bv-animate-delay-1">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-stethoscope"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">TOTAL ESPECIALIDADES</span>
                <span class="info-box-number" id="total_especialidades">0</span>
                <div class="progress">
                    <div class="progress-bar bg-info" style="width: 0%"></div>
                </div>
                <span class="progress-description">
                    <i class="fas fa-check-circle"></i> <span id="total_activas">0</span> activas
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bv-animate bv-animate-delay-1">
            <span class="info-box-icon bg-warning elevation-1">
                <i class="fas fa-user-md"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">MÉDICOS ASIGNADOS</span>
                <span class="info-box-number" id="total_medicos">0</span>
                <span class="progress-description">
                    <i class="fas fa-users"></i> en todas las especialidades
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bv-animate bv-animate-delay-2">
            <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-calendar-week"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">CITAS DEL MES</span>
                <span class="info-box-number" id="citas_mes">0</span>
                <span class="progress-description">
                    <i class="fas fa-chart-line"></i> programadas este mes
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bv-animate bv-animate-delay-2">
            <span class="info-box-icon bg-primary elevation-1">
                <i class="fas fa-plus-circle"></i>
            </span>
            <div class="info-box-content">
                <button class="btn btn-primary btn-sm btn-block" id="btnNuevaEspecialidad">
                    <i class="fas fa-plus"></i> Nueva Especialidad
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                            <input type="text" id="buscar_especialidad" class="form-control" 
                                   placeholder="Buscar especialidad por nombre o descripción...">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" id="btnBuscar">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="filtro_estado">
                            <option value="todas">Todas las especialidades</option>
                            <option value="activas">Activas</option>
                            <option value="inactivas">Inactivas</option>
                        </select>
                    </div>
                    <div class="col-md-2 text-right">
                        <button class="btn btn-outline-secondary btn-sm" id="btnRefresh">
                            <i class="fas fa-sync-alt"></i> Actualizar
                        </button>
                    </div>
                </div>
                <div id="resultado_busqueda" class="mt-2 text-center" style="display: none;">
                    <small class="text-muted">
                        <i class="fas fa-filter"></i> Mostrando resultados para: 
                        <strong id="termino_busqueda" class="text-primary"></strong>
                        <a href="#" id="limpiarResultados" class="ml-2 text-danger">
                            <i class="fas fa-times-circle"></i> quitar filtro
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de especialidades (Grid estilo dashboard) -->
<div class="row" id="contenedor_especialidades">
    <div class="col-12 text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-2">Cargando especialidades...</p>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header bg-danger text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar esta especialidad?</p>
                <p class="text-muted">Esta acción no se puede deshacer. La especialidad se marcará como inactiva.</p>
                <input type="hidden" id="eliminar_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('=== CARGANDO MÓDULO DE ESPECIALIDADES ===');
    
    // Variables
    var busquedaActual = '';
    var estadoActual = 'todas';
    
    // Cargar estadísticas al iniciar
    cargarEstadisticas();
    cargarEspecialidades();
    
    // ==================== EVENTOS ====================
    
    // Botón nueva especialidad
    $('#btnNuevaEspecialidad').click(function() {
        window.location.href = APP_URL + '/especialidades/crear';
    });
    
    // Botón refrescar
    $('#btnRefresh').click(function() {
        cargarEspecialidades(busquedaActual, estadoActual);
        cargarEstadisticas();
    });
    
    // Botón buscar
    $('#btnBuscar').click(function() {
        busquedaActual = $('#buscar_especialidad').val();
        estadoActual = $('#filtro_estado').val();
        
        if (busquedaActual.length > 0) {
            $('#termino_busqueda').text(busquedaActual);
            $('#resultado_busqueda').show();
        } else {
            $('#resultado_busqueda').hide();
        }
        
        cargarEspecialidades(busquedaActual, estadoActual);
    });
    
    // Búsqueda con tecla Enter
    $('#buscar_especialidad').keypress(function(e) {
        if (e.which == 13) {
            $('#btnBuscar').click();
        }
    });
    
    // Filtro de estado
    $('#filtro_estado').change(function() {
        estadoActual = $(this).val();
        cargarEspecialidades(busquedaActual, estadoActual);
    });
    
    // Limpiar resultados
    $('#limpiarResultados').click(function(e) {
        e.preventDefault();
        $('#buscar_especialidad').val('');
        $('#resultado_busqueda').hide();
        busquedaActual = '';
        cargarEspecialidades('', estadoActual);
        cargarEstadisticas();
    });
    
    // Confirmar eliminación
    $('#confirmarEliminar').click(function() {
        eliminarEspecialidad($('#eliminar_id').val());
    });
    
    // ==================== FUNCIONES ====================
    
    function cargarEstadisticas() {
        console.log('Cargando estadísticas desde:', APP_URL + '/api/especialidades/estadisticas');
        
        $.ajax({
            url: APP_URL + '/api/especialidades/estadisticas',
            type: 'POST',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Estadísticas recibidas:', response);
                
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
        console.log('Cargando especialidades con búsqueda:', busqueda, 'estado:', estado);
        
        $('#contenedor_especialidades').html(`
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-2">Cargando especialidades...</p>
            </div>
        `);
        
        $.ajax({
            url: APP_URL + '/api/especialidades/listar',
            type: 'POST',
            data: { busqueda: busqueda, estado: estado },
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta especialidades:', response);
                
                // Manejar formato ApiResponse
                var especialidades = [];
                if (response.success && response.data) {
                    especialidades = response.data;
                } else if (Array.isArray(response)) {
                    especialidades = response;
                } else if (response.especialidades && Array.isArray(response.especialidades)) {
                    especialidades = response.especialidades;
                }
                
                // Asegurar que sea un array
                if (!Array.isArray(especialidades)) {
                    especialidades = [];
                }
                
                console.log('Especialidades procesadas:', especialidades.length);
                
                let html = '';
                
                if (especialidades.length === 0) {
                    html = `
                        <div class="col-12 text-center">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No se encontraron especialidades
                            </div>
                        </div>
                    `;
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
                                            <span class="horario-text">
                                                ${esp.duracion_defecto || 30} minutos por cita
                                            </span>
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
                                            <a href="${APP_URL}/especialidades/detalle/${esp.id_especialidad}" class="btn btn-info btn-sm btn-accion">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <button class="btn btn-warning btn-sm btn-accion btn-editar" data-id="${esp.id_especialidad}">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            ${esp.activo == 1 ? `
                                            <button class="btn btn-danger btn-sm btn-accion btn-eliminar" data-id="${esp.id_especialidad}">
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
                $('#contenedor_especialidades').html(`
                    <div class="col-12 text-center">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar especialidades: ${error}
                        </div>
                    </div>
                `);
            }
        });
    }
    
    function eliminarEspecialidad(id) {
        console.log('Eliminando especialidad ID:', id);
        
        $.ajax({
            url: APP_URL + '/api/especialidades/eliminar',
            type: 'POST',
            data: { id_especialidad: id },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta eliminar:', response);
                
                if (response.resultado === 'eliminado' || response.success === true) {
                    $('#modalEliminar').modal('hide');
                    cargarEspecialidades(busquedaActual, estadoActual);
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
    
    // Editar especialidad (redireccionar)
    $(document).on('click', '.btn-editar', function() {
        let id = $(this).data('id');
        window.location.href = APP_URL + '/especialidades/editar?id=' + id;
    });
    
    // Eliminar especialidad (abrir modal)
    $(document).on('click', '.btn-eliminar', function() {
        let id = $(this).data('id');
        $('#eliminar_id').val(id);
        $('#modalEliminar').modal('show');
    });
    
    // ==================== FUNCIONES UTILITARIAS ====================
    
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
        var alertDiv = $('<div>', {
            class: 'alert alert-' + (tipo === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show position-fixed',
            style: 'top: 70px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 12px;',
            role: 'alert'
        });
        
        alertDiv.html(`
            <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
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
});
</script>