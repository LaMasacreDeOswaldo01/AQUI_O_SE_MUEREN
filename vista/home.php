<?php
// Redireccionar si ya tiene sesión activa
if(isset($_SESSION['usuario']) && isset($_SESSION['rol'])){
    $redirects = [
        'paciente'      => 'panel/paciente',
        'medico'        => 'panel/medico',
        'asistente'     => 'panel/asistente',
        'administrador' => 'panel/administrador'
    ];
    if(isset($redirects[$_SESSION['rol']])){
        header('Location: ' . APP_URL . '/' . $redirects[$_SESSION['rol']]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BioVital - Consultoría Médica Inteligente</title>
    <meta name="description" content="BioVital - Tu salud es nuestra prioridad. Ecosistema médico digital con especialidades en cardiología, neumonología, psicología y pediatría.">
    <meta name="keywords" content="consultoría médica, salud, cardiología, neumonología, psicología, pediatría, BioVital">
    <script>
        const APP_URL      = '<?php echo APP_URL; ?>';
        const openLoginRol = '<?php echo $_SESSION['open_login'] ?? ''; ?>';
        <?php if(isset($_SESSION['open_login'])) unset($_SESSION['open_login']); ?>
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
/* === Base === */
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body { font-family: 'Poppins', sans-serif; background: #f0f9ff; }

/* === Navbar === */
.navbar             { background: rgba(255, 255, 255, .95) !important; box-shadow: 0 2px 10px rgba(0, 0, 0, .1); padding: 15px 0; }
.navbar-brand img   { height: 50px; }
.navbar-brand span  { font-size: 1.5rem; font-weight: 700; color: #4e73df; margin-left: 10px; }
.btn-register       { background: #4e73df; color: #fff !important; border-radius: 20px; padding: 8px 20px !important; }
.btn-register:hover { background: #3a5fc8; }
.btn-login-nav      { background: #4e73df; color: #fff !important; border-radius: 20px; padding: 8px 20px !important; }

/* === Carrusel === */
.carousel            { margin-top: 76px; }
.carousel-item       { height: 500px; }
.carousel-item img   { object-fit: cover; height: 100%; width: 100%; }
.carousel-caption    { background: rgba(0, 0, 0, .5); border-radius: 10px; padding: 20px; }
.carousel-caption h3 { font-size: 2rem; font-weight: 600; }

/* === Hero === */
.hero-section     { background: #eaf5fb; padding: 70px 0 50px; }
.hero-title       { font-size: 2.5rem; font-weight: 700; color: #1e293b; margin-bottom: 20px; }
.hero-title span  { color: #4e73df; }
.hero-subtitle    { color: #64748b; font-size: 1.05rem; margin-bottom: 30px; }
.specialties-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.specialty-card {
    background: #fff; border-radius: 12px; padding: 18px 12px;
    text-align: center; cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, .07);
    transition: transform .3s, box-shadow .3s;
    border: 2px solid transparent;
}
.specialty-card:hover { transform: translateY(-5px); box-shadow: 0 10px 24px rgba(78, 115, 223, .18); border-color: #4e73df; }
.specialty-icon       { font-size: 1.8rem; color: #4e73df; margin-bottom: 8px; }
.specialty-name       { font-size: .72rem; font-weight: 600; color: #475569; letter-spacing: .5px; }

/* === Nosotros === */
.about-section    { padding: 80px 0; background: #fff; }
.about-img        { width: 100%; border-radius: 16px; box-shadow: 0 20px 40px rgba(0, 0, 0, .12); object-fit: cover; height: 400px; }
.about-image-wrap { position: relative; }
.about-badge      { position: absolute; bottom: 20px; right: -15px; background: #4e73df; color: #fff; padding: 12px 20px; border-radius: 12px; font-weight: 600; font-size: .9rem; box-shadow: 0 8px 20px rgba(78, 115, 223, .4); }
.section-tag      { display: inline-block; background: #eff6ff; color: #4e73df; padding: 6px 16px; border-radius: 20px; font-size: .85rem; font-weight: 600; margin-bottom: 15px; }
.gradient-text    { color: #2e59d9; }
.about-title      { font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 20px; }
.about-text       { color: #64748b; margin-bottom: 15px; }

/* === Estadísticas === */
.stats-row   { display: flex; gap: 30px; margin-top: 30px; }
.stat-item   { text-align: center; }
.stat-number { font-size: 2.2rem; font-weight: 700; color: #4e73df; display: block; }
.stat-suffix { font-size: 1.4rem; font-weight: 700; color: #4e73df; }
.stat-label  { font-size: .8rem; color: #64748b; display: block; }

/* === Portal de Acceso === */
.access-section { padding: 70px 0; background: #f8f9fa; }
.sec-title      { text-align: center; margin-bottom: 50px; }
.sec-title h2   { font-size: 2.2rem; color: #333; margin-bottom: 15px; }
.sec-title p    { color: #666; font-size: 1.05rem; }
.access-card {
    background: #fff; border-radius: 15px; padding: 30px;
    text-align: center; cursor: pointer;
    transition: transform .3s, box-shadow .3s;
    box-shadow: 0 5px 15px rgba(0, 0, 0, .1); margin-bottom: 30px;
}
.access-card:hover        { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0, 0, 0, .2); }
.access-card .icon        { width: 80px; height: 80px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 2.5rem; color: #fff; }
.card-paciente .icon      { background: #4e73df; }
.card-medico .icon        { background: #1cc88a; }
.card-asistente .icon     { background: #36b9cc; }
.card-administrador .icon { background: #e74a3b; }
.access-card h3           { font-size: 1.4rem; margin-bottom: 10px; color: #333; }
.access-card p            { color: #666; margin-bottom: 0; font-size: .92rem; }

/* === Modal Login === */
.modal-content                  { border-radius: 15px; border: none; }
.modal-header                   { background: #1a6b9e; color: #fff; border-radius: 15px 15px 0 0; border: none; }
.modal-header .btn-close        { filter: brightness(0) invert(1); }
.login-form .input-group        { margin-bottom: 20px; }
.login-form .input-group-text   { background: #f0f0f0; border: none; border-radius: 10px 0 0 10px; }
.login-form .form-control       { border: none; border-radius: 0 10px 10px 0; background: #f0f0f0; padding: 12px 15px; }
.login-form .form-control:focus { box-shadow: none; background: #e8e8e8; }
.btn-login-form                 { background: #4e73df; border: none; padding: 12px; border-radius: 10px; font-weight: 600; width: 100%; margin-top: 10px; color: #fff; }
.btn-login-form:hover           { filter: brightness(1.05); }
.register-link                  { text-align: center; margin-top: 20px; }
.register-link a                { color: #4e73df; text-decoration: none; }

/* === Sucursales === */
.locations-section   { padding: 70px 0; background: #eaf5fb; }
.location-card       { background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0, 0, 0, .08); transition: transform .3s, box-shadow .3s; margin-bottom: 30px; height: 100%; }
.location-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(78, 115, 223, .15); }
.location-icon       { width: 55px; height: 55px; background: #4e73df; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.4rem; margin-bottom: 15px; }
.location-card h3    { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 10px; }
.location-card p     { color: #64748b; font-size: .9rem; margin-bottom: 8px; }
.location-card p i   { color: #4e73df; margin-right: 6px; }
.btn-map             { background: #4e73df; color: #fff; border: none; padding: 8px 20px; border-radius: 20px; font-size: .85rem; font-weight: 600; margin-top: 10px; cursor: pointer; transition: filter .2s; }
.btn-map:hover       { filter: brightness(1.1); }

/* === Footer === */
footer                 { background: #1e293b; color: #cbd5e1; padding: 50px 0 20px; }
.footer-logo span      { color: #fff; font-size: 1.5rem; font-weight: 700; margin-left: 10px; }
.footer-logo span em   { color: #4e73df; font-style: normal; }
.footer-desc           { color: #94a3b8; font-size: .9rem; margin-top: 12px; }
.social-icons a        { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: rgba(255, 255, 255, .1); border-radius: 50%; color: #fff; margin-right: 8px; margin-top: 15px; text-decoration: none; transition: background .3s; }
.social-icons a:hover  { background: #4e73df; }
.footer-heading            { color: #fff; font-size: 1rem; font-weight: 600; margin-bottom: 15px; }
.footer-links-list         { list-style: none; padding: 0; }
.footer-links-list li      { margin-bottom: 8px; }
.footer-links-list a       { color: #94a3b8; text-decoration: none; font-size: .9rem; transition: color .2s; }
.footer-links-list a:hover { color: #fff; }
.footer-links-list li i    { color: #4e73df; margin-right: 6px; }
.footer-bottom             { border-top: 1px solid rgba(255, 255, 255, .1); padding-top: 20px; margin-top: 30px; text-align: center; color: #64748b; font-size: .85rem; }

/* === Responsive === */
@media (max-width: 768px) {
    .carousel-item       { height: 300px; }
    .carousel-caption h3 { font-size: 1.2rem; }
    .hero-title          { font-size: 1.8rem; }
    .specialties-grid    { grid-template-columns: repeat(2, 1fr); }
    .stats-row           { gap: 15px; }
    .about-badge         { right: 0; }
}
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo APP_URL; ?>">
            <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="Logo">
            <span>BioVital</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item"><a class="nav-link" href="#especializaciones">Especialidades</a></li>
                <li class="nav-item"><a class="nav-link" href="#nosotros">Sobre Nosotros</a></li>
                <li class="nav-item"><a class="nav-link" href="#ubicacion">Ubicación</a></li>
                <li class="nav-item"><a class="nav-link btn-register" href="<?php echo APP_URL; ?>/registro/paciente">Registrarse</a></li>
                <li class="nav-item"><a class="nav-link btn-login-nav" href="#acceso">Iniciar Sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Carrusel -->
<div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
<div class="carousel-indicators">
<button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
<button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
<button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2"></button>
</div>
<div class="carousel-inner">
<div class="carousel-item active">
<img src="<?php echo APP_URL; ?>/img/modernos.jpg" class="d-block w-100" alt="Instalaciones Modernas">
<div class="carousel-caption d-none d-md-block">
    <h3>Instalaciones Modernas</h3>
    <p>Tecnología de última generación al servicio de tu salud</p>
</div>
</div>
<div class="carousel-item">
<img src="<?php echo APP_URL; ?>/img/medicos.jpg" class="d-block w-100" alt="Equipo Médico">
<div class="carousel-caption d-none d-md-block">
    <h3>Equipo Médico Especializado</h3>
    <p>Profesionales altamente calificados comprometidos contigo</p>
</div>
</div>
<div class="carousel-item">
<img src="<?php echo APP_URL; ?>/img/atencion.jpg" class="d-block w-100" alt="Atención Personalizada">
<div class="carousel-caption d-none d-md-block">
    <h3>Atención Personalizada</h3>
    <p>Cuidado individual adaptado a cada paciente</p>
</div>
</div>
</div>
<button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
<button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
</div>

<!-- Hero / Especialidades -->
<section class="hero-section" id="home">
<div class="container">
<div class="row align-items-center gy-4">
<div class="col-lg-5">
<h1 class="hero-title">Tu Salud es Nuestra <span>Prioridad</span></h1>
<p class="hero-subtitle">Ecosistema médico digital de última generación. Accede a consultas, especialidades y seguimiento desde una sola plataforma.</p>
</div>
<div class="col-lg-7" id="especializaciones">
<div class="specialties-grid">
<div class="specialty-card" data-specialty="general">
    <div class="specialty-icon"><i class="fas fa-stethoscope"></i></div>
    <span class="specialty-name">GENERAL</span>
</div>
<div class="specialty-card" data-specialty="cardiologia">
    <div class="specialty-icon"><i class="fas fa-heartbeat"></i></div>
    <span class="specialty-name">CARDIOLOGÍA</span>
</div>
<div class="specialty-card" data-specialty="neumonologia">
    <div class="specialty-icon"><i class="fas fa-lungs"></i></div>
    <span class="specialty-name">NEUMOLOGÍA</span>
</div>
<div class="specialty-card" data-specialty="psicologia">
    <div class="specialty-icon"><i class="fas fa-brain"></i></div>
    <span class="specialty-name">PSICOLOGÍA</span>
</div>
<div class="specialty-card" data-specialty="pediatria">
    <div class="specialty-icon"><i class="fas fa-baby"></i></div>
    <span class="specialty-name">PEDIATRÍA</span>
</div>
<div class="specialty-card" data-specialty="ginecologia">
    <div class="specialty-icon"><i class="fas fa-venus"></i></div>
    <span class="specialty-name">GINECOLOGÍA</span>
</div>
</div>
</div>
</div>
</div>
</section>

<!-- Sobre Nosotros -->
<section class="about-section" id="nosotros">
<div class="container">
<div class="row align-items-center gy-4">
<div class="col-lg-5">
<div class="about-image-wrap">
<img src="<?php echo APP_URL; ?>/img/Doctora.png" alt="Profesional de la salud" class="about-img">
<div class="about-badge"><i class="fas fa-award me-2"></i>Médicos Certificados</div>
</div>
</div>
<div class="col-lg-7">
<span class="section-tag"><i class="fas fa-info-circle me-1"></i> Conócenos</span>
<h2 class="about-title">Excelencia Médica con Tecnología de Vanguardia</h2>
<p class="about-text">En BioVital, nos comprometemos a ofrecer servicios médicos de la más alta calidad con un equipo de profesionales altamente calificados y experimentados. Nuestra misión es brindar atención médica integral y personalizada.</p>
<p class="about-text">Contamos con tecnología de última generación e instalaciones modernas para asegurar diagnósticos precisos y tratamientos efectivos en todas nuestras especialidades.</p>
<div class="stats-row">
<div class="stat-item">
    <span class="stat-number" data-count="15">0</span><span class="stat-suffix">+</span>
    <span class="stat-label">Años de Experiencia</span>
</div>
<div class="stat-item">
    <span class="stat-number" data-count="50">0</span><span class="stat-suffix">+</span>
    <span class="stat-label">Médicos Especializados</span>
</div>
<div class="stat-item">
    <span class="stat-number" data-count="10">0</span><span class="stat-suffix">K+</span>
    <span class="stat-label">Pacientes Atendidos</span>
</div>
</div>
</div>
</div>
</div>
</section>

<!-- Portal de Acceso -->
<section class="access-section" id="acceso">
<div class="container">
<div class="sec-title">
<span class="section-tag"><i class="fas fa-lock-open me-1"></i> Portal de Acceso</span>
<h2>Ecosistema Digital <span class="gradient-text">Biovital</span></h2>
<p>Selecciona tu perfil para acceder a la plataforma</p>
</div>
<div class="row justify-content-center">
<div class="col-md-3">
    <div class="access-card card-paciente" data-rol="paciente">
        <div class="icon"><i class="fa-solid fa-user-injured"></i></div>
        <h3>Paciente</h3>
        <p>Accede a tu historial clínico, agenda citas y descarga recetas.</p>
    </div>
</div>
<div class="col-md-3">
    <div class="access-card card-medico" data-rol="medico">
        <div class="icon"><i class="fa-solid fa-user-md"></i></div>
        <h3>Médico</h3>
        <p>Gestiona pacientes, genera diagnósticos y controla horarios.</p>
    </div>
</div>
<div class="col-md-3">
    <div class="access-card card-asistente" data-rol="asistente">
        <div class="icon"><i class="fa-solid fa-clipboard-list"></i></div>
        <h3>Asistente</h3>
        <p>Organiza flujos de la clínica y agendas de especialistas.</p>
    </div>
</div>
<div class="col-md-3">
    <div class="access-card card-administrador" data-rol="administrador">
        <div class="icon"><i class="fa-solid fa-sliders"></i></div>
        <h3>Administrador</h3>
        <p>Supervisa métricas operativas, gestiona accesos y seguridad.</p>
    </div>
</div>
</div>
</div>
</section>

<!-- Sucursales -->
<section class="locations-section" id="ubicacion">
<div class="container">
<div class="sec-title">
<span class="section-tag"><i class="fas fa-map-marker-alt me-1"></i> Encuéntranos</span>
<h2>Nuestras <span class="gradient-text">Sucursales</span></h2>
</div>
<div class="row gy-4">
<div class="col-md-4">
    <div class="location-card">
        <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
        <h3>CARACAS (Principal)</h3>
        <p>Av. Principal con Calle Secundaria, Edificio Médico, Piso 3</p>
        <p><i class="fas fa-phone"></i> +58 212-1234567</p>
        <p><i class="fas fa-envelope"></i> caracas@biovital.com</p>
        <button class="btn-map" onclick="showMap('caracas')"><i class="fas fa-directions me-1"></i> Ver en mapa</button>
    </div>
</div>
<div class="col-md-4">
    <div class="location-card">
        <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
        <h3>CARACAS (Este)</h3>
        <p>Av. Este con Calle Norte, Centro Médico Este, Piso 2</p>
        <p><i class="fas fa-phone"></i> +58 212-7654321</p>
        <p><i class="fas fa-envelope"></i> este@biovital.com</p>
        <button class="btn-map" onclick="showMap('caracas-este')"><i class="fas fa-directions me-1"></i> Ver en mapa</button>
    </div>
</div>
<div class="col-md-4">
    <div class="location-card">
        <div class="location-icon"><i class="fas fa-map-marker-alt"></i></div>
        <h3>MARACAIBO (Zulia)</h3>
        <p>Av. del Lago con Calle 15, Hospital Zulia, Piso 1</p>
        <p><i class="fas fa-phone"></i> +58 261-9876543</p>
        <p><i class="fas fa-envelope"></i> maracaibo@biovital.com</p>
        <button class="btn-map" onclick="showMap('maracaibo')"><i class="fas fa-directions me-1"></i> Ver en mapa</button>
    </div>
</div>
</div>
</div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row gy-4">

            <div class="col-lg-4">
                <div class="footer-logo d-flex align-items-center">
                    <img src="<?php echo APP_URL; ?>/img/logo_blanco.png" alt="Logo" height="40">
                    <span>Bio<em>vital</em></span>
                </div>
                <p class="footer-desc">Plataforma tecnológica hospitalaria certificada. Tu bienestar, nuestra misión.</p>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5 class="footer-heading">Especialidades</h5>
                <ul class="footer-links-list">
                    <li><a href="#especializaciones">Medicina General</a></li>
                    <li><a href="#especializaciones">Cardiología</a></li>
                    <li><a href="#especializaciones">Neumología</a></li>
                    <li><a href="#especializaciones">Psicología</a></li>
                    <li><a href="#especializaciones">Pediatría</a></li>
                    <li><a href="#especializaciones">Ginecología</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5 class="footer-heading">Sucursales</h5>
                <ul class="footer-links-list">
                    <li><a href="#ubicacion">Caracas (Principal)</a></li>
                    <li><a href="#ubicacion">Caracas (Este)</a></li>
                    <li><a href="#ubicacion">Maracaibo (Zulia)</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-4">
                <h5 class="footer-heading">Contacto</h5>
                <ul class="footer-links-list">
                    <li><i class="fas fa-phone"></i> <a href="tel:+582121234567">+58 212-1234567</a></li>
                    <li><i class="fas fa-envelope"></i> <a href="mailto:info@biovital.com">info@biovital.com</a></li>
                    <li><i class="fas fa-clock"></i> Lun-Vie: 8AM - 6PM</li>
                    <li><i class="fas fa-calendar"></i> Sáb: 9AM - 1PM</li>
                </ul>
            </div>

        </div>
        <div class="footer-bottom">
            <p>© 2026 BioVital. Todos los derechos reservados. Plataforma Tecnológica Hospitalaria.</p>
        </div>
    </div>
</footer>

<!-- Modal Login -->
<div class="modal fade" id="loginModal" tabindex="-1" data-bs-backdrop="static">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title" id="modalTitle">Iniciar Sesión</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<form id="loginForm" class="login-form">
<input type="hidden" id="rol" name="rol" value="">
<div class="input-group">
    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
    <input type="text" class="form-control" id="cedula" name="user" placeholder="Cédula de identidad" required>
</div>
<div class="input-group">
    <span class="input-group-text"><i class="fas fa-lock"></i></span>
    <input type="password" class="form-control" id="password" name="pass" placeholder="Contraseña de seguridad" required>
</div>
<button type="submit" class="btn-login-form"><i class="fas fa-sign-in-alt"></i> Autenticar Entrada</button>
<div class="register-link" id="registerLink"><a href="#" id="registerButton">¿No tienes cuenta? Regístrate aquí</a></div>
<div id="loginError" class="alert alert-danger mt-3" style="display:none;"></div>
</form>
</div>
</div>
</div>
</div>

<!-- Modal Especialidades -->
<div class="modal fade" id="specialtyModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title" id="specialtyModalTitle"></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body" id="specialtyModalBody"></div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function () {
    var loginModal = null;
    var specModal  = null;

    function abrirModalConRol(rol) {
        var titles = {
            paciente:      '<i class="fa-solid fa-user-injured me-2"></i>Acceso Paciente',
            medico:        '<i class="fa-solid fa-user-md me-2"></i>Acceso Médico',
            asistente:     '<i class="fa-solid fa-clipboard-list me-2"></i>Acceso Asistente',
            administrador: '<i class="fa-solid fa-sliders me-2"></i>Acceso Administrador'
        };
        if (!titles[rol]) return false;

        $('#modalTitle').html(titles[rol]);
        $('#rol').val(rol);

        if (rol === 'paciente' || rol === 'medico') {
            $('#registerLink').html('<a href="' + APP_URL + '/registro/' + rol + '">¿No tienes cuenta? Regístrate aquí</a>').show();
        } else {
            $('#registerLink').html('<small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i>Cuentas administrativas protegidas.</small>').show();
        }

        $('#cedula, #password').val('');
        $('#loginError').hide();

        if (!loginModal) loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
        return true;
    }

    var urlParams      = new URLSearchParams(window.location.search);
    var openLoginParam = urlParams.get('openLogin');

    if (openLoginParam) {
        setTimeout(function () {
            if (abrirModalConRol(openLoginParam)) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }, 200);
    }

    if (typeof openLoginRol !== 'undefined' && openLoginRol) {
        setTimeout(function () { abrirModalConRol(openLoginRol); }, 200);
    }

    $('.access-card').on('click', function () {
        abrirModalConRol($(this).data('rol'));
    });

    $('#loginForm').on('submit', function (e) {
        e.preventDefault();

        var $btn = $(this).find('button[type="submit"]');
        var orig = $btn.html();

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verificando...');

        $.ajax({
            url:      APP_URL + '/login',
            type:     'POST',
            data:     { user: $('#cedula').val().trim(), pass: $('#password').val(), rol: $('#rol').val() },
            dataType: 'json',
            timeout:  15000,
            success: function (r) {
                if (r.success) {
                    $btn.html('<i class="fas fa-check-circle"></i> ¡Acceso concedido!');
                    window.location.href = APP_URL + '/panel/' + $('#rol').val();
                } else {
                    $('#loginError').text(r.message || r.error || 'Cédula o contraseña incorrecta').fadeIn();
                    setTimeout(function () { $('#loginError').fadeOut(); }, 5000);
                    $btn.prop('disabled', false).html(orig);
                }
            },
            error: function (xhr) {
                var m = xhr.status === 404 ? 'Servidor no responde.'
                      : xhr.status === 500 ? 'Error interno.'
                      : 'Error de conexión.';
                $('#loginError').text(m).fadeIn();
                setTimeout(function () { $('#loginError').fadeOut(); }, 5000);
                $btn.prop('disabled', false).html(orig);
            }
        });
    });

    var si = {
        general:      { t: 'Medicina General', d: 'Atención integral para adultos y niños.',               s: ['Consulta general', 'Chequeos preventivos', 'Vacunación', 'Exámenes de rutina'] },
        cardiologia:  { t: 'Cardiología',       d: 'Diagnóstico y tratamiento de enfermedades del corazón.', s: ['Electrocardiograma', 'Ecocardiograma', 'Prueba de esfuerzo', 'Cateterismo'] },
        neumonologia: { t: 'Neumología',        d: 'Tratamiento de enfermedades respiratorias y pulmonares.',   s: ['Espirometría', 'Broncoscopía', 'Pruebas de alergia', 'Tratamiento del asma'] },
        psicologia:   { t: 'Psicología',        d: 'Apoyo emocional y mental para mejorar tu bienestar.',      s: ['Terapia individual', 'Terapia de pareja', 'Psicoterapia', 'Evaluación psicológica'] },
        pediatria:    { t: 'Pediatría',          d: 'Cuidado especializado para niños desde el nacimiento.',   s: ['Control de crecimiento', 'Vacunación infantil', 'Chequeos pediátricos', 'Urgencias'] },
        ginecologia:  { t: 'Ginecología',       d: 'Atención integral en salud femenina y reproductiva.',     s: ['Consulta ginecológica', 'Control prenatal', 'Ecografía obstétrica', 'Planificación familiar'] }
    };

    $('.specialty-card').on('click', function () {
        var info = si[$(this).data('specialty')];
        if (!info) return;

        $('#specialtyModalTitle').text(info.t);
        $('#specialtyModalBody').html(
            '<p>' + info.d + '</p>' +
            '<h6 class="mt-3 mb-2">Servicios:</h6>' +
            '<ul>' + info.s.map(function (s) { return '<li>' + s + '</li>'; }).join('') + '</ul>'
        );

        if (!specModal) specModal = new bootstrap.Modal(document.getElementById('specialtyModal'));
        specModal.show();
    });

    var counted = false;
    var obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting && !counted) {
                counted = true;
                $('.stat-number[data-count]').each(function () {
                    var $el  = $(this);
                    var t    = parseInt($el.data('count'));
                    var c    = 0;
                    var step = Math.max(1, Math.floor(t / 40));
                    var timer = setInterval(function () {
                        c += step;
                        if (c >= t) { c = t; clearInterval(timer); }
                        $el.text(c);
                    }, 40);
                });
            }
        });
    }, { threshold: .3 });

    var aboutEl = document.querySelector('.about-section');
    if (aboutEl) obs.observe(aboutEl);
});

function showMap(l) {
    var urls = {
        'caracas':      'https://maps.google.com/?q=Caracas,Venezuela',
        'caracas-este': 'https://maps.google.com/?q=Caracas+Este,Venezuela',
        'maracaibo':    'https://maps.google.com/?q=Maracaibo,Zulia,Venezuela'
    };
    if (urls[l]) window.open(urls[l], '_blank');
}
</script>
</body>
</html>