<?php
// vista/layouts/scripts_head.php
// Scripts y variables globales que deben cargarse en TODAS las vistas
?>
<script>
    // URL base del proyecto (definida en PHP)
    var APP_URL = '<?php echo APP_URL; ?>';
    
    // Modo debug (útil para desarrollo)
    var DEBUG_MODE = <?php echo (getenv('APP_ENV') !== 'production') ? 'true' : 'false'; ?>;
    
    console.log('[BioVital] APP_URL:', APP_URL);
    <?php if (getenv('APP_ENV') !== 'production'): ?>
    console.log('[BioVital] Modo desarrollo activado');
    <?php endif; ?>
</script>

<!-- jQuery (siempre primero) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Configuración y CSRF -->
<script src="<?php echo APP_URL; ?>/js/config.js"></script>
<script src="<?php echo APP_URL; ?>/js/csrf.js"></script>

<!-- Bootstrap 4 (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
