<?php
// Configurar APP_URL si no está definido
if (!defined('APP_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = rtrim($protocol . $host . $scriptName, '/');
    define('APP_URL', $baseUrl);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del sistema | BioVital</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #0077b6 0%, #4e73df 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .error-container {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .error-logo {
            margin-bottom: 25px;
        }
        
        .error-logo img {
            height: 50px;
        }
        
        .error-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            box-shadow: 0 10px 25px rgba(239,68,68,0.3);
        }
        
        .error-icon i {
            font-size: 36px;
            color: white;
        }
        
        h1 { 
            font-size: 1.6rem; 
            font-weight: 700;
            color: #0f172a; 
            margin-bottom: 15px; 
        }
        
        p { 
            color: #64748b; 
            line-height: 1.6; 
            margin-bottom: 30px;
            font-size: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            background: linear-gradient(135deg, #0077b6, #4e73df);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,119,182,0.3);
        }
        
        .btn:hover { 
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,119,182,0.4);
        }
        
        .error-id {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 25px;
            font-family: monospace;
            background: #f1f5f9;
            padding: 8px 15px;
            border-radius: 8px;
            display: inline-block;
        }
        
        @media (max-width: 576px) {
            .error-container { padding: 35px 25px; }
            h1 { font-size: 1.4rem; }
            .error-icon { width: 70px; height: 70px; }
            .error-icon i { font-size: 30px; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-logo">
            <img src="<?php echo APP_URL; ?>/img/logo_azul.png" alt="BioVital">
        </div>
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>Algo salió mal</h1>
        <p>Lo sentimos, estamos experimentando problemas técnicos. Por favor, intenta nuevamente en unos momentos.</p>
        <a href="<?php echo APP_URL; ?>" class="btn">
            <i class="fas fa-home"></i> Volver al inicio
        </a>
        <?php if (isset($error_id)): ?>
        <div class="error-id">ID: <?php echo htmlspecialchars($error_id); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
