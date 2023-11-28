<?php
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

require_once "./clases/clientes.php";
require_once "./clases/reservas.php";
require_once "./clases/ajustes.php";
require_once "./clases/archivo.php";
require_once "./db/accesoDatos.php";
require_once './controllers/clienteController.php';
require_once './controllers/ajustesController.php';

class reservaController
{
    public function ReservaHabitacion($request, $response)
    {
        $data = $request->getParsedBody();
        $tipoCliente = $data["tipoCliente"];
        $numeroCliente = $data["numeroCliente"];
        $fechaDeEntrada = $data["fechaDeEntrada"];
        $fechaDeSalida = $data["fechaDeSalida"];
        $tipoHabitacion = $data["tipoHabitacion"];
        $importeTotalReserva = $data["importeTotalReserva"];
        $estado = $data["estado"];
        $clienteBuscado = clienteController::buscarClienteNumeroTipoCliente($numeroCliente,$tipoCliente);

        if ($clienteBuscado != null) {
            $reserva = new reserva();
            $reserva-> constructParametros($tipoCliente, $numeroCliente, $fechaDeEntrada,
             $fechaDeSalida, $tipoHabitacion, $importeTotalReserva, $estado);
             $respuesta = $reserva->insertarReservaParametros();

             $fotoGuardada = new guardarImagen();

             $fotoGuardada ->guardarImagenReserva($reserva);

             $respuestaJson = json_encode(['resultado' => $respuesta]);
            $payload = json_encode($respuestaJson);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $payload = json_encode(array("Respuesta" => "Tipo Cliente Incorrecto"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }



    }
    
    public function consultarReservasPorFecha(Request $request, ResponseInterface $response)
    {
        try {
            $parametros = $request->getQueryParams();
            $tiposHabitacion = array('SIMPLE', 'DOBLE', 'SUITE');
            $tipoHabitacion = $parametros['tipoHabitacion'] ?? null;
            $fechaConsulta = $parametros['fecha'] ?? null;

            if ($tipoHabitacion == null) {
                $payload = json_encode(array("Respuesta" => "Tipo habitacion Incorrecto"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            $tipoHabitacion = strtoupper($tipoHabitacion);
            if (!in_array($tipoHabitacion, $tiposHabitacion)) {
                $payload = json_encode(array("Respuesta" => "Tipo de habitacion incorrecto. Debe ser de tipo: SIMPLE, DOBLE o SUITE."));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            $totalReservas = reserva::obtenerTotalReservasPorTipoYFecha($tipoHabitacion, $fechaConsulta);
            $response->getBody()->write(json_encode(['totalReservas' => $totalReservas]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (PDOException $e) {
            $errorResponse = [
                'error' => 'Error en la base de datos',
                'message' => $e->getMessage(),
            ];
            
            $payload = json_encode($errorResponse);
            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function consultarReservasPorNumeroCliente(Request $request, ResponseInterface $response)
    {
        try{
            $data = $request->getQueryParams();
            $numeroCliente = $data['numeroDeCliente']?? null;

            if($numeroCliente == null)
            {
                $payload = json_encode(array("Respuesta" => "no ha agregado numero de cliente"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');


            }
            $reserva = reserva::obtenerReservaPorIdCliente($numeroCliente);
            if($reserva)
            {
                $response->getBody()->write(json_encode(['reserva' => $reserva]));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

            }else
            {
                $payload = json_encode(array("Respuesta" => "no se ha hallado la reserva"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');

            }

        } catch (PDOException $e) {
            $errorResponse = [
                'error' => 'Error en la base de datos',
                'message' => $e->getMessage(),
            ];
            
            $payload = json_encode($errorResponse);
            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
        


    }

    public function consultarReservasPorFechaOrdenada(Request $request, ResponseInterface $response)
    {
        try
        {
            $data = $request->getQueryParams();
            $fechaUno = $data['fechaUno']?? null;
            $fechaDos = $data['fechaDos']?? null;
            if($fechaUno == null &&  $fechaDos == null)
            {
                $payload = json_encode(array("Respuesta" => "no ha agregado las fechas"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');


            }else
            {
                $respuesta = reserva::obtenerReservasEnRangoYOrdenadasPorFechaEntrada($fechaUno, $fechaDos);
                $response->getBody()->write(json_encode(['reserva' => $respuesta]));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

            }


        }catch(PDOException $e) 
        {
            $errorResponse = [
                'error' => 'Error en la base de datos',
                'message' => $e->getMessage(),
            ];
            
            $payload = json_encode($errorResponse);
            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);

        }
    }

    public function consultarReservasPorTipoReserva(Request $request, ResponseInterface $response)
    {
        try
        {
            $data = $request->getQueryParams();
            $tipoReserva = array('SIMPLE', 'DOBLE', 'SUITE');
            $tipoReserva = $data['tipoReserva']?? null;
            if($tipoReserva == null){

            }else
            {
                $reserva = reserva::obtenerReservasPorTipoHabitacion($tipoReserva);
                $response->getBody()->write(json_encode(['reserva' => $reserva]));

                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

            }



        }catch(PDOException $e) 
        {
            $errorResponse = [
                'error' => 'Error en la base de datos',
                'message' => $e->getMessage(),
            ];
            
            $payload = json_encode($errorResponse);
            $response->getBody()->write($payload);
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);


        }
    }
    public function modificarReserva($id, $fechaDeEntrada, $fechaDeSalida, $tipoHabitacion, $importeTotal, $numeroCliente, $tipoCliente, $estado)
    {
        $reserva = new reserva();
        $reserva->id = $id;
        $reserva->fechaDeEntrada = $fechaDeEntrada;
        $reserva->fechaDeSalida = $fechaDeSalida;
        $reserva->tipoDeHabitacion = $tipoHabitacion;
        $reserva->importeTotalReserva = $importeTotal;
        $reserva->numeroCliente = $numeroCliente;
        $reserva->tipoCliente = $tipoCliente;
        $reserva->estado = $estado;

        return $reserva->modificarReservasParametros();
    }

    public static function buscarReservaId($id) 
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM `reservas` 
        WHERE id = :id ");

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        $listaReservas = $consulta->fetchObject("reserva");// aqui estoy yeniendo el problema 216
        return $listaReservas;
        $reservaArray = $consulta->fetch(PDO::FETCH_ASSOC);

    
    }

    public function cancelarReserva($request, $response)
    {
        $data = $request->getParsedBody();
        $tipoCliente = $data["tipoCliente"];
        $numeroCliente = $data["numeroCliente"];
        $reservaId = $data["idReserva"];

        $reservaBuscada = reservaController::buscarReservaId($reservaId);
        $clienteBuscado = clienteController::buscarClienteNumeroTipoCliente($numeroCliente,$tipoCliente);
        if ($reservaBuscada != null && $clienteBuscado != null) {
            $reservaController = new reservaController();
            $respuesta = $reservaController->modificarReserva($reservaBuscada->id,$reservaBuscada->fechaDeEntrada,$reservaBuscada->fechaDeSalida,$reservaBuscada->tipoDeHabitacion,$reservaBuscada->importeTotalReserva,$reservaBuscada->numeroCliente,$reservaBuscada->tipoCliente,"cancelada");

            $payload = json_encode(array("Resultado Modificar" => $respuesta));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $payload = json_encode(array("Respuesta" => "No se encontro el cliente o la Reserva"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public function AjusteReserva($request, $response)
    {
        $data = $request->getParsedBody();
        $numeroReserva = $data["IdReserva"];
        $motivo = $data["motivo"];
        $nuevoImporte = $data["importe"];

        $reservaBuscada = reservaController::buscarReservaId($numeroReserva);

        if ($reservaBuscada != null) {
            $reservaController = new reservaController();
            $respuestaModificar = $reservaController->modificarReserva($reservaBuscada->id,$reservaBuscada->fechaDeEntrada,$reservaBuscada->fechaDeSalida,$reservaBuscada->tipoDeHabitacion,$nuevoImporte,$reservaBuscada->numeroCliente,$reservaBuscada->tipoCliente,"cancelada");

            $ajusteController = new ajusteController();
            $respuestaInsertarAjuste = $ajusteController->InsertarAjuste($numeroReserva,$motivo,$nuevoImporte);
            $payload = json_encode(array("Resultado Modificar" => $respuestaModificar,"Resultado Insertar Ajuste" => $respuestaInsertarAjuste));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $payload = json_encode(array("Respuesta" => "No se encontro el Numero De Reserva"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
    public function obtenerTotalCancelacionesPorTipoYFecha(Request $request, ResponseInterface $response)
    {
        try {
            $parametros = $request->getQueryParams();
            $tiposCliente = array('INDI', 'CORPO');
            $tipoCliente = $parametros['tipoCliente'] ?? null;
            $fechaConsulta = $parametros['fechaConsulta'] ?? null;

            if ($tipoCliente == null) {
                $payload = json_encode(array("Respuesta" => "ingresa tipo Cliente"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            $tipoCliente = strtoupper($tipoCliente);
            if (!in_array($tipoCliente, $tiposCliente)) {
                $payload = json_encode(array("Respuesta" => "tipo de cliente incorrecto indi o corpo"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            $totalCancelaciones = reserva::obtenerTotalCancelacionesPorTipoYFecha($tipoCliente, $fechaConsulta);
            $payload = json_encode(array("total cancelacion" => $totalCancelaciones ));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            return $response->withStatus(500)->withJson(['error' => 'Error en la base de datos']);
        }
    }
    public function listarCancelacionesPorCliente(Request $request, ResponseInterface $response)
    {
        try {
            $parametros = $request->getQueryParams();
            $numeroCliente = $parametros['numeroCliente'] ?? null;

            if ($numeroCliente == null) {
                $payload = json_encode(array("Respuesta" => "debe Ingresar el numero de cliente"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            
            $cancelaciones = reserva::obtenerCancelacionesPorCliente($numeroCliente);
            if ($cancelaciones) {
                $payload = json_encode(array("cancelaciones cliente" => $cancelaciones ));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
                
            } else {
                $payload = json_encode(array("Respuesta" => "hubo algun error"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (PDOException $e)
         {
            return $response->withStatus(500)->withJson(['error' => 'Error en la base de datos']);
        }
    }

    public function listarCancelacionesEntreFecha(Request $request, ResponseInterface $response)
    {
        try {
            $parametros = $request->getQueryParams();
            $fechaInicio = $parametros['fechaInicio'] ?? null;
            $fechaFin = $parametros['fechaFin'] ?? null;

            if ($fechaInicio == null || $fechaFin == null) {
                $payload = json_encode(array("Respuesta" => "debe Ingresar todas las fechas"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            try {
                $fechaInicioObj = new DateTime($fechaInicio);
                $fechaFinObj = new DateTime($fechaFin);
            } catch (Exception $e) {
                $payload = json_encode(array("Respuesta" => "error fechas proporcionadas son invalidas"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            $fechaInicioFormateada = $fechaInicioObj->format('Y-m-d');
            $fechaFinFormateada = $fechaFinObj->format('Y-m-d');

            $reservas = reserva::obtenerCancelacionesEntreFechas($fechaInicioFormateada, $fechaFinFormateada);
            if ($reservas) {
                
                $payload = json_encode(array("lista ordenada por fecha" => $reservas ));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
            } else {
                return $response->withStatus(404)->withJson(['error' => 'No se encontraron reservas entre las fechas proporcionadas.']);
            }
        } catch (PDOException $e) {
            return $response->withStatus(500)->withJson(['error' => 'Error en la base de datos']);
        }
    }
    public function listarCancelacionesPorTipoCliente(Request $request, ResponseInterface $response)
    {
        try {
            $parametros = $request->getQueryParams();
            $tiposCliente = array('INDI', 'CORPO');
            $tipoCliente = $parametros['tipoCliente'] ?? null;

            if ($tipoCliente == null) {
                $payload = json_encode(array("error" => "Tiene que ingresar un tipo de cliente"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
                
            }
            $tipoCliente = strtoupper($tipoCliente);
            if (!in_array($tipoCliente, $tiposCliente)) {
                $payload = json_encode(array("error" => "Tipo de cliente incorrecto. Debe ser de tipo: INDI o CORPO."));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
             
            }

            $cancelaciones = reserva::obtenerCancelacionesPorTipoCliente($tipoCliente);
            if ($cancelaciones) {
                $payload = json_encode(array("lista ordenada por fecha" => $cancelaciones ));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(array("error" => "en la carga de elementos"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (PDOException $e) {
            return $response->withStatus(500)->withJson(['error' => 'Error en la base de datos']);
        }
    }

    public function listarPorCliente(Request $request, ResponseInterface $response)
    {
        try {
            $parametros = $request->getQueryParams();
            $numeroCliente = $parametros['numeroCliente'] ?? null;

            if ($numeroCliente == null) {
                $payload = json_encode(array("error" => "ingresa numero cliente"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            $operaciones = reserva::obtenerOperacionesPorCliente($numeroCliente);
            if ($operaciones) {
                $payload = json_encode(array("lista operaciones por cliente" => $operaciones ));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(array("error" => "problemas con la carga"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (PDOException $e) {
            return $response->withStatus(500)->withJson(['error' => 'Error en la base de datos']);
        }
    }
    public function listarPorModalidad(Request $request, ResponseInterface $response)
    {
        try {
            $parametros = $request->getQueryParams();
            $modalidad = $parametros['modalidad'] ?? null;

            if ($modalidad == null) {
                $payload = json_encode(array("error" => "ingrese modalidad"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            $reservas = reserva::obtenerReservasPorModalidad($modalidad);
            if ($reservas) {
                $payload = json_encode(array("lista modalidad reservas" => $reservas ));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $payload = json_encode(array("error" => "no ha cargado correctamente la api"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (PDOException $e) {
            return $response->withStatus(500)->withJson(['error' => 'Error en la base de datos']);
        }
    }

    

}