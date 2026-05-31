<?php

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $isProduction = (getenv('APP_ENV') === 'production');
    
    if ($isProduction) {        
        error_log("Error [$errno] $errstr en $errfile línea $errline");           
        if (!headers_sent()) {
            http_response_code(500);
            include_once VIEW_PATH . '/errors/500.php';
        }
        exit();
    } else {        
        return false;
    }
});

// Configurar manejador de excepciones
set_exception_handler(function($exception) {
    $isProduction = (getenv('APP_ENV') === 'production');
    
    if ($isProduction) {
        error_log("Excepción no capturada: " . $exception->getMessage());
        error_log($exception->getTraceAsString());
        
        if (!headers_sent()) {
            http_response_code(500);
            include_once VIEW_PATH . '/errors/500.php';
        }
        exit();
    } else {
        throw $exception;
    }
});

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $isProduction = (getenv('APP_ENV') === 'production');
        
        if ($isProduction) {
            error_log("Error fatal: {$error['message']} en {$error['file']} línea {$error['line']}");
            
            if (!headers_sent()) {
                http_response_code(500);
                include_once VIEW_PATH . '/errors/500.php';
            }
            exit();
        }
    }
});
