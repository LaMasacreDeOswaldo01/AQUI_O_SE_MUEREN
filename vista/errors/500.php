<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del sistema - BioVital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        h1 { font-size: 28px; color: #333; margin-bottom: 15px; }
        p { color: #666; line-height: 1.6; margin-bottom: 25px; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: transform 0.3s;
        }
        .btn:hover { transform: translateY(-2px); }
        .error-id {
            font-size: 12px;
            color: #999;
            margin-top: 20px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>Algo salió mal</h1>
        <p>Lo sentimos, estamos experimentando problemas técnicos. Por favor, intenta nuevamente en unos momentos.</p>
        <a href="<?php echo APP_URL; ?>" class="btn">
            <i class="fas fa-home"></i> Volver al inicio
        </a>
        <?php if (isset($error_id)): ?>
        <div class="error-id">ID de referencia: <?php echo htmlspecialchars($error_id); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
