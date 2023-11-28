<?php

require_once "./clases/archivo.php";

class cliente {
    public $id;
    public $numeroCliente;
    public $nombre;
    public $apellido;
    public $tipoDocumento;
    public $numeroDocumento;
    public $mail;
    public $tipoCliente;
    public $pais;
    public $ciudad;
    public $telefono;
    public $modalidadPago;
    public $estado;

    public function __construct()
    {
        
    }

    public function constructParametros($numeroCliente, $nombre, $apellido, $tipoDocumento, $numeroDocumento, $mail, $tipoCliente, $pais, $ciudad, $telefono, $modalidadPago, $estado) {
        $this->numeroCliente = $numeroCliente;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipoDocumento = $tipoDocumento;
        $this->numeroDocumento = $numeroDocumento;
        $this->mail = $mail;
        $this->tipoCliente = $tipoCliente;
        $this->pais = $pais;
        $this->ciudad = $ciudad;
        $this->telefono = $telefono;
        $this->modalidadPago = $modalidadPago;
        $this->estado = $estado;
    }

    public function exponerDatosCliente()
    {
        return "Numero De Cliente {$this->numeroCliente}<br>Nombre {$this->nombre}<br>Apellido {$this->apellido}
        <br>Tipo Documento {$this->tipoDocumento}<br>Numero Documento {$this->numeroDocumento}
        <br>Mail {$this->mail}<br>Tipo Cliente {$this->tipoCliente}<br>Pais {$this->pais}
        <br>Ciudad {$this->ciudad}<br>Telefono {$this->telefono} <br>Modalidad {$this->modalidadPago}
        <br>Estado {$this->estado}";
    }

    public function insertarClienteParametros()
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->RetornarConsulta("INSERT INTO `clientes`(`numeroCliente`, `nombre`, `apellido`, `tipoDocumento`, `numeroDocumento`, `mail`, `tipoCliente`, `pais`, `ciudad`, `telefono`, `modalidadPago`, `estado`) 
        VALUES (:numeroCliente,:nombre,:apellido,:tipoDocumento,:numeroDocumento,:mail,:tipoCliente,:pais,:ciudad,:telefono,:modalidadPago,:estado)");
        $consulta->bindValue(':numeroCliente', $this->numeroCliente, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDocumento', $this->tipoDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':numeroDocumento', $this->numeroDocumento, PDO::PARAM_INT);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipoCliente', $this->tipoCliente, PDO::PARAM_STR);
        $consulta->bindValue(':pais', $this->pais, PDO::PARAM_STR);
        $consulta->bindValue(':ciudad', $this->ciudad, PDO::PARAM_STR);
        $consulta->bindValue(':telefono', $this->telefono, PDO::PARAM_INT);
        $consulta->bindValue(':modalidadPago', $this->modalidadPago, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        $consulta->execute();
        return $objetoAccesoDatos->RetornarUltimoIdInsertado();
    }

    public function modificarCliente()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE `clientes` 
        SET `numeroCliente`= :numeroCliente, `nombre`= :nombre, `apellido`= :apellido, 
        `tipoDocumento`= :tipoDocumento, `numeroDocumento`= :numeroDocumento, `mail`= :mail, `tipoCliente`= :tipoCliente,
        `pais`= :pais, `ciudad`= :ciudad, `telefono`= :telefono, `modalidadPago`= :modalidadPago,`estado` = :estado
        WHERE id = :id");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':numeroCliente', $this->numeroCliente, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDocumento', $this->tipoDocumento, PDO::PARAM_STR);
        $consulta->bindValue(':numeroDocumento', $this->numeroDocumento, PDO::PARAM_INT);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipoCliente', $this->tipoCliente, PDO::PARAM_STR);
        $consulta->bindValue(':pais', $this->pais, PDO::PARAM_STR);
        $consulta->bindValue(':ciudad', $this->ciudad, PDO::PARAM_STR);
        $consulta->bindValue(':telefono', $this->telefono, PDO::PARAM_INT);
        $consulta->bindValue(':modalidadPago', $this->modalidadPago, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_INT);
        return $consulta->execute();
    }
    public function darDeBajaCliente($numeroCliente)
{
    try {
        $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $pdo = $accesoDatos->obtenerConexionPDO();
        
        $sql = "UPDATE clientes SET estado = 'no-activo' WHERE numeroCliente = ? AND estado = 'activo'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$numeroCliente]);

        // Verificar si se realizó la actualización correctamente
        $rowCount = $stmt->rowCount();
        return ($rowCount > 0);  // Devolver true si se actualizó al menos una fila
    } catch (PDOException $e) {
        echo 'Error al dar de baja el cliente: ' . $e->getMessage();
        return false;
    }
}

    public static function moverCarpetaCliente($numeroCliente, $tipoCliente)
    {
        $carpeta_archivos_origen = './datos/ImagenesClientes/2023/';
        $carpeta_archivos_Destino = './datos/ImagenesBackuClientes/2023/';
        $nombre_archivo = $numeroCliente . $tipoCliente . ".png";
        
        if (rename($carpeta_archivos_origen . $nombre_archivo,  $carpeta_archivos_Destino .  $nombre_archivo)) {
            return 'La imagen se movió exitosamente.';
        } else {
            return 'No se pudo mover la imagen.';
        }
    }



}

?>
