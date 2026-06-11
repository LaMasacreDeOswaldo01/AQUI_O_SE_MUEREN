<?php
// scripts/check_environment.php
// Ejecutar desde terminal: php scripts/check_environment.php

echo "=== Verificación de Entorno BioVital ===\n\n";

// Verificar entorno
$env = getenv('APP_ENV') ?: 'development';
echo "Entorno actual: " . strtoupper($env) . "\n";

if ($env === 'production') {
    echo "✓ Configuración de PRODUCCIÓN\n";
    
    // Verificar que display_errors está desactivado
    if (ini_get('display_errors')) {
        echo "✗ ADVERTENCIA: display_errors está ACTIVADO en producción\n";
    } else {
        echo "✓ display_errors está desactivado\n";
    }
    
    // Verificar que log_errors está activado
    if (ini_get('log_errors')) {
        echo "✓ log_errors está activado\n";
        echo "  Log location: " . ini_get('error_log') . "\n";
    } else {
        echo "✗ ADVERTENCIA: log_errors está desactivado\n";
    }
    
    // Verificar permisos de logs
    $logDir = dirname(__DIR__) . '/logs';
    if (is_writable($logDir)) {
        echo "✓ Directorio de logs tiene permisos de escritura\n";
    } else {
        echo "✗ ADVERTENCIA: Directorio de logs no tiene permisos de escritura\n";
    }
    
} else {
    echo "! Modo DESARROLLO - Los errores son visibles\n";
}
