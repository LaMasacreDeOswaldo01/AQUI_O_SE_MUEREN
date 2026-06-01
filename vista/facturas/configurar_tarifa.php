<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-dollar-sign"></i> Configurar Tarifa de Consulta</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Configurar Tarifa</h3>
                    </div>
                    <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Establezca su tarifa fija por consulta. Esta tarifa se usará automáticamente al generar facturas para sus pacientes.
                    </div>
                    
                    <form id="form-tarifa">
                        <div class="form-group">
                            <label for="tarifa">Tarifa por consulta (USD)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" class="form-control" id="tarifa" name="tarifa" 
                                       value="<?= number_format($tarifa_actual, 2) ?>" step="0.01" min="0" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">USD</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">Ingrese el monto en dólares estadounidenses</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Tarifa actual</label>
                            <div class="alert alert-secondary">
                                <strong>$<?= number_format($tarifa_actual, 2) ?> USD</strong>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Guardar Tarifa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$('#form-tarifa').submit(function(e) {
    e.preventDefault();
    
    const tarifa = parseFloat($('#tarifa').val());
    
    if (tarifa < 0) {
        alert('La tarifa no puede ser negativa');
        return;
    }
    
    const $btn = $(this).find('button[type="submit"]');
    const originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
    
    $.ajax({
        url: APP_URL + '/api/facturas/actualizar-tarifa',
        type: 'POST',
        data: { tarifa: tarifa },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Tarifa actualizada exitosamente');
                location.reload();
            } else {
                alert('Error al actualizar tarifa: ' + response.message);
                $btn.prop('disabled', false).html(originalText);
            }
        },
        error: function() {
            alert('Error al actualizar tarifa');
            $btn.prop('disabled', false).html(originalText);
        }
    });
});
</script>
