<?php

if (!function_exists('AuthHelper')) {
    require_once dirname(__DIR__, 2) . '/helpers/AuthHelper.php';
}

// Obtener el rol actual
$current_role = AuthHelper::getCurrentRole();
$nombre_usuario = htmlspecialchars($_SESSION['nombre_us'] ?? 'Usuario');
$titulo_pagina = $titulo_pagina ?? 'BioVital - Panel';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo Security::getTokenCSRF(); ?>">
    <title><?php echo $titulo_pagina; ?></title>
    
    <!-- Variables globales JavaScript -->
    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
        var CURRENT_ROLE = '<?php echo $current_role; ?>';
        var USER_NAME = '<?php echo $nombre_usuario; ?>';
        var AVATAR_URL = '<?php echo !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : APP_URL . "/img/avatarDES.jpg"; ?>';
    </script>
    
    <!-- CSS Globales -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS del Sistema -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/dashboard.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/dashboard-utils.css">
    
    <!-- jQuery (siempre primero) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Configuración y CSRF -->
    <script src="<?php echo APP_URL; ?>/js/config.js"></script>
    <script src="<?php echo APP_URL; ?>/js/csrf.js"></script>
    
    <?php if (isset($css_extra)) echo $css_extra; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Botón sidebar toggle -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <!-- Breadcrumbs (opcional) -->
        <?php if (isset($breadcrumbs)): ?>
        <ol class="breadcrumb float-sm-left ml-3 mb-0 bg-transparent p-0">
            <?php foreach ($breadcrumbs as $crumb): ?>
                <?php if (isset($crumb['url'])): ?>
                    <li class="breadcrumb-item"><a href="<?php echo $crumb['url']; ?>"><?php echo $crumb['label']; ?></a></li>
                <?php else: ?>
                    <li class="breadcrumb-item active"><?php echo $crumb['label']; ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
        <?php endif; ?>

        <!-- Navbar Right -->
        <ul class="navbar-nav ml-auto">
            <!-- Notificaciones (placeholder) -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">3 Notificaciones</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-prescription-bottle-alt mr-2"></i> Nueva receta emitida
                        <span class="float-right text-muted text-sm">hace 2 min</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-calendar-check mr-2"></i> Cita confirmada
                        <span class="float-right text-muted text-sm">hace 1 hora</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">Ver todas</a>
                </div>
            </li>
            
            <!-- Usuario -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <img src="<?php echo !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : APP_URL . '/img/avatarDES.jpg'; ?>" 
                         class="img-circle elevation-2" style="width: 28px; height: 28px; object-fit: cover; margin-top: -6px;">
                    <span class="ml-1 d-none d-sm-inline"><?php echo $nombre_usuario; ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="<?php echo APP_URL; ?>/perfil" class="dropdown-item">
                        <i class="fas fa-user-circle mr-2"></i> Mi Perfil
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?php echo APP_URL; ?>/logout" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="<?php echo APP_URL; ?>/panel/<?php echo $current_role; ?>" class="brand-link">
            <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="BioVital Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-bold">BIOVITAL</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- User Panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="<?php echo !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : APP_URL . '/img/avatarDES.jpg'; ?>" 
                         class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="<?php echo APP_URL; ?>/perfil" class="d-block"><?php echo $nombre_usuario; ?></a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <!-- Inicio / Dashboard -->
                    <li class="nav-item">
                        <a href="<?php echo APP_URL; ?>/panel/<?php echo $current_role; ?>" class="nav-link <?php echo ($active_page ?? '') === 'dashboard' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Inicio</p>
                        </a>
                    </li>
                    <li class="nav-divider"></li>
                    <!-- Menú según el rol -->
                    <?php if ($current_role === 'administrador'): ?>
                        <!-- Menú de Administrador -->
                        <li class="nav-header">USUARIO</li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/perfil" class="nav-link <?php echo ($active_page ?? '') === 'perfil' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Datos personales</p>
                            </a>
                        </li>
                        
                        <li class="nav-header">GESTIÓN</li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/administrador/usuarios" class="nav-link <?php echo ($active_page ?? '') === 'usuarios' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Usuarios</p>
                            </a>
                        </li>
                        
                        <li class="nav-header">CLÍNICA</li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/especialidades" class="nav-link <?php echo ($active_page ?? '') === 'especialidades' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-stethoscope"></i>
                                <p>Especialidades</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/consultorios" class="nav-link <?php echo ($active_page ?? '') === 'consultorios' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Consultorios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/recetas" class="nav-link <?php echo ($active_page ?? '') === 'recetas' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                                <p>Recetas</p>
                            </a>
                        </li>
                        
                        <li class="nav-header">REPORTES</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Estadísticas</p>
                            </a>
                        </li>
                        
                    <?php elseif ($current_role === 'medico'): ?>
                        <!-- Menú de Médico -->
                        <li class="nav-header">USUARIO</li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/perfil" class="nav-link <?php echo ($active_page ?? '') === 'perfil' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Datos personales</p>
                            </a>
                        </li>
                        
                        <li class="nav-header">CLÍNICA</li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/recetas" class="nav-link <?php echo ($active_page ?? '') === 'recetas' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                                <p>Recetas</p>
                            </a>
                        </li>
                        
                        <li class="nav-header">PACIENTES</li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/medico/pacientes" class="nav-link <?php echo ($active_page ?? '') === 'pacientes' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Mis Pacientes</p>
                            </a>
                        </li>
                        
                    <?php elseif ($current_role === 'asistente'): ?>
                        <!-- Menú de Asistente -->
                        <li class="nav-header">USUARIO</li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/perfil" class="nav-link <?php echo ($active_page ?? '') === 'perfil' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Datos personales</p>
                            </a>
                        </li>
                        
                        <li class="nav-header">CLÍNICA</li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/recetas" class="nav-link <?php echo ($active_page ?? '') === 'recetas' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                                <p>Recetas</p>
                            </a>
                        </li>
                        
                    <?php elseif ($current_role === 'paciente'): ?>
                        <!-- Menú de Paciente -->
                        <li class="nav-header">USUARIO</li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/perfil" class="nav-link <?php echo ($active_page ?? '') === 'perfil' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Datos personales</p>
                            </a>
                        </li>
                        
                        <li class="nav-header">CLÍNICA</li>
                        <li class="nav-item">
                            <a href="<?php echo APP_URL; ?>/paciente/recetas" class="nav-link <?php echo ($active_page ?? '') === 'recetas' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                                <p>Mis Recetas</p>
                            </a>
                        </li>
                        
                        <li class="nav-header">CITAS</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Mis Citas</p>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <?php echo $content ?? ''; ?>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Versión</b> <?php echo APP_VERSION; ?>
        </div>
        <strong>Copyright &copy; <?php echo date('Y'); ?> BioVital.</strong> Todos los derechos reservados.
    </footer>
</div>

<!-- Scripts globales -->
<script src="<?php echo APP_URL; ?>/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if (isset($scripts_extra)) echo $scripts_extra; ?>

<script>
// Función global para mostrar alertas estilo toast
function mostrarToast(mensaje, tipo = 'success') {
    var toastHtml = `
        <div class="toast align-items-center text-white bg-${tipo === 'success' ? 'success' : tipo === 'error' ? 'danger' : 'info'} border-0 position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 250px;" 
             role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${tipo === 'success' ? 'fa-check-circle' : tipo === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} me-2"></i>
                    ${mensaje}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    $('body').append(toastHtml);
    var toast = $('.toast').last();
    setTimeout(function() { toast.remove(); }, 3500);
}

// Inicializar tooltips y popovers de Bootstrap
$(function() {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
});
</script>

</body>
</html>
