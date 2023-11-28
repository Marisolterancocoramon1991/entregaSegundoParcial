
<?php

require_once "./clases/archivo.php";

class reserva {
    public $id;
    public $tipoCliente;
    public $numeroCliente;
    public $fechaDeEntrada;	
    public $fechaDeSalida;
    public $tipoDeHabitacion;
    public $importeTotalReserva;
    public $estado;

    public function __construct()
    {
        
    }

    public function constructParametros($tipoCliente, $numeroCliente,  $fechaDeEntrada, $fechaDeSalida, $tipoDeHabitacion, $importeTotalReserva, $estado) {
        $this->tipoCliente = $tipoCliente;
        $this->numeroCliente = $numeroCliente;
        $this->fechaDeEntrada = $fechaDeEntrada;
        $this->fechaDeSalida = $fechaDeSalida;
        $this->tipoDeHabitacion = $tipoDeHabitacion;
        $this->importeTotalReserva = $importeTotalReserva;
        $this->estado = $estado;
    }

    public function exponerDatosCliente()
    {
        return "tipo De Cliente {$this->tipoCliente}<br>Numero Cliente {$this->numeroCliente}<br>Fecha De Entreda {$this->fechaDeEntrada}
        <br>Fecha De Salida {$this->fechaDeSalida}<br>Tipo De Habitacion {$this->tipoHabitacion}
        <br>Importe De Reserva {$this->importeTotalReserva} <br>Estado {$this->estado}";
    }

    public function InsertarReserva()
    {
        $objetoAccsesoDatos = AccesoDatos::dameUnObjetoAcceso("INSERT INTO `reservas`(`id`, `tipoCliente`, `numeroCliente`, `fechaDeEntrada`, `fechaDeSalida`, `tipoDeHabitacion`, `importeTotalReserva`, `estado`) 
        VALUES ('$this->tipoCliente','$this->numeroCliente',(STR_TO_DATE('$this->fechaDeEntrada', '%Y-%m-%d')),(STR_TO_DATE('$this->fechaDeSalida', '%Y-%m-%d')),'$this->tipoDeHabitacion', '$this->importeTotalReserva', '$this->estado')");
        $consulta = $objetoAccsesoDatos->RetornarConsulta();
        $consulta->execute();
        return $objetoAccsesoDatos->RetornarUltimoIdInsertado();
    }

    public function insertarReservaParametros()
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->RetornarConsulta("INSERT INTO `reservas`(`tipoCliente`, `numeroCliente`, `fechaDeEntrada`, `fechaDeSalida`, `tipoDeHabitacion`, `importeTotalReserva`, `estado`) 
        VALUES (:tipoCliente, :numeroCliente, STR_TO_DATE(:fechaDeEntrada, '%Y-%m-%d'), STR_TO_DATE(:fechaDeSalida, '%Y-%m-%d'), :tipoDeHabitacion, :importeTotalReserva, :estado)");

        $consulta->bindValue(':tipoCliente', $this->tipoCliente, PDO::PARAM_STR);
        $consulta->bindValue(':numeroCliente', $this->numeroCliente, PDO::PARAM_INT);
        $consulta->bindValue(':fechaDeEntrada', $this->fechaDeEntrada, PDO::PARAM_STR);
        $consulta->bindValue(':fechaDeSalida', $this->fechaDeSalida, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeHabitacion', $this->tipoDeHabitacion, PDO::PARAM_STR);
        $consulta->bindValue(':importeTotalReserva', $this->importeTotalReserva, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);

        $consulta->execute();

        return $objetoAccesoDatos->RetornarUltimoIdInsertado();
    }

    public static function traerTodosLosReservas()
    {
        $objetoAccsesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccsesoDatos->RetornarConsulta("SELECT * FROM `reservas`");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "cliente");
    }

    public function modificarReservasParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE `reservas` 
        SET `tipoCliente`= :tipoCliente,`numeroCliente`= :numeroCliente,`fechaDeEntrada`= STR_TO_DATE(:fechaDeEntrada, '%Y-%m-%d'),`fechaDeSalida`= STR_TO_DATE(:fechaDeSalida, '%Y-%m-%d'),`tipoDeHabitacion`= :tipoDeHabitacion, `importeTotalReserva`= :importeTotalReserva, `estado`= :estado
        WHERE id = :id");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':tipoCliente', $this->tipoCliente, PDO::PARAM_STR);
        $consulta->bindValue(':numeroCliente', $this->numeroCliente, PDO::PARAM_INT);
        $consulta->bindValue(':fechaDeEntrada', $this->fechaDeEntrada, PDO::PARAM_STR);
        $consulta->bindValue(':fechaDeSalida', $this->fechaDeSalida, PDO::PARAM_STR);
        $consulta->bindValue(':tipoDeHabitacion', $this->tipoDeHabitacion, PDO::PARAM_STR);
        $consulta->bindValue(':importeTotalReserva', $this->importeTotalReserva, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);

        return $consulta->execute();
    }

    public static function obtenerTotalReservasPorTipoYFecha($tipoHabitacion, $fecha = null)
    {
        try {
            $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $pdo = $accesoDatos->obtenerConexionPDO();

            if ($fecha === null) {
                $fecha = date('Y-m-d', strtotime('-1 day'));
            }
            $stmt = $pdo->prepare("SELECT tipoDeHabitacion, SUM(importeTotalReserva) as importeTotalReserva FROM reservas WHERE fechaDeEntrada = ? GROUP BY tipoDeHabitacion");
            $stmt->execute([$fecha]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultados;
        } catch (PDOException $e) {
            echo 'Error al obtener el total de reservas por tipo de habitación y fecha: ' . $e->getMessage();
            return false;
        }
    }
    
    public static function obtenerReservaPorIdCliente($numeroCliente)
    {
        try {
            $accesoDatos = AccesoDatos::dameUnObjetoAcceso(); // Asumiendo que AccesoDatos es tu clase de acceso a la base de datos
            $pdo = $accesoDatos->obtenerConexionPDO();
            $stmt = $pdo->prepare("SELECT * FROM reservas WHERE numeroCliente = ? ");
            $stmt->execute([$numeroCliente]);
            $reserva = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $reserva;
        } catch (PDOException $e) {
            echo 'Error al obtener la reserva: ' . $e->getMessage();
            return false;
        }
    }
    public static function obtenerReservasEnRangoYOrdenadasPorFechaEntrada($fechaUno, $fechaDos)
    {
        try {
            $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $pdo = $accesoDatos->obtenerConexionPDO();
            $stmt = $pdo->prepare("SELECT * FROM reservas WHERE fechaDeEntrada >= ? AND fechaDeEntrada <= ? ORDER BY fechaDeEntrada ASC");
            $stmt->execute([$fechaUno, $fechaDos]);
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $reservas;
        } catch (PDOException $e) {
            echo 'Error al obtener las reservas: ' . $e->getMessage();
            return false;
        }
    }
    
    public static function obtenerReservasPorTipoHabitacion($tipoHabitacion)
    {
        try {
            $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $pdo = $accesoDatos->obtenerConexionPDO();
            $stmt = $pdo->prepare("SELECT * FROM reservas WHERE tipoDeHabitacion = ?");
            $stmt->execute([$tipoHabitacion]);
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $reservas;
        } catch (PDOException $e) {
            echo 'Error al obtener las reservas por tipo de habitación: ' . $e->getMessage();
            return false;
        }
    }

    public static function obtenerTotalCancelacionesPorTipoYFecha($tipoCliente, $fecha = null)
    {
        try {
            $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $pdo = $accesoDatos->obtenerConexionPDO();

            // Asumo que la columna de estado es 'estado' y la columna de fecha es 'fechaEntrada', ajusta según tu esquema de base de datos
            $sql = "SELECT SUM(importeTotalReserva) as totalCancelaciones FROM reservas WHERE tipoCliente = ? AND estado = 'cancelada'";
            // Agregamos la condición de fecha si se proporciona
            if ($fecha !== null) {
                $sql .= " AND fechaDeEntrada = ?";
            }

            $stmt = $pdo->prepare($sql);

            // Enlazamos los parámetros
            $stmt->bindValue(1, $tipoCliente, PDO::PARAM_STR);

            // Enlazamos el parámetro de fecha si se proporciona
            if ($fecha !== null) {
                $stmt->bindValue(2, $fecha, PDO::PARAM_STR);
            }

            $stmt->execute();

            $resultados = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Devolvemos el total de cancelaciones
            return $resultados['totalCancelaciones'];
        } catch (PDOException $e) {
            echo 'Error al obtener el total de cancelaciones por tipo de cliente y fecha: ' . $e->getMessage();
            return false;
        }
    }
    public static function obtenerCancelacionesPorCliente($numeroCliente)
    {
        try {
            $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $pdo = $accesoDatos->obtenerConexionPDO();
            $stmt = $pdo->prepare("SELECT * FROM reservas WHERE numeroCliente = ? AND estado = 'cancelada'");
            $stmt->execute([$numeroCliente]);
            $cancelaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $cancelaciones;
        } catch (PDOException $e) {
            echo 'Error al obtener las cancelaciones por cliente: ' . $e->getMessage();
            return false;
        }
    }


    public static function obtenerCancelacionesEntreFechas($fechaInicio, $fechaFin)
    {
        try {
            $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $pdo = $accesoDatos->obtenerConexionPDO();

            $sql = "SELECT * FROM reservas WHERE estado = 'cancelada' AND fechaDeEntrada BETWEEN ? AND ? ORDER BY fechaDeEntrada ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$fechaInicio, $fechaFin]);

            $cancelaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $cancelaciones;
        } catch (PDOException $e) {
            echo 'Error al obtener cancelaciones: ' . $e->getMessage();
            return false;
        }
    }

    public static function obtenerCancelacionesPorTipoCliente($tipoCliente)
    {
        try {
            $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $pdo = $accesoDatos->obtenerConexionPDO();
            $tipoClienteBusqueda = '%' . strtoupper($tipoCliente) . '%';
            $stmt = $pdo->prepare("SELECT * FROM reservas WHERE estado = 'cancelada' AND tipoCliente LIKE ?");
            $stmt->execute([$tipoClienteBusqueda]);
            $cancelaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $cancelaciones;
        } catch (PDOException $e) {
            echo 'Error al obtener las cancelaciones por tipo de cliente: ' . $e->getMessage();
            return false;
        }
    }

    public static function obtenerOperacionesPorCliente($numeroCliente)
    {
        try {
            $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $pdo = $accesoDatos->obtenerConexionPDO();
            $stmt = $pdo->prepare("SELECT * FROM reservas WHERE numeroCliente = ?");
            $stmt->execute([$numeroCliente]);
            $operaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $operaciones;
        } catch (PDOException $e) {
            echo 'Error al obtener las operaciones por nroCliente: ' . $e->getMessage();
            return false;
        }
    }
    public static function obtenerReservasPorModalidad($modalidad)
    {
        try {
            $accesoDatos = AccesoDatos::dameUnObjetoAcceso();
            $pdo = $accesoDatos->obtenerConexionPDO();
            $stmtClientes = $pdo->prepare("SELECT numeroCliente FROM clientes WHERE modalidadPago = ? AND estado = 'activo'");
            $stmtClientes->execute([$modalidad]);
            $clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

            // Obtener las reservas de los clientes encontrados
            $numerosClientes = array_column($clientes, 'numeroCliente');
            $placeholders = rtrim(str_repeat('?, ', count($numerosClientes)), ', ');

            if (empty($numerosClientes)) {
                return []; // No hay clientes con esa modalidad de pago
            }
            $stmtReservas = $pdo->prepare("SELECT * FROM reservas WHERE numeroCliente IN ($placeholders)");
            $stmtReservas->execute($numerosClientes);
            $reservas = $stmtReservas->fetchAll(PDO::FETCH_ASSOC);
            return $reservas;
        } catch (PDOException $e) {
            echo 'Error al obtener las reservas por modalidad: ' . $e->getMessage();
            return false;
        }
    }

}


?>