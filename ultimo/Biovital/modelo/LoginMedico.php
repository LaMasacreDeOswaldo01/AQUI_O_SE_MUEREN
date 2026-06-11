<?php
include_once 'Conexion.php';

class LoginMedico {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    function Loguearse($cedula, $pass) {
        $sql = "SELECT lm.*, rm.nombre_medico, rm.apellido_medico, rm.medico_tipo, tp.nombre_tipo
                FROM login_medico lm
                INNER JOIN registro_medico rm ON lm.id_medico = rm.id_medico
                INNER JOIN tipo_paciente tp ON rm.medico_tipo = tp.id_tipo_us
                WHERE rm.cedula_medico = :cedula AND lm.status = 'activo'";
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
    
    function cambiar_contra($id_medico, $old_pass, $newpass) {
        $sql = "SELECT password_hash FROM login_medico WHERE id_medico = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id_medico));
        $usuario = $query->fetch(PDO::FETCH_OBJ);
        
        if($usuario && password_verify($old_pass, $usuario->password_hash)) {
            $new_hash = password_hash($newpass, PASSWORD_DEFAULT);
            $sql = "UPDATE login_medico SET password_hash = :newpass WHERE id_medico = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_medico, ':newpass' => $new_hash));
            echo 'update';
        } else {
            echo 'noupdate';
        }
    }
    
    function actualizarUltimoAcceso($id_medico) {
        $sql = "UPDATE login_medico SET ultimo_acceso = NOW() WHERE id_medico = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id_medico));
    }
}
?>