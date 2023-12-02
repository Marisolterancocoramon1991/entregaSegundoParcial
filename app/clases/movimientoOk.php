<?php
include_once "./db/accesoDatos.php";

class movimientoExitoso{
    public $id;
    public $requestNombre;
    public $nombreUsuario;
    public $fecha;

    
    public function __construct()
    {

    }

    public function constructorParametros($requestNombre,$fecha,$nombreUsuario)
    {
        $this->requestNombre = $requestNombre;
        $this->fecha = $fecha;
        $this->nombreUsuario = $nombreUsuario;
    }

    public static function insertarMovimientoExitosoParametros($requestNombre,$nombreUsuario)
    {
        $objetoAccsesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccsesoDatos->RetornarConsulta("INSERT INTO `movimientoejecutadosconexito`(`operacion`, `nombreUsuario`, `fecha`) 
        VALUES (:requestNombre,:nombreUsuario , NOW());");

        $consulta->bindValue(':requestNombre', $requestNombre, PDO::PARAM_STR);
        $consulta->bindValue(':nombreUsuario', $nombreUsuario, PDO::PARAM_STR);

        $consulta->execute();
        return $objetoAccsesoDatos->RetornarUltimoIdInsertado();
    }
}
?>