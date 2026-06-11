<?php

class Paciente {
    private $db;

    public function __construct() {
        // Accedemos a la conexión global que suele tener tu sistema
        // Si tu variable de conexión se llama $conexion o $conn, cámbialo abajo
        global $db; 
        
        if ($db === null) {
            // Esto es por si acaso la variable global no existe
            // Intenta buscar el nombre de tu variable de conexión en config.php
            throw new Exception("Error: La conexión a la base de datos no está disponible en el modelo.");
        }
        
        $this->db = $db;
    }

    /**
     * Método para insertar el paciente
     */
    public function registrar($datos) {
        $sql = "INSERT INTO paciente (
            cedula, nombre, apellido, correo, clave, telefono, 
            fecha_nacimiento, sexo, direccion, estado, ciudad, municipio, parroquia
        ) VALUES (
            :cedula, :nombre, :apellido, :correo, :clave, :telefono, 
            :fecha_nacimiento, :sexo, :direccion, :estado, :ciudad, :municipio, :parroquia
        )";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':cedula'           => $datos['cedula'],
            ':nombre'           => $datos['nombre'],
            ':apellido'         => $datos['apellido'],
            ':correo'           => $datos['correo'],
            ':clave'            => $datos['clave'],
            ':telefono'         => $datos['telefono'],
            ':fecha_nacimiento' => $datos['fecha_nacimiento'],
            ':sexo'             => $datos['sexo'],
            ':direccion'        => $datos['direccion'],
            ':estado'           => $datos['estado'],
            ':ciudad'           => $datos['ciudad'],
            ':municipio'        => $datos['municipio'],
            ':parroquia'        => $datos['parroquia']
        ]);
    }

    /**
     * Método para verificar si la cédula existe
     */
    public function existeCedula($cedula) {
        $sql = "SELECT count(*) FROM paciente WHERE cedula = :cedula";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cedula' => $cedula]);
        return (int)$stmt->fetchColumn() > 0;
    }
}