<?php
$nombre_usuario = $nombre_usuario ?? 'Usuario';
?>
<style>
    .tipo-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        border: 2px solid #eef2f6;
        height: 100%;
    }
    .tipo-card:hover {
        transform: translateY(-8px);
        border-color: var(--bv-primary);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .tipo-card .icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, rgba(0,119,182,0.1), rgba(67,97,238,0.1));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--bv-primary);
        transition: all 0.3s;
    }
    .tipo-card:hover .icon {
        background: linear-gradient(135deg, var(--bv-primary), var(--bv-accent));
        color: white;
        transform: scale(1.1);
    }
    .tipo-card h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .tipo-card p {
        color: var(--bv-text-light);
        margin-bottom: 0;
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-calendar-plus"></i> Agendar Nueva Cita</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2 class="mb-2">Completa los pasos para solicitar tu consulta médica</h2>
                <p class="text-muted">¿Para quién es esta cita?</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-5 col-sm-10 mb-4">
                <div class="tipo-card" id="tipo-propia">
                    <div class="icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>Cita Propia</h3>
                    <p>La consulta es para mí mismo</p>
                </div>
            </div>
            <div class="col-md-5 col-sm-10 mb-4">
                <div class="tipo-card" id="tipo-tercero">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Cita para Terceros</h3>
                    <p>Agendar para otra persona</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function() {
    $('#tipo-propia').click(function() {
        window.location.href = APP_URL + '/paciente/citas/agendar-propia';
    });
    
    $('#tipo-tercero').click(function() {
        window.location.href = APP_URL + '/paciente/citas/agendar-tercero';
    });
});
</script>
