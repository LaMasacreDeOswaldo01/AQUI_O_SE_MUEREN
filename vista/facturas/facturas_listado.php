<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                                Facturas
                            </h5>
                        </div>
                        <div class="col-auto">
                            <div class="input-group" style="width: 300px;">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="buscadorFacturas" 
                                       placeholder="Buscar por número o paciente...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="filtroEstado">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendientes</option>
                                <option value="pagada">Pagadas</option>
                                <option value="cancelada">Canceladas</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tabla de facturas -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>N° Factura</th>
                                    <th>Paciente</th>
                                    <th>Médico</th>
                                    <th>Fecha Emisión</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Referencia</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaFacturas">
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    cargarFacturas();
    
    // Buscador
    $('#buscadorFacturas').on('keyup', function() {
        cargarFacturas();
    });
    
    // Filtro por estado
    $('#filtroEstado').on('change', function() {
        cargarFacturas();
    });
    
    function cargarFacturas() {
        var busqueda = $('#buscadorFacturas').val();
        var estado = $('#filtroEstado').val();
        
        $.ajax({
            url: APP_URL + '/api/facturas/listar',
            type: 'POST',
            data: {
                busqueda: busqueda,
                estado: estado
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderizarFacturas(response.data);
                } else {
                    $('#tablaFacturas').html('<tr><td colspan="8" class="text-center text-danger">Error al cargar facturas</td></tr>');
                }
            },
            error: function() {
                $('#tablaFacturas').html('<tr><td colspan="8" class="text-center text-danger">Error de conexión</td></tr>');
            }
        });
    }
    
    function renderizarFacturas(facturas) {
        if (facturas.length === 0) {
            $('#tablaFacturas').html('<tr><td colspan="8" class="text-center text-muted">No hay facturas registradas</td></tr>');
            return;
        }
        
        var html = '';
        facturas.forEach(function(f) {
            var estadoClass = f.estado === 'pagada' ? 'bg-success' : (f.estado === 'pendiente' ? 'bg-warning' : 'bg-danger');
            var puedeEditar = <?php echo $puede_editar ? 'true' : 'false'; ?>;
            var puedeEliminar = <?php echo $puede_eliminar ? 'true' : 'false'; ?>;
            
            html += '<tr>';
            html += '<td><strong>' + f.numero_factura + '</strong></td>';
            html += '<td>' + f.paciente_nombre + '</td>';
            html += '<td>' + f.medico_nombre + '</td>';
            html += '<td>' + f.fecha_emision + '</td>';
            html += '<td><strong>$' + f.total + '</strong></td>';
            html += '<td><span class="badge ' + estadoClass + '">' + f.estado.charAt(0).toUpperCase() + f.estado.slice(1) + '</span></td>';
            html += '<td>' + (f.referencia_pago || '-') + '</td>';
            html += '<td class="text-center">';
            html += '<button type="button" class="btn btn-sm btn-primary btnVerFactura" data-id="' + f.id_factura + '">';
            html += '<i class="fas fa-eye"></i>';
            html += '</button>';
            
            if (puedeEditar && f.estado === 'pendiente') {
                html += ' <button type="button" class="btn btn-sm btn-warning btnEditarFactura" data-id="' + f.id_factura + '">';
                html += '<i class="fas fa-edit"></i>';
                html += '</button>';
            }
            
            if (puedeEliminar) {
                html += ' <button type="button" class="btn btn-sm btn-danger btnEliminarFactura" data-id="' + f.id_factura + '">';
                html += '<i class="fas fa-trash"></i>';
                html += '</button>';
            }
            
            html += '</td>';
            html += '</tr>';
        });
        
        $('#tablaFacturas').html(html);
        
        // Event listeners para los botones
        $('.btnVerFactura').on('click', function() {
            var id = $(this).data('id');
            window.location.href = APP_URL + '/factura/ver?id=' + id;
        });
        
        $('.btnEditarFactura').on('click', function() {
            var id = $(this).data('id');
            window.location.href = APP_URL + '/factura/ver?id=' + id;
        });
        
        $('.btnEliminarFactura').on('click', function() {
            var id = $(this).data('id');
            if (confirm('¿Está seguro de eliminar esta factura?')) {
                $.ajax({
                    url: APP_URL + '/api/facturas/eliminar',
                    type: 'POST',
                    data: { id_factura: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Factura eliminada');
                            cargarFacturas();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Error al eliminar la factura');
                    }
                });
            }
        });
    }
});
</script>
