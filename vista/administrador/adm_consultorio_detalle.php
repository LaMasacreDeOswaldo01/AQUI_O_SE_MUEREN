<?php
if($_SESSION['us_tipo'] != 4 || $_SESSION['rol'] != 'administrador'){
    header('Location: ' . APP_URL . '/login/administrador');
    exit();
}

$nombre_usuario = $_SESSION['nombre_us'] ?? 'Administrador';
// Obtener el ID de la URL (GET)
$id_consultorio = isset($_GET['id']) ? intval($_GET['id']) : 0;
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
    
    <title>Administrador | Detalle Consultorio</title>
    
    <style>
        .info-box-icon-custom {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 15px;
        }
        .medico-item {
            border-left: 3px solid #17a2b8;
            margin-bottom: 10px;
            transition: all 0.2s;
        }
        .medico-item:hover {
            background-color: #f8f9fa;
        }
        .especialidad-badge {
            display: inline-block;
            background: #e9ecef;
            padding: 5px 12px;
            border-radius: 20px;
            margin: 3px;
            font-size: 12px;
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
                    <a href="<?php echo APP_URL; ?>/especialidades" class="nav-link">
                        <i class="nav-icon fas fa-stethoscope"></i>
                        <p>Especialidades</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo APP_URL; ?>/consultorios" class="nav-link active">
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
                    <h1><i class="fas fa-building"></i> <span id="consultorio_nombre">Detalle del Consultorio</span></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/administrador">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/consultorios">Consultorios</a></li>
                        <li class="breadcrumb-item active">Detalle</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <input type="hidden" id="id_consultorio" value="<?php echo $id_consultorio; ?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <div class="info-box-icon-custom bg-primary mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%;">
                                    <i class="fas fa-hospital-user fa-3x text-white"></i>
                                </div>
                            </div>
                            <h3 class="profile-username text-center" id="detalle_nombre">-</h3>
                            <p class="text-muted text-center" id="detalle_ciudad">-</p>
                            
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b><i class="fas fa-clock"></i> Horario</b>
                                    <a class="float-right" id="detalle_horario">-</a>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-phone"></i> Teléfono</b>
                                    <a class="float-right" id="detalle_telefono">-</a>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-envelope"></i> Email</b>
                                    <a class="float-right" id="detalle_email">-</a>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fas fa-map-marker-alt"></i> Dirección</b>
                                    <a class="float-right" id="detalle_direccion">-</a>
                                </li>
                            </ul>                            
                            <div class="text-center">
                                <a href="<?php echo APP_URL; ?>/consultorios/horarios?id=<?php echo $id_consultorio; ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-calendar-alt"></i> Gestionar Horarios
                                </a>
                                <a href="<?php echo APP_URL; ?>/consultorios/editar?id=<?php echo $id_consultorio; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Editar Información
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-stethoscope"></i> Especialidades Admitidas</h3>
                        </div>
                        <div class="card-body" id="contenedor_especialidades">
                            <p class="text-muted text-center">Cargando...</p>
                        </div>
                    </div>
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line"></i> Estadísticas</h3>
                        </div>
                        <div class="card-body text-center">
                            <h2 class="text-info" id="total_citas">0</h2>
                            <p>Citas Históricas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-user-md"></i> Médicos Asignados</h3>
                            <div class="card-tools">
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAsignarMedico">
                                    <i class="fas fa-plus"></i> Asignar Médico
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="contenedor_medicos">
                                <div class="text-center">
                                    <div class="spinner-border spinner-border-sm"></div> Cargando...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Información Adicional</h3>
                        </div>
                        <div class="card-body" id="detalle_descripcion">
                            <p class="text-muted">-</p>
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

<!-- Modal Asignar Médico -->
<div class="modal fade" id="modalAsignarMedico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Asignar Médico</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Seleccionar Médico</label>
                    <select class="form-control" id="medico_seleccionado">
                        <option value="">Seleccione un médico...</option>
                    </select>
                </div>
                <div id="mensaje_asignacion" class="alert" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnAsignarMedico">Asignar</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo APP_URL; ?>/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo APP_URL; ?>/js/consultorio.js"></script>

</body>
</html>