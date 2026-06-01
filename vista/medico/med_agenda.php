<?php
// vista/medico/med_agenda.php
$id_medico = $_SESSION['usuario'] ?? 0;
?>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<style>
    .agenda-container { background: white; border-radius: 16px; padding: 20px; border: 1px solid #eef2f6; }
    .fc .fc-toolbar-title { font-size: 1.2rem; font-weight: 700; color: var(--bv-dark); }
    .fc-button-primary { background-color: #0d9488 !important; border-color: #0d9488 !important; }
</style>

<div class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-calendar-alt"></i> Gestión de Agenda</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-primary card-outline">
                    <div class="card-header"><h3 class="card-title">Configurar Disponibilidad</h3></div>
                    <form id="formAgenda">
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Sede</label>
                                <select class="form-control" id="id_sede" required></select>
                            </div>
                            <div class="mb-3">
                                <label>Servicio</label>
                                <select class="form-control" id="id_servicio" required></select>
                            </div>
                            <div class="mb-3">
                                <label>Días de atención</label>
                                <div id="dias_semana" class="d-grid gap-2">
                                    <?php foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="dias[]" value="<?= $dia ?>" id="chk_<?= $dia ?>">
                                            <label class="form-check-label" for="chk_<?= $dia ?>"><?= $dia ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6"><label>Inicio</label><input type="time" class="form-control" id="hora_inicio" required></div>
                                <div class="col-6"><label>Fin</label><input type="time" class="form-control" id="hora_fin" required></div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary w-100">Guardar Horario</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="agenda-container">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    // 1. Inicializar Calendario
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'timeGridWeek,dayGridMonth' },
        locale: 'es',
        editable: true,
        selectable: true
    });
    calendar.render();

    // 2. Cargar datos de Sedes y Servicios
    $.ajax({
        url: APP_URL + '/api/data/obtener-catalogos',
        success: function(res) {
            res.sedes.forEach(s => $('#id_sede').append(`<option value="${s.id_sede}">${s.nombre}</option>`));
            res.servicios.forEach(s => $('#id_servicio').append(`<option value="${s.id_servicio}">${s.nombre}</option>`));
        }
    });

    // 3. Guardar en base de datos
    $('#formAgenda').submit(function(e) {
        e.preventDefault();
        let formData = {
            id_sede: $('#id_sede').val(),
            id_servicio: $('#id_servicio').val(),
            dias: $('input[name="dias[]"]:checked').map(function(){ return $(this).val(); }).get(),
            hora_inicio: $('#hora_inicio').val(),
            hora_fin: $('#hora_fin').val()
        };

        $.post(APP_URL + '/api/medicos/guardar-agenda', formData, function(res) {
            if(res.success) {
                alert('Agenda actualizada correctamente');
                calendar.refetchEvents(); // Recarga visual del calendario
            }
        });
    });
});
</script>
