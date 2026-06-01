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
        
        // Obtener los NOMBRES de los selects (no los IDs)
        var estado_nombre = $('#estado option:selected').text();
        var ciudad_nombre = $('#ciudad option:selected').text();
        var municipio_nombre = $('#municipio option:selected').text();
        var parroquia_nombre = $('#parroquia option:selected').text();
        var direccion_detallada = $('#direccion').val();
        
        // VALIDAR que se seleccionó estado y ciudad (son obligatorios)
        if (!estado_nombre || estado_nombre === 'Seleccione un estado...') {
            mostrarError('Debe seleccionar un estado');
            return false;
        }
        
        if (!ciudad_nombre || ciudad_nombre === 'Seleccione una ciudad...') {
            mostrarError('Debe seleccionar una ciudad');
            return false;
        }
        
        if (!direccion_detallada) {
            mostrarError('Debe ingresar la dirección detallada');
            return false;
        }
        
        // Construir dirección completa para guardar en la base de datos
        var partes_ubicacion = [];
        if (estado_nombre && estado_nombre !== 'Seleccione un estado...') {
            partes_ubicacion.push(estado_nombre);
        }
        if (ciudad_nombre && ciudad_nombre !== 'Seleccione una ciudad...') {
            partes_ubicacion.push(ciudad_nombre);
        }
        if (municipio_nombre && municipio_nombre !== 'Seleccione un municipio...' && municipio_nombre !== 'Primero seleccione un estado...') {
            partes_ubicacion.push(municipio_nombre);
        }
        if (parroquia_nombre && parroquia_nombre !== 'Seleccione una parroquia...' && parroquia_nombre !== 'Primero seleccione un municipio...') {
            partes_ubicacion.push(parroquia_nombre);
        }
        
        var ubicacion_completa = partes_ubicacion.join(', ');
        var direccion_completa = ubicacion_completa;
        
        if (direccion_detallada) {
            direccion_completa += (ubicacion_completa ? ' - ' : '') + direccion_detallada;
        }
        
        console.log('Dirección completa a guardar:', direccion_completa);
        
        var datos = {
            funcion: 'crear_administrador',
            nombre: $('#nombre').val(),
            apellidos: $('#apellidos').val(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            cedula: $('#cedula').val(),
            telefono: $('#telefono').val(),
            direccion: direccion_completa,
            correo: $('#correo').val(),
            sexo: $('#sexo').val(),
            adicional: $('#adicional').val(),
            pass: pass,
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        console.log('Datos enviados:', datos);
        
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando cuenta...');
        
        $.ajax({
            url: APP_URL + '/api/registro/administrador',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    mostrarExito(response.message);
                    setTimeout(function() {
                        window.location.href = APP_URL + '/login/administrador';
                    }, 2000);
                } else {
                    mostrarError(response.message);
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                console.error('Error en AJAX:', xhr.responseText);
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