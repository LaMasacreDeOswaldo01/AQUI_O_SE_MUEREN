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
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
        
        cargarAgendaMedico(() => {
            $btn.prop('disabled', false).html('<i class="fas fa-sync"></i> Actualizar');
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