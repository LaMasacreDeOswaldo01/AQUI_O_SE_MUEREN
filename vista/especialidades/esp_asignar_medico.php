<?php
<<<<<<< HEAD
// vista/especialidades/esp_asignar_medico.php
<<<<<<< HEAD
// Contenido principal para asignar médicos a especialidades
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
$id_especialidad = $id_especialidad ?? $_GET['id'] ?? 0;
?>

=======
$nombre_usuario = $nombre_usuario ?? 'Administrador';
$id_especialidad = $id_especialidad ?? $_GET['id'] ?? 0;
?>
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
<!-- CSS Adicional para esta vista -->
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .form-section h4 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--bv-primary);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--bv-border);
    }
    .form-section h4 i {
        margin-right: 0.5rem;
    }
    .info-especialidad {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .info-especialidad::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -5%;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .info-especialidad::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -5%;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .info-especialidad h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        position: relative;
        z-index: 1;
    }
    .info-especialidad p {
        opacity: 0.9;
        margin-bottom: 0;
        position: relative;
        z-index: 1;
    }
    .info-especialidad .badge {
        position: relative;
        z-index: 1;
        margin-top: 0.5rem;
        background: rgba(255,255,255,0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .medico-card {
        border-left: 3px solid var(--bv-primary);
        margin-bottom: 10px;
        transition: all 0.2s;
    }
    .medico-card:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    .alert-custom {
        border-radius: 12px;
        border: none;
        padding: 1rem;
    }
    .btn-asignar {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        border: none;
        border-radius: 12px;
        padding: 0.8rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-asignar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,119,182,0.3);
    }
    .btn-cancelar {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        border: none;
        border-radius: 12px;
        padding: 0.8rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-cancelar:hover {
        transform: translateY(-2px);
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
    .info-adicional {
        background: #f0fdf4;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .info-adicional i {
        color: #10b981;
        margin-right: 0.5rem;
    }
    .info-adicional p {
        font-size: 0.75rem;
        color: #065f46;
        margin-bottom: 0;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-md"></i> Asignar Médico</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_especialidad" value="<?php echo $id_especialidad; ?>">
        
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <!-- Información de la Especialidad -->
                <div class="info-especialidad" id="info_especialidad">
                    <div class="text-center">
                        <i class="fas fa-stethoscope fa-3x mb-2"></i>
                        <h3 id="especialidad_nombre">Cargando especialidad...</h3>
                        <p id="especialidad_descripcion" class="mb-2"></p>
                        <span class="badge" id="especialidad_badge">
                            <i class="fas fa-info-circle"></i> Especialidad Médica
                        </span>
                    </div>
                </div>

                <!-- Formulario Principal -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus"></i> Registrar Médico a Especialidad
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Alertas -->
                        <div class="alert alert-success alert-custom" id="alertExito" style="display:none;">
                            <i class="fas fa-check-circle"></i> <span id="exitoMensaje"></span>
                        </div>
                        <div class="alert alert-danger alert-custom" id="alertError" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                        </div>
                        <div class="alert alert-warning alert-custom" id="alertWarning" style="display:none;">
                            <i class="fas fa-exclamation-triangle"></i> <span id="warningMensaje"></span>
                        </div>
                        
                        <div id="loadingDatos" class="loading-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        
                        <form id="formAsignarMedico">
                            <?php echo Security::campoCSRF(); ?>
                            
                            <div class="form-section">
                                <h4><i class="fas fa-user-md"></i> Datos del Médico</h4>
                                
                                <div class="form-group">
                                    <label for="medico_seleccionado" class="required-field">Seleccionar Médico</label>
                                    <select class="form-control" id="medico_seleccionado" name="id_medico" required>
                                        <option value="">Seleccione un médico...</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> Solo se muestran médicos no asignados a esta especialidad
                                    </small>
                                </div>
                            </div>

                            <div class="form-section">
                                <h4><i class="fas fa-chart-line"></i> Datos Profesionales</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tarifa">Tarifa por Consulta ($)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" step="0.01" class="form-control" 
                                                       id="tarifa" name="tarifa" placeholder="0.00">
                                            </div>
                                            <small class="form-text text-muted">Monto base por consulta</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exp_anios">Años de Experiencia</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" 
                                                       id="exp_anios" name="exp_anios" placeholder="0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">años</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="extra">Costo Adicional ($)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" step="0.01" class="form-control" 
                                                       id="extra" name="extra" placeholder="0.00">
                                            </div>
                                            <small class="form-text text-muted">Por procedimientos especiales</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox mt-4 pt-2">
                                                <input type="checkbox" class="custom-control-input" id="domicilio" name="domicilio">
                                                <label class="custom-control-label" for="domicilio">
                                                    <i class="fas fa-home"></i> ¿Realiza consulta a domicilio?
                                                </label>
<<<<<<< HEAD
=======
if($_SESSION['us_tipo'] != 4 || $_SESSION['rol'] != 'administrador'){
    header('Location: ' . APP_URL . '/login/administrador');
    exit();
}

// RUTA CORREGIDA - Security.php está en la carpeta modelo a nivel de raíz
include_once dirname(__DIR__) . '/modelo/Security.php';

$nombre_usuario = $_SESSION['nombre_us'] ?? 'Administrador';
$id_especialidad = isset($_GET['id']) ? intval($_GET['id']) : 0;
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
    
    <title>Administrador | Asignar Médico</title>
    
    <style>
        .form-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .form-section h4 {
            color: var(--bv-primary);
            margin-bottom: 15px;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .info-especialidad {
            background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
            color: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
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
                    <h1><i class="fas fa-user-md"></i> Asignar Médico</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/administrador">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/especialidades">Especialidades</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/especialidades/detalle/<?php echo $id_especialidad; ?>">Detalle</a></li>
                        <li class="breadcrumb-item active">Asignar Médico</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <input type="hidden" id="id_especialidad" value="<?php echo $id_especialidad; ?>">
            
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <!-- Información de la Especialidad -->
                    <div class="info-especialidad" id="info_especialidad">
                        <div class="text-center">
                            <i class="fas fa-stethoscope fa-3x mb-2"></i>
                            <h3 id="especialidad_nombre">Cargando...</h3>
                            <p id="especialidad_descripcion" class="mb-0"></p>
                        </div>
                    </div>

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-plus"></i> Registrar Médico a Especialidad</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success" id="alertExito" style="display:none;">
                                <i class="fas fa-check-circle"></i> Médico asignado exitosamente
                            </div>
                            <div class="alert alert-danger" id="alertError" style="display:none;">
                                <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                            </div>
                            
                            <form id="formAsignarMedico">
                                <?php echo Security::campoCSRF(); ?>
                                
                                <div class="form-section">
                                    <h4><i class="fas fa-user-md"></i> Datos del Médico</h4>
                                    
                                    <div class="form-group">
                                        <label class="required-field">Seleccionar Médico</label>
                                        <select class="form-control" id="medico_seleccionado" name="id_medico" required>
                                            <option value="">Seleccione un médico...</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <h4><i class="fas fa-chart-line"></i> Datos Profesionales</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Tarifa ($)</label>
                                                <input type="number" step="0.01" class="form-control" id="tarifa" name="tarifa" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>EXP (AÑOS)</label>
                                                <input type="number" class="form-control" id="exp_anios" name="exp_anios" placeholder="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Extra ($)</label>
                                                <input type="number" step="0.01" class="form-control" id="extra" name="extra" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mt-4">
                                                <input type="checkbox" class="form-check-input" id="domicilio" name="domicilio">
                                                <label class="form-check-label" for="domicilio">¿Realiza consulta a domicilio?</label>
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                                            </div>
                                        </div>
                                    </div>
                                </div>
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-secondary btn-cancelar" id="btnCancelar">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary btn-asignar" id="btnAsignar">
                                    <i class="fas fa-save"></i> Asignar Médico
                                </button>
                            </div>
                        </form>
                        
                        <div class="info-adicional mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información adicional:</strong>
                            <p class="mt-1">Los datos profesionales ayudan a calcular tarifas y disponibilidad del médico para esta especialidad. Puede editarlos más tarde desde el detalle de la especialidad.</p>
<<<<<<< HEAD
=======
                                
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fas fa-save"></i> Asignar Médico
                                </button>
                                <a href="<?php echo APP_URL; ?>/especialidades/detalle/<?php echo $id_especialidad; ?>" class="btn btn-secondary btn-block mt-2">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </form>
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                        </div>
                    </div>
                </div>
            </div>
        </div>
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    </div>
</section>

<script>
$(document).ready(function() {
    console.log('=== FORMULARIO DE ASIGNACIÓN DE MÉDICO ===');
    console.log('ID Especialidad:', $('#id_especialidad').val());
    
    // ==================== CARGAR DATOS DE LA ESPECIALIDAD ====================
    
    function cargarEspecialidad() {
        let id = $('#id_especialidad').val();
        
        if (!id || id === '0') {
            mostrarError('ID de especialidad no válido');
            return;
        }
        
        $.ajax({
            url: APP_URL + '/api/especialidades/obtener-detalle',
            type: 'POST',
            data: { id_especialidad: id },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Datos de especialidad:', response);
                
                // Manejar formato ApiResponse
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                if (data.error) {
                    $('#especialidad_nombre').text('Error: ' + data.error);
                    return;
                }
                
                $('#especialidad_nombre').text(data.nombre);
                $('#especialidad_descripcion').text(data.descripcion || 'Sin descripción registrada');
                
                // Color dinámico para el badge
                let colorMap = {
                    'Azul Médico': '#007bff',
                    'Verde Salud': '#28a745',
                    'Rojo Urgencias': '#dc3545',
                    'Amarillo Precaución': '#ffc107',
                    'Púrpura Especial': '#6f42c1',
                    'Naranja': '#fd7e14'
                };
                let colorHex = colorMap[data.color] || '#007bff';
                $('#especialidad_badge').css('background-color', colorHex);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar especialidad:', error);
                $('#especialidad_nombre').text('Error al cargar datos');
                mostrarError('No se pudo cargar la información de la especialidad');
<<<<<<< HEAD
=======
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
    var id_especialidad = $('#id_especialidad').val();
    
    // Cargar información de la especialidad
    cargarEspecialidad();
    
    // Cargar lista de médicos disponibles
    cargarMedicosDisponibles();
    
    function cargarEspecialidad() {
        $.ajax({
            url: APP_URL + '/api/especialidades/obtener-detalle',
            type: 'POST',
            data: { id_especialidad: id_especialidad },
            dataType: 'json',
            success: function(data) {
                $('#especialidad_nombre').text(data.nombre);
                $('#especialidad_descripcion').text(data.descripcion || 'Sin descripción');
            },
            error: function() {
                $('#especialidad_nombre').text('Error al cargar especialidad');
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            }
        });
    }
    
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    // ==================== CARGAR MÉDICOS DISPONIBLES ====================
    
    function cargarMedicosDisponibles() {
        let id_especialidad = $('#id_especialidad').val();
        
        $('#medico_seleccionado').html('<option value="">Cargando médicos...</option>');
        
<<<<<<< HEAD
=======
    function cargarMedicosDisponibles() {
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        $.ajax({
            url: APP_URL + '/api/especialidades/listar-medicos',
            type: 'POST',
            data: { id_especialidad: id_especialidad },
            dataType: 'json',
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            timeout: 10000,
            success: function(response) {
                console.log('Médicos disponibles:', response);
                
                // Manejar formato ApiResponse
                var medicos = [];
                if (response.success && response.data) {
                    medicos = response.data;
                } else if (Array.isArray(response)) {
                    medicos = response;
                } else if (response.medicos && Array.isArray(response.medicos)) {
                    medicos = response.medicos;
                }
                
                let options = '<option value="">Seleccione un médico...</option>';
                
                if (medicos.length === 0) {
                    options = '<option value="">No hay médicos disponibles para asignar</option>';
                    $('#medico_seleccionado').prop('disabled', true);
                    mostrarWarning('No hay médicos disponibles para asignar a esta especialidad');
                } else {
                    $('#medico_seleccionado').prop('disabled', false);
                    for (let i = 0; i < medicos.length; i++) {
                        let med = medicos[i];
                        let infoExtra = med.mpps ? ` - MPPS: ${med.mpps}` : '';
                        options += `<option value="${med.id_medico}">
                            ${escapeHtml(med.nombre)} (Cédula: ${med.cedula})${infoExtra}
                        </option>`;
                    }
                }
                
                $('#medico_seleccionado').html(options);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar médicos:', error);
                $('#medico_seleccionado').html('<option value="">Error al cargar médicos</option>');
                mostrarError('Error al cargar la lista de médicos disponibles');
<<<<<<< HEAD
=======
            success: function(medicos) {
                var options = '<option value="">Seleccione un médico...</option>';
                for (var i = 0; i < medicos.length; i++) {
                    options += '<option value="' + medicos[i].id_medico + '">' + 
                               medicos[i].nombre + ' (Cédula: ' + medicos[i].cedula + ')' + 
                               (medicos[i].mpps ? ' - MPPS: ' + medicos[i].mpps : '') + 
                               '</option>';
                }
                $('#medico_seleccionado').html(options);
            },
            error: function() {
                $('#medico_seleccionado').html('<option value="">Error al cargar médicos</option>');
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            }
        });
    }
    
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    // ==================== ENVÍO DEL FORMULARIO ====================
    
    $('#formAsignarMedico').submit(function(e) {
        e.preventDefault();
        
        let id_especialidad = $('#id_especialidad').val();
        let id_medico = $('#medico_seleccionado').val();
        let tarifa = $('#tarifa').val() || 0;
        let exp_anios = $('#exp_anios').val() || 0;
        let extra = $('#extra').val() || 0;
        let domicilio = $('#domicilio').is(':checked') ? 1 : 0;
        
        // Validaciones
        if (!id_especialidad || id_especialidad === '0') {
            mostrarError('ID de especialidad no válido');
            return;
        }
        
        if (!id_medico) {
            mostrarError('Debe seleccionar un médico');
            $('#medico_seleccionado').focus();
            return;
        }
        
        // Validar valores numéricos
        if (tarifa < 0) {
            mostrarError('La tarifa no puede ser negativa');
            $('#tarifa').focus();
            return;
        }
        
        if (exp_anios < 0) {
            mostrarError('Los años de experiencia no pueden ser negativos');
            $('#exp_anios').focus();
            return;
        }
        
        if (extra < 0) {
            mostrarError('El costo adicional no puede ser negativo');
            $('#extra').focus();
            return;
        }
        
        let datos = {
            id_especialidad: id_especialidad,
            id_medico: id_medico,
            tarifa: tarifa,
            exp_anios: exp_anios,
            extra: extra,
            domicilio: domicilio,
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        console.log('Enviando datos de asignación:', datos);
        
        let $btn = $('#btnAsignar');
        let originalText = $btn.html();
<<<<<<< HEAD
=======
    // Enviar formulario
    $('#formAsignarMedico').submit(function(e) {
        e.preventDefault();
        
        var datos = {
            id_especialidad: id_especialidad,
            id_medico: $('#medico_seleccionado').val(),
            tarifa: $('#tarifa').val(),
            exp_anios: $('#exp_anios').val(),
            extra: $('#extra').val(),
            domicilio: $('#domicilio').is(':checked') ? 1 : 0,
            csrf_token: CSRF.getToken()
        };
        
        if (!datos.id_medico) {
            mostrarError('Debe seleccionar un médico');
            return;
        }
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Asignando...');
        
        $.ajax({
            url: APP_URL + '/api/especialidades/asignar-medico',
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
                
                if (response.resultado === 'asignado') {
                    mostrarExito('Médico asignado correctamente a la especialidad');
<<<<<<< HEAD
=======
            success: function(response) {
                if (response.resultado === 'asignado') {
                    $('#alertExito').show();
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    setTimeout(function() {
                        window.location.href = APP_URL + '/especialidades/detalle/' + id_especialidad;
                    }, 2000);
                } else if (response.resultado === 'ya_asignado') {
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    mostrarWarning('El médico ya está asignado a esta especialidad');
                    $btn.prop('disabled', false).html(originalText);
                } else if (response.resultado === 'error_csrf') {
                    mostrarError('Error de seguridad. Por favor, recargue la página.');
                    $btn.prop('disabled', false).html(originalText);
                } else {
                    let errorMsg = response.error || response.message || 'Error al asignar el médico';
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
                    mostrarError('El médico ya está asignado a esta especialidad');
                } else {
                    mostrarError('Error al asignar el médico');
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
    // ==================== BOTÓN CANCELAR ====================
    
    $('#btnCancelar').click(function() {
        let id = $('#id_especialidad').val();
        if (confirm('¿Está seguro que desea cancelar? Los datos no guardados se perderán.')) {
            window.location.href = APP_URL + '/especialidades/detalle/' + id;
        }
    });
    
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
    
    function mostrarWarning(mensaje) {
        $('#warningMensaje').text(mensaje);
        $('#alertWarning').fadeIn(300);
        setTimeout(function() {
            $('#alertWarning').fadeOut(500);
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
    cargarEspecialidad();
    cargarMedicosDisponibles();
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
