<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Factura -->
            <div class="card shadow-sm" id="factura-imprimir">
                <div class="card-body p-4">
                    <!-- Encabezado -->
                    <div class="row mb-4 border-bottom pb-3">
                        <div class="col-md-6">
                            <h4 class="fw-bold text-primary mb-2">
                                <i class="fas fa-hospital-alt me-2"></i>
                                <?php echo htmlspecialchars($factura->datos_clinica['nombre'] ?? 'BioVital Clínica'); ?>
                            </h4>
                            <p class="mb-1 text-muted small">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?php echo htmlspecialchars($factura->datos_clinica['direccion'] ?? ''); ?>
                            </p>
                            <p class="mb-1 text-muted small">
                                <i class="fas fa-phone me-1"></i>
                                <?php echo htmlspecialchars($factura->datos_clinica['telefono'] ?? ''); ?>
                            </p>
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-envelope me-1"></i>
                                <?php echo htmlspecialchars($factura->datos_clinica['email'] ?? ''); ?>
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h3 class="fw-bold mb-1">FACTURA</h3>
                            <h5 class="text-primary mb-2"><?php echo htmlspecialchars($factura->numero_factura); ?></h5>
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-calendar me-1"></i>
                                Fecha: <?php echo date('d/m/Y H:i', strtotime($factura->fecha_emision)); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Datos del paciente -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold mb-2 text-secondary">
                                <i class="fas fa-user me-2"></i>DATOS DEL PACIENTE
                            </h6>
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Nombre:</strong> <?php echo htmlspecialchars($factura->paciente_nombre); ?></p>
                                            <p class="mb-0"><strong>Cédula:</strong> <?php echo htmlspecialchars($factura->paciente_cedula); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del médico y servicio -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold mb-2 text-secondary">
                                <i class="fas fa-user-md me-2"></i>DATOS DEL MÉDICO Y SERVICIO
                            </h6>
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Médico:</strong> <?php echo htmlspecialchars($factura->medico_nombre); ?></p>
                                            <p class="mb-0"><strong>Especialidad:</strong> <?php echo htmlspecialchars($factura->especialidad_nombre); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Fecha cita:</strong> <?php echo date('d/m/Y', strtotime($factura->fecha_cita)); ?></p>
                                            <p class="mb-0"><strong>Hora:</strong> <?php echo substr($factura->hora_cita, 0, 5); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalle de cobro -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold mb-2 text-secondary">
                                <i class="fas fa-file-invoice-dollar me-2"></i>DETALLE DE COBRO
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Concepto</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Precio Unitario</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($factura->detalles_factura['items'])): ?>
                                            <?php foreach ($factura->detalles_factura['items'] as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['concepto']); ?></td>
                                                    <td class="text-center"><?php echo $item['cantidad']; ?></td>
                                                    <td class="text-end">$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                                                    <td class="text-end">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                                            <td class="text-end fw-bold">$<?php echo number_format($factura->subtotal, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">IVA (0%):</td>
                                            <td class="text-end fw-bold">$<?php echo number_format($factura->iva, 2); ?></td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td colspan="3" class="text-end fw-bold fs-5">TOTAL A PAGAR:</td>
                                            <td class="text-end fw-bold fs-5">$<?php echo number_format($factura->total, 2); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Pago Móvil -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold mb-2 text-secondary">
                                <i class="fas fa-mobile-alt me-2"></i>PAGO MÓVIL
                            </h6>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Banco:</strong> <?php echo htmlspecialchars($factura->datos_beneficiario['banco'] ?? ''); ?></p>
                                            <p class="mb-1"><strong>Celular:</strong> <?php echo htmlspecialchars($factura->datos_beneficiario['celular'] ?? ''); ?></p>
                                            <p class="mb-0"><strong>Cédula:</strong> <?php echo htmlspecialchars($factura->datos_beneficiario['cedula'] ?? ''); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-0"><strong>Tipo de cuenta:</strong> <?php echo htmlspecialchars($factura->datos_beneficiario['tipo_cuenta'] ?? ''); ?></p>
                                        </div>
                                    </div>
                                    
                                    <?php if ($factura->estado === 'pendiente' && $puede_confirmar_pago): ?>
                                        <div class="row mt-3">
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-receipt"></i>
                                                    </span>
                                                    <input type="text" class="form-control" id="referenciaPago" 
                                                           placeholder="Ingrese el número de referencia" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-success w-100" id="btnConfirmarPago">
                                                    <i class="fas fa-check-circle me-2"></i>Confirmar Pago
                                                </button>
                                            </div>
                                        </div>
                                        <div id="pagoError" class="alert alert-danger mt-2" style="display:none;"></div>
                                    <?php elseif ($factura->estado === 'pagada'): ?>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Pago confirmado</strong><br>
                                            Referencia: <?php echo htmlspecialchars($factura->referencia_pago); ?><br>
                                            Fecha: <?php echo date('d/m/Y H:i', strtotime($factura->fecha_pago)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pie de factura -->
                    <div class="row border-top pt-3">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Forma de pago:</strong> 
                                        <?php echo $factura->metodo_pago ? ucfirst(str_replace('_', ' ', $factura->metodo_pago)) : 'Pendiente'; ?>
                                    </p>
                                    <p class="mb-1"><strong>Referencia:</strong> 
                                        <?php echo $factura->referencia_pago ?: 'N/A'; ?>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Estado:</strong> 
                                        <span class="badge <?php echo $factura->estado === 'pagada' ? 'bg-success' : 'bg-warning'; ?>">
                                            <?php echo ucfirst($factura->estado); ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <p class="mb-0 text-muted fst-italic">
                                        <i class="fas fa-heart me-1"></i>
                                        Gracias por su visita
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones funcionales -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-center">
                        <?php if ($puede_imprimir): ?>
                            <button type="button" class="btn btn-primary" id="btnImprimir">
                                <i class="fas fa-print me-2"></i>Imprimir
                            </button>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-info" id="btnGuardarPDF">
                            <i class="fas fa-file-pdf me-2"></i>Guardar PDF
                        </button>
                        
                        <button type="button" class="btn btn-secondary" onclick="history.back()">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </button>
                        
                        <?php if ($puede_editar && $factura->estado === 'pendiente'): ?>
                            <button type="button" class="btn btn-warning" id="btnEditar">
                                <i class="fas fa-edit me-2"></i>Editar
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($puede_eliminar): ?>
                            <button type="button" class="btn btn-danger" id="btnEliminar">
                                <i class="fas fa-trash me-2"></i>Eliminar
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditar">
                    <input type="hidden" id="editIdFactura" value="<?php echo $factura->id_factura; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Subtotal ($)</label>
                        <input type="number" step="0.01" class="form-control" id="editSubtotal" 
                               value="<?php echo $factura->subtotal; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">IVA ($)</label>
                        <input type="number" step="0.01" class="form-control" id="editIva" 
                               value="<?php echo $factura->iva; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Total ($)</label>
                        <input type="number" step="0.01" class="form-control" id="editTotal" 
                               value="<?php echo $factura->total; ?>" required readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarEdicion">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var facturaId = <?php echo $factura->id_factura; ?>;
    var facturaEstado = '<?php echo $factura->estado; ?>';
    
    // Calcular total automáticamente al editar
    $('#editSubtotal, #editIva').on('input', function() {
        var subtotal = parseFloat($('#editSubtotal').val()) || 0;
        var iva = parseFloat($('#editIva').val()) || 0;
        $('#editTotal').val((subtotal + iva).toFixed(2));
    });
    
    // Confirmar pago
    $('#btnConfirmarPago').on('click', function() {
        var referencia = $('#referenciaPago').val().trim();
        
        if (!referencia) {
            $('#pagoError').text('Por favor ingrese la referencia de pago').fadeIn();
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        
        $.ajax({
            url: APP_URL + '/api/facturas/confirmar-pago',
            type: 'POST',
            data: {
                id_factura: facturaId,
                referencia: referencia
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Pago confirmado exitosamente');
                    location.reload();
                } else {
                    $('#pagoError').text(response.message).fadeIn();
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error al procesar el pago';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#pagoError').text(errorMsg).fadeIn();
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i>Confirmar Pago');
            }
        });
    });
    
    // Imprimir
    $('#btnImprimir').on('click', function() {
        window.print();
    });
    
    // Guardar PDF (simulado)
    $('#btnGuardarPDF').on('click', function() {
        alert('Funcionalidad de PDF en desarrollo. Use la opción de imprimir y seleccione "Guardar como PDF".');
    });
    
    // Editar
    $('#btnEditar').on('click', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalEditar'));
        modal.show();
    });
    
    // Guardar edición
    $('#btnGuardarEdicion').on('click', function() {
        var datos = {
            id_factura: facturaId,
            subtotal: parseFloat($('#editSubtotal').val()),
            iva: parseFloat($('#editIva').val()),
            total: parseFloat($('#editTotal').val()),
            detalles_factura: JSON.stringify(<?php echo json_encode($factura->detalles_factura); ?>)
        };
        
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: APP_URL + '/api/facturas/actualizar',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Factura actualizada exitosamente');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error al actualizar la factura';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            },
            complete: function() {
                $btn.prop('disabled', false).html('Guardar');
            }
        });
    });
    
    // Eliminar
    $('#btnEliminar').on('click', function() {
        if (!confirm('¿Está seguro de eliminar esta factura? Esta acción no se puede deshacer.')) {
            return;
        }
        
        $.ajax({
            url: APP_URL + '/api/facturas/eliminar',
            type: 'POST',
            data: { id_factura: facturaId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Factura eliminada exitosamente');
                    history.back();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error al eliminar la factura';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    });
});
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #factura-imprimir, #factura-imprimir * {
        visibility: visible;
    }
    #factura-imprimir {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
}
</style>
