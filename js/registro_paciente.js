<<<<<<< HEAD
<<<<<<< HEAD


=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
$(document).ready(function() {
    // Verificar que APP_URL esté definida
    if (typeof APP_URL === 'undefined') {
        console.error('ERROR: APP_URL no está definida');
        window.APP_URL = '';
<<<<<<< HEAD
    }
    
    console.log('APP_URL:', APP_URL);
    
=======
<<<<<<< HEAD


$(document).ready(function() {
    // Verificar que APP_URL esté definida
    if (typeof APP_URL === 'undefined') {
        console.error('ERROR: APP_URL no está definida');
        window.APP_URL = '';
    }
    
    console.log('APP_URL:', APP_URL);
    
=======
//***   crear_paciente */
$(document).ready(function() {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
    }    
    console.log('APP_URL:', APP_URL);
    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    $('#form-registro').submit(function(e) {
        e.preventDefault();
        
        var pass = $('#pass').val();
        var confirm_pass = $('#confirm_pass').val();
        
        if(pass !== confirm_pass) {
            mostrarError('Las contraseñas no coinciden');
            return false;
        }
        
        if(pass.length < 6) {
            mostrarError('La contraseña debe tener al menos 6 caracteres');
            return false;
        }
        
        // Obtener valores de ubicación (guardamos los nombres, no los IDs)
        var estado_nombre = $('#estado option:selected').text();
        var ciudad_nombre = $('#ciudad option:selected').text();
        var municipio_nombre = $('#municipio option:selected').text();
        var parroquia_nombre = $('#parroquia option:selected').text();
        
        // Construir dirección completa con todos los campos
        var direccion_completa = '';
        if (estado_nombre && estado_nombre !== 'Seleccione un estado...') {
            direccion_completa += estado_nombre;
        }
        if (ciudad_nombre && ciudad_nombre !== 'Seleccione una ciudad...') {
            direccion_completa += (direccion_completa ? ', ' : '') + ciudad_nombre;
        }
        if (municipio_nombre && municipio_nombre !== 'Seleccione un municipio...') {
            direccion_completa += (direccion_completa ? ', ' : '') + municipio_nombre;
        }
        if (parroquia_nombre && parroquia_nombre !== 'Seleccione una parroquia...') {
            direccion_completa += (direccion_completa ? ', ' : '') + parroquia_nombre;
        }
        
        var direccion_detallada = $('#direccion').val();
        if (direccion_detallada) {
            direccion_completa += (direccion_completa ? ' - ' : '') + direccion_detallada;
        }
        
<<<<<<< HEAD
<<<<<<< HEAD
        // Obtener el token CSRF
        var csrf_token = $('input[name="csrf_token"]').val();
        
=======
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        // Obtener el token CSRF
        var csrf_token = $('input[name="csrf_token"]').val();
        
        var datos = {
            nombre: $('#nombre').val().trim(),
            apellidos: $('#apellidos').val().trim(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            cedula: $('#cedula').val().trim(),
            telefono: $('#telefono').val().trim(),
            direccion: direccion_completa,
            correo: $('#correo').val().trim(),
            sexo: $('#sexo').val(),
            adicional: $('#adicional').val().trim(),
            pass: pass,
            csrf_token: csrf_token
<<<<<<< HEAD
=======
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        var datos = {
            nombre: $('#nombre').val().trim(),
            apellidos: $('#apellidos').val().trim(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            cedula: $('#cedula').val().trim(),
            telefono: $('#telefono').val().trim(),
            direccion: direccion_completa,
            correo: $('#correo').val().trim(),
            sexo: $('#sexo').val(),
<<<<<<< HEAD
            adicional: $('#adicional').val().trim(),
            pass: pass,
            csrf_token: csrf_token
=======
            adicional: $('#adicional').val(),
            pass: pass
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        };
        
        console.log('Enviando datos:', datos);
        
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando cuenta...');
        
<<<<<<< HEAD
<<<<<<< HEAD
       
        $.ajax({
            url: APP_URL + '/api/registro/paciente',  // ← Ruta corregida
=======
<<<<<<< HEAD
       
        $.ajax({
            url: APP_URL + '/api/registro/paciente',  // ← Ruta corregida
=======
        $.ajax({
            url: '../controlador/RegistroPacienteController.php',
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
       
        $.ajax({
            url: APP_URL + '/api/registro/paciente',  // ← Ruta corregida
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta:', response);
                if(response.success) {
                    mostrarExito(response.message);
                    setTimeout(function() {
<<<<<<< HEAD
<<<<<<< HEAD
                        // Redirigir al login del paciente con la ruta amigable
                        window.location.href = APP_URL + '/login/paciente';
=======
<<<<<<< HEAD
                        // Redirigir al login del paciente con la ruta amigable
                        window.location.href = APP_URL + '/login/paciente';
=======
                        window.location.href = '../vista/login_paciente.php';
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                        // Redirigir al login del paciente con la ruta amigable
                        window.location.href = APP_URL + '/login/paciente';
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    }, 2000);
                } else {
                    mostrarError(response.message);
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                console.error('Respuesta del servidor:', xhr.responseText);
                
                let errorMsg = 'Error de conexión: ' + xhr.status;
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                mostrarError(errorMsg);
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
                console.error('Respuesta:', xhr.responseText);
                mostrarError('Error de conexión: ' + xhr.status);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                $submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    function mostrarError(mensaje) {
        $('#error-message').text(mensaje);
        $('#alert-error').fadeIn();
        setTimeout(function() { $('#alert-error').fadeOut(); }, 5000);
    }
    
    function mostrarExito(mensaje) {
        $('#success-message').text(mensaje);
        $('#alert-success').fadeIn();
    }
});