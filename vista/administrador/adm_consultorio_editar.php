<?php
// vista/administrador/adm_consultorio_editar.php
// Contenido principal para la edición de consultorios
// Este archivo se renderiza dentro del layout base dashboard.php

// Los datos vienen del controlador a través de $data
$nombre_usuario = $nombre_usuario ?? 'Administrador';
$id_consultorio = $id_consultorio ?? $_GET['id'] ?? 0;
?>

<!-- CSS Adicional para esta vista -->
<style>
    .form-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .form-card .card-header {
        background: white;
        border-bottom: 2px solid var(--bv-primary);
        padding: 1.25rem 1.5rem;
    }
    .form-card .card-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
        color: var(--bv-dark);
    }
    .form-card .card-header h3 i {
        color: var(--bv-primary);
        margin-right: 0.5rem;
    }
    .form-card .card-body {
        padding: 1.5rem;
    }
    .preview-card {
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border-radius: 16px;
        border: 1px solid #eef2f6;
        transition: all 0.3s;
        position: sticky;
        top: 20px;
    }
    .preview-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 0.6rem 1rem;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--bv-primary);
        box-shadow: 0 0 0 3px rgba(0,119,182,0.1);
    }
    .checkbox-group {
        max-height: 250px;
        overflow-y: auto;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        background: #fafbfc;
    }
    .checkbox-group .form-check {
        margin-bottom: 0.6rem;
        padding-left: 1.8rem;
    }
    .checkbox-group .form-check-input {
        margin-left: -1.5rem;
    }
    .checkbox-group .form-check-label {
        font-size: 0.9rem;
        cursor: pointer;
    }
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    .btn-submit {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        border: none;
        border-radius: 12px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,119,182,0.3);
    }
    .btn-cancel {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        border: none;
        border-radius: 12px;
        padding: 0.8rem 2rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-cancel:hover {
        transform: translateY(-2px);
    }
    .alert-custom {
        border-radius: 12px;
        border: none;
        padding: 1rem;
    }
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--bv-dark);
        margin: 1.5rem 0 1rem 0;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #eef2f6;
    }
    .section-title i {
        color: var(--bv-primary);
        margin-right: 0.5rem;
    }
    .preview-nombre {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--bv-primary);
        word-break: break-word;
    }
    .preview-info {
        font-size: 0.85rem;
        color: var(--bv-text-light);
        margin-bottom: 0.5rem;
    }
    .badge-preview {
        background: #e8f4f8;
        color: #0d9488;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .row-selects {
        margin-bottom: 1rem;
    }
    .info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .info-card h6 {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--bv-primary);
        margin-bottom: 0.75rem;
    }
    .info-card p {
        font-size: 0.75rem;
        color: var(--bv-text-light);
        margin-bottom: 0.5rem;
    }
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 16px;
    }
    .card {
        position: relative;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-edit"></i> Editar Consultorio</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_consultorio" value="<?php echo $id_consultorio; ?>">
        
        <!-- Welcome Banner -->
        <div class="bv-welcome-banner admin bv-animate">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-edit me-2"></i> Editar Consultorio</h2>
                    <p class="mb-0">Modifique la información del consultorio según sea necesario.</p>
                    <div class="bv-role-tag mt-2">
                        <i class="fas fa-hospital-user"></i> Infraestructura
                    </div>
                </div>
                <div class="d-none d-md-block">
                    <i class="fas fa-chart-line fa-3x" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Formulario Principal -->
                <div class="form-card">
                    <div class="card-header">
                        <h3><i class="fas fa-edit"></i> Editar Información</h3>
                    </div>
                    <div class="card-body">
                        <!-- Alertas -->
                        <div class="alert alert-success alert-custom" id="alertExito" style="display:none;">
                            <i class="fas fa-check-circle"></i> <span id="exitoMensaje"></span>
                        </div>
                        <div class="alert alert-danger alert-custom" id="alertError" style="display:none;">
                            <i class="fas fa-exclamation-circle"></i> <span id="errorMensaje"></span>
                        </div>
                        
                        <div id="loadingDatos" class="loading-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        
                        <form id="formEditarConsultorio">
                            <?php echo Security::campoCSRF(); ?>
                            
                            <div class="form-group">
                                <label for="nombre" class="required-field">Nombre del Consultorio</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                <small class="form-text text-muted">Nombre descriptivo del consultorio</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="apertura" class="required-field">Apertura Habitual</label>
                                        <input type="time" class="form-control" id="apertura" name="apertura" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cierre" class="required-field">Cierre Habitual</label>
                                        <input type="time" class="form-control" id="cierre" name="cierre" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Especialidades Admitidas</label>
                                <div class="checkbox-group" id="especialidades_container">
                                    <div class="text-center py-3">
                                        <div class="spinner-border spinner-border-sm text-primary"></div>
                                        <span class="ml-2">Cargando especialidades...</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Seleccione las especialidades que se atienden en este consultorio</small>
                            </div>

                            <div class="section-title">
                                <i class="fas fa-map-marker-alt"></i> Ubicación
                            </div>

                            <div class="row row-selects">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estado" class="required-field">Estado</label>
                                        <select class="form-control" id="estado" name="estado" required>
                                            <option value="">Seleccione un estado...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ciudad" class="required-field">Ciudad</label>
                                        <select class="form-control" id="ciudad" name="ciudad" required disabled>
                                            <option value="">Primero seleccione un estado...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-selects">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="municipio">Municipio</label>
                                        <select class="form-control" id="municipio" name="municipio" disabled>
                                            <option value="">Primero seleccione un estado...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="parroquia">Parroquia</label>
                                        <select class="form-control" id="parroquia" name="parroquia" disabled>
                                            <option value="">Primero seleccione un municipio...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="direccion" class="required-field">Dirección Detallada</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                                <small class="form-text text-muted">Ej: Av. Principal, Edificio Central, Piso 3, Oficina 5</small>
                            </div>

                            <div class="section-title">
                                <i class="fas fa-address-card"></i> Datos de Contacto
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono Interno/Directo</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email del Consultorio</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="button" class="btn btn-secondary btn-cancel mr-2" id="btnCancelar">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary btn-submit" id="btnGuardar">
                                    <i class="fas fa-save"></i> Actualizar Consultorio
                                </button>
                            </div>
                            
                            <div class="csrf-info mt-3">
                                <i class="fas fa-shield-alt"></i> Formulario protegido contra CSRF
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Vista Previa -->
                <div class="preview-card p-3">
                    <div class="text-center mb-3">
                        <i class="fas fa-building fa-2x" style="color: var(--bv-primary);"></i>
                        <h5 class="mt-2 mb-0">Vista Previa</h5>
                        <small class="text-muted">Así se verá el consultorio</small>
                    </div>
                    <hr>
                    <div class="preview-nombre text-center" id="preview_nombre">
                        Nombre del Consultorio
                    </div>
                    <div class="preview-info text-center mt-2">
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        <span id="preview_ciudad">Ciudad no seleccionada</span>
                    </div>
                    <div class="preview-info text-center" id="preview_descripcion">
                        <em class="text-muted">Sin descripción</em>
                    </div>
                    <hr>
                    <div class="preview-info">
                        <i class="fas fa-phone text-success"></i>
                        <span id="preview_telefono">-</span>
                    </div>
                    <div class="preview-info">
                        <i class="fas fa-envelope text-info"></i>
                        <span id="preview_email">-</span>
                    </div>
                    <div class="preview-info">
                        <i class="fas fa-clock text-warning"></i>
                        <span id="preview_horario">08:00 - 17:00</span>
                    </div>
                    <hr>
                    <div class="text-center">
                        <span class="badge-preview">
                            <i class="fas fa-check-circle"></i> Consultorio activo
                        </span>
                    </div>
                </div>

                <!-- Info Adicional -->
                <div class="info-card">
                    <h6><i class="fas fa-info-circle"></i> Información importante</h6>
                    <p><i class="fas fa-check-circle text-success"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios</p>
                    <p><i class="fas fa-clock"></i> Los horarios pueden modificarse desde la sección de horarios</p>
                    <p><i class="fas fa-user-md"></i> Los médicos se asignan desde la página de detalle</p>
                </div>
                
                <!-- Botón Volver al Detalle -->
                <div class="info-card text-center">
                    <a href="<?php echo APP_URL; ?>/consultorios/detalle?id=<?php echo $id_consultorio; ?>" class="btn btn-info btn-block">
                        <i class="fas fa-arrow-left"></i> Volver al Detalle
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    console.log('=== FORMULARIO DE EDICIÓN DE CONSULTORIO ===');
    console.log('ID Consultorio:', $('#id_consultorio').val());
    
    // ==================== FUNCIONES DE UBICACIÓN ====================
    
    function cargarEstadosConSeleccion(estadoSeleccionado, ciudadSeleccionada, municipioSeleccionado, parroquiaSeleccionada) {
        console.log('Cargando estados con selección:', {estadoSeleccionado, ciudadSeleccionada, municipioSeleccionado, parroquiaSeleccionada});
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/estados',
            type: 'POST',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
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
                let estadoId = null;
                
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
                    cargarCiudadesConSeleccion(estadoId, ciudadSeleccionada, municipioSeleccionado, parroquiaSeleccionada);
                    cargarMunicipiosConSeleccion(estadoId, municipioSeleccionado, parroquiaSeleccionada);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estados:', error);
                cargarEstadosFallback();
            }
        });
    }
    
    function cargarCiudadesConSeleccion(id_estado, ciudadSeleccionada, municipioSeleccionado, parroquiaSeleccionada) {
        if (!id_estado) return;
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/ciudades',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
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
    
    function cargarMunicipiosConSeleccion(id_estado, municipioSeleccionado, parroquiaSeleccionada) {
        if (!id_estado) return;
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/municipios',
            type: 'POST',
            data: { id_estado: id_estado },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
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
                    if (parroquiaSeleccionada) {
                        cargarParroquiasConSeleccion(municipioId, parroquiaSeleccionada);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar municipios:', error);
                $('#municipio').html('<option value="">Error al cargar municipios</option>').prop('disabled', false);
            }
        });
    }
    
    function cargarParroquiasConSeleccion(id_municipio, parroquiaSeleccionada) {
        if (!id_municipio) return;
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/parroquias',
            type: 'POST',
            data: { id_municipio: id_municipio },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
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
    
    function cargarEstadosFallback() {
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
    
    function cargarListaEspecialidadesConSeleccion(especialidadesSeleccionadas) {
        $('#especialidades_container').html(`
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                <span class="ml-2">Cargando especialidades...</span>
            </div>
        `);
        
        $.ajax({
            url: APP_URL + '/api/ubicacion/especialidades',
            type: 'POST',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                var especialidades = [];
                if (response.success && response.data) {
                    especialidades = response.data;
                } else if (Array.isArray(response)) {
                    especialidades = response;
                } else if (response.especialidades) {
                    especialidades = response.especialidades;
                } else {
                    especialidades = response;
                }
                
                if (!Array.isArray(especialidades)) {
                    especialidades = [];
                }
                
                let html = '';
                for (let i = 0; i < especialidades.length; i++) {
                    let esp = especialidades[i];
                    let nombre = esp.especialidad || esp.nombre || esp;
                    let id = esp.id_especialidad || esp.id || i;
                    let checked = especialidadesSeleccionadas && especialidadesSeleccionadas.includes(nombre) ? 'checked' : '';
                    html += `
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="esp_${id}" value="${escapeHtml(nombre)}" ${checked}>
                            <label class="form-check-label" for="esp_${id}">${escapeHtml(nombre)}</label>
                        </div>
                    `;
                }
                $('#especialidades_container').html(html || '<p class="text-muted text-center">No hay especialidades disponibles</p>');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar especialidades:', error);
                $('#especialidades_container').html('<p class="text-danger text-center">Error al cargar especialidades</p>');
            }
        });
    }
    
    // ==================== CARGAR DATOS DEL CONSULTORIO ====================
    
    function cargarDatosConsultorio() {
        let id = $('#id_consultorio').val();
        
        if (!id || id === '0') {
            mostrarError('ID de consultorio no válido');
            return;
        }
        
        $('#loadingDatos').show();
        
        $.ajax({
            url: APP_URL + '/api/consultorios/obtener-detalle',
            type: 'POST',
            data: { id_consultorio: id },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                console.log('Datos del consultorio:', response);
                
                var data = response;
                if (response.success && response.data) {
                    data = response.data;
                }
                
                if (data.error) {
                    mostrarError('Error al cargar datos: ' + data.error);
                    $('#loadingDatos').hide();
                    return;
                }
                
                // Llenar formulario
                $('#nombre').val(data.nombre);
                $('#descripcion').val(data.descripcion || '');
                $('#apertura').val(data.apertura || '08:00');
                $('#cierre').val(data.cierre || '17:00');
                $('#telefono').val(data.telefono || '');
                $('#email').val(data.email || '');
                $('#direccion').val(data.direccion_detallada || '');
                
                // Cargar ubicación con selección
                cargarEstadosConSeleccion(data.estado, data.ciudad, data.municipio, data.parroquia);
                
                // Cargar especialidades con selección
                cargarListaEspecialidadesConSeleccion(data.especialidades || []);
                
                // Actualizar vista previa
                actualizarPreview();
                
                $('#loadingDatos').hide();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar datos:', error);
                $('#loadingDatos').hide();
                mostrarError('Error al cargar los datos del consultorio');
            }
        });
    }
    
    // ==================== VISTA PREVIA ====================
    
    function actualizarPreview() {
        $('#preview_nombre').text($('#nombre').val() || 'Nombre del Consultorio');
        
        var ciudadNombre = $('#ciudad option:selected').text();
        if (ciudadNombre && ciudadNombre !== 'Seleccione una ciudad...' && ciudadNombre !== 'Primero seleccione un estado...') {
            $('#preview_ciudad').text(ciudadNombre);
        } else {
            $('#preview_ciudad').text('Ciudad no seleccionada');
        }
        
        var descripcion = $('#descripcion').val();
        if (descripcion) {
            $('#preview_descripcion').html(descripcion.substring(0, 80) + (descripcion.length > 80 ? '...' : ''));
        } else {
            $('#preview_descripcion').html('<em class="text-muted">Sin descripción</em>');
        }
        
        $('#preview_telefono').text($('#telefono').val() || '-');
        $('#preview_email').text($('#email').val() || '-');
        $('#preview_horario').text($('#apertura').val() + ' - ' + $('#cierre').val());
    }
    
    // ==================== EVENTOS ====================
    
    $(document).on('change', '#estado', function() {
        let id_estado = $(this).val();
        if (id_estado) {
            cargarCiudadesConSeleccion(id_estado, '', '', '');
            cargarMunicipiosConSeleccion(id_estado, '', '');
        } else {
            $('#ciudad').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#municipio').html('<option value="">Seleccione un estado primero...</option>').prop('disabled', true);
            $('#parroquia').html('<option value="">Seleccione un municipio primero...</option>').prop('disabled', true);
        }
        actualizarPreview();
    });
    
    $(document).on('change', '#ciudad, #municipio', function() {
        actualizarPreview();
    });
    
    $('#nombre, #descripcion, #telefono, #email, #apertura, #cierre, #direccion').on('input change', function() {
        actualizarPreview();
    });
    
    $('#btnCancelar').click(function() {
        let id = $('#id_consultorio').val();
        if (confirm('¿Está seguro que desea cancelar? Los cambios no guardados se perderán.')) {
            window.location.href = APP_URL + '/consultorios/detalle?id=' + id;
        }
    });
    
    // ==================== ENVÍO DEL FORMULARIO ====================
    
    $('#formEditarConsultorio').submit(function(e) {
        e.preventDefault();
        
        let id = $('#id_consultorio').val();
        let nombre = $('#nombre').val().trim();
        
        if (!nombre) {
            mostrarError('El nombre del consultorio es requerido');
            $('#nombre').focus();
            return;
        }
        
        let estado = $('#estado').val();
        if (!estado) {
            mostrarError('Debe seleccionar un estado');
            $('#estado').focus();
            return;
        }
        
        let ciudad = $('#ciudad').val();
        if (!ciudad) {
            mostrarError('Debe seleccionar una ciudad');
            $('#ciudad').focus();
            return;
        }
        
        let direccion = $('#direccion').val().trim();
        if (!direccion) {
            mostrarError('La dirección detallada es requerida');
            $('#direccion').focus();
            return;
        }
        
        // Recopilar especialidades seleccionadas
        let especialidades = [];
        $('.checkbox-group input:checked').each(function() {
            especialidades.push($(this).val());
        });
        
        let datos = {
            id_consultorio: id,
            nombre: nombre,
            descripcion: $('#descripcion').val().trim(),
            apertura: $('#apertura').val(),
            cierre: $('#cierre').val(),
            telefono: $('#telefono').val().trim(),
            email: $('#email').val().trim(),
            estado: $('#estado option:selected').text(),
            ciudad: $('#ciudad option:selected').text(),
            municipio: $('#municipio option:selected').text() || '',
            parroquia: $('#parroquia option:selected').text() || '',
            direccion: direccion,
            especialidades: especialidades,
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        console.log('Enviando datos de actualización:', datos);
        
        let $btn = $('#btnGuardar');
        let originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
        
        $.ajax({
            url: APP_URL + '/api/consultorios/editar',
            type: 'POST',
            data: datos,
            dataType: 'json',
            timeout: 20000,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                
                if (response.resultado === 'editado') {
                    mostrarExito('Consultorio actualizado exitosamente');
                    setTimeout(function() {
                        window.location.href = APP_URL + '/consultorios/detalle?id=' + id;
                    }, 2000);
                } else if (response.resultado === 'error_csrf') {
                    mostrarError('Error de seguridad. Por favor, recargue la página.');
                    $btn.prop('disabled', false).html(originalText);
                } else {
                    let errorMsg = response.error || response.message || response.resultado || 'Error al actualizar el consultorio';
                    mostrarError(errorMsg);
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en AJAX:', error);
                let errorMsg = 'Error de conexión: ' + status;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                mostrarError(errorMsg);
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // ==================== FUNCIONES DE NOTIFICACIÓN ====================
    
    function mostrarError(mensaje) {
        $('#errorMensaje').text(mensaje);
        $('#alertError').fadeIn(300);
        setTimeout(function() {
            $('#alertError').fadeOut(500);
        }, 5000);
        
        $('html, body').animate({
            scrollTop: $('#alertError').offset().top - 100
        }, 500);
    }
    
    function mostrarExito(mensaje) {
        $('#exitoMensaje').text(mensaje);
        $('#alertExito').fadeIn(300);
        setTimeout(function() {
            $('#alertExito').fadeOut(500);
        }, 3000);
    }
    
    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
    
    // ==================== INICIALIZAR ====================
    cargarDatosConsultorio();
});
</script>