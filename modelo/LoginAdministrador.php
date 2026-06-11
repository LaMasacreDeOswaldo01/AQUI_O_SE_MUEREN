<?php
include_once 'Conexion.php';

class LoginAdministrador {
    var $objetos;
    var $acceso;
    
    public function __construct() {
        $db = new Conexion();
        $this->acceso = $db->pdo;
    }
    
    function Loguearse($cedula, $pass) {
        $sql = "SELECT la.*, ra.nombre_administrador, ra.apellido_administrador, ra.administrador_tipo, tp.nombre_tipo
                FROM login_administrador la
                INNER JOIN registro_administrador ra ON la.id_administrador = ra.id_administrador
                INNER JOIN tipo_paciente tp ON ra.administrador_tipo = tp.id_tipo_us
                WHERE ra.cedula_administrador = :cedula AND la.status = 'activo'";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':cedula' => $cedula));
        $usuario = $query->fetch(PDO::FETCH_OBJ);
        
        if($usuario) {
           
            if(password_verify($pass, $usuario->password_hash)) {
                $this->objetos = array($usuario);
                return $this->objetos;
            }
        }
        
        $this->objetos = array();
        return $this->objetos;
    }
    
    function cambiar_contra($id_administrador, $old_pass, $newpass) {
        $sql = "SELECT password_hash FROM login_administrador WHERE id_administrador = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id_administrador));
        $usuario = $query->fetch(PDO::FETCH_OBJ);
        
        if($usuario && password_verify($old_pass, $usuario->password_hash)) {
            $new_hash = password_hash($newpass, PASSWORD_DEFAULT);
            $sql = "UPDATE login_administrador SET password_hash = :newpass WHERE id_administrador = :id";
            $query = $this->acceso->prepare($sql);
            $query->execute(array(':id' => $id_administrador, ':newpass' => $new_hash));
            echo 'update';
        } else {
            echo 'noupdate';
        }
    }
    
    function actualizarUltimoAcceso($id_administrador) {
        $sql = "UPDATE login_administrador SET ultimo_acceso = NOW() WHERE id_administrador = :id";
        $query = $this->acceso->prepare($sql);
        $query->execute(array(':id' => $id_administrador));
    }
}
?>