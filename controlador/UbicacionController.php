<?php
/**
 * UbicacionController.php
 * Controlador para manejar peticiones de ubicación (estados, ciudades, municipios, parroquias)
 * Rutas: /api/ubicacion/estados, /api/ubicacion/ciudades, etc.
 */

class UbicacionController {
    
    private $consultorio;
    
    public function __construct() {
        // Usar rutas absolutas con constantes definidas en index.php
        $modelPath = dirname(__DIR__) . '/modelo/Consultorio.php';
        
        if (!file_exists($modelPath)) {
            error_log("ERROR: No se encuentra Consultorio.php en: " . $modelPath);
            jsonResponse(['error' => 'Error interno del servidor'], 500);
            return;
        }
        
        require_once $modelPath;
        $this->consultorio = new Consultorio();
    }
    
    /**
     * Listar todos los estados de Venezuela
     * POST /api/ubicacion/estados
     */
    public function listarEstados() {
        try {
            $estados = $this->consultorio->listarEstados();
            jsonResponse($estados);
        } catch(Exception $e) {
            error_log("Error en listarEstados: " . $e->getMessage());
            jsonResponse(['error' => 'Error al cargar estados'], 500);
        }
    }
    
    /**
     * Listar ciudades por estado
     * POST /api/ubicacion/ciudades
     * Datos requeridos: id_estado
     */
    public function listarCiudades() {
        try {
            $id_estado = isset($_POST['id_estado']) ? intval($_POST['id_estado']) : 0;
            
            if ($id_estado <= 0) {
                jsonResponse(['error' => 'ID de estado no válido'], 400);
                return;
            }
            
            $ciudades = $this->consultorio->listarCiudades($id_estado);
            jsonResponse($ciudades);
        } catch(Exception $e) {
            error_log("Error en listarCiudades: " . $e->getMessage());
            jsonResponse(['error' => 'Error al cargar ciudades'], 500);
        }
    }
    
    /**
     * Listar municipios por estado
     * POST /api/ubicacion/municipios
     * Datos requeridos: id_estado
     */
    public function listarMunicipios() {
        try {
            $id_estado = isset($_POST['id_estado']) ? intval($_POST['id_estado']) : 0;
            
            if ($id_estado <= 0) {
                jsonResponse(['error' => 'ID de estado no válido'], 400);
                return;
            }
            
            $municipios = $this->consultorio->listarMunicipios($id_estado);
            jsonResponse($municipios);
        } catch(Exception $e) {
            error_log("Error en listarMunicipios: " . $e->getMessage());
            jsonResponse(['error' => 'Error al cargar municipios'], 500);
        }
    }
    
    /**
     * Listar parroquias por municipio
     * POST /api/ubicacion/parroquias
     * Datos requeridos: id_municipio
     */
    public function listarParroquias() {
        try {
            $id_municipio = isset($_POST['id_municipio']) ? intval($_POST['id_municipio']) : 0;
            
            if ($id_municipio <= 0) {
                jsonResponse(['error' => 'ID de municipio no válido'], 400);
                return;
            }
            
            $parroquias = $this->consultorio->listarParroquias($id_municipio);
            jsonResponse($parroquias);
        } catch(Exception $e) {
            error_log("Error en listarParroquias: " . $e->getMessage());
            jsonResponse(['error' => 'Error al cargar parroquias'], 500);
        }
    }
    
    /**
     * Listar especialidades médicas disponibles
     * POST /api/ubicacion/especialidades
     */
    public function listaEspecialidades() {
        try {
            $especialidades = $this->consultorio->obtenerListaEspecialidades();
            jsonResponse($especialidades);
        } catch(Exception $e) {
            error_log("Error en listaEspecialidades: " . $e->getMessage());
            jsonResponse(['error' => 'Error al cargar especialidades'], 500);
        }
    }
}
?>