<?php
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_asistente = $id_asistente ?? $_SESSION['usuario'] ?? 0;
?>
<!-- CSS Adicional para esta vista -->
<style>
    .registro-paciente-container {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .registro-header {
        background: linear-gradient(135deg, #9333ea, #7c3aed);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        color: white;
    }
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .form-section h4 {
        font-size: 1rem;
        font-weight: 700;
        color: #9333ea;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #eef2f6;
    }
    .form-group label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
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
    .btn-registrar {
        background: linear-gradient(135deg, #9333ea, #7c3aed);
        border: none;
        border-radius: 12px;
        padding: 0.8rem 2rem;
        font-size: 1rem;
        font-weight: 700;
        color: white;
        transition: all 0.3s;
    }
    .btn-registrar:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(147,51,234,0.3);
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
        border: 2px solid #e2e8f0;
    }
    .step.active .step-circle {
        background: linear-gradient(135deg, #9333ea, #7c3aed);
        color: white;
        border-color: #9333ea;
        box-shadow: 0 4px 12px rgba(147,51,234,0.3);
    }
    .step.completed .step-circle {
        background: #9333ea;
        color: white;
        border-color: #9333ea;
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
        color: #9333ea;
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
        background: linear-gradient(90deg, #9333ea, #9333ea);
    }
    .step .step-circle {
        position: relative;
        z-index: 1;
        background: white;
    }
    .blood-type-field {
        background: #fef3c7;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-user-plus"></i> Registro de Paciente</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/asistente">Inicio</a></li>
                    <li class="breadcrumb-item active">Registro de Paciente</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="registro-paciente-container">
            <div class="registro-header">
                <h2><i class="fas fa-user-injured me-2"></i> Registrar Nuevo Paciente</h2>
                <p class="mb-0 opacity-75">Complete el formulario para registrar un nuevo paciente en el sistema</p>
            </div>

            <?php
            $securityPath = dirname(__DIR__) . '/../modelo/Security.php';
            if (!file_exists($securityPath)) {
                die("Error: No se encuentra Security.php");
            }
            include_once $securityPath;
            ?>
            
            <!-- Alertas -->
            <div class="alert alert-success" id="alert-success" style="display:none; border-radius: 12px; border: none;">
                <i class="fas fa-check-circle"></i> <span id="success-message"></span>
            </div>
            <div class="alert alert-danger" id="alert-error" style="display:none; border-radius: 12px; border: none;">
                <i class="fas fa-exclamation-circle"></i> <span id="error-message"></span>
            </div>
            
            <!-- Step Indicator -->
            <div class="step-indicator" id="stepIndicator">
                <div class="step active" data-step="1">
                    <div class="step-circle"><span>1</span></div>
                    <div class="step-label">Datos Personales</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-circle"><span>2</span></div>
                    <div class="step-label">Ubicación</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-circle"><span>3</span></div>
                    <div class="step-label">Contacto y Seguridad</div>
                </div>
            </div>
            
            <form id="form-registro-paciente">
                <?php echo Security::campoCSRF(); ?>
                
                <!-- Campos ocultos para IDs de ubicación -->
                <input type="hidden" id="estado_id" name="estado_id" value="">
                <input type="hidden" id="ciudad_id" name="ciudad_id" value="">
                <input type="hidden" id="municipio_id" name="municipio_id" value="">
                <input type="hidden" id="parroquia_id" name="parroquia_id" value="">
                <input type="hidden" id="id_asistente" name="id_asistente" value="<?php echo $id_asistente; ?>">
                
                <!-- Paso 1: Datos Personales -->
                <div id="step1" class="step-content">
                    <div class="form-section">
                        <h4><i class="fas fa-user-circle"></i> Datos Personales</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           placeholder="Ingrese el nombre" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label for="apellidos">Apellido</label>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos" 
                                           placeholder="Ingrese el apellido" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label for="cedula">Cédula de Identidad</label>
                                    <input type="text" class="form-control" id="cedula" name="cedula" 
                                           placeholder="Ej: 12345678" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label for="telefono">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           placeholder="Ej: 04141234567" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label for="sexo">Sexo</label>
                                    <select class="form-control" id="sexo" name="sexo" required>
                                        <option value="">Seleccione...</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <button type="button" class="btn btn-primary btn-next" data-next="2">
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Paso 2: Sistema de Ubicación -->
                <div id="step2" class="step-content" style="display:none;">
                    <div class="form-section">
                        <h4><i class="fas fa-map-marker-alt"></i> Ubicación</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label for="estado">Estado</label>
                                    <select class="form-control" id="estado" name="estado" required>
                                        <option value="">Seleccione un estado...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label for="ciudad">Ciudad</label>
                                    <select class="form-control" id="ciudad" name="ciudad" required disabled>
                                        <option value="">Primero seleccione un estado...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="municipio">Municipio</label>
                                    <select class="form-control" id="municipio" name="municipio" disabled>
                                        <option value="">Primero seleccione un estado...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parroquia">Parroquia</label>
                                    <select class="form-control" id="parroquia" name="parroquia" disabled>
                                        <option value="">Primero seleccione un municipio...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group required">
                            <label for="direccion_detallada">Dirección Detallada</label>
                            <input type="text" class="form-control" id="direccion_detallada" name="direccion_detallada" required 
                                   placeholder="Av. Principal, Edificio, Número, etc.">
                            <small class="form-text text-muted">Ej: Av. Principal, Edificio Central, Casa #123, Urbanización Las Delicias</small>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-prev" data-prev="1">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="button" class="btn btn-primary btn-next" data-next="3">
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Paso 3: Contacto y Seguridad -->
                <div id="step3" class="step-content" style="display:none;">
                    <div class="form-section">
                        <h4><i class="fas fa-envelope"></i> Información de Contacto</h4>
                        
                        <div class="form-group required">
                            <label for="correo">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" 
                                   placeholder="ejemplo@correo.com" required>
                            <small class="form-text text-muted">Usaremos este correo para comunicaciones</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_sangre">Tipo de Sangre</label>
                            <select class="form-control blood-type-field" id="tipo_sangre" name="tipo_sangre">
                                <option value="">Seleccione su tipo de sangre...</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                            <small class="form-text text-muted">Información importante para emergencias médicas</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="adicional">Información Adicional</label>
                            <textarea class="form-control" id="adicional" name="adicional" rows="2" 
                                      placeholder="Alergias, condiciones médicas, medicamentos que toma regularmente, etc."></textarea>
                            <small class="form-text text-muted">Esta información ayudará a los médicos a conocer mejor su historial</small>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h4><i class="fas fa-lock"></i> Seguridad</h4>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label for="pass">Contraseña</label>
                                    <input type="password" class="form-control" id="pass" name="pass" 
                                           placeholder="Mínimo 6 caracteres" required>
                                    <small class="form-text text-muted">Use una contraseña segura</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group required">
                                    <label for="confirm_pass">Confirmar Contraseña</label>
                                    <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" 
                                           placeholder="Repita su contraseña" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h4><i class="fas fa-shield-alt"></i> Preguntas de Seguridad</h4>
                        <p class="text-muted small mb-3">Selecciona 3 preguntas de seguridad y responde cada una para recuperar la cuenta en caso de olvidar la contraseña.</p>
                        
                        <div class="form-group required">
                            <label for="pregunta1">Pregunta 1</label>
                            <select class="form-control pregunta-select" id="pregunta1" name="pregunta1" required>
                                <option value="">-- Selecciona una pregunta --</option>
                                <option value="¿Cuál es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                                <option value="¿En qué ciudad naciste?">¿En qué ciudad naciste?</option>
                                <option value="¿Cuál es el apellido de soltera de tu madre?">¿Cuál es el apellido de soltera de tu madre?</option>
                                <option value="¿Cuál fue tu primer trabajo?">¿Cuál fue tu primer trabajo?</option>
                                <option value="¿Cuál es tu comida favorita?">¿Cuál es tu comida favorita?</option>
                            </select>
                            <input type="text" class="form-control mt-2 respuesta-input" id="respuesta1" name="respuesta1" 
                                   placeholder="Tu respuesta" required>
                        </div>
                        
                        <div class="form-group required">
                            <label for="pregunta2">Pregunta 2</label>
                            <select class="form-control pregunta-select" id="pregunta2" name="pregunta2" required>
                                <option value="">-- Selecciona una pregunta --</option>
                                <option value="¿Cuál es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                                <option value="¿En qué ciudad naciste?">¿En qué ciudad naciste?</option>
                                <option value="¿Cuál es el apellido de soltera de tu madre?">¿Cuál es el apellido de soltera de tu madre?</option>
                                <option value="¿Cuál fue tu primer trabajo?">¿Cuál fue tu primer trabajo?</option>
                                <option value="¿Cuál es tu comida favorita?">¿Cuál es tu comida favorita?</option>
                            </select>
                            <input type="text" class="form-control mt-2 respuesta-input" id="respuesta2" name="respuesta2" 
                                   placeholder="Tu respuesta" required>
                        </div>
                        
                        <div class="form-group required">
                            <label for="pregunta3">Pregunta 3</label>
                            <select class="form-control pregunta-select" id="pregunta3" name="pregunta3" required>
                                <option value="">-- Selecciona una pregunta --</option>
                                <option value="¿Cuál es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                                <option value="¿En qué ciudad naciste?">¿En qué ciudad naciste?</option>
                                <option value="¿Cuál es el apellido de soltera de tu madre?">¿Cuál es el apellido de soltera de tu madre?</option>
                                <option value="¿Cuál fue tu primer trabajo?">¿Cuál fue tu primer trabajo?</option>
                                <option value="¿Cuál es tu comida favorita?">¿Cuál es tu comida favorita?</option>
                            </select>
                            <input type="text" class="form-control mt-2 respuesta-input" id="respuesta3" name="respuesta3" 
                                   placeholder="Tu respuesta" required>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-prev" data-prev="2">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                        <button type="submit" class="btn btn-registrar">
                            <i class="fas fa-check-circle"></i> Registrar Paciente
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="<?php echo APP_URL; ?>/js/registro_ubicacion.js"></script>
<script>
$(document).ready(function() {
    console.log('=== FORMULARIO DE REGISTRO DE PACIENTE POR ASISTENTE ===');
    
    // Variables de navegación entre pasos
    let currentStep = 1;
    const totalSteps = 3;
    
    // Función para cambiar de paso
    function goToStep(step) {
        $('#step1, #step2, #step3').hide();
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
        
        // Scroll al inicio del formulario
        $('html, body').animate({
            scrollTop: $('.registro-paciente-container').offset().top - 20
        }, 300);
    }
    
    // Eventos de navegación
    $('.btn-next').click(function() {
        let nextStep = $(this).data('next');
        
        // Validar campos del paso actual antes de avanzar
        if (currentStep === 1) {
            if (!validarPaso1()) return;
        } else if (currentStep === 2) {
            if (!validarPaso2()) return;
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
    
    // Validación del Paso 1 (Datos Personales)
    function validarPaso1() {
        let nombre = $('#nombre').val().trim();
        let apellidos = $('#apellidos').val().trim();
        let fecha_nacimiento = $('#fecha_nacimiento').val();
        let cedula = $('#cedula').val().trim();
        let telefono = $('#telefono').val().trim();
        let sexo = $('#sexo').val();
        
        if (!nombre) {
            mostrarError('El nombre es requerido');
            $('#nombre').focus();
            return false;
        }
        if (!apellidos) {
            mostrarError('Los apellidos son requeridos');
            $('#apellidos').focus();
            return false;
        }
        if (!fecha_nacimiento) {
            mostrarError('La fecha de nacimiento es requerida');
            $('#fecha_nacimiento').focus();
            return false;
        }
        if (!cedula) {
            mostrarError('La cédula es requerida');
            $('#cedula').focus();
            return false;
        }
        if (!telefono) {
            mostrarError('El teléfono es requerido');
            $('#telefono').focus();
            return false;
        }
        if (!sexo) {
            mostrarError('Debe seleccionar el sexo');
            $('#sexo').focus();
            return false;
        }
        
        return true;
    }
    
    // Validación del Paso 2 (Ubicación)
    function validarPaso2() {
        let estado_val = $('#estado').val();
        let ciudad_val = $('#ciudad').val();
        let direccion_detallada = $('#direccion_detallada').val().trim();
        
        if (!estado_val) {
            mostrarError('Debe seleccionar un estado');
            $('#estado').focus();
            return false;
        }
        
        if (!ciudad_val) {
            mostrarError('Debe seleccionar una ciudad');
            $('#ciudad').focus();
            return false;
        }
        
        if (!direccion_detallada) {
            mostrarError('Debe ingresar la dirección detallada');
            $('#direccion_detallada').focus();
            return false;
        }
        
        return true;
    }
    
    // Función para actualizar los IDs de ubicación
    function actualizarIdsUbicacion() {
        $('#estado_id').val($('#estado').val() || '');
        $('#ciudad_id').val($('#ciudad').val() || '');
        $('#municipio_id').val($('#municipio').val() || '');
        $('#parroquia_id').val($('#parroquia').val() || '');
    }
    
    // Actualizar IDs cuando cambien los selects
    $(document).on('change', '#estado, #ciudad, #municipio, #parroquia', function() {
        actualizarIdsUbicacion();
    });
    
    // Envío del formulario
    $('#form-registro-paciente').submit(function(e) {
        e.preventDefault();
        
        var pass = $('#pass').val();
        var confirm_pass = $('#confirm_pass').val();
        
        if(pass !== confirm_pass) {
            mostrarError('Las contraseñas no coinciden');
            $('#pass').focus();
            return false;
        }
        
        if(pass.length < 6) {
            mostrarError('La contraseña debe tener al menos 6 caracteres');
            $('#pass').focus();
            return false;
        }
        
        // Validar paso 3 (correo)
        var correo = $('#correo').val().trim();
        if (!correo) {
            mostrarError('El correo electrónico es requerido');
            $('#correo').focus();
            return false;
        }
        
        // Validar formato de correo
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo)) {
            mostrarError('Ingrese un correo electrónico válido');
            $('#correo').focus();
            return false;
        }
        
        // Validar ubicación nuevamente antes de enviar
        if (!validarPaso2()) {
            goToStep(2);
            return false;
        }
        
        // Enviar formulario
        var formData = $(this).serialize();
        
        $.ajax({
            url: APP_URL + '/api/asistentes/registrar-paciente',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success) {
                    mostrarExito('Paciente registrado correctamente');
                    
                    // Redirigir al panel del asistente después de 2 segundos
                    setTimeout(function() {
                        window.location.href = APP_URL + '/panel/asistente';
                    }, 2000);
                } else {
                    mostrarError(response.message || 'Error al registrar paciente');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al registrar paciente:', error);
                mostrarError('Error al registrar paciente. Intente nuevamente.');
            }
        });
    });
    
    function mostrarError(mensaje) {
        $('#error-message').text(mensaje);
        $('#alert-error').show();
        $('#alert-success').hide();
        
        setTimeout(function() {
            $('#alert-error').fadeOut();
        }, 5000);
    }
    
    function mostrarExito(mensaje) {
        $('#success-message').text(mensaje);
        $('#alert-success').show();
        $('#alert-error').hide();
        
        setTimeout(function() {
            $('#alert-success').fadeOut();
        }, 5000);
    }
});
</script>
