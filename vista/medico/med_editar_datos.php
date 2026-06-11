<?php
// vista/medico/med_editar_datos.php
// Perfil del Médico - Edición de datos personales
// Incluye sistema de ubicación, cambio de foto, cambio de contraseña y MPPS Registro

$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_medico = $id_medico ?? $_SESSION['usuario'] ?? 0;
$avatar_actual = $avatar_actual ?? (!empty($_SESSION['avatar']) ? $_SESSION['avatar'] : APP_URL . '/img/avatarDES.jpg');
?>

<!-- CSS Adicional para esta vista -->
<style>
    .profile-header {
        background: linear-gradient(135deg, #0d9488, #0f766e);
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
        border-bottom: 2px solid #0d9488;
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
        background: linear-gradient(135deg, #0d9488, #0f766e);
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-editar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13,148,136,0.3);
    }
    .form-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .form-card .card-header {
        background: white;
        border-bottom: 2px solid #0d9488;
        padding: 1rem 1.5rem;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 0.6rem 1rem;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0d9488;
        box-shadow: 0 0 0 3px rgba(13,148,136,0.1);
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
        background: #0d9488;
        color: white;
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
    .info-card .fa,
    .info-card .fas {
        width: 20px;
        color: #0d9488;
    }
    .mpps-badge {
        background: #fef3c7;
        color: #92400e;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
    }
    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0d9488;
        margin: 1.5rem 0 1rem 0;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #eef2f6;
    }
    .section-title i {
        margin-right: 0.5rem;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-md"></i> Mi Perfil Profesional</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/medico">Inicio</a></li>
                    <li class="breadcrumb-item active">Mi Perfil</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_usuario" value="<?php echo htmlspecialchars($id_medico); ?>">
        
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
                            <i class="fas fa-user-md"></i> Médico
                        </span>
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
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-stethoscope me-1"></i> Registro MPPS</div>
                            <div class="info-value">
                                <span class="mpps-badge" id="mpps_registro_us">-</span>
                            </div>
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
                                <small class="form-text text-muted">Número de contacto profesional</small>
                            </div>
                            
                            <!-- Sistema de Ubicación -->
                            <h5 class="section-title">
                                <i class="fas fa-map-marker-alt"></i> Ubicación
                            </h5>

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
                                <small class="form-text text-muted">Ej: Av. Principal, Edificio Central, Consultorio 305</small>
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
                            
                            <!-- MPPS Registro - Campo específico para médicos -->
                            <div class="form-group">
                                <label for="mpps_registro">Registro MPPS</label>
                                <input type="text" id="mpps_registro" class="form-control" 
                                       placeholder="Ej: MPPS-12345" disabled>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Número de registro en el Ministerio del Poder Popular para la Salud
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label for="adicional">Información adicional</label>
                                <textarea class="form-control" id="adicional" rows="3" 
                                          placeholder="Especialidad, años de experiencia, consultorio, etc..." disabled></textarea>
                                <small class="form-text text-muted">Comparte información relevante sobre tu práctica médica</small>
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
            <div class="modal-header" style="background: linear-gradient(135deg, #0d9488, #0f766e); color: white; border-radius: 16px 16px 0 0;">
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
            <div class="modal-header" style="background: linear-gradient(135deg, #0d9488, #0f766e); color: white; border-radius: 16px 16px 0 0;">
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

<!-- Scripts -->
<script src="<?php echo APP_URL; ?>/js/ubicacion.js"></script>
<script src="<?php echo APP_URL; ?>/js/medico.js"></script>

<script>
$(document).ready(function() {
    console.log('=== PERFIL MÉDICO - INICIALIZANDO ===');
    console.log('ID Usuario:', $('#id_usuario').val());
    console.log('APP_URL:', APP_URL);
    
    // Verificar que el ID de usuario existe
    var id_usuario = $('#id_usuario').val();
    if (!id_usuario || id_usuario === '0' || id_usuario === '') {
        console.error('ERROR: ID de médico no encontrado');
        $('#nombre_us').html('Error: Sesión no válida');
        mostrarToast('Error de sesión. Por favor inicie sesión nuevamente.', 'error');
        return;
    }
    
    // Función para mostrar toasts de notificación
    window.mostrarToast = function(mensaje, tipo) {
        var icono = tipo === 'success' ? 'fa-check-circle' : (tipo === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
        var color = tipo === 'success' ? '#10b981' : (tipo === 'error' ? '#ef4444' : '#3b82f6');
        
        var toastHtml = `
            <div class="toast align-items-center text-white border-0 position-fixed" 
                 style="top: 70px; right: 20px; z-index: 9999; min-width: 280px; background: ${color}; border-radius: 12px;" 
                 role="alert" aria-live="assertive" aria-atomic="true" data-autohide="true" data-delay="4000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${icono} me-2"></i>
                        ${mensaje}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast">×</button>
                </div>
            </div>
        `;
        
        $('body').append(toastHtml);
        var toast = $('.toast').last();
        
        setTimeout(function() {
            toast.fadeOut(300, function() { $(this).remove(); });
        }, 4000);
    };
    
    // Exportar función al scope global
    window.mostrarAlerta = window.mostrarToast;
    
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

</body>
</html>