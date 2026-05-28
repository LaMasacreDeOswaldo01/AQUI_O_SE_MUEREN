/**
 * registro_ubicacion.js - Sistema de carga de ubicación para formularios de registro
 * Usa la API del sistema a través del Front Controller
 */

$(document).ready(function() {
    // Detectar APP_URL automáticamente si no está definida
    if (typeof APP_URL === 'undefined') {
        console.warn('APP_URL no está definida, intentando detectar automáticamente...');
        
        // Detectar la URL base del proyecto automáticamente
        var path = window.location.pathname;
        var baseUrl = '';
        
        // Patrones comunes de instalación
        var patterns = [
            { pattern: '/biovital/', base: '/biovital' },
            { pattern: '/public/', base: '/public' }
        ];
        
        // Buscar coincidencia con patrones conocidos
        for (var i = 0; i < patterns.length; i++) {
            if (path.includes(patterns[i].pattern)) {
                baseUrl = patterns[i].base;
                break;
            }
        }
        
        // Si no se detectó, inferir de la estructura de carpetas
        if (baseUrl === '') {
            var parts = path.split('/');
            parts = parts.filter(function(p) { return p !== ''; });
            
            if (parts.length > 0 && !parts[0].includes('.php') && !parts[0].includes('.')) {
                baseUrl = '/' + parts[0];
            }
        }
        
        window.APP_URL = baseUrl;
        console.log('APP_URL detectada automáticamente:', APP_URL);
    } else {
        console.log('APP_URL ya definida:', APP_URL);
    }
    
    // Verificar que APP_URL tenga un valor válido
    if (!APP_URL || APP_URL === '') {
        console.error('No se pudo determinar APP_URL');
        mostrarErrorUbicacion('Error de configuración de la aplicación');
        return;
    }
    
    // Cargar estados al cargar la página
    cargarEstados();
    
    // Eventos de cambio
    $(document).on('change', '#estado', function() {
        var id_estado = $(this).val();
        if (id_estado) {
            cargarCiudades(id_estado);
            cargarMunicipiosPorEstado(id_estado);
        } else {
            resetUbicacion();
        }
    });
    
    $(document).on('change', '#ciudad', function() {
        var ciudad_nombre = $('#ciudad option:selected').text();
        if ($('#preview_ciudad').length) {
            $('#preview_ciudad').text(ciudad_nombre || 'Ciudad');
        }
    });
    
    $(document).on('change', '#municipio', function() {
        var id_municipio = $(this).val();
        if (id_municipio) {
            cargarParroquias(id_municipio);
        } else {
            $('#parroquia').html('<option value="">Primero seleccione un municipio...</option>').prop('disabled', true);
        }
    });
    
    // Función para mostrar errores al usuario
    function mostrarErrorUbicacion(mensaje) {
        var errorDiv = $('#alert-error');
        if (errorDiv.length) {
            $('#error-message').text(mensaje);
            errorDiv.fadeIn();
            setTimeout(function() {
                errorDiv.fadeOut();
            }, 5000);
        } else {
            console.error('Error de ubicación:', mensaje);
        }
    }
    
    // Funciones principales
    function cargarEstados() {
        console.log('Cargando estados desde API:', APP_URL + '/api/ubicacion/estados');
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta estados:', response);
                
                // Verificar el formato de la respuesta
                var estados = Array.isArray(response) ? response : (response.data || response.estados || []);
                
                if (!Array.isArray(estados) || estados.length === 0) {
                    console.warn('No se recibieron estados, usando fallback');
                    cargarEstadosFallback();
                    return;
                }
                
                var options = '<option value="">Seleccione un estado...</option>';
                for (var i = 0; i < estados.length; i++) {
                    var estado = estados[i];
                    var id = estado.id_estado || estado.id || '';
                    var nombre = estado.estado || estado.nombre || '';
                    options += '<option value="' + id + '">' + nombre + '</option>';
                }
                $('#estado').html(options);
                $('#estado').prop('disabled', false);
                console.log('Estados cargados correctamente');
            },
            error: function(xhr, status, error) {
                console.error('Error cargando estados:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
                cargarEstadosFallback();
            }
        });
    }
    
    function cargarEstadosFallback() {
        console.log('Usando datos de estados fallback');
        var estados = [
            {id_estado: 1, estado: 'Amazonas'}, {id_estado: 2, estado: 'Anzoátegui'},
            {id_estado: 3, estado: 'Apure'}, {id_estado: 4, estado: 'Aragua'},
            {id_estado: 5, estado: 'Barinas'}, {id_estado: 6, estado: 'Bolívar'},
            {id_estado: 7, estado: 'Carabobo'}, {id_estado: 8, estado: 'Cojedes'},
            {id_estado: 9, estado: 'Delta Amacuro'}, {id_estado: 10, estado: 'Falcón'},
            {id_estado: 11, estado: 'Guárico'}, {id_estado: 12, estado: 'Lara'},
            {id_estado: 13, estado: 'Mérida'}, {id_estado: 14, estado: 'Miranda'},
            {id_estado: 15, estado: 'Monagas'}, {id_estado: 16, estado: 'Nueva Esparta'},
            {id_estado: 17, estado: 'Portuguesa'}, {id_estado: 18, estado: 'Sucre'},
            {id_estado: 19, estado: 'Táchira'}, {id_estado: 20, estado: 'Trujillo'},
            {id_estado: 21, estado: 'La Guaira'}, {id_estado: 22, estado: 'Yaracuy'},
            {id_estado: 23, estado: 'Zulia'}, {id_estado: 24, estado: 'Distrito Capital'}
        ];
        var options = '<option value="">Seleccione un estado...</option>';
        for (var i = 0; i < estados.length; i++) {
            options += '<option value="' + estados[i].id_estado + '">' + estados[i].estado + '</option>';
        }
        $('#estado').html(options);
        $('#estado').prop('disabled', false);
    }
    
    function cargarCiudades(id_estado) {
        if (!id_estado) {
            $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            return;
        }
        
        console.log('Cargando ciudades para estado:', id_estado);
        $('#ciudad').html('<option value="">Cargando ciudades...</option>').prop('disabled', true);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/ciudades',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta ciudades:', response);
                
                var ciudades = Array.isArray(response) ? response : (response.data || response.ciudades || []);
                
                if (!Array.isArray(ciudades) || ciudades.length === 0) {
                    $('#ciudad').html('<option value="">No hay ciudades disponibles</option>').prop('disabled', false);
                    return;
                }
                
                var options = '<option value="">Seleccione una ciudad...</option>';
                for (var i = 0; i < ciudades.length; i++) {
                    var ciudad = ciudades[i];
                    var id = ciudad.id_ciudad || ciudad.id || '';
                    var nombre = ciudad.ciudad || ciudad.nombre || '';
                    options += '<option value="' + id + '">' + nombre + '</option>';
                }
                $('#ciudad').html(options).prop('disabled', false);
            },
            error: function(xhr) {
                console.error('Error cargando ciudades:', xhr.responseText);
                $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
                mostrarErrorUbicacion('Error al cargar las ciudades');
            }
        });
    }
    
    function cargarMunicipiosPorEstado(id_estado) {
        if (!id_estado) {
            $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Primero seleccione un municipio...</option>').prop('disabled', true);
            return;
        }
        
        console.log('Cargando municipios para estado:', id_estado);
        $('#municipio').html('<option value="">Cargando municipios...</option>').prop('disabled', true);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/municipios',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta municipios:', response);
                
                var municipios = Array.isArray(response) ? response : (response.data || response.municipios || []);
                
                if (!Array.isArray(municipios) || municipios.length === 0) {
                    $('#municipio').html('<option value="">No hay municipios disponibles</option>').prop('disabled', false);
                    $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
                    return;
                }
                
                var options = '<option value="">Seleccione un municipio...</option>';
                for (var i = 0; i < municipios.length; i++) {
                    var municipio = municipios[i];
                    var id = municipio.id_municipio || municipio.id || '';
                    var nombre = municipio.municipio || municipio.nombre || '';
                    options += '<option value="' + id + '">' + nombre + '</option>';
                }
                $('#municipio').html(options).prop('disabled', false);
                $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            },
            error: function(xhr) {
                console.error('Error cargando municipios:', xhr.responseText);
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
                mostrarErrorUbicacion('Error al cargar los municipios');
            }
        });
    }
    
    function cargarParroquias(id_municipio) {
        if (!id_municipio) {
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            return;
        }
        
        console.log('Cargando parroquias para municipio:', id_municipio);
        $('#parroquia').html('<option value="">Cargando parroquias...</option>').prop('disabled', true);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/parroquias',
            type: 'POST',
            data: { id_municipio: id_municipio },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta parroquias:', response);
                
                var parroquias = Array.isArray(response) ? response : (response.data || response.parroquias || []);
                
                if (!Array.isArray(parroquias) || parroquias.length === 0) {
                    $('#parroquia').html('<option value="">No hay parroquias disponibles</option>').prop('disabled', false);
                    return;
                }
                
                var options = '<option value="">Seleccione una parroquia...</option>';
                for (var i = 0; i < parroquias.length; i++) {
                    var parroquia = parroquias[i];
                    var id = parroquia.id_parroquia || parroquia.id || '';
                    var nombre = parroquia.parroquia || parroquia.nombre || '';
                    options += '<option value="' + id + '">' + nombre + '</option>';
                }
                $('#parroquia').html(options).prop('disabled', false);
            },
            error: function(xhr) {
                console.error('Error cargando parroquias:', xhr.responseText);
                $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
                mostrarErrorUbicacion('Error al cargar las parroquias');
            }
        });
    }
    
    function resetUbicacion() {
        $('#ciudad').html('<option value="">Primero seleccione un estado...</option>').prop('disabled', true);
        $('#municipio').html('<option value="">Primero seleccione una ciudad...</option>').prop('disabled', true);
        $('#parroquia').html('<option value="">Primero seleccione un municipio...</option>').prop('disabled', true);
    }
});