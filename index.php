<?php
// =========================================================================
// FRONT CONTROLLER (VERSIÓN DEFINITIVA, BLINDADA Y 100% CORREGIDA)
// =========================================================================

// 1. DETECTAR ENTORNO
$environment = getenv('APP_ENV') ?: 'development';
$isProduction = ($environment === 'production');

// 2. CONFIGURACIÓN DE ERRORES Y SESIONES (Antes de cualquier salida de texto)
if ($isProduction) {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    ini_set('error_log', $logDir . '/php_errors.log');
    
    // Blindaje de cookies de sesión para Producción
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
}

// Inicializar sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. CARGAR CONFIGURACIONES BASE
require_once __DIR__ . '/config/errors.php';
require_once __DIR__ . '/config/app.php';

// 4. CONSTANTES DE RUTAS INDEPENDIENTES DEL SITEMA OPERATIVO
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', __DIR__);
define('CONTROLLER_PATH', ROOT_PATH . DS . 'controlador');
define('MODEL_PATH', ROOT_PATH . DS . 'modelo');
define('VIEW_PATH', ROOT_PATH . DS . 'vista');
define('API_PATH', ROOT_PATH . DS . 'api');
define('IMG_PATH', ROOT_PATH . DS . 'img');
define('HELPER_PATH', ROOT_PATH . DS . 'helpers');

// ==================== CARGAR HELPERS ====================
$helpers = ['AuthHelper.php', 'ApiResponse.php', 'ViewHelper.php'];

foreach ($helpers as $helper) {
    $helperPath = HELPER_PATH . DS . $helper;
    if (file_exists($helperPath)) {
        require_once $helperPath;
    } else {
        error_log("Helper no encontrado: " . $helperPath);
        if (!$isProduction) {
            echo "Advertencia: Helper no encontrado: {$helper}<br>";
        }
    }
}

// ==================== AUTOLOADER OPTIMIZADO ====================
spl_autoload_register(function($className) {
    $classMap = [
        'Security' => MODEL_PATH . DS . 'Security.php',
        'Conexion' => MODEL_PATH . DS . 'Conexion.php',
        'Paciente' => MODEL_PATH . DS . 'Paciente.php',
        'Medico' => MODEL_PATH . DS . 'Medico.php',
        'Asistente' => MODEL_PATH . DS . 'Asistente.php',
        'Administrador' => MODEL_PATH . DS . 'Administrador.php',
        'Consultorio' => MODEL_PATH . DS . 'Consultorio.php',
        'Especialidad' => MODEL_PATH . DS . 'Especialidad.php',
        'Receta' => MODEL_PATH . DS . 'Receta.php',
        'LoginPaciente' => MODEL_PATH . DS . 'LoginPaciente.php',
        'LoginMedico' => MODEL_PATH . DS . 'LoginMedico.php',
        'LoginAsistente' => MODEL_PATH . DS . 'LoginAsistente.php',
        'LoginAdministrador' => MODEL_PATH . DS . 'LoginAdministrador.php'
    ];
    
    if (isset($classMap[$className])) {
        require_once $classMap[$className];
        return;
    }
    
    if (file_exists($modelFile = MODEL_PATH . DS . $className . '.php')) {
        require_once $modelFile;
        return;
    }
    
    if (file_exists($controllerFile = CONTROLLER_PATH . DS . $className . '.php')) {
        require_once $controllerFile;
        return;
    }
});

// ==================== FUNCIONES AUXILIARES GLOBALES ====================

/**
 * Envía una respuesta JSON estándar o delegada a ApiResponse
 */
function jsonResponse($data, $statusCode = 200) {
    if (class_exists('ApiResponse')) {
        ApiResponse::send(true, ApiResponse::CODE_SUCCESS, 'Operación exitosa', $data, $statusCode);
    } else {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
}

/**
 * Redirige de forma segura a una URL interna de la app
 */
function redirect($url, $permanent = false) {
    if ($permanent) {
        header('HTTP/1.1 301 Moved Permanently');
    }
    $redirectUrl = APP_URL . '/' . ltrim($url, '/');
    header('Location: ' . $redirectUrl);
    exit();
}

/**
 * Renderiza una vista aislada inyectando parámetros dinámicos
 */
function renderView($view, $data = []) {
    extract($data);
    $viewFile = VIEW_PATH . DS . $view . '.php';
    if (file_exists($viewFile)) {
        require_once $viewFile;
    } else {
        die("Vista no encontrada: {$view}");
    }
}

/**
 * Detecta de forma estricta peticiones AJAX asíncronas
 */
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Valida si el rol de la sesión cumple con los accesos requeridos
 */
function verificarRol($rolRequerido, $rolUsuario) {
    if (is_array($rolRequerido)) {
        return in_array($rolUsuario, $rolRequerido);
    }
    return $rolRequerido === $rolUsuario;
}

// ==================== ROUTER INTELIGENTE ====================
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);

// Separar el path limpio de los parámetros string (?url=...)
$uriParts = parse_url($requestUri);
$path = $uriParts['path'] ?? $requestUri;
$path = str_replace($scriptName, '', $path);
$path = trim($path, '/');

$method = $_SERVER['REQUEST_METHOD'];
$isAjax = isAjax();

$routesFile = ROOT_PATH . DS . 'config' . DS . 'routes.php';
if (!file_exists($routesFile)) {
    die("Error: Archivo de rutas no encontrado en: " . $routesFile);
}

$routes = require_once $routesFile;

$routeFound = false;
$routeParams = [];
$isApiRoute = false;
$pathParts = explode('/', $path);

foreach ($routes as $route => $config) {
    $routeParts = explode('/', $route);
    
    if (count($routeParts) !== count($pathParts)) {
        continue;
    }
    
    $match = true;
    $params = [];
    
    for ($i = 0; $i < count($routeParts); $i++) {
        // Detectar token dinámico (Ej: :id, :slug)
        if (strpos($routeParts[$i], ':') === 0) {
            $paramName = substr($routeParts[$i], 1);
            $params[$paramName] = $pathParts[$i];
            $_GET[$paramName] = $pathParts[$i]; // Compatibilidad con $_GET tradicional
        } 
        elseif ($routeParts[$i] !== $pathParts[$i]) {
            $match = false;
            break;
        }
    }
    
    if ($match) {
        $routeFound = true;
        $routeParams = $params;
        $isApiRoute = (strpos($route, 'api/') === 0);
        
        // 1. VALIDAR MÉTODO HTTP
        if (isset($config['method']) && $config['method'] !== $method) {
            http_response_code(405);
            if ($isApiRoute || $isAjax) {
                if (class_exists('ApiResponse')) {
                    ApiResponse::error('Método no permitido', ApiResponse::CODE_SERVER_ERROR, [], 405);
                } else {
                    echo json_encode(['error' => 'Método HTTP no permitido']);
                }
                exit();
            }
            die("Método no permitido");
        }
        
        // 2. VALIDAR AUTENTICACIÓN REQUERIDA
        if (isset($config['auth']) && $config['auth'] === true) {
            if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
                if ($isApiRoute || $isAjax) {
                    if (class_exists('ApiResponse')) {
                        ApiResponse::unauthorized('Debe iniciar sesión para acceder');
                    } else {
                        http_response_code(401);
                        echo json_encode(['error' => 'No autorizado']);
                    }
                    exit();
                }
                redirect('login');
            }
        }
        
        // 3. VALIDAR ROLES Y PERMISOS
        if (isset($config['rol'])) {
            if (!isset($_SESSION['rol']) || !verificarRol($config['rol'], $_SESSION['rol'])) {
                if ($isApiRoute || $isAjax) {
                    if (class_exists('ApiResponse')) {
                        ApiResponse::forbidden('No tiene permisos para este recurso');
                    } else {
                        http_response_code(403);
                        echo json_encode(['error' => 'Acceso prohibido']);
                    }
                    exit();
                }
                redirect('login');
            }
        }
        
        // 4. INSTANCIACIÓN DEL CONTROLADOR Y ACCIÓN
        $controllerName = $config['controller'];
        $actionName = $config['action'];
        $controllerFile = CONTROLLER_PATH . DS . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            die("Error interno: No se encuentra el archivo del controlador '{$controllerName}'");
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName) || !method_exists($controllerName, $actionName)) {
            http_response_code(500);
            die("Error interno: La acción '{$actionName}' no existe en el controlador '{$controllerName}'");
        }
        
        $controller = new $controllerName();

        // 5. CONTROL SEGURO DE BUFFERS Y EJECUCIÓN (CON PARÁMETROS)
        if (!$isApiRoute) {
            ob_start();
        }

        try {
            // ENVIAR PARÁMETROS DINÁMICOS AL MÉTODO DEL CONTROLADOR SI EXISTEN
            if (!empty($routeParams)) {
                call_user_func_array([$controller, $actionName], array_values($routeParams));
            } else {
                $controller->$actionName();
            }
        } catch (\Throwable $e) {
            // Si la aplicación falla internamente, limpiamos el buffer para no romper la pantalla
            if (!$isApiRoute) {
                ob_end_clean();
            }
            throw $e; // Re-lanzamos el error para que config/errors.php lo procese
        }

        // 6. PROCESAR SALIDA DE RUTAS WEB
        if (!$isApiRoute) {
            $output = ob_get_clean();
            if (!headers_sent()) {
                if (empty($output)) {
                    // Evita pantallas en blanco si olvidaste hacer un echo/render en tu controlador
                    error_log("Aviso: El controlador {$controllerName}::{$actionName} no generó contenido visible.");
                } else {
                    echo $output;
                }
            }
        }

        // Importante: Terminamos el ciclo ya que la ruta fue resuelta con éxito
        break;
    }
}

// ==================== MANEJO DE RUTAS NO ENCONTRADAS (404) ====================
if (!$routeFound) {
    http_response_code(404);
    
    if ($isAjax) {
        if (class_exists('ApiResponse')) {
            ApiResponse::notFound("Ruta no encontrada: {$path}");
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Ruta no encontrada']);
        }
    } else {
        if (file_exists(VIEW_PATH . DS . 'errors' . DS . '404.php')) {
            renderView('errors/404');
        } else {
            echo "<!DOCTYPE html>
            <html lang='es'>
            <head>
                <meta charset='UTF-8'>
                <title>404 - Página no encontrada</title>
                <style>
                    body { font-family: 'Segoe UI', Arial, sans-serif; text-align: center; padding: 100px 20px; background: #f8f9fa; color: #333; }
                    h1 { font-size: 64px; color: #dc3545; margin: 0; }
                    p { font-size: 20px; color: #6c757d; }
                    a { color: #007bff; text-decoration: none; font-weight: bold; }
                    a:hover { text-decoration: underline; }
                </style>
            </head>
            <body>
                <h1>404</h1>
                <p>La página que estás buscando no existe o ha sido movida.</p>
                <p><a href='" . (defined('APP_URL') ? APP_URL : '#') . "'>Volver al panel principal</a></p>
            </body>
            </html>";
        }
    }
    exit();
}
