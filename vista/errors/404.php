<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>404 - Página no encontrada</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .error-container {
            text-align: center;
            color: white;
            padding: 40px;
        }
        h1 {
            font-size: 120px;
            margin: 0;
            text-shadow: 4px 4px 0 rgba(0,0,0,0.2);
        }
        h2 {
            font-size: 32px;
            margin: 20px 0;
        }
        p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .btn-home {
            background: white;
            color: #667eea;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s;
            display: inline-block;
        }
        .btn-home:hover {
            transform: scale(1.05);
            color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <h2>¡Página no encontrada!</h2>
        <p>Lo sentimos, la página que buscas no existe o ha sido movida.</p>
        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>" class="btn-home">
            <i class="fas fa-home"></i> Volver al inicio
        </a>
    </div>
</body>
</html>