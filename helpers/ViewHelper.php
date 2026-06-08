<?php
class ViewHelper {
    public static function renderDashboard($view, $data = [], $options = []) {
        // Extraer opciones con valores por defecto
        $titulo_pagina = $options['title'] ?? 'BioVital - Panel';
        $breadcrumbs = $options['breadcrumbs'] ?? [];
        $css_extra = $options['css'] ?? '';
        $scripts_extra = $options['scripts'] ?? '';
        $active_page = $options['active_page'] ?? '';
        
        // Extraer datos para la vista
        extract($data);
        
        // Iniciar buffer para capturar el contenido de la vista
        ob_start();
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "<div class='alert alert-danger'>Vista no encontrada: {$view}</div>";
        }
        $content = ob_get_clean();
        
        // Renderizar el layout con el contenido
        require VIEW_PATH . '/layouts/dashboard.php';
    }
    
    public static function renderSimple($view, $data = []) {
        extract($data);
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("Vista no encontrada: {$view}");
        }
    }
    
    public static function generateBreadcrumbs($current_title) {
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/' . AuthHelper::getCurrentRole()]
        ];
        
        $breadcrumbs[] = ['label' => $current_title];
        return $breadcrumbs;
    }
    
    public static function loadUbicacionScripts() {
        return '<script src="' . APP_URL . '/js/ubicacion.js"></script>';
    }
    
    public static function loadConsultorioScripts() {
        return '<script src="' . APP_URL . '/js/consultorio.js"></script>';
    }
    
    public static function loadEspecialidadScripts() {
        return '<script src="' . APP_URL . '/js/especialidades.js"></script>';
    }
    
    public static function loadRecetaScripts() {
        return '<script src="' . APP_URL . '/js/recetas.js"></script>';
    }
}
