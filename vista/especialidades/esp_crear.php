<?php
<<<<<<< HEAD
<<<<<<< HEAD
// vista/especialidades/esp_crear.php
// Contenido principal para la creación de especialidades
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
?>

=======
$nombre_usuario = $nombre_usuario ?? 'Administrador';
?>
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
<!-- CSS Adicional para esta vista -->
<style>
    .preview-card {
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border-radius: 16px;
        border: 1px solid #eef2f6;
        transition: all 0.3s;
        position: sticky;
        top: 20px;
    }
    .preview-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .csrf-info {
        font-size: 12px;
        color: #6c757d;
        margin-top: 15px;
        text-align: center;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .color-preview {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 10px;
        vertical-align: middle;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .color-preview:hover {
        transform: scale(1.1);
    }
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .form-section h4 {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--bv-primary);
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .preview-nombre {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--bv-primary);
        word-break: break-word;
    }
    .preview-info {
        font-size: 0.8rem;
        color: var(--bv-text-light);
        margin-bottom: 0.5rem;
    }
    .badge-preview {
        background: #e8f4f8;
        color: #0d9488;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
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
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-plus-circle"></i> Crear Especialidad</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Formulario Principal -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> Información Básica
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Alertas -->
                        <div class="alert alert-success alert-dismissible fade show" id="alertExito" style="display:none;">
                            <i class="fas fa-check-circle"></i> <span id="exitoMensaje"></span>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        <div class="alert alert-danger alert-dismissible fade show" id="alertError" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        
                        <form id="formCrearEspecialidad">
                            <?php echo Security::campoCSRF(); ?>
                            
                            <div class="form-group">
                                <label for="nombre" class="required-field">Nombre de la Especialidad</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       placeholder="Ej: Cardiología, Pediatría, Radiología..." required>
                                <small class="form-text text-muted">Nombre descriptivo de la especialidad médica</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="codigo">Código Interno</label>
                                <input type="text" class="form-control" id="codigo" name="codigo" 
                                       placeholder="Ej: CARD-01, PED-01">
                                <small class="form-text text-muted">Código para identificación rápida (opcional)</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" 
                                          rows="3" placeholder="Ej: Especialidad dedicada al diagnóstico y tratamiento de enfermedades del corazón..."></textarea>
                            </div>

                            <div class="form-section">
                                <h4><i class="fas fa-cog"></i> Configuración de Atención</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="duracion_defecto" class="required-field">Duración por Defecto</label>
<<<<<<< HEAD
=======
if($_SESSION['us_tipo'] != 4 || $_SESSION['rol'] != 'administrador'){
    header('Location: ' . APP_URL . '/login/administrador');
    exit();
}

// RUTA CORRECTA - Sube dos niveles hasta la raíz del proyecto
include_once dirname(__DIR__, 2) . '/modelo/Security.php';

$nombre_usuario = $_SESSION['nombre_us'] ?? 'Administrador';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo APP_URL; ?>/js/config.js"></script>
    <script src="<?php echo APP_URL; ?>/js/csrf.js"></script>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <title>Administrador | Crear Especialidad</title>
    
    <style>
        .preview-card {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .csrf-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 10px;
            text-align: center;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
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
        <a href="<?php echo APP_URL; ?>/logout" class="btn btn-danger btn-sm">Cerrar sesión</a>
    </ul>
</nav>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo APP_URL; ?>/panel/administrador" class="brand-link">
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
                    <i class="fas fa-user-shield"></i> Usuario
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/perfil" class="nav-link">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Datos personales</p>
                    </a>
                </li>
                <li class="nav-header">
                    <i class="fas fa-chart-line"></i> Gestión
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/administrador/usuarios" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Usuarios</p>
                    </a>
                </li>      
                <li class="nav-header">
                    <i class="fas fa-hospital-user"></i> Clínica
                </li>
                 <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/especialidades" class="nav-link active">
                        <i class="nav-icon fas fa-stethoscope"></i>
                        <p>Especialidades</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/consultorios" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Consultorios</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/recetas" class="nav-link">
                        <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                        <p>Recetas</p>
                    </a>
                </li>
                <li class="nav-header">
                    <i class="fas fa-chart-bar"></i> Reportes
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Estadísticas</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-plus-circle"></i> Crear Especialidad</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/administrador">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/especialidades">Especialidades</a></li>
                        <li class="breadcrumb-item active">Crear</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Información Básica</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success" id="alertExito" style="display:none;">
                                <i class="fas fa-check-circle"></i> Especialidad creada exitosamente
                            </div>
                            <div class="alert alert-danger" id="alertError" style="display:none;">
                                <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                            </div>
                            
                            <form id="formCrearEspecialidad" method="POST">
                                <?php echo Security::campoCSRF(); ?>
                                
                                <div class="form-group">
                                    <label class="required-field">Nombre de la Especialidad</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Radiología">
                                </div>
                                
                                <div class="form-group">
                                    <label>Código</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Ej: RADI-01">
                                    <small class="form-text text-muted">Código interno para identificar el tipo de especialidad (opcional)</small>
                                </div>
                                
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Ej: Diagnósticos con imágenes (Rayos X)"></textarea>
                                </div>

                                <h4 class="mt-4">Configuración</h4>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Duración por Defecto (min)</label>
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                                            <select class="form-control" id="duracion_defecto" name="duracion_defecto">
                                                <option value="15">15 minutos</option>
                                                <option value="20">20 minutos</option>
                                                <option value="30" selected>30 minutos</option>
                                                <option value="45">45 minutos</option>
                                                <option value="60">60 minutos</option>
                                            </select>
<<<<<<< HEAD
<<<<<<< HEAD
                                            <small class="form-text text-muted">Tiempo estándar para cada cita</small>
=======
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                                            <small class="form-text text-muted">Tiempo estándar para cada cita</small>
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
<<<<<<< HEAD
<<<<<<< HEAD
                                            <label for="color">Color Identificador</label>
=======
                                            <label>Color Identificador</label>
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                                            <label for="color">Color Identificador</label>
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                                            <div class="input-group">
                                                <select class="form-control" id="color" name="color">
                                                    <option value="Azul Médico">Azul Médico</option>
                                                    <option value="Verde Salud">Verde Salud</option>
                                                    <option value="Rojo Urgencias">Rojo Urgencias</option>
                                                    <option value="Amarillo Precaución">Amarillo Precaución</option>
                                                    <option value="Púrpura Especial">Púrpura Especial</option>
                                                    <option value="Naranja">Naranja</option>
                                                </select>
                                                <span id="color_preview" class="color-preview" style="background-color: #007bff;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
<<<<<<< HEAD
<<<<<<< HEAD
                                            <label for="prioridad">Prioridad</label>
=======
                                            <label>Prioridad</label>
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                                            <label for="prioridad">Prioridad</label>
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                                            <select class="form-control" id="prioridad" name="prioridad">
                                                <option value="Baja">Baja</option>
                                                <option value="Media" selected>Media</option>
                                                <option value="Alta">Alta</option>
                                                <option value="Urgente">Urgente</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                                            <label for="orden_visualizacion">Orden de Visualización</label>
                                            <input type="number" class="form-control" id="orden_visualizacion" 
                                                   name="orden_visualizacion" value="0">
                                            <small class="form-text text-muted">Números más pequeños aparecen primero</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4><i class="fas fa-clipboard-list"></i> Información Adicional</h4>
                                
                                <div class="form-group">
                                    <label for="requisitos">Requisitos para Citas</label>
                                    <textarea class="form-control" id="requisitos" name="requisitos" 
                                              rows="3" placeholder="Ej: Ayuno de 8 horas, resultados de laboratorio previos, etc."></textarea>
                                    <small class="form-text text-muted">Indicaciones que debe cumplir el paciente antes de la cita</small>
                                </div>

                                <div class="form-group">
                                    <label for="observaciones">Observaciones Internas</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" 
                                              rows="3" placeholder="Notas internas sobre la especialidad..."></textarea>
                                    <small class="form-text text-muted">Información para uso interno del personal médico</small>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-secondary" id="btnCancelar">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnGuardar">
                                    <i class="fas fa-save"></i> Guardar Especialidad
                                </button>
                            </div>
                            
                            <div class="csrf-info">
                                <i class="fas fa-shield-alt"></i> Formulario protegido contra CSRF
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Vista Previa en Tiempo Real -->
                <div class="preview-card p-3">
                    <div class="text-center mb-3">
                        <i class="fas fa-stethoscope fa-2x" style="color: var(--bv-primary);"></i>
                        <h5 class="mt-2 mb-0">Vista Previa</h5>
                        <small class="text-muted">Así se verá en el catálogo</small>
                    </div>
                    <hr>
                    <div class="preview-nombre text-center" id="preview_nombre">
                        Nombre de la Especialidad
                    </div>
                    <div class="preview-info text-center mt-2" id="preview_descripcion">
                        <em class="text-muted">Sin descripción</em>
                    </div>
                    <hr>
                    <div class="preview-info">
                        <i class="fas fa-clock text-warning"></i>
                        <span id="preview_duracion">30 minutos</span>
                    </div>
                    <div class="preview-info">
                        <i class="fas fa-chart-line text-info"></i>
                        <span id="preview_prioridad">Media</span>
                    </div>
                    <div class="preview-info">
                        <i class="fas fa-palette"></i>
                        <span id="preview_color">Azul Médico</span>
                    </div>
                    <hr>
                    <div class="text-center">
                        <span class="badge-preview">
                            <i class="fas fa-check-circle"></i> Especialidad activa
                        </span>
                    </div>
                </div>

                <!-- Información de Ayuda -->
                <div class="info-card">
                    <h6><i class="fas fa-info-circle"></i> Información importante</h6>
                    <p><i class="fas fa-check-circle text-success"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios</p>
                    <p><i class="fas fa-clock"></i> La duración afecta la programación de citas</p>
                    <p><i class="fas fa-palette"></i> El color ayuda en la identificación visual</p>
                    <p><i class="fas fa-user-md"></i> Puede asignar médicos después de crear la especialidad</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    console.log('=== FORMULARIO DE CREACIÓN DE ESPECIALIDAD ===');
    
    // ==================== VISTA PREVIA EN TIEMPO REAL ====================
    
    function actualizarPreview() {
        // Nombre
        let nombre = $('#nombre').val();
        $('#preview_nombre').text(nombre || 'Nombre de la Especialidad');
        
        // Descripción
        let descripcion = $('#descripcion').val();
        if (descripcion) {
            $('#preview_descripcion').html(descripcion.length > 80 ? 
                descripcion.substring(0, 80) + '...' : descripcion);
        } else {
            $('#preview_descripcion').html('<em class="text-muted">Sin descripción</em>');
        }
        
        // Duración
        let duracion = $('#duracion_defecto').val();
        $('#preview_duracion').text(duracion + ' minutos');
        
        // Prioridad
        let prioridad = $('#prioridad').val();
        let prioridadBadge = getPrioridadBadge(prioridad);
        $('#preview_prioridad').html(prioridadBadge);
        
        // Color
        let color = $('#color').val();
        $('#preview_color').text(color);
    }
    
    function getPrioridadBadge(prioridad) {
        let color = '';
        switch(prioridad) {
            case 'Alta': color = '#fde68a'; break;
            case 'Media': color = '#dbeafe'; break;
            case 'Baja': color = '#e5e7eb'; break;
            case 'Urgente': color = '#fee2e2'; break;
            default: color = '#dbeafe';
        }
        return `<span style="background-color: ${color}; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem;">${prioridad}</span>`;
    }
    
    // Eventos para actualizar vista previa
    $('#nombre, #descripcion, #duracion_defecto, #prioridad, #color').on('input change', function() {
        actualizarPreview();
    });
    
    // Vista previa del color
    $('#color').on('change', function() {
        let colorMap = {
<<<<<<< HEAD
=======
                                            <label>Orden de visualización</label>
                                            <input type="number" class="form-control" id="orden_visualizacion" name="orden_visualizacion" value="0">
                                            <small class="form-text text-muted">Número más pequeño = aparece primero</small>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="mt-4">Información Adicional</h4>
                                <hr>

                                <div class="form-group">
                                    <label>Requisitos para Citas</label>
                                    <textarea class="form-control" id="requisitos" name="requisitos" rows="3" placeholder="Ej: Ayuno de 8 horas, resultados previos, etc."></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Observaciones</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="Notas internas sobre la especialidad..."></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fas fa-save"></i> Guardar Especialidad
                                </button>
                                
                                <div class="csrf-info mt-3">
                                    <i class="fas fa-shield-alt"></i> Formulario protegido contra CSRF
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-eye"></i> Vista Previa</h3>
                        </div>
                        <div class="card-body">
                            <div class="preview-card p-3">
                                <h4 id="preview_nombre">Nombre de Especialidad</h4>
                                <p id="preview_descripcion" class="text-muted small">Descripción de la especialidad</p>
                                <hr>
                                <div class="text-center">
                                    <span class="badge badge-primary" id="preview_duracion">30 min</span>
                                    <span class="badge badge-info" id="preview_prioridad">Media</span>
                                </div>
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
<script>
$(document).ready(function() {
    // Vista previa en tiempo real
    $('#nombre').on('input', function() {
        $('#preview_nombre').text($(this).val() || 'Nombre de Especialidad');
    });
    
    $('#descripcion').on('input', function() {
        $('#preview_descripcion').text($(this).val() || 'Descripción de la especialidad');
    });
    
    $('#duracion_defecto').on('change', function() {
        var val = $(this).val();
        $('#preview_duracion').text(val + ' min');
    });
    
    $('#prioridad').on('change', function() {
        var val = $(this).val();
        var badgeClass = 'badge-info';
        if (val === 'Alta') badgeClass = 'badge-danger';
        else if (val === 'Urgente') badgeClass = 'badge-warning';
        else if (val === 'Baja') badgeClass = 'badge-secondary';
        $('#preview_prioridad').removeClass().addClass('badge ' + badgeClass).text(val);
    });
    
    $('#color').on('change', function() {
        var colorMap = {
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            'Azul Médico': '#007bff',
            'Verde Salud': '#28a745',
            'Rojo Urgencias': '#dc3545',
            'Amarillo Precaución': '#ffc107',
            'Púrpura Especial': '#6f42c1',
            'Naranja': '#fd7e14'
        };
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        let colorHex = colorMap[$(this).val()] || '#007bff';
        $('#color_preview').css('background-color', colorHex);
        actualizarPreview();
    });
    
    // Inicializar vista previa
    actualizarPreview();
    $('#color').trigger('change');
    
    // ==================== BOTÓN CANCELAR ====================
    $('#btnCancelar').click(function() {
        if (confirm('¿Está seguro que desea cancelar? Los datos no guardados se perderán.')) {
            window.location.href = APP_URL + '/especialidades';
        }
    });
    
    // ==================== ENVÍO DEL FORMULARIO ====================
    $('#formCrearEspecialidad').submit(function(e) {
        e.preventDefault();
        
        // Validaciones
        let nombre = $('#nombre').val().trim();
        if (!nombre) {
            mostrarError('El nombre de la especialidad es requerido');
            $('#nombre').focus();
            return;
        }
        
        // Recopilar datos
        let datos = {
            nombre: nombre,
            descripcion: $('#descripcion').val().trim(),
            codigo: $('#codigo').val().trim(),
<<<<<<< HEAD
=======
        $('#color_preview').css('background-color', colorMap[$(this).val()] || '#007bff');
    });
    
    $('#color').trigger('change');
    
    // Enviar formulario
    $('#formCrearEspecialidad').submit(function(e) {
        e.preventDefault();
        
        var datos = {
            nombre: $('#nombre').val(),
            descripcion: $('#descripcion').val(),
            codigo: $('#codigo').val(),
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            duracion_defecto: $('#duracion_defecto').val(),
            color: $('#color').val(),
            prioridad: $('#prioridad').val(),
            orden_visualizacion: $('#orden_visualizacion').val(),
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            requisitos: $('#requisitos').val().trim(),
            observaciones: $('#observaciones').val().trim(),
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        console.log('Enviando datos:', datos);
        
        let $btn = $('#btnGuardar');
        let originalText = $btn.html();
<<<<<<< HEAD
=======
            requisitos: $('#requisitos').val(),
            observaciones: $('#observaciones').val(),
            csrf_token: CSRF.getToken()
        };
        
        if (!datos.nombre) {
            mostrarError('El nombre de la especialidad es requerido');
            return;
        }
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/especialidades/crear',
            type: 'POST',
            data: datos,
            dataType: 'json',
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.resultado === 'creado') {
                    mostrarExito('Especialidad creada exitosamente');
                    setTimeout(function() {
                        window.location.href = APP_URL + '/especialidades';
                    }, 2000);
                } else if (response.resultado === 'error_csrf') {
                    mostrarError('Error de seguridad. Por favor, recargue la página.');
                } else {
                    let errorMsg = response.error || response.message || 'Error al crear la especialidad';
                    mostrarError(errorMsg);
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                
                let errorMsg = 'Error de conexión: ' + status;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                mostrarError(errorMsg);
<<<<<<< HEAD
=======
            success: function(response) {
                if (response.resultado === 'creado') {
                    $('#alertExito').show();
                    setTimeout(function() {
                        window.location.href = APP_URL + '/especialidades';
                    }, 2000);
                } else {
                    mostrarError('Error al crear la especialidad: ' + (response.error || response.resultado));
                }
            },
            error: function(xhr) {
                mostrarError('Error de conexión: ' + xhr.status);
            },
            complete: function() {
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    // ==================== FUNCIONES DE NOTIFICACIÓN ====================
    
    function mostrarError(mensaje) {
        $('#errorMensaje').text(mensaje);
        $('#alertError').fadeIn(300);
        setTimeout(function() {
            $('#alertError').fadeOut(500);
        }, 5000);
        
        // Scroll al error
        $('html, body').animate({
            scrollTop: $('#alertError').offset().top - 100
        }, 500);
    }
    
    function mostrarExito(mensaje) {
        $('#exitoMensaje').text(mensaje);
        $('#alertExito').fadeIn(300);
        setTimeout(function() {
            $('#alertExito').fadeOut(500);
        }, 3000);
    }
});
<<<<<<< HEAD
</script>
=======
    function mostrarError(mensaje) {
        $('#errorMensaje').text(mensaje);
        $('#alertError').show();
        setTimeout(function() { $('#alertError').hide(); }, 4000);
    }
});
</script>

</body>
</html>
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
</script>
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
