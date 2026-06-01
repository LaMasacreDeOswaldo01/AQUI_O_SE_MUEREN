-- Tabla de facturas
CREATE TABLE IF NOT EXISTS facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(20) NOT NULL UNIQUE,
    id_cita INT NOT NULL,
    id_paciente INT NOT NULL,
    id_medico INT NOT NULL,
    fecha_emision DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'pagada', 'cancelada') NOT NULL DEFAULT 'pendiente',
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    iva DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    referencia_pago VARCHAR(100) NULL,
    fecha_pago DATETIME NULL,
    metodo_pago VARCHAR(50) NULL,
    modificado_por INT NULL,
    fecha_modificacion DATETIME NULL,
    datos_clinica JSON NULL,
    datos_beneficiario JSON NULL,
    detalles_factura JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_cita) REFERENCES citas(id_cita) ON DELETE CASCADE,
    FOREIGN KEY (id_paciente) REFERENCES registro_paciente(id_paciente) ON DELETE CASCADE,
    FOREIGN KEY (id_medico) REFERENCES registro_medico(id_medico) ON DELETE CASCADE,
    
    INDEX idx_numero_factura (numero_factura),
    INDEX idx_id_paciente (id_paciente),
    INDEX idx_id_medico (id_medico),
    INDEX idx_estado (estado),
    INDEX idx_fecha_emision (fecha_emision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de la clínica por defecto (se pueden actualizar)
INSERT INTO facturas (numero_factura, id_cita, id_paciente, id_medico, datos_clinica, datos_beneficiario)
VALUES ('FACT-0000', 0, 0, 0, 
    '{"nombre": "BioVital Clínica", "direccion": "Av. Principal, Edificio Médico", "telefono": "+58-212-1234567", "email": "contacto@biovital.com"}',
    '{"banco": "Banco de Venezuela", "celular": "0412-1234567", "cedula": "12345678", "tipo_cuenta": "Ahorros"}'
) ON DUPLICATE KEY UPDATE numero_factura = numero_factura;
