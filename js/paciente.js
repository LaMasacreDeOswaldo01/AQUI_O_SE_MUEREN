<<<<<<< HEAD
/**
<<<<<<< HEAD
 * js/paciente.js 
 * Maneja correctamente el formato ApiResponse de la API de ubicación
=======
<<<<<<< HEAD
 * js/paciente.js 
 * Maneja correctamente el formato ApiResponse de la API de ubicación
 */
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0

$(document).ready(function() {
    var id_usuario = $('#id_usuario').val();
    var edit = false;

    console.log('=== PACIENTE JS CORREGIDO V2 ===');
    console.log('APP_URL:', APP_URL);
    console.log('ID Usuario:', id_usuario);

    if (!id_usuario || id_usuario === '') {
        console.error('ERROR: ID de paciente no encontrado');
        $('#nombre_us').html('Error: Sesión no válida');
        return;
    }

    // ==================== FUNCIÓN PRINCIPAL: BUSCAR PACIENTE ====================
    function buscar_paciente(dato) {
        console.log('Buscando paciente con dato:', dato);
<<<<<<< HEAD
=======
 * js/paciente.js
 * Funcionalidades para el panel del paciente
 * Maneja: carga de datos, edición de perfil, cambio de contraseña y avatar
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
 */

$(document).ready(function() {
    var id_usuario = $('#id_usuario').val();
    var edit = false;

    console.log('=== PACIENTE JS CORREGIDO V2 ===');
    console.log('APP_URL:', APP_URL);
    console.log('ID Usuario:', id_usuario);

    if (!id_usuario || id_usuario === '') {
        console.error('ERROR: ID de paciente no encontrado');
        $('#nombre_us').html('Error: Sesión no válida');
        return;
    }
<<<<<<< HEAD

    // ==================== FUNCIÓN PRINCIPAL: BUSCAR PACIENTE ====================
    function buscar_paciente(dato) {
        console.log('Buscando paciente con dato:', dato);
=======
    
    // ==================== FUNCIÓN PRINCIPAL: CARGAR DATOS ====================
    function cargarDatosPaciente(id) {
        console.log('Cargando datos del paciente ID:', id);
        $('#nombre_us').html('Cargando...');
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        
        $.ajax({
            url: APP_URL + '/api/pacientes/buscar',
            type: 'POST',
<<<<<<< HEAD
<<<<<<< HEAD
            data: { dato: dato, id_paciente: dato },
=======
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            data: { dato: dato, id_paciente: dato },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                // Manejar formato ApiResponse
                var paciente = response;
                if (response.success && response.data) {
                    paciente = response.data;
                }
                
                if (paciente.error) {
                    console.error('Error:', paciente.error);
<<<<<<< HEAD
=======
            data: { 
                dato: id, 
                id_paciente: id 
            },
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                // Manejar formato ApiResponse
                var paciente = response;
                if (response.success && response.data) {
                    paciente = response.data;
                }
                
                if (paciente.error) {
<<<<<<< HEAD
                    console.error('Error:', paciente.error);
=======
                    console.error('Error del servidor:', paciente.error);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    $('#nombre_us').html('Error: ' + paciente.error);
                    return;
                }
                
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                // Actualizar UI con los datos
                $('#nombre_us').html(paciente.nombre || '');
                $('#apellidos_us').html(paciente.apellidos || '');
                $('#edad').html(paciente.fecha_nacimiento || '');
                $('#cedula_us').html(paciente.cedula || '');
                $('#us_tipo').html(paciente.tipo || 'Paciente');
                $('#telefono_us').html(paciente.telefono || '');
                $('#correo_us').html(paciente.correo || '');
                $('#sexo_us').html(paciente.sexo || '');
                $('#adicional_us').html(paciente.adicional || '');
                
                // Mostrar dirección
                if (paciente.direccion) {
                    $('#direccion_us').html(paciente.direccion);
                    // Cargar dirección en los campos de edición
                    cargarDireccionEnCampos(paciente.direccion);
                } else {
                    $('#direccion_us').html('-');
                    cargarEstados();
                }
                
                // Actualizar avatar
                if (paciente.avatar) {
                    var avatarUrl = paciente.avatar;
                    var timestamp = new Date().getTime();
                    avatarUrl = avatarUrl + '?t=' + timestamp;
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav').attr('src', avatarUrl);
                    console.log('Avatar actualizado:', avatarUrl);
                } else {
                    var defaultAvatar = APP_URL + '/img/avatarDES.jpg?t=' + new Date().getTime();
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav').attr('src', defaultAvatar);
                }
                
                console.log('Datos actualizados correctamente');
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            },
            error: function(xhr, status, error) {
                console.error('Error en la petición AJAX:', error);
                $('#nombre_us').html('Error de conexión: ' + status);
                cargarEstados();
            }
        });
    }

<<<<<<< HEAD
    // ==================== FUNCIONES DE UBICACIÓN ====================
    
=======
    // ==================== FUNCIONES DE UBICACIÓN ====================    
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    function cargarDireccionEnCampos(direccion_completa) {
        console.log('Parseando dirección:', direccion_completa);
        
        if (!direccion_completa || direccion_completa === '-') {
            console.log('No hay dirección guardada');
            cargarEstados();
            return;
        }
        
        let direccion_detallada = '';
        let ubicacion = direccion_completa;
        
        if (direccion_completa.includes(' - ')) {
            let partes = direccion_completa.split(' - ');
            ubicacion = partes[0];
            direccion_detallada = partes.slice(1).join(' - ');
        }
        
        let ubicacion_partes = ubicacion.split(', ').filter(p => p.trim() !== '');
        console.log('Partes de ubicación:', ubicacion_partes);
        
        $('#direccion_detallada').val(direccion_detallada);
        
        cargarEstadosConSeleccion(ubicacion_partes);
    }

    function cargarEstadosConSeleccion(ubicacion_partes) {
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta de estados:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
                var estados = [];
                if (response.success && response.data) {
                    estados = response.data;
                } else if (Array.isArray(response)) {
                    estados = response;
                } else if (response.estados) {
                    estados = response.estados;
                } else {
                    estados = response;
                }
                
                // Asegurar que sea un array
                if (!Array.isArray(estados)) {
                    console.error('Estados no es un array:', estados);
                    estados = [];
                }
                
                console.log('Estados procesados:', estados.length);
                
                let options = '<option value="">Seleccione un estado...</option>';
                let estadoId = null;
                let estadoSeleccionado = ubicacion_partes[0] || '';
                
                for (let i = 0; i < estados.length; i++) {
                    let estado = estados[i];
                    let id = estado.id_estado || estado.id;
                    let nombre = estado.estado || estado.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                    if (nombre === estadoSeleccionado) {
                        estadoId = id;
                    }
                }
                $('#estado').html(options).prop('disabled', false);
                
                if (estadoId) {
                    $('#estado').val(estadoId);
                    if (ubicacion_partes.length >= 2 && ubicacion_partes[1]) {
                        cargarCiudadesConSeleccion(estadoId, ubicacion_partes);
                    }
                    if (ubicacion_partes.length >= 3 && ubicacion_partes[2]) {
                        cargarMunicipiosConSeleccion(estadoId, ubicacion_partes);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estados:', error);
                cargarEstadosFallbackConSeleccion(ubicacion_partes);
            }
        });
    }

    function cargarCiudadesConSeleccion(id_estado, ubicacion_partes) {
        if (!id_estado) return;
        
        console.log('Cargando ciudades para estado:', id_estado);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/ciudades',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta ciudades:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
                var ciudades = [];
                if (response.success && response.data) {
                    ciudades = response.data;
                } else if (Array.isArray(response)) {
                    ciudades = response;
                } else if (response.ciudades) {
                    ciudades = response.ciudades;
                } else {
                    ciudades = response;
                }
                
                if (!Array.isArray(ciudades)) {
                    ciudades = [];
                }
                
                let options = '<option value="">Seleccione una ciudad...</option>';
                let ciudadId = null;
                let ciudadSeleccionada = ubicacion_partes[1] || '';
                
                for (let i = 0; i < ciudades.length; i++) {
                    let ciudad = ciudades[i];
                    let id = ciudad.id_ciudad || ciudad.id;
                    let nombre = ciudad.ciudad || ciudad.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                    if (nombre === ciudadSeleccionada) {
                        ciudadId = id;
                    }
                }
                $('#ciudad').html(options).prop('disabled', false);
                
                if (ciudadId) {
                    $('#ciudad').val(ciudadId);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar ciudades:', error);
                $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
            }
        });
    }

    function cargarMunicipiosConSeleccion(id_estado, ubicacion_partes) {
        if (!id_estado) return;
        
        console.log('Cargando municipios para estado:', id_estado);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/municipios',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta municipios:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
                var municipios = [];
                if (response.success && response.data) {
                    municipios = response.data;
                } else if (Array.isArray(response)) {
                    municipios = response;
                } else if (response.municipios) {
                    municipios = response.municipios;
                } else {
                    municipios = response;
                }
                
                if (!Array.isArray(municipios)) {
                    municipios = [];
                }
                
                let options = '<option value="">Seleccione un municipio...</option>';
                let municipioId = null;
                let municipioSeleccionado = ubicacion_partes[2] || '';
                
                for (let i = 0; i < municipios.length; i++) {
                    let municipio = municipios[i];
                    let id = municipio.id_municipio || municipio.id;
                    let nombre = municipio.municipio || municipio.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                    if (nombre === municipioSeleccionado) {
                        municipioId = id;
                    }
                }
                $('#municipio').html(options).prop('disabled', false);
                
                if (municipioId) {
                    $('#municipio').val(municipioId);
                    if (ubicacion_partes.length >= 4 && ubicacion_partes[3]) {
                        cargarParroquiasConSeleccion(municipioId, ubicacion_partes);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar municipios:', error);
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
            }
        });
    }

    function cargarParroquiasConSeleccion(id_municipio, ubicacion_partes) {
        if (!id_municipio) return;
        
        console.log('Cargando parroquias para municipio:', id_municipio);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/parroquias',
            type: 'POST',
            data: { id_municipio: id_municipio },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta parroquias:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
                var parroquias = [];
                if (response.success && response.data) {
                    parroquias = response.data;
                } else if (Array.isArray(response)) {
                    parroquias = response;
                } else if (response.parroquias) {
                    parroquias = response.parroquias;
                } else {
                    parroquias = response;
                }
                
                if (!Array.isArray(parroquias)) {
                    parroquias = [];
                }
                
                let options = '<option value="">Seleccione una parroquia...</option>';
                let parroquiaId = null;
                let parroquiaSeleccionada = ubicacion_partes[3] || '';
                
                for (let i = 0; i < parroquias.length; i++) {
                    let parroquia = parroquias[i];
                    let id = parroquia.id_parroquia || parroquia.id;
                    let nombre = parroquia.parroquia || parroquia.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                    if (nombre === parroquiaSeleccionada) {
                        parroquiaId = id;
                    }
                }
                $('#parroquia').html(options).prop('disabled', false);
                
                if (parroquiaId) {
                    $('#parroquia').val(parroquiaId);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar parroquias:', error);
                $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
            }
        });
    }

    function cargarEstados() {
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta estados (sin selección):', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
                var estados = [];
                if (response.success && response.data) {
                    estados = response.data;
                } else if (Array.isArray(response)) {
                    estados = response;
                } else if (response.estados) {
                    estados = response.estados;
                } else {
                    estados = response;
                }
                
                if (!Array.isArray(estados)) {
                    console.error('Estados no es un array:', estados);
                    estados = [];
                }
                
                let options = '<option value="">Seleccione un estado...</option>';
                for (let i = 0; i < estados.length; i++) {
                    let estado = estados[i];
                    let id = estado.id_estado || estado.id;
                    let nombre = estado.estado || estado.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                }
                $('#estado').html(options).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estados:', error);
                cargarEstadosFallback();
            }
        });
    }

    function cargarEstadosFallback() {
        console.log('Usando fallback de estados');
        const estados = [
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
        let options = '<option value="">Seleccione un estado...</option>';
        for (let i = 0; i < estados.length; i++) {
            options += `<option value="${estados[i].id_estado}">${estados[i].estado}</option>`;
        }
        $('#estado').html(options).prop('disabled', false);
    }

    function cargarEstadosFallbackConSeleccion(ubicacion_partes) {
        console.log('Usando fallback de estados con selección');
        const estados = [
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
        
        let options = '<option value="">Seleccione un estado...</option>';
        let estadoId = null;
        let estadoSeleccionado = ubicacion_partes[0] || '';
        
        for (let i = 0; i < estados.length; i++) {
            options += `<option value="${estados[i].id_estado}">${estados[i].estado}</option>`;
            if (estados[i].estado === estadoSeleccionado) {
                estadoId = estados[i].id_estado;
            }
        }
        $('#estado').html(options).prop('disabled', false);
        
        if (estadoId) {
            $('#estado').val(estadoId);
        }
    }

    function cargarCiudades(id_estado) {
        if (!id_estado) {
            $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            return;
        }
        
        $('#ciudad').html('<option value="">Cargando ciudades...</option>').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/ciudades',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(response) {
                var ciudades = [];
                if (response.success && response.data) {
                    ciudades = response.data;
                } else if (Array.isArray(response)) {
                    ciudades = response;
                } else {
                    ciudades = response;
                }
                
                if (!Array.isArray(ciudades)) {
                    ciudades = [];
                }
                
                let options = '<option value="">Seleccione una ciudad...</option>';
                for (let i = 0; i < ciudades.length; i++) {
                    let ciudad = ciudades[i];
                    let id = ciudad.id_ciudad || ciudad.id;
                    let nombre = ciudad.ciudad || ciudad.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                }
                $('#ciudad').html(options).prop('disabled', false);
            },
            error: function() {
                $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
            }
        });
    }

    function cargarMunicipios(id_estado) {
        if (!id_estado) {
            $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            return;
        }
        
        $('#municipio').html('<option value="">Cargando municipios...</option>').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/municipios',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(response) {
                var municipios = [];
                if (response.success && response.data) {
                    municipios = response.data;
                } else if (Array.isArray(response)) {
                    municipios = response;
                } else {
                    municipios = response;
                }
                
                if (!Array.isArray(municipios)) {
                    municipios = [];
                }
                
                let options = '<option value="">Seleccione un municipio...</option>';
                for (let i = 0; i < municipios.length; i++) {
                    let municipio = municipios[i];
                    let id = municipio.id_municipio || municipio.id;
                    let nombre = municipio.municipio || municipio.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                }
                $('#municipio').html(options).prop('disabled', false);
                $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            },
            error: function() {
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
            }
        });
    }

    function cargarParroquias(id_municipio) {
        if (!id_municipio) {
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            return;
        }
        
        $('#parroquia').html('<option value="">Cargando parroquias...</option>').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/parroquias',
            type: 'POST',
            data: { id_municipio: id_municipio },
            dataType: 'json',
            success: function(response) {
                var parroquias = [];
                if (response.success && response.data) {
                    parroquias = response.data;
                } else if (Array.isArray(response)) {
                    parroquias = response;
                } else {
                    parroquias = response;
                }
                
                if (!Array.isArray(parroquias)) {
                    parroquias = [];
                }
                
                let options = '<option value="">Seleccione una parroquia...</option>';
                for (let i = 0; i < parroquias.length; i++) {
                    let parroquia = parroquias[i];
                    let id = parroquia.id_parroquia || parroquia.id;
                    let nombre = parroquia.parroquia || parroquia.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                }
                $('#parroquia').html(options).prop('disabled', false);
            },
            error: function() {
                $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
            }
        });
    }

    // ==================== EVENTOS DE UBICACIÓN ====================
    $(document).on('change', '#estado', function() {
        let id_estado = $(this).val();
        if (id_estado) {
            cargarCiudades(id_estado);
            cargarMunicipios(id_estado);
        } else {
            $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });

    $(document).on('change', '#municipio', function() {
        let id_municipio = $(this).val();
        if (id_municipio) {
            cargarParroquias(id_municipio);
        } else {
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });

    // ==================== BOTÓN EDITAR ====================
    $(document).on('click', '.edit, .btn-editor', function(e) {
<<<<<<< HEAD
=======
                actualizarUI(paciente);
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
            },
            error: function(xhr, status, error) {
                console.error('Error en la petición AJAX:', error);
                $('#nombre_us').html('Error de conexión: ' + status);
                cargarEstados();
            }
        });
    }

    // ==================== FUNCIONES DE UBICACIÓN ====================
    
    function cargarDireccionEnCampos(direccion_completa) {
        console.log('Parseando dirección:', direccion_completa);
        
        if (!direccion_completa || direccion_completa === '-') {
            console.log('No hay dirección guardada');
            cargarEstados();
            return;
        }
        
        let direccion_detallada = '';
        let ubicacion = direccion_completa;
        
        if (direccion_completa.includes(' - ')) {
            let partes = direccion_completa.split(' - ');
            ubicacion = partes[0];
            direccion_detallada = partes.slice(1).join(' - ');
        }
        
        let ubicacion_partes = ubicacion.split(', ').filter(p => p.trim() !== '');
        console.log('Partes de ubicación:', ubicacion_partes);
        
        $('#direccion_detallada').val(direccion_detallada);
        
        cargarEstadosConSeleccion(ubicacion_partes);
    }

    function cargarEstadosConSeleccion(ubicacion_partes) {
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta de estados:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
                var estados = [];
                if (response.success && response.data) {
                    estados = response.data;
                } else if (Array.isArray(response)) {
                    estados = response;
                } else if (response.estados) {
                    estados = response.estados;
                } else {
                    estados = response;
                }
                
                // Asegurar que sea un array
                if (!Array.isArray(estados)) {
                    console.error('Estados no es un array:', estados);
                    estados = [];
                }
                
                console.log('Estados procesados:', estados.length);
                
                let options = '<option value="">Seleccione un estado...</option>';
                let estadoId = null;
                let estadoSeleccionado = ubicacion_partes[0] || '';
                
                for (let i = 0; i < estados.length; i++) {
                    let estado = estados[i];
                    let id = estado.id_estado || estado.id;
                    let nombre = estado.estado || estado.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                    if (nombre === estadoSeleccionado) {
                        estadoId = id;
                    }
                }
                $('#estado').html(options).prop('disabled', false);
                
                if (estadoId) {
                    $('#estado').val(estadoId);
                    if (ubicacion_partes.length >= 2 && ubicacion_partes[1]) {
                        cargarCiudadesConSeleccion(estadoId, ubicacion_partes);
                    }
                    if (ubicacion_partes.length >= 3 && ubicacion_partes[2]) {
                        cargarMunicipiosConSeleccion(estadoId, ubicacion_partes);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estados:', error);
                cargarEstadosFallbackConSeleccion(ubicacion_partes);
            }
        });
    }

    function cargarCiudadesConSeleccion(id_estado, ubicacion_partes) {
        if (!id_estado) return;
        
        console.log('Cargando ciudades para estado:', id_estado);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/ciudades',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta ciudades:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
                var ciudades = [];
                if (response.success && response.data) {
                    ciudades = response.data;
                } else if (Array.isArray(response)) {
                    ciudades = response;
                } else if (response.ciudades) {
                    ciudades = response.ciudades;
                } else {
                    ciudades = response;
                }
                
                if (!Array.isArray(ciudades)) {
                    ciudades = [];
                }
                
                let options = '<option value="">Seleccione una ciudad...</option>';
                let ciudadId = null;
                let ciudadSeleccionada = ubicacion_partes[1] || '';
                
                for (let i = 0; i < ciudades.length; i++) {
                    let ciudad = ciudades[i];
                    let id = ciudad.id_ciudad || ciudad.id;
                    let nombre = ciudad.ciudad || ciudad.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                    if (nombre === ciudadSeleccionada) {
                        ciudadId = id;
                    }
                }
                $('#ciudad').html(options).prop('disabled', false);
                
                if (ciudadId) {
                    $('#ciudad').val(ciudadId);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar ciudades:', error);
                $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
            }
        });
    }

    function cargarMunicipiosConSeleccion(id_estado, ubicacion_partes) {
        if (!id_estado) return;
        
        console.log('Cargando municipios para estado:', id_estado);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/municipios',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta municipios:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
                var municipios = [];
                if (response.success && response.data) {
                    municipios = response.data;
                } else if (Array.isArray(response)) {
                    municipios = response;
                } else if (response.municipios) {
                    municipios = response.municipios;
                } else {
                    municipios = response;
                }
                
                if (!Array.isArray(municipios)) {
                    municipios = [];
                }
                
                let options = '<option value="">Seleccione un municipio...</option>';
                let municipioId = null;
                let municipioSeleccionado = ubicacion_partes[2] || '';
                
                for (let i = 0; i < municipios.length; i++) {
                    let municipio = municipios[i];
                    let id = municipio.id_municipio || municipio.id;
                    let nombre = municipio.municipio || municipio.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                    if (nombre === municipioSeleccionado) {
                        municipioId = id;
                    }
                }
                $('#municipio').html(options).prop('disabled', false);
                
                if (municipioId) {
                    $('#municipio').val(municipioId);
                    if (ubicacion_partes.length >= 4 && ubicacion_partes[3]) {
                        cargarParroquiasConSeleccion(municipioId, ubicacion_partes);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar municipios:', error);
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
            }
        });
    }

    function cargarParroquiasConSeleccion(id_municipio, ubicacion_partes) {
        if (!id_municipio) return;
        
        console.log('Cargando parroquias para municipio:', id_municipio);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/parroquias',
            type: 'POST',
            data: { id_municipio: id_municipio },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta parroquias:', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
                var parroquias = [];
                if (response.success && response.data) {
                    parroquias = response.data;
                } else if (Array.isArray(response)) {
                    parroquias = response;
                } else if (response.parroquias) {
                    parroquias = response.parroquias;
                } else {
                    parroquias = response;
                }
                
                if (!Array.isArray(parroquias)) {
                    parroquias = [];
                }
                
                let options = '<option value="">Seleccione una parroquia...</option>';
                let parroquiaId = null;
                let parroquiaSeleccionada = ubicacion_partes[3] || '';
                
                for (let i = 0; i < parroquias.length; i++) {
                    let parroquia = parroquias[i];
                    let id = parroquia.id_parroquia || parroquia.id;
                    let nombre = parroquia.parroquia || parroquia.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                    if (nombre === parroquiaSeleccionada) {
                        parroquiaId = id;
                    }
                }
                $('#parroquia').html(options).prop('disabled', false);
                
                if (parroquiaId) {
                    $('#parroquia').val(parroquiaId);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar parroquias:', error);
                $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
            }
        });
    }

    function cargarEstados() {
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta estados (sin selección):', response);
                
                // ========== MANEJAR FORMATO ApiResponse ==========
                var estados = [];
                if (response.success && response.data) {
                    estados = response.data;
                } else if (Array.isArray(response)) {
                    estados = response;
                } else if (response.estados) {
                    estados = response.estados;
                } else {
                    estados = response;
                }
                
                if (!Array.isArray(estados)) {
                    console.error('Estados no es un array:', estados);
                    estados = [];
                }
                
                let options = '<option value="">Seleccione un estado...</option>';
                for (let i = 0; i < estados.length; i++) {
                    let estado = estados[i];
                    let id = estado.id_estado || estado.id;
                    let nombre = estado.estado || estado.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                }
                $('#estado').html(options).prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estados:', error);
                cargarEstadosFallback();
            }
        });
    }

    function cargarEstadosFallback() {
        console.log('Usando fallback de estados');
        const estados = [
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
        let options = '<option value="">Seleccione un estado...</option>';
        for (let i = 0; i < estados.length; i++) {
            options += `<option value="${estados[i].id_estado}">${estados[i].estado}</option>`;
        }
        $('#estado').html(options).prop('disabled', false);
    }

    function cargarEstadosFallbackConSeleccion(ubicacion_partes) {
        console.log('Usando fallback de estados con selección');
        const estados = [
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
        
        let options = '<option value="">Seleccione un estado...</option>';
        let estadoId = null;
        let estadoSeleccionado = ubicacion_partes[0] || '';
        
        for (let i = 0; i < estados.length; i++) {
            options += `<option value="${estados[i].id_estado}">${estados[i].estado}</option>`;
            if (estados[i].estado === estadoSeleccionado) {
                estadoId = estados[i].id_estado;
            }
        }
        $('#estado').html(options).prop('disabled', false);
        
        if (estadoId) {
            $('#estado').val(estadoId);
        }
    }

    function cargarCiudades(id_estado) {
        if (!id_estado) {
            $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            return;
        }
        
        $('#ciudad').html('<option value="">Cargando ciudades...</option>').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/ciudades',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(response) {
                var ciudades = [];
                if (response.success && response.data) {
                    ciudades = response.data;
                } else if (Array.isArray(response)) {
                    ciudades = response;
                } else {
                    ciudades = response;
                }
                
                if (!Array.isArray(ciudades)) {
                    ciudades = [];
                }
                
                let options = '<option value="">Seleccione una ciudad...</option>';
                for (let i = 0; i < ciudades.length; i++) {
                    let ciudad = ciudades[i];
                    let id = ciudad.id_ciudad || ciudad.id;
                    let nombre = ciudad.ciudad || ciudad.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                }
                $('#ciudad').html(options).prop('disabled', false);
            },
            error: function() {
                $('#ciudad').html('<option value="">Error al cargar ciudades</option>').prop('disabled', false);
            }
        });
    }

    function cargarMunicipios(id_estado) {
        if (!id_estado) {
            $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            return;
        }
        
        $('#municipio').html('<option value="">Cargando municipios...</option>').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/municipios',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            success: function(response) {
                var municipios = [];
                if (response.success && response.data) {
                    municipios = response.data;
                } else if (Array.isArray(response)) {
                    municipios = response;
                } else {
                    municipios = response;
                }
                
                if (!Array.isArray(municipios)) {
                    municipios = [];
                }
                
                let options = '<option value="">Seleccione un municipio...</option>';
                for (let i = 0; i < municipios.length; i++) {
                    let municipio = municipios[i];
                    let id = municipio.id_municipio || municipio.id;
                    let nombre = municipio.municipio || municipio.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                }
                $('#municipio').html(options).prop('disabled', false);
                $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            },
            error: function() {
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
            }
        });
    }

    function cargarParroquias(id_municipio) {
        if (!id_municipio) {
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
            return;
        }
        
        $('#parroquia').html('<option value="">Cargando parroquias...</option>').prop('disabled', false);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/parroquias',
            type: 'POST',
            data: { id_municipio: id_municipio },
            dataType: 'json',
            success: function(response) {
                var parroquias = [];
                if (response.success && response.data) {
                    parroquias = response.data;
                } else if (Array.isArray(response)) {
                    parroquias = response;
                } else {
                    parroquias = response;
                }
                
                if (!Array.isArray(parroquias)) {
                    parroquias = [];
                }
                
                let options = '<option value="">Seleccione una parroquia...</option>';
                for (let i = 0; i < parroquias.length; i++) {
                    let parroquia = parroquias[i];
                    let id = parroquia.id_parroquia || parroquia.id;
                    let nombre = parroquia.parroquia || parroquia.nombre;
                    options += `<option value="${id}">${nombre}</option>`;
                }
                $('#parroquia').html(options).prop('disabled', false);
            },
            error: function() {
                $('#parroquia').html('<option value="">Error al cargar parroquias</option>').prop('disabled', false);
            }
        });
    }

    // ==================== EVENTOS DE UBICACIÓN ====================
    $(document).on('change', '#estado', function() {
        let id_estado = $(this).val();
        if (id_estado) {
            cargarCiudades(id_estado);
            cargarMunicipios(id_estado);
        } else {
            $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });

    $(document).on('change', '#municipio', function() {
        let id_municipio = $(this).val();
        if (id_municipio) {
            cargarParroquias(id_municipio);
        } else {
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
    });

    // ==================== BOTÓN EDITAR ====================
<<<<<<< HEAD
    $(document).on('click', '.edit, .btn-editor', function(e) {
=======
    $(document).on('click', '.edit', function(e) {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        e.preventDefault();
        edit = true;
        
        console.log('Editando paciente ID:', id_usuario);
        
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
        // Mostrar indicador de carga
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        $.ajax({
            url: APP_URL + '/api/pacientes/capturar-datos',
            type: 'POST',
            data: { id_paciente: id_usuario },
            dataType: 'json',
<<<<<<< HEAD
<<<<<<< HEAD
            success: function(response) {
                var paciente = response.success && response.data ? response.data : response;
                console.log('Datos a editar:', paciente);
=======
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            success: function(response) {
                var paciente = response.success && response.data ? response.data : response;
                console.log('Datos a editar:', paciente);
                
                if (paciente.error) {
                    alert('Error: ' + paciente.error);
                    return;
                }
                
                // Cargar datos en los campos
                $('#telefono').val(paciente.telefono || '');
                $('#correo').val(paciente.correo || '');
                $('#sexo').val(paciente.sexo || '');
                $('#adicional').val(paciente.adicional || '');
                
                // Habilitar campos de edición
                $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', false);
<<<<<<< HEAD
=======
            success: function(paciente) {
                console.log('Datos para edición:', paciente);
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
                
                if (paciente.error) {
                    alert('Error: ' + paciente.error);
                    return;
                }
                
                // Cargar datos en los campos
                $('#telefono').val(paciente.telefono || '');
                $('#correo').val(paciente.correo || '');
                $('#sexo').val(paciente.sexo || '');
                $('#adicional').val(paciente.adicional || '');
                
<<<<<<< HEAD
                // Habilitar campos de edición
                $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', false);
=======
                // Habilitar campos de ubicación
                $('#estado').prop('disabled', false);
                $('#ciudad').prop('disabled', false);
                $('#municipio').prop('disabled', false);
                $('#parroquia').prop('disabled', false);
                $('#direccion_detallada').prop('disabled', false);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                
                // Cambiar estilo del botón guardar
                $('.btn-outline-success')
                    .removeClass('btn-outline-success')
                    .addClass('btn-success')
                    .prop('disabled', false);
                
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                // Cargar dirección existente en los selects
                if (paciente.direccion && paciente.direccion !== '-') {
                    cargarDireccionEnCampos(paciente.direccion);
                } else {
                    cargarEstados();
                }
                
                $('#editado').show(1000);
                setTimeout(function() { $('#editado').hide(2000); }, 2000);
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            },
            error: function(xhr, status, error) {
                console.error('Error al capturar datos:', error);
                alert('Error al cargar datos para edición: ' + status);
<<<<<<< HEAD
=======
            },
            error: function(xhr, status, error) {
                console.error('Error al capturar datos:', error);
                alert('Error al cargar datos para edición: ' + status);
=======
                mostrarMensaje('Campos habilitados para edición', 'success');
            },
            error: function(xhr, status, error) {
                console.error('Error al capturar datos:', error);
                mostrarMensaje('Error al cargar datos para edición: ' + status, 'error');
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            },
            complete: function() {
                $btn.html(originalText);
            }
        });
    });
<<<<<<< HEAD
<<<<<<< HEAD

    // ==================== FORMULARIO DE EDICIÓN - GUARDAR CAMBIOS ====================
    $('#form-usuario').off('submit').on('submit', function(e) {
=======
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0

    // ==================== FORMULARIO DE EDICIÓN - GUARDAR CAMBIOS ====================
    $('#form-usuario').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        if (!edit) {
            alert('Primero haga clic en "Editar"');
            return false;
        }
        
        // Construir dirección completa
<<<<<<< HEAD
=======
    
    // ==================== GUARDAR CAMBIOS ====================
    $('#form-usuario').submit(function(e) {
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        e.preventDefault();
        
        if (!edit) {
            alert('Primero haga clic en "Editar"');
            return false;
        }
        
<<<<<<< HEAD
        // Construir dirección completa
=======
        // Construir dirección completa desde los selects
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        var estado_nombre = $('#estado option:selected').text();
        var ciudad_nombre = $('#ciudad option:selected').text();
        var municipio_nombre = $('#municipio option:selected').text();
        var parroquia_nombre = $('#parroquia option:selected').text();
        var direccion_detallada = $('#direccion_detallada').val();
        
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        console.log('Estado seleccionado:', estado_nombre);
        console.log('Ciudad seleccionada:', ciudad_nombre);
        console.log('Municipio seleccionado:', municipio_nombre);
        console.log('Parroquia seleccionada:', parroquia_nombre);
        console.log('Dirección detallada:', direccion_detallada);
        
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        var direccion_completa = '';
        
        if (estado_nombre && estado_nombre !== 'Seleccione un estado...' && estado_nombre !== '') {
            direccion_completa = estado_nombre;
        }
        if (ciudad_nombre && ciudad_nombre !== 'Seleccione una ciudad...' && ciudad_nombre !== '' && ciudad_nombre !== 'Cargando ciudades...') {
            direccion_completa += (direccion_completa ? ', ' : '') + ciudad_nombre;
        }
        if (municipio_nombre && municipio_nombre !== 'Seleccione un municipio...' && municipio_nombre !== '' && municipio_nombre !== 'Cargando municipios...') {
            direccion_completa += (direccion_completa ? ', ' : '') + municipio_nombre;
        }
        if (parroquia_nombre && parroquia_nombre !== 'Seleccione una parroquia...' && parroquia_nombre !== '' && parroquia_nombre !== 'Cargando parroquias...') {
<<<<<<< HEAD
=======
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        var direccion_completa = '';
        
        if (estado_nombre && estado_nombre !== 'Seleccione un estado...' && estado_nombre !== '') {
            direccion_completa = estado_nombre;
        }
        if (ciudad_nombre && ciudad_nombre !== 'Seleccione una ciudad...' && ciudad_nombre !== '' && ciudad_nombre !== 'Cargando ciudades...') {
            direccion_completa += (direccion_completa ? ', ' : '') + ciudad_nombre;
        }
        if (municipio_nombre && municipio_nombre !== 'Seleccione un municipio...' && municipio_nombre !== '' && municipio_nombre !== 'Cargando municipios...') {
            direccion_completa += (direccion_completa ? ', ' : '') + municipio_nombre;
        }
<<<<<<< HEAD
        if (parroquia_nombre && parroquia_nombre !== 'Seleccione una parroquia...' && parroquia_nombre !== '' && parroquia_nombre !== 'Cargando parroquias...') {
=======
        if (parroquia_nombre && parroquia_nombre !== 'Seleccione una parroquia...' && parroquia_nombre !== 'Seleccione un municipio primero...') {
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            direccion_completa += (direccion_completa ? ', ' : '') + parroquia_nombre;
        }
        if (direccion_detallada && direccion_detallada !== '') {
            direccion_completa += (direccion_completa ? ' - ' : '') + direccion_detallada;
        }
        
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        var telefono = $('#telefono').val();
        var correo = $('#correo').val();
        var sexo = $('#sexo').val();
        var adicional = $('#adicional').val();
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        
        console.log('=== ENVIANDO DATOS ===');
        console.log('ID:', id_usuario);
        console.log('Dirección completa:', direccion_completa);
        console.log('Teléfono:', telefono);
        console.log('Correo:', correo);
        console.log('Sexo:', sexo);
        console.log('Adicional:', adicional);
<<<<<<< HEAD
=======
        var datos = {
            id_paciente: id_usuario,
            telefono: $('#telefono').val(),
            direccion: direccion_completa,
            correo: $('#correo').val(),
            sexo: $('#sexo').val(),
            adicional: $('#adicional').val()
        };
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
        
        console.log('=== ENVIANDO DATOS ===');
        console.log('ID:', id_usuario);
        console.log('Dirección completa:', direccion_completa);
<<<<<<< HEAD
        console.log('Teléfono:', telefono);
        console.log('Correo:', correo);
        console.log('Sexo:', sexo);
        console.log('Adicional:', adicional);
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/pacientes/editar',
            type: 'POST',
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            data: {
                id_paciente: id_usuario,
                telefono: telefono,
                direccion: direccion_completa,
                correo: correo,
                sexo: sexo,
                adicional: adicional
            },
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
            data: datos,
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success) {
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    $('#editado').show(1000);
                    setTimeout(function() { 
                        $('#editado').hide(2000); 
                    }, 3000);
                    
                    edit = false;
<<<<<<< HEAD
<<<<<<< HEAD
                    
                    // Deshabilitar campos después de guardar
                    $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
=======
                    
                    // Deshabilitar campos después de guardar
                    $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
=======
                    mostrarMensaje('Datos actualizados correctamente', 'success');
                    
                    // Deshabilitar campos después de guardar
                    $('#telefono, #correo, #sexo, #adicional').prop('disabled', true);
                    $('#estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
                    edit = false;
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                    
                    // Deshabilitar campos después de guardar
                    $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    
                    // Restaurar estilo del botón guardar
                    $('.btn-success')
                        .removeClass('btn-success')
                        .addClass('btn-outline-success')
                        .prop('disabled', true);
                    
<<<<<<< HEAD
<<<<<<< HEAD
                    // Recargar datos del paciente
                    buscar_paciente(id_usuario);
=======
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    // Recargar datos del paciente
                    buscar_paciente(id_usuario);
                    
                    alert('¡Datos actualizados correctamente!');
                } else {
<<<<<<< HEAD
=======
                    // Recargar datos actualizados
                    cargarDatosPaciente(id_usuario);
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
                    
                    alert('¡Datos actualizados correctamente!');
                } else {
<<<<<<< HEAD
=======
                    mostrarMensaje(response.error || 'Error al guardar', 'error');
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    $('#noeditado').show(1000);
                    setTimeout(function() { 
                        $('#noeditado').hide(2000); 
                    }, 3000);
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    alert(response.error || 'Error al guardar los cambios');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
<<<<<<< HEAD
=======
<<<<<<< HEAD
                    alert(response.error || 'Error al guardar los cambios');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
=======
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al editar:', error);
                mostrarMensaje('Error de conexión: ' + status, 'error');
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                $('#noeditado').show(1000);
                setTimeout(function() { 
                    $('#noeditado').hide(2000); 
                }, 3000);
<<<<<<< HEAD
<<<<<<< HEAD
                alert('Error de conexión: ' + status);
=======
<<<<<<< HEAD
                alert('Error de conexión: ' + status);
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                alert('Error de conexión: ' + status);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        
        return false;
    });

<<<<<<< HEAD
=======
<<<<<<< HEAD
        
        return false;
    });

=======
    });
    
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
    // ==================== CAMBIAR CONTRASEÑA ====================
    $('#form-pass').submit(function(e) {
        e.preventDefault();
        
        var oldpass = $('#oldpass').val();
        var newpass = $('#newpass').val();
        
        if (newpass.length < 6) {
<<<<<<< HEAD
<<<<<<< HEAD
            alert('La nueva contraseña debe tener al menos 6 caracteres');
            return false;
=======
<<<<<<< HEAD
            alert('La nueva contraseña debe tener al menos 6 caracteres');
            return false;
=======
            mostrarMensaje('La nueva contraseña debe tener al menos 6 caracteres', 'error');
            return;
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
            alert('La nueva contraseña debe tener al menos 6 caracteres');
            return false;
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        }
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: APP_URL + '/api/pacientes/cambiar-password',
            type: 'POST',
            data: {
                id_paciente: id_usuario,
                oldpass: oldpass,
                newpass: newpass
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta cambio contraseña:', response);
                
                if (response.resultado === 'update') {
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    $('#update').show(1000);
                    setTimeout(function() { 
                        $('#update').hide(2000); 
                        $('#cambiocontra').modal('hide');
                    }, 2000);
                    $('#form-pass').trigger('reset');
                } else {
                    $('#noupdate').show(1000);
                    setTimeout(function() { 
                        $('#noupdate').hide(2000); 
                    }, 2000);
<<<<<<< HEAD
=======
                    mostrarMensaje('Contraseña actualizada correctamente', 'success');
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
                    $('#update').show(1000);
                    setTimeout(function() { 
                        $('#update').hide(2000); 
                        $('#cambiocontra').modal('hide');
                    }, 2000);
                    $('#form-pass').trigger('reset');
                } else {
                    $('#noupdate').show(1000);
                    setTimeout(function() { 
                        $('#noupdate').hide(2000); 
<<<<<<< HEAD
                    }, 2000);
=======
                    }, 3000);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar contraseña:', error);
<<<<<<< HEAD
<<<<<<< HEAD
                alert('Error de conexión: ' + status);
=======
<<<<<<< HEAD
                alert('Error de conexión: ' + status);
=======
                mostrarMensaje('Error de conexión: ' + status, 'error');
                $('#noupdate').show(1000);
                setTimeout(function() { 
                    $('#noupdate').hide(2000); 
                }, 3000);
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                alert('Error de conexión: ' + status);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0

    // ==================== CAMBIAR FOTO ====================
    $('#form-photo').submit(function(e) {
        e.preventDefault();
        
        var fileInput = $(this).find('input[type="file"]')[0];
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Por favor seleccione una imagen');
            return;
        }
        
<<<<<<< HEAD
=======
<<<<<<< HEAD

    // ==================== CAMBIAR FOTO ====================
    $('#form-photo').submit(function(e) {
        e.preventDefault();
        
        var fileInput = $(this).find('input[type="file"]')[0];
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Por favor seleccione una imagen');
            return;
        }
        
=======
    
    // ==================== CAMBIAR AVATAR ====================
    $('#form-photo').submit(function(e) {
        e.preventDefault();
        
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        var formData = new FormData(this);
        formData.append('id_paciente', id_usuario);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
<<<<<<< HEAD
<<<<<<< HEAD
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
=======
<<<<<<< HEAD
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
=======
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        
        $.ajax({
            url: APP_URL + '/api/pacientes/cambiar-foto',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta cambio foto:', response);
                
                if (response.alert === 'edit') {
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    var timestamp = new Date().getTime();
                    var nuevaRuta = response.ruta + '?t=' + timestamp;
                    
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav').attr('src', nuevaRuta);
                    
<<<<<<< HEAD
<<<<<<< HEAD
=======
=======
                    var nuevaRuta = response.ruta;
                    if (nuevaRuta && nuevaRuta !== '') {
                        $('#avatar1, #avatar2, #avatar3, #avatar4').attr('src', nuevaRuta + '?t=' + new Date().getTime());
                    }
                    mostrarMensaje('Avatar actualizado correctamente', 'success');
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    $('#edit').show(1000);
                    setTimeout(function() { 
                        $('#edit').hide(2000); 
                    }, 3000);
<<<<<<< HEAD
<<<<<<< HEAD
                    
=======
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    
                    $('#form-photo').trigger('reset');
                    
                    setTimeout(function() { 
                        $('#cambiophoto').modal('hide'); 
                    }, 1500);
                    
                } else {
<<<<<<< HEAD
=======
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
                    $('#form-photo').trigger('reset');
                    
                    setTimeout(function() { 
                        $('#cambiophoto').modal('hide'); 
                    }, 1500);
                    
                } else {
<<<<<<< HEAD
=======
                    mostrarMensaje(response.error || 'Formato no admitido', 'error');
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                    $('#noedit').show(1000);
                    setTimeout(function() { 
                        $('#noedit').hide(2000); 
                    }, 3000);
<<<<<<< HEAD
<<<<<<< HEAD
                    alert(response.error || 'Error al cambiar la foto');
=======
<<<<<<< HEAD
                    alert(response.error || 'Error al cambiar la foto');
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                    alert(response.error || 'Error al cambiar la foto');
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar foto:', error);
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
                mostrarMensaje('Error de conexión: ' + status, 'error');
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
                $('#noedit').show(1000);
                setTimeout(function() { 
                    $('#noedit').hide(2000); 
                }, 3000);
<<<<<<< HEAD
<<<<<<< HEAD
                alert('Error al cambiar la foto. Verifique el tipo de archivo (JPG, PNG, GIF)');
=======
<<<<<<< HEAD
                alert('Error al cambiar la foto. Verifique el tipo de archivo (JPG, PNG, GIF)');
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
                alert('Error al cambiar la foto. Verifique el tipo de archivo (JPG, PNG, GIF)');
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
<<<<<<< HEAD
<<<<<<< HEAD

    // ==================== INICIALIZAR ====================
    buscar_paciente(id_usuario);
=======
<<<<<<< HEAD

    // ==================== INICIALIZAR ====================
    buscar_paciente(id_usuario);
=======
    
    // ==================== FUNCIÓN PARA MOSTRAR MENSAJES ====================
    function mostrarMensaje(mensaje, tipo) {
        console.log('Mensaje:', tipo, '-', mensaje);
        
        if (tipo === 'success') {
            // Usar SweetAlert si está disponible, si no, alert normal
            if (typeof Swal !== 'undefined') {
                Swal.fire('Éxito', mensaje, 'success');
            } else {
                alert('✓ Éxito: ' + mensaje);
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', mensaje, 'error');
            } else {
                alert('✗ Error: ' + mensaje);
            }
        }
    }
    
    // ==================== INICIALIZAR CARGA DE DATOS ====================
    cargarDatosPaciente(id_usuario);
    
    // ==================== EVENTOS ADICIONALES ====================
    // Limpiar formulario al cerrar modales
    $('#cambiocontra').on('hidden.bs.modal', function() {
        $('#form-pass').trigger('reset');
        $('#update, #noupdate').hide();
    });
    
    $('#cambiophoto').on('hidden.bs.modal', function() {
        $('#form-photo').trigger('reset');
        $('#edit, #noedit').hide();
    });
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======

    // ==================== INICIALIZAR ====================
    buscar_paciente(id_usuario);
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
});