-- Agregar campo de tarifa fija a la tabla de médicos
ALTER TABLE registro_medico ADD COLUMN tarifa_consulta DECIMAL(10, 2) DEFAULT 50.00 COMMENT 'Tarifa fija por consulta en dólares';
