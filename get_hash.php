<?php
echo "<h2>Hash para '123456'</h2>";
echo "<pre>";
echo "Contraseña: 123456\n";
echo "Hash: " . password_hash('123456', PASSWORD_DEFAULT) . "\n";
echo "</pre>";
?>
