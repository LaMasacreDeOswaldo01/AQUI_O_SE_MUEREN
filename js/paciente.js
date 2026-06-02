/**
 * js/paciente.js 
 * Maneja correctamente el formato ApiResponse de la API de ubicación
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

    // ==================== FUNCIÓN PRINCIPAL: BUSCAR PACIENTE ====================
    function buscar_paciente(dato) {
        console.log('Buscando paciente con dato:', dato);
        
        $.ajax({
            url: APP_URL + '/api/pacientes/buscar',
            type: 'POST',
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
                    $('#nombre_us').html('Error: ' + paciente.error);
                    return;
                }
                
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
    $(document).on('click', '.edit, .btn-editor', function(e) {
        e.preventDefault();
        edit = true;
        
        console.log('Editando paciente ID:', id_usuario);
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        $.ajax({
            url: APP_URL + '/api/pacientes/capturar-datos',
            type: 'POST',
            data: { id_paciente: id_usuario },
            dataType: 'json',
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
                
                // Cambiar estilo del botón guardar
                $('.btn-outline-success')
                    .removeClass('btn-outline-success')
                    .addClass('btn-success')
                    .prop('disabled', false);
                
                // Cargar dirección existente en los selects
                if (paciente.direccion && paciente.direccion !== '-') {
                    cargarDireccionEnCampos(paciente.direccion);
                } else {
                    cargarEstados();
                }
                
                $('#editado').show(1000);
                setTimeout(function() { $('#editado').hide(2000); }, 2000);
            },
            error: function(xhr, status, error) {
                console.error('Error al capturar datos:', error);
                alert('Error al cargar datos para edición: ' + status);
            },
            complete: function() {
                $btn.html(originalText);
            }
        });
    });

    // ==================== FORMULARIO DE EDICIÓN - GUARDAR CAMBIOS ====================
    $('#form-usuario').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        if (!edit) {
            alert('Primero haga clic en "Editar"');
            return false;
        }
        
        // Construir dirección completa
        var estado_nombre = $('#estado option:selected').text();
        var ciudad_nombre = $('#ciudad option:selected').text();
        var municipio_nombre = $('#municipio option:selected').text();
        var parroquia_nombre = $('#parroquia option:selected').text();
        var direccion_detallada = $('#direccion_detallada').val();
        
        console.log('Estado seleccionado:', estado_nombre);
        console.log('Ciudad seleccionada:', ciudad_nombre);
        console.log('Municipio seleccionado:', municipio_nombre);
        console.log('Parroquia seleccionada:', parroquia_nombre);
        console.log('Dirección detallada:', direccion_detallada);
        
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
            direccion_completa += (direccion_completa ? ', ' : '') + parroquia_nombre;
        }
        if (direccion_detallada && direccion_detallada !== '') {
            direccion_completa += (direccion_completa ? ' - ' : '') + direccion_detallada;
        }
        
        var telefono = $('#telefono').val();
        var correo = $('#correo').val();
        var sexo = $('#sexo').val();
        var adicional = $('#adicional').val();
        
        console.log('=== ENVIANDO DATOS ===');
        console.log('ID:', id_usuario);
        console.log('Dirección completa:', direccion_completa);
        console.log('Teléfono:', telefono);
        console.log('Correo:', correo);
        console.log('Sexo:', sexo);
        console.log('Adicional:', adicional);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/pacientes/editar',
            type: 'POST',
            data: {
                id_paciente: id_usuario,
                telefono: telefono,
                direccion: direccion_completa,
                correo: correo,
                sexo: sexo,
                adicional: adicional
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.success) {
                    $('#editado').show(1000);
                    setTimeout(function() { 
                        $('#editado').hide(2000); 
                    }, 3000);
                    
                    edit = false;
                    
                    // Deshabilitar campos después de guardar
                    $('#telefono, #correo, #sexo, #adicional, #estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
                    
                    // Restaurar estilo del botón guardar
                    $('.btn-success')
                        .removeClass('btn-success')
                        .addClass('btn-outline-success')
                        .prop('disabled', true);
                    
                    // Recargar datos del paciente
                    buscar_paciente(id_usuario);
                    
                    alert('¡Datos actualizados correctamente!');
                } else {
                    $('#noeditado').show(1000);
                    setTimeout(function() { 
                        $('#noeditado').hide(2000); 
                    }, 3000);
                    alert(response.error || 'Error al guardar los cambios');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
                $('#noeditado').show(1000);
                setTimeout(function() { 
                    $('#noeditado').hide(2000); 
                }, 3000);
                alert('Error de conexión: ' + status);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
        
        return false;
    });

    // ==================== CAMBIAR CONTRASEÑA ====================
    $('#form-pass').submit(function(e) {
        e.preventDefault();
        
        var oldpass = $('#oldpass').val();
        var newpass = $('#newpass').val();
        
        if (newpass.length < 6) {
            alert('La nueva contraseña debe tener al menos 6 caracteres');
            return false;
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
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar contraseña:', error);
                alert('Error de conexión: ' + status);
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // ==================== CAMBIAR FOTO ====================
    $('#form-photo').submit(function(e) {
        e.preventDefault();
        
        var fileInput = $(this).find('input[type="file"]')[0];
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Por favor seleccione una imagen');
            return;
        }
        
        var formData = new FormData(this);
        formData.append('id_paciente', id_usuario);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');
        
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
                    var timestamp = new Date().getTime();
                    var nuevaRuta = response.ruta + '?t=' + timestamp;
                    
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav').attr('src', nuevaRuta);
                    
                    $('#edit').show(1000);
                    setTimeout(function() { 
                        $('#edit').hide(2000); 
                    }, 3000);
                    
                    $('#form-photo').trigger('reset');
                    
                    setTimeout(function() { 
                        $('#cambiophoto').modal('hide'); 
                    }, 1500);
                    
                } else {
                    $('#noedit').show(1000);
                    setTimeout(function() { 
                        $('#noedit').hide(2000); 
                    }, 3000);
                    alert(response.error || 'Error al cambiar la foto');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar foto:', error);
                $('#noedit').show(1000);
                setTimeout(function() { 
                    $('#noedit').hide(2000); 
                }, 3000);
                alert('Error al cambiar la foto. Verifique el tipo de archivo (JPG, PNG, GIF)');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // ==================== INICIALIZAR ====================
    buscar_paciente(id_usuario);
});