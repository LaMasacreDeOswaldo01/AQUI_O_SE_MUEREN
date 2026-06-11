// js/medico_evoluciones.js
$(document).ready(function() {
    console.log('=== EVOLUCIONES CLÍNICAS - MÉDICO ===');
    
    var id_medico = $('#id_medico').val();
    var citasData = [];
    var citaSeleccionada = null;
    var evolucionExistente = null;
    
    // ==================== CARGAR CITAS ====================
    
    function cargarCitas(busqueda = '') {
        $('#lista_citas').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary spinner-border-sm"></div>
                <p class="mt-2 mb-0 text-muted small">Cargando citas...</p>
            </div>
        `);
        
        $.ajax({
            url: APP_URL + '/api/evoluciones/citas',
            type: 'POST',
            data: { busqueda: busqueda },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    citasData = response.data;
                    renderizarListaCitas(citasData);
                } else {
                    $('#lista_citas').html('<div class="alert alert-danger">Error al cargar citas</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#lista_citas').html('<div class="alert alert-danger">Error de conexión</div>');
            }
        });
    }
    
    function renderizarListaCitas(citas) {
        if (citas.length === 0) {
            $('#lista_citas').html(`
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No hay citas disponibles</p>
                </div>
            `);
            return;
        }
        
        var html = '';
        for (var i = 0; i < citas.length; i++) {
            var c = citas[i];
            var estadoBadge = c.estado === 'completada' ? 
                '<span class="badge-evolucion badge-completada"><i class="fas fa-check-circle"></i> Completada</span>' : 
                '<span class="badge-evolucion badge-pendiente"><i class="fas fa-clock"></i> Pendiente</span>';
            
            var evolucionBadge = c.tiene_evolucion ? 
                '<span class="badge-evolucion badge-completada ml-1"><i class="fas fa-file-alt"></i> Evolución</span>' : '';
            
            html += `
                <div class="cita-item p-3 ${citaSeleccionada && citaSeleccionada.id_cita === c.id_cita ? 'active' : ''}" 
                     data-id="${c.id_cita}" 
                     data-paciente-id="${c.id_paciente || 0}"
                     data-paciente="${escapeHtml(c.paciente_nombre)}"
                     data-cedula="${c.paciente_cedula}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="cita-paciente">${escapeHtml(c.paciente_nombre)}</div>
                            <div class="cita-fecha">
                                <i class="far fa-calendar-alt"></i> ${c.fecha} ${c.hora}
                            </div>
                            <div class="cita-fecha mt-1">
                                <i class="fas fa-stethoscope"></i> ${escapeHtml(c.especialidad)}
                            </div>
                        </div>
                        <div class="text-right">
                            ${estadoBadge}
                            ${evolucionBadge}
                        </div>
                    </div>
                </div>
            `;
        }
        
        $('#lista_citas').html(html);
    }
    
    // ==================== SELECCIONAR CITA ====================
    
    $(document).on('click', '.cita-item', function() {
        var id_cita = $(this).data('id');
        var id_paciente = $(this).data('paciente-id');
        var paciente_nombre = $(this).data('paciente');
        
        console.log('Seleccionando cita ID:', id_cita);
        
        // Actualizar UI
        $('.cita-item').removeClass('active');
        $(this).addClass('active');
        
        // Limpiar formulario
        limpiarFormulario();
        
        // Cargar detalle de la cita
        cargarDetalleCita(id_cita, id_paciente, paciente_nombre);
    });
    
    function cargarDetalleCita(id_cita, id_paciente, paciente_nombre) {
        $('#loadingEvolucion').show();
        
        $.ajax({
            url: APP_URL + '/api/evoluciones/detalle-cita',
            type: 'POST',
            data: { id_cita: id_cita },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    citaSeleccionada = response.data;
                    $('#id_cita_seleccionada').val(id_cita);
                    $('#id_paciente_seleccionado').val(id_paciente);
                    
                    // Mostrar información del paciente
                    mostrarInfoPaciente(citaSeleccionada);
                    
                    // Cargar datos en el formulario
                    cargarDatosFormulario(citaSeleccionada.evolucion);
                    
                    // Mostrar formulario
                    $('#info_cita_seleccionada').show();
                    $('#form_evolucion').show();
                    $('#sin_cita_seleccionada').hide();
                } else {
                    mostrarAlerta(response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarAlerta('Error al cargar los datos de la cita', 'error');
            }
        });
    }
    
    function mostrarInfoPaciente(data) {
        var html = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong><i class="fas fa-user-injured"></i> Paciente:</strong> ${escapeHtml(data.paciente_nombre)}</p>
                    <p><strong><i class="fas fa-id-card"></i> Cédula:</strong> ${data.paciente_cedula}</p>
                    <p><strong><i class="fas fa-birthday-cake"></i> Edad:</strong> ${data.paciente_edad} años</p>
                    <p><strong><i class="fas fa-venus-mars"></i> Sexo:</strong> ${data.paciente_sexo}</p>
                </div>
                <div class="col-md-6">
                    <p><strong><i class="fas fa-stethoscope"></i> Especialidad:</strong> ${escapeHtml(data.especialidad)}</p>
                    <p><strong><i class="fas fa-calendar-alt"></i> Fecha Cita:</strong> ${data.fecha_cita} - ${data.hora_cita}</p>
                    <p><strong><i class="fas fa-building"></i> Consultorio:</strong> ${escapeHtml(data.consultorio)}</p>
                    <p><strong><i class="fas fa-tint"></i> Tipo Sangre:</strong> ${data.paciente_tipo_sangre || 'No registrado'}</p>
                </div>
            </div>
            <hr>
            <p><strong><i class="fas fa-notes-medical"></i> Motivo original:</strong> ${escapeHtml(data.motivo_original || 'No especificado')}</p>
        `;
        
        $('#info_cita_seleccionada').html(html);
    }
    
    function cargarDatosFormulario(evolucion) {
        if (evolucion) {
            // Cargar datos existentes
            $('#peso').val(evolucion.peso || '');
            $('#talla').val(evolucion.talla || '');
            $('#temperatura').val(evolucion.temperatura || '');
            $('#tension_sistolica').val(evolucion.tension_sistolica || '');
            $('#tension_diastolica').val(evolucion.tension_diastolica || '');
            $('#frecuencia_cardiaca').val(evolucion.frecuencia_cardiaca || '');
            $('#frecuencia_respiratoria').val(evolucion.frecuencia_respiratoria || '');
            $('#saturacion_oxigeno').val(evolucion.saturacion_oxigeno || '');
            $('#motivo_consulta').val(evolucion.motivo_consulta || '');
            $('#enfermedad_actual').val(evolucion.enfermedad_actual || '');
            $('#examen_fisico').val(evolucion.examen_fisico || '');
            $('#diagnostico').val(evolucion.diagnostico || '');
            $('#tratamiento').val(evolucion.tratamiento || '');
            $('#recomendaciones').val(evolucion.recomendaciones || '');
            $('#notas_adicionales').val(evolucion.notas_adicionales || '');
            
            calcularIMC();
        } else {
            limpiarFormulario();
        }
        
        // Actualizar IDs del formulario
        $('#evolucion_id_cita').val(citaSeleccionada.id_cita);
        $('#evolucion_id_paciente').val(citaSeleccionada.id_paciente);
    }
    
    function limpiarFormulario() {
        $('#peso, #talla, #temperatura, #tension_sistolica, #tension_diastolica, #frecuencia_cardiaca, #frecuencia_respiratoria, #saturacion_oxigeno').val('');
        $('#motivo_consulta, #enfermedad_actual, #examen_fisico, #diagnostico, #tratamiento, #recomendaciones, #notas_adicionales').val('');
        $('#imc').val('');
        $('#marcar_completada').val('0');
    }
    
    // ==================== CÁLCULO DE IMC ====================
    
    $('#peso, #talla').on('input', function() {
        calcularIMC();
    });
    
    function calcularIMC() {
        var peso = parseFloat($('#peso').val());
        var talla = parseFloat($('#talla').val());
        
        if (peso && talla && talla > 0) {
            var imc = peso / ((talla / 100) * (talla / 100));
            $('#imc').val(imc.toFixed(1));
        } else {
            $('#imc').val('');
        }
    }
    
    // ==================== GUARDAR EVOLUCIÓN ====================
    
    $('#formEvolucion').submit(function(e) {
        e.preventDefault();
        guardarEvolucion(false);
    });
    
    $('#btnGuardarCompletar').click(function() {
        guardarEvolucion(true);
    });
    
    function guardarEvolucion(marcarCompletada) {
        // Validar campos requeridos
        var motivo = $('#motivo_consulta').val().trim();
        var enfermedad = $('#enfermedad_actual').val().trim();
        var tratamiento = $('#tratamiento').val().trim();
        
        if (!motivo) {
            mostrarAlerta('El motivo de consulta es requerido', 'error');
            $('#motivo_consulta').focus();
            return;
        }
        
        if (!enfermedad) {
            mostrarAlerta('La enfermedad actual es requerida', 'error');
            $('#enfermedad_actual').focus();
            return;
        }
        
        if (!tratamiento) {
            mostrarAlerta('El tratamiento es requerido', 'error');
            $('#tratamiento').focus();
            return;
        }
        
        var $btn = marcarCompletada ? $('#btnGuardarCompletar') : $('#btnGuardar');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        var datos = {
            id_cita: $('#evolucion_id_cita').val(),
            id_paciente: $('#evolucion_id_paciente').val(),
            peso: $('#peso').val(),
            talla: $('#talla').val(),
            temperatura: $('#temperatura').val(),
            tension_sistolica: $('#tension_sistolica').val(),
            tension_diastolica: $('#tension_diastolica').val(),
            frecuencia_cardiaca: $('#frecuencia_cardiaca').val(),
            frecuencia_respiratoria: $('#frecuencia_respiratoria').val(),
            saturacion_oxigeno: $('#saturacion_oxigeno').val(),
            motivo_consulta: motivo,
            enfermedad_actual: enfermedad,
            examen_fisico: $('#examen_fisico').val(),
            diagnostico: $('#diagnostico').val(),
            tratamiento: tratamiento,
            recomendaciones: $('#recomendaciones').val(),
            notas_adicionales: $('#notas_adicionales').val(),
            marcar_completada: marcarCompletada ? 1 : 0,
            csrf_token: $('input[name="csrf_token"]').val()
        };
        
        $.ajax({
            url: APP_URL + '/api/evoluciones/guardar',
            type: 'POST',
            data: datos,
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                if (response.success) {
                    mostrarAlerta(response.message, 'success');
                    
                    // Recargar lista de citas para actualizar el badge
                    cargarCitas();
                    
                    if (marcarCompletada) {
                        // Limpiar selección
                        citaSeleccionada = null;
                        $('#info_cita_seleccionada').hide();
                        $('#form_evolucion').hide();
                        $('#sin_cita_seleccionada').show();
                        $('.cita-item').removeClass('active');
                    }
                } else {
                    mostrarAlerta(response.message, 'error');
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                mostrarAlerta('Error de conexión: ' + status, 'error');
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }
    
    // ==================== CANCELAR ====================
    
    $('#btnCancelar').click(function() {
        if (citaSeleccionada) {
            limpiarFormulario();
            cargarDatosFormulario(citaSeleccionada.evolucion);
            mostrarAlerta('Cambios descartados', 'info');
        }
    });
    
    // ==================== BUSCAR CITAS ====================
    
    var timeoutBusqueda;
    $('#buscar_cita').on('input', function() {
        var termino = $(this).val().trim();
        
        clearTimeout(timeoutBusqueda);
        
        if (termino.length >= 2) {
            timeoutBusqueda = setTimeout(function() {
                cargarCitas(termino);
            }, 500);
        } else if (termino.length === 0) {
            cargarCitas('');
        }
    });
    
    // ==================== FUNCIONES UTILITARIAS ====================
    
    function mostrarAlerta(mensaje, tipo) {
        var alertDiv = $('<div>', {
            class: 'alert alert-' + (tipo === 'success' ? 'success' : tipo === 'error' ? 'danger' : 'info') + ' alert-dismissible fade show position-fixed',
            style: 'top: 70px; right: 20px; z-index: 9999; min-width: 300px; border-radius: 12px;',
            role: 'alert'
        });
        
        var icon = tipo === 'success' ? 'fa-check-circle' : (tipo === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
        
        alertDiv.html(`
            <i class="fas ${icon}"></i>
            ${mensaje}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `);
        
        $('body').append(alertDiv);
        
        setTimeout(function() {
            alertDiv.fadeOut(300, function() { $(this).remove(); });
        }, 4000);
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
    cargarCitas();
});
