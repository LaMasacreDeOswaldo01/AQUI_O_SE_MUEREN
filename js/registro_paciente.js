

$(document).ready(function() {
    // Verificar que APP_URL esté definida
    if (typeof APP_URL === 'undefined') {
        console.error('ERROR: APP_URL no está definida');
        window.APP_URL = '';
    }
    
    console.log('APP_URL:', APP_URL);
    
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
        
        // Obtener el token CSRF
        var csrf_token = $('input[name="csrf_token"]').val();
        
        var datos = {
            nombre: $('#nombre').val().trim(),
            apellidos: $('#apellidos').val().trim(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            cedula: $('#cedula').val().trim(),
            telefono: $('#telefono').val().trim(),
            estado: $('#estado').val(),
            ciudad: $('#ciudad').val(),
            municipio: $('#municipio').val(),
            parroquia: $('#parroquia').val(),
            direccion: $('#direccion').val().trim(),
            correo: $('#correo').val().trim(),
            sexo: $('#sexo').val(),
            adicional: $('#adicional').val().trim(),
            pass: pass,
            confirm_pass: confirm_pass,
            csrf_token: csrf_token
        };
        
        console.log('Enviando datos:', datos);
        
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando cuenta...');
        
       
        $.ajax({
            url: APP_URL + '/api/registro/paciente',  // ← Ruta corregida
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta:', response);
                if(response.success) {
                    mostrarExito(response.message);
                    setTimeout(function() {
                        // Redirigir al login del paciente con la ruta amigable
                        window.location.href = APP_URL + '/login/paciente';
                    }, 2000);
                } else {
                    mostrarError(response.message);
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                
                let errorMsg = 'Error de conexión: ' + xhr.status;
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                mostrarError(errorMsg);
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