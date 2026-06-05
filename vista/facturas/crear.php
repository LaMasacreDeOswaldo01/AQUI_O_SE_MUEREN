<?php require_once ROOT . '/vista/partials/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-file-invoice-dollar mr-2"></i>Generar Factura</h4>
                </div>
                <div class="card-body">
                    <form id="form-crear-factura">
                        <!-- Datos de la cita -->
                        <div class="alert alert-info">
                            <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Datos de la Cita</h5>
                            <p class="mb-1"><strong>Paciente:</strong> <?= htmlspecialchars($cita->nombre_paciente . ' ' . $cita->apellido_paciente) ?></p>
                            <p class="mb-1"><strong>Médico:</strong> <?= htmlspecialchars($cita->nombre_medico . ' ' . $cita->apellido_medico) ?></p>
                            <p class="mb-0"><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($cita->fecha)) ?> - <?= $cita->hora ?></p>
                        </div>
                        
                        <input type="hidden" name="id_cita" value="<?= $cita->id_cita ?>">
                        <input type="hidden" name="id_paciente" value="<?= $cita->id_paciente ?>">
                        <input type="hidden" name="id_medico" value="<?= $cita->id_medico ?>">
                        <input type="hidden" name="fecha_cita" value="<?= $cita->fecha ?>">
                        <input type="hidden" name="nombre_medico" value="<?= htmlspecialchars($cita->nombre_medico . ' ' . $cita->apellido_medico) ?>">
                        
                        <!-- Opciones de generación -->
                        <div class="form-group mb-3">
                            <label><strong>¿Cómo desea generar la factura?</strong></label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="modo_generacion" value="automatica" checked onchange="cambiarModo()">
                                    <strong>Automática</strong> - Usar la tarifa del médico ($<span id="tarifa-medico">...</span>)
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="modo_generacion" value="manual" onchange="cambiarModo()">
                                    <strong>Manual</strong> - Ingresar conceptos y montos personalizados
                                </label>
                            </div>
                        </div>
                        
                        <!-- Detalles manuales (oculto por defecto) -->
                        <div id="detalles-manuales" style="display: none;">
                            <h5 class="mb-3">Detalles de Cobro</h5>
                            <div id="detalles-container">
                                <div class="row detalle-row mb-3">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control concepto" name="detalles[0][concepto]" 
                                               placeholder="Concepto (ej: Consulta médica)" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control cantidad" name="detalles[0][cantidad]" 
                                               value="1" min="1" required onchange="calcularSubtotal(this)">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control precio" name="detalles[0][precio_unitario]" 
                                               placeholder="Precio ($)" step="0.01" required onchange="calcularSubtotal(this)">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control subtotal" name="detalles[0][subtotal]" 
                                               readonly value="0.00">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(this)" 
                                                style="display: none;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="agregarDetalle()">
                                <i class="fas fa-plus"></i> Agregar Concepto
                            </button>
                            
                            <!-- Totales -->
                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6"><strong>Subtotal:</strong></div>
                                                <div class="col-6 text-right" id="subtotal-display">$0.00</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6"><strong>IVA (0%):</strong></div>
                                                <div class="col-6 text-right" id="iva-display">$0.00</div>
                                            </div>
                                            <div class="row border-top pt-2">
                                                <div class="col-6"><strong>TOTAL:</strong></div>
                                                <div class="col-6 text-right" id="total-display"><strong>$0.00</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="subtotal" id="subtotal-input" value="0">
                            <input type="hidden" name="iva" id="iva-input" value="0">
                            <input type="hidden" name="total" id="total-input" value="0">
                        </div>
                        
                        <!-- Forma de pago -->
                        <div class="form-group mb-3">
                            <label>Forma de Pago</label>
                            <select class="form-control" name="forma_pago">
                                <option value="pago_movil">Pago Móvil</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="tarjeta">Tarjeta</option>
                            </select>
                        </div>
                        
                        <!-- Observaciones -->
                        <div class="form-group mb-3">
                            <label>Observaciones (opcional)</label>
                            <textarea class="form-control" name="observaciones" rows="3" 
                                      placeholder="Notas adicionales sobre la factura"></textarea>
                        </div>
                        
                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= APP_URL ?>/citas" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Generar Factura
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let detalleIndex = 1;

// Cargar tarifa del médico al inicio
$(document).ready(function() {
    $.ajax({
        url: APP_URL + '/api/medico/obtener-tarifa',
        type: 'POST',
        data: { id_medico: <?= $cita->id_medico ?> },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#tarifa-medico').text(response.data.tarifa);
            }
        }
    });
});

function cambiarModo() {
    const modo = $('input[name="modo_generacion"]:checked').val();
    if (modo === 'manual') {
        $('#detalles-manuales').show();
    } else {
        $('#detalles-manuales').hide();
    }
}

function agregarDetalle() {
    const html = `
        <div class="row detalle-row mb-3">
            <div class="col-md-5">
                <input type="text" class="form-control concepto" name="detalles[${detalleIndex}][concepto]" 
                       placeholder="Concepto" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control cantidad" name="detalles[${detalleIndex}][cantidad]" 
                       value="1" min="1" required onchange="calcularSubtotal(this)">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control precio" name="detalles[${detalleIndex}][precio_unitario]" 
                       placeholder="Precio ($)" step="0.01" required onchange="calcularSubtotal(this)">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control subtotal" name="detalles[${detalleIndex}][subtotal]" 
                       readonly value="0.00">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    $('#detalles-container').append(html);
    detalleIndex++;
    actualizarBotonesEliminar();
}

function eliminarDetalle(btn) {
    $(btn).closest('.detalle-row').remove();
    calcularTotales();
    actualizarBotonesEliminar();
}

function actualizarBotonesEliminar() {
    const filas = $('.detalle-row');
    filas.each(function(index) {
        const btn = $(this).find('.btn-danger');
        if (filas.length > 1) {
            btn.show();
        } else {
            btn.hide();
        }
    });
}

function calcularSubtotal(input) {
    const row = $(input).closest('.detalle-row');
    const cantidad = parseFloat(row.find('.cantidad').val()) || 0;
    const precio = parseFloat(row.find('.precio').val()) || 0;
    const subtotal = cantidad * precio;
    
    row.find('.subtotal').val(subtotal.toFixed(2));
    calcularTotales();
}

function calcularTotales() {
    let subtotal = 0;
    
    $('.detalle-row').each(function() {
        const subtotalDetalle = parseFloat($(this).find('.subtotal').val()) || 0;
        subtotal += subtotalDetalle;
    });
    
    const iva = 0; // 0% IVA
    const total = subtotal + iva;
    
    $('#subtotal-display').text('$' + subtotal.toFixed(2));
    $('#iva-display').text('$' + iva.toFixed(2));
    $('#total-display').text('$' + total.toFixed(2));
    
    $('#subtotal-input').val(subtotal.toFixed(2));
    $('#iva-input').val(iva.toFixed(2));
    $('#total-input').val(total.toFixed(2));
}

$('#form-crear-factura').submit(function(e) {
    e.preventDefault();
    
    const modo = $('input[name="modo_generacion"]:checked').val();
    
    if (modo === 'manual') {
        const subtotal = parseFloat($('#subtotal-input').val());
        if (subtotal <= 0) {
            alert('El total de la factura debe ser mayor a $0.00');
            return;
        }
    }
    
    const formData = $(this).serialize();
    const $btn = $(this).find('button[type="submit"]');
    const originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');
    
    $.ajax({
        url: APP_URL + '/api/facturas/crear',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Factura generada exitosamente: ' + response.data.numero_factura);
                window.location.href = response.data.redirect;
            } else {
                alert('Error al generar factura: ' + response.message);
                $btn.prop('disabled', false).html(originalText);
            }
        },
        error: function() {
            alert('Error al generar factura');
            $btn.prop('disabled', false).html(originalText);
        }
    });
});
</script>

<?php require_once ROOT . '/vista/partials/footer.php'; ?>
