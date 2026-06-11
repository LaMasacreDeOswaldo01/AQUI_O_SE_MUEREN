// js/ubicacion.js - Sistema de ubicación para Venezuela
$(document).ready(function() {
    var API_URL = APP_URL + '/api/ubicacion';
    
       cargarEstados();
    
    // Evento cambio de estado
    $(document).on('change', '#estado', function() {
        var id_estado = $(this).val();
        if (id_estado) {
            cargarCiudades(id_estado);
            cargarMunicipios(id_estado);
        } else {
            limpiarSelects();
        }
    });
    
    // Evento cambio de municipio
    $(document).on('change', '#municipio', function() {
        var id_municipio = $(this).val();
        if (id_municipio) {
            cargarParroquias(id_municipio);
        } else {
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });
    
    function cargarEstados() {
        $.ajax({
            url: API_URL + '/estados',
            type: 'POST',
            dataType: 'json',
            success: function(estados) {
                var options = '<option value="">Seleccione un estado...</option>';
                for (var i = 0; i < estados.length; i++) {
                    options += '<option value="' + estados[i].id_estado + '">' + estados[i].estado + '</option>';
                }
                $('#estado').html(options);
                $('#estado').prop('disabled', false);
            },
            error: function() {
                console.error('Error al cargar estados');
            }
        });
    }
    
    function cargarCiudades(id_estado) {
        $('#ciudad').html('<option value="">Cargando...</option>').prop('disabled', true);
        
        $.ajax({
            url: API_URL + '/ciudades',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(ciudades) {
                var options = '<option value="">Seleccione una ciudad...</option>';
                for (var i = 0; i < ciudades.length; i++) {
                    options += '<option value="' + ciudades[i].id_ciudad + '">' + ciudades[i].ciudad + '</option>';
                }
                $('#ciudad').html(options).prop('disabled', false);
            },
            error: function() {
                $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarMunicipios(id_estado) {
        $('#municipio').html('<option value="">Cargando...</option>').prop('disabled', true);
        $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        
        $.ajax({
            url: API_URL + '/municipios',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(municipios) {
                var options = '<option value="">Seleccione un municipio...</option>';
                for (var i = 0; i < municipios.length; i++) {
                    options += '<option value="' + municipios[i].id_municipio + '">' + municipios[i].municipio + '</option>';
                }
                $('#municipio').html(options).prop('disabled', false);
            },
            error: function() {
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarParroquias(id_municipio) {
        $('#parroquia').html('<option value="">Cargando...</option>').prop('disabled', true);
        
        $.ajax({
            url: API_URL + '/parroquias',
            type: 'POST',
            data: { id_municipio: id_municipio },
            dataType: 'json',
            success: function(parroquias) {
                var options = '<option value="">Seleccione una parroquia...</option>';
                for (var i = 0; i < parroquias.length; i++) {
                    options += '<option value="' + parroquias[i].id_parroquia + '">' + parroquias[i].parroquia + '</option>';
                }
                $('#parroquia').html(options).prop('disabled', false);
            },
            error: function() {
                $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
            }
        });
    }
    
    function limpiarSelects() {
        $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
        $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
        $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
    }
    
    // Función para cargar dirección existente (para edición)
    window.cargarDireccionExistente = function(direccion_completa) {
        if (!direccion_completa || direccion_completa === '-') return;
        
        // Parsear dirección (formato: "Estado, Ciudad, Municipio, Parroquia - Dirección Detallada")
        var partes = direccion_completa.split(' - ');
        var ubicacion = partes[0];
        var direccion_detallada = partes.length > 1 ? partes[1] : '';
        
        $('#direccion_detallada').val(direccion_detallada);
        
        var ubicacion_partes = ubicacion.split(', ');
        
        // Buscar y seleccionar estado
        if (ubicacion_partes[0]) {
            $('#estado option').each(function() {
                if ($(this).text() === ubicacion_partes[0]) {
                    $(this).prop('selected', true);
                    $('#estado').trigger('change');
                    
                    // Esperar a que cargue y luego seleccionar ciudad
                    setTimeout(function() {
                        if (ubicacion_partes[1]) {
                            $('#ciudad option').each(function() {
                                if ($(this).text() === ubicacion_partes[1]) {
                                    $(this).prop('selected', true);
                                }
                            });
                        }
                        if (ubicacion_partes[2]) {
                            setTimeout(function() {
                                $('#municipio option').each(function() {
                                    if ($(this).text() === ubicacion_partes[2]) {
                                        $(this).prop('selected', true);
                                        $('#municipio').trigger('change');
                                    }
                                });
                            }, 500);
                        }
                        if (ubicacion_partes[3]) {
                            setTimeout(function() {
                                $('#parroquia option').each(function() {
                                    if ($(this).text() === ubicacion_partes[3]) {
                                        $(this).prop('selected', true);
                                    }
                                });
                            }, 1000);
                        }
                    }, 500);
                }
            });
        }
    };
});
