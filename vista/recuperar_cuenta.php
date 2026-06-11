<?php
// vista/recuperar_cuenta.php - Recuperación de Cuenta con Preguntas de Seguridad
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Cuenta - BioVital</title>
    
    <!-- Variables globales JavaScript -->
    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
    </script>
    
    <!-- CSS Globales -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .recovery-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        
        .recovery-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        
        .recovery-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .recovery-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .recovery-body {
            padding: 2rem;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }
        
        .step {
            text-align: center;
            flex: 1;
            position: relative;
        }
        
        .step .step-circle {
            width: 40px;
            height: 40px;
            background: #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-weight: 700;
            color: #64748b;
            transition: all 0.3s;
        }
        
        .step.active .step-circle {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 12px rgba(102,126,234,0.3);
        }
        
        .step.completed .step-circle {
            background: #667eea;
            color: white;
        }
        
        .step.completed .step-circle::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }
        
        .step.completed .step-circle span {
            display: none;
        }
        
        .step .step-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #94a3b8;
        }
        
        .step.active .step-label {
            color: #667eea;
        }
        
        .step:not(:last-child)::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e2e8f0;
            z-index: 0;
        }
        
        .step.completed:not(:last-child)::before {
            background: linear-gradient(90deg, #667eea, #667eea);
        }
        
        .step .step-circle {
            position: relative;
            z-index: 1;
            background: white;
            border: 2px solid #e2e8f0;
        }
        
        .step.active .step-circle {
            border-color: #667eea;
        }
        
        .step.completed .step-circle {
            border-color: #667eea;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-group label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            padding: 0.6rem 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 12px;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            font-weight: 700;
            transition: all 0.3s;
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102,126,234,0.3);
        }
        
        .btn-secondary {
            border-radius: 12px;
            padding: 0.8rem 2rem;
            font-weight: 600;
        }
        
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .security-question {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }
        
        .security-question h5 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .success-icon {
            font-size: 4rem;
            color: #10b981;
            margin-bottom: 1rem;
        }
        
        .user-info {
            background: #f0f9ff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #0ea5e9;
        }
        
        .user-info h5 {
            color: #0ea5e9;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .user-info p {
            margin-bottom: 0.5rem;
            color: #475569;
        }
        
        .user-info strong {
            color: #1e293b;
        }
    </style>
</head>
<body>
    <div class="recovery-card">
        <div class="recovery-header">
            <h2><i class="fas fa-key me-2"></i> Recuperar Cuenta</h2>
            <p>Verifica tu identidad con tus preguntas de seguridad</p>
        </div>
        <div class="recovery-body">
            <!-- Step Indicator -->
            <div class="step-indicator" id="stepIndicator">
                <div class="step active" data-step="1">
                    <div class="step-circle"><span>1</span></div>
                    <div class="step-label">Buscar Usuario</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle"><span>2</span></div>
                    <div class="step-label">Verificar</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle"><span>3</span></div>
                    <div class="step-label">Recuperar</div>
                </div>
            </div>
            
            <!-- Alertas -->
            <div class="alert alert-success alert-custom" id="alert-success" style="display:none;">
                <i class="fas fa-check-circle"></i> <span id="success-message"></span>
            </div>
            <div class="alert alert-danger alert-custom" id="alert-error" style="display:none;">
                <i class="fas fa-exclamation-circle"></i> <span id="error-message"></span>
            </div>
            
            <!-- Paso 1: Buscar Usuario -->
            <div id="step1" class="step-content">
                <form id="form-buscar">
                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" 
                               placeholder="ejemplo@correo.com" required>
                        <small class="form-text text-muted">Ingresa el correo con el que te registraste</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Buscar Cuenta
                    </button>
                </form>
                <div class="back-link">
                    <a href="<?php echo APP_URL; ?>/">
                        <i class="fas fa-arrow-left"></i> Volver al inicio
                    </a>
                </div>
            </div>
            
            <!-- Paso 2: Preguntas de Seguridad -->
            <div id="step2" class="step-content" style="display:none;">
                <div id="preguntas-container">
                    <!-- Las preguntas se cargarán dinámicamente -->
                </div>
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-prev" data-prev="1">
                        <i class="fas fa-arrow-left"></i> Atrás
                    </button>
                    <button type="button" class="btn btn-primary btn-next" data-next="3">
                        Verificar Respuestas <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- Paso 3: Opciones de Recuperación -->
            <div id="step3" class="step-content" style="display:none;">
                <div class="user-info" id="user-info">
                    <!-- Información del usuario se cargará dinámicamente -->
                </div>
                
                <div class="form-group">
                    <label>¿Qué deseas recuperar?</label>
                    <div class="btn-group-vertical w-100">
                        <button type="button" class="btn btn-outline-primary mb-2" id="btn-recuperar-usuario">
                            <i class="fas fa-user me-2"></i> Ver mi nombre de usuario
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btn-recuperar-password">
                            <i class="fas fa-lock me-2"></i> Cambiar mi contraseña
                        </button>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-prev" data-prev="2">
                        <i class="fas fa-arrow-left"></i> Atrás
                    </button>
                </div>
            </div>
            
            <!-- Paso 4: Mostrar Usuario -->
            <div id="step4" class="step-content" style="display:none;">
                <div class="text-center">
                    <i class="fas fa-user-circle success-icon"></i>
                    <h4>Tu nombre de usuario</h4>
                    <div class="user-info">
                        <p><strong>Cédula:</strong> <span id="usuario-cedula"></span></p>
                        <p><strong>Nombre:</strong> <span id="usuario-nombre"></span></p>
                        <p><strong>Rol:</strong> <span id="usuario-rol"></span></p>
                    </div>
                    <button type="button" class="btn btn-primary" id="btn-login-ahora">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión Ahora
                    </button>
                </div>
            </div>
            
            <!-- Paso 5: Cambiar Contraseña -->
            <div id="step5" class="step-content" style="display:none;">
                <form id="form-cambiar-password">
                    <div class="form-group">
                        <label for="nueva-password">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="nueva-password" name="nueva_password" 
                               placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmar-password">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirmar-password" name="confirmar_password" 
                               placeholder="Repite tu contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Guardar Nueva Contraseña
                    </button>
                </form>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary btn-prev" data-prev="3">
                        <i class="fas fa-arrow-left"></i> Atrás
                    </button>
                </div>
            </div>
        </div>
    </div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    console.log('=== RECUPERACIÓN DE CUENTA ===');
    
    if (typeof APP_URL === 'undefined') {
        console.error('ERROR: APP_URL no está definida');
        window.APP_URL = '';
    }
    
    let currentStep = 1;
    const totalSteps = 5;
    let usuarioData = null;
    let preguntasSeguridad = [];
    
    // Función para cambiar de paso
    function goToStep(step) {
        $('.step-content').hide();
        $(`#step${step}`).show();
        
        $('.step').removeClass('active completed');
        for (let i = 1; i <= totalSteps; i++) {
            if (i < step) {
                $(`.step[data-step="${i}"]`).addClass('completed');
            } else if (i === step) {
                $(`.step[data-step="${i}"]`).addClass('active');
            }
        }
        
        currentStep = step;
        
        $('html, body').animate({
            scrollTop: $('.recovery-card').offset().top - 20
        }, 300);
    }
    
    // Eventos de navegación
    $('.btn-next').click(function() {
        let nextStep = $(this).data('next');

        // En el paso 2, verificar respuestas via AJAX antes de avanzar
        if (currentStep === 2) {
            let respuestas = [];
            let todasRespondidas = true;

            $('.respuesta-pregunta').each(function() {
                let respuesta = $(this).val().trim();
                let index = $(this).data('index');

                if (!respuesta) {
                    todasRespondidas = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                    respuestas[index] = respuesta;
                }
            });

            if (!todasRespondidas) {
                mostrarError('Debes responder todas las preguntas');
                return;
            }

            verificarRespuestas(respuestas); // goToStep(3) se llama internamente al éxito
            return; // No avanzar hasta confirmar respuestas correctas
        }

        if (nextStep && nextStep <= totalSteps) {
            goToStep(nextStep);
        }
    });

    $('.btn-prev').click(function() {
        let prevStep = $(this).data('prev');
        if (prevStep && prevStep >= 1) {
            goToStep(prevStep);
        }
    });
    
    // Paso 1: Buscar usuario por correo
    $('#form-buscar').submit(function(e) {
        e.preventDefault();
        
        let correo = $('#correo').val().trim();
        
        if (!correo) {
            mostrarError('El correo es requerido');
            return;
        }
        
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo)) {
            mostrarError('Ingrese un correo válido');
            return;
        }
        
        let $btn = $(this).find('button[type="submit"]');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Buscando...');
        
        $.ajax({
            url: APP_URL + '/api/recuperar/buscar-usuario',
            type: 'POST',
            data: { correo: correo },
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta buscar usuario:', response);
                
                if (response.success) {
                    usuarioData = response.data.usuario;
                    preguntasSeguridad = response.data.preguntas;
                    
                    // Cargar preguntas
                    cargarPreguntas(preguntasSeguridad);
                    
                    // Mostrar información del usuario
                    mostrarInfoUsuario(usuarioData);
                    
                    goToStep(2);
                    mostrarExito('Usuario encontrado. Responde las preguntas de seguridad.');
                } else {
                    mostrarError(response.message || 'No se encontró ningún usuario con ese correo');
                }
                
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr, status, error) {
                console.error('Error buscando usuario:', error);
                mostrarError('Error de conexión. Intente nuevamente.');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Cargar preguntas de seguridad
    function cargarPreguntas(preguntas) {
        let container = $('#preguntas-container');
        container.empty();
        
        preguntas.forEach((pregunta, index) => {
            let html = `
                <div class="security-question">
                    <h5><i class="fas fa-question-circle me-2"></i> Pregunta ${index + 1}</h5>
                    <p class="mb-3">${pregunta.pregunta}</p>
                    <div class="form-group">
                        <input type="text" class="form-control respuesta-pregunta" 
                               data-index="${index}" 
                               placeholder="Tu respuesta" required>
                    </div>
                </div>
            `;
            container.append(html);
        });
    }
    
    // Mostrar información del usuario
    function mostrarInfoUsuario(usuario) {
        let html = `
            <h5><i class="fas fa-user me-2"></i> Usuario Encontrado</h5>
            <p><strong>Nombre:</strong> ${usuario.nombre} ${usuario.apellido}</p>
            <p><strong>Rol:</strong> ${usuario.rol}</p>
        `;
        $('#user-info').html(html);
    }
    
    // Paso 2: La verificación de respuestas se maneja en el evento .btn-next unificado de arriba
    
    function verificarRespuestas(respuestas) {
        let $btn = $('.btn-next');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Verificando...');
        
        $.ajax({
            url: APP_URL + '/api/recuperar/verificar-respuestas',
            type: 'POST',
            data: {
                id_usuario: usuarioData.id,
                respuestas: respuestas
            },
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta verificar:', response);
                
                if (response.success) {
                    mostrarExito('Respuestas correctas. Elige qué deseas recuperar.');
                    goToStep(3);
                } else {
                    mostrarError('Una o más respuestas son incorrectas. Intente nuevamente.');
                }
                
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr, status, error) {
                console.error('Error verificando respuestas:', error);
                mostrarError('Error de conexión. Intente nuevamente.');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }
    
    // Paso 3: Opciones de recuperación
    $('#btn-recuperar-usuario').click(function() {
        $('#usuario-cedula').text(usuarioData.cedula);
        $('#usuario-nombre').text(usuarioData.nombre + ' ' + usuarioData.apellido);
        $('#usuario-rol').text(usuarioData.rol);
        
        goToStep(4);
    });
    
    $('#btn-recuperar-password').click(function() {
        goToStep(5);
    });
    
    // Paso 4: Iniciar sesión
    $('#btn-login-ahora').click(function() {
        window.location.href = APP_URL + '/';
    });
    
    // Paso 5: Cambiar contraseña
    $('#form-cambiar-password').submit(function(e) {
        e.preventDefault();
        
        let nuevaPassword = $('#nueva-password').val();
        let confirmarPassword = $('#confirmar-password').val();
        
        if (nuevaPassword.length < 6) {
            mostrarError('La contraseña debe tener al menos 6 caracteres');
            return;
        }
        
        if (nuevaPassword !== confirmarPassword) {
            mostrarError('Las contraseñas no coinciden');
            return;
        }
        
        let $btn = $(this).find('button[type="submit"]');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/recuperar/cambiar-password',
            type: 'POST',
            data: {
                id_usuario: usuarioData.id,
                rol: usuarioData.rol,
                nueva_password: nuevaPassword
            },
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                console.log('Respuesta cambiar password:', response);
                
                if (response.success) {
                    mostrarExito('Contraseña cambiada exitosamente. Ya puedes iniciar sesión.');
                    setTimeout(function() {
                        window.location.href = APP_URL + '/';
                    }, 2000);
                } else {
                    mostrarError(response.message || 'Error al cambiar la contraseña');
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cambiando password:', error);
                mostrarError('Error de conexión. Intente nuevamente.');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    function mostrarError(mensaje) {
        $('#error-message').text(mensaje);
        $('#alert-error').fadeIn(300);
        setTimeout(function() { $('#alert-error').fadeOut(500); }, 5000);
        $('html, body').animate({ scrollTop: $('#alert-error').offset().top - 100 }, 500);
    }
    
    function mostrarExito(mensaje) {
        $('#success-message').text(mensaje);
        $('#alert-success').fadeIn(300);
        setTimeout(function() { $('#alert-success').fadeOut(500); }, 3000);
    }
});
</script>

</body>
</html>
