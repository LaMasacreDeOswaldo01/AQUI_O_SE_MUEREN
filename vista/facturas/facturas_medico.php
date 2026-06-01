<?php require_once ROOT . '/vista/partials/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-file-invoice-dollar mr-2"></i>Facturas de Mis Pacientes</h4>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select class="form-control" id="filtro-estado" onchange="filtrarFacturas()">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendientes</option>
                                <option value="pagada">Pagadas</option>
                                <option value="cancelada">Canceladas</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="filtro-paciente" placeholder="Nombre del paciente" onkeyup="filtrarFacturas()">
                        </div>
                    </div>
                    
                    <!-- Tabla de facturas -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>N° Factura</th>
                                    <th>Fecha</th>
                                    <th>Paciente</th>
                                    <th>Total ($)</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-facturas">
                                <?php if (empty($facturas)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No hay facturas de sus pacientes</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($facturas as $factura): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($factura->numero_factura) ?></strong></td>
                                            <td><?= date('d/m/Y H:i', strtotime($factura->fecha_emision)) ?></td>
                                            <td><?= htmlspecialchars($factura->nombre_paciente . ' ' . $factura->apellido_paciente) ?></td>
                                            <td><strong>$<?= number_format($factura->total, 2) ?></strong></td>
                                            <td>
                                                <?php
                                                $badgeClass = 'secondary';
                                                switch ($factura->estado) {
                                                    case 'pagada': $badgeClass = 'success'; break;
                                                    case 'pendiente': $badgeClass = 'warning'; break;
                                                    case 'cancelada': $badgeClass = 'danger'; break;
                                                }
                                                ?>
                                                <span class="badge badge-<?= $badgeClass ?>"><?= ucfirst($factura->estado) ?></span>
                                            </td>
                                            <td>
                                                <a href="<?= APP_URL ?>/facturas/detalle?id=<?= $factura->id_factura ?>" 
                                                   class="btn btn-info btn-sm" title="Ver detalle">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            </td>
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
</div>

<script>
function filtrarFacturas() {
    const estado = $('#filtro-estado').val();
    const paciente = $('#filtro-paciente').val().toLowerCase();
    
    $('#tabla-facturas tr').each(function() {
        const row = $(this);
        const rowEstado = row.find('.badge').text().toLowerCase();
        const rowPaciente = row.find('td:nth-child(3)').text().toLowerCase();
        
        let mostrar = true;
        
        if (estado && rowEstado !== estado) {
            mostrar = false;
        }
        
        if (paciente && !rowPaciente.includes(paciente)) {
            mostrar = false;
        }
        
        row.toggle(mostrar);
    });
}
</script>

<?php require_once ROOT . '/vista/partials/footer.php'; ?>
