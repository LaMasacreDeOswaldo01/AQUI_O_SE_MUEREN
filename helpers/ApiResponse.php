<?php

/**
 * Clase ApiResponse
 *
 * Proporciona métodos estandarizados para enviar respuestas JSON a los clientes,
 * simplificando la gestión de códigos de estado, mensajes y datos, tanto para
 * operaciones exitosas como para errores.
 *
 * Diseñada para ser utilizada en aplicaciones web construidas con PHP, asegurando
 * consistencia en las respuestas de la API y facilitando la depuración y el registro
 * de errores.
 */
class ApiResponse
{
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

    // ==================== CONFIGURACIÓN DEL ENTORNO ====================
    
    private static $environment = null;

    /**
     * Inicializa o establece el entorno de la aplicación.
     * Se recomienda llamar a esto al inicio de tu aplicación.
     *
     * @param string $env El entorno actual ('production', 'development', 'testing', etc.)
     */
    public static function setEnvironment(string $env): void
    {
        self::$environment = strtolower($env);
    }

    /**
     * Obtiene el entorno actual de la aplicación.
     * Si no se ha establecido, intenta leer la variable de entorno 'APP_ENV'.
     * Si tampoco está disponible, asume 'development'.
     *
     * @return string El entorno actual.
     */
    private static function getEnvironment(): string
    {
        if (self::$environment === null) {
            self::$environment = getenv('APP_ENV') ?: 'development';
        }
        return strtolower(self::$environment);
    }

    // ==================== MÉTODO PRINCIPAL DE ENVÍO ====================

    /**
     * Envía una respuesta JSON estandarizada al cliente.
     * Este es el método central que maneja la construcción y envío de la respuesta.
     *
     * @param bool $success Indica si la operación fue exitosa.
     * @param string $code Código descriptivo de la operación (ej: 'created', 'validation_error').
     * @param string $message Mensaje amigable para el usuario o técnico para depuración.
     * @param array $data Datos adicionales a incluir en la respuesta (siempre un array).
     * @param int $httpStatusCode Código de estado HTTP de la respuesta (ej: 200, 400, 401, 403, 404, 500).
     * @return void Termina la ejecución del script para asegurar una única respuesta.
     */
    public static function send(bool $success, string $code, string $message, array $data = [], int $httpStatusCode = 200): void
    {
        // 1. Prevenir envío múltiple de cabeceras o contenido
        if (headers_sent()) {
            error_log("[ApiResponse] Error crítico: Cabeceras ya enviadas antes de intentar responder. Caller: " . self::getCallerInfo());
            
            if (self::getEnvironment() !== 'production') {
                http_response_code(500);
                header('Content-Type: text/plain; charset=utf-8');
                echo "Internal Server Error: Headers already sent.";
            }
            exit();
        }

        // 2. Establecer código de respuesta HTTP
        http_response_code($httpStatusCode);

        // 3. Establecer cabeceras de respuesta
        header('Content-Type: application/json; charset=utf-8');
        
        if ($success && $httpStatusCode < 300) {
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        } else {
            header('Cache-Control: no-store, no-cache, must-revalidate, proxy-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
        }

        // 4. Construir la estructura de la respuesta JSON
        $response = [
            'success' => $success,
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c') // Formato ISO 8601 estándar
        ];

        // 5. Agregar información de depuración (SOLO en entornos de desarrollo)
        $environment = self::getEnvironment();
        if ($environment !== 'production') {
            $response['debug'] = self::getDebugInfo();
        }

        // 6. Registrar logs de errores (SOLO errores y en producción)
        if (!$success && $environment === 'production') {
            self::logError($code, $message, $data, $httpStatusCode);
        }

        // 7. Enviar la respuesta JSON
        $jsonOptions = JSON_UNESCAPED_UNICODE;
        if ($environment !== 'production') {
            $jsonOptions |= JSON_PRETTY_PRINT;
        }

        echo json_encode($response, $jsonOptions);

        // 8. Terminar la ejecución del script
        exit();
    }

    // ==================== RESPUESTAS DE ÉXITO (2xx) ====================

    /**
     * Respuesta de éxito genérica.
     * HTTP Status: 200 OK
     */
    public static function success(array $data = [], string $code = self::CODE_SUCCESS, string $message = 'Operación exitosa'): void
    {
        self::send(true, $code, $message, $data, 200);
    }

    /**
     * Respuesta de éxito al crear un nuevo recurso.
     * HTTP Status: 201 Created
     */
    public static function created(array $data = [], string $message = 'Recurso creado exitosamente'): void
    {
        self::send(true, self::CODE_CREATED, $message, $data, 201);
    }

    /**
     * Respuesta de éxito al actualizar un recurso existente.
     * HTTP Status: 200 OK
     */
    public static function updated(array $data = [], string $message = 'Recurso actualizado exitosamente'): void
    {
        self::send(true, self::CODE_UPDATED, $message, $data, 200);
    }

    /**
     * Respuesta de éxito al eliminar un recurso.
     * HTTP Status: 200 OK
     */
    public static function deleted(string $message = 'Recurso eliminado exitosamente', array $data = []): void
    {
        self::send(true, self::CODE_DELETED, $message, $data, 200);
    }

    /**
     * Respuesta sin contenido. Útil para operaciones asíncronas de guardado o eliminación pura.
     * HTTP Status: 204 No Content
     */
    public static function noContent(): void
    {
        if (headers_sent()) {
            error_log("[ApiResponse] Error crítico: Cabeceras ya enviadas antes de intentar enviar 204 No Content. Caller: " . self::getCallerInfo());
            if (self::getEnvironment() !== 'production') {
                http_response_code(500);
                header('Content-Type: text/plain; charset=utf-8');
                echo "Internal Server Error: Headers already sent during noContent().";
            }
            exit();
        }

        http_response_code(204);
        exit();
    }

    // ==================== RESPUESTAS DE ERROR (4xx y 5xx) ====================

    /**
     * Respuesta de error genérica.
     * HTTP Status: 400 Bad Request (por defecto)
     */
    public static function error(string $message, string $code = self::CODE_ERROR, array $data = [], int $httpStatusCode = 400): void
    {
        if ($httpStatusCode < 400 || $httpStatusCode >= 600) {
            error_log("[ApiResponse] Advertencia: Se intentó usar el método error() con un código HTTP no válido: {$httpStatusCode}. Reajustando a 400.");
            $httpStatusCode = 400;
            $code = self::CODE_ERROR;
        }
        self::send(false, $code, $message, $data, $httpStatusCode);
    }

    /**
     * Error de validación de datos (ej: campos requeridos vacíos o formatos incorrectos).
     * HTTP Status: 422 Unprocessable Entity
     */
    public static function validationError(array $errors, string $message = 'Error de validación'): void
    {
        self::send(false, self::CODE_VALIDATION_ERROR, $message, ['errors' => $errors], 422);
    }

    /**
     * Error de autenticación (ej: token inválido o credenciales incorrectas).
     * HTTP Status: 401 Unauthorized
     */
    public static function unauthorized(string $message = 'No autenticado. Debe iniciar sesión'): void
    {
        self::send(false, self::CODE_UNAUTHORIZED, $message, [], 401);
    }

    /**
     * Error de autorización (ej: usuario logueado pero sin roles suficientes).
     * HTTP Status: 403 Forbidden
     */
    public static function forbidden(string $message = 'No tiene permisos para realizar esta acción'): void
    {
        self::send(false, self::CODE_FORBIDDEN, $message, [], 403);
    }

    /**
     * Error de recurso no encontrado.
     * HTTP Status: 404 Not Found
     */
    public static function notFound(string $resource = 'Recurso', array $data = []): void
    {
        self::send(false, self::CODE_NOT_FOUND, "{$resource} no encontrado", $data, 404);
    }

    /**
     * Error de token CSRF inválido.
     * HTTP Status: 403 Forbidden
     */
    public static function csrfError(string $message = 'Token CSRF inválido. Por favor, recargue la página'): void
    {
        self::send(false, self::CODE_CSRF_ERROR, $message, [], 403);
    }

    /**
     * Error de entrada duplicada (ej: cédula, correo o ID ya registrados).
     * HTTP Status: 409 Conflict
     */
    public static function duplicateEntry(string $message = 'El registro ya existe', array $data = []): void
    {
        self::send(false, self::CODE_DUPLICATE_ENTRY, $message, $data, 409);
    }

    /**
     * Error interno del servidor.
     * HTTP Status: 500 Internal Server Error
     */
    public static function serverError(string $message = 'Error interno del servidor. Por favor, intente más tarde', array $data = []): void
    {
        $prodMessage = (self::getEnvironment() === 'production') ? 'Error interno del servidor. Por favor, intente más tarde.' : $message;
        self::send(false, self::CODE_SERVER_ERROR, $prodMessage, $data, 500);
    }

    // ==================== MÉTODOS AUXILIARES PRIVADOS ====================

    /**
     * Obtiene información detallada del frame para depuración en desarrollo.
     */
    private static function getDebugInfo(): array
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        
        foreach ($trace as $index => $frame) {
            if (!isset($frame['class']) || $frame['class'] !== __CLASS__) {
                return [
                    'caller_file' => $frame['file'] ?? 'unknown',
                    'caller_line' => $frame['line'] ?? 'unknown',
                    'caller_function' => $frame['function'] ?? 'unknown',
                    'caller_class' => $frame['class'] ?? 'global',
                    'trace_index' => $index
                ];
            }
        }

        if (!empty($trace)) {
            $lastFrame = end($trace);
            return [
                'caller_file' => $lastFrame['file'] ?? 'unknown',
                'caller_line' => $lastFrame['line'] ?? 'unknown',
                'caller_function' => $lastFrame['function'] ?? 'unknown',
                'caller_class' => $lastFrame['class'] ?? 'global',
                'trace_index' => count($trace) - 1
            ];
        }

        return ['caller' => 'unknown'];
    }

    /**
     * Obtiene una cadena formateada del origen exacto de la llamada, saltándose la propia infraestructura.
     */
    private static function getCallerInfo(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);

        foreach ($trace as $frame) {
            if (isset($frame['class']) && $frame['class'] === __CLASS__) {
                continue;
            }

            $file = $frame['file'] ?? 'unknown';
            $line = $frame['line'] ?? 'unknown';
            $function = $frame['function'] ?? 'unknown';
            $class = isset($frame['class']) ? $frame['class'] . '::' : '';

            return "{$class}{$function} en {$file}:{$line}";
        }

        return 'Origen desconocido';
    }

    /**
     * Registra detalles de un error en el log de PHP (Solo producción)
     */
    private static function logError(string $code, string $message, array $data, int $httpStatusCode): void
    {
        if (self::getEnvironment() === 'production') {
            $logData = [
                'timestamp' => date('c'),
                'http_status' => $httpStatusCode,
                'api_code' => $code,
                'message' => $message,
                'request_data' => self::sanitizeData($_POST ?: []),
                'query_params' => self::sanitizeData($_GET ?: []),
                'server_data' => [
                    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                    'http_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                ],
                'caller' => self::getCallerInfo(),
                'error_details' => self::sanitizeData($data)
            ];

            error_log("[ApiResponse Error] " . json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR));
        }
    }

    /**
     * Sanitiza arrays de entrada para evitar almacenar datos sensibles en los archivos de log.
     */
    private static function sanitizeData(array $data): array
    {
        $sensitiveKeys = ['password', 'contrasena', 'password_hash', 'token', 'token_csrf', 'credit_card', 'cvv'];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::sanitizeData($value);
            } elseif (in_array(strtolower($key), $sensitiveKeys)) {
                $data[$key] = '********';
            }
        }
        return $data;
    }

    // ==================== MÉTODOS DE UTILIDAD PARA TESTING ====================

    public static function isSuccessResponse(array $response): bool
    {
        return isset($response['success']) && $response['success'] === true;
    }

    public static function getErrorCode(array $response): ?string
    {
        return self::isSuccessResponse($response) ? null : ($response['code'] ?? null);
    }

    public static function getResponseMessage(array $response): ?string
    {
        return $response['message'] ?? null;
    }

    public static function getResponseData(array $response): array
    {
        return isset($response['data']) && is_array($response['data']) ? $response['data'] : [];
    }
}
?>