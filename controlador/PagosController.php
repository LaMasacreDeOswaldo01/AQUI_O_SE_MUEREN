<?php
class PagosController {
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    public function index() {
        // Obtenemos los pagos pendientes para la vista del asistente
        $stmt = $this->db->query("SELECT * FROM pagos WHERE estado = 'por_confirmar'");
        $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'vista/asistente/asi_pagos.php';
    }

    public function procesar() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        $id = $data['id_pago'];
        $estado = ($data['accion'] === 'confirmar') ? 'confirmado' : 'rechazado';

        $stmt = $this->db->prepare("UPDATE pagos SET estado = :estado WHERE id_pago = :id");
        $success = $stmt->execute([':estado' => $estado, ':id' => $id]);

        echo json_encode(['success' => $success]);
        exit;
    }
}