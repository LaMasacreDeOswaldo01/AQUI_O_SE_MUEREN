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
            <div class="card shadow-sm no-print">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Demo: Diseño de Factura</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Este es el diseño de factura que se mostrará cuando las tablas estén creadas.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row justify-content-center mt-4">
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
                    <p><strong>FACTURA N°:</strong> FACT-0001</p>
                    <p><strong>Fecha:</strong> 01/06/2026</p>
                </div>
                
                <div class="factura-line"></div>
                
                <!-- Datos del paciente y médico -->
                <div class="factura-section">
                    <p><span class="factura-label">PACIENTE:</span> Juan Pérez</p>
                    <p><span class="factura-label">CÉDULA:</span> 12345678</p>
                    <p><span class="factura-label">MÉDICO:</span> Dra. Ana García</p>
                    <p><span class="factura-label">ESPECIALIDAD:</span> Medicina General</p>
                    <p><span class="factura-label">SERVICIO:</span> Consulta médica</p>
                    <p><span class="factura-label">FECHA DE CITA:</span> 01/06/2026</p>
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
                            <tr>
                                <td>Consulta médica</td>
                                <td>1</td>
                                <td>$50.00</td>
                                <td>$50.00</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;"><strong>TOTAL:</strong></td>
                                <td><strong>$50.00</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="factura-line"></div>
                
                <!-- Pago Móvil -->
                <div class="factura-section">
                    <p><strong>PAGO MÓVIL:</strong></p>
                    <p>Banco: Banco de Venezuela</p>
                    <p>Celular: 0412-1234567</p>
                    <p>Referencia: <input type="text" id="referencia-pago" style="width: 200px;" placeholder="Número de referencia"></p>
                    <button class="btn btn-success btn-sm no-print" onclick="alert('Pago simulado - Funcionalidad completa cuando las tablas estén creadas')">[CONFIRMAR PAGO]</button>
                </div>
                
                <div class="factura-line"></div>
                
                <!-- Estado y pie -->
                <div class="factura-section">
                    <p><span class="factura-label">ESTADO:</span> 
                        <span class="badge badge-warning">Pendiente</span>
                    </p>
                    <p class="text-center"><em>¡Gracias por su visita!</em></p>
                </div>
                
                <div class="factura-line"></div>
                
                <!-- Botones -->
                <div class="text-center no-print" style="margin-top: 20px;">
                    <button class="btn btn-secondary" onclick="window.print()">[IMPRIMIR]</button>
                    <button class="btn btn-info" onclick="alert('Use la opción de impresión del navegador y seleccione Guardar como PDF')">[GUARDAR PDF]</button>
                    <button class="btn btn-primary" onclick="alert('Cerrar - Funcionalidad completa cuando las tablas estén creadas')">[CERRAR]</button>
                    <button class="btn btn-warning no-print" onclick="alert('Editar - Funcionalidad completa cuando las tablas estén creadas')">[EDITAR]</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT . '/vista/partials/footer.php'; ?>
