// Usar la configuración global
var BASE_URL = window.CONFIG ? window.CONFIG.BASE_URL : '';
function getUrl(endpoint) {
    if (window.CONFIG) {
        return window.CONFIG.getApiUrl(endpoint);
    }
    // Fallback para compatibilidad
    return BASE_URL + '/api/' + endpoint;
}

$(document).ready(function() {
    console.log('BASE_URL detectada:', BASE_URL);
    
    listar_recetas();

    // Botón nueva receta
    $('#btnNuevaReceta').click(function() {
        resetFormulario();
        $('#modalTitle').text('Nueva Receta');
        $('#modalReceta').modal('show');
    });

    // Guardar receta
    $('#btnGuardar').click(function() {
        guardarReceta();
    });

    // Buscar pacientes
    let timeoutId;
    $('#buscar_paciente').on('keyup', function() {
        let dato = $(this).val();
        clearTimeout(timeoutId);
        
        if (dato.length >= 2) {
            timeoutId = setTimeout(function() {
                buscarPacientes(dato);
            }, 500);
        } else {
            $('#resultados_pacientes').hide();
        }
    });

    // Ocultar resultados al hacer clic fuera
    $(document).click(function(e) {
        if (!$(e.target).closest('#buscar_paciente, #resultados_pacientes').length) {
            $('#resultados_pacientes').hide();
        }
    });

    // Buscar en la tabla
    $('#buscar_receta').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#tabla_recetas tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
function cargarEstadisticas() {
    $.ajax({
        url: APP_URL + '/api/recetas/estadisticas',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log('Estadísticas recibidas:', response);
            
            // Manejar formato ApiResponse
            var data = {};
            if (response.success && response.data) {
                data = response.data;
            } else {
                data = response;
            }
            
            $('#total_recetas').text(data.total_recetas || 0);
            $('#total_medicos').text(data.total_medicos || 0);
            $('#total_pacientes').text(data.total_pacientes || 0);
            $('#recetas_mes').text(data.recetas_mes || 0);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar estadísticas:', error);
            $('#total_recetas').text('0');
            $('#total_medicos').text('0');
            $('#total_pacientes').text('0');
            $('#recetas_mes').text('0');
        }
    });
}

function listar_recetas() {
    $('#tabla_recetas').html('<tr><td colspan="9" class="text-center"><div class="spinner-border text-primary"></div><p>Cargando recetas...</p></td></tr>');
    
    $.ajax({
        url: APP_URL + '/api/recetas/listar',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta recetas (raw):', response);
            
            // ==================== MANEJAR FORMATO ApiResponse ====================
            var recetas = [];
            
            // Si la respuesta tiene el formato ApiResponse (success + data)
            if (response.success && response.data) {
                recetas = response.data;
                console.log('Recetas extraídas de ApiResponse.data:', recetas);
            } 
            // Si es un array directo
            else if (Array.isArray(response)) {
                recetas = response;
                console.log('Recetas es un array directo:', recetas);
            }
            // Si tiene propiedad recetas
            else if (response.recetas && Array.isArray(response.recetas)) {
                recetas = response.recetas;
                console.log('Recetas extraídas de response.recetas:', recetas);
            }
            // Otro formato
            else {
                console.warn('Formato de respuesta no reconocido:', response);
                recetas = [];
            }
            
            // Asegurar que sea un array
            if (!Array.isArray(recetas)) {
                console.error('recetas no es un array:', recetas);
                recetas = [];
            }
            
            console.log('Recetas procesadas (cantidad):', recetas.length);
            
            let html = '';
            
            if (recetas.length === 0) {
                html = '<tr><td colspan="9" class="text-center text-muted">No hay recetas registradas</td></tr>';
            } else {
                for (let i = 0; i < recetas.length; i++) {
                    let receta = recetas[i];
                    html += `
                        <tr>
                            <td><span class="badge badge-secondary">${receta.id_receta || ''}</span></td>
                            <td><strong>${escapeHtml(receta.nombre_medicamento || '')}</strong></td>
                            <td>${escapeHtml(receta.marca || '')}</td>
                            <td>${escapeHtml(receta.cantidad || '')}</td>
                            <td>${escapeHtml(receta.dosis || '-')}</td>
                            <td><i class="fas fa-user-injured text-info"></i> ${escapeHtml(receta.paciente || 'N/A')}</td>
                            <td><i class="fas fa-user-md text-success"></i> ${escapeHtml(receta.medico || 'N/A')}</td>
                            <td><i class="fas fa-calendar-alt"></i> ${receta.fecha_receta || ''}</td>
                            <td class="table-actions">
                                <button class="btn btn-info btn-sm btn-ver-detalle" data-id="${receta.id_receta}">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                             </td>
                         </tr>
                    `;
                }
            }
            
            $('#tabla_recetas').html(html);
            console.log('Tabla actualizada con', recetas.length, 'recetas');
        },
        error: function(xhr, status, error) {
            console.error('Error al listar recetas:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
            $('#tabla_recetas').html('<tr><td colspan="9" class="text-center text-danger">Error al cargar las recetas: ' + error + '</td></tr>');
        }
    });
}

// USAR EVENTOS DELEGADOS - Esta es la solución para evitar duplicación
$(document).on('click', '.btn-editar', function() {
    let id = $(this).data('id');
    console.log('Click editar - ID:', id);
    editarReceta(id);
});

$(document).on('click', '.btn-borrar', function() {
    let id = $(this).data('id');
    console.log('Click borrar - ID:', id);
    borrarReceta(id);
});

// Eventos para los botones de los modales
$(document).on('click', '#btnEstudioLaboratorio', function() {
    resetModalEstudio();
    $('#modalEstudioLaboratorio').modal('show');
});

$(document).on('click', '#btnDiagnostico', function() {
    resetModalDiagnostico();
    $('#modalDiagnostico').modal('show');
});

$(document).on('click', '#btnBuscarPacienteLab', function() {
    let cedula = $('#buscar_paciente_lab').val().trim();
    if (cedula === '') {
        mostrarAlerta('Ingrese una cédula para buscar', 'error');
        return;
    }
    buscarPacientePorCedula(cedula, 'lab');
});

$(document).on('click', '#btnBuscarPacienteDiag', function() {
    let cedula = $('#buscar_paciente_diag').val().trim();
    if (cedula === '') {
        mostrarAlerta('Ingrese una cédula para buscar', 'error');
        return;
    }
    buscarPacientePorCedula(cedula, 'diag');
});

function guardarReceta() {
    let id_receta = $('#id_receta').val();
    let nombre_medicamento = $('#nombre_medicamento').val().trim();
    let marca = $('#marca').val().trim();
    let cantidad = $('#cantidad').val().trim();
    let dosis = $('#dosis').val().trim();
    let instrucciones = $('#instrucciones').val().trim();
    let id_paciente = $('#id_paciente').val();
    let fecha_receta = $('#fecha_receta').val();
    
    // Validaciones
    if (!nombre_medicamento) {
        mostrarAlerta('Debe ingresar el nombre del medicamento', 'error');
        $('#nombre_medicamento').focus();
        return;
    }
    if (!marca) {
        mostrarAlerta('Debe ingresar la marca del medicamento', 'error');
        $('#marca').focus();
        return;
    }
    if (!cantidad) {
        mostrarAlerta('Debe ingresar la cantidad del medicamento', 'error');
        $('#cantidad').focus();
        return;
    }
    if (!id_paciente || id_paciente === '') {
        mostrarAlerta('Debe seleccionar un paciente', 'error');
        $('#buscar_paciente').focus();
        return;
    }
    if (!fecha_receta) {
        mostrarAlerta('Debe seleccionar la fecha de la receta', 'error');
        $('#fecha_receta').focus();
        return;
    }
    
    let funcion = id_receta ? 'editar_receta' : 'crear_receta';
    let datos = {
        funcion: funcion,
        nombre_medicamento: nombre_medicamento,
        marca: marca,
        cantidad: cantidad,
        dosis: dosis,
        instrucciones: instrucciones,
        id_paciente: id_paciente,
        fecha_receta: fecha_receta
    };
    
    if (id_receta) {
        datos.id_receta = id_receta;
    }
    
    console.log('Enviando datos:', datos);
    
    $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    
    $.ajax({
        url: getUrl('RecetaController', 'guardar_receta'),
        type: 'POST',
        data: datos,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            
            if (response.success) {
                mostrarAlerta(response.message, 'success');
                $('#modalReceta').modal('hide');
                listar_recetas();
                resetFormulario();
            } else {
                mostrarAlerta(response.message || 'Error al guardar la receta', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            mostrarAlerta('Error de conexión al guardar la receta', 'error');
        },
        complete: function() {
            $('#btnGuardar').prop('disabled', false).html('Guardar Receta');
        }
    });
}

function editarReceta(id) {
    console.log('EDITAR - ID:', id);
    
    if (!id) {
        mostrarAlerta('ID de receta no válido', 'error');
        return;
    }
    
    $.ajax({
        url: getUrl('RecetaController', 'obtener_receta'),
        type: 'POST',
        data: { funcion: 'obtener_receta', id_receta: id },
        dataType: 'json',
        success: function(receta) {
            console.log('EDITAR - Datos:', receta);
            
            if (receta && receta.id_receta) {
                $('#id_receta').val(receta.id_receta);
                $('#nombre_medicamento').val(receta.nombre_medicamento);
                $('#marca').val(receta.marca);
                $('#cantidad').val(receta.cantidad);
                $('#dosis').val(receta.dosis || '');
                $('#instrucciones').val(receta.instrucciones || '');
                $('#fecha_receta').val(receta.fecha_receta);
                
                cargarDatosPaciente(receta.id_paciente);
                
                $('#modalTitle').text('Editar Receta');
                $('#modalReceta').modal('show');
            } else {
                mostrarAlerta('Error al cargar los datos de la receta', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('EDITAR - Error:', error);
            mostrarAlerta('Error al cargar los datos de la receta', 'error');
        }
    });
}

function borrarReceta(id) {
    console.log('BORRAR - ID:', id);
    
    if (!id) {
        mostrarAlerta('ID de receta no válido', 'error');
        return;
    }
    
    if (confirm('¿Está seguro de que desea eliminar esta receta?')) {
        $.ajax({
            url: getUrl('RecetaController', 'borrar_receta'),
            type: 'POST',
            data: { 
                funcion: 'borrar_receta', 
                id_receta: id 
            },
            dataType: 'json',
            success: function(response) {
                console.log('BORRAR - Respuesta:', response);
                
                if (response.success) {
                    mostrarAlerta(response.message, 'success');
                    listar_recetas();
                } else {
                    mostrarAlerta(response.message || 'Error al borrar la receta', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('BORRAR - Error:', error);
                mostrarAlerta('Error de conexión al borrar la receta', 'error');
            }
        });
    }
}

function buscarPacientes(dato) {
    console.log('BUSCAR - Dato:', dato);
    
    $.ajax({
        url: getUrl('RecetaController', 'buscar_pacientes'),
        type: 'POST',
        data: { funcion: 'buscar_pacientes', dato: dato },
        dataType: 'json',
        success: function(pacientes) {
            console.log('BUSCAR - Resultados:', pacientes);
            
            let html = '';
            
            if (!pacientes || pacientes.length === 0) {
                html = '<a href="#" class="list-group-item list-group-item-action disabled">No se encontraron pacientes</a>';
            } else {
                for (let paciente of pacientes) {
                    html += `<a href="#" class="list-group-item list-group-item-action paciente-item" 
                                data-id="${paciente.id_usuario}" 
                                data-nombre="${escapeHtml(paciente.nombre_completo)}" 
                                data-cedula="${escapeHtml(paciente.cedula)}">
                                <strong>${escapeHtml(paciente.nombre_completo)}</strong><br>
                                <small>Cédula: ${escapeHtml(paciente.cedula)}</small>
                            </a>`;
                }
            }
            
            $('#resultados_pacientes').html(html).show();
            
            $('.paciente-item').off('click').on('click', function(e) {
                e.preventDefault();
                let nombreCompleto = $(this).data('nombre');
                let id = $(this).data('id');
                let cedula = $(this).data('cedula');
                
                console.log('PACIENTE SELECCIONADO:', {id, nombreCompleto, cedula});
                
                $('#buscar_paciente').val(nombreCompleto);
                $('#id_paciente').val(id);
                $('#resultados_pacientes').hide();
            });
        },
        error: function(xhr, status, error) {
            console.error('BUSCAR - Error:', error);
            $('#resultados_pacientes').html('<a href="#" class="list-group-item list-group-item-action disabled">Error al buscar pacientes</a>').show();
        }
    });
}

function cargarDatosPaciente(id_paciente) {
    console.log('CARGAR PACIENTE - ID:', id_paciente);
    
    if (!id_paciente) return;
    
    $.ajax({
        url: getUrl('RecetaController', 'buscar_pacientes'),
        type: 'POST',
        data: { funcion: 'buscar_pacientes', dato: '' },
        dataType: 'json',
        success: function(pacientes) {
            console.log('CARGAR PACIENTE - Todos:', pacientes);
            
            if (pacientes && Array.isArray(pacientes)) {
                let paciente = pacientes.find(p => p.id_usuario == id_paciente);
                if (paciente) {
                    console.log('CARGAR PACIENTE - Encontrado:', paciente);
                    $('#buscar_paciente').val(paciente.nombre_completo);
                    $('#id_paciente').val(paciente.id_usuario);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('CARGAR PACIENTE - Error:', error);
        }
    });
}

function resetFormulario() {
    $('#id_receta').val('');
    $('#nombre_medicamento').val('');
    $('#marca').val('');
    $('#cantidad').val('');
    $('#dosis').val('');
    $('#instrucciones').val('');
    $('#buscar_paciente').val('');
    $('#id_paciente').val('');
    let hoy = new Date();
    let fecha = hoy.toISOString().split('T')[0];
    $('#fecha_receta').val(fecha);
}

function mostrarAlerta(mensaje, tipo) {
    console.log('ALERTA:', tipo, '-', mensaje);
    
    if (tipo === 'success') {
        alert('✓ Éxito: ' + mensaje);
    } else {
        alert('✗ Error: ' + mensaje);
    }
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

// ==================== FUNCIONES PARA ESTUDIO LABORATORIO ====================
function buscarPacientePorCedula(cedula, tipo) {
    $.ajax({
        url: getUrl('RecetaController', 'buscar_pacientes'),
        type: 'POST',
        data: { 
            funcion: 'buscar_pacientes', 
            dato: cedula 
        },
        dataType: 'json',
        success: function(pacientes) {
            console.log('Pacientes encontrados:', pacientes);
            
            if (pacientes && pacientes.length > 0) {
                let paciente = pacientes[0];
                
                if (tipo === 'lab') {
                    $('#id_paciente_lab').val(paciente.id_usuario);
                    $('#lab_nombre').text(paciente.nombre_completo);
                    $('#lab_edad').text(calcularEdad(paciente.fecha_nacimiento || '1990-01-01'));
                    $('#lab_sexo').text(paciente.sexo || 'No especificado');
                    $('#lab_medico').text($('.user-panel .info a').text().trim() || 'Médico tratante');
                    $('#info_paciente_lab').show();
                } else if (tipo === 'diag') {
                    $('#id_paciente_diag').val(paciente.id_usuario);
                    $('#diag_nombre').text(paciente.nombre_completo);
                    $('#diag_edad').text(calcularEdad(paciente.fecha_nacimiento || '1990-01-01'));
                    $('#diag_sexo').text(paciente.sexo || 'No especificado');
                    $('#diag_medico').text($('.user-panel .info a').text().trim() || 'Médico tratante');
                    $('#info_paciente_diag').show();
                }
                
                mostrarAlerta('Paciente encontrado: ' + paciente.nombre_completo, 'success');
            } else {
                mostrarAlerta('No se encontró ningún paciente con la cédula: ' + cedula, 'error');
                if (tipo === 'lab') {
                    $('#info_paciente_lab').hide();
                    $('#id_paciente_lab').val('');
                } else {
                    $('#info_paciente_diag').hide();
                    $('#id_paciente_diag').val('');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al buscar paciente:', error);
            mostrarAlerta('Error al buscar el paciente', 'error');
        }
    });
}

function calcularEdad(fechaNacimiento) {
    if (!fechaNacimiento) return 'N/A';
    const hoy = new Date();
    const nacimiento = new Date(fechaNacimiento);
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const mes = hoy.getMonth() - nacimiento.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
        edad--;
    }
    return edad + ' años';
}

function resetModalEstudio() {
    $('#buscar_paciente_lab').val('');
    $('#id_paciente_lab').val('');
    $('#lab_nombre').text('');
    $('#lab_edad').text('');
    $('#lab_sexo').text('');
    $('#lab_medico').text('');
    $('#info_paciente_lab').hide();
    $('#estudio_hemograma').prop('checked', false);
    $('#estudio_coprocultivo').prop('checked', false);
    $('#estudio_placa').prop('checked', false);
    $('#estudio_orina').prop('checked', false);
    $('#estudio_tomografia').prop('checked', false);
    $('#lab_observaciones').val('');
}

function resetModalDiagnostico() {
    $('#buscar_paciente_diag').val('');
    $('#id_paciente_diag').val('');
    $('#diag_nombre').text('');
    $('#diag_edad').text('');
    $('#diag_sexo').text('');
    $('#diag_medico').text('');
    $('#info_paciente_diag').hide();
    $('#diagnostico_texto').val('');
    $('#tratamiento_texto').val('');
}

// ==================== EVENTOS PARA GENERAR RECETAS ====================
$(document).on('click', '#btnGenerarRecetaLab', function() {
    let id_paciente = $('#id_paciente_lab').val();
    let nombre_paciente = $('#lab_nombre').text();
    
    if (!id_paciente || id_paciente === '') {
        mostrarAlerta('Debe buscar y seleccionar un paciente primero', 'error');
        return;
    }
    
    let estudios = [];
    if ($('#estudio_hemograma').is(':checked')) estudios.push('Hemograma');
    if ($('#estudio_coprocultivo').is(':checked')) estudios.push('Coprocultivo');
    if ($('#estudio_placa').is(':checked')) estudios.push('Placa de tórax');
    if ($('#estudio_orina').is(':checked')) estudios.push('Examen de orina');
    if ($('#estudio_tomografia').is(':checked')) estudios.push('Tomografía computarizada');
    
    if (estudios.length === 0) {
        mostrarAlerta('Debe seleccionar al menos un estudio', 'error');
        return;
    }
    
    let observaciones = $('#lab_observaciones').val();
    
    let estudioTexto = 'ESTUDIOS SOLICITADOS:\n';
    estudios.forEach(e => { estudioTexto += '• ' + e + '\n'; });
    if (observaciones) {
        estudioTexto += '\nOBSERVACIONES:\n' + observaciones;
    }
    
    $('#nombre_medicamento').val('ESTUDIOS DE LABORATORIO');
    $('#marca').val('Solicitud de estudios');
    $('#cantidad').val(estudios.length + ' estudio(s) solicitado(s)');
    $('#dosis').val('Realizar según indicaciones');
    $('#instrucciones').val(estudioTexto);
    $('#buscar_paciente').val(nombre_paciente);
    $('#id_paciente').val(id_paciente);
    let hoy = new Date();
    let fecha = hoy.toISOString().split('T')[0];
    $('#fecha_receta').val(fecha);
    $('#id_receta').val('');
    
    $('#modalEstudioLaboratorio').modal('hide');
    $('#modalTitle').text('Nueva Receta - Estudio Laboratorio');
    $('#modalReceta').modal('show');
});

$(document).on('click', '#btnGenerarRecetaDiag', function() {
    let id_paciente = $('#id_paciente_diag').val();
    let nombre_paciente = $('#diag_nombre').text();
    let diagnostico = $('#diagnostico_texto').val().trim();
    let tratamiento = $('#tratamiento_texto').val().trim();
    
    if (!id_paciente || id_paciente === '') {
        mostrarAlerta('Debe buscar y seleccionar un paciente primero', 'error');
        return;
    }
    
    if (!diagnostico) {
        mostrarAlerta('Debe ingresar un diagnóstico', 'error');
        return;
    }
    
    let diagnosticoTexto = 'DIAGNÓSTICO:\n' + diagnostico + '\n';
    if (tratamiento) {
        diagnosticoTexto += '\nTRATAMIENTO SUGERIDO:\n' + tratamiento;
    }
    
    $('#nombre_medicamento').val('DIAGNÓSTICO MÉDICO');
    $('#marca').val('Registro médico');
    $('#cantidad').val('1 diagnóstico');
    $('#dosis').val('Ver instrucciones');
    $('#instrucciones').val(diagnosticoTexto);
    $('#buscar_paciente').val(nombre_paciente);
    $('#id_paciente').val(id_paciente);
    let hoy = new Date();
    let fecha = hoy.toISOString().split('T')[0];
    $('#fecha_receta').val(fecha);
    $('#id_receta').val('');
    
    $('#modalDiagnostico').modal('hide');
    $('#modalTitle').text('Nueva Receta - Diagnóstico');
    $('#modalReceta').modal('show');
});
