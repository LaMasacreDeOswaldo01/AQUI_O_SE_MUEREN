<?php require_once ROOT . '/vista/partials/header.php'; ?>

<style>
@media print {
    .no-print { display: none !important; }
    .factura-container { 
        box-shadow: none !important; 
        border: 1px solid #ddd !important; 
        max-width: 100% !important;
    }
    body { background: white !important; }
    .container { max-width: 100% !important; }
}
.factura-container {
    font-family: 'Courier New', monospace;
    background: white;
    padding: 20px;
    border: 2px solid #333;
    max-width: 600px;
    margin: 0 auto;
}
.factura-line {
    border-top: 1px solid #333;
    margin: 10px 0;
}
.factura-section {
    margin: 15px 0;
}
.factura-label {
    font-weight: bold;
}
.factura-table {
    width: 100%;
    border-collapse: collapse;
}
.factura-table th, .factura-table td {
    border: 1px solid #333;
    padding: 8px;
    text-align: left;
}
.factura-table th {
    background: #f0f0f0;
}
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="factura-container shadow-sm" id="factura-card">
                <!-- Encabezado -->
                <div class="text-center">
                    <h3>MI CLÍNICA</h3>
                    <p>Dirección: Calle Principal #123</p>
                    <p>Teléfono: (809) 555-1234</p>
                </div>
                
                <div class="factura-line"></div>
                
                <!-- Número y fecha -->
                <div class="text-center">
                    <p><strong>FACTURA N°:</strong> <?= htmlspecialchars($factura->numero_factura) ?></p>
                    <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($factura->fecha_emision)) ?></p>
                </div>
                
                <div class="factura-line"></div>
                
                <!-- Datos del paciente y médico -->
                <div class="factura-section">
                    <p><span class="factura-label">PACIENTE:</span> <?= htmlspecialchars($factura->nombre_paciente . ' ' . $factura->apellido_paciente) ?></p>
                    <p><span class="factura-label">CÉDULA:</span> <?= htmlspecialchars($factura->cedula_paciente) ?></p>
                    <p><span class="factura-label">MÉDICO:</span> <?= htmlspecialchars($factura->nombre_medico . ' ' . $factura->apellido_medico) ?></p>
                    <p><span class="factura-label">ESPECIALIDAD:</span> <?= htmlspecialchars($factura->especialidad ?? 'N/A') ?></p>
                    <p><span class="factura-label">SERVICIO:</span> Consulta médica</p>
                    <p><span class="factura-label">FECHA DE CITA:</span> <?= date('d/m/Y', strtotime($factura->fecha_cita)) ?></p>
                </div>
                
                <div class="factura-line"></div>
                
                <!-- Tabla de conceptos -->
                <div class="factura-section">
                    <table class="factura-table">
                        <thead>
                            <tr>
                                <th>DESCRIPCIÓN</th>
                                <th>CANTIDAD</th>
                                <th>PRECIO</th>
                                <th>SUBTOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles as $detalle): ?>
                                <tr>
                                    <td><?= htmlspecialchars($detalle->concepto) ?></td>
                                    <td><?= $detalle->cantidad ?></td>
                                    <td>$<?= number_format($detalle->precio_unitario, 2) ?></td>
                                    <td>$<?= number_format($detalle->subtotal, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;"><strong>TOTAL:</strong></td>
                                <td><strong>$<?= number_format($factura->total, 2) ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="factura-line"></div>
                
                <!-- Pago Móvil -->
                <?php if ($config_pago_movil): ?>
                    <div class="factura-section">
                        <p><strong>PAGO MÓVIL:</strong></p>
                        <p>Banco: <?= htmlspecialchars($config_pago_movil->banco) ?></p>
                        <p>Celular: <?= htmlspecialchars($config_pago_movil->telefono_beneficiario) ?></p>
                        
                        <?php if ($factura->estado === 'pendiente' && $permisos['marcar_pago']): ?>
                            <div class="no-print">
                                <p>Referencia: <input type="text" id="referencia-pago" style="width: 200px;" placeholder="Número de referencia"></p>
                                <button class="btn btn-success btn-sm" onclick="confirmarPago()">[CONFIRMAR PAGO]</button>
                            </div>
                        <?php elseif ($factura->estado === 'pagada'): ?>
                            <p>Referencia: <?= htmlspecialchars($factura->referencia_pago) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="factura-line"></div>
                
                <!-- Estado y pie -->
                <div class="factura-section">
                    <p><span class="factura-label">ESTADO:</span> 
                        <span class="badge badge-<?= $factura->estado === 'pagada' ? 'success' : ($factura->estado === 'pendiente' ? 'warning' : 'danger') ?>">
                            <?= ucfirst($factura->estado) ?>
                        </span>
                    </p>
                    <p class="text-center"><em>¡Gracias por su visita!</em></p>
                </div>
                
                <div class="factura-line"></div>
                
                <!-- Botones -->
                <div class="text-center no-print" style="margin-top: 20px;">
                    <button class="btn btn-secondary" onclick="window.print()">[IMPRIMIR]</button>
                    <button class="btn btn-info" onclick="guardarPDF()">[GUARDAR PDF]</button>
                    <a href="<?= APP_URL ?>/<?= $rol === 'paciente' ? 'panel/paciente' : ($rol === 'medico' ? 'panel/medico' : 'facturas') ?>" class="btn btn-primary">[CERRAR]</a>
                    
                    <?php if ($permisos['editar'] && $factura->estado === 'pendiente'): ?>
                        <button class="btn btn-warning" onclick="editarFactura()">[EDITAR]</button>
                    <?php endif; ?>
                    
                    <?php if ($permisos['eliminar'] && $factura->estado === 'pendiente'): ?>
                        <button class="btn btn-danger" onclick="cancelarFactura()">[CANCELAR]</button>
                    <?php endif; ?>
                </div>
                
                <!-- Auditoría (solo asistente y administrador) -->
                <?php if ($permisos['ver_auditoria'] && !empty($auditoria)): ?>
                    <div class="factura-line no-print"></div>
                    <div class="factura-section no-print">
                        <p><strong>HISTORIAL DE CAMBIOS:</strong></p>
                        <table class="factura-table" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Acción</th>
                                    <th>Usuario</th>
                                    <th>Cambio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($auditoria as $aud): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($aud->fecha_auditoria)) ?></td>
                                        <td><?= ucfirst($aud->accion) ?></td>
                                        <td><?= htmlspecialchars($aud->nombre_usuario ?? 'N/A') ?></td>
                                        <td>
                                            <?php if ($aud->valor_anterior || $aud->valor_nuevo): ?>
                                                <?= htmlspecialchars($aud->valor_anterior ?? '-') ?> → <?= htmlspecialchars($aud->valor_nuevo ?? '-') ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const idFactura = <?= $factura->id_factura ?>;

function confirmarPago() {
    const referencia = $('#referencia-pago').val().trim();
    
    if (!referencia) {
        alert('Por favor, ingrese el número de referencia del pago.');
        return;
    }
    
    if (!confirm('¿Confirmar el pago de esta factura con la referencia: ' + referencia + '?')) {
        return;
    }
    
    $.ajax({
        url: APP_URL + '/api/facturas/marcar-pagada',
        type: 'POST',
        data: {
            id_factura: idFactura,
            referencia_pago: referencia
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Pago confirmado exitosamente');
                location.reload();
            } else {
                alert('Error al confirmar pago: ' + response.message);
            }
        },
        error: function() {
            alert('Error al confirmar pago');
        }
    });
}

function editarFactura() {
    alert('Función de edición en desarrollo');
}

function cancelarFactura() {
    if (!confirm('¿Está seguro de cancelar esta factura?')) {
        return;
    }
    
    $.ajax({
        url: APP_URL + '/api/facturas/cancelar',
        type: 'POST',
        data: { id_factura: idFactura },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Factura cancelada exitosamente');
                location.reload();
            } else {
                alert('Error al cancelar factura: ' + response.message);
            }
        },
        error: function() {
            alert('Error al cancelar factura');
        }
    });
}

function guardarPDF() {
    window.print();
    alert('Use la opción de impresión del navegador y seleccione "Guardar como PDF"');
}
</script>

<?php require_once ROOT . '/vista/partials/footer.php'; ?>
