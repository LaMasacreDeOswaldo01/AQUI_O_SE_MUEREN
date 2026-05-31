<?php
include_once 'Conexion.php';

class LoginPaciente {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    function Loguearse($cedula, $pass) {
        // Primero obtener el hash de la base de datos
        $sql = "SELECT lp.*, rp.nombre_paciente, rp.apellido_paciente, rp.paciente_tipo, tp.nombre_tipo
                FROM login_paciente lp
                INNER JOIN registro_paciente rp ON lp.id_paciente = rp.id_paciente
                INNER JOIN tipo_paciente tp ON rp.paciente_tipo = tp.id_tipo_us
                WHERE rp.cedula_paciente = :cedula AND lp.status = 'activo'";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':cedula' => $cedula));
        $usuario = $query->fetch(PDO::FETCH_OBJ);
        
        if($usuario) {
            // 🔐 VERIFICAR CONTRASEÑA CON password_verify
            if(password_verify($pass, $usuario->password_hash)) {
                $this->objetos = array($usuario);
                return $this->objetos;
            }
        }
        
        $this->objetos = array();
        return $this->objetos;
    }
    
    function cambiar_contra($id_paciente, $old_pass, $newpass) {
        // Obtener el hash actual
        $sql = "SELECT password_hash FROM login_paciente WHERE id_paciente = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id_paciente));
        $usuario = $query->fetch(PDO::FETCH_OBJ);
        
        if($usuario && password_verify($old_pass, $usuario->password_hash)) {
            // 🔐 ENCRIPTAR NUEVA CONTRASEÑA
            $new_hash = password_hash($newpass, PASSWORD_DEFAULT);
            $sql = "UPDATE login_paciente SET password_hash = :newpass WHERE id_paciente = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_paciente, ':newpass' => $new_hash));
            echo 'update';
        } else {
            echo 'noupdate';
        }
    }
    
    function actualizarUltimoAcceso($id_paciente) {
        $sql = "UPDATE login_paciente SET ultimo_acceso = NOW() WHERE id_paciente = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id_paciente));
    }
}
?>