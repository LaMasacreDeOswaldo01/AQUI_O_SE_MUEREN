<?php

class AlertaController {
    private $alerta;

    public function __construct() {
        AuthHelper::checkRole(['medico', 'administrador'], true);
        $this->alerta = new Alerta();
    }

    public function listar() {
        $alertas = $this->alerta->listar();
        ApiResponse::success($alertas, 'alertas_listadas');
    }

    public function registrar() {
        if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            ApiResponse::csrfError();
            return;
        }

        $datos = [
            'tipo_amenaza' => trim($_POST['tipo_amenaza'] ?? ''),
            'nombre_paciente' => trim($_POST['nombre_paciente'] ?? ''),
            'cedula_paciente' => trim($_POST['cedula_paciente'] ?? ''),
            'nivel_riesgo' => $_POST['nivel_riesgo'] ?? 'Bajo',
            'descripcion_breve' => trim($_POST['descripcion_breve'] ?? ''),
            'id_medico' => $_SESSION['usuario']
        ];

        if (empty($datos['tipo_amenaza']) || empty($datos['cedula_paciente'])) {
            ApiResponse::error('Datos incompletos', ApiResponse::CODE_VALIDATION_ERROR);
            return;
        }

        if ($this->alerta->registrar($datos)) {
            ApiResponse::created([], 'Alerta publicada correctamente');
        } else {
            ApiResponse::serverError();
        }
    }

    public function eliminar() {
        $id = $_POST['id'] ?? 0;
        
        if (!Security::verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            ApiResponse::csrfError();
            return;
        }

        if ($this->alerta->eliminar($id)) {
            ApiResponse::success([], 'deleted', 'Alerta eliminada');
        } else {
            ApiResponse::error('No se pudo eliminar');
        }
    }
}