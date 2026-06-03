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