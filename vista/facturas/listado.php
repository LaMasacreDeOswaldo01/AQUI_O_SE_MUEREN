<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-file-invoice-dollar"></i> Gestión de Facturas</h1>
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
                        <h3 class="card-title">Listado de Facturas</h3>
                    </div>
                    <div class="card-body">
                    <!-- Filtros de búsqueda -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="filtro-numero" placeholder="Número de factura">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="filtro-paciente" placeholder="Nombre del paciente">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="filtro-estado">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="pagada">Pagada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="filtro-fecha-desde">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" onclick="buscarFacturas()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
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
                                    <th>Médico</th>
                                    <th>Total ($)</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-facturas">
                                <?php if (empty($facturas)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No hay facturas registradas</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($facturas as $factura): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($factura->numero_factura) ?></strong></td>
                                            <td><?= date('d/m/Y H:i', strtotime($factura->fecha_emision)) ?></td>
                                            <td><?= htmlspecialchars($factura->nombre_paciente . ' ' . $factura->apellido_paciente) ?></td>
                                            <td><?= htmlspecialchars($factura->nombre_medico . ' ' . $factura->apellido_medico) ?></td>
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
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= APP_URL ?>/facturas/detalle?id=<?= $factura->id_factura ?>" 
                                                       class="btn btn-info" title="Ver detalle">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($_SESSION['rol'] === 'administrador'): ?>
                                                        <button class="btn btn-danger" onclick="eliminarFactura(<?= $factura->id_factura ?>)" 
                                                                title="Eliminar factura">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
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
</section>

<script>
function buscarFacturas() {
    const filtros = {
        numero: $('#filtro-numero').val(),
        paciente: $('#filtro-paciente').val(),
        estado: $('#filtro-estado').val(),
        fecha_desde: $('#filtro-fecha-desde').val()
    };
    
    $.ajax({
        url: APP_URL + '/api/facturas/buscar',
        type: 'POST',
        data: filtros,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                actualizarTabla(response.data.facturas);
            }
        },
        error: function() {
            alert('Error al buscar facturas');
        }
    });
}

function actualizarTabla(facturas) {
    let html = '';
    
    if (facturas.length === 0) {
        html = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No se encontraron facturas</p>
                </td>
            </tr>
        `;
    } else {
        facturas.forEach(factura => {
            let badgeClass = 'secondary';
            switch (factura.estado) {
                case 'pagada': badgeClass = 'success'; break;
                case 'pendiente': badgeClass = 'warning'; break;
                case 'cancelada': badgeClass = 'danger'; break;
            }
            
            html += `
                <tr>
                    <td><strong>${factura.numero_factura}</strong></td>
                    <td>${formatDate(factura.fecha_emision)}</td>
                    <td>${factura.nombre_paciente} ${factura.apellido_paciente}</td>
                    <td>${factura.nombre_medico} ${factura.apellido_medico}</td>
                    <td><strong>$${parseFloat(factura.total).toFixed(2)}</strong></td>
                    <td><span class="badge badge-${badgeClass}">${factura.estado.charAt(0).toUpperCase() + factura.estado.slice(1)}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="${APP_URL}/facturas/detalle?id=${factura.id_factura}" class="btn btn-info" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            ${$_SESSION['rol'] === 'administrador' ? `
                                <button class="btn btn-danger" onclick="eliminarFactura(${factura.id_factura})" title="Eliminar factura">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#tabla-facturas').html(html);
}

function eliminarFactura(idFactura) {
    if (!confirm('¿Está seguro de eliminar esta factura? Esta acción no se puede deshacer.')) {
        return;
    }
    
    $.ajax({
        url: APP_URL + '/api/facturas/eliminar',
        type: 'POST',
        data: { id_factura: idFactura },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Factura eliminada exitosamente');
                location.reload();
            } else {
                alert('Error al eliminar factura: ' + response.message);
            }
        },
        error: function() {
            alert('Error al eliminar factura');
        }
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-VE') + ' ' + date.toLocaleTimeString('es-VE', { hour: '2-digit', minute: '2-digit' });
}
</script>
