<?php

// vista/registro_asistente.php 
// Registro de Asistente - Estilo BioVital Dashboard

// vista/registro_asistente.php
// Registro de asistentes - Versión con interfaz unificada BioVital

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistente - BioVital</title>
    
    <script>var APP_URL = '<?php echo APP_URL; ?>';</script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    
    <!-- CSS Unificado de Registro BioVital -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/registro.css">
</head>
<body>

<div class="registro-wrapper">
    <!-- Navbar -->
    <div class="registro-navbar">
        <a href="<?php echo APP_URL; ?>">
            <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="BioVital">
            <span class="brand-text">Bio<em>vital</em></span>
        </a>
    </div>
    
    <div class="registro-container rol-asistente">
        <div class="registro-header">
            <h2><i class="fas fa-user-nurse"></i> Registro de Asistente</h2>
            <p>Complete todos los campos para registrarse</p>
        </div>
            <div class="registro-body">
                <?php
                // Incluir Security con ruta correcta
                $securityPath = dirname(__DIR__) . '/modelo/Security.php';
                if (!file_exists($securityPath)) {
                    die("Error: No se encuentra Security.php en: " . $securityPath);
                }
                include_once $securityPath;
                ?>
                
                <form id="form-registro" method="POST" action="<?php echo APP_URL; ?>/api/registro/asistente">
                    <?php echo Security::campoCSRF(); ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="apellidos">Apellido</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
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
                                <label for="cedula">Cédula</label>
                                <input type="text" class="form-control" id="cedula" name="cedula" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" required>
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
                    <div class="seccion-titulo">
                        <i class="fas fa-map-marker-alt"></i> Ubicación
                    </div>

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
                        <label for="direccion">Dirección Detallada</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" required placeholder="Av. Principal, Edificio, Número, etc.">
                        <small class="form-text text-muted">Ej: Av. Principal, Edificio Central, Piso 3, Oficina 5</small>
                    </div>
                    <!-- ==================== FIN SISTEMA DE UBICACIÓN ==================== -->
                    
                    <div class="form-group required">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="adicional">Información Adicional</label>
                        <textarea class="form-control" id="adicional" name="adicional" rows="2" placeholder="Información adicional sobre el asistente..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="pass">Contraseña</label>
                                <input type="password" class="form-control" id="pass" name="pass" required>
                                <small class="form-text text-muted">Mínimo 6 caracteres</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="confirm_pass">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" required>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success btn-registro">
                        <i class="fas fa-check-circle"></i> Crear Cuenta
                    </button>
                    
                    <div class="csrf-info">
                        <i class="fas fa-shield-alt"></i> Formulario protegido contra CSRF - Tus datos están seguros
                    </div>
                </form>
                
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
                    <a href="<?php echo APP_URL; ?>"><i class="fas fa-sign-in-alt"></i> ¿Ya tienes cuenta? Inicia sesión aquí</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo APP_URL; ?>/js/registro_asistente.js"></script>
<script src="<?php echo APP_URL; ?>/js/registro_ubicacion.js"></script>
</body>
</html>