<?php
include_once "./db/accesoDatos.php";

class movimiento{
    public $id;
    public $requestNombre;

    
    public function __construct()
    {

    }

    public function constructorParametros($requestNombre)
    {
        $this->requestNombre = $requestNombre;
    }

    public static function insertarMovimientoParametros($requestNombre,$nombreUsuario)
    {
        $objetoAccsesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccsesoDatos->RetornarConsulta("INSERT INTO `movimientos`(`request`, `nombreUsuario`) 
        VALUES (:requestNombre, :nombreUsuario)");

        $consulta->bindValue(':requestNombre', $requestNombre, PDO::PARAM_STR);
        $consulta->bindValue(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);

        $consulta->execute();
        return $objetoAccsesoDatos->RetornarUltimoIdInsertado();
    }
}
?>