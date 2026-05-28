/**
 * CSRF Protection - Versión corregida
 * NO genera tokens falsos. Si falla el servidor, muestra error y bloquea formularios.
 */

var CSRF = (function() {
    // Almacenar token en memoria
    var currentToken = null;
    var tokenExpiry = null;
    var isInitialized = false;
    var initError = null;
    var retryCount = 0;
    var maxRetries = 3;
    
    // Verificar que APP_URL esté definida
    if (typeof APP_URL === 'undefined') {
        console.error('APP_URL no está definida para CSRF');
        window.APP_URL = '';
    }
    
    // Mostrar error al usuario
    function showError(mensaje) {
        console.error('[CSRF] ' + mensaje);
        
        // Buscar o crear elemento de error global
        var errorDiv = document.getElementById('csrf-global-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'csrf-global-error';
            errorDiv.style.cssText = 'position:fixed; top:10px; left:50%; transform:translateX(-50%); z-index:10000; background:#dc3545; color:white; padding:12px 20px; border-radius:8px; font-size:14px; font-family:sans-serif; box-shadow:0 4px 12px rgba(0,0,0,0.2); display:none;';
            document.body.appendChild(errorDiv);
        }
        
        errorDiv.innerHTML = '<i class="fas fa-shield-alt"></i> ' + mensaje + ' <button onclick="location.reload()" style="background:white; border:none; border-radius:4px; margin-left:10px; padding:4px 10px; cursor:pointer;">Recargar</button>';
        errorDiv.style.display = 'block';
        
        // Deshabilitar todos los formularios
        $('form').each(function() {
            $(this).data('csrf-disabled', true);
            $(this).find('button[type="submit"]').prop('disabled', true);
            $(this).find('input, select, textarea').prop('disabled', true);
        });
    }
    
    // Obtener token del servidor (sin fallback falso)
    function fetchTokenFromServer(callback) {
        var tokenUrl = APP_URL + '/api/csrf/token';
        console.log('[CSRF] Obteniendo token desde:', tokenUrl);
        
        $.ajax({
            url: tokenUrl,
            type: 'POST',
            dataType: 'json',
            timeout: 8000, // 8 segundos de timeout
            success: function(response) {
                if (response && response.status === 'success' && response.csrf_token) {
                    currentToken = response.csrf_token;
                    tokenExpiry = Date.now() + 3600000;
                    console.log('[CSRF] Token obtenido correctamente');
                    if (callback) callback(true, currentToken);
                } else {
                    console.error('[CSRF] Respuesta inválida del servidor:', response);
                    if (callback) callback(false, null);
                }
            },
            error: function(xhr, status, error) {
                console.error('[CSRF] Error al obtener token - Status:', status, 'Error:', error);
                console.error('[CSRF] Respuesta del servidor:', xhr.responseText);
                if (callback) callback(false, null);
            }
        });
    }
    
    // Intentar obtener token con reintentos
    function fetchTokenWithRetry(callback, attempt) {
        attempt = attempt || 0;
        
        fetchTokenFromServer(function(success, token) {
            if (success) {
                isInitialized = true;
                enableForms();
                if (callback) callback(token);
            } else if (attempt < maxRetries - 1) {
                console.log('[CSRF] Reintentando obtener token... (intento ' + (attempt + 2) + ' de ' + maxRetries + ')');
                setTimeout(function() {
                    fetchTokenWithRetry(callback, attempt + 1);
                }, 1000);
            } else {
                // Fallaron todos los reintentos - mostrar error y bloquear
                initError = 'No se pudo establecer conexión segura con el servidor.';
                showError(initError);
                isInitialized = false;
                if (callback) callback(null);
            }
        });
    }
    
    // Habilitar formularios después de obtener token
    function enableForms() {
        $('form').each(function() {
            $(this).data('csrf-disabled', false);
            $(this).find('button[type="submit"]').prop('disabled', false);
            $(this).find('input, select, textarea').prop('disabled', false);
        });
        
        var errorDiv = document.getElementById('csrf-global-error');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }
    
    // Inicializar token
    function init(callback) {
        fetchTokenWithRetry(function(token) {
            if (token) {
                setupAjaxInterceptor();
                addTokenToForms();
                setupFormUpdater();
            }
            if (callback) callback(token !== null);
        });
    }
    
    // Obtener token actual (devuelve null si no hay token válido)
    function getToken() {
        if (!isInitialized || !currentToken) {
            console.warn('[CSRF] Token no disponible. Asegúrate de que CSRF.init() se haya completado.');
            return null;
        }
        
        if (tokenExpiry && Date.now() > tokenExpiry) {
            console.warn('[CSRF] Token expirado. Se renovará en la próxima petición.');
            // Renovar token de forma asíncrona
            fetchTokenWithRetry(function(newToken) {
                if (newToken) {
                    currentToken = newToken;
                    updateFormsToken();
                }
            });
            return null;
        }
        
        return currentToken;
    }
    
    // Actualizar tokens en formularios existentes
    function updateFormsToken() {
        if (!currentToken) return;
        $('form input[name="csrf_token"]').each(function() {
            $(this).val(currentToken);
        });
    }
    
    // Agregar token a los datos de una petición AJAX
    function addTokenToData(data) {
        var token = getToken();
        if (!token) {
            console.error('[CSRF] No se puede enviar petición: token CSRF no disponible');
            return data;
        }
        
        if (typeof data === 'object' && data !== null) {
            data.csrf_token = token;
        }
        return data;
    }
    
    // Interceptar peticiones AJAX POST
    function setupAjaxInterceptor() {
        $(document).ajaxSend(function(event, xhr, settings) {
            if (settings.type === 'POST' && settings.data) {
                var token = getToken();
                if (!token) {
                    console.error('[CSRF] Petición POST bloqueada: token CSRF no disponible');
                    // Abortar la petición si no hay token
                    xhr.abort();
                    showError('No se puede completar la acción por un problema de seguridad. Recarga la página.');
                    return false;
                }
                
                if (typeof settings.data === 'string') {
                    if (settings.data.indexOf('csrf_token=') === -1) {
                        settings.data += '&csrf_token=' + encodeURIComponent(token);
                    }
                } 
                else if (settings.data instanceof FormData) {
                    if (!settings.data.has('csrf_token')) {
                        settings.data.append('csrf_token', token);
                    }
                }
                else if (typeof settings.data === 'object') {
                    if (!settings.data.csrf_token) {
                        settings.data.csrf_token = token;
                    }
                }
            }
        });
    }
    
    // Agregar token a formularios HTML
    function addTokenToForms() {
        if (!currentToken) return;
        
        $('form').each(function() {
            // Marcar que este formulario ya tiene protección CSRF
            if ($(this).data('csrf-protected')) return;
            $(this).data('csrf-protected', true);
            
            if ($(this).find('input[name="csrf_token"]').length === 0) {
                $(this).append('<input type="hidden" name="csrf_token" value="' + currentToken + '">');
            } else {
                $(this).find('input[name="csrf_token"]').val(currentToken);
            }
        });
    }
    
    // Actualizar tokens periódicamente (cada 30 minutos)
    function setupFormUpdater() {
        setInterval(function() {
            if (currentToken && tokenExpiry && Date.now() > tokenExpiry - 300000) {
                // Token próximo a expirar, renovar
                fetchTokenWithRetry(function(newToken) {
                    if (newToken) {
                        currentToken = newToken;
                        updateFormsToken();
                    }
                });
            }
        }, 30 * 60 * 1000);
    }
    
    // Verificar si CSRF está listo
    function isReady() {
        return isInitialized && currentToken !== null;
    }
    
    // API pública
    return {
        init: init,
        getToken: getToken,
        addTokenToData: addTokenToData,
        setupAjaxInterceptor: setupAjaxInterceptor,
        addTokenToForms: addTokenToForms,
        setupFormUpdater: setupFormUpdater,
        isReady: isReady
    };
})();

// Inicializar cuando el documento esté listo
$(document).ready(function() {
    console.log('[CSRF] Inicializando protección CSRF...');
    CSRF.init(function(success) {
        if (success) {
            console.log('[CSRF] Protección CSRF activada correctamente');
        } else {
            console.error('[CSRF] No se pudo activar la protección CSRF');
        }
    });
});