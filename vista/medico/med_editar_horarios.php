<?php
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_medico = $id_medico ?? $_SESSION['usuario'] ?? 0;
?>
<style>
    .horario-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #eef2f6;
        margin-bottom: 1rem;
        overflow: hidden;
        transition: all 0.3s;
    }
    .horario-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .horario-card .card-header {
        background: #f8f9fa;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #eef2f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .horario-card .card-header .dia-nombre {
        font-weight: 700;
        font-size: 1rem;
        color: var(--bv-dark);
    }
    .horario-card .card-header .dia-nombre i {
        color: var(--bv-primary);
        margin-right: 0.5rem;
    }
    .horario-card .card-body {
        padding: 1.25rem;
    }
    .turno-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.5rem;
    }
    .turno-section:last-child {
        margin-bottom: 0;
    }
    .turno-section h6 {
        font-weight: 700;
        color: var(--bv-primary);
        margin-bottom: 0.75rem;
        font-size: 0.85rem;
    }
    .badge-estado {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .badge-activo {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-inactivo {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-horario {
        background: #e8f4f8;
        color: #0d9488;
        margin-left: 0.5rem;
    }
    .btn-editar-dia {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        border: none;
        border-radius: 8px;
        padding: 0.35rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-editar-dia:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,119,182,0.3);
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 22px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .3s;
        border-radius: 22px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #10b981;
    }
    input:checked + .slider:before {
        transform: translateX(22px);
    }
    .plantilla-btn {
        background: #f8f9fa;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .plantilla-btn:hover {
        background: var(--bv-primary);
        color: white;
        border-color: var(--bv-primary);
    }
    .resumen-stats {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-box {
        flex: 1;
        text-align: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 12px;
    }
    .stat-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--bv-primary);
    }
    .info-medico {
        background: linear-gradient(135deg, #0d9488, #0f766e);
        border-radius: 16px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        color: white;
    }
    .info-medico h4 {
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
    }
    .info-medico p {
        opacity: 0.9;
        margin-bottom: 0;
        font-size: 0.8rem;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-clock"></i> Horarios del Médico</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/medico">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/medico/agenda">Mi Agenda</a></li>
                    <li class="breadcrumb-item active">Editar Horarios</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_medico" value="<?php echo $id_medico; ?>">
        
        <!-- Información del Médico -->
        <div class="info-medico">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4><i class="fas fa-user-md"></i> Dr(a). <?php echo htmlspecialchars($nombre_usuario); ?></h4>
                    <p><i class="fas fa-stethoscope"></i> Configure sus horarios de atención semanales</p>
                </div>
                <div>
                    <i class="fas fa-chart-line fa-2x" style="opacity: 0.5;"></i>
                </div>
            </div>
        </div>
        
        <!-- Resumen Semanal -->
        <div class="resumen-stats">
            <div class="stat-box">
                <div class="stat-number" id="dias_activos">0</div>
                <small>Días activos de 7</small>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="horas_semanales">0</div>
                <small>Horas semanales</small>
            </div>
            <div class="stat-box">
                <div class="stat-number" id="cupos_maximos">0</div>
                <small>Cupos máximos/día</small>
            </div>
        </div>
        
        <!-- Plantillas Rápidas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bolt"></i> Plantillas Rápidas</h3>
                        <div class="card-tools">
                            <small class="text-muted">Seleccione una plantilla para aplicar horarios predefinidos</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="plantilla-btn" data-plantilla="completa">
                                    <i class="fas fa-sun"></i> Jornada Completa<br>
                                    <small>8-12, 2-6 (Lun-Vie)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="plantilla-btn" data-plantilla="solo_mananas">
                                    <i class="fas fa-sun"></i> Solo Mañanas<br>
                                    <small>8-12 (Lun-Vie)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="plantilla-btn" data-plantilla="solo_tardes">
                                    <i class="fas fa-moon"></i> Solo Tardes<br>
                                    <small>2-6 (Lun-Vie)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Horarios por Día -->
        <div id="horarios-container">
            <div class="text-center py-4">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2">Cargando horarios...</p>
            </div>
        </div>
        
        <!-- Botones de acción -->
        <div class="row mt-3">
            <div class="col-12 text-center">
                <button class="btn btn-secondary" id="btnVolverAgenda">
                    <i class="fas fa-arrow-left"></i> Volver a Mi Agenda
                </button>
                <button class="btn btn-success ml-2" id="btnGuardarTodos">
                    <i class="fas fa-save"></i> Guardar Todos los Cambios
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Modal Editar Horario por Día -->
<div class="modal fade modal-bv" id="modalEditarHorario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent)); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-day"></i> <span id="modal_dia"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_dia">
                
                <!-- Turno Mañana -->
                <div class="turno-section">
                    <h6><i class="fas fa-sun"></i> Turno Mañana</h6>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="manana_activo">
                            <label class="custom-control-label" for="manana_activo">Activar turno mañana</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora de Inicio</label>
                                <input type="time" class="form-control" id="manana_hora_inicio" value="08:00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora de Fin</label>
                                <input type="time" class="form-control" id="manana_hora_fin" value="12:00">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Duración por Cita (minutos)</label>
                        <select class="form-control" id="manana_duracion">
                            <option value="15">15 minutos</option>
                            <option value="20">20 minutos</option>
                            <option value="30" selected>30 minutos</option>
                            <option value="45">45 minutos</option>
                            <option value="60">60 minutos</option>
                        </select>
                        <small class="form-text text-muted">Tiempo estimado por consulta</small>
                    </div>
                </div>
                
                <!-- Turno Tarde -->
                <div class="turno-section">
                    <h6><i class="fas fa-moon"></i> Turno Tarde</h6>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="tarde_activo">
                            <label class="custom-control-label" for="tarde_activo">Activar turno tarde</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora de Inicio</label>
                                <input type="time" class="form-control" id="tarde_hora_inicio" value="14:00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora de Fin</label>
                                <input type="time" class="form-control" id="tarde_hora_fin" value="18:00">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Duración por Cita (minutos)</label>
                        <select class="form-control" id="tarde_duracion">
                            <option value="15">15 minutos</option>
                            <option value="20">20 minutos</option>
                            <option value="30" selected>30 minutos</option>
                            <option value="45">45 minutos</option>
                            <option value="60">60 minutos</option>
                        </select>
                        <small class="form-text text-muted">Tiempo estimado por consulta</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarHorarioDia">
                    <i class="fas fa-save"></i> Guardar Horario
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo APP_URL; ?>/js/medico_editar_horarios.js"></script>