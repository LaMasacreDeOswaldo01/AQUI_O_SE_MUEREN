<?php
<<<<<<< HEAD
<<<<<<< HEAD
// vista/asistente/asi_editar_datos.php
// Contenido principal para la edición de perfil del asistente
// Este archivo se renderiza dentro del layout base dashboard.php
=======
<<<<<<< HEAD
// vista/asistente/asi_editar_datos.php - CORREGIDO
// Usa rutas absolutas con las constantes definidas en index.php

// Verificar autenticación y rol
if($_SESSION['us_tipo'] != 3 || $_SESSION['rol'] != 'asistente'){
    header('Location: ' . APP_URL . '/login/asistente');
    exit();
}

$id_asistente = $_SESSION['usuario'];
$nombre_usuario = $_SESSION['nombre_us'];

// Incluir Security usando la constante MODEL_PATH
$securityPath = MODEL_PATH . '/Security.php';
if (!file_exists($securityPath)) {
    die("Error: No se encuentra Security.php en: " . $securityPath);
}
include_once $securityPath;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
        var ID_ASISTENTE = <?php echo json_encode($id_asistente); ?>;
        console.log('APP_URL:', APP_URL);
        console.log('ID_ASISTENTE:', ID_ASISTENTE);
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo APP_URL; ?>/js/config.js"></script>
    <script src="<?php echo APP_URL; ?>/js/csrf.js"></script>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <title>Asistente | Mi Perfil</title>
    
    <style>
        .profile-header {
            background: linear-gradient(135deg, #9333ea, #7c3aed);
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
            border-bottom: 2px solid #9333ea;
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
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-editar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(147,51,234,0.3);
        }
        .form-card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .form-card .card-header {
            background: white;
            border-bottom: 2px solid #9333ea;
            padding: 1rem 1.5rem;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            padding: 0.6rem 1rem;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #9333ea;
            box-shadow: 0 0 0 3px rgba(147,51,234,0.1);
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
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <a href="<?php echo APP_URL; ?>/logout" class="btn btn-danger btn-sm">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
    </ul>
</nav>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo APP_URL; ?>/panel/asistente" class="brand-link">
        <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">BioVital</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img id="avatar_nav" src="<?php echo APP_URL; ?>/img/avatar.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($nombre_usuario); ?></a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-header">
                    <i class="fas fa-user-nurse"></i> Usuario
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/perfil" class="nav-link active">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Datos personales</p>
                    </a>
                </li>
                <li class="nav-header">
                    <i class="fas fa-clinic-medical"></i> Clínica
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/recetas" class="nav-link">
                        <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                        <p>Recetas</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Modal Cambiar Contraseña -->
<div class="modal fade" id="cambiocontra" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #9333ea, #7c3aed); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title"><i class="fas fa-key"></i> Cambiar contraseña</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="avatar3" src="<?php echo APP_URL; ?>/img/avatar.png" class="profile-avatar" style="width: 80px; height: 80px;">
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
            <div class="modal-header" style="background: linear-gradient(135deg, #9333ea, #7c3aed); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title"><i class="fas fa-camera"></i> Cambiar avatar</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="avatar_modal" src="<?php echo APP_URL; ?>/img/avatarDES.jpg" class="profile-avatar" style="width: 100px; height: 100px;">
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

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-user-circle"></i> Mi Perfil</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/asistente">Home</a></li>
                        <li class="breadcrumb-item active">Mi Perfil</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <input type="hidden" id="id_usuario" value="<?php echo htmlspecialchars($id_asistente); ?>">
            
            <div class="row">
                <!-- COLUMNA IZQUIERDA - PERFIL VISUAL -->
                <div class="col-md-4">
                    <!-- Profile Header -->
                    <div class="profile-header text-white">
                        <div class="text-center">
                            <img id="avatar2" src="<?php echo APP_URL; ?>/img/avatarDES.jpg" class="profile-avatar mb-3">
                            <h3 id="nombre_us" class="mb-0">Cargando...</h3>
                            <p id="apellidos_us" class="opacity-75 mb-2">Cargando...</p>
                            <span class="badge-role"><i class="fas fa-user-nurse"></i> Asistente</span>
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
                                    <textarea class="form-control" id="adicional" rows="4" placeholder="Información adicional sobre el asistente..." disabled></textarea>
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
</div>

<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2024 BioVital.</strong> Todos los derechos reservados.
</footer>

</div>

<script src="<?php echo APP_URL; ?>/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo APP_URL; ?>/js/ubicacion.js"></script>

<script>
// Script de carga de datos del asistente - VERSIÓN CORREGIDA
$(document).ready(function() {
    var id_usuario = $('#id_usuario').val();
    var edit = false;
    
    console.log('=== INICIANDO CARGA DE PERFIL ASISTENTE ===');
    console.log('ID Usuario:', id_usuario);
    console.log('APP_URL:', APP_URL);
    
    if (!id_usuario || id_usuario === '') {
        console.error('ERROR: ID de asistente no encontrado');
        $('#nombre_us').html('Error: Sesión no válida');
        return;
    }
    
    // ==================== FUNCIONES DE UBICACIÓN ====================
    
    function cargarDireccionEnCampos(direccion_completa) {
        console.log('Parseando dirección:', direccion_completa);
        
        if (!direccion_completa || direccion_completa === '-' || direccion_completa === '') {
            console.log('No hay dirección guardada o está vacía');
            cargarEstados();
            return;
        }
        
        let direccion_detallada = '';
        let ubicacion = direccion_completa;
        
        // Separar dirección detallada de la ubicación si existe
        if (direccion_completa.includes(' - ')) {
            let partes = direccion_completa.split(' - ');
            ubicacion = partes[0];
            direccion_detallada = partes.slice(1).join(' - ');
        }
        
        // Dividir la ubicación por comas
        let ubicacion_partes = ubicacion.split(', ').filter(p => p.trim() !== '');
        console.log('Partes de ubicación:', ubicacion_partes);
        
        // Si no hay partes o solo hay una (como "platanal"), no intentar cargar selects con selección
        if (ubicacion_partes.length < 1) {
            console.log('Dirección no tiene formato de ubicación completo, cargando selects vacíos');
            cargarEstados();
            return;
        }
        
        $('#direccion_detallada').val(direccion_detallada);
        
        // Solo intentar cargar los selects si tenemos al menos el estado
        if (ubicacion_partes[0] && ubicacion_partes[0] !== '') {
            cargarEstadosConSeleccion(ubicacion_partes);
        } else {
            cargarEstados();
        }
    }
    
    function cargarEstadosConSeleccion(ubicacion_partes) {
        console.log('Cargando estados con selección:', ubicacion_partes);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta de estados:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
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
                    console.error('Estados no es un array:', estados);
                    estados = [];
                }
                
                console.log('Estados procesados:', estados.length);
                
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
                    // Solo cargar ciudad si tenemos al menos 2 partes
                    if (ubicacion_partes.length >= 2 && ubicacion_partes[1]) {
                        cargarCiudadesConSeleccion(estadoId, ubicacion_partes);
                    }
                    // Solo cargar municipio si tenemos al menos 3 partes
                    if (ubicacion_partes.length >= 3 && ubicacion_partes[2]) {
                        cargarMunicipiosConSeleccion(estadoId, ubicacion_partes);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estados:', error);
                cargarEstadosFallback();
            }
        });
    }
    
    function cargarCiudadesConSeleccion(id_estado, ubicacion_partes) {
        if (!id_estado) return;
        
        console.log('Cargando ciudades para estado:', id_estado);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/ciudades',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta ciudades:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
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
                    console.error('Ciudades no es un array:', ciudades);
                    ciudades = [];
                }
                
                let options = '<option value="">Seleccione una ciudad...</option>';
                let ciudadId = null;
                let ciudadSeleccionada = (ubicacion_partes.length >= 2 && ubicacion_partes[1]) ? ubicacion_partes[1] : '';
                
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
    
    function cargarMunicipiosConSeleccion(id_estado, ubicacion_partes) {
        if (!id_estado) return;
        
        console.log('Cargando municipios para estado:', id_estado);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/municipios',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta municipios:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
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
                    console.error('Municipios no es un array:', municipios);
                    municipios = [];
                }
                
                let options = '<option value="">Seleccione un municipio...</option>';
                let municipioId = null;
                let municipioSeleccionado = (ubicacion_partes.length >= 3 && ubicacion_partes[2]) ? ubicacion_partes[2] : '';
                
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
                    // Cargar parroquias solo si tenemos al menos 4 partes
                    if (ubicacion_partes.length >= 4 && ubicacion_partes[3]) {
                        cargarParroquiasConSeleccion(municipioId, ubicacion_partes);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar municipios:', error);
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarParroquiasConSeleccion(id_municipio, ubicacion_partes) {
        if (!id_municipio) return;
        
        console.log('Cargando parroquias para municipio:', id_municipio);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/parroquias',
            type: 'POST',
            data: { id_municipio: id_municipio },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta parroquias:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
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
                    console.error('Parroquias no es un array:', parroquias);
                    parroquias = [];
                }
                
                let options = '<option value="">Seleccione una parroquia...</option>';
                let parroquiaId = null;
                let parroquiaSeleccionada = (ubicacion_partes.length >= 4 && ubicacion_partes[3]) ? ubicacion_partes[3] : '';
                
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
    
    function cargarEstados() {
        console.log('Cargando lista de estados...');
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta estados (sin selección):', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
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
                    console.error('Estados no es un array:', estados);
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
        console.log('Usando fallback de estados');
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
    
    // ==================== FUNCIÓN PRINCIPAL: CARGAR DATOS DEL ASISTENTE ====================
    function cargarDatosAsistente() {
        console.log('Cargando datos del asistente...');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/buscar',
            type: 'POST',
            data: { dato: id_usuario, id_asistente: id_usuario },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                // Verificar si la respuesta tiene el formato ApiResponse
                var datos = response;
                if (response.data && response.success) {
                    datos = response.data;
                }
                
                if (datos.error) {
                    console.error('Error:', datos.error);
                    $('#nombre_us').html('Error: ' + datos.error);
                    return;
                }
                
                // Actualizar UI con los datos
                $('#nombre_us').html(datos.nombre || '');
                $('#apellidos_us').html(datos.apellidos || '');
                $('#edad').html(datos.fecha_nacimiento || '');
                $('#cedula_us').html(datos.cedula || '');
                $('#telefono_us').html(datos.telefono || '');
                $('#correo_us').html(datos.correo || '');
                $('#sexo_us').html(datos.sexo || '');
                $('#adicional_us').html(datos.adicional || '');
                
                // Mostrar dirección
                if (datos.direccion) {
                    $('#direccion_us').html(datos.direccion);
                    // Cargar dirección en los campos de edición
                    cargarDireccionEnCampos(datos.direccion);
                } else {
                    $('#direccion_us').html('-');
                    cargarEstados();
                }
                
                // Actualizar avatar
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
    $(document).on('click', '.edit, .btn-editar', function(e) {
        e.preventDefault();
        edit = true;
        
        console.log('Editando asistente ID:', id_usuario);
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/capturar-datos',
            type: 'POST',
            data: { id_asistente: id_usuario },
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
                
                // Cargar datos básicos en los campos
                $('#telefono').val(datos.telefono || '');
                $('#correo').val(datos.correo || '');
                $('#sexo').val(datos.sexo || '');
                $('#adicional').val(datos.adicional || '');
                $('#direccion_detallada').val(datos.direccion_detallada || '');
                
                // Habilitar campos de edición
                $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', false);
                
                // Cambiar estilo del botón guardar
                $('.btn-outline-success')
                    .removeClass('btn-outline-success')
                    .addClass('btn-success')
                    .prop('disabled', false);
                
                // Cargar dirección existente
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
        
        // Construir dirección completa
        var estado_nombre = $('#estado option:selected').text();
        var ciudad_nombre = $('#ciudad option:selected').text();
        var municipio_nombre = $('#municipio option:selected').text();
        var parroquia_nombre = $('#parroquia option:selected').text();
        var direccion_detallada = $('#direccion_detallada').val();
        
        console.log('Estado seleccionado:', estado_nombre);
        console.log('Ciudad seleccionada:', ciudad_nombre);
        console.log('Municipio seleccionado:', municipio_nombre);
        console.log('Parroquia seleccionada:', parroquia_nombre);
        console.log('Dirección detallada:', direccion_detallada);
        
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
        
        console.log('=== ENVIANDO DATOS ===');
        console.log('ID:', id_usuario);
        console.log('Dirección completa:', direccion_completa);
        console.log('Teléfono:', telefono);
        console.log('Correo:', correo);
        console.log('Sexo:', sexo);
        console.log('Adicional:', adicional);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/editar',
            type: 'POST',
            data: {
                id_asistente: id_usuario,
                telefono: telefono,
                direccion: direccion_completa,
                correo: correo,
                sexo: sexo,
                adicional: adicional
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success) {
                    $('#editado').show(1000);
                    setTimeout(function() { 
                        $('#editado').hide(2000); 
                    }, 3000);
                    
                    edit = false;
                    
                    // Deshabilitar campos después de guardar
                    $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
                    
                    // Restaurar estilo del botón guardar
                    $('.btn-success')
                        .removeClass('btn-success')
                        .addClass('btn-outline-success')
                        .prop('disabled', true);
                    
                    // Recargar datos del asistente
                    cargarDatosAsistente();
                    
                    alert('¡Datos actualizados correctamente!');
                } else {
                    $('#noeditado').show(1000);
                    setTimeout(function() { 
                        $('#noeditado').hide(2000); 
                    }, 3000);
                    alert(response.error || 'Error al guardar los cambios');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
                $('#noeditado').show(1000);
                setTimeout(function() { 
                    $('#noeditado').hide(2000); 
                }, 3000);
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
            url: APP_URL + '/api/asistentes/cambiar-password',
            type: 'POST',
            data: {
                id_asistente: id_usuario,
                oldpass: oldpass,
                newpass: newpass
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta cambio contraseña:', response);
                
                if (response.resultado === 'update') {
                    $('#update').show(1000);
                    setTimeout(function() { 
                        $('#update').hide(2000); 
                        $('#cambiocontra').modal('hide');
                    }, 2000);
                    $('#form-pass').trigger('reset');
                } else {
                    $('#noupdate').show(1000);
                    setTimeout(function() { 
                        $('#noupdate').hide(2000); 
                    }, 2000);
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
        formData.append('id_asistente', id_usuario);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/cambiar-foto',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta cambio foto:', response);
                
                if (response.alert === 'edit') {
                    var timestamp = new Date().getTime();
                    var nuevaRuta = response.ruta + '?t=' + timestamp;
                    
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav').attr('src', nuevaRuta);
                    
                    $('#edit').show(1000);
                    setTimeout(function() { 
                        $('#edit').hide(2000); 
                    }, 3000);
                    
                    $('#form-photo').trigger('reset');
                    
                    setTimeout(function() { 
                        $('#cambiophoto').modal('hide'); 
                    }, 1500);
                    
                } else {
                    $('#noedit').show(1000);
                    setTimeout(function() { 
                        $('#noedit').hide(2000); 
                    }, 3000);
                    alert(response.error || 'Error al cambiar la foto');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar foto:', error);
                $('#noedit').show(1000);
                setTimeout(function() { 
                    $('#noedit').hide(2000); 
                }, 3000);
                alert('Error al cambiar la foto. Verifique el tipo de archivo (JPG, PNG, GIF)');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== INICIALIZAR ====================
    cargarDatosAsistente();
});
</script>

</body>
</html>
=======
if($_SESSION['us_tipo'] == 3 && $_SESSION['rol'] == 'asistente'){
    // Incluir clase de seguridad para CSRF
    include_once '../../modelo/Security.php';
    
    include_once '../layouts/header.php';
?>
<title>Asistente | Editar datos</title>
<?php include_once '../layouts/nav_asistente.php'; ?>
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852

// Los datos vienen del controlador a través de $data
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_asistente = $id_asistente ?? $_SESSION['usuario'] ?? 0;
$avatar_actual = $avatar_actual ?? (!empty($_SESSION['avatar']) ? $_SESSION['avatar'] : APP_URL . '/img/avatarDES.jpg');
?>
<<<<<<< HEAD

=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
<!-- CSS Adicional para esta vista -->
<style>
    .profile-header {
        background: linear-gradient(135deg, #9333ea, #7c3aed);
        border-radius: 20px;
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
        width: 130px;
        height: 130px;
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
        border-bottom: 2px solid #9333ea;
        padding: 1rem 1.5rem;
    }
    .info-card .card-header h3 {
        font-size: 1rem;
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
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-value {
        font-weight: 500;
        color: var(--bv-dark);
        margin-top: 0.25rem;
        font-size: 0.9rem;
    }
    .btn-editar {
        background: linear-gradient(135deg, #9333ea, #7c3aed);
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-editar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(147,51,234,0.3);
    }
    .form-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .form-card .card-header {
        background: white;
        border-bottom: 2px solid #9333ea;
        padding: 1rem 1.5rem;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 0.6rem 1rem;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #9333ea;
        box-shadow: 0 0 0 3px rgba(147,51,234,0.1);
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
        font-size: 0.7rem;
        color: #94a3b8;
        text-align: center;
        margin-top: 1rem;
    }
    .badge-role {
        background: rgba(255,255,255,0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
    }
    .avatar-container {
        position: relative;
        display: inline-block;
    }
    .avatar-edit-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .avatar-edit-btn:hover {
        transform: scale(1.1);
        background: #9333ea;
        color: white;
    }
    .stats-mini {
        display: flex;
        justify-content: space-around;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.2);
    }
    .stats-mini-item {
        text-align: center;
    }
    .stats-mini-number {
        font-size: 1.2rem;
        font-weight: 700;
    }
    .stats-mini-label {
        font-size: 0.65rem;
        opacity: 0.8;
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
    .card {
        position: relative;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-nurse"></i> Mi Perfil</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_usuario" value="<?php echo htmlspecialchars($id_asistente); ?>">
        
        <div class="row">
            <!-- COLUMNA IZQUIERDA - PERFIL VISUAL -->
            <div class="col-md-4">
                <!-- Profile Header -->
                <div class="profile-header text-white">
                    <div class="text-center">
                        <div class="avatar-container">
                            <img id="avatar2" src="<?php echo $avatar_actual; ?>" class="profile-avatar mb-3">
                            <div class="avatar-edit-btn" data-toggle="modal" data-target="#cambiophoto">
                                <i class="fas fa-camera fa-sm"></i>
                            </div>
                        </div>
                        <h3 id="nombre_us" class="mb-0 mt-2">Cargando...</h3>
                        <p id="apellidos_us" class="opacity-75 mb-2">Cargando...</p>
                        <span class="badge-role">
                            <i class="fas fa-user-nurse"></i> Asistente
                        </span>
                    </div>
                    <div class="stats-mini">
                        <div class="stats-mini-item">
                            <div class="stats-mini-number" id="mini_recetas">0</div>
                            <div class="stats-mini-label">Recetas</div>
                        </div>
                        <div class="stats-mini-item">
                            <div class="stats-mini-number" id="mini_pacientes">0</div>
                            <div class="stats-mini-label">Pacientes</div>
                        </div>
                        <div class="stats-mini-item">
                            <div class="stats-mini-number" id="mini_medicos">0</div>
                            <div class="stats-mini-label">Médicos</div>
                        </div>
                    </div>
                </div>

                <!-- Información Personal Card -->
                <div class="info-card">
                    <div class="card-header">
                        <h3><i class="fas fa-id-card me-2"></i> Información Personal</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-birthday-cake me-1"></i> Edad</div>
                            <div class="info-value" id="edad">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-id-card me-1"></i> Cédula</div>
                            <div class="info-value" id="cedula_us">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-venus-mars me-1"></i> Sexo</div>
                            <div class="info-value" id="sexo_us">-</div>
                        </div>
                    </div>
                </div>

                <!-- Contacto Card -->
                <div class="info-card">
                    <div class="card-header">
                        <h3><i class="fas fa-address-card me-2"></i> Contacto</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-phone me-1"></i> Teléfono</div>
                            <div class="info-value" id="telefono_us">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-envelope me-1"></i> Correo Electrónico</div>
                            <div class="info-value" id="correo_us">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-map-marker-alt me-1"></i> Dirección</div>
                            <div class="info-value" id="direccion_us">-</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-pencil-alt me-1"></i> Información adicional</div>
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
                        <h3><i class="fas fa-user-edit me-2"></i> Editar Datos Personales</h3>
                    </div>
                    <div class="card-body">
                        <!-- Alertas -->
                        <div class="alert alert-success alert-custom" id="editado" style="display:none;">
                            <i class="fas fa-check-circle"></i> Datos actualizados correctamente
                        </div>
                        <div class="alert alert-danger alert-custom" id="noeditado" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> Primero haga clic en "Editar información"
                        </div>
                        <div class="alert alert-danger alert-custom" id="alertError" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                        </div>
                        
                        <div id="loadingDatos" class="loading-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        
                        <form id="form-usuario" class="form-horizontal">
                            <?php echo Security::campoCSRF(); ?>
                            
                            <div class="form-group">
                                <label for="telefono" class="required-field">Teléfono</label>
                                <input type="tel" id="telefono" class="form-control" 
                                       placeholder="Ej: 04141234567" disabled>
                                <small class="form-text text-muted">Número de contacto</small>
                            </div>
                            
                            <!-- Sistema de Ubicación -->
                            <h5 class="mt-3 mb-2"><i class="fas fa-map-marker-alt text-primary"></i> Ubicación</h5>
                            <hr class="mt-0">

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
                                <input type="text" class="form-control" id="direccion_detallada" 
                                       placeholder="Av. Principal, Edificio, Número, etc." disabled>
                                <small class="form-text text-muted">Ej: Av. Principal, Edificio Central, Piso 3</small>
                            </div>

                            <input type="hidden" id="direccion" name="direccion">
                            
                            <div class="form-group">
                                <label for="correo" class="required-field">Correo Electrónico</label>
                                <input type="email" id="correo" class="form-control" 
                                       placeholder="ejemplo@correo.com" disabled>
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
                                <textarea class="form-control" id="adicional" rows="3" 
                                          placeholder="Información adicional sobre el asistente..." disabled></textarea>
                                <small class="form-text text-muted">Comparte información relevante sobre tu perfil</small>
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
            <div class="modal-header" style="background: linear-gradient(135deg, #9333ea, #7c3aed); color: white; border-radius: 16px 16px 0 0;">
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
            <div class="modal-header" style="background: linear-gradient(135deg, #9333ea, #7c3aed); color: white; border-radius: 16px 16px 0 0;">
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

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
<script src="<?php echo APP_URL; ?>/js/ubicacion.js"></script>

<script>
$(document).ready(function() {
    var id_usuario = $('#id_usuario').val();
    var edit = false;
    
    console.log('=== PERFIL ASISTENTE - INICIALIZANDO ===');
    console.log('ID Usuario:', id_usuario);
    console.log('APP_URL:', APP_URL);
    
    if (!id_usuario || id_usuario === '') {
        console.error('ERROR: ID de asistente no encontrado');
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
    
    // ==================== FUNCIÓN PRINCIPAL: CARGAR DATOS DEL ASISTENTE ====================
    function cargarDatosAsistente() {
        console.log('Cargando datos del asistente...');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/buscar',
            type: 'POST',
            data: { id_asistente: id_usuario, dato: id_usuario },
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
                
                // Actualizar UI con los datos
                $('#nombre_us').html(datos.nombre || '');
                $('#apellidos_us').html(datos.apellidos || '');
                $('#edad').html(datos.fecha_nacimiento || '');
                $('#cedula_us').html(datos.cedula || '');
                $('#telefono_us').html(datos.telefono || '');
                $('#correo_us').html(datos.correo || '');
                $('#sexo_us').html(datos.sexo || '');
                $('#adicional_us').html(datos.adicional || '');
                
                // Mini stats
                $('#mini_recetas').text(datos.total_recetas || 0);
                $('#mini_pacientes').text(datos.total_pacientes || 0);
                $('#mini_medicos').text(datos.total_medicos || 0);
                
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
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav, #avatar_modal').attr('src', avatarUrl);
                    console.log('Avatar actualizado:', avatarUrl);
                } else {
                    var defaultAvatar = APP_URL + '/img/avatarDES.jpg?t=' + new Date().getTime();
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav, #avatar_modal').attr('src', defaultAvatar);
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
        
        console.log('Editando asistente ID:', id_usuario);
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/capturar-datos',
            type: 'POST',
            data: { id_asistente: id_usuario },
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
        
        // Construir dirección completa
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
            url: APP_URL + '/api/asistentes/editar',
            type: 'POST',
            data: {
                id_asistente: id_usuario,
                telefono: telefono,
                direccion: direccion_completa,
                correo: correo,
                sexo: sexo,
                adicional: adicional
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success) {
                    $('#editado').show(1000);
                    setTimeout(function() { 
                        $('#editado').hide(2000); 
                    }, 3000);
                    
                    edit = false;
                    
                    // Deshabilitar campos después de guardar
                    $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
                    
                    // Restaurar estilo del botón guardar
                    $('.btn-success')
                        .removeClass('btn-success')
                        .addClass('btn-outline-success')
                        .prop('disabled', true);
                    
                    // Recargar datos del asistente
                    cargarDatosAsistente();
                    
                    alert('¡Datos actualizados correctamente!');
                } else {
                    $('#noeditado').show(1000);
                    setTimeout(function() { 
                        $('#noeditado').hide(2000); 
                    }, 3000);
                    alert(response.error || 'Error al guardar los cambios');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
                $('#noeditado').show(1000);
                setTimeout(function() { 
                    $('#noeditado').hide(2000); 
                }, 3000);
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
            url: APP_URL + '/api/asistentes/cambiar-password',
            type: 'POST',
            data: {
                id_asistente: id_usuario,
                oldpass: oldpass,
                newpass: newpass,
                csrf_token: $('input[name="csrf_token"]').val()
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
                $('#noupdate').show(1000);
                setTimeout(function() { $('#noupdate').hide(2000); }, 2000);
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
        formData.append('id_asistente', id_usuario);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/cambiar-foto',
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
                    
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav, #avatar_modal').attr('src', nuevaRuta);
                    
                    $('#edit').show(1000);
                    setTimeout(function() { 
                        $('#edit').hide(2000); 
                    }, 3000);
                    
                    $('#form-photo').trigger('reset');
                    
                    setTimeout(function() { 
                        $('#cambiophoto').modal('hide'); 
                    }, 1500);
                    
                } else {
                    $('#noedit').show(1000);
                    setTimeout(function() { 
                        $('#noedit').hide(2000); 
                    }, 3000);
                    alert(response.error || 'Error al cambiar la foto');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar foto:', error);
                $('#noedit').show(1000);
                setTimeout(function() { 
                    $('#noedit').hide(2000); 
                }, 3000);
                alert('Error al cambiar la foto. Verifique el tipo de archivo (JPG, PNG, GIF)');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== INICIALIZAR ====================
    cargarDatosAsistente();
});
<<<<<<< HEAD
</script>
=======
<?php
include_once '../layouts/footer.php';
}
else{
    header('Location: ../login_asistente.php');
}
?>
<script src="../../js/asistente.js"></script>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
</script>
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
