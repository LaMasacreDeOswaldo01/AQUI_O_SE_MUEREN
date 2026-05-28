<?php
// vista/med_registro.php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Médico - BioVital</title>
    
    <script>
        var APP_URL = '<?php echo APP_URL; ?>';
    </script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .registro-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .registro-header {
            background: #4e73df;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .registro-header h2 {
            margin: 0;
            font-size: 28px;
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
            color: red;
        }
        .btn-registro {
            background: #4e73df;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
        }
        .btn-registro:hover {
            background: #2e59d9;
        }
        .alert {
            margin-top: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="registro-container">
            <div class="registro-header">
                <h2><i class="fas fa-user-md"></i> Registro de Médico</h2>
                <p>Complete todos los campos para registrarse como médico</p>
            </div>
            <div class="registro-body">
                <?php
               $securityPath = dirname(__DIR__) . '/modelo/Security.php';
if (!file_exists($securityPath)) die("Error: No se encuentra Security.php");
include_once $securityPath;
                ?>
                <form id="form-registro" method="POST" action="<?php echo APP_URL; ?>/api/registro/medico">
                    <!-- CSRF Token generado directamente en el servidor -->
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
                    <h4 class="mt-4"><i class="fas fa-map-marker-alt"></i> Ubicación</h4>
                    <hr>

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
                        <textarea class="form-control" id="adicional" name="adicional" rows="2" placeholder="Especialidad, años de experiencia, etc."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="pass">Contraseña</label>
                                <input type="password" class="form-control" id="pass" name="pass" required>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label for="confirm_pass">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" required>
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

                 <!-- ==================== redirecciona a login ==================== -->
              <div class="login-link">
    <a href="http://localhost/biovital/"><i class="fas fa-sign-in-alt"></i> ¿Ya tienes cuenta? Inicia sesión aquí</a>
</div>
  <!-- ==================== FIN ==================== -->
                
             
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/registro_medico.js"></script>
    <script src="../js/registro_ubicacion.js"></script>
</body>
</html>