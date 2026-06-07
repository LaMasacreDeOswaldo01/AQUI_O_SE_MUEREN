<?php
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_medico = $id_medico ?? $_SESSION['usuario'] ?? 0;
?>
<style>
    /* Estilos para Mi Agenda */
    .agenda-sidebar {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border: 1px solid #eef2f6;
    }
    .agenda-sidebar h5 {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--bv-dark);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #eef2f6;
    }
    .legenda-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        font-size: 0.8rem;
    }
    .legenda-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        margin-right: 10px;
    }
    .color-confirmada { background-color: #3b82f6; }
    .color-pendiente { background-color: #f59e0b; }
    .color-completada { background-color: #10b981; }
    .color-disponible { background-color: #d1fae5; border: 1px solid #10b981; }
    .color-no-laborable { background-color: #fee2e2; border: 1px solid #ef4444; }
    
    .dias-semana {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }
    .dia-btn {
        flex: 1;
        text-align: center;
        padding: 0.5rem;
        border-radius: 10px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .dia-btn:hover {
        background: #e8f4f8;
    }
    .dia-btn.active {
        background: var(--bv-primary);
        color: white;
    }
    
    .consultorio-badge {
        background: #e8f4f8;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        display: inline-block;
        margin: 0.25rem;
    }
    
    .fc-event {
        cursor: pointer;
        border-radius: 8px;
        padding: 2px 4px;
    }
    
    .btn-editar-horarios {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        border: none;
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-editar-horarios:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,119,182,0.3);
    }
    
    .resumen-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .resumen-card .resumen-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--bv-primary);
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-calendar-alt"></i> Mi Agenda</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/medico">Inicio</a></li>
                    <li class="breadcrumb-item active">Mi Agenda</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_medico" value="<?php echo $id_medico; ?>">
        
        <div class="row">
            <!-- Sidebar Izquierdo -->
            <div class="col-md-3">
                <!-- Información del Médico -->
                <div class="agenda-sidebar">
                    <h5><i class="fas fa-user-md"></i> Dr(a). <?php echo htmlspecialchars($nombre_usuario); ?></h5>
                    <div id="info_especialidades"></div>
                    <div id="info_consultorios"></div>
                </div>
                
                <!-- Leyenda -->
                <div class="agenda-sidebar">
                    <h5><i class="fas fa-tag"></i> Estado de Citas</h5>
                    <div class="legenda-item">
                        <div class="legenda-color color-confirmada"></div>
                        <span>Cita Confirmada</span>
                    </div>
                    <div class="legenda-item">
                        <div class="legenda-color color-pendiente"></div>
                        <span>Cita Programada</span>
                    </div>
                    <div class="legenda-item">
                        <div class="legenda-color color-completada"></div>
                        <span>Cita Completada</span>
                    </div>
                    <div class="legenda-item">
                        <div class="legenda-color color-disponible"></div>
                        <span>Disponible</span>
                    </div>
                    <div class="legenda-item">
                        <div class="legenda-color color-no-laborable"></div>
                        <span>No Laborable</span>
                    </div>
                </div>
                
                <!-- Días de la semana -->
                <div class="agenda-sidebar">
                    <h5><i class="fas fa-calendar-week"></i> Días de la semana</h5>
                    <div class="dias-semana" id="dias-semana">
                        <div class="dia-btn" data-dia="Lunes">Lun</div>
                        <div class="dia-btn" data-dia="Martes">Mar</div>
                        <div class="dia-btn" data-dia="Miércoles">Mié</div>
                        <div class="dia-btn" data-dia="Jueves">Jue</div>
                        <div class="dia-btn" data-dia="Viernes">Vie</div>
                        <div class="dia-btn" data-dia="Sábado">Sáb</div>
                        <div class="dia-btn" data-dia="Domingo">Dom</div>
                    </div>
                </div>
                
                <!-- Resumen Semanal -->
                <div class="agenda-sidebar">
                    <h5><i class="fas fa-chart-line"></i> Resumen Semanal</h5>
                    <div class="resumen-card">
                        <div class="text-center">
                            <div class="resumen-number" id="dias_activos">0</div>
                            <small>Días activos</small>
                        </div>
                        <div class="text-center mt-2">
                            <div class="resumen-number" id="horas_semanales">0</div>
                            <small>Horas semanales</small>
                        </div>
                        <div class="text-center mt-2">
                            <div class="resumen-number" id="cupos_maximos">0</div>
                            <small>Cupos máximos/día</small>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <button class="btn btn-editar-horarios btn-sm w-100" id="btnEditarHorarios">
                            <i class="fas fa-edit"></i> Editar Horarios Semanales
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Calendario Principal -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt"></i> Calendario de Citas
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Detalle Cita Rápido -->
<div class="modal fade modal-bv" id="modalCitaRapida" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent)); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check"></i> Detalle de Cita
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="modal_cita_content">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <a href="#" class="btn btn-primary" id="verDetalleCompleto">Ver Detalle Completo</a>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo APP_URL; ?>/js/medico_agenda.js"></script>
