$(document).ready(function() {
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
        
        var datos = {
            funcion: 'crear_asistente',
<<<<<<< HEAD
            nombre: $('#nombre').val(),
            apellidos: $('#apellidos').val(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            cedula: $('#cedula').val(),
            telefono: $('#telefono').val(),
            direccion: $('#direccion').val(),
            correo: $('#correo').val(),
            sexo: $('#sexo').val(),
            adicional: $('#adicional').val(),
            pass: pass,
            pregunta_seguridad_1: $('#pregunta_seguridad_1').val(),
            respuesta_seguridad_1: $('#respuesta_seguridad_1').val().trim(),
            pregunta_seguridad_2: $('#pregunta_seguridad_2').val(),
            respuesta_seguridad_2: $('#respuesta_seguridad_2').val().trim(),
            pregunta_seguridad_3: $('#pregunta_seguridad_3').val(),
            respuesta_seguridad_3: $('#respuesta_seguridad_3').val().trim(),
=======
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
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando cuenta...');
        
        $.ajax({
            url: APP_URL + '/api/registro/asistente',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    mostrarExito(response.message);
                    setTimeout(function() {
                        window.location.href = APP_URL + '/login/asistente';
                    }, 2000);
                } else {
                    mostrarError(response.message);
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
<<<<<<< HEAD
                mostrarError('Error de conexión: ' + xhr.status);
=======
                var errorMsg = 'Error de conexión';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) errorMsg = response.message;
                    } catch (e) {
                        errorMsg = 'Error de conexión: ' + xhr.status;
                    }
                }
                mostrarError(errorMsg);
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
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