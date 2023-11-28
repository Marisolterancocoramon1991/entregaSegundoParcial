<?php

use Slim\Psr7\Response;

//require_once "./clases/archivos.php";
require_once "./clases/ajustes.php";

class ajusteController{
    public function InsertarAjuste($numeroReserva,$motivo,$nuevoImporte)
    {
        $ajuste = new ajuste();
        $ajuste->numeroReserva = $numeroReserva;
        $ajuste->motivo = $motivo;
        $ajuste->nuevoImporte = $nuevoImporte;

        return $ajuste->insertarAjusteParametros();
    }

    public function modificarAjuste($id,$numeroReserva,$motivo,$nuevoImporte)
    {
        $ajuste = new ajuste();
        $ajuste->id = $id;
        $ajuste->numeroReserva = $numeroReserva;
        $ajuste->motivo = $motivo;
        $ajuste->nuevoImporte = $nuevoImporte;

        return $ajuste->modificarUsuarioParametros();
    }

    public function borrarAjuste($id)
    {
        $ajuste = new ajuste();
        $ajuste->id = $id;
        return $ajuste->borrarAjuste();
    }

    public function listarAjustes()
    {
        return ajuste::traerTodosLosAjustes();
    }
}
?>