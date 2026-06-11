<?php
include_once 'Conexion.php';

class LoginAsistente {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    function Loguearse($cedula, $pass) {
        $sql = "SELECT la.*, ra.nombre_asistente, ra.apellido_asistente, ra.asistente_tipo, tp.nombre_tipo
                FROM login_asistente la
                INNER JOIN registro_asistente ra ON la.id_asistente = ra.id_asistente
                INNER JOIN tipo_paciente tp ON ra.asistente_tipo = tp.id_tipo_us
                WHERE ra.cedula_asistente = :cedula AND la.status = 'activo'";
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
    
    function cambiar_contra($id_asistente, $old_pass, $newpass) {
        $sql = "SELECT password_hash FROM login_asistente WHERE id_asistente = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id_asistente));
        $usuario = $query->fetch(PDO::FETCH_OBJ);
        
        if($usuario && password_verify($old_pass, $usuario->password_hash)) {
            $new_hash = password_hash($newpass, PASSWORD_DEFAULT);
            $sql = "UPDATE login_asistente SET password_hash = :newpass WHERE id_asistente = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_asistente, ':newpass' => $new_hash));
            echo 'update';
        } else {
            echo 'noupdate';
        }
    }
    
    function actualizarUltimoAcceso($id_asistente) {
        $sql = "UPDATE login_asistente SET ultimo_acceso = NOW() WHERE id_asistente = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id_asistente));
    }
}
?>