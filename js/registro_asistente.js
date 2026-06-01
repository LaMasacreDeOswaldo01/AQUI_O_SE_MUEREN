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
                mostrarError('Error de conexión: ' + xhr.status);
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