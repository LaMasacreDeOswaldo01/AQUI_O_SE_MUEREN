<?php
// med_alerta.php
// Sistema de Gestión de Alertas BioVital
$securityPath = dirname(__DIR__) . '/modelo/Security.php';
if (file_exists($securityPath)) {
    include_once $securityPath;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Alertas - BioVital</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>var APP_URL = '<?php echo defined('APP_URL') ? APP_URL : ''; ?>';</script>
</head>
<body>

<div class="container mt-5">
    <div class="header mb-4">
        <h2><i class="fas fa-exclamation-triangle"></i> Gestión de Alertas Médicas</h2>
        <p>Panel colaborativo de monitoreo epidemiológico</p>
    </div>

    <div class="card p-4 mb-4 shadow-sm">
        <form id="form-alerta">
            <?php echo Security::campoCSRF(); ?>
            <div class="row">
                <div class="col-md-3 form-group"><label>Tipo de Amenaza</label><input type="text" id="tipo_amenaza" class="form-control" required></div>
                <div class="col-md-3 form-group"><label>Paciente</label><input type="text" id="nombre_paciente" class="form-control" required></div>
                <div class="col-md-3 form-group"><label>Cédula</label><input type="text" id="cedula_paciente" class="form-control" required></div>
                <div class="col-md-3 form-group">
                    <label>Nivel de Riesgo</label>
                    <select id="nivel_riesgo" class="form-control" required>
                        <option value="Bajo">Bajo</option>
                        <option value="Moderado">Moderado</option>
                        <option value="Alto">Alto</option>
                        <option value="Posible foco epidemico">Posible foco epidemico</option>
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Descripción Breve</label><textarea id="descripcion_breve" class="form-control" rows="2" required></textarea></div>
            <button type="submit" class="btn btn-danger"><i class="fas fa-paper-plane"></i> Publicar Alerta</button>
        </form>
    </div>

    <div class="card p-4 shadow-sm">
        <table class="table table-hover">
            <thead>
                <tr><th>Amenaza</th><th>Paciente</th><th>Riesgo</th><th>Descripción</th><th>Acción</th></tr>
            </thead>
            <tbody id="lista-alertas">
                </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function cargarAlertas() {
        $.get(APP_URL + '/api/alertas/listar', function(res) {
            let html = '';
            res.forEach(a => {
                html += `<tr>
                    <td>${a.tipo_amenaza}</td>
                    <td>${a.nombre_paciente}</td>
                    <td><span class="badge badge-${a.nivel_riesgo === 'Alto' ? 'danger' : 'warning'}">${a.nivel_riesgo}</span></td>
                    <td>${a.descripcion_breve}</td>
                    <td><button class="btn btn-sm btn-outline-danger" onclick="eliminarAlerta(${a.id_alerta})"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            });
            $('#lista-alertas').html(html);
        });
    }

    $('#form-alerta').submit(function(e) {
        e.preventDefault();
        $.post(APP_URL + '/api/alertas/guardar', {
            tipo_amenaza: $('#tipo_amenaza').val(),
            nombre_paciente: $('#nombre_paciente').val(),
            cedula_paciente: $('#cedula_paciente').val(),
            nivel_riesgo: $('#nivel_riesgo').val(),
            descripcion_breve: $('#descripcion_breve').val(),
            csrf_token: $('input[name="csrf_token"]').val()
        }, function(res) {
            if(res.success) {
                $('#form-alerta')[0].reset();
                cargarAlertas();
            }
        });
    });

    window.eliminarAlerta = function(id) {
        if(confirm('¿Confirmar eliminación?')) {
            $.post(APP_URL + '/api/alertas/eliminar', {id: id, csrf_token: $('input[name="csrf_token"]').val()}, cargarAlertas);
        }
    };
    
    cargarAlertas();
});
</script>
</body>
</html>
