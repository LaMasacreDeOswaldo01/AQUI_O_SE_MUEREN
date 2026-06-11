$(document).ready(function() {
    // ==================== VERIFICACIÓN INICIAL ====================
    if (typeof APP_URL === 'undefined') {
        console.error('ERROR: APP_URL no está definida');
        window.APP_URL = '';
    }

    console.log('=== PERFIL ASISTENTE - MIGRADO A API ===');
    console.log('APP_URL:', APP_URL);
    
    var id_usuario = $('#id_usuario').val();
    var edit = false;

    console.log('ID Asistente desde PHP:', id_usuario);

    if (!id_usuario || id_usuario === '') {
        console.error('ERROR: ID de asistente no encontrado');
        $('#nombre_us').html('Error: Sesión no válida');
        return;
    }
    // ==================== FUNCIÓN PRINCIPAL: CARGAR DATOS DEL ASISTENTE ====================
    function cargarDatosAsistente(dato) {
        console.log('Cargando datos del asistente ID:', dato);
        console.log('URL de petición:', APP_URL + '/api/asistentes/buscar');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/buscar',
            type: 'POST',
            data: { id_asistente: dato, dato: dato },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                // Manejar formato ApiResponse
                var asistente = response;
                if (response.success && response.data) {
                    asistente = response.data;
                }
                
                if (!response.success || asistente.error) {
                    console.error('Error:', asistente.error || response.message);
                    $('#nombre_us').html('Error: ' + (asistente.error || response.message));
                    return;
                }
                
                // Actualizar UI con los datos
                actualizarUI(asistente);
                
                // Cargar dirección en los campos de edición
                if (asistente.direccion && asistente.direccion !== '-') {
                    cargarDireccionEnCampos(asistente.direccion);
                } else {
                    cargarEstados();
                }
                
                console.log('Datos actualizados correctamente');
            },
            error: function(xhr, status, error) {
                console.error('Error en la petición AJAX:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                $('#nombre_us').html('Error de conexión: ' + status);
                cargarEstados();
            }
        });
    }    
    // ==================== ACTUALIZAR INTERFAZ ====================
    function actualizarUI(asistente) {
        console.log('Actualizando UI con datos:', asistente);
        
        // Actualizar los campos del perfil
        $('#nombre_us').html(asistente.nombre || 'No disponible');
        $('#apellidos_us').html(asistente.apellidos || 'No disponible');
        $('#edad').html(asistente.fecha_nacimiento || 'No disponible');
        $('#cedula_us').html(asistente.cedula || 'No disponible');
        $('#us_tipo').html(asistente.tipo || 'Asistente');
        $('#telefono_us').html(asistente.telefono || '-');
        $('#correo_us').html(asistente.correo || '-');
        $('#sexo_us').html(asistente.sexo || '-');
        $('#adicional_us').html(asistente.adicional || '-');
        $('#direccion_us').html(asistente.direccion || '-');
        
        // Cargar datos en los campos del formulario de edición
        $('#telefono').val(asistente.telefono || '');
        $('#correo').val(asistente.correo || '');
        $('#sexo').val(asistente.sexo || '');
        $('#adicional').val(asistente.adicional || '');
        $('#direccion_detallada').val('');
        
        // Mini stats para el dashboard
        $('#mini_recetas').text(asistente.total_recetas || 0);
        $('#mini_pacientes').text(asistente.total_pacientes || 0);
        $('#mini_medicos').text(asistente.total_medicos || 0);
        
        // Actualizar avatar
        if (asistente.avatar) {
            var avatarUrl = asistente.avatar;
            // Si la URL es relativa, agregar APP_URL
            if (avatarUrl.indexOf('http') === -1 && avatarUrl.indexOf('/') === 0) {
                avatarUrl = APP_URL + avatarUrl;
            } else if (avatarUrl.indexOf('http') === -1) {
                avatarUrl = APP_URL + '/' + avatarUrl;
            }
            var timestamp = new Date().getTime();
            avatarUrl = avatarUrl + '?t=' + timestamp;
            $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav, #avatar_modal').attr('src', avatarUrl);
            console.log('Avatar actualizado:', avatarUrl);
        } else {
            var defaultAvatar = APP_URL + '/img/avatarDES.jpg?t=' + new Date().getTime();
            $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav, #avatar_modal').attr('src', defaultAvatar);
        }
    }    
    // ==================== FUNCIONES DE UBICACIÓN COMPLETAS ====================    
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
        
        if (ubicacion_partes.length >= 1 && ubicacion_partes[0]) {
            cargarEstadosConSeleccion(ubicacion_partes);
        } else {
            cargarEstados();
        }
    }

    function cargarEstadosConSeleccion(ubicacion_partes) {
        console.log('Cargando estados con selección:', ubicacion_partes);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta estados API:', response);
                
                // Manejar formato ApiResponse
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
                console.log('Respuesta ciudades API:', response);
                
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
                console.log('Respuesta municipios API:', response);
                
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
                console.log('Respuesta parroquias API:', response);
                
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
        console.log('Cargando lista de estados...');
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Respuesta estados:', response);
                
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
    $(document).on('click', '.edit', function(e) {
        e.preventDefault();
        edit = true;
        
        console.log('Editando asistente ID:', id_usuario);
        
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Cargando...');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/capturar-datos',
            type: 'POST',
            data: { id_asistente: id_usuario },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta capturar datos:', response);
                
                var datos = response;
                if (response.data && response.success) {
                    datos = response.data;
                }
                
                if (!response.success || datos.error) {
                    alert('Error: ' + (datos.error || response.message));
                    return;
                }
                
                // Llenar los campos del formulario
                $('#telefono').val(datos.telefono || '').prop('disabled', false);
                $('#correo').val(datos.correo || '').prop('disabled', false);
                $('#sexo').val(datos.sexo || '').prop('disabled', false);
                $('#adicional').val(datos.adicional || '').prop('disabled', false);
                $('#direccion_detallada').val(datos.direccion_detallada || '');
                
                // Habilitar campos de ubicación
                $('#estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', false);
                
                // Cambiar estilo del botón guardar
                $('.btn-outline-success')
                    .removeClass('btn-outline-success')
                    .addClass('btn-success')
                    .prop('disabled', false);
                
                // Cargar dirección existente en los selectores
                if (datos.direccion && datos.direccion !== '-') {
                    cargarDireccionEnCampos(datos.direccion);
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
        
        var datos = {
            id_asistente: id_usuario,
            telefono: $('#telefono').val(),
            direccion: direccion_completa,
            correo: $('#correo').val(),
            sexo: $('#sexo').val(),
            adicional: $('#adicional').val(),
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        console.log('Guardando datos:', datos);
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/editar',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta editar:', response);
                
                if (response.success) {
                    $('#editado').show(1000);
                    setTimeout(function() { 
                        $('#editado').hide(2000); 
                    }, 3000);
                    
                    // Resetear formulario
                    $('#form-usuario').trigger('reset');
                    $('#telefono, #correo, #sexo, #adicional').prop('disabled', true);
                    $('#estado, #ciudad, #municipio, #parroquia, #direccion_detallada').prop('disabled', true);
                    edit = false;
                    
                    $('.btn-success')
                        .removeClass('btn-success')
                        .addClass('btn-outline-success')
                        .prop('disabled', true);
                    
                    // Recargar datos del asistente
                    cargarDatosAsistente(id_usuario);
                    
                    alert('¡Datos actualizados correctamente!');
                } else {
                    $('#noeditado').show(1000);
                    setTimeout(function() { 
                        $('#noeditado').hide(2000); 
                    }, 3000);
                    alert(response.message || 'Error al guardar los cambios');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al editar:', error);
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
            url: APP_URL + '/api/asistentes/cambiar-password',
            type: 'POST',
            data: {
                id_asistente: id_usuario,
                oldpass: oldpass,
                newpass: newpass,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta cambio contraseña:', response);
                
                if (response.resultado === 'update' || response.success) {
                    $('#update').show(1000);
                    setTimeout(function() { 
                        $('#update').hide(2000); 
                        $('#cambiocontra').modal('hide');
                    }, 2000);
                    $('#form-pass').trigger('reset');
                } else {
                    $('#noupdate').show(1000);
                    setTimeout(function() { $('#noupdate').hide(2000); }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#noupdate').show(1000);
                setTimeout(function() { $('#noupdate').hide(2000); }, 2000);
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
        formData.append('id_asistente', id_usuario);
        formData.append('csrf_token', $('input[name="csrf_token"]').val());
        
        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: APP_URL + '/api/asistentes/cambiar-foto',
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta cambio foto:', response);
                
                if (response.alert === 'edit' || response.success) {
                    var timestamp = new Date().getTime();
                    var nuevaRuta = (response.ruta || response.data.ruta) + '?t=' + timestamp;
                    
                    // Actualizar todas las instancias del avatar
                    $('#avatar1, #avatar2, #avatar3, #avatar4, #avatar_nav, #avatar_modal').attr('src', nuevaRuta);
                    
                    $('#edit').show(1000);
                    setTimeout(function() { 
                        $('#edit').hide(2000); 
                    }, 2000);
                    $('#form-photo').trigger('reset');
                    setTimeout(function() { 
                        $('#cambiophoto').modal('hide'); 
                    }, 1500);
                    
                    // Recargar datos para actualizar la sesión
                    cargarDatosAsistente(id_usuario);
                } else {
                    $('#noedit').show(1000);
                    setTimeout(function() { 
                        $('#noedit').hide(2000); 
                    }, 2000);
                    alert(response.message || 'Error al cambiar la foto');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cambiar foto:', error);
                $('#noedit').show(1000);
                setTimeout(function() { 
                    $('#noedit').hide(2000); 
                }, 2000);
                alert('Error al cambiar la foto. Verifique el tipo de archivo (JPG, PNG, GIF)');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });    
    // ==================== INICIALIZAR ====================
    cargarDatosAsistente(id_usuario);    
    // Función global para actualizar datos (útil si se necesita desde otros scripts)
    window.recargarDatosAsistente = function() {
        cargarDatosAsistente(id_usuario);
    };
});