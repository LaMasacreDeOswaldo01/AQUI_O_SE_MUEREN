<?php
// controlador/PanelController.php - Versión actualizada con ViewHelper

class PanelController {
    
  public function paciente() {
    AuthHelper::checkRole('paciente', true);
    
    $options = [
        'title' => 'Panel del Paciente - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/paciente'],
            ['label' => 'Dashboard']
        ],
        'active_page' => 'dashboard',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
        'id_paciente' => $_SESSION['usuario'] ?? 0
    ];
    
    ViewHelper::renderDashboard('paciente/pac_catalogo', $data, $options);
}
    
    public function medico() {
    AuthHelper::checkRole('medico', true);
    
    $options = [
        'title' => 'Panel del Médico - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/medico'],
            ['label' => 'Dashboard']
        ],
        'active_page' => 'dashboard',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
        'id_medico' => $_SESSION['usuario'] ?? 0
    ];
    
    ViewHelper::renderDashboard('medico/med_catalogo', $data, $options);
}
    
   public function asistente() {
    AuthHelper::checkRole('asistente', true);
    
    $options = [
        'title' => 'Panel del Asistente - BioVital',
        'breadcrumbs' => [
            ['label' => 'Inicio', 'url' => APP_URL . '/panel/asistente'],
            ['label' => 'Dashboard']
        ],
        'active_page' => 'dashboard',
        'css' => '<link rel="stylesheet" href="' . APP_URL . '/css/dashboard-utils.css">'
    ];
    
    $data = [
        'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario',
        'id_asistente' => $_SESSION['usuario'] ?? 0
    ];
    
    ViewHelper::renderDashboard('asistente/asi_catalogo', $data, $options);
}
    
    public function administrador() {
        AuthHelper::checkRole('administrador', true);
        
        $options = [
            'title' => 'Panel de Administración',
            'breadcrumbs' => ViewHelper::generateBreadcrumbs('Dashboard'),
            'active_page' => 'dashboard'
        ];
        
        $data = [
            'nombre_usuario' => $_SESSION['nombre_us'] ?? 'Usuario'
        ];
        
        ViewHelper::renderDashboard('administrador/adm_catalogo', $data, $options);
    }
}