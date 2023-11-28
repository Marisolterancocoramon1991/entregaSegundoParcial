<?php
include_once "./db/accesoDatos.php";

class ajuste{
    public $id;
    public $numeroReserva;
    public $motivo;
    public $nuevoImporte;

    public function __construct()
    {
        
    }

    public function constructorParametros($numeroReserva,$motivo,$nuevoImporte)
    {
        $this->numeroReserva = $numeroReserva;
        $this->motivo = $motivo;
        $this->nuevoImporte = $nuevoImporte;
    }

    public function MostrarDatos(){
        return "Id {$this->id}<br>Numero de Reserva {$this->numeroReserva}<br>Motivo {$this->motivo}<br>Nuevo Importe {$this->nuevoImporte}";
    }

    public function InsertarAjuste()
    {
        $objetoAccsesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccsesoDatos->RetornarConsulta("INSERT INTO `ajustes`(`numeroReserva`, `motivo`, `nuevoImporte`) 
        VALUES ('$this->numeroReserva','$this->motivo', '$this->nuevoImporte')");
        $consulta->execute();
        return $objetoAccsesoDatos->RetornarUltimoIdInsertado();
    }

    public function insertarAjusteParametros()
    {
        $objetoAccsesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccsesoDatos->RetornarConsulta("INSERT INTO `ajustes`(`numeroReserva`, `motivo`, `nuevoImporte`) 
        VALUES (:numeroReserva, :motivo, :nuevoImporte)");

        $consulta->bindValue(':numeroReserva', $this->numeroReserva, PDO::PARAM_INT);
        $consulta->bindValue(':motivo', $this->motivo, PDO::PARAM_STR);
        $consulta->bindValue(':nuevoImporte', $this->nuevoImporte, PDO::PARAM_INT);
        $consulta->execute();
        return $objetoAccsesoDatos->RetornarUltimoIdInsertado();
    }

    public static function traerTodosLosAjustes()
    {
        $objetoAccsesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccsesoDatos->RetornarConsulta("SELECT * FROM `ajustes` ");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "ajuste");
    }

    public static function TraerUnAjuste($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM `ajustes` 
        WHERE id = $id ");
        $consulta->execute();
        $usuarioBuscado = $consulta->fetchObject("ajuste");
        return $usuarioBuscado;
    }

    public function modificarAjuste()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE `ajustes` 
        SET `numeroReserva`= '$this->numeroReserva',`motivo`='$this->motivo',`nuevoImporte`= '$this->nuevoImporte' 
        WHERE id = '$this->id'");
        return $consulta->execute();
    }

    public function modificarAjustesParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE `ajustes` 
        SET `numeroReserva`= :numeroReserva,`motivo`= :motivo,`nuevoImporte`= :nuevoImporte 
        WHERE id = :id ");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':numeroReserva', $this->numeroReserva, PDO::PARAM_INT);
        $consulta->bindValue(':motivo', $this->motivo, PDO::PARAM_STR);
        $consulta->bindValue(':nuevoImporte', $this->nuevoImporte, PDO::PARAM_INT);
        return $consulta->execute();
    }

    public function borrarAjuste()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("DELETE FROM `ajustes` 
        WHERE id = :id");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }
}
?>