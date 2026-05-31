<?php
// vista/administrador/adm_consultorios.php
// Contenido principal para la gestión de consultorios
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
?>

<!-- CSS Adicional para esta vista -->
<style>
    .consultorio-card {
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 20px;
        border-radius: 16px;
        overflow: hidden;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .consultorio-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12);
    }
    .consultorio-card .card-header {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        border: none;
        padding: 1rem 1.25rem;
    }
    .consultorio-card .card-header h5 {
        font-weight: 600;
        margin: 0;
    }
    .consultorio-card .card-body {
        padding: 1.25rem;
    }
    .consultorio-card .card-footer {
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
    .ubicacion-text {
        font-size: 0.85rem;
        color: #6c757d;
        line-height: 1.4;
    }
    .horario-text {
        font-family: monospace;
        font-size: 0.85rem;
        background: #f0f0f0;
        padding: 3px 8px;
        border-radius: 12px;
        display: inline-block;
    }
    .estado-badge {
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 20px;
    }
    .estado-activo {
        background-color: #d1fae5;
        color: #065f46;
    }
    .estado-inactivo {
        background-color: #fee2e2;
        color: #991b1b;
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
    .empty-state p {
        color: #94a3b8;
        margin-bottom: 1rem;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-building"></i> Gestión de Consultorios</h1>
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
                    <h2><i class="fas fa-building me-2"></i> Consultorios Médicos</h2>
                    <p class="mb-0">Gestiona las sedes, horarios y asignación de médicos a cada consultorio.</p>
                    <div class="bv-role-tag mt-2">
                        <i class="fas fa-hospital-user"></i> Infraestructura
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
                        <i class="fas fa-building"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL CONSULTORIOS</span>
                        <span class="info-box-number" id="total_consultorios">0</span>
                        <div class="progress">
                            <div class="progress-bar bg-info" style="width: 0%"></div>
                        </div>
                        <span class="progress-description">
                            <i class="fas fa-check-circle"></i> <span id="total_activos">0</span> activos
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
                        <span class="info-box-number" id="total_medicos_asignados">0</span>
                        <span class="progress-description">
                            <i class="fas fa-users"></i> en todos los consultorios
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bv-animate bv-animate-delay-2">
                    <span class="info-box-icon bg-success elevation-1">
                        <i class="fas fa-calendar-check"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">CITAS HOY</span>
                        <span class="info-box-number" id="citas_hoy">0</span>
                        <span class="progress-description">
                            <i class="fas fa-chart-line"></i> programadas
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
                        <button class="btn btn-primary btn-sm btn-block" id="btnNuevoConsultorio">
                            <i class="fas fa-plus"></i> Nuevo Consultorio
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-2">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                            <input type="text" id="buscar_consultorio" class="form-control" 
                                   placeholder="Buscar consultorio por nombre, ciudad o dirección...">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" id="btnBuscar">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <button class="btn btn-outline-secondary" id="btnLimpiarBusqueda" style="display: none;">
                                    <i class="fas fa-times"></i> Limpiar
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

        <!-- Tarjetas de consultorios -->
        <div class="row" id="contenedor_consultorios">
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-2">Cargando consultorios...</p>
            </div>
        </div>
    </div>
</section>

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
                <p>¿Está seguro que desea eliminar este consultorio?</p>
                <p class="text-muted">Esta acción no se puede deshacer. El consultorio se marcará como inactivo.</p>
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
    console.log('=== CARGANDO MÓDULO DE CONSULTORIOS ===');
    
    // Variables
    var busquedaActual = '';
    
    // Cargar estadísticas al iniciar
    cargarEstadisticas();
    cargarConsultorios();
    
    // ==================== EVENTOS ====================
    
    // Botón nuevo consultorio
    $('#btnNuevoConsultorio').click(function() {
        window.location.href = APP_URL + '/consultorios/crear';
    });
    
    // Botón buscar
    $('#btnBuscar').click(function() {
        busquedaActual = $('#buscar_consultorio').val();
        
        if (busquedaActual.length > 0) {
            $('#termino_busqueda').text(busquedaActual);
            $('#resultado_busqueda').show();
            $('#btnLimpiarBusqueda').show();
        } else {
            $('#resultado_busqueda').hide();
            $('#btnLimpiarBusqueda').hide();
        }
        
        cargarConsultorios(busquedaActual);
    });
    
    // Búsqueda con tecla Enter
    $('#buscar_consultorio').keypress(function(e) {
        if (e.which == 13) {
            $('#btnBuscar').click();
        }
    });
    
    // Limpiar búsqueda
    $('#btnLimpiarBusqueda, #limpiarResultados').click(function(e) {
        e.preventDefault();
        $('#buscar_consultorio').val('');
        $('#resultado_busqueda').hide();
        $('#btnLimpiarBusqueda').hide();
        busquedaActual = '';
        cargarConsultorios('');
        cargarEstadisticas();
    });
    
    // Confirmar eliminación
    $('#confirmarEliminar').click(function() {
        eliminarConsultorio($('#eliminar_id').val());
    });
    
    // ==================== FUNCIONES PRINCIPALES ====================
    
    function cargarEstadisticas() {
        console.log('Cargando estadísticas desde:', APP_URL + '/api/consultorios/estadisticas');
        
        $.ajax({
            url: APP_URL + '/api/consultorios/estadisticas',
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
                
                $('#total_consultorios').text(data.total_consultorios || 0);
                $('#total_activos').text(data.activos || 0);
                $('#total_medicos_asignados').text(data.total_medicos_asignados || 0);
                $('#citas_hoy').text(data.citas_hoy || 0);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estadísticas:', error);
                $('#total_consultorios').text('0');
                $('#total_activos').text('0');
                $('#total_medicos_asignados').text('0');
                $('#citas_hoy').text('0');
            }
        });
    }
    
    function cargarConsultorios(busqueda = '') {
        console.log('Cargando consultorios con búsqueda:', busqueda);
        
        $('#contenedor_consultorios').html(`
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-2">Cargando consultorios...</p>
            </div>
        `);
        
        $.ajax({
            url: APP_URL + '/api/consultorios/listar',
            type: 'POST',
            data: { busqueda: busqueda },
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta consultorios:', response);
                
                // Manejar formato ApiResponse
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
                    html = `
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="fas fa-building"></i>
                                <p>No se encontraron consultorios</p>
                                <button class="btn btn-primary" id="btnCrearPrimero">
                                    <i class="fas fa-plus"></i> Crear primer consultorio
                                </button>
                            </div>
                        </div>
                    `;
                    
                    $('#btnCrearPrimero').click(function() {
                        window.location.href = APP_URL + '/consultorios/crear';
                    });
                } else {
                    for (let i = 0; i < consultorios.length; i++) {
                        let c = consultorios[i];
                        let estadoClass = 'estado-activo';
                        let estadoIcono = 'fa-check-circle';
                        
                        let direccionMostrar = c.direccion_detallada || 'Sin dirección registrada';
                        if (direccionMostrar.length > 60) {
                            direccionMostrar = direccionMostrar.substring(0, 60) + '...';
                        }
                        
                        html += `
                            <div class="col-md-4 col-sm-6">
                                <div class="consultorio-card h-100">
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
                                            <a href="${APP_URL}/consultorios/detalle/${c.id_consultorio}" class="btn btn-info btn-sm btn-accion">
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
                $('#total_medicos_asignados').text(totalMedicos);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar consultorios:', error);
                $('#contenedor_consultorios').html(`
                    <div class="col-12 text-center">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar consultorios: ${error}
                        </div>
                    </div>
                `);
            }
        });
    }
    
    function eliminarConsultorio(id) {
        console.log('Eliminando consultorio ID:', id);
        
        $.ajax({
            url: APP_URL + '/api/consultorios/eliminar',
            type: 'POST',
            data: { id_consultorio: id },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta eliminar:', response);
                
                // Manejar formato ApiResponse
                var resultado = response;
                if (response.success && response.data) {
                    resultado = response.data;
                }
                
                if (response.success === true || response.resultado === 'eliminado' || resultado.resultado === 'eliminado') {
                    $('#modalEliminar').modal('hide');
                    cargarConsultorios(busquedaActual);
                    cargarEstadisticas();
                    mostrarAlerta('Consultorio eliminado correctamente', 'success');
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
    
    // Evento para eliminar (abrir modal)
    $(document).on('click', '.btn-eliminar', function() {
        let id = $(this).data('id');
        let nombre = $(this).data('nombre');
        $('#eliminar_id').val(id);
        $('#modalEliminar .modal-body p').first().html(`¿Está seguro que desea eliminar el consultorio <strong>${nombre}</strong>?`);
        $('#modalEliminar').modal('show');
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
});
</script>