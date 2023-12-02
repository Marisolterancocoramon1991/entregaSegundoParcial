<?php

require_once "./clases/archivo.php";

class usuario {
    public $id;
    public $nombre;
    public $mail;
    public $clave;
    public $rol;

    public function __construct() {
       
    }

    
    public function constructParametros($nombre, $mail, $clave, $rol) {
        $this->nombre = $nombre;
        $this->clave = $clave;
        $this->mail = $mail;
        $this->rol = $rol; 
    }

    // Método para insertar un usuario en la base de datos
    public function insertarUsuarioParametros()
    {
        try {
            $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $consulta = $objetoAccesoDatos->RetornarConsulta("INSERT INTO usuarios(nombre, mail, clave, rol) 
            VALUES (:nombre, :mail, :clave, :rol)");
    
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
            $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
    
            $consulta->execute();
    
            return $objetoAccesoDatos->RetornarUltimoIdInsertado();
        } catch (PDOException $e) {
            // Manejo de errores
            echo 'Error al insertar usuario: ' . $e->getMessage();
            // Puedes registrar el error en un archivo de registro o manejarlo de otra manera según tus necesidades
            return false;
        }
    }

    public static function traerUsuarioMailClaveRol($mail, $clave,$rol)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM `usuarios` 
        WHERE mail = :mail AND clave = :clave AND rol = :rol");

        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $rol, PDO::PARAM_STR);

        $consulta->execute();
        $usuarioBuscado = $consulta->fetchObject("usuario");
        return $usuarioBuscado;
    }
    

} 
?>
