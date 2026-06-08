<?php
// vista/administrador/adm_usuarios.php
// Contenido principal para la gestión de usuarios del sistema
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
?>

<!-- CSS Adicional para esta vista -->
<style>
    .user-card {
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 20px;
        border-radius: 16px;
        overflow: hidden;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .user-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12);
    }
    .user-card .card-header {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        border: none;
        padding: 0.75rem 1rem;
    }
    .user-card .card-body {
        padding: 1rem;
    }
    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .user-avatar-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #64748b;
    }
    .badge-role {
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
    }
    .badge-role-paciente { background-color: #dbeafe; color: #1e40af; }
    .badge-role-medico { background-color: #d1fae5; color: #065f46; }
    .badge-role-asistente { background-color: #fef3c7; color: #92400e; }
    .badge-role-administrador { background-color: #fee2e2; color: #991b1b; }
    
    .badge-status {
        font-size: 0.65rem;
        padding: 3px 8px;
        border-radius: 20px;
    }
    .badge-status-activo { background-color: #d1fae5; color: #065f46; }
    .badge-status-inactivo { background-color: #fee2e2; color: #991b1b; }
    
    .filter-card {
        border-radius: 16px;
        border: 1px solid #eef2f6;
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
    .user-info {
        flex: 1;
    }
    .user-name {
        font-weight: 700;
        color: var(--bv-dark);
        margin-bottom: 0.25rem;
    }
    .user-detail {
        font-size: 0.7rem;
        color: var(--bv-text-light);
    }
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    .btn-sm-custom {
        padding: 0.25rem 0.6rem;
        font-size: 0.7rem;
        border-radius: 8px;
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
    .modal-bv .modal-content {
        border-radius: 16px;
    }
    .modal-bv .modal-header {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        border-radius: 16px 16px 0 0;
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .3s;
        border-radius: 24px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #28a745;
    }
    input:checked + .slider:before {
        transform: translateX(20px);
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
                <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
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
                    <h2><i class="fas fa-users-cog me-2"></i> Administración de Usuarios</h2>
                    <p class="mb-0">Gestiona las cuentas de pacientes, médicos, asistentes y administradores del sistema.</p>
                    <div class="bv-role-tag mt-2">
                        <i class="fas fa-user-shield"></i> Control de Acceso
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
                    <div class="stats-number" id="total_usuarios">0</div>
                    <div class="stats-label">Total Usuarios</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-1">
                    <div class="stats-number" id="total_pacientes">0</div>
                    <div class="stats-label">Pacientes</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-2">
                    <div class="stats-number" id="total_medicos">0</div>
                    <div class="stats-label">Médicos</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-2">
                    <div class="stats-number" id="total_asistentes">0</div>
                    <div class="stats-label">Asistentes</div>
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
                                <input type="text" id="buscar_usuario" class="form-control" 
                                       placeholder="Buscar por nombre, cédula o correo...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="filtro_rol">
                                <option value="todos">Todos los roles</option>
                                <option value="paciente">Pacientes</option>
                                <option value="medico">Médicos</option>
                                <option value="asistente">Asistentes</option>
                                <option value="administrador">Administradores</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filtro_estado">
                                <option value="todos">Todos los estados</option>
                                <option value="activo">Activos</option>
                                <option value="inactivo">Inactivos</option>
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
                </div>
            </div>
        </div>

        <!-- User Cards Grid -->
        <div class="row mt-3" id="contenedor_usuarios">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-2">Cargando usuarios...</p>
            </div>
        </div>
    </div>
</section>

<!-- Modal Editar Usuario -->
<div class="modal fade modal-bv" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit"></i> Editar Usuario
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_usuario_id">
                <input type="hidden" id="edit_usuario_rol">
                
                <div class="form-group">
                    <label for="edit_nombre">Nombre</label>
                    <input type="text" class="form-control" id="edit_nombre" readonly>
                </div>
                
                <div class="form-group">
                    <label for="edit_apellidos">Apellidos</label>
                    <input type="text" class="form-control" id="edit_apellidos" readonly>
                </div>
                
                <div class="form-group">
                    <label for="edit_cedula">Cédula</label>
                    <input type="text" class="form-control" id="edit_cedula" readonly>
                </div>
                
                <div class="form-group">
                    <label for="edit_correo">Correo Electrónico</label>
                    <input type="email" class="form-control" id="edit_correo">
                </div>
                
                <div class="form-group">
                    <label for="edit_telefono">Teléfono</label>
                    <input type="text" class="form-control" id="edit_telefono">
                </div>
                
                <div class="form-group">
                    <label for="edit_estado">Estado de la Cuenta</label>
                    <div class="d-flex align-items-center">
                        <label class="switch">
                            <input type="checkbox" id="edit_estado_switch">
                            <span class="slider"></span>
                        </label>
                        <span class="ml-2" id="edit_estado_texto">Activo</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarUsuario">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Usuario -->
<div class="modal fade modal-bv" id="modalEliminarUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar este usuario?</p>
                <p class="text-muted">Esta acción no se puede deshacer. El usuario será eliminado permanentemente del sistema.</p>
                <input type="hidden" id="eliminar_usuario_id">
                <input type="hidden" id="eliminar_usuario_rol">
                <p><strong>Usuario:</strong> <span id="eliminar_usuario_nombre"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('=== GESTIÓN DE USUARIOS ===');
    
    // Variables
    let busquedaActual = '';
    let rolActual = 'todos';
    let estadoActual = 'todos';
    let usuariosData = [];
    
    // ==================== CARGAR USUARIOS ====================
    
    function cargarUsuarios() {
        $('#contenedor_usuarios').html(`
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <p class="mt-2">Cargando usuarios...</p>
            </div>
        `);
        
        $.ajax({
            url: APP_URL + '/api/administradores/listar-usuarios',
            type: 'POST',
            data: {
                busqueda: busquedaActual,
                rol: rolActual !== 'todos' ? rolActual : '',
                estado: estadoActual !== 'todos' ? estadoActual : ''
            },
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Usuarios recibidos:', response);
                
                var usuarios = [];
                if (response.success && response.data) {
                    usuarios = response.data;
                } else if (Array.isArray(response)) {
                    usuarios = response;
                } else if (response.usuarios && Array.isArray(response.usuarios)) {
                    usuarios = response.usuarios;
                }
                
                usuariosData = usuarios;
                actualizarEstadisticas(usuarios);
                renderizarUsuarios(usuarios);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar usuarios:', error);
                $('#contenedor_usuarios').html(`
                    <div class="col-12 text-center">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar los usuarios: ${error}
                        </div>
                    </div>
                `);
            }
        });
    }
    
    function actualizarEstadisticas(usuarios) {
        let total = usuarios.length;
        let pacientes = usuarios.filter(u => u.tipo === 'paciente').length;
        let medicos = usuarios.filter(u => u.tipo === 'medico').length;
        let asistentes = usuarios.filter(u => u.tipo === 'asistente').length;
        
        $('#total_usuarios').text(total);
        $('#total_pacientes').text(pacientes);
        $('#total_medicos').text(medicos);
        $('#total_asistentes').text(asistentes);
    }
    
    function renderizarUsuarios(usuarios) {
        let html = '';
        
        if (usuarios.length === 0) {
            html = `
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <p>No se encontraron usuarios</p>
                        <p class="text-muted small">Intente con otros criterios de búsqueda</p>
                    </div>
                </div>
            `;
        } else {
            for (let i = 0; i < usuarios.length; i++) {
                let user = usuarios[i];
                let rolClass = getRolClass(user.tipo);
                let rolIcono = getRolIcono(user.tipo);
                let estadoClass = user.activo == 1 ? 'badge-status-activo' : 'badge-status-inactivo';
                let estadoTexto = user.activo == 1 ? 'Activo' : 'Inactivo';
                let inicial = (user.nombre || 'U').charAt(0).toUpperCase();
                
                html += `
                    <div class="col-md-6 col-lg-4">
                        <div class="user-card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge-role ${rolClass}">
                                        <i class="fas ${rolIcono}"></i> ${user.tipo || 'Usuario'}
                                    </span>
                                    <span class="badge-status ${estadoClass}">
                                        <i class="fas ${user.activo == 1 ? 'fa-check-circle' : 'fa-ban'}"></i> ${estadoTexto}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-placeholder mr-3">
                                        ${inicial}
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name">${escapeHtml(user.nombre || '')} ${escapeHtml(user.apellidos || '')}</div>
                                        <div class="user-detail">
                                            <i class="fas fa-id-card"></i> ${user.cedula || 'N/A'}
                                        </div>
                                        <div class="user-detail">
                                            <i class="fas fa-envelope"></i> ${user.correo || 'N/A'}
                                        </div>
                                        <div class="user-detail">
                                            <i class="fas fa-phone"></i> ${user.telefono || 'N/A'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="action-buttons">
                                    <button class="btn btn-warning btn-sm-custom btn-editar" 
                                            data-id="${user.id}"
                                            data-rol="${user.tipo}"
                                            data-nombre="${escapeHtml(user.nombre)}"
                                            data-apellidos="${escapeHtml(user.apellidos)}"
                                            data-cedula="${user.cedula}"
                                            data-correo="${user.correo || ''}"
                                            data-telefono="${user.telefono || ''}"
                                            data-activo="${user.activo}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-danger btn-sm-custom btn-eliminar" 
                                            data-id="${user.id}"
                                            data-rol="${user.tipo}"
                                            data-nombre="${escapeHtml(user.nombre)} ${escapeHtml(user.apellidos)}">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }
        
        $('#contenedor_usuarios').html(html);
    }
    
    function getRolClass(rol) {
        const classes = {
            'paciente': 'badge-role-paciente',
            'medico': 'badge-role-medico',
            'asistente': 'badge-role-asistente',
            'administrador': 'badge-role-administrador'
        };
        return classes[rol] || 'badge-role-paciente';
    }
    
    function getRolIcono(rol) {
        const iconos = {
            'paciente': 'fa-user-injured',
            'medico': 'fa-user-md',
            'asistente': 'fa-user-nurse',
            'administrador': 'fa-user-shield'
        };
        return iconos[rol] || 'fa-user';
    }
    
    // ==================== EDITAR USUARIO ====================
    
    $(document).on('click', '.btn-editar', function() {
        let id = $(this).data('id');
        let rol = $(this).data('rol');
        let nombre = $(this).data('nombre');
        let apellidos = $(this).data('apellidos');
        let cedula = $(this).data('cedula');
        let correo = $(this).data('correo');
        let telefono = $(this).data('telefono');
        let activo = $(this).data('activo');
        
        $('#edit_usuario_id').val(id);
        $('#edit_usuario_rol').val(rol);
        $('#edit_nombre').val(nombre);
        $('#edit_apellidos').val(apellidos);
        $('#edit_cedula').val(cedula);
        $('#edit_correo').val(correo);
        $('#edit_telefono').val(telefono);
        $('#edit_estado_switch').prop('checked', activo == 1);
        $('#edit_estado_texto').text(activo == 1 ? 'Activo' : 'Inactivo');
        
        $('#modalEditarUsuario').modal('show');
    });
    
    $('#edit_estado_switch').change(function() {
        let texto = $(this).is(':checked') ? 'Activo' : 'Inactivo';
        $('#edit_estado_texto').text(texto);
    });
    
    $('#btnGuardarUsuario').click(function() {
        let id = $('#edit_usuario_id').val();
        let rol = $('#edit_usuario_rol').val();
        let correo = $('#edit_correo').val();
        let telefono = $('#edit_telefono').val();
        let estado = $('#edit_estado_switch').is(':checked') ? 'activo' : 'inactivo';
        
        let $btn = $(this);
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/administradores/editar-usuario',
            type: 'POST',
            data: {
                id_usuario: id,
                rol: rol,
                correo: correo,
                telefono: telefono,
                estado: estado,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    $('#modalEditarUsuario').modal('hide');
                    mostrarAlerta('Usuario actualizado correctamente', 'success');
                    cargarUsuarios();
                } else {
                    mostrarAlerta(response.message || 'Error al actualizar el usuario', 'error');
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr, status, error) {
                mostrarAlerta('Error de conexión: ' + status, 'error');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== ELIMINAR USUARIO ====================
    
    $(document).on('click', '.btn-eliminar', function() {
        let id = $(this).data('id');
        let rol = $(this).data('rol');
        let nombre = $(this).data('nombre');
        
        $('#eliminar_usuario_id').val(id);
        $('#eliminar_usuario_rol').val(rol);
        $('#eliminar_usuario_nombre').text(nombre);
        
        $('#modalEliminarUsuario').modal('show');
    });
    
    $('#confirmarEliminar').click(function() {
        let id = $('#eliminar_usuario_id').val();
        let rol = $('#eliminar_usuario_rol').val();
        
        let $btn = $(this);
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');
        
        $.ajax({
            url: APP_URL + '/api/administradores/eliminar-usuario',
            type: 'POST',
            data: {
                id_usuario: id,
                rol: rol,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    $('#modalEliminarUsuario').modal('hide');
                    mostrarAlerta('Usuario eliminado correctamente', 'success');
                    cargarUsuarios();
                } else {
                    mostrarAlerta(response.message || 'Error al eliminar el usuario', 'error');
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr, status, error) {
                mostrarAlerta('Error de conexión: ' + status, 'error');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== FILTROS ====================
    
    $('#buscar_usuario').on('keyup', function() {
        busquedaActual = $(this).val();
        filtrarUsuarios();
    });
    
    $('#filtro_rol').change(function() {
        rolActual = $(this).val();
        filtrarUsuarios();
    });
    
    $('#filtro_estado').change(function() {
        estadoActual = $(this).val();
        filtrarUsuarios();
    });
    
    function filtrarUsuarios() {
        let filtrados = usuariosData.filter(function(user) {
            // Filtro por búsqueda
            let textoBusqueda = (user.nombre + ' ' + (user.apellidos || '') + ' ' + user.cedula + ' ' + user.correo).toLowerCase();
            let coincideBusqueda = textoBusqueda.includes(busquedaActual.toLowerCase());
            
            // Filtro por rol
            let coincideRol = rolActual === 'todos' || user.tipo === rolActual;
            
            // Filtro por estado
            let userActivo = user.activo == 1;
            let coincideEstado = estadoActual === 'todos' || 
                                (estadoActual === 'activo' && userActivo) || 
                                (estadoActual === 'inactivo' && !userActivo);
            
            return coincideBusqueda && coincideRol && coincideEstado;
        });
        
        actualizarEstadisticas(filtrados);
        renderizarUsuarios(filtrados);
        
        if (busquedaActual.length > 0 || rolActual !== 'todos' || estadoActual !== 'todos') {
            let filtrosActivos = [];
            if (busquedaActual) filtrosActivos.push(`"${busquedaActual}"`);
            if (rolActual !== 'todos') filtrosActivos.push(`Rol: ${rolActual}`);
            if (estadoActual !== 'todos') filtrosActivos.push(`Estado: ${estadoActual}`);
            console.log(`Filtros aplicados: ${filtrosActivos.join(', ')} - Resultados: ${filtrados.length}`);
        }
    }
    
    $('#btnRefresh').click(function() {
        busquedaActual = '';
        rolActual = 'todos';
        estadoActual = 'todos';
        $('#buscar_usuario').val('');
        $('#filtro_rol').val('todos');
        $('#filtro_estado').val('todos');
        cargarUsuarios();
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
    cargarUsuarios();
});
</script>