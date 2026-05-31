

(function() {
    // Detectar la URL base del proyecto
    var path = window.location.pathname;
    var baseUrl = '';
    
    // Patrones comunes de instalación
    var patterns = [
        { pattern: '/biovital/', base: '/biovital' },
        { pattern: '/biovital', base: '/biovital' },
        { pattern: '/public/', base: '/public' },
        { pattern: '/public', base: '/public' },
        { pattern: '/medico/', base: '' },
        { pattern: '/paciente/', base: '' },
        { pattern: '/administrador/', base: '' },
        { pattern: '/asistente/', base: '' }
    ];
    
    // Buscar coincidencia con patrones conocidos
    for (var i = 0; i < patterns.length; i++) {
        if (path.includes(patterns[i].pattern)) {
            baseUrl = patterns[i].base;
            break;
        }
    }
    
    // Si no se detectó, inferir de la estructura de carpetas
    if (baseUrl === '') {
        var parts = path.split('/');
        // Eliminar partes vacías
        parts = parts.filter(function(p) { return p !== ''; });
        
        if (parts.length > 0 && !parts[0].includes('.php') && !parts[0].includes('.')) {
            baseUrl = '/' + parts[0];
        }
    }
    
    // ==================== NUEVA CONFIGURACIÓN ====================
    // API_URL ahora apunta a /api (router MVC), NO a /controlador directamente
    window.CONFIG = {
        BASE_URL: baseUrl,
        API_URL: baseUrl + '/api',           // ← CAMBIADO: usa router MVC
        UPLOADS_URL: baseUrl + '/img',
        
        // Método para obtener URL de API (sin extensión .php)
        getApiUrl: function(endpoint) {
            // endpoint ejemplo: 'registro/paciente', 'csrf/token', 'consultorios/crear'
            return this.API_URL + '/' + endpoint;
        },
        
        // DEPRECADO: Mantener por compatibilidad, pero mostrar advertencia
        getControllerUrl: function(controller) {
            console.warn('[CONFIG] getControllerUrl() está obsoleto. Use getApiUrl() en su lugar.');
            console.warn('[CONFIG] Llamado desde:', new Error().stack);
            // Convertir nombre de controlador a endpoint
            var endpoint = controller.toLowerCase().replace('controller', '');
            return this.getApiUrl(endpoint);
        }
    };
    
    if (typeof window.APP_URL === 'undefined') {
        window.APP_URL = baseUrl;
    }
    
    console.log('[CONFIG] Configuración cargada:', {
        BASE_URL: window.CONFIG.BASE_URL,
        API_URL: window.CONFIG.API_URL,
        UPLOADS_URL: window.CONFIG.UPLOADS_URL
    });
})();