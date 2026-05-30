<?php
// helpers/ViewHelper.php
// Helper para cargar vistas con layout unificado

class ViewHelper {
    
    /**
     * Renderiza una vista dentro del layout base del dashboard
     * 
     * @param string $view Nombre de la vista (sin extensión .php)
     * @param array $data Datos a pasar a la vista
     * @param array $options Opciones adicionales (titulo, breadcrumbs, scripts, css)
     */
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
    
    /**
     * Renderiza una vista simple (sin layout)
     * 
     * @param string $view Nombre de la vista
     * @param array $data Datos a pasar a la vista
     */
    public static function renderSimple($view, $data = []) {
        extract($data);
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("Vista no encontrada: {$view}");
        }
    }
    
    /**
     * Genera breadcrumbs a partir de la URL actual
     * 
     * @param string $current_title Título de la página actual
     * @return array Array de breadcrumbs
     */
    public static function generateBreadcrumbs($current_title) {
        $breadcrumbs = [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/' . AuthHelper::getCurrentRole()]
        ];
        
        // Aquí se puede implementar lógica para breadcrumbs más complejos
        // basados en la URL actual
        
        $breadcrumbs[] = ['label' => $current_title];
        return $breadcrumbs;
    }
    
    /**
     * Carga los scripts necesarios para el módulo de ubicación
     * 
     * @return string HTML con los scripts
     */
    public static function loadUbicacionScripts() {
        return '<script src="' . APP_URL . '/js/ubicacion.js"></script>';
    }
    
    /**
     * Carga los scripts necesarios para el módulo de consultorios
     * 
     * @return string HTML con los scripts
     */
    public static function loadConsultorioScripts() {
        return '<script src="' . APP_URL . '/js/consultorio.js"></script>';
    }
    
    /**
     * Carga los scripts necesarios para el módulo de especialidades
     * 
     * @return string HTML con los scripts
     */
    public static function loadEspecialidadScripts() {
        return '<script src="' . APP_URL . '/js/especialidades.js"></script>';
    }
    
    /**
     * Carga los scripts necesarios para el módulo de recetas
     * 
     * @return string HTML con los scripts
     */
    public static function loadRecetaScripts() {
        return '<script src="' . APP_URL . '/js/recetas.js"></script>';
    }
}
