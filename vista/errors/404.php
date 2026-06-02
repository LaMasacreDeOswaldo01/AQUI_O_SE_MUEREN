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
    <title>404 - Página no encontrada | BioVital</title>
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
            margin: 0;
            padding: 20px;
        }
        
        .error-container {
            text-align: center;
            color: white;
            padding: 40px;
            max-width: 500px;
        }
        
        .error-logo {
            margin-bottom: 30px;
        }
        
        .error-logo img {
            height: 60px;
            opacity: 0.9;
        }
        
        h1 {
            font-size: 120px;
            font-weight: 800;
            margin: 0;
            text-shadow: 4px 4px 0 rgba(0,0,0,0.15);
            line-height: 1;
        }
        
        h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 20px 0 15px;
        }
        
        p {
            font-size: 1rem;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .btn-home {
            background: white;
            color: #0077b6;
            padding: 14px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            color: #023e8a;
        }
        
        .btn-home i {
            font-size: 1.1rem;
        }
        
        @media (max-width: 576px) {
            h1 { font-size: 80px; }
            h2 { font-size: 1.4rem; }
            .error-container { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-logo">
            <img src="<?php echo APP_URL; ?>/img/logo_blanco.png" alt="BioVital">
        </div>
        <h1>404</h1>
        <h2>¡Página no encontrada!</h2>
        <p>Lo sentimos, la página que buscas no existe o ha sido movida.</p>
        <a href="<?php echo APP_URL; ?>" class="btn-home">
            <i class="fas fa-home"></i> Volver al inicio
        </a>
    </div>
</body>
</html>