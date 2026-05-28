<?php
// vista/layouts/dashboard_head.php - VERSIÓN OPTIMIZADA
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $titulo_pagina ?? 'BioVital - Panel'; ?></title>

    <!-- Estilos globales -->
    <?php include_once VIEW_PATH . '/layouts/styles_head.php'; ?>
    
    <!-- Estilos específicos del dashboard -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/adminlte.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/dashboard.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">