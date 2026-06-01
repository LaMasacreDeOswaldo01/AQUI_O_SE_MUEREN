<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-dollar-sign"></i> Tarifas de Consulta por Médico</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Tarifas</h3>
                    </div>
                    <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Médico</th>
                                    <th>Especialidad</th>
                                    <th>Tarifa por Consulta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($tarifas)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No hay médicos registrados</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($tarifas as $tarifa): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($tarifa->nombre_medico . ' ' . $tarifa->apellido_medico) ?></td>
                                            <td><?= htmlspecialchars($tarifa->especialidad ?? 'No especificada') ?></td>
                                            <td><strong>$<?= number_format($tarifa->tarifa_consulta, 2) ?> USD</strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
