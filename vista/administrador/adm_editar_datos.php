<?php
// vista/administrador/adm_editar_datos.php
// Contenido principal para la edición de perfil del administrador
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_administrador = $id_administrador ?? $_SESSION['usuario'] ?? 0;
$avatar_actual = $avatar_actual ?? (!empty($_SESSION['avatar']) ? $_SESSION['avatar'] : APP_URL . '/img/avatarDES.jpg');
?>

<!-- CSS Adicional para esta vista -->
<style>
    .profile-header {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .profile-header::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -5%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .profile-header::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -5%;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        object-fit: cover;
        transition: transform 0.3s;
    }
    .profile-avatar:hover {
        transform: scale(1.05);
    }
    .info-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 1.5rem;
    }
    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12);
    }
    .info-card .card-header {
        background: white;
        border-bottom: 2px solid var(--bv-primary);
        padding: 1rem 1.5rem;
    }
    .info-card .card-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
        color: var(--bv-dark);
    }
    .info-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #eef2f6;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: var(--bv-text-light);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-value {
        font-weight: 500;
        color: var(--bv-dark);
        margin-top: 0.25rem;
    }
    .btn-editar {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-editar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,119,182,0.3);
    }
    .form-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .form-card .card-header {
        background: white;
        border-bottom: 2px solid var(--bv-primary);
        padding: 1rem 1.5rem;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 0.6rem 1rem;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--bv-primary);
        box-shadow: 0 0 0 3px rgba(0,119,182,0.1);
    }
    .btn-guardar {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        border-radius: 10px;
        padding: 0.7rem 2rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-guardar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    }
    .alert-custom {
        border-radius: 12px;
        border: none;
        padding: 1rem;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .csrf-info {
        font-size: 0.75rem;
        color: #94a3b8;
        text-align: center;
        margin-top: 1rem;
    }
    .badge-role {
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .ubicacion-select {
        margin-bottom: 1rem;
    }
</style>

<!-- Content Wrapper Content -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-circle"></i> Mi Perfil</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_usuario" value="<?php echo htmlspecialchars($id_administrador); ?>">
        
        <div class="row">
            <!-- COLUMNA IZQUIERDA - PERFIL VISUAL -->
            <div class="col-md-4">
                <!-- Profile Header -->
                <div class="profile-header text-white">
                    <div class="text-center">
                        <img id="avatar2" src="<?php echo $avatar_actual; ?>" class="profile-avatar mb-3">
                        <h3 id="nombre_us" class="mb-0">Cargando...</h3>
                        <p id="apellidos_us" class="opacity-75 mb-2">Cargando...</p>
                        <span class="badge-role"><i class="fas fa-user-shield"></i> Administrador</span>
                        <div class="mt-2">
                            <button type="button" data-toggle="modal" data-target="#cambiophoto" class="btn btn-light btn-sm">
                                <i class="fas fa-camera"></i> Cambiar avatar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Información Personal Card -->
                <div class="info-card">
                    <div class="card-header">
                        <h3><i class="fas fa-id-card"></i> Información Personal</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-birthday-cake"></i> Edad</div>
                            <div class="info-value" id="edad">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-id-card"></i> Cédula</div>
                            <div class="info-value" id="cedula_us">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-user-tag"></i> Tipo de Usuario</div>
                            <div class="info-value">
                                <span class="badge bg-primary" style="background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));">
                                    Administrador
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-venus-mars"></i> Sexo</div>
                            <div class="info-value" id="sexo_us">-</div>
                        </div>
                    </div>
                </div>

                <!-- Contacto Card -->
                <div class="info-card">
                    <div class="card-header">
                        <h3><i class="fas fa-address-card"></i> Contacto</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-phone"></i> Teléfono</div>
                            <div class="info-value" id="telefono_us">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-envelope"></i> Correo Electrónico</div>
                            <div class="info-value" id="correo_us">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-map-marker-alt"></i> Dirección</div>
                            <div class="info-value" id="direccion_us">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-pencil-alt"></i> Información adicional</div>
                            <div class="info-value" id="adicional_us">-</div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent text-center">
                        <button class="edit btn btn-editar btn-sm w-100">
                            <i class="fas fa-edit"></i> Editar información
                        </button>
                        <button data-toggle="modal" data-target="#cambiocontra" type="button" class="btn btn-outline-warning btn-sm w-100 mt-2">
                            <i class="fas fa-key"></i> Cambiar contraseña
                        </button>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA - FORMULARIO DE EDICIÓN -->
            <div class="col-md-8">
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-edit"></i> Editar Datos Personales</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success alert-custom" id="editado" style="display:none;">
                            <i class="fas fa-check-circle"></i> Datos actualizados correctamente
                        </div>
                        <div class="alert alert-danger alert-custom" id="noeditado" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> Primero haga clic en "Editar información"
                        </div>
                        
                        <form id="form-usuario" class="form-horizontal">
                            <?php echo Security::campoCSRF(); ?>
                            
                            <div class="form-group">
                                <label for="telefono" class="required-field">Teléfono</label>
                                <input type="tel" id="telefono" class="form-control" placeholder="Ej: 04141234567" disabled>
                            </div>
                            
                            <!-- Sistema de Ubicación -->
                            <h5 class="mt-4 mb-3"><i class="fas fa-map-marker-alt text-primary"></i> Ubicación</h5>
                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado">Estado</label>
                                        <select class="form-control" id="estado" disabled>
                                            <option value="">Seleccione un estado...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ciudad">Ciudad</label>
                                        <select class="form-control" id="ciudad" disabled>
                                            <option value="">Seleccione un estado primero...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="municipio">Municipio</label>
                                        <select class="form-control" id="municipio" disabled>
                                            <option value="">Seleccione un estado primero...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parroquia">Parroquia</label>
                                        <select class="form-control" id="parroquia" disabled>
                                            <option value="">Seleccione un municipio primero...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="direccion_detallada">Dirección Detallada</label>
                                <input type="text" class="form-control" id="direccion_detallada" placeholder="Av. Principal, Edificio, Número, etc." disabled>
                                <small class="form-text text-muted">Ej: Av. Principal, Edificio Central, Casa #123</small>
                            </div>

                            <input type="hidden" id="direccion" name="direccion">
                            
                            <div class="form-group">
                                <label for="correo" class="required-field">Correo Electrónico</label>
                                <input type="email" id="correo" class="form-control" placeholder="ejemplo@correo.com" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label for="sexo">Sexo</label>
                                <select id="sexo" class="form-control" disabled>
                                    <option value="">Seleccione...</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="adicional">Información adicional</label>
                                <textarea class="form-control" id="adicional" rows="4" placeholder="Información adicional sobre el administrador..." disabled></textarea>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-guardar" disabled>
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="csrf-info">
                            <i class="fas fa-shield-alt"></i> Todos los cambios están protegidos contra falsificación de solicitudes (CSRF)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="cambiocontra" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent)); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title"><i class="fas fa-key"></i> Cambiar contraseña</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="avatar3" src="<?php echo $avatar_actual; ?>" class="profile-avatar" style="width: 80px; height: 80px;">
                    <h5 class="mt-2"><?php echo htmlspecialchars($nombre_usuario); ?></h5>
                </div>
                <div class="alert alert-success alert-custom" id="update" style="display:none;">
                    <i class="fas fa-check-circle"></i> Contraseña actualizada correctamente
                </div>
                <div class="alert alert-danger alert-custom" id="noupdate" style="display:none;">
                    <i class="fas fa-exclamation-circle"></i> Contraseña actual incorrecta
                </div>
                <form id="form-pass">
                    <?php echo Security::campoCSRF(); ?>
                    <div class="form-group">
                        <label>Contraseña actual</label>
                        <input type="password" id="oldpass" class="form-control" placeholder="Ingrese su contraseña actual" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña nueva</label>
                        <input type="password" id="newpass" class="form-control" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Avatar -->
<div class="modal fade" id="cambiophoto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent)); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title"><i class="fas fa-camera"></i> Cambiar avatar</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="avatar_modal" src="<?php echo $avatar_actual; ?>" class="profile-avatar" style="width: 100px; height: 100px;">
                    <h5 class="mt-2"><?php echo htmlspecialchars($nombre_usuario); ?></h5>
                </div>
                <div class="alert alert-success alert-custom" id="edit" style="display:none;">
                    <i class="fas fa-check-circle"></i> Avatar actualizado correctamente
                </div>
                <div class="alert alert-danger alert-custom" id="noedit" style="display:none;">
                    <i class="fas fa-exclamation-circle"></i> Formato no admitido. Use JPG, PNG o GIF
                </div>
                <form id="form-photo" enctype="multipart/form-data">
                    <?php echo Security::campoCSRF(); ?>
                    <div class="form-group">
                        <label>Seleccionar imagen</label>
                        <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/gif" required>
                        <small class="text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo APP_URL; ?>/js/ubicacion.js"></script>

<script>
// Script de carga de datos del administrador - VERSIÓN CORREGIDA
$(document).ready(function() {
    var id_usuario = $('#id_usuario').val();
    var edit = false;
    
    console.log('=== INICIANDO CARGA DE PERFIL ===');
    console.log('ID Usuario:', id_usuario);
    console.log('APP_URL:', APP_URL);
    
    if (!id_usuario || id_usuario === '') {
        console.error('ERROR: ID de administrador no encontrado');
        $('#nombre_us').html('Error: Sesión no válida');
        return;
    }
    
    // ==================== FUNCIONES DE UBICACIÓN ====================
    
    function cargarDireccionEnCampos(direccion_completa) {
        console.log('Parseando dirección:', direccion_completa);
        
        if (!direccion_completa || direccion_completa === '-') {
            console.log('No hay dirección guardada');
            cargarEstados();
            return;
        }
        
        let direccion_detallada = '';
        let ubicacion = direccion_completa;
        
        if (direccion_completa.includes(' - ')) {
            let partes = direccion_completa.split(' - ');
            ubicacion = partes[0];
            direccion_detallada = partes.slice(1).join(' - ');
        }
        
        let ubicacion_partes = ubicacion.split(', ').filter(p => p.trim() !== '');
        console.log('Partes de ubicación:', ubicacion_partes);
        
        $('#direccion_detallada').val(direccion_detallada);
        
        if (ubicacion_partes.length >= 1 && ubicacion_partes[0]) {
            cargarEstadosConSeleccion(ubicacion_partes);
        } else {
            cargarEstados();
        }
    }
    
    function cargarEstadosConSeleccion(ubicacion_partes) {
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
                } else {
                    estados = response;
                }
                
                if (!Array.isArray(estados)) {
                    estados = [];
                }
                
                let options = '<option value="">Seleccione un estado...</option>';
                let estadoId = null;
                let estadoSeleccionado = ubicacion_partes[0] || '';
                
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
                    if (ubicacion_partes.length >= 2 && ubicacion_partes[1]) {
                        cargarCiudadesConSeleccion(estadoId, ubicacion_partes);
                    }
                    if (ubicacion_partes.length >= 3 && ubicacion_partes[2]) {
                        cargarMunicipiosConSeleccion(estadoId, ubicacion_partes);
                    }
                }
            },
            error: function() {
                cargarEstadosFallback();
            }
        });
    }
    
    function cargarCiudadesConSeleccion(id_estado, ubicacion_partes) {
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
                } else {
                    ciudades = response;
                }
                
                if (!Array.isArray(ciudades)) {
                    ciudades = [];
                }
                
                let options = '<option value="">Seleccione una ciudad...</option>';
                let ciudadId = null;
                let ciudadSeleccionada = ubicacion_partes[1] || '';
                
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
            error: function() {
                $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarMunicipiosConSeleccion(id_estado, ubicacion_partes) {
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
                } else {
                    municipios = response;
                }
                
                if (!Array.isArray(municipios)) {
                    municipios = [];
                }
                
                let options = '<option value="">Seleccione un municipio...</option>';
                let municipioId = null;
                let municipioSeleccionado = ubicacion_partes[2] || '';
                
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
                    if (ubicacion_partes.length >= 4 && ubicacion_partes[3]) {
                        cargarParroquiasConSeleccion(municipioId, ubicacion_partes);
                    }
                }
            },
            error: function() {
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarParroquiasConSeleccion(id_municipio, ubicacion_partes) {
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
                } else {
                    parroquias = response;
                }
                
                if (!Array.isArray(parroquias)) {
                    parroquias = [];
                }
                
                let options = '<option value="">Seleccione una parroquia...</option>';
                let parroquiaId = null;
                let parroquiaSeleccionada = ubicacion_partes[3] || '';
                
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
            error: function() {
                $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarEstados() {
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
            error: function() {
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
            success: function(response) {
                var ciudades = [];
                if (response.success && response.data) {
                    ciudades = response.data;
                } else if (Array.isArray(response)) {
                    ciudades = response;
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
            error: function() {
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
            success: function(response) {
                var municipios = [];
                if (response.success && response.data) {
                    municipios = response.data;
                } else if (Array.isArray(response)) {
                    municipios = response;
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
            error: function() {
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
            success: function(response) {
                var parroquias = [];
                if (response.success && response.data) {
                    parroquias = response.data;
                } else if (Array.isArray(response)) {
                    parroquias = response;
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
            error: function() {
                $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
            }
        });
    }
    
    // ==================== EVENTOS DE UBICACIÓN ====================
    $(document).on('change', '#estado', function() {
        let id_estado = $(this).val();
        if (id_estado) {
            cargarCiudades(id_estado);
            cargarMunicipios(id_estado);
        } else {
            $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });
    
    $(document).on('change', '#municipio', function() {
        let id_municipio = $(this).val();
        if (id_municipio) {
            cargarParroquias(id_municipio);
        } else {
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });
    
    // ==================== FUNCIÓN PRINCIPAL: CARGAR DATOS DEL ADMINISTRADOR ====================
    function cargarDatosAdministrador() {
        console.log('Cargando datos del administrador...');
        
        $.ajax({
            url: APP_URL + '/api/administradores/buscar',
            type: 'POST',
            data: { id_administrador: id_usuario, dato: id_usuario },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                var datos = response;
                if (response.data && response.success) {
                    datos = response.data;
                }
                
                if (datos.error) {
                    console.error('Error:', datos.error);
                    $('#nombre_us').html('Error: ' + datos.error);
                    return;
                }
                
                $('#nombre_us').html(datos.nombre || '');
                $('#apellidos_us').html(datos.apellidos || '');
                $('#edad').html(datos.fecha_nacimiento || '');
                $('#cedula_us').html(datos.cedula || '');
                $('#telefono_us').html(datos.telefono || '');
                $('#correo_us').html(datos.correo || '');
                $('#sexo_us').html(datos.sexo || '');
                $('#adicional_us').html(datos.adicional || '');
                
                if (datos.direccion) {
                    $('#direccion_us').html(datos.direccion);
                    cargarDireccionEnCampos(datos.direccion);
                } else {
                    $('#direccion_us').html('-');
                    cargarEstados();
                }
                
                if (datos.avatar) {
                    var avatarUrl = datos.avatar;
                    var timestamp = new Date().getTime();
                    avatarUrl = avatarUrl + '?t=' + timestamp;
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav').attr('src', avatarUrl);
                    console.log('Avatar actualizado:', avatarUrl);
                } else {
                    var defaultAvatar = APP_URL + '/img/avatarDES.jpg?t=' + new Date().getTime();
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav').attr('src', defaultAvatar);
                }
                
                console.log('Datos cargados correctamente');
            },
            error: function(xhr, status, error) {
                console.error('Error en la petición AJAX:', error);
                $('#nombre_us').html('Error de conexión: ' + status);
                cargarEstados();
            }
        });
    }
    
    // ==================== BOTÓN EDITAR ====================
    $(document).on('click', '.edit', function(e) {
        e.preventDefault();
        edit = true;
        
        console.log('Editando administrador ID:', id_usuario);
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        $.ajax({
            url: APP_URL + '/api/administradores/capturar-datos',
            type: 'POST',
            data: { id_administrador: id_usuario },
            dataType: 'json',
            success: function(response) {
                var datos = response;
                if (response.data && response.success) {
                    datos = response.data;
                }
                
                console.log('Datos a editar:', datos);
                
                if (datos.error) {
                    alert('Error: ' + datos.error);
                    return;
                }
                
                $('#telefono').val(datos.telefono || '');
                $('#correo').val(datos.correo || '');
                $('#sexo').val(datos.sexo || '');
                $('#adicional').val(datos.adicional || '');
                $('#direccion_detallada').val(datos.direccion_detallada || '');
                
                $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', false);
                
                $('.btn-outline-success')
                    .removeClass('btn-outline-success')
                    .addClass('btn-success')
                    .prop('disabled', false);
                
                if (datos.direccion && datos.direccion !== '-') {
                    cargarDireccionEnCampos(datos.direccion);
                } else {
                    cargarEstados();
                }
                
                $('#editado').show(1000);
                setTimeout(function() { $('#editado').hide(2000); }, 2000);
            },
            error: function(xhr, status, error) {
                console.error('Error al capturar datos:', error);
                alert('Error al cargar datos para edición: ' + status);
            },
            complete: function() {
                $btn.html(originalText);
            }
        });
    });
    
    // ==================== FORMULARIO DE EDICIÓN - GUARDAR CAMBIOS ====================
    $('#form-usuario').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        if (!edit) {
            alert('Primero haga clic en "Editar"');
            return false;
        }
        
        var estado_nombre = $('#estado option:selected').text();
        var ciudad_nombre = $('#ciudad option:selected').text();
        var municipio_nombre = $('#municipio option:selected').text();
        var parroquia_nombre = $('#parroquia option:selected').text();
        var direccion_detallada = $('#direccion_detallada').val();
        
        var direccion_completa = '';
        
        if (estado_nombre && estado_nombre !== 'Seleccione un estado...' && estado_nombre !== '') {
            direccion_completa = estado_nombre;
        }
        if (ciudad_nombre && ciudad_nombre !== 'Seleccione una ciudad...' && ciudad_nombre !== '' && ciudad_nombre !== 'Cargando ciudades...') {
            direccion_completa += (direccion_completa ? ', ' : '') + ciudad_nombre;
        }
        if (municipio_nombre && municipio_nombre !== 'Seleccione un municipio...' && municipio_nombre !== '' && municipio_nombre !== 'Cargando municipios...') {
            direccion_completa += (direccion_completa ? ', ' : '') + municipio_nombre;
        }
        if (parroquia_nombre && parroquia_nombre !== 'Seleccione una parroquia...' && parroquia_nombre !== '' && parroquia_nombre !== 'Cargando parroquias...') {
            direccion_completa += (direccion_completa ? ', ' : '') + parroquia_nombre;
        }
        if (direccion_detallada && direccion_detallada !== '') {
            direccion_completa += (direccion_completa ? ' - ' : '') + direccion_detallada;
        }
        
        var telefono = $('#telefono').val();
        var correo = $('#correo').val();
        var sexo = $('#sexo').val();
        var adicional = $('#adicional').val();
        
        console.log('Guardando datos:', {id_usuario, direccion_completa});
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/administradores/editar',
            type: 'POST',
            data: {
                id_administrador: id_usuario,
                telefono: telefono,
                direccion: direccion_completa,
                correo: correo,
                sexo: sexo,
                adicional: adicional
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editado').show(1000);
                    setTimeout(function() { 
                        $('#editado').hide(2000); 
                    }, 3000);
                    
                    edit = false;
                    $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
                    $('.btn-success')
                        .removeClass('btn-success')
                        .addClass('btn-outline-success')
                        .prop('disabled', true);
                    
                    cargarDatosAdministrador();
                    alert('¡Datos actualizados correctamente!');
                } else {
                    $('#noeditado').show(1000);
                    setTimeout(function() { $('#noeditado').hide(2000); }, 3000);
                    alert(response.error || 'Error al guardar los cambios');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
                $('#noeditado').show(1000);
                setTimeout(function() { $('#noeditado').hide(2000); }, 3000);
                alert('Error de conexión: ' + status);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
        
        return false;
    });
    
    // ==================== CAMBIAR CONTRASEÑA ====================
    $('#form-pass').submit(function(e) {
        e.preventDefault();
        
        var oldpass = $('#oldpass').val();
        var newpass = $('#newpass').val();
        
        if (newpass.length < 6) {
            alert('La nueva contraseña debe tener al menos 6 caracteres');
            return false;
        }
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: APP_URL + '/api/administradores/cambiar-password',
            type: 'POST',
            data: {
                id_administrador: id_usuario,
                oldpass: oldpass,
                newpass: newpass
            },
            dataType: 'json',
            success: function(response) {
                if (response.resultado === 'update') {
                    $('#update').show(1000);
                    setTimeout(function() { 
                        $('#update').hide(2000); 
                        $('#cambiocontra').modal('hide');
                    }, 2000);
                    $('#form-pass').trigger('reset');
                } else {
                    $('#noupdate').show(1000);
                    setTimeout(function() { $('#noupdate').hide(2000); }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar contraseña:', error);
                alert('Error de conexión: ' + status);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== CAMBIAR FOTO ====================
    $('#form-photo').submit(function(e) {
        e.preventDefault();
        
        var fileInput = $(this).find('input[type="file"]')[0];
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Por favor seleccione una imagen');
            return;
        }
        
        var formData = new FormData(this);
        formData.append('id_administrador', id_usuario);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
        
        $.ajax({
            url: APP_URL + '/api/administradores/cambiar-foto',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.alert === 'edit') {
                    var timestamp = new Date().getTime();
                    var nuevaRuta = response.ruta + '?t=' + timestamp;
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav').attr('src', nuevaRuta);
                    
                    $('#edit').show(1000);
                    setTimeout(function() { $('#edit').hide(2000); }, 3000);
                    $('#form-photo').trigger('reset');
                    setTimeout(function() { $('#cambiophoto').modal('hide'); }, 1500);
                } else {
                    $('#noedit').show(1000);
                    setTimeout(function() { $('#noedit').hide(2000); }, 3000);
                    alert(response.error || 'Error al cambiar la foto');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar foto:', error);
                $('#noedit').show(1000);
                setTimeout(function() { $('#noedit').hide(2000); }, 3000);
                alert('Error al cambiar la foto. Verifique el tipo de archivo (JPG, PNG, GIF)');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== INICIALIZAR ====================
    cargarDatosAdministrador();
});
</script>