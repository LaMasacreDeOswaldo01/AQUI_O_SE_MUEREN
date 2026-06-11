// js/medico_editar_horarios.js
$(document).ready(function() {
    console.log('=== EDITAR HORARIOS - MÉDICO ===');
    
    var id_medico = $('#id_medico').val();
    var horariosData = {};
    var consultorios = [];
    var especialidades = [];
    var diaActualEditando = '';
    
    // Cargar horarios
    function cargarHorarios() {
        $('#horarios-container').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Cargando horarios...</p>
            </div>
        `);
        
        $.ajax({
            url: APP_URL + '/api/medicos/horarios',
            type: 'POST',
            data: { id_medico: id_medico },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    horariosData = response.data.horarios || {};
                    consultorios = response.data.consultorios || [];
                    especialidades = response.data.especialidades || [];
                    renderizarHorarios();
                    calcularResumen();
                } else {
                    $('#horarios-container').html('<div class="alert alert-danger">Error al cargar horarios</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#horarios-container').html('<div class="alert alert-danger">Error de conexión</div>');
            }
        });
    }
    
  function renderizarHorarios() {
    var dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    var html = '';
    
    for (var i = 0; i < dias.length; i++) {
        var dia = dias[i];
        var diaHorarios = horariosData[dia] || {};
        var manana = diaHorarios['Mañana'] || { activo: 0, hora_inicio: '08:00', hora_fin: '12:00', duracion_cita: 30 };
        var tarde = diaHorarios['Tarde'] || { activo: 0, hora_inicio: '14:00', hora_fin: '18:00', duracion_cita: 30 };
        
        // Determinar estado general del día
        var tieneHorario = (manana.activo || tarde.activo);
        var estadoBadge = tieneHorario ? 
            '<span class="badge-estado badge-activo"><i class="fas fa-check-circle"></i> Horario configurado</span>' : 
            '<span class="badge-estado badge-inactivo"><i class="fas fa-ban"></i> Sin horario</span>';
        
        html += `
            <div class="horario-card" data-dia="${dia}">
                <div class="card-header">
                    <div class="dia-nombre">
                        <i class="fas fa-calendar-day"></i> ${dia}
                        ${estadoBadge}
                    </div>
                    <button class="btn btn-editar-dia btn-sm" data-dia="${dia}">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="turno-section">
                                <h6><i class="fas fa-sun"></i> Turno Mañana</h6>
                                ${manana.activo ? 
                                    `<span class="badge-estado badge-activo"><i class="fas fa-check-circle"></i> Activo</span>
                                     <span class="badge-estado badge-horario"><i class="fas fa-clock"></i> ${manana.hora_inicio} - ${manana.hora_fin}</span>
                                     <span class="badge-estado badge-horario"><i class="fas fa-hourglass-half"></i> ${manana.duracion_cita || 30} min/cita</span>` :
                                    `<span class="badge-estado badge-inactivo"><i class="fas fa-ban"></i> Inactivo</span>`
                                }
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="turno-section">
                                <h6><i class="fas fa-moon"></i> Turno Tarde</h6>
                                ${tarde.activo ? 
                                    `<span class="badge-estado badge-activo"><i class="fas fa-check-circle"></i> Activo</span>
                                     <span class="badge-estado badge-horario"><i class="fas fa-clock"></i> ${tarde.hora_inicio} - ${tarde.hora_fin}</span>
                                     <span class="badge-estado badge-horario"><i class="fas fa-hourglass-half"></i> ${tarde.duracion_cita || 30} min/cita</span>` :
                                    `<span class="badge-estado badge-inactivo"><i class="fas fa-ban"></i> Inactivo</span>`
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    $('#horarios-container').html(html);
}
    
    function calcularResumen() {
        var diasActivos = 0;
        var horasSemanales = 0;
        var maxCuposPorDia = 0;
        
        for (var dia in horariosData) {
            var manana = horariosData[dia]['Mañana'] || {};
            var tarde = horariosData[dia]['Tarde'] || {};
            var diaActivo = false;
            var cuposDia = 0;
            
            if (manana.activo && manana.hora_inicio && manana.hora_fin) {
                diaActivo = true;
                var inicio = manana.hora_inicio.split(':');
                var fin = manana.hora_fin.split(':');
                var horas = (parseInt(fin[0]) - parseInt(inicio[0])) + (parseInt(fin[1]) - parseInt(inicio[1])) / 60;
                horasSemanales += horas;
                cuposDia += Math.floor(horas * 60 / (manana.duracion_cita || 30));
            }
            
            if (tarde.activo && tarde.hora_inicio && tarde.hora_fin) {
                diaActivo = true;
                var inicio = tarde.hora_inicio.split(':');
                var fin = tarde.hora_fin.split(':');
                var horas = (parseInt(fin[0]) - parseInt(inicio[0])) + (parseInt(fin[1]) - parseInt(inicio[1])) / 60;
                horasSemanales += horas;
                cuposDia += Math.floor(horas * 60 / (tarde.duracion_cita || 30));
            }
            
            if (diaActivo) {
                diasActivos++;
                if (cuposDia > maxCuposPorDia) maxCuposPorDia = cuposDia;
            }
        }
        
        $('#dias_activos').text(diasActivos);
        $('#horas_semanales').text(Math.round(horasSemanales));
        $('#cupos_maximos').text(maxCuposPorDia || '~16');
    }
    
    // Abrir modal para editar día
    $(document).on('click', '.btn-editar-dia', function() {
        var dia = $(this).data('dia');
        diaActualEditando = dia;
        var diaHorarios = horariosData[dia] || {};
        var manana = diaHorarios['Mañana'] || { activo: 0, hora_inicio: '08:00', hora_fin: '12:00', duracion_cita: 30 };
        var tarde = diaHorarios['Tarde'] || { activo: 0, hora_inicio: '14:00', hora_fin: '18:00', duracion_cita: 30 };
        
        $('#modal_dia').text(dia);
        $('#edit_dia').val(dia);
        
        // Mañana
        $('#manana_activo').prop('checked', manana.activo == 1);
        $('#manana_hora_inicio').val(manana.hora_inicio || '08:00');
        $('#manana_hora_fin').val(manana.hora_fin || '12:00');
        $('#manana_duracion').val(manana.duracion_cita || 30);
        
        // Tarde
        $('#tarde_activo').prop('checked', tarde.activo == 1);
        $('#tarde_hora_inicio').val(tarde.hora_inicio || '14:00');
        $('#tarde_hora_fin').val(tarde.hora_fin || '18:00');
        $('#tarde_duracion').val(tarde.duracion_cita || 30);
        
        $('#modalEditarHorario').modal('show');
    });
    
    // Guardar horario del día
    $('#btnGuardarHorarioDia').click(function() {
        var dia = $('#edit_dia').val();
        
        // Guardar horario mañana
        guardarTurno(dia, 'Mañana', $('#manana_activo').is(':checked'), 
                     $('#manana_hora_inicio').val(), $('#manana_hora_fin').val(), 
                     parseInt($('#manana_duracion').val()));
        
        // Guardar horario tarde
        guardarTurno(dia, 'Tarde', $('#tarde_activo').is(':checked'), 
                     $('#tarde_hora_inicio').val(), $('#tarde_hora_fin').val(), 
                     parseInt($('#tarde_duracion').val()));
    });
    
    function guardarTurno(dia, turno, activo, hora_inicio, hora_fin, duracion) {
        $.ajax({
            url: APP_URL + '/api/medicos/guardar-horario',
            type: 'POST',
            data: {
                dia: dia,
                turno: turno,
                activo: activo ? 1 : 0,
                hora_inicio: hora_inicio,
                hora_fin: hora_fin,
                duracion_cita: duracion,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Actualizar datos locales
                    if (!horariosData[dia]) horariosData[dia] = {};
                    horariosData[dia][turno] = {
                        activo: activo ? 1 : 0,
                        hora_inicio: hora_inicio,
                        hora_fin: hora_fin,
                        duracion_cita: duracion
                    };
                    
                    mostrarToast('Horario guardado correctamente', 'success');
                } else {
                    mostrarToast(response.message || 'Error al guardar', 'error');
                }
            },
            error: function(xhr, status, error) {
                mostrarToast('Error de conexión: ' + status, 'error');
            }
        });
    }
    
    // Guardar todos los horarios
    $('#btnGuardarTodos').click(function() {
        mostrarToast('Guardando todos los horarios...', 'info');
        // Recargar desde el servidor para asegurar
        cargarHorarios();
    });
    
    // Aplicar plantilla
    $('.plantilla-btn').click(function() {
        var plantilla = $(this).data('plantilla');
        
        if (!confirm('¿Está seguro de aplicar esta plantilla? Esto sobrescribirá los horarios actuales.')) {
            return;
        }
        
        $.ajax({
            url: APP_URL + '/api/medicos/aplicar-plantilla',
            type: 'POST',
            data: {
                plantilla: plantilla,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarToast('Plantilla aplicada correctamente', 'success');
                    cargarHorarios();
                } else {
                    mostrarToast(response.message || 'Error al aplicar plantilla', 'error');
                }
            },
            error: function(xhr, status, error) {
                mostrarToast('Error de conexión: ' + status, 'error');
            }
        });
    });
    
    // Volver a agenda
    $('#btnVolverAgenda').click(function() {
        window.location.href = APP_URL + '/medico/agenda';
    });
    
    function mostrarToast(mensaje, tipo) {
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
        setTimeout(function() { alertDiv.fadeOut(300, function() { $(this).remove(); }); }, 4000);
    }
    
    // Inicializar
    cargarHorarios();
});
