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
        
        // Obtener ubicación
        var estado_nombre = $('#estado option:selected').text();
        var ciudad_nombre = $('#ciudad option:selected').text();
        var municipio_nombre = $('#municipio option:selected').text();
        var parroquia_nombre = $('#parroquia option:selected').text();
        var direccion_detallada = $('#direccion').val();
        
        if (!estado_nombre || estado_nombre === 'Seleccione un estado...') {
            mostrarError('Por favor seleccione un estado');
            return false;
        }
        
        if (!ciudad_nombre || ciudad_nombre === 'Seleccione una ciudad...') {
            mostrarError('Por favor seleccione una ciudad');
            return false;
        }
        
        if (!direccion_detallada) {
            mostrarError('Por favor ingrese la dirección detallada');
            return false;
        }
        
        // Construir dirección completa
        var direccion_completa = '';
        if (estado_nombre && estado_nombre !== 'Seleccione un estado...') direccion_completa = estado_nombre;
        if (ciudad_nombre && ciudad_nombre !== 'Seleccione una ciudad...') direccion_completa += (direccion_completa ? ', ' : '') + ciudad_nombre;
        if (municipio_nombre && municipio_nombre !== 'Seleccione un municipio...') direccion_completa += (direccion_completa ? ', ' : '') + municipio_nombre;
        if (parroquia_nombre && parroquia_nombre !== 'Seleccione una parroquia...') direccion_completa += (direccion_completa ? ', ' : '') + parroquia_nombre;
        if (direccion_detallada) direccion_completa += (direccion_completa ? ' - ' : '') + direccion_detallada;
        
        var datos = {
            funcion: 'crear_medico',
            nombre: $('#nombre').val().trim(),
            apellidos: $('#apellidos').val().trim(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            cedula: $('#cedula').val().trim(),
            telefono: $('#telefono').val().trim(),
<<<<<<< HEAD
            direccion: direccion_completa,
=======
            estado: $('#estado').val(),
            ciudad: $('#ciudad').val(),
            municipio: $('#municipio').val(),
            parroquia: $('#parroquia').val(),
            direccion: direccion_detallada.trim(),
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
            correo: $('#correo').val().trim(),
            sexo: $('#sexo').val(),
            adicional: $('#adicional').val().trim(),
            pass: pass,
<<<<<<< HEAD
            pregunta_seguridad_1: $('#pregunta_seguridad_1').val(),
            respuesta_seguridad_1: $('#respuesta_seguridad_1').val().trim(),
            pregunta_seguridad_2: $('#pregunta_seguridad_2').val(),
            respuesta_seguridad_2: $('#respuesta_seguridad_2').val().trim(),
            pregunta_seguridad_3: $('#pregunta_seguridad_3').val(),
            respuesta_seguridad_3: $('#respuesta_seguridad_3').val().trim(),
=======
            confirm_pass: confirm_pass,
>>>>>>> 08fa34e7676afef1b6a097b9607f3411a6663e15
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        console.log('Enviando datos:', datos);
        
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando cuenta...');
        
        $.ajax({
            url: APP_URL + '/api/registro/medico',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta:', response);
                if(response.success) {
                    mostrarExito(response.message);
                    setTimeout(function() {
                        window.location.href = APP_URL + '/login/medico';
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