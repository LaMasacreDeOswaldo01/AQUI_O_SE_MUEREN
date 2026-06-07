<?php
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_medico = $id_medico ?? $_SESSION['usuario'] ?? 0;
?>
<style>
    .citas-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card-citas {
        background: white;
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        border: 2px solid transparent;
        border-left: 4px solid var(--bv-primary);
    }
    .stat-card-citas:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .stat-card-citas.active {
        border-color: var(--bv-primary);
        background: #f0f7ff;
    }
    .stat-card-citas .stat-number {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--bv-dark);
    }
    .stat-card-citas .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--bv-text-light);
    }
    .stat-card-citas .stat-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    /* Tarjeta de cita */
    .cita-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #eef2f6;
        transition: all 0.3s;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .cita-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .cita-header {
        background: #f8f9fa;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid #eef2f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    .cita-fecha {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .cita-fecha .dia {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--bv-primary);
    }
    .cita-fecha .hora {
        background: #e8f4f8;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-family: monospace;
    }
    .cita-body {
        padding: 1.25rem;
    }
    .cita-paciente {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .paciente-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.2rem;
    }
    .paciente-info h4 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .paciente-info p {
        font-size: 0.75rem;
        color: var(--bv-text-light);
        margin-bottom: 0;
    }
    .cita-detalle {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eef2f6;
    }
    .detalle-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
    }
    .detalle-item i {
        width: 20px;
        color: var(--bv-primary);
    }
    .cita-footer {
        background: #fafbfc;
        padding: 0.75rem 1.25rem;
        border-top: 1px solid #eef2f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .badge-estado-cita {
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .badge-estado-pendiente { background: #fef3c7; color: #92400e; }
    .badge-estado-confirmada { background: #dbeafe; color: #1e40af; }
    .badge-estado-en_progreso { background: #e0e7ff; color: #4338ca; }
    .badge-estado-completada { background: #d1fae5; color: #065f46; }
    .badge-estado-cancelada { background: #fee2e2; color: #991b1b; }
    .badge-estado-no_asistio { background: #f1f5f9; color: #475569; }
    
    .dropdown-estado .dropdown-item {
        font-size: 0.8rem;
        cursor: pointer;
    }
    .dropdown-estado .dropdown-item:hover {
        background: var(--bv-primary);
        color: white;
    }
    
    .filter-card {
        border-radius: 16px;
        border: 1px solid #eef2f6;
        background: white;
    }
    .search-box {
        position: relative;
    }
    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .search-box input {
        padding-left: 35px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
    }
    .resultados-pacientes {
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-top: 2px;
    }
    .resultados-pacientes .list-group-item {
        cursor: pointer;
        transition: background 0.2s;
        border: none;
        border-bottom: 1px solid #eef2f6;
        padding: 10px 12px;
    }
    .resultados-pacientes .list-group-item:hover {
        background-color: #f0f7ff;
    }
    .btn-accion-cita {
        padding: 0.25rem 0.75rem;
        font-size: 0.7rem;
        border-radius: 8px;
    }
    .empty-state {
        text-align: center;
        padding: 3rem;
        background: #fafbfc;
        border-radius: 16px;
    }
    .empty-state i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 16px;
    }
    .pagination-custom {
        margin-bottom: 0;
    }
    .pagination-custom .page-item.active .page-link {
        background-color: var(--bv-primary);
        border-color: var(--bv-primary);
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-calendar-alt"></i> Mis Citas Médicas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/medico">Inicio</a></li>
                    <li class="breadcrumb-item active">Mis Citas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_medico" value="<?php echo $id_medico; ?>">
        
        <!-- Welcome Banner -->
        <div class="bv-welcome-banner medico bv-animate">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-calendar-check me-2"></i> Mis Citas Médicas</h2>
                    <p class="mb-0">Consulta y gestiona todas tus citas programadas.</p>
                    <div class="bv-role-tag mt-2">
                        <i class="fas fa-user-md"></i> Dr(a). <?php echo htmlspecialchars($nombre_usuario); ?>
                    </div>
                </div>
                <div class="d-none d-md-block">
                    <i class="fas fa-chart-line fa-3x" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="citas-stats" id="stats-container">
            <div class="stat-card-citas" data-estado="todos">
                <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="stat-number" id="total-citas">0</div>
                <div class="stat-label">Total Citas</div>
            </div>
            <div class="stat-card-citas" data-estado="pendiente">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number" id="pendientes-count">0</div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-card-citas" data-estado="confirmada">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number" id="confirmadas-count">0</div>
                <div class="stat-label">Confirmadas</div>
            </div>
            <div class="stat-card-citas" data-estado="completada">
                <div class="stat-icon"><i class="fas fa-check-double"></i></div>
                <div class="stat-number" id="completadas-count">0</div>
                <div class="stat-label">Completadas (mes)</div>
            </div>
            <div class="stat-card-citas" data-estado="cancelada">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <div class="stat-number" id="canceladas-count">0</div>
                <div class="stat-label">Canceladas (mes)</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="row mt-3">
            <div class="col-md-8">
                <div class="filter-card p-3">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-calendar-day"></i> Fecha</label>
                                <input type="date" id="filtro_fecha" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-filter"></i> Estado</label>
                                <select id="filtro_estado" class="form-control">
                                    <option value="todos">Todos</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="en_progreso">En Progreso</option>
                                    <option value="completada">Completada</option>
                                    <option value="cancelada">Cancelada</option>
                                    <option value="no_asistio">No Asistió</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label><i class="fas fa-stethoscope"></i> Tipo Consulta</label>
                                <select id="filtro_tipo_consulta" class="form-control">
                                    <option value="todos">Todos</option>
                                    <option value="primera_vez">Primera Vez</option>
                                    <option value="control">Control</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group position-relative">
                                <label><i class="fas fa-user-injured"></i> Paciente</label>
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="buscar_paciente" class="form-control" 
                                           placeholder="Buscar por nombre, cédula o apellido..." autocomplete="off">
                                </div>
                                <div id="resultados_pacientes" class="list-group resultados-pacientes" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="filter-card p-3 text-right">
                    <button class="btn btn-primary btn-sm" id="btnRefresh">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                    <button class="btn btn-secondary btn-sm ml-2" id="btnLimpiarFiltros">
                        <i class="fas fa-eraser"></i> Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Citas -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Listado de Citas
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="loadingCitas" class="loading-overlay" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Cargando...</span>
                            </div>
                        </div>
                        <div id="contenedor_citas">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando tus citas...</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_info">
                                    Mostrando <span id="desde">0</span> a <span id="hasta">0</span> de <span id="total_registros">0</span> citas
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-end pagination-custom" id="paginacion">
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Detalle Cita -->
<div class="modal fade modal-bv" id="modalDetalleCita" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent)); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check"></i> Detalle de Cita
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="detalle_cita_content">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <div class="dropdown d-inline-block" id="dropdown-estado-modal">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fas fa-edit"></i> Cambiar Estado
                    </button>
                    <div class="dropdown-menu dropdown-estado">
                        <a class="dropdown-item" data-estado="pendiente"><i class="fas fa-clock"></i> Pendiente</a>
                        <a class="dropdown-item" data-estado="confirmada"><i class="fas fa-check-circle"></i> Confirmada</a>
                        <a class="dropdown-item" data-estado="en_progreso"><i class="fas fa-spinner"></i> En Progreso</a>
                        <a class="dropdown-item" data-estado="completada"><i class="fas fa-check-double"></i> Completada</a>
                        <a class="dropdown-item" data-estado="cancelada"><i class="fas fa-times-circle"></i> Cancelada</a>
                        <a class="dropdown-item" data-estado="no_asistio"><i class="fas fa-user-slash"></i> No Asistió</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo APP_URL; ?>/js/medico_citas.js"></script>
