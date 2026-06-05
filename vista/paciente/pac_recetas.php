<?php
<<<<<<< HEAD
<<<<<<< HEAD
// vista/paciente/pac_recetas.php
// Contenido principal para la visualización de recetas del paciente
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_paciente = $id_paciente ?? $_SESSION['usuario'] ?? 0;
=======
<<<<<<< HEAD
// vista/paciente/pac_recetas.php - CORREGIDO
// Maneja correctamente el formato ApiResponse

// Verificar autenticación y rol
=======

>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
if($_SESSION['us_tipo'] != 1 || $_SESSION['rol'] != 'paciente'){
    header('Location: ' . APP_URL . '/login/paciente');
    exit();
}

<<<<<<< HEAD
// Incluir Security para CSRF si es necesario
$securityPath = dirname(__DIR__, 2) . '/modelo/Security.php';
if (file_exists($securityPath)) {
    include_once $securityPath;
}

$nombre_usuario = $_SESSION['nombre_us'] ?? 'Usuario';
$id_paciente = $_SESSION['usuario'];
=======
// Usar rutas absolutas con dirname()
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
?>

<<<<<<< HEAD
=======
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_paciente = $id_paciente ?? $_SESSION['usuario'] ?? 0;
?>
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    .receta-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #eef2f6;
        transition: all 0.3s;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .receta-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .receta-header {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .receta-header .receta-id {
        font-size: 0.75rem;
        opacity: 0.8;
    }
    .receta-body {
        padding: 1.25rem;
    }
    .receta-medicamento {
        font-weight: 700;
        font-size: 1rem;
        color: var(--bv-dark);
        margin-bottom: 0.5rem;
    }
    .receta-marca {
        font-size: 0.8rem;
        color: var(--bv-text-light);
        margin-bottom: 0.5rem;
    }
    .receta-detalle {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid #eef2f6;
    }
    .receta-detalle-item {
        flex: 1;
        min-width: 100px;
    }
    .receta-detalle-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        color: var(--bv-text-light);
        margin-bottom: 0.25rem;
    }
    .receta-detalle-value {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--bv-dark);
    }
    .receta-footer {
        background: #f8f9fa;
        padding: 0.75rem 1.25rem;
        border-top: 1px solid #eef2f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .receta-medico {
        font-size: 0.75rem;
        color: var(--bv-text-light);
    }
    .receta-medico i {
        color: #0d9488;
        margin-right: 0.25rem;
    }
    .btn-ver-detalle {
        background: none;
        border: none;
        color: var(--bv-primary);
        cursor: pointer;
        transition: all 0.2s;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
    }
    .btn-ver-detalle:hover {
        background: rgba(0,119,182,0.1);
        transform: scale(1.05);
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
    .badre-receta-tipo {
        font-size: 0.65rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        background: #e8f4f8;
        color: #0d9488;
        display: inline-block;
    }
    .pagination-custom {
        margin-bottom: 0;
    }
    .pagination-custom .page-item.active .page-link {
        background-color: var(--bv-primary);
        border-color: var(--bv-primary);
    }
    .pagination-custom .page-link {
        color: var(--bv-primary);
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
        color: var(--bv-primary);
        margin-bottom: 0.75rem;
    }
    .info-card p {
        font-size: 0.75rem;
        color: var(--bv-text-light);
        margin-bottom: 0.5rem;
    }
    .modal-bv .modal-content {
        border-radius: 16px;
    }
    .modal-bv .modal-header {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        border-radius: 16px 16px 0 0;
    }
    .receta-detalle-modal {
        font-size: 0.9rem;
    }
    .receta-detalle-modal h3 {
        font-size: 1.2rem;
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
                <h1><i class="fas fa-prescription-bottle-alt"></i> Mis Recetas</h1>
            </div>
        </div>
<<<<<<< HEAD
=======
    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
<<<<<<< HEAD
        var ID_PACIENTE = <?php echo json_encode($id_paciente); ?>;
        console.log('APP_URL:', APP_URL);
        console.log('ID_PACIENTE:', ID_PACIENTE);
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?php echo APP_URL; ?>/js/config.js"></script>
    <script src="<?php echo APP_URL; ?>/js/csrf.js"></script>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
<<<<<<< HEAD
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <title>Paciente | Mis Recetas</title>
    
    <style>
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
        .receta-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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
            transition: all 0.2s;
        }
        .btn-ver-detalle:hover {
            color: #0056b3;
            transform: scale(1.05);
        }
        .prescription-icon {
            font-size: 2.5rem;
            color: var(--bv-primary);
        }
    </style>
=======
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    
    <title>Paciente | Mis Recetas</title>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
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
<<<<<<< HEAD
        <a href="<?php echo APP_URL; ?>/logout" class="btn btn-danger btn-sm">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
=======
        <a href="<?php echo APP_URL; ?>/logout" class="btn btn-danger btn-sm">Cerrar sesión</a>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
    </ul>
</nav>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo APP_URL; ?>/panel/paciente" class="brand-link">
        <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
<<<<<<< HEAD
        <span class="brand-text font-weight-light">BioVital</span>
=======
        <span class="brand-text font-weight-light">BIOVITAL</span>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img id="avatar4" src="<?php echo APP_URL; ?>/img/avatar.png" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
<<<<<<< HEAD
                <a href="#" class="d-block"><?php echo htmlspecialchars($nombre_usuario); ?></a>
=======
                <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['nombre_us'] ?? 'Usuario'); ?></a>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
<<<<<<< HEAD
                <li class="nav-header">
                    <i class="fas fa-user-injured"></i> Usuario
                </li>
=======
                <li class="nav-header">Usuario</li>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/perfil" class="nav-link">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Datos personales</p>
                    </a>
                </li>
<<<<<<< HEAD
                <li class="nav-header">
                    <i class="fas fa-clinic-medical"></i> Clínica
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/paciente/recetas" class="nav-link active">
=======
                <li class="nav-header">Clínica</li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/paciente/recetas" class="nav-link">
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
                        <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                        <p>Mis Recetas</p>
                    </a>
                </li>
<<<<<<< HEAD
                <li class="nav-header">
                    <i class="fas fa-calendar-alt"></i> Citas
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Mis Citas</p>
=======
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/documentos" class="nav-link">
                        <i class="nav-icon fas fa-file-medical"></i>
                        <p>Documentos médicos</p>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
                    </a>
                </li>
            </ul>
        </nav>
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
    </div>
</div>

<<<<<<< HEAD
=======
    </div>
</div>

>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_paciente" value="<?php echo $id_paciente; ?>">
        
        <!-- Welcome Banner -->
        <div class="bv-welcome-banner bv-animate">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-prescription me-2"></i> Mis Recetas Médicas</h2>
                    <p class="mb-0">Consulta todas las recetas médicas que te han sido prescritas.</p>
                    <div class="bv-role-tag mt-2">
                        <i class="fas fa-user-injured"></i> Historial de Recetas
                    </div>
                </div>
                <div class="d-none d-md-block">
                    <i class="fas fa-chart-line fa-3x" style="opacity: 0.3;"></i>
<<<<<<< HEAD
=======
<<<<<<< HEAD
<!-- Content Wrapper -->
=======

>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-prescription-bottle-alt"></i> Mis Recetas Médicas</h1>
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
                </div>
<<<<<<< HEAD
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/paciente">Home</a></li>
                        <li class="breadcrumb-item active">Mis Recetas</li>
                    </ol>
                </div>
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
            </div>
        </div>

<<<<<<< HEAD
=======
                </div>
            </div>
        </div>

>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-1">
                    <div class="stats-number" id="total_recetas">0</div>
                    <div class="stats-label">Total Recetas</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-2">
                    <div class="stats-number" id="total_medicos">0</div>
                    <div class="stats-label">Médicos Atendidos</div>
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-12">
                <div class="stats-card bv-animate bv-animate-delay-3">
                    <div class="stats-number" id="ultima_receta">-</div>
                    <div class="stats-label">Última Receta</div>
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
                                       placeholder="Buscar por medicamento, marca o médico...">
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
                                <option value="medicamento">Por medicamento</option>
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
                    <button class="btn btn-success btn-sm ml-2" id="btnImprimirTodo">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>

        <!-- Listado de Recetas -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Historial de Recetas
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="loadingRecetas" class="loading-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        <div id="contenedor_recetas">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando tus recetas...</p>
                            </div>
                        </div>
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
                            </div>
<<<<<<< HEAD
=======
    <section class="content">
        <div class="container-fluid">
<<<<<<< HEAD
            
            <!-- Welcome Banner -->
            <div class="bv-welcome-banner bv-animate">
                <h2><i class="fas fa-prescription"></i> Mis Recetas</h2>
                <p>Consulta todas las recetas médicas que te han sido prescritas.</p>
                <div class="bv-role-tag"><i class="fas fa-user-injured"></i> Historial de Recetas</div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="info-box bv-animate bv-animate-delay-1">
                        <span class="info-box-icon bg-info"><i class="fas fa-prescription-bottle-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Recetas</span>
                            <span class="info-box-number" id="total_recetas">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="info-box bv-animate bv-animate-delay-2">
                        <span class="info-box-icon bg-success"><i class="fas fa-user-md"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Médicos Atendidos</span>
                            <span class="info-box-number" id="total_medicos">0</span>
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
                                <input type="text" id="buscar_receta" class="form-control" placeholder="Buscar receta por medicamento, médico o fecha...">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" id="btnBuscar">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <button class="btn btn-outline-secondary" id="btnLimpiarBusqueda" style="display: none;">
                                        <i class="fas fa-times"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Recetas -->
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
<<<<<<< HEAD
                            <h3 class="card-title"><i class="fas fa-list"></i> Listado de Recetas</h3>
                            <div class="card-tools">
                                <button class="btn btn-default btn-sm" id="btnRefresh">
                                    <i class="fas fa-sync-alt"></i> Actualizar
                                </button>
=======
                            <h3 class="card-title">Listado de Recetas</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" id="buscar_receta" class="form-control float-right" placeholder="Buscar...">
                                </div>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
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
                                        <th>Médico</th>
                                        <th>Fecha</th>
<<<<<<< HEAD
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
=======
                                    </tr>
                                </thead>
                                <tbody id="tabla_recetas">
                                    <tr><td colspan="7" class="text-center">Cargando recetas...</td</tr>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
                                </tbody>
                            </table>
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Ayuda -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="info-card">
                    <h6><i class="fas fa-info-circle"></i> Información importante</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <p><i class="fas fa-check-circle text-success"></i> <strong>Recetas válidas:</strong> Todas las recetas son legalmente válidas</p>
                        </div>
                        <div class="col-md-4">
                            <p><i class="fas fa-print text-info"></i> <strong>Impresión:</strong> Puedes imprimir tus recetas para presentarlas en farmacias</p>
                        </div>
                        <div class="col-md-4">
                            <p><i class="fas fa-clock text-warning"></i> <strong>Vigencia:</strong> Consulta con tu médico sobre la vigencia de cada receta</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Ver Detalle Receta -->
<div class="modal fade modal-bv" id="modalDetalleReceta" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
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
                <button type="button" class="btn btn-primary" id="btnImprimirReceta">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<<<<<<< HEAD
<<<<<<< HEAD
<footer class="main-footer">
    <div class="float-right d-none d-sm-block">
        <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2024 BioVital.</strong> Todos los derechos reservados.
</footer>

</div>

<!-- Modal Ver Detalle Receta -->
<div class="modal fade" id="modalDetalleReceta" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg-custom" role="document">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header bg-info text-white" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title"><i class="fas fa-file-prescription"></i> Detalle de Receta</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalle_receta_content">
                <div class="text-center">
                    <div class="spinner-border text-primary"></div>
                    <p>Cargando detalles...</p>
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

<script src="<?php echo APP_URL; ?>/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    console.log('=== CARGANDO RECETAS DEL PACIENTE ===');
    console.log('ID Paciente:', ID_PACIENTE);
    
    cargarEstadisticas();
    listar_recetas();

    // Botón refrescar
    $('#btnRefresh').click(function() {
        listar_recetas();
        cargarEstadisticas();
    });

    // Buscar en la tabla
    $('#btnBuscar').click(function() {
        let busqueda = $('#buscar_receta').val().toLowerCase();
        buscarEnTabla(busqueda);
    });

    $('#buscar_receta').keypress(function(e) {
        if (e.which == 13) {
            $('#btnBuscar').click();
        }
    });

    $('#btnLimpiarBusqueda').click(function() {
        $('#buscar_receta').val('');
        $(this).hide();
        listar_recetas();
    });

    function buscarEnTabla(busqueda) {
        let hayResultados = false;
        $('#tabla_recetas tr').each(function() {
            if ($(this).find('td').length > 0) {
                let texto = $(this).text().toLowerCase();
                let mostrar = texto.indexOf(busqueda) > -1;
                $(this).toggle(mostrar);
                if (mostrar) hayResultados = true;
            }
        });
        
        if (busqueda.length > 0) {
            $('#btnLimpiarBusqueda').show();
            if (!hayResultados) {
                $('#tabla_recetas').append('<tr class="sin-resultados"><td colspan="8" class="text-center text-muted">No se encontraron resultados para "' + escapeHtml(busqueda) + '"</td></tr>');
            }
        } else {
            $('#btnLimpiarBusqueda').hide();
            $('.sin-resultados').remove();
        }
    }

    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + '/api/pacientes/mis-estadisticas',
            type: 'POST',
            data: { id_paciente: ID_PACIENTE },
            dataType: 'json',
            success: function(response) {
                console.log('Estadísticas recibidas:', response);
                
                // Manejar formato ApiResponse
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                $('#total_recetas').text(data.total_recetas || 0);
                // Calcular médicos únicos de las recetas (esto se puede mejorar)
                $('#total_medicos').text(data.medicos_atendieron || 0);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estadísticas:', error);
                $('#total_recetas').text('0');
                $('#total_medicos').text('0');
            }
        });
    }

    function listar_recetas() {
        $('#tabla_recetas').html('<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary"></div><p>Cargando recetas...</p></td></tr>');
=======
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
<script>
$(document).ready(function() {
    console.log('=== MIS RECETAS - PACIENTE ===');
    console.log('ID Paciente:', $('#id_paciente').val());
    
    // Variables
    let recetasData = [];
    let paginaActual = 1;
    let registrosPorPagina = 10;
    let busquedaActual = '';
    let tipoActual = 'todas';
    let ordenActual = 'fecha_desc';
    let recetaActual = null;
    
    // ==================== CARGAR DATOS ====================
    
    function cargarEstadisticas() {
        $.ajax({
            url: APP_URL + '/api/pacientes/mis-estadisticas',
            type: 'POST',
            data: { id_paciente: <?php echo $id_paciente; ?> },
            dataType: 'json',
            success: function(response) {
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                $('#total_recetas').text(data.total_recetas || 0);
                $('#total_medicos').text(data.medicos_atendieron || 0);
                
                if (data.ultima_receta) {
                    let fecha = new Date(data.ultima_receta);
                    $('#ultima_receta').text(fecha.toLocaleDateString('es-ES'));
                } else {
                    $('#ultima_receta').text('Ninguna');
                }
            },
            error: function() {
                $('#total_recetas').text('0');
                $('#total_medicos').text('0');
                $('#ultima_receta').text('Ninguna');
            }
        });
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    }
    
    function cargarRecetas() {
        $('#loadingRecetas').show();
<<<<<<< HEAD
=======
    });

    function listar_recetas() {
        $('#tabla_recetas').html('<tr><td colspan="7" class="text-center">Cargando recetas...<div class="spinner-border spinner-border-sm ml-2"></div></td></tr>');
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        
        $.ajax({
            url: APP_URL + '/api/recetas/mis-recetas',
            type: 'POST',
<<<<<<< HEAD
<<<<<<< HEAD
            data: { id_paciente: <?php echo $id_paciente; ?> },
=======
<<<<<<< HEAD
            data: { id_paciente: ID_PACIENTE },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta recetas (raw):', response);
                
                // ==================== MANEJAR FORMATO ApiResponse ====================
                var recetas = [];
                
                // Si la respuesta tiene el formato ApiResponse (success + data)
                if (response.success && response.data) {
                    recetas = response.data;
                    console.log('Recetas extraídas de ApiResponse.data:', recetas);
                } 
                // Si es un array directo
                else if (Array.isArray(response)) {
                    recetas = response;
                    console.log('Recetas es un array directo:', recetas);
                }
                // Si tiene propiedad recetas
                else if (response.recetas && Array.isArray(response.recetas)) {
                    recetas = response.recetas;
                    console.log('Recetas extraídas de response.recetas:', recetas);
                }
                // Otro formato
                else {
                    console.warn('Formato de respuesta no reconocido:', response);
                    recetas = [];
                }
                
                // Asegurar que sea un array
                if (!Array.isArray(recetas)) {
                    console.error('recetas no es un array:', recetas);
                    recetas = [];
                }
                
                console.log('Recetas procesadas (cantidad):', recetas.length);
                
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
                console.log('Tabla actualizada con', recetas.length, 'recetas');
            },
            error: function(xhr, status, error) {
                console.error('Error al listar recetas:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                $('#tabla_recetas').html('</table><td colspan="8" class="text-center text-danger">Error al cargar las recetas: ' + error + '</td></tr>');
            }
        });
    }

    // Ver detalle de receta
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
                var receta = response;
                if (response.success && response.data) {
                    receta = response.data;
                }
                
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

=======
            data: { id_paciente: <?php echo $_SESSION['usuario']; ?> },
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
            data: { id_paciente: <?php echo $id_paciente; ?> },
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
                $('#contenedor_recetas').html(`
                    <div class="text-center py-4">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error al cargar tus recetas: ${error}
                        </div>
                    </div>
                `);
                $('#loadingRecetas').hide();
            }
        });
    }
    
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    function aplicarFiltros() {
        let filtrados = [...recetasData];
        
        // Filtro por búsqueda
        if (busquedaActual) {
            let busquedaLower = busquedaActual.toLowerCase();
            filtrados = filtrados.filter(receta => {
                return (receta.nombre_medicamento && receta.nombre_medicamento.toLowerCase().includes(busquedaLower)) ||
                       (receta.marca && receta.marca.toLowerCase().includes(busquedaLower)) ||
                       (receta.medico && receta.medico.toLowerCase().includes(busquedaLower));
            });
        }
        
        // Filtro por tipo
        if (tipoActual !== 'todas') {
            if (tipoActual === 'medicamento') {
                filtrados = filtrados.filter(receta => 
                    receta.nombre_medicamento && !receta.nombre_medicamento.includes('ESTUDIOS') && !receta.nombre_medicamento.includes('DIAGNÓSTICO')
                );
            } else if (tipoActual === 'estudio') {
                filtrados = filtrados.filter(receta => 
                    receta.nombre_medicamento && receta.nombre_medicamento.includes('ESTUDIOS')
                );
            } else if (tipoActual === 'diagnostico') {
                filtrados = filtrados.filter(receta => 
                    receta.nombre_medicamento && receta.nombre_medicamento.includes('DIAGNÓSTICO')
                );
            }
        }
        
        // Ordenamiento
        if (ordenActual === 'fecha_desc') {
            filtrados.sort((a, b) => new Date(b.fecha_receta || 0) - new Date(a.fecha_receta || 0));
        } else if (ordenActual === 'fecha_asc') {
            filtrados.sort((a, b) => new Date(a.fecha_receta || 0) - new Date(b.fecha_receta || 0));
        } else if (ordenActual === 'medicamento') {
            filtrados.sort((a, b) => (a.nombre_medicamento || '').localeCompare(b.nombre_medicamento || ''));
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
        
        renderizarRecetas(recetasPagina);
        renderizarPaginacion(total);
    }
    
    function renderizarRecetas(recetas) {
        let html = '';
        
        if (recetas.length === 0) {
            html = `
                <div class="empty-state">
                    <i class="fas fa-prescription-bottle-alt"></i>
                    <p>No se encontraron recetas</p>
                    <p class="text-muted small">Intente con otros criterios de búsqueda</p>
                </div>
            `;
        } else {
            for (let i = 0; i < recetas.length; i++) {
                let receta = recetas[i];
                let tipoBadge = '';
                let tipoColor = '';
                
                if (receta.nombre_medicamento && receta.nombre_medicamento.includes('ESTUDIOS')) {
                    tipoBadge = '<span class="badre-receta-tipo"><i class="fas fa-flask"></i> Estudio de Laboratorio</span>';
                    tipoColor = '#e8f4f8';
                } else if (receta.nombre_medicamento && receta.nombre_medicamento.includes('DIAGNÓSTICO')) {
                    tipoBadge = '<span class="badre-receta-tipo"><i class="fas fa-stethoscope"></i> Diagnóstico Médico</span>';
                    tipoColor = '#fef3c7';
                } else {
                    tipoBadge = '<span class="badre-receta-tipo"><i class="fas fa-capsules"></i> Medicamento</span>';
                    tipoColor = '#d1fae5';
                }
                
                let fecha = new Date(receta.fecha_receta);
                let fechaFormateada = fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' });
                
                html += `
                    <div class="receta-card">
                        <div class="receta-header">
                            <div>
                                <strong>Receta #${receta.id_receta}</strong>
                            </div>
                            <div class="receta-id">
                                <i class="fas fa-calendar-alt"></i> ${fechaFormateada}
                            </div>
                        </div>
                        <div class="receta-body">
                            <div class="receta-medicamento">
                                ${escapeHtml(receta.nombre_medicamento || 'Medicamento no especificado')}
                            </div>
                            <div class="receta-marca">
                                <i class="fas fa-trademark"></i> ${escapeHtml(receta.marca || 'Sin marca especificada')}
                            </div>
                            <div class="receta-detalle">
                                <div class="receta-detalle-item">
                                    <div class="receta-detalle-label">Cantidad</div>
                                    <div class="receta-detalle-value">${escapeHtml(receta.cantidad || 'N/A')}</div>
                                </div>
                                <div class="receta-detalle-item">
                                    <div class="receta-detalle-label">Dosis</div>
                                    <div class="receta-detalle-value">${escapeHtml(receta.dosis || 'No especificada')}</div>
                                </div>
                                <div class="receta-detalle-item">
                                    <div class="receta-detalle-label">Tipo</div>
                                    <div class="receta-detalle-value">${tipoBadge}</div>
                                </div>
                            </div>
                        </div>
                        <div class="receta-footer">
                            <div class="receta-medico">
                                <i class="fas fa-user-md"></i> Dr(a). ${escapeHtml(receta.medico || 'Médico no especificado')}
                            </div>
                            <button class="btn-ver-detalle" data-id="${receta.id_receta}">
                                <i class="fas fa-eye"></i> Ver detalles completos
                            </button>
                        </div>
                    </div>
                `;
            }
        }
        
        $('#contenedor_recetas').html(html);
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
    
    // ==================== VER DETALLE DE RECETA ====================
    
    $(document).on('click', '.btn-ver-detalle', function() {
        let id = $(this).data('id');
        recetaActual = id;
        
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
            success: function(response) {
                var receta = response;
                if (response.success && response.data) {
                    receta = response.data;
                }
                
                if (receta && receta.id_receta) {
                    let tipo = '';
                    let fecha = new Date(receta.fecha_receta);
                    let fechaFormateada = fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' });
                    
                    if (receta.nombre_medicamento && receta.nombre_medicamento.includes('ESTUDIOS')) {
                        tipo = '<span class="badge badge-info"><i class="fas fa-flask"></i> Estudio de Laboratorio</span>';
                    } else if (receta.nombre_medicamento && receta.nombre_medicamento.includes('DIAGNÓSTICO')) {
                        tipo = '<span class="badge badge-primary"><i class="fas fa-stethoscope"></i> Diagnóstico Médico</span>';
                    } else {
                        tipo = '<span class="badge badge-success"><i class="fas fa-capsules"></i> Medicamento</span>';
                    }
                    
                    let html = `
                        <div class="receta-detalle-modal p-3">
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
                                    <p><strong><i class="fas fa-user-md"></i> Médico:</strong> ${escapeHtml(receta.medico || 'N/A')}</p>
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
    
    // Imprimir receta actual
    $('#btnImprimirReceta').click(function() {
        if (recetaActual) {
            window.open(APP_URL + '/recetas/imprimir/' + recetaActual, '_blank');
        }
    });
    
    $('#btnImprimirTodo').click(function() {
        window.print();
    });
    
    // ==================== FILTROS Y EVENTOS ====================
    
    $('#buscar_receta').on('keyup', function() {
        busquedaActual = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    $('#filtro_tipo').change(function() {
        tipoActual = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    $('#filtro_orden').change(function() {
        ordenActual = $(this).val();
        paginaActual = 1;
        aplicarFiltros();
    });
    
    $('#btnRefresh').click(function() {
        busquedaActual = '';
        tipoActual = 'todas';
        ordenActual = 'fecha_desc';
        paginaActual = 1;
        $('#buscar_receta').val('');
        $('#filtro_tipo').val('todas');
        $('#filtro_orden').val('fecha_desc');
        cargarRecetas();
        cargarEstadisticas();
        mostrarToast('Datos actualizados', 'success');
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
    
    // ==================== FUNCIONES UTILITARIAS ====================
    
    function mostrarToast(mensaje, tipo) {
        var toastHtml = `
            <div class="toast align-items-center text-white bg-${tipo === 'success' ? 'success' : 'info'} border-0 position-fixed" 
                 style="top: 70px; right: 20px; z-index: 9999; min-width: 250px; border-radius: 12px;" 
                 role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${tipo === 'success' ? 'fa-check-circle' : 'fa-info-circle'} me-2"></i>
                        ${mensaje}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        $('body').append(toastHtml);
        var toast = $('.toast').last();
        setTimeout(function() { toast.fadeOut(300, function() { $(this).remove(); }); }, 3000);
    }
    
<<<<<<< HEAD
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
    cargarEstadisticas();
    cargarRecetas();
});
<<<<<<< HEAD
<<<<<<< HEAD
</script>
=======
</script>

<<<<<<< HEAD
<?php include_once dirname(__DIR__) . '/layouts/footer.php'; ?>
=======
<?php
include_once dirname(__DIR__) . '/layouts/footer.php';
?>
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
</script>
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
