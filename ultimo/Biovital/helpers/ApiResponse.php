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
    public static function success($data = [], $code = self::CODE_SUCCESS, $message = 'Operación exitosa') {
        self::send(true, $code, $message, $data, 200);
    }
    
    public static function created($data = [], $message = 'Recurso creado exitosamente') {
        self::send(true, self::CODE_CREATED, $message, $data, 201);
    }
    
    public static function updated($data = [], $message = 'Recurso actualizado exitosamente') {
        self::send(true, self::CODE_UPDATED, $message, $data, 200);
    }
  
    public static function deleted($message = 'Recurso eliminado exitosamente') {
        self::send(true, self::CODE_DELETED, $message, [], 200);
    }
    
    public static function noContent() {
        http_response_code(204);
        header('Content-Type: application/json');
        exit();
    }
    
    // ==================== RESPUESTAS DE ERROR ====================
    public static function error($message, $code = self::CODE_ERROR, $data = [], $httpStatusCode = 400) {
        self::send(false, $code, $message, $data, $httpStatusCode);
    }
  
    public static function validationError($errors, $message = 'Error de validación') {
        self::send(false, self::CODE_VALIDATION_ERROR, $message, ['errors' => $errors], 422);
    }
 
    public static function unauthorized($message = 'No autenticado. Debe iniciar sesión') {
        self::send(false, self::CODE_UNAUTHORIZED, $message, [], 401);
    }
    
   
    public static function forbidden($message = 'No tiene permisos para realizar esta acción') {
        self::send(false, self::CODE_FORBIDDEN, $message, [], 403);
    }
    
    public static function notFound($resource = 'Recurso') {
        self::send(false, self::CODE_NOT_FOUND, "{$resource} no encontrado", [], 404);
    }
    
    public static function csrfError($message = 'Token CSRF inválido. Por favor, recargue la página') {
        self::send(false, self::CODE_CSRF_ERROR, $message, [], 403);
    }
 
    public static function duplicateEntry($message = 'El registro ya existe', $data = []) {
        self::send(false, self::CODE_DUPLICATE_ENTRY, $message, $data, 409);
    }

    public static function serverError($message = 'Error interno del servidor. Por favor, intente más tarde') {
        self::send(false, self::CODE_SERVER_ERROR, $message, [], 500);
    }
    
    // ==================== MÉTODOS AUXILIARES PRIVADOS ====================
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
    
    private static function getCallerInfo() {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = $trace[1] ?? $trace[0] ?? [];
        
        $file = $caller['file'] ?? 'unknown';
        $line = $caller['line'] ?? 'unknown';
        $function = $caller['function'] ?? 'unknown';
        
        return "$file:$line (function: $function)";
    }

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
    public static function isSuccessResponse($response) {
        return is_array($response) && isset($response['success']) && $response['success'] === true;
    }
    
    public static function getErrorCode($response) {
        if (self::isSuccessResponse($response)) {
            return null;
        }
        return $response['code'] ?? null;
    }
}
?>