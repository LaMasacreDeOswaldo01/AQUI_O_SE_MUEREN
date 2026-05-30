<?php
class ApiResponse {
    
    // ==================== CONSTANTES PARA CÓDIGOS DE ESTADO ====================
    
    // Códigos de estado generales
    const CODE_SUCCESS = 'success';
    const CODE_ERROR = 'error';
    
    // Códigos para operaciones CRUD
    const CODE_CREATED = 'created';
    const CODE_UPDATED = 'updated';
    const CODE_DELETED = 'deleted';
    
    // Códigos para errores comunes
    const CODE_NOT_FOUND = 'not_found';
    const CODE_VALIDATION_ERROR = 'validation_error';
    const CODE_AUTH_ERROR = 'auth_error';
    const CODE_CSRF_ERROR = 'csrf_error';
    const CODE_SERVER_ERROR = 'server_error';
    const CODE_FORBIDDEN = 'forbidden';
    const CODE_DUPLICATE_ENTRY = 'duplicate_entry';
    const CODE_UNAUTHORIZED = 'unauthorized';
    
    // ==================== MÉTODO PRINCIPAL ====================
    
    /**
     * Envía una respuesta JSON estandarizada al cliente.
     * 
     * @param bool $success Indica si la operación fue exitosa
     * @param string $code Código de la operación (ej: 'created', 'validation_error')
     * @param string $message Mensaje amigable para el usuario (o técnico para debug)
     * @param mixed $data Datos adicionales a incluir en la respuesta (array u objeto)
     * @param int $httpStatusCode Código HTTP de la respuesta (ej: 200, 400, 401, 403, 404, 500)
     * @return void Termina la ejecución del script
     */
    public static function send($success, $code, $message, $data = [], $httpStatusCode = 200) {
        // Evitar enviar múltiples respuestas
        if (headers_sent()) {
            error_log("[ApiResponse] Error: Headers ya enviados en " . self::getCallerInfo());
            error_log("[ApiResponse] No se pudo enviar respuesta JSON. Success: " . ($success ? 'true' : 'false') . ", Code: $code");
            return;
        }
        
        // Establecer código de respuesta HTTP
        http_response_code($httpStatusCode);
        
        // Establecer cabecera Content-Type como JSON
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        
        // Construir la respuesta base
        $response = [
            'success' => $success,
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // ==================== INFORMACIÓN DE DEPURACIÓN (SOLO EN DESARROLLO) ====================
        $environment = getenv('APP_ENV') ?: 'development';
        if ($environment !== 'production') {
            $response['debug'] = self::getDebugInfo();
        }
        
        // ==================== REGISTRO DE LOGS (SOLO ERRORES EN PRODUCCIÓN) ====================
        if (!$success && $environment === 'production') {
            self::logError($code, $message, $data);
        }
        
        // Enviar respuesta como JSON
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
    
    // ==================== RESPUESTAS EXITOSAS ====================
    
    /**
     * Respuesta de éxito genérica
     * HTTP Status: 200 OK
     * 
     * @param mixed $data Datos a incluir en la respuesta
     * @param string $code Código de la operación
     * @param string $message Mensaje de éxito
     */
    public static function success($data = [], $code = self::CODE_SUCCESS, $message = 'Operación exitosa') {
        self::send(true, $code, $message, $data, 200);
    }
    
    /**
     * Respuesta de éxito al crear un recurso
     * HTTP Status: 201 Created
     * 
     * @param mixed $data Datos del recurso creado (incluyendo ID si es necesario)
     * @param string $message Mensaje de éxito
     */
    public static function created($data = [], $message = 'Recurso creado exitosamente') {
        self::send(true, self::CODE_CREATED, $message, $data, 201);
    }
    
    /**
     * Respuesta de éxito al actualizar un recurso
     * HTTP Status: 200 OK
     * 
     * @param mixed $data Datos actualizados del recurso
     * @param string $message Mensaje de éxito
     */
    public static function updated($data = [], $message = 'Recurso actualizado exitosamente') {
        self::send(true, self::CODE_UPDATED, $message, $data, 200);
    }
    
    /**
     * Respuesta de éxito al eliminar un recurso
     * HTTP Status: 200 OK
     * 
     * @param string $message Mensaje de éxito
     */
    public static function deleted($message = 'Recurso eliminado exitosamente') {
        self::send(true, self::CODE_DELETED, $message, [], 200);
    }
    
    /**
     * Respuesta sin contenido (para operaciones que no retornan datos)
     * HTTP Status: 204 No Content
     */
    public static function noContent() {
        http_response_code(204);
        header('Content-Type: application/json');
        exit();
    }
    
    // ==================== RESPUESTAS DE ERROR ====================
    
    /**
     * Respuesta de error genérico
     * HTTP Status: 400 Bad Request
     * 
     * @param string $message Mensaje de error
     * @param string $code Código de error
     * @param mixed $data Datos adicionales del error
     * @param int $httpStatusCode Código HTTP (por defecto 400)
     */
    public static function error($message, $code = self::CODE_ERROR, $data = [], $httpStatusCode = 400) {
        self::send(false, $code, $message, $data, $httpStatusCode);
    }
    
    /**
     * Error de validación de datos (formulario con campos incorrectos)
     * HTTP Status: 422 Unprocessable Entity
     * 
     * @param array $errors Array con los errores de validación (campo => mensaje)
     * @param string $message Mensaje general del error
     */
    public static function validationError($errors, $message = 'Error de validación') {
        self::send(false, self::CODE_VALIDATION_ERROR, $message, ['errors' => $errors], 422);
    }
    
    /**
     * Error de autenticación (usuario no ha iniciado sesión)
     * HTTP Status: 401 Unauthorized
     * 
     * @param string $message Mensaje de error
     */
    public static function unauthorized($message = 'No autenticado. Debe iniciar sesión') {
        self::send(false, self::CODE_UNAUTHORIZED, $message, [], 401);
    }
    
    /**
     * Error de permisos (usuario autenticado pero sin autorización)
     * HTTP Status: 403 Forbidden
     * 
     * @param string $message Mensaje de error
     */
    public static function forbidden($message = 'No tiene permisos para realizar esta acción') {
        self::send(false, self::CODE_FORBIDDEN, $message, [], 403);
    }
    
    /**
     * Error de recurso no encontrado
     * HTTP Status: 404 Not Found
     * 
     * @param string $resource Nombre del recurso no encontrado
     */
    public static function notFound($resource = 'Recurso') {
        self::send(false, self::CODE_NOT_FOUND, "{$resource} no encontrado", [], 404);
    }
    
    /**
     * Error de token CSRF inválido
     * HTTP Status: 403 Forbidden
     * 
     * @param string $message Mensaje de error
     */
    public static function csrfError($message = 'Token CSRF inválido. Por favor, recargue la página') {
        self::send(false, self::CODE_CSRF_ERROR, $message, [], 403);
    }
    
    /**
     * Error de entrada duplicada (registro ya existe)
     * HTTP Status: 409 Conflict
     * 
     * @param string $message Mensaje de error
     * @param mixed $data Datos adicionales
     */
    public static function duplicateEntry($message = 'El registro ya existe', $data = []) {
        self::send(false, self::CODE_DUPLICATE_ENTRY, $message, $data, 409);
    }
    
    /**
     * Error interno del servidor
     * HTTP Status: 500 Internal Server Error
     * 
     * @param string $message Mensaje de error
     */
    public static function serverError($message = 'Error interno del servidor. Por favor, intente más tarde') {
        self::send(false, self::CODE_SERVER_ERROR, $message, [], 500);
    }
    
    // ==================== MÉTODOS AUXILIARES PRIVADOS ====================
    
    /**
     * Obtiene información de depuración para respuestas en modo desarrollo
     * 
     * @return array Información del archivo y línea que llamó a la API
     */
    private static function getDebugInfo() {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        
        // Buscar el primer frame que no sea de esta clase ni de funciones anónimas
        foreach ($trace as $index => $frame) {
            if (isset($frame['class']) && $frame['class'] === __CLASS__) {
                continue;
            }
            if (isset($frame['function']) && $frame['function'] === '{closure}') {
                continue;
            }
            
            return [
                'caller_file' => $frame['file'] ?? 'unknown',
                'caller_line' => $frame['line'] ?? 'unknown',
                'caller_function' => $frame['function'] ?? 'unknown',
                'caller_class' => $frame['class'] ?? 'global',
                'trace_index' => $index
            ];
        }
        
        // Si no se encontró información, usar el frame más reciente
        $lastFrame = end($trace);
        return [
            'caller_file' => $lastFrame['file'] ?? 'unknown',
            'caller_line' => $lastFrame['line'] ?? 'unknown',
            'caller_function' => $lastFrame['function'] ?? 'unknown',
            'caller_class' => $lastFrame['class'] ?? 'global'
        ];
    }
    
    /**
     * Obtiene información del caller para logs de error
     * 
     * @return string Información del archivo y línea
     */
    private static function getCallerInfo() {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = $trace[1] ?? $trace[0] ?? [];
        
        $file = $caller['file'] ?? 'unknown';
        $line = $caller['line'] ?? 'unknown';
        $function = $caller['function'] ?? 'unknown';
        
        return "$file:$line (function: $function)";
    }
    
    /**
     * Registra errores en el log del sistema (solo para producción)
     * 
     * @param string $code Código de error
     * @param string $message Mensaje de error
     * @param mixed $data Datos adicionales
     */
    private static function logError($code, $message, $data) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'caller' => self::getCallerInfo(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
        ];
        
        error_log("[ApiResponse Error] " . json_encode($logData, JSON_UNESCAPED_UNICODE));
    }
    
    // ==================== MÉTODOS DE UTILIDAD ====================
    
    /**
     * Verifica si la respuesta fue exitosa (útil para testing)
     * 
     * @param array $response Respuesta decodificada de la API
     * @return bool True si fue exitosa
     */
    public static function isSuccessResponse($response) {
        return is_array($response) && isset($response['success']) && $response['success'] === true;
    }
    
    /**
     * Obtiene el código de error de una respuesta (útil para testing)
     * 
     * @param array $response Respuesta decodificada de la API
     * @return string|null Código de error o null si no existe
     */
    public static function getErrorCode($response) {
        if (self::isSuccessResponse($response)) {
            return null;
        }
        return $response['code'] ?? null;
    }
}
?>