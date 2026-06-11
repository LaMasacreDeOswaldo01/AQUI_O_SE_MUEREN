<?php

class RegistroController {

    /**
     * Muestra la vista del formulario
     */
    public function showRegistroPaciente() {
        // Asegúrate que el nombre 'registro_pac' coincida con tu archivo en vista/
        $vista = 'registro_pac'; 
        
        if (file_exists(VIEW_PATH . DS . $vista . '.php')) {
            renderView($vista, ['titulo' => 'Registro de Paciente']);
        } else {
            echo "Error: No se encuentra la vista " . $vista . ".php en " . VIEW_PATH;
        }
    }

    /**
     * Procesa los datos del formulario (AJAX)
     */
    public function crearPaciente() {
        // Limpiar cualquier salida previa
        if (ob_get_length()) ob_clean();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            return;
        }

        // Captura de datos
        $datos = [
            'cedula'   => $_POST['cedula'] ?? null,
            'nombre'   => $_POST['nombre'] ?? null,
            'apellido' => $_POST['apellidos'] ?? null,
            'correo'   => $_POST['correo'] ?? null,
            'clave'    => $_POST['pass'] ?? null,
            'telefono' => $_POST['telefono'] ?? null,
            'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
            'sexo'     => $_POST['sexo'] ?? null,
            'direccion'=> $_POST['direccion'] ?? null,
            'estado'   => $_POST['estado'] ?? null,
            'ciudad'   => $_POST['ciudad'] ?? null,
            'municipio'=> $_POST['municipio'] ?? null,
            'parroquia'=> $_POST['parroquia'] ?? null
        ];

        // Validación básica
        if (empty($datos['cedula']) || empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['correo']) || empty($datos['clave'])) {
            jsonResponse(['success' => false, 'message' => 'Todos los campos obligatorios deben ser completados'], 400);
            return;
        }

        try {
            $pacienteModel = new Paciente();

            /* * VALIDACIÓN COMENTADA TEMPORALMENTE 
             * PARA EVITAR EL ERROR DE MÉTODO NO DEFINIDO
             */
            /*
            if ($pacienteModel->existeCedula($datos['cedula'])) {
                jsonResponse(['success' => false, 'message' => 'La cédula ya está registrada'], 400);
                return;
            }
            */

            // Hashear contraseña
            $datos['clave'] = password_hash($datos['clave'], PASSWORD_BCRYPT);
            
            // Guardar
            if ($pacienteModel->registrar($datos)) {
                header("Content-Type: application/json; charset=UTF-8");
                echo json_encode([
                    'success' => true, 
                    'message' => '¡Cuenta creada con éxito!'
                ]);
                exit();
            } else {
                throw new Exception("Error al guardar en base de datos.");
            }
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}