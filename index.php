<?php
// ==================== FRONT CONTROLLER ====================

// Detectar entorno
$environment = getenv('APP_ENV') ?: 'development';
$isProduction = ($environment === 'production');

// Configuración de errores (ANTES de cualquier otra cosa)
if ($isProduction) {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    
    $logDir = dirname(__FILE__) . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    ini_set('error_log', $logDir . '/php_errors.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
}

// Incluir configuración de errores
require_once __DIR__ . '/config/errors.php';

// Iniciar sesión SOLO UNA VEZ al principio
if (session_status() === PHP_SESSION_NONE) {
    if ($isProduction) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', 1);
    }
    session_start();
}

require_once 'config/app.php';

// Constantes de rutas
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__FILE__));
define('CONTROLLER_PATH', ROOT_PATH . DS . 'controlador');
define('MODEL_PATH', ROOT_PATH . DS . 'modelo');
define('VIEW_PATH', ROOT_PATH . DS . 'vista');
define('API_PATH', ROOT_PATH . DS . 'api');
define('IMG_PATH', ROOT_PATH . DS . 'img');
define('HELPER_PATH', ROOT_PATH . DS . 'helpers');

// ==================== CARGAR HELPERS ====================
// Cargar helpers necesarios en orden
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

// Autoloader
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
    
    // Buscar en modelos
    $modelFile = MODEL_PATH . DS . $className . '.php';
    if (file_exists($modelFile)) {
        require_once $modelFile;
        return;
    }
    
    // Buscar en controladores
    $controllerFile = CONTROLLER_PATH . DS . $className . '.php';
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        return;
    }
});

// ==================== FUNCIONES AUXILIARES ====================

/**
 * Envía una respuesta JSON estándar
 */
function jsonResponse($data, $statusCode = 200) {
    if (class_exists('ApiResponse')) {
        ApiResponse::send(true, ApiResponse::CODE_SUCCESS, 'Operación exitosa', $data, $statusCode);
    } else {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

/**
 * Redirige a una URL relativa al APP_URL
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
 * Renderiza una vista simple (sin layout)
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
 * Verifica si la petición es AJAX
 */
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

// ==================== ROUTER CON PARÁMETROS DINÁMICOS ====================
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$path = str_replace($scriptName, '', $requestUri);
$path = trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'];
$isAjax = isAjax();

// Verificar si las rutas existen
$routesFile = ROOT_PATH . DS . 'config' . DS . 'routes.php';
if (!file_exists($routesFile)) {
    die("Error: Archivo de rutas no encontrado en: " . $routesFile);
}

$routes = require_once $routesFile;

/**
 * Verifica si el usuario tiene el rol requerido
 */
function verificarRol($rolRequerido, $rolUsuario) {
    if (is_array($rolRequerido)) {
        return in_array($rolUsuario, $rolRequerido);
    }
    return $rolRequerido === $rolUsuario;
}

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
        if (strpos($routeParts[$i], ':') === 0) {
            $paramName = substr($routeParts[$i], 1);
            $params[$paramName] = $pathParts[$i];
            $_GET[$paramName] = $pathParts[$i];
        } 
        elseif ($routeParts[$i] !== $pathParts[$i]) {
            $match = false;
            break;
        }
    }
    
    if ($match) {
        $routeFound = true;
        $routeParams = $params;
        
        // Detectar si es una ruta de API (empieza con 'api/')
        $isApiRoute = (strpos($route, 'api/') === 0);
        
        // Verificar método HTTP
        if (isset($config['method']) && $config['method'] !== $method) {
            if ($isApiRoute || $isAjax) {
                if (class_exists('ApiResponse')) {
                    ApiResponse::error('Método no permitido', ApiResponse::CODE_SERVER_ERROR, [], 405);
                } else {
                    http_response_code(405);
                    die("Método no permitido");
                }
            } else {
                http_response_code(405);
                die("Método no permitido");
            }
        }
        
        // Verificar autenticación
        if (isset($config['auth']) && $config['auth'] === true) {
            if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
                if ($isApiRoute || $isAjax) {
                    if (class_exists('ApiResponse')) {
                        ApiResponse::unauthorized('Debe iniciar sesión para acceder a este recurso');
                    } else {
                        http_response_code(401);
                        die("No autorizado");
                    }
                } else {
                    redirect('login');
                }
            }
        }
        
        // Verificar rol
        if (isset($config['rol'])) {
            if (!isset($_SESSION['rol'])) {
                if ($isApiRoute || $isAjax) {
                    if (class_exists('ApiResponse')) {
                        ApiResponse::forbidden('No tiene permisos para acceder a este recurso');
                    } else {
                        http_response_code(403);
                        die("Prohibido");
                    }
                } else {
                    redirect('login');
                }
            }
            if (!verificarRol($config['rol'], $_SESSION['rol'])) {
                if ($isApiRoute || $isAjax) {
                    if (class_exists('ApiResponse')) {
                        ApiResponse::forbidden('Rol no autorizado para acceder a este recurso');
                    } else {
                        http_response_code(403);
                        die("Prohibido");
                    }
                } else {
                    redirect('login');
                }
            }
        }
        
        $controllerName = $config['controller'];
        $actionName = $config['action'];
        $controllerFile = CONTROLLER_PATH . DS . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            if ($isApiRoute || $isAjax) {
                if (class_exists('ApiResponse')) {
                    ApiResponse::serverError("Archivo de controlador no encontrado: {$controllerName}");
                } else {
                    http_response_code(500);
                    die("Error interno");
                }
            } else {
                die("Error: Archivo de controlador no encontrado: {$controllerName}");
            }
        }
        
        require_once $controllerFile;
        
        if (!class_exists($controllerName)) {
            if ($isApiRoute || $isAjax) {
                if (class_exists('ApiResponse')) {
                    ApiResponse::serverError("Controlador '{$controllerName}' no encontrado");
                } else {
                    http_response_code(500);
                    die("Error interno");
                }
            } else {
                die("Error: Controlador '{$controllerName}' no encontrado");
            }
        }
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $actionName)) {
            if ($isApiRoute || $isAjax) {
                if (class_exists('ApiResponse')) {
                    ApiResponse::serverError("Acción '{$actionName}' no encontrada en {$controllerName}");
                } else {
                    http_response_code(500);
                    die("Error interno");
                }
            } else {
                die("Error: Acción '{$actionName}' no encontrada en {$controllerName}");
            }
        }
        
        // Iniciar buffer SOLO para rutas que NO son API
        // Las rutas API ya manejan su propia salida con ApiResponse
        if (!$isApiRoute) {
            ob_start();
        }
        
        // Ejecutar el controlador
        $controller->$actionName();
        
        // Si es una ruta que NO es API, capturar y mostrar la salida normalmente
        if (!$isApiRoute) {
            $output = ob_get_clean();
            
            // Verificar si el controlador ya envió headers (por ejemplo, con redirect)
            if (!headers_sent()) {
                // Si no hay salida, mostrar un mensaje por defecto
                if (empty($output)) {
                    echo "Vista renderizada correctamente.";
                } else {
                    echo $output;
                }
            }
        }
        
        break;
    }
}

// ==================== MANEJO DE RUTAS NO ENCONTRADAS ====================
if (!$routeFound) {
    if ($isAjax) {
        if (class_exists('ApiResponse')) {
            ApiResponse::notFound("Ruta: {$path}");
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Ruta no encontrada']);
        }
    } else {
        http_response_code(404);
        if (file_exists(VIEW_PATH . DS . 'errors' . DS . '404.php')) {
            renderView('errors/404');
        } else {
            echo "<!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <title>404 - Página no encontrada</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                    h1 { font-size: 48px; color: #dc3545; }
                    p { font-size: 18px; color: #666; }
                </style>
            </head>
            <body>
                <h1>404</h1>
                <p>La página que buscas no existe.</p>
                <p><a href='" . APP_URL . "'>Volver al inicio</a></p>
            </body>
            </html>";
        }
    }
    exit();
}