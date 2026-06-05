<<<<<<< HEAD
<<<<<<< HEAD

=======
/**
 * Configuración global del sistema
 * Detecta automáticamente la URL base del proyecto
<<<<<<< HEAD
 * VERSIÓN 2.0 - Compatible con Front Controller (rutas /api/...)
=======
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
 */
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======

>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0

(function() {
    // Detectar la URL base del proyecto
    var path = window.location.pathname;
    var baseUrl = '';
    
    // Patrones comunes de instalación
    var patterns = [
        { pattern: '/biovital/', base: '/biovital' },
<<<<<<< HEAD
<<<<<<< HEAD
        { pattern: '/biovital', base: '/biovital' },
        { pattern: '/public/', base: '/public' },
        { pattern: '/public', base: '/public' },
=======
<<<<<<< HEAD
        { pattern: '/biovital', base: '/biovital' },
        { pattern: '/public/', base: '/public' },
        { pattern: '/public', base: '/public' },
=======
        { pattern: '/public/', base: '/public' },
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
        { pattern: '/biovital', base: '/biovital' },
        { pattern: '/public/', base: '/public' },
        { pattern: '/public', base: '/public' },
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
        
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
        // Buscar la carpeta del proyecto (la que contiene 'controlador', 'modelo', 'vista')
        // Por defecto, la primera parte suele ser el proyecto
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
        if (parts.length > 0 && !parts[0].includes('.php') && !parts[0].includes('.')) {
            baseUrl = '/' + parts[0];
        }
    }
    
<<<<<<< HEAD
<<<<<<< HEAD
    // ==================== NUEVA CONFIGURACIÓN ====================
    // API_URL ahora apunta a /api (router MVC), NO a /controlador directamente
=======
<<<<<<< HEAD
=======
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
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
<<<<<<< HEAD
})();
=======
    // Variables globales de configuración
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
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
    
<<<<<<< HEAD
    if (typeof window.APP_URL === 'undefined') {
        window.APP_URL = baseUrl;
    }
    
    console.log('[CONFIG] Configuración cargada:', {
        BASE_URL: window.CONFIG.BASE_URL,
        API_URL: window.CONFIG.API_URL,
        UPLOADS_URL: window.CONFIG.UPLOADS_URL
    });
})();
=======
    console.log('Configuración cargada:', window.CONFIG);
})();
>>>>>>> d2039bf34adef6d12dd6c79371df596a3d39fedb
>>>>>>> f341bcbb925276c3abd14e136b7a785bda722852
=======
})();
>>>>>>> c29324f8947233d5281c64cb5729a15acf34bac0
