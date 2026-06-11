<?php
$nombre_usuario = $nombre_usuario ?? 'Usuario';
$id_medico = $id_medico ?? $_SESSION['usuario'] ?? 0;
?>
<style>
    /* Estilos para Evoluciones Clínicas */
    .cita-selector {
        background: white;
        border-radius: 16px;
        border: 1px solid #eef2f6;
        margin-bottom: 1.5rem;
    }
    .cita-selector .card-header {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 1rem 1.25rem;
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
    .cita-item {
        cursor: pointer;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    .cita-item:hover {
        background-color: #f0f7ff;
        transform: translateX(3px);
    }
    .cita-item.active {
        background-color: #e8f4f8;
        border-left-color: var(--bv-primary);
    }
    .cita-fecha {
        font-size: 0.7rem;
        color: var(--bv-text-light);
    }
    .cita-paciente {
        font-weight: 600;
        font-size: 0.9rem;
    }
    .badge-evolucion {
        font-size: 0.65rem;
        padding: 0.2rem 0.5rem;
        border-radius: 20px;
    }
    .badge-completada {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-pendiente {
        background: #fef3c7;
        color: #92400e;
    }
    
    /* Formulario de evolución */
    .evolucion-form {
        background: white;
        border-radius: 16px;
        border: 1px solid #eef2f6;
    }
    .evolucion-form .card-header {
        background: white;
        border-bottom: 2px solid var(--bv-primary);
        padding: 1rem 1.25rem;
    }
    .signos-vitales {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    .signos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
    }
    .signo-item {
        text-align: center;
    }
    .signo-item label {
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--bv-text-light);
        display: block;
        margin-bottom: 0.25rem;
    }
    .signo-item input {
        text-align: center;
        font-weight: 600;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 0.5rem;
    }
    .signo-item .unidad {
        font-size: 0.65rem;
        color: var(--bv-text-light);
        margin-top: 0.25rem;
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    
    .guia-rapida {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    .guia-rapida h6 {
        font-size: 0.8rem;
        font-weight: 700;
        color: #92400e;
        margin-bottom: 0.5rem;
    }
    .guia-rapida ul {
        margin-bottom: 0;
        padding-left: 1rem;
    }
    .guia-rapida li {
        font-size: 0.7rem;
        color: #92400e;
    }
    
    .btn-guardar {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        border-radius: 12px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
    }
    .btn-completar {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        border: none;
        border-radius: 12px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
    }
    
    .info-paciente {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .info-paciente p {
        margin-bottom: 0.25rem;
        font-size: 0.8rem;
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
    .card {
        position: relative;
    }
    textarea.form-control {
        resize: vertical;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-notes-medical"></i> Evoluciones Clínicas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?php echo APP_URL; ?>/panel/medico">Inicio</a></li>
                    <li class="breadcrumb-item active">Evoluciones Clínicas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <input type="hidden" id="id_medico" value="<?php echo $id_medico; ?>">
        <input type="hidden" id="id_cita_seleccionada" value="">
        <input type="hidden" id="id_paciente_seleccionado" value="">
        
        <div class="row">
            <!-- Columna Izquierda - Listado de Citas -->
            <div class="col-md-4">
                <div class="cita-selector">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt"></i> Mis Citas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="search-box mb-3">
                            <i class="fas fa-search"></i>
                            <input type="text" id="buscar_cita" class="form-control" 
                                   placeholder="Buscar paciente por nombre o cédula...">
                        </div>
                        <div id="lista_citas" style="max-height: 500px; overflow-y: auto;">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary spinner-border-sm"></div>
                                <p class="mt-2 mb-0 text-muted small">Cargando citas...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Columna Derecha - Formulario de Evolución -->
            <div class="col-md-8">
                <div class="evolucion-form">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt"></i> Registrar Evolución Clínica
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="info_cita_seleccionada" class="info-paciente" style="display: none;">
                            <!-- Información dinámica de la cita seleccionada -->
                        </div>
                        
                        <div id="form_evolucion" style="display: none;">
                            <form id="formEvolucion">
                                <?php echo Security::campoCSRF(); ?>
                                <input type="hidden" id="evolucion_id_cita" name="id_cita">
                                <input type="hidden" id="evolucion_id_paciente" name="id_paciente">
                                <input type="hidden" id="marcar_completada" name="marcar_completada" value="0">
                                
                                <!-- Guía Rápida -->
                                <div class="guia-rapida">
                                    <h6><i class="fas fa-lightbulb"></i> Guía Rápida</h6>
                                    <ul>
                                        <li>Registre todos los signos vitales del paciente</li>
                                        <li>Describa detalladamente el motivo de consulta</li>
                                        <li>Especifique el diagnóstico con claridad</li>
                                        <li>Incluya el tratamiento completo</li>
                                    </ul>
                                </div>
                                
                                <!-- Signos Vitales -->
                                <div class="signos-vitales">
                                    <h6 class="mb-3"><i class="fas fa-heartbeat"></i> Signos Vitales</h6>
                                    <div class="signos-grid">
                                        <div class="signo-item">
                                            <label>Peso</label>
                                            <input type="number" step="0.1" class="form-control" id="peso" name="peso" placeholder="kg">
                                            <span class="unidad">kg</span>
                                        </div>
                                        <div class="signo-item">
                                            <label>Talla</label>
                                            <input type="number" step="0.1" class="form-control" id="talla" name="talla" placeholder="cm">
                                            <span class="unidad">cm</span>
                                        </div>
                                        <div class="signo-item">
                                            <label>IMC</label>
                                            <input type="text" class="form-control" id="imc" readonly placeholder="--">
                                            <span class="unidad">kg/m²</span>
                                        </div>
                                        <div class="signo-item">
                                            <label>Temperatura</label>
                                            <input type="number" step="0.1" class="form-control" id="temperatura" name="temperatura" placeholder="°C">
                                            <span class="unidad">°C</span>
                                        </div>
                                        <div class="signo-item">
                                            <label>T. Sistólica</label>
                                            <input type="number" class="form-control" id="tension_sistolica" name="tension_sistolica" placeholder="mmHg">
                                            <span class="unidad">mmHg</span>
                                        </div>
                                        <div class="signo-item">
                                            <label>T. Diastólica</label>
                                            <input type="number" class="form-control" id="tension_diastolica" name="tension_diastolica" placeholder="mmHg">
                                            <span class="unidad">mmHg</span>
                                        </div>
                                        <div class="signo-item">
                                            <label>Frec. Cardíaca</label>
                                            <input type="number" class="form-control" id="frecuencia_cardiaca" name="frecuencia_cardiaca" placeholder="bpm">
                                            <span class="unidad">bpm</span>
                                        </div>
                                        <div class="signo-item">
                                            <label>Frec. Respiratoria</label>
                                            <input type="number" class="form-control" id="frecuencia_respiratoria" name="frecuencia_respiratoria" placeholder="rpm">
                                            <span class="unidad">rpm</span>
                                        </div>
                                        <div class="signo-item">
                                            <label>Saturación O₂</label>
                                            <input type="number" class="form-control" id="saturacion_oxigeno" name="saturacion_oxigeno" placeholder="%">
                                            <span class="unidad">%</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Evaluación Clínica -->
                                <div class="form-group">
                                    <label class="required-field">Motivo de Consulta</label>
                                    <textarea class="form-control" id="motivo_consulta" name="motivo_consulta" rows="3" 
                                              placeholder="Describa el motivo de la consulta..."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="required-field">Enfermedad Actual</label>
                                    <textarea class="form-control" id="enfermedad_actual" name="enfermedad_actual" rows="3" 
                                              placeholder="Describa la enfermedad actual..."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Examen Físico</label>
                                    <textarea class="form-control" id="examen_fisico" name="examen_fisico" rows="3" 
                                              placeholder="Hallazgos del examen físico..."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Diagnóstico</label>
                                    <textarea class="form-control" id="diagnostico" name="diagnostico" rows="2" 
                                              placeholder="Diagnóstico principal..."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="required-field">Tratamiento</label>
                                    <textarea class="form-control" id="tratamiento" name="tratamiento" rows="3" 
                                              placeholder="Incluya medicamentos, dosis y recomendaciones..."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Recomendaciones</label>
                                    <textarea class="form-control" id="recomendaciones" name="recomendaciones" rows="2" 
                                              placeholder="Recomendaciones para el paciente..."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Notas Adicionales</label>
                                    <textarea class="form-control" id="notas_adicionales" name="notas_adicionales" rows="2" 
                                              placeholder="Notas adicionales..."></textarea>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button type="button" class="btn btn-secondary" id="btnCancelar">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-guardar ml-2" id="btnGuardar">
                                        <i class="fas fa-save"></i> Guardar Evolución
                                    </button>
                                    <button type="button" class="btn btn-completar ml-2" id="btnGuardarCompletar">
                                        <i class="fas fa-check-double"></i> Guardar y Marcar Cita Completada
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div id="sin_cita_seleccionada" class="text-center py-5">
                            <i class="fas fa-notes-medical fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Seleccione una cita para registrar la evolución clínica</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?php echo APP_URL; ?>/js/medico_evoluciones.js"></script>
