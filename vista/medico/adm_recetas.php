<?php
<<<<<<< HEAD
// vista/medico/adm_recetas.php
// Vista de recetas para el rol de médico - VERSIÓN CORREGIDA
// Incluye búsqueda de pacientes funcional

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
    .table-actions {
        white-space: nowrap;
        width: 120px;
    }
    .btn-action {
        padding: 5px 10px;
        margin: 0 2px;
    }
    .modal-lg-custom {
        max-width: 800px;
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
    .prescription-icon {
        font-size: 2rem;
        color: #0d9488;
        margin-right: 0.5rem;
    }
    .badge-receta-tipo {
        font-size: 0.65rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
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
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    
    /* Estilos para el buscador de pacientes */
    .resultados-pacientes {
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-top: 2px;
    }
    .resultados-pacientes .list-group-item {
        cursor: pointer;
        transition: background 0.2s;
        border: none;
        border-bottom: 1px solid #eef2f6;
        padding: 10px 12px;
    }
    .resultados-pacientes .list-group-item:hover {
        background-color: #f0fdf4;
    }
    .resultados-pacientes .list-group-item.disabled {
        cursor: default;
        color: #94a3b8;
        text-align: center;
    }
    .resultados-pacientes .list-group-item.disabled:hover {
        background-color: transparent;
    }
    .position-relative {
        position: relative;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-prescription-bottle-alt"></i> Mis Recetas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/medico">Inicio</a></li>
                    <li class="breadcrumb-item active">Recetas</li>
                </ol>
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
                        <i class="fas fa-prescription me-2"></i> 
                        Recetario Electrónico
                    </h2>
                    <p class="mb-0 opacity-75">Gestiona las recetas médicas de tus pacientes de forma digital.</p>
                    <div class="mt-2">
                        <span class="badge bg-white text-dark px-3 py-1 rounded-pill">
                            <i class="fas fa-user-md me-1"></i> Dr(a). <?php echo htmlspecialchars($nombre_usuario); ?>
                        </span>
                    </div>
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
                    <div class="stats-number" id="total_recetas">0</div>
                    <div class="stats-label">Mis Recetas</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-2">
                    <div class="stats-number" id="total_pacientes">0</div>
                    <div class="stats-label">Pacientes Atendidos</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-3">
                    <div class="stats-number" id="recetas_mes">0</div>
                    <div class="stats-label">Recetas este Mes</div>
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
                                <input type="text" id="buscar_receta" class="form-control" 
                                       placeholder="Buscar por medicamento, paciente o fecha...">
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
                        <div class="col-md-2">
                            <select class="form-control" id="filtro_orden">
                                <option value="fecha_desc">Más recientes</option>
                                <option value="fecha_asc">Más antiguas</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="filter-card p-3 text-right">
                    <button class="btn btn-primary btn-sm" id="btnNuevaReceta">
                        <i class="fas fa-plus"></i> Nueva Receta
                    </button>
                    <button class="btn btn-secondary btn-sm ml-2" id="btnRefresh">
                        <i class="fas fa-sync-alt"></i> Actualizar
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
                        <div id="loadingRecetas" class="loading-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Medicamento</th>
                                    <th>Marca</th>
                                    <th>Cantidad</th>
                                    <th>Dosis</th>
                                    <th>Paciente</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_recetas">
                                <tr><td colspan="8" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando recetas...</p>
                                 </td
                                <tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_info" id="info_recetas">
                                    Mostrando <span id="desde">0</span> a <span id="hasta">0</span> de <span id="total_registros">0</span> recetas
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-end pagination-custom" id="paginacion">
                                    </ul>
                                </nav>
=======
// NO iniciar sesión aquí - el Front Controller ya lo hace
if($_SESSION['us_tipo'] != 2 || $_SESSION['rol'] != 'medico'){
    header('Location: ' . APP_URL . '/login/medico');
    exit();
}

$nombre_usuario = $_SESSION['nombre_us'] ?? 'Usuario';
$id_medico = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
        var ID_MEDICO = <?php echo json_encode($id_medico); ?>;
        console.log('APP_URL:', APP_URL);
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo APP_URL; ?>/js/config.js"></script>
    <script src="<?php echo APP_URL; ?>/js/csrf.js"></script>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <title>Médico | Recetas</title>
    
    <style>
        .table-actions {
            white-space: nowrap;
            width: 120px;
        }
        .btn-action {
            padding: 5px 10px;
            margin: 0 2px;
        }
        .modal-lg-custom {
            max-width: 800px;
        }
        .receta-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .search-box {
            max-width: 300px;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
        }
        .btn-ver-detalle {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
        }
        .btn-ver-detalle:hover {
            color: #0056b3;
        }
        .prescription-icon {
            font-size: 2.5rem;
            color: var(--bv-primary);
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
    <a href="<?php echo APP_URL; ?>/panel/medico" class="brand-link">
        <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">BioVital</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img id="avatar4" src="<?php echo APP_URL; ?>/img/avatar.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($nombre_usuario); ?></a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-header">
                    <i class="fas fa-user-md"></i> Usuario
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/perfil" class="nav-link">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Datos personales</p>
                    </a>
                </li>
                <li class="nav-header">
                    <i class="fas fa-clinic-medical"></i> Clínica
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/recetas" class="nav-link active">
                        <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                        <p>Recetas</p>
                    </a>
                </li>
                <li class="nav-header">
                    <i class="fas fa-users"></i> Pacientes
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/medico/pacientes" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Mis Pacientes</p>
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
                    <h1><i class="fas fa-prescription-bottle-alt"></i> Recetas Médicas</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/medico">Home</a></li>
                        <li class="breadcrumb-item active">Recetas</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <!-- Welcome Banner -->
            <div class="bv-welcome-banner medico bv-animate">
                <h2><i class="fas fa-prescription"></i> Recetario Electrónico</h2>
                <p>Gestiona las recetas médicas de tus pacientes de forma digital.</p>
                <div class="bv-role-tag"><i class="fas fa-user-md"></i> Módulo de Recetas</div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="info-box bv-animate bv-animate-delay-1">
                        <span class="info-box-icon bg-info"><i class="fas fa-prescription-bottle-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Mis Recetas</span>
                            <span class="info-box-number" id="total_recetas">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="info-box bv-animate bv-animate-delay-2">
                        <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pacientes Atendidos</span>
                            <span class="info-box-number" id="total_pacientes">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="info-box bv-animate bv-animate-delay-3">
                        <span class="info-box-icon bg-primary"><i class="fas fa-plus-circle"></i></span>
                        <div class="info-box-content">
                            <button class="btn btn-primary btn-sm btn-block" id="btnNuevaReceta">
                                <i class="fas fa-plus"></i> Nueva Receta
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
                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                </div>
                                <input type="text" id="buscar_receta" class="form-control" placeholder="Buscar receta por medicamento, paciente o fecha...">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" id="btnBuscar">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <button class="btn btn-outline-secondary" id="btnLimpiarBusqueda" style="display: none;">
                                        <i class="fas fa-times"></i> Limpiar
                                    </button>
                                </div>
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<<<<<<< HEAD
        </div>
    </div>
</section>

<!-- Modal para Crear/Editar Receta -->
<div class="modal fade modal-bv" id="modalReceta" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg-custom" role="document">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0d9488, #0f766e); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-prescription"></i> Nueva Receta
                </h5>
=======

            <!-- Tabla de Recetas -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list"></i> Listado de Recetas</h3>
                            <div class="card-tools">
                                <button class="btn btn-default btn-sm" id="btnRefresh">
                                    <i class="fas fa-sync-alt"></i> Actualizar
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
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_recetas">
                                    <tr><td colspan="8" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                        <p>Cargando recetas...</p>
                                    </td></tr>
                                </tbody>
                            </table>
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

<!-- Modal para Crear/Editar Receta -->
<div class="modal fade" id="modalReceta" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg-custom" role="document">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent)); color: white; border-radius: 16px 16px 0 0;">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-prescription"></i> Nueva Receta</h5>
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="id_receta" value="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required-field">Nombre del Medicamento</label>
                            <input type="text" class="form-control" id="nombre_medicamento" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required-field">Marca</label>
                            <input type="text" class="form-control" id="marca" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required-field">Cantidad</label>
                            <input type="text" class="form-control" id="cantidad" placeholder="Ej: 30 tabletas" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Dosis</label>
                            <input type="text" class="form-control" id="dosis" placeholder="Ej: 1 tableta cada 8 horas">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required-field">Paciente</label>
<<<<<<< HEAD
                            <div class="position-relative">
                                <input type="text" class="form-control" id="buscar_paciente" 
                                       placeholder="Buscar por cédula o nombre..." autocomplete="off">
                                <input type="hidden" id="id_paciente">
                                <div id="resultados_pacientes" class="list-group resultados-pacientes" style="display:none;"></div>
                            </div>
                            <small class="form-text text-muted">Ingrese al menos 2 caracteres para buscar</small>
=======
                            <input type="text" class="form-control" id="buscar_paciente" placeholder="Buscar por cédula o nombre">
                            <input type="hidden" id="id_paciente">
                            <div id="resultados_pacientes" class="list-group mt-1" style="display:none; position:absolute; z-index:1000; width:95%;"></div>
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required-field">Fecha de Receta</label>
                            <input type="date" class="form-control" id="fecha_receta" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Instrucciones</label>
<<<<<<< HEAD
                    <textarea class="form-control" id="instrucciones" rows="3" 
                              placeholder="Instrucciones adicionales para el paciente..."></textarea>
=======
                    <textarea class="form-control" id="instrucciones" rows="3" placeholder="Instrucciones adicionales para el paciente..."></textarea>
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
<<<<<<< HEAD
                <button type="button" class="btn btn-success" id="btnGuardar">
                    <i class="fas fa-save"></i> Guardar Receta
                </button>
=======
                <button type="button" class="btn btn-success" id="btnGuardar">Guardar Receta</button>
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalle Receta -->
<<<<<<< HEAD
<div class="modal fade modal-bv" id="modalDetalleReceta" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg-custom" role="document">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header bg-info text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title">
                    <i class="fas fa-file-prescription"></i> Detalle de Receta
                </h5>
=======
<div class="modal fade" id="modalDetalleReceta" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg-custom" role="document">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header bg-info text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title"><i class="fas fa-file-prescription"></i> Detalle de Receta</h5>
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalle_receta_content">
<<<<<<< HEAD
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Cargando detalles...</p>
=======
                <div class="text-center">
                    <div class="spinner-border text-primary"></div>
                    <p>Cargando detalles...</p>
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
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

<<<<<<< HEAD
<script>
$(document).ready(function() {
    console.log('=== RECETAS DEL MÉDICO ===');
    console.log('ID Médico:', $('#id_medico').val());
    console.log('APP_URL:', APP_URL);
    
    // Variables
    let recetasData = [];
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let filtroBusqueda = '';
    let filtroTipo = 'todas';
    let filtroOrden = 'fecha_desc';
    
    // ==================== CARGAR DATOS ====================
    
    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + '/api/medicos/mis-estadisticas',
            type: 'POST',
            data: { id_medico: <?php echo $id_medico; ?> },
            dataType: 'json',
            success: function(response) {
                console.log('Estadísticas recibidas:', response);
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                $('#total_recetas').text(data.total_recetas || 0);
                $('#total_pacientes').text(data.total_pacientes || 0);
                $('#recetas_mes').text(data.recetas_mes || 0);
            },
            error: function() {
                $('#total_recetas').text('0');
                $('#total_pacientes').text('0');
                $('#recetas_mes').text('0');
            }
        });
    }
    
    function cargarRecetas() {
        $('#loadingRecetas').show();
        $('#tabla_recetas').html(`
            <tr><td colspan="8" class="text-center py-4">
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
                console.log('Recetas recibidas:', response);
                var recetas = [];
                if (response.success && response.data) {
                    recetas = response.data;
                } else if (Array.isArray(response)) {
                    recetas = response;
                }
                recetasData = recetas;
                aplicarFiltros();
                $('#loadingRecetas').hide();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar recetas:', error);
                $('#tabla_recetas').html(`
                    <tr><td colspan="8" class="text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar las recetas: ${error}
                        </div>
                     </td
                    </tr>
                `);
                $('#loadingRecetas').hide();
            }
        });
    }
    
    function aplicarFiltros() {
        let filtrados = [...recetasData];
        
        if (filtroBusqueda) {
            let busquedaLower = filtroBusqueda.toLowerCase();
            filtrados = filtrados.filter(receta => {
                return (receta.nombre_medicamento && receta.nombre_medicamento.toLowerCase().includes(busquedaLower)) ||
                       (receta.marca && receta.marca.toLowerCase().includes(busquedaLower)) ||
                       (receta.paciente && receta.paciente.toLowerCase().includes(busquedaLower));
            });
        }
        
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
        
        if (filtroOrden === 'fecha_desc') {
            filtrados.sort((a, b) => new Date(b.fecha_receta || 0) - new Date(a.fecha_receta || 0));
        } else {
            filtrados.sort((a, b) => new Date(a.fecha_receta || 0) - new Date(b.fecha_receta || 0));
        }
        
        let total = filtrados.length;
        let desde = (paginaActual - 1) * registrosPorPagina + 1;
        let hasta = Math.min(paginaActual * registrosPorPagina, total);
        
        $('#total_registros').text(total);
        $('#desde').text(total > 0 ? desde : 0);
        $('#hasta').text(hasta);
        
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
                <tr><td colspan="8" class="text-center py-4">
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
                    tipoBadge = '<span class="badge badge-info"><i class="fas fa-flask"></i> Estudio</span>';
                } else if (receta.nombre_medicamento && receta.nombre_medicamento.includes('DIAGNÓSTICO')) {
                    tipoBadge = '<span class="badge badge-primary"><i class="fas fa-stethoscope"></i> Diagnóstico</span>';
                } else {
                    tipoBadge = '<span class="badge badge-success"><i class="fas fa-capsules"></i> Medicamento</span>';
                }
                
                let fecha = new Date(receta.fecha_receta);
                let fechaFormateada = fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                
                html += `
                    <tr>
                        <td><span class="badge badge-secondary">${receta.id_receta || ''}</span></td>
                        <td><strong>${escapeHtml(receta.nombre_medicamento || '')}</strong><br>${tipoBadge}</td>
                        <td>${escapeHtml(receta.marca || '')}</td>
                        <td>${escapeHtml(receta.cantidad || '')}</td>
                        <td>${escapeHtml(receta.dosis || '-')}</td>
                        <td><i class="fas fa-user-injured text-info"></i> ${escapeHtml(receta.paciente || 'N/A')}</td>
                        <td><i class="fas fa-calendar-alt"></i> ${fechaFormateada}</td>
                        <td class="table-actions">
                            <button class="btn btn-info btn-sm btn-ver-detalle" data-id="${receta.id_receta}" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm btn-editar" data-id="${receta.id_receta}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-borrar" data-id="${receta.id_receta}" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
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
    
    // ==================== CRUD DE RECETAS ====================
    
=======
<script src="<?php echo APP_URL; ?>/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    cargarEstadisticas();
    listar_recetas();

    // Botón nueva receta
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
    $('#btnNuevaReceta').click(function() {
        resetFormulario();
        $('#modalTitle').text('Nueva Receta');
        $('#modalReceta').modal('show');
    });
<<<<<<< HEAD
    
    $(document).on('click', '.btn-editar', function() {
        let id = $(this).data('id');
        editarReceta(id);
    });
    
    $(document).on('click', '.btn-borrar', function() {
        let id = $(this).data('id');
=======

    // Botón refrescar
    $('#btnRefresh').click(function() {
        listar_recetas();
        cargarEstadisticas();
    });

    // Buscar en la tabla
    $('#buscar_receta').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        let hayResultados = false;
        
        $('#tabla_recetas tr').each(function() {
            if ($(this).find('td').length > 0) {
                let texto = $(this).text().toLowerCase();
                let mostrar = texto.indexOf(value) > -1;
                $(this).toggle(mostrar);
                if (mostrar) hayResultados = true;
            }
        });
        
        if (value.length > 0) {
            $('#termino_busqueda').text(value);
            $('#resultado_busqueda').show();
            $('#btnLimpiarBusqueda').show();
        } else {
            $('#resultado_busqueda').hide();
            $('#btnLimpiarBusqueda').hide();
        }
        
        if (!hayResultados && value.length > 0) {
            $('#tabla_recetas').append('<tr class="sin-resultados"><td colspan="8" class="text-center text-muted">No se encontraron resultados para "' + escapeHtml(value) + '"</td></tr>');
        } else {
            $('.sin-resultados').remove();
        }
    });

    $('#btnLimpiarBusqueda').click(function() {
        $('#buscar_receta').val('');
        $('#resultado_busqueda').hide();
        $(this).hide();
        listar_recetas();
    });

    $('#limpiarResultados').click(function(e) {
        e.preventDefault();
        $('#buscar_receta').val('');
        $('#resultado_busqueda').hide();
        $('#btnLimpiarBusqueda').hide();
        listar_recetas();
    });

    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + '/api/medicos/mis-estadisticas',
            type: 'POST',
            data: { id_medico: <?php echo $_SESSION['usuario'] ?? 0; ?> },
            dataType: 'json',
            success: function(response) {
                console.log('Estadísticas recibidas:', response);
                
                // Manejar formato ApiResponse
                var data = response.success && response.data ? response.data : response;
                
                $('#total_recetas').text(data.total_recetas || 0);
                $('#total_pacientes').text(data.total_pacientes || 0);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estadísticas:', error);
                $('#total_recetas').text('0');
                $('#total_pacientes').text('0');
            }
        });
    }

    function listar_recetas() {
        $('#tabla_recetas').html('<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary"></div><p>Cargando recetas...</p></td></tr>');
        
        $.ajax({
            url: APP_URL + '/api/recetas/listar',
            type: 'POST',
            dataType: 'json',
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
                
                console.log('Recetas procesadas:', recetas.length);
                
                let html = '';
                
                if (recetas.length === 0) {
                    html = '<tr><td colspan="8" class="text-center text-muted">No hay recetas registradas</td></tr>';
                } else {
                    for (let i = 0; i < recetas.length; i++) {
                        let receta = recetas[i];
                        html += `
                            <tr>
                                <td><span class="badge badge-secondary">${receta.id_receta || ''}</span></td>
                                <td><strong>${escapeHtml(receta.nombre_medicamento || '')}</strong></td>
                                <td>${escapeHtml(receta.marca || '')}</td>
                                <td>${escapeHtml(receta.cantidad || '')}</td>
                                <td>${escapeHtml(receta.dosis || '-')}</td>
                                <td><i class="fas fa-user-injured text-info"></i> ${escapeHtml(receta.paciente || 'N/A')}</td>
                                <td><i class="fas fa-calendar-alt"></i> ${receta.fecha_receta || ''}</td>
                                <td class="table-actions">
                                    <button class="btn btn-info btn-sm btn-ver-detalle" data-id="${receta.id_receta}">
                                        <i class="fas fa-eye"></i> Ver
                                    </button>
                                    <button class="btn btn-warning btn-sm btn-editar" data-id="${receta.id_receta}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-borrar" data-id="${receta.id_receta}">
                                        <i class="fas fa-trash-alt"></i> Borrar
                                    </button>
                                </td>
                            </tr>
                        `;
                    }
                }
                
                $('#tabla_recetas').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error al listar recetas:', error);
                $('#tabla_recetas').html('<tr><td colspan="8" class="text-center text-danger">Error al cargar las recetas: ' + error + '</td></tr>');
            }
        });
    }

    $(document).on('click', '.btn-ver-detalle', function() {
        let id = $(this).data('id');
        console.log('Ver detalle receta ID:', id);
        
        $('#detalle_receta_content').html('<div class="text-center"><div class="spinner-border text-primary"></div><p>Cargando detalles...</p></div>');
        $('#modalDetalleReceta').modal('show');
        
        $.ajax({
            url: APP_URL + '/api/recetas/obtener',
            type: 'POST',
            data: { id_receta: id },
            dataType: 'json',
            success: function(response) {
                console.log('Detalle receta:', response);
                
                // Manejar formato ApiResponse
                var receta = response.success && response.data ? response.data : response;
                
                if (receta && receta.id_receta) {
                    let html = `
                        <div class="receta-detalle">
                            <div class="row mb-3">
                                <div class="col-md-12 text-center">
                                    <h3 class="text-primary">RECETA MÉDICA</h3>
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

    // Editar receta
    $(document).on('click', '.btn-editar', function() {
        let id = $(this).data('id');
        console.log('Editar receta ID:', id);
        editarReceta(id);
    });

    // Borrar receta
    $(document).on('click', '.btn-borrar', function() {
        let id = $(this).data('id');
        console.log('Borrar receta ID:', id);
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
        if (confirm('¿Está seguro de que desea eliminar esta receta?')) {
            borrarReceta(id);
        }
    });
<<<<<<< HEAD
    
    $(document).on('click', '.btn-ver-detalle', function() {
        let id = $(this).data('id');
        verDetalleReceta(id);
    });
    
    $('#btnGuardar').click(function() {
        guardarReceta();
    });
    
=======

    // Guardar receta
    $('#btnGuardar').click(function() {
        guardarReceta();
    });

    // Buscar pacientes
    let timeoutId;
    $('#buscar_paciente').on('keyup', function() {
        let dato = $(this).val();
        clearTimeout(timeoutId);
        
        if (dato.length >= 2) {
            timeoutId = setTimeout(function() {
                buscarPacientes(dato);
            }, 500);
        } else {
            $('#resultados_pacientes').hide();
        }
    });

    // Ocultar resultados al hacer clic fuera
    $(document).click(function(e) {
        if (!$(e.target).closest('#buscar_paciente, #resultados_pacientes').length) {
            $('#resultados_pacientes').hide();
        }
    });

>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
    function resetFormulario() {
        $('#id_receta').val('');
        $('#nombre_medicamento').val('');
        $('#marca').val('');
        $('#cantidad').val('');
        $('#dosis').val('');
        $('#instrucciones').val('');
        $('#buscar_paciente').val('');
        $('#id_paciente').val('');
<<<<<<< HEAD
        $('#resultados_pacientes').hide();
=======
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
        let hoy = new Date();
        let fecha = hoy.toISOString().split('T')[0];
        $('#fecha_receta').val(fecha);
    }
<<<<<<< HEAD
    
    function editarReceta(id) {
        $.ajax({
            url: APP_URL + '/api/recetas/obtener',
            type: 'POST',
            data: { id_receta: id },
            dataType: 'json',
            success: function(response) {
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
            error: function() {
                mostrarAlerta('Error al cargar los datos de la receta', 'error');
            }
        });
    }
    
=======

    function buscarPacientes(dato) {
        $.ajax({
            url: APP_URL + '/api/recetas/buscar-pacientes',
            type: 'POST',
            data: { dato: dato },
            dataType: 'json',
            success: function(response) {
                console.log('Pacientes encontrados:', response);
                
                // Manejar formato ApiResponse
                var pacientes = [];
                if (response.success && response.data) {
                    pacientes = response.data;
                } else if (Array.isArray(response)) {
                    pacientes = response;
                } else {
                    pacientes = response;
                }
                
                let html = '';
                
                if (!pacientes || pacientes.length === 0) {
                    html = '<a href="#" class="list-group-item list-group-item-action disabled">No se encontraron pacientes</a>';
                } else {
                    for (let i = 0; i < pacientes.length; i++) {
                        let paciente = pacientes[i];
                        html += `<a href="#" class="list-group-item list-group-item-action paciente-item" 
                                    data-id="${paciente.id_usuario}" 
                                    data-nombre="${escapeHtml(paciente.nombre_completo)}" 
                                    data-cedula="${escapeHtml(paciente.cedula)}">
                                    <strong>${escapeHtml(paciente.nombre_completo)}</strong><br>
                                    <small>Cédula: ${escapeHtml(paciente.cedula)}</small>
                                </a>`;
                    }
                }
                
                $('#resultados_pacientes').html(html).show();
                
                $('.paciente-item').off('click').on('click', function(e) {
                    e.preventDefault();
                    let nombreCompleto = $(this).data('nombre');
                    let id = $(this).data('id');
                    
                    $('#buscar_paciente').val(nombreCompleto);
                    $('#id_paciente').val(id);
                    $('#resultados_pacientes').hide();
                });
            },
            error: function(xhr, status, error) {
                console.error('Error al buscar pacientes:', error);
                $('#resultados_pacientes').html('<a href="#" class="list-group-item list-group-item-action disabled">Error al buscar pacientes</a>').show();
            }
        });
    }

>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
    function guardarReceta() {
        let id_receta = $('#id_receta').val();
        let nombre_medicamento = $('#nombre_medicamento').val().trim();
        let marca = $('#marca').val().trim();
        let cantidad = $('#cantidad').val().trim();
        let dosis = $('#dosis').val().trim();
        let instrucciones = $('#instrucciones').val().trim();
        let id_paciente = $('#id_paciente').val();
        let fecha_receta = $('#fecha_receta').val();
        
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
        
        let funcion = id_receta ? 'editar' : 'crear';
        let datos = {
            nombre_medicamento: nombre_medicamento,
            marca: marca,
            cantidad: cantidad,
            dosis: dosis,
            instrucciones: instrucciones,
            id_paciente: id_paciente,
<<<<<<< HEAD
            fecha_receta: fecha_receta,
            csrf_token: $('input[name="csrf_token"]').val()
=======
            fecha_receta: fecha_receta
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
        };
        
        if (id_receta) {
            datos.id_receta = id_receta;
        }
        
        let $btn = $('#btnGuardar');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/recetas/' + funcion,
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarAlerta(response.message, 'success');
                    $('#modalReceta').modal('hide');
<<<<<<< HEAD
                    cargarRecetas();
=======
                    listar_recetas();
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
                    cargarEstadisticas();
                    resetFormulario();
                } else {
                    mostrarAlerta(response.message || 'Error al guardar la receta', 'error');
                }
<<<<<<< HEAD
                $btn.prop('disabled', false).html(originalText);
=======
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión al guardar la receta', 'error');
<<<<<<< HEAD
=======
            },
            complete: function() {
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }
<<<<<<< HEAD
    
    function borrarReceta(id) {
        $.ajax({
            url: APP_URL + '/api/recetas/borrar',
            type: 'POST',
            data: { id_receta: id, csrf_token: $('input[name="csrf_token"]').val() },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarAlerta(response.message, 'success');
                    cargarRecetas();
                    cargarEstadisticas();
                } else {
                    mostrarAlerta(response.message || 'Error al borrar la receta', 'error');
                }
            },
            error: function() {
                mostrarAlerta('Error de conexión al borrar la receta', 'error');
            }
        });
    }
    
    function verDetalleReceta(id) {
        $('#detalle_receta_content').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Cargando detalles...</p>
            </div>
        `);
        $('#modalDetalleReceta').modal('show');
        
=======

    function editarReceta(id) {
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
        $.ajax({
            url: APP_URL + '/api/recetas/obtener',
            type: 'POST',
            data: { id_receta: id },
            dataType: 'json',
            success: function(response) {
<<<<<<< HEAD
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
                    
                    let fecha = new Date(receta.fecha_receta);
                    let fechaFormateada = fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' });
                    
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
            error: function() {
                $('#detalle_receta_content').html('<div class="alert alert-danger">Error al cargar los detalles de la receta</div>');
            }
        });
    }
    
    // ==================== BÚSQUEDA DE PACIENTES CORREGIDA ====================
    
    let timeoutId;
    
    // Evento de búsqueda con debounce
    $('#buscar_paciente').on('input', function() {
        let dato = $(this).val().trim();
        console.log('Buscando paciente con dato:', dato);
        
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
    
    // Función para buscar pacientes
    function buscarPacientes(dato) {
        console.log('Enviando petición a:', APP_URL + '/api/recetas/buscar-pacientes');
        console.log('Dato de búsqueda:', dato);
        
        // Mostrar indicador de carga
        $('#resultados_pacientes').html('<div class="list-group-item list-group-item-action text-center">Buscando...</div>').show();
        
        $.ajax({
            url: APP_URL + '/api/recetas/buscar-pacientes',
            type: 'POST',
            data: { dato: dato },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                // Manejar formato ApiResponse
                var pacientes = [];
                if (response.success && response.data) {
                    pacientes = response.data;
                    console.log('Pacientes desde ApiResponse.data:', pacientes);
                } else if (Array.isArray(response)) {
                    pacientes = response;
                    console.log('Pacientes desde array directo:', pacientes);
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
                console.error('Respuesta del servidor:', xhr.responseText);
                $('#resultados_pacientes').html('<a href="#" class="list-group-item list-group-item-action disabled">Error al buscar pacientes</a>');
            }
        });
    }
    
    // Evento para seleccionar un paciente de la lista
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
        
        // Opcional: Mostrar un mensaje de confirmación
        mostrarAlerta('Paciente seleccionado: ' + nombre_completo, 'success');
    });
    
    // Ocultar resultados al hacer clic fuera
    $(document).click(function(e) {
        if (!$(e.target).closest('#buscar_paciente, #resultados_pacientes').length) {
            $('#resultados_pacientes').hide();
        }
    });
    
    // Función para cargar datos de un paciente existente (para edición)
    function cargarDatosPaciente(id_paciente) {
        console.log('Cargando datos del paciente ID:', id_paciente);
        
=======
                var receta = response.success && response.data ? response.data : response;
                
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
                console.error('Error al editar:', error);
                mostrarAlerta('Error al cargar los datos de la receta', 'error');
            }
        });
    }

    function borrarReceta(id) {
        $.ajax({
            url: APP_URL + '/api/recetas/borrar',
            type: 'POST',
            data: { id_receta: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarAlerta(response.message, 'success');
                    listar_recetas();
                    cargarEstadisticas();
                } else {
                    mostrarAlerta(response.message || 'Error al borrar la receta', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al borrar:', error);
                mostrarAlerta('Error de conexión al borrar la receta', 'error');
            }
        });
    }

    function cargarDatosPaciente(id_paciente) {
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
        $.ajax({
            url: APP_URL + '/api/recetas/buscar-pacientes',
            type: 'POST',
            data: { dato: '' },
            dataType: 'json',
<<<<<<< HEAD
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
=======
            success: function(response) {
                var pacientes = response.success && response.data ? response.data : (Array.isArray(response) ? response : []);
                if (pacientes && Array.isArray(pacientes)) {
                    let paciente = pacientes.find(p => p.id_usuario == id_paciente);
                    if (paciente) {
                        $('#buscar_paciente').val(paciente.nombre_completo);
                        $('#id_paciente').val(paciente.id_usuario);
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar datos del paciente:', error);
            }
        });
    }
<<<<<<< HEAD
    
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
    
    $('#filtro_orden').change(function() {
        filtroOrden = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    $('#btnRefresh').click(function() {
        filtroBusqueda = '';
        filtroTipo = 'todas';
        filtroOrden = 'fecha_desc';
        paginaActual = 1;
        $('#buscar_receta').val('');
        $('#filtro_tipo').val('todas');
        $('#filtro_orden').val('fecha_desc');
        cargarRecetas();
        cargarEstadisticas();
        mostrarAlerta('Datos actualizados', 'success');
    });
    
    $(document).on('click', '#paginacion .page-link', function(e) {
        e.preventDefault();
        let nuevaPagina = $(this).data('pagina');
        if (nuevaPagina && !$(this).parent().hasClass('disabled')) {
            paginaActual = nuevaPagina;
            aplicarFiltros();
        }
    });
    
    // Establecer fecha actual por defecto
    let hoy = new Date();
    let fecha = hoy.toISOString().split('T')[0];
    $('#fecha_receta').val(fecha);
    
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
    
=======

    function mostrarAlerta(mensaje, tipo) {
        if (tipo === 'success') {
            alert('✓ Éxito: ' + mensaje);
        } else {
            alert('✗ Error: ' + mensaje);
        }
    }

>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    
<<<<<<< HEAD
    // ==================== INICIALIZAR ====================
    cargarEstadisticas();
    cargarRecetas();
});
</script>
=======
    // Establecer fecha actual por defecto
    let hoy = new Date();
    let fecha = hoy.toISOString().split('T')[0];
    $('#fecha_receta').val(fecha);
});
</script>

</body>
</html>
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
