<div class="modal fade" id="modal_agenda" tabindex="-1" role="dialog" aria-labelledby="modal_agendaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modal_agendaLabel">
                    <i class="fas fa-calendar-alt mr-2"></i>Agenda de Citas Médicas
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="px-3 pt-3">
                <ul class="nav nav-tabs" id="agendaTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="lista-tab" data-toggle="tab" href="#tab_lista_citas" role="tab" aria-controls="lista" aria-selected="true">
                            <i class="fas fa-list mr-1"></i> Lista de Citas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="config-tab" data-toggle="tab" href="#tab_config_calendario" role="tab" aria-controls="config" aria-selected="false">
                            <i class="fas fa-cog mr-1"></i> Configuración y Calendario
                        </a>
                    </li>
                </ul>
            </div>

            <div class="modal-body">
                <div class="tab-content" id="agendaTabsContent">
                    
                    <div class="tab-pane fade show active" id="tab_lista_citas" role="tabpanel" aria-labelledby="lista-tab">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3 id="citas_hoy_count">0</h3>
                                        <p>Citas Programadas para Hoy</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-user-clock"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card card-outline card-primary mb-0">
                                    <div class="card-body py-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            </div>
                                            <input type="text" id="filtro_agenda" class="form-control" placeholder="Filtrar por paciente, motivo o estado...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button" id="btn_ver_todas_citas">
                                                    <i class="fas fa-globe mr-1"></i> Ver Todas
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-gray-dark">
                                    <tr>
                                        <th>Hora</th>
                                        <th>Paciente</th>
                                        <th>Tipo / Especialidad</th>
                                        <th>Motivo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="lista_agenda_medico">
                                    </tbody>
                            </table>
                        </div>
                    </div> <div class="tab-pane fade" id="tab_config_calendario" role="tabpanel" aria-labelledby="config-tab">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card card-info card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-clock mr-1"></i> Horario de Atención</h3>
                                    </div>
                                    <div class="card-body">
                                        <form id="form_config_horario">
                                            <div class="form-group">
                                                <label for="hora_inicio">Hora de Inicio General</label>
                                                <input type="time" class="form-control" id="hora_inicio" value="08:00">
                                            </div>
                                            <div class="form-group">
                                                <label for="hora_fin">Hora de Cierre General</label>
                                                <input type="time" class="form-control" id="hora_fin" value="17:00">
                                            </div>
                                            <div class="form-group">
                                                <label>Días Laborables Semanales</label>
                                                <div class="d-flex flex-wrap">
                                                    <div class="custom-control custom-checkbox mr-3">
                                                        <input class="custom-control-input" type="checkbox" id="chk_lun" checked>
                                                        <label for="chk_lun" class="custom-control-label">Lun</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox mr-3">
                                                        <input class="custom-control-input" type="checkbox" id="chk_mar" checked>
                                                        <label for="chk_mar" class="custom-control-label">Mar</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox mr-3">
                                                        <input class="custom-control-input" type="checkbox" id="chk_mie" checked>
                                                        <label for="chk_mie" class="custom-control-label">Mié</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox mr-3">
                                                        <input class="custom-control-input" type="checkbox" id="chk_jue" checked>
                                                        <label for="chk_jue" class="custom-control-label">Jue</label>
                                                    </div>
                                                    <div class="custom-control custom-checkbox mr-3">
                                                        <input class="custom-control-input" type="checkbox" id="chk_vie" checked>
                                                        <label for="chk_vie" class="custom-control-label">Vie</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-info btn-block" id="btn_guardar_horario_base">
                                                <i class="fas fa-save mr-1"></i> Guardar Horario Base
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <h5><i class="icon fas fa-info"></i> Bloqueo de Días</h5>
                                    <p class="small mb-0">Para bloquear un día entero o feriado (marcarlo en rojo), haz <strong>clic</strong> directamente sobre el día correspondiente en el calendario.</p>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="card card-primary card-outline">
                                    <div class="card-body p-2">
                                        <div id="calendario_medico"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> </div> </div> <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btn_actualizar_agenda">
                    <i class="fas fa-sync"></i> Actualizar Lista
                </button>
            </div>
        </div>
    </div>
</div>