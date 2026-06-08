
// js/medico_agenda.js
$(document).ready(function() {
    console.log('=== MI AGENDA - MÉDICO ===');
    
    var id_medico = $('#id_medico').val();
    var calendar;
    var horariosData = {};
    
    // Inicializar calendario
    function initCalendar() {
        var calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,timeGridDay'
            },
            slotMinTime: '07:00:00',
            slotMaxTime: '21:00:00',
            slotDuration: '00:30:00',
            allDaySlot: false,
            nowIndicator: true,
            editable: false,
            selectable: false,
            events: function(fetchInfo, successCallback, failureCallback) {
                cargarCitasCalendario(fetchInfo.startStr, fetchInfo.endStr, successCallback, failureCallback);
            },
            eventClick: function(info) {
                mostrarDetalleCita(info.event);
            },
            height: 'auto'
        });
        
        calendar.render();
        
        // Cambiar vista al hacer clic en día
        $('.dia-btn').click(function() {
            var dia = $(this).data('dia');
            var fecha = obtenerFechaPorDia(dia);
            if (fecha) {
                calendar.gotoDate(fecha);
                calendar.changeView('timeGridDay');
            }
            $('.dia-btn').removeClass('active');
            $(this).addClass('active');
        });
    }
    
    function obtenerFechaPorDia(dia) {
        var diasMap = {
            'Lunes': 1, 'Martes': 2, 'Miércoles': 3, 
            'Jueves': 4, 'Viernes': 5, 'Sábado': 6, 'Domingo': 0
        };
        var hoy = new Date();
        var diaActual = hoy.getDay();
        var diferencia = diasMap[dia] - diaActual;
        var fecha = new Date(hoy);
        fecha.setDate(hoy.getDate() + diferencia);
        return fecha;
    }
    
    function cargarCitasCalendario(start, end, successCallback, failureCallback) {
        $.ajax({
            url: APP_URL + '/api/medicos/citas-calendario',
            type: 'POST',
            data: { start: start, end: end },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    successCallback(response.data);
                } else {
                    failureCallback(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar citas:', error);
                failureCallback(error);
            }
        });
    }
    
    function mostrarDetalleCita(event) {
        var props = event.extendedProps;
        var fecha = new Date(event.start);
        var fechaStr = fecha.toLocaleDateString('es-ES', { 
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
        });
        var horaStr = fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        
        var estadoClass = '';
        var estadoTexto = '';
        switch(props.estado) {
            case 'pendiente': estadoClass = 'text-warning'; estadoTexto = 'Pendiente'; break;
            case 'confirmada': estadoClass = 'text-primary'; estadoTexto = 'Confirmada'; break;
            case 'en_progreso': estadoClass = 'text-info'; estadoTexto = 'En Progreso'; break;
            case 'completada': estadoClass = 'text-success'; estadoTexto = 'Completada'; break;
            default: estadoClass = 'text-secondary'; estadoTexto = props.estado;
        }
        
        var html = `
            <div class="p-3">
                <p><strong><i class="fas fa-user-injured"></i> Paciente:</strong> ${escapeHtml(event.title)}</p>
                <p><strong><i class="fas fa-id-card"></i> Cédula:</strong> ${props.cedula || 'N/A'}</p>
                <p><strong><i class="fas fa-calendar-day"></i> Fecha:</strong> ${fechaStr}</p>
                <p><strong><i class="fas fa-clock"></i> Hora:</strong> ${horaStr}</p>
                <p><strong><i class="fas fa-stethoscope"></i> Especialidad:</strong> ${props.especialidad || 'N/A'}</p>
                <p><strong><i class="fas fa-building"></i> Consultorio:</strong> ${props.consultorio || 'N/A'}</p>
                <p><strong><i class="fas fa-tag"></i> Tipo:</strong> ${props.tipo_consulta === 'primera_vez' ? 'Primera Vez' : 'Control'}</p>
                <p><strong><i class="fas fa-notes-medical"></i> Motivo:</strong> ${props.motivo || 'No especificado'}</p>
                <p><strong><i class="fas fa-circle ${estadoClass}"></i> Estado:</strong> ${estadoTexto}</p>
            </div>
        `;
        
        $('#modal_cita_content').html(html);
        $('#verDetalleCompleto').attr('href', APP_URL + '/medico/citas?cita_id=' + event.id);
        $('#modalCitaRapida').modal('show');
    }
    
    function cargarInfoMedico() {
        $.ajax({
            url: APP_URL + '/api/medicos/horarios',
            type: 'POST',
            data: { id_medico: id_medico },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    horariosData = response.data.horarios || {};
                    
                    // Mostrar especialidades
                    var especialidades = response.data.especialidades || [];
                    var espHtml = '';
                    for (var i = 0; i < especialidades.length; i++) {
                        espHtml += `<span class="consultorio-badge">${escapeHtml(especialidades[i].nombre)}</span>`;
                    }
                    $('#info_especialidades').html('<strong>Especialidades:</strong><br>' + (espHtml || 'Ninguna'));
                    
                    // Mostrar consultorios
                    var consultorios = response.data.consultorios || [];
                    var consHtml = '';
                    for (var i = 0; i < consultorios.length; i++) {
                        consHtml += `<span class="consultorio-badge">${escapeHtml(consultorios[i].nombre)}</span>`;
                    }
                    $('#info_consultorios').html('<strong>Consultorios:</strong><br>' + (consHtml || 'Ninguno'));
                    
                    // Calcular resumen
                    calcularResumen(horariosData);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar info del médico:', error);
            }
        });
    }
    
    function calcularResumen(horarios) {
        var diasActivos = 0;
        var horasSemanales = 0;
        var cuposMaximos = 0;
        var maxCuposPorDia = 0;
        
        for (var dia in horarios) {
            var turnoManana = horarios[dia]['Mañana'] || {};
            var turnoTarde = horarios[dia]['Tarde'] || {};
            var diaActivo = false;
            var cuposDia = 0;
            
            if (turnoManana.activo && turnoManana.hora_inicio && turnoManana.hora_fin) {
                diaActivo = true;
                var inicio = turnoManana.hora_inicio.split(':');
                var fin = turnoManana.hora_fin.split(':');
                var horas = (parseInt(fin[0]) - parseInt(inicio[0])) + (parseInt(fin[1]) - parseInt(inicio[1])) / 60;
                horasSemanales += horas;
                cuposDia += Math.floor(horas * 60 / (turnoManana.duracion_cita || 30));
            }
            
            if (turnoTarde.activo && turnoTarde.hora_inicio && turnoTarde.hora_fin) {
                diaActivo = true;
                var inicio = turnoTarde.hora_inicio.split(':');
                var fin = turnoTarde.hora_fin.split(':');
                var horas = (parseInt(fin[0]) - parseInt(inicio[0])) + (parseInt(fin[1]) - parseInt(inicio[1])) / 60;
                horasSemanales += horas;
                cuposDia += Math.floor(horas * 60 / (turnoTarde.duracion_cita || 30));
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
    
    // Botón editar horarios
    $('#btnEditarHorarios').click(function() {
        window.location.href = APP_URL + '/medico/editar-horarios';
    });
    
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    
    // Inicializar
    initCalendar();
    cargarInfoMedico();
});

/**
 * Lógica para la Agenda Médica (med_agenda.php)
 */
$(document).ready(function() {
    // Forzar redibujado del calendario al cambiar a la pestaña de configuración
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.id === 'config-tab') {
            if (typeof calendar !== 'undefined' && calendar !== null) {
                calendar.render(); // Fuerza a FullCalendar a redibujarse con el tamaño correcto
            }
        }
    });

    // Ejemplo de configuración para bloquear días en el calendario
    const configDiaBloqueado = {
        title: 'DÍA BLOQUEADO / NO TRABAJA',
        start: '2026-06-15', 
        allDay: true,
        backgroundColor: '#dc3545', // Rojo Bootstrap (Danger)
        borderColor: '#dc3545',
        display: 'background' // Opcional: si solo quieres pintar el fondo de la celda
    };

    // Cargar la agenda automáticamente cuando se abre el modal
    $('#modal_agenda').on('show.bs.modal', function() {
        cargarAgendaMedico();
    });

    // Evento para el botón de actualizar manualmente
    $('#btn_actualizar_agenda').click(function() {
        let $btn = $(this);
        
        // 1. Deshabilitar el botón y meter el spinner de carga animado
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
        
        // 2. NUEVO: Limpiar el campo de búsqueda de la agenda por si tiene filtros viejos
        $('#filtro_agenda').val('');
        
        // 3. Disparar tu función nativa pasándole el callback de restauración
        cargarAgendaMedico(function() {
            // Este bloque se ejecuta ÚNICAMENTE cuando el $.ajax termina
            $btn.prop('disabled', false).html('<i class="fas fa-sync"></i> Actualizar');
        });
    });

    // Evento para guardar el horario base
$('#btn_guardar_horario_base').click(function() {
    let $btn = $(this);
    const id_medico = $('#id_usuario').val(); // Tu ID de médico actual
    const hora_inicio = $('#hora_inicio').val();
    const hora_fin = $('#hora_fin').val();
    
    // Mapeamos qué días seleccionó el médico (Lun=1, Mar=2, etc.)
    let dias_seleccionados = [];
    if ($('#chk_lun').is(':checked')) dias_seleccionados.push(1);
    if ($('#chk_mar').is(':checked')) dias_seleccionados.push(2);
    if ($('#chk_mie').is(':checked')) dias_seleccionados.push(3);
    if ($('#chk_jue').is(':checked')) dias_seleccionados.push(4);
    if ($('#chk_vie').is(':checked')) dias_seleccionados.push(5);

    if (dias_seleccionados.length === 0) {
        Swal.fire('¡Atención!', 'Debes seleccionar al menos un día laborable.', 'warning');
        return;
    }

    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    $.ajax({
        url: APP_URL + '/api/medicos/guardar-horario',
        type: 'POST',
        data: {
            id_medico: id_medico,
            hora_inicio: hora_inicio,
            hora_fin: hora_fin,
            dias: dias_seleccionados // Se envía como un array [1, 2, 3, 4, 5]
        },
        dataType: 'json',
        success: function(response) {
            $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Guardar Horario Base');
            
            if (response.success) {
                Swal.fire('¡Guardado!', 'Tu horario base se ha actualizado con éxito.', 'success');
                // Opcional: recargar FullCalendar aquí para que refresque visualmente las horas válidas
                if (typeof calendar !== 'undefined' && calendar !== null) {
                    calendar.refetchEvents();
                }
            } else {
                Swal.fire('Error', response.message || 'No se pudo guardar el horario.', 'error');
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Guardar Horario Base');
            console.error(xhr.responseText);
            Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
        }
    });
});

    // Filtrado en tiempo real en la tabla
    $('#filtro_agenda').on('keyup', function() {
        let valor = $(this).val().toLowerCase();
        $('#lista_agenda_medico tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(valor) > -1);
        });
    });

    /**
     * Función principal para obtener las citas desde la API
     */
    function cargarAgendaMedico(callback = null) {
        const id_medico = $('#id_usuario').val(); // Tomado del campo oculto en el perfil o dashboard
        
        $.ajax({
            url: APP_URL + '/api/medicos/proximas-citas',
            type: 'POST',
            data: { id_medico: id_medico },
            dataType: 'json',
            success: function(response) {
                // Manejar tanto formato directo como el estándar ApiResponse
                let citas = response.success ? response.data : response;
                let html = '';
                let hoy = new Date().toISOString().split('T')[0];
                let contadorHoy = 0;

                if (!Array.isArray(citas) || citas.length === 0) {
                    html = '<tr><td colspan="5" class="text-center text-muted">No tienes citas pendientes programadas.</td></tr>';
                } else {
                    citas.forEach(cita => {
                        // Contar citas del día de hoy
                        if (cita.fecha_cita === hoy) contadorHoy++;

                        html += `
                            <tr>
                                <td class="text-center"><strong>${cita.hora_cita.substring(0, 5)}</strong></td>
                                <td>${escapeHtml(cita.paciente)}</td>
                                <td>
                                    <span class="badge badge-info">${cita.tipo_consulta === 'primera_vez' ? '1ra Vez' : 'Control'}</span><br>
                                    <small class="text-muted">${escapeHtml(cita.especialidad_nombre)}</small>
                                </td>
                                <td>${escapeHtml(cita.motivo || 'Consulta general')}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-success btn-atender" data-id="${cita.id_cita}" title="Iniciar Consulta">
                                        <i class="fas fa-stethoscope"></i>
                                    </button>
                                </td>
                            </tr>`;
                    });
                }

                $('#lista_agenda_medico').html(html);
                $('#citas_hoy_count').text(contadorHoy);
                if (callback) callback();
            },
            error: function(xhr) {
                console.error("Error al cargar agenda:", xhr.responseText);
                $('#lista_agenda_medico').html('<tr><td colspan="5" class="text-center text-danger">Error de conexión al cargar la agenda.</td></tr>');
                if (callback) callback();
            }
        });
    }

    /**
     * Función auxiliar para escapar HTML y prevenir XSS
     */
    function escapeHtml(str) {
        if (!str) return '';
        return str.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
});
