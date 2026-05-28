<?php
// vista/registro_pac.php
// Registro de pacientes - Versión corregida con APP_URL unificada

// Incluir seguridad
$securityPath = dirname(__DIR__) . '/modelo/Security.php';
if (!file_exists($securityPath)) {
    die("Error: No se encuentra Security.php en: " . $securityPath);
}
include_once $securityPath;

$titulo_pagina = 'Registro de Paciente - BioVital';
include_once VIEW_PATH . '/layouts/head.php';
?>

<style>
    .registro-container {
        max-width: 800px;
        margin: 2rem auto;
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    .registro-header {
        background: #1a6b9e;
        color: white;
        padding: 30px;
        text-align: center;
        border-radius: 10px 10px 0 0;
    }
    .registro-header h2 {
        margin: 0;
        font-size: 28px;
    }
    .registro-header p {
        margin: 10px 0 0;
        opacity: 0.9;
    }
    .registro-body {
        padding: 40px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    .form-group.required label:after {
        content: " *";
        color: #dc3545;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 10px 15px;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 3px rgba(78,115,223,0.1);
        outline: none;
    }
    .btn-registro {
        background: #4e73df;
        border: none;
        padding: 12px 30px;
        font-size: 16px;
        font-weight: 600;
        width: 100%;
        margin-top: 20px;
        border-radius: 8px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .btn-registro:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(78,115,223,0.3);
    }
    .alert {
        margin-top: 20px;
        border-radius: 8px;
        display: none;
    }
    .login-link {
        text-align: center;
        margin-top: 20px;
    }
    .login-link a {
        color: #4e73df;
        text-decoration: none;
    }
    .login-link a:hover {
        text-decoration: underline;
    }
    .csrf-info {
        font-size: 12px;
        color: #6c757d;
        margin-top: 15px;
        text-align: center;
    }
    .help-text {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }
    .row-selects {
        margin-bottom: 10px;
    }
</style>

<div class="container">
    <div class="registro-container">
        <div class="registro-header">
            <h2><i class="fas fa-user-plus"></i> Crear Cuenta de Paciente</h2>
            <p>Complete todos los campos para registrarse en BioVital</p>
        </div>
        <div class="registro-body">
            
            <form id="form-registro" method="POST" action="<?php echo APP_URL; ?>/api/registro/paciente">
                <!-- CSRF Token -->
                <?php echo Security::campoCSRF(); ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   placeholder="Ingrese su nombre" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="apellidos">Apellido</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" 
                                   placeholder="Ingrese su apellido" required>
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
                
                <!-- ==================== SISTEMA DE UBICACIÓN ==================== -->
                <h4 class="mt-4 mb-3"><i class="fas fa-map-marker-alt"></i> Ubicación</h4>
                <hr>

                <div class="row row-selects">
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

                <div class="row row-selects">
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
                    <label for="direccion">Dirección Detallada</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" required 
                           placeholder="Av. Principal, Edificio, Número, etc.">
                    <small class="help-text">Ej: Av. Principal, Edificio Central, Casa #123, Urbanización Las Delicias</small>
                </div>
                <!-- ==================== FIN SISTEMA DE UBICACIÓN ==================== -->
                
                <div class="form-group required">
                    <label for="correo">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo" 
                           placeholder="ejemplo@correo.com" required>
                </div>
                
                <div class="form-group">
                    <label for="adicional">Información Adicional</label>
                    <textarea class="form-control" id="adicional" name="adicional" rows="3" 
                              placeholder="Alergias, condiciones médicas, medicamentos que toma regularmente, etc."></textarea>
                    <small class="help-text">Esta información ayudará a los médicos a conocer mejor su historial</small>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="pass">Contraseña</label>
                            <input type="password" class="form-control" id="pass" name="pass" 
                                   placeholder="Mínimo 6 caracteres" required>
                            <small class="help-text">Use una contraseña segura</small>
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
                
                <button type="submit" class="btn btn-primary btn-registro">
                    <i class="fas fa-check-circle"></i> Crear Cuenta
                </button>
                
                <div class="csrf-info">
                    <i class="fas fa-shield-alt"></i> Formulario protegido contra CSRF - Tus datos están seguros
                </div>
            </form>
            
            <!-- Alertas -->
            <div id="alert-success" class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
                <i class="fas fa-check-circle"></i> <span id="success-message"></span>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>

            <div id="alert-error" class="alert alert-danger alert-dismissible fade show" role="alert" style="display:none;">
                <i class="fas fa-exclamation-circle"></i> <span id="error-message"></span>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
            
            <!-- Enlace a login -->
         <div class="login-link">
           <a href="http://localhost/biovital/"><i class="fas fa-sign-in-alt"></i> ¿Ya tienes cuenta? Inicia sesión aquí</a>
         </div>
          <!-- Fin Enlace a login -->
        </div>
    </div>
</div>

<!-- Scripts específicos del registro -->
<script src="<?php echo APP_URL; ?>/js/registro_paciente.js"></script>
<script src="<?php echo APP_URL; ?>/js/registro_ubicacion.js"></script>

<?php
include_once VIEW_PATH . '/layouts/footer.php';
?>