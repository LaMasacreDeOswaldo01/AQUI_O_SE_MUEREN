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
