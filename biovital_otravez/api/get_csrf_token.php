<?php
session_start();

// Siempre regenerar token si no existe o si la sesión es nueva
if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expiry'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expiry'] = time() + 3600;
}

// Renovar si está por expirar
if (time() > $_SESSION['csrf_token_expiry'] - 600) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_expiry'] = time() + 3600;
}

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'csrf_token' => $_SESSION['csrf_token']
]);
?>