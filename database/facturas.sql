-- Tabla de facturas
CREATE TABLE IF NOT EXISTS facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(20) NOT NULL UNIQUE,
    id_cita INT NOT NULL,
    id_paciente INT NOT NULL,
    id_medico INT NOT NULL,
    fecha_emision DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_cita DATE NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    iva DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    estado ENUM('pendiente', 'pagada', 'cancelada') NOT NULL DEFAULT 'pendiente',
    forma_pago ENUM('efectivo', 'pago_movil', 'transferencia', 'tarjeta') NOT NULL DEFAULT 'pago_movil',
    referencia_pago VARCHAR(100) NULL,
    observaciones TEXT NULL,
    creado_por INT NOT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME NULL,
    modificado_por INT NULL,
    FOREIGN KEY (id_cita) REFERENCES citas(id_cita) ON DELETE CASCADE,
    FOREIGN KEY (id_paciente) REFERENCES registro_paciente(id_paciente) ON DELETE CASCADE,
    FOREIGN KEY (id_medico) REFERENCES registro_medico(id_medico) ON DELETE CASCADE,
    INDEX idx_numero_factura (numero_factura),
    INDEX idx_paciente (id_paciente),
    INDEX idx_medico (id_medico),
    INDEX idx_estado (estado),
    INDEX idx_fecha_emision (fecha_emision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalles de factura
CREATE TABLE IF NOT EXISTS factura_detalles (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (id_factura) REFERENCES facturas(id_factura) ON DELETE CASCADE,
    INDEX idx_factura (id_factura)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de auditoría de facturas
CREATE TABLE IF NOT EXISTS factura_auditoria (
    id_auditoria INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT NOT NULL,
    accion VARCHAR(50) NOT NULL COMMENT 'crear, editar, eliminar, pagar, cancelar',
    campo_modificado VARCHAR(100) NULL,
    valor_anterior TEXT NULL,
    valor_nuevo TEXT NULL,
    realizado_por INT NOT NULL,
    rol_usuario VARCHAR(50) NOT NULL,
    fecha_auditoria DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45) NULL,
    FOREIGN KEY (id_factura) REFERENCES facturas(id_factura) ON DELETE CASCADE,
    INDEX idx_factura (id_factura),
    INDEX idx_accion (accion),
    INDEX idx_fecha (fecha_auditoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de configuración de pago móvil
CREATE TABLE IF NOT EXISTS pago_movil_config (
    id_config INT AUTO_INCREMENT PRIMARY KEY,
    banco VARCHAR(100) NOT NULL,
    cedula_beneficiario VARCHAR(20) NOT NULL,
    telefono_beneficiario VARCHAR(20) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuración de pago móvil por defecto
INSERT INTO pago_movil_config (banco, cedula_beneficiario, telefono_beneficiario) 
VALUES ('Banco de Venezuela', '12345678', '0412-1234567')
ON DUPLICATE KEY UPDATE banco=VALUES(banco);
