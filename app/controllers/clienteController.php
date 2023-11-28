<?php
use Slim\Psr7\Response;

require_once "./clases/clientes.php";
require_once "./clases/archivo.php";
require_once "./db/accesoDatos.php";

class clienteController
{

    public function AltaClienteOModificacion($request, $response)
    {
        $data = $request->getParsedBody();   
        $numeroCliente = mt_rand(100000, 999999);
        $nombre = $data['nombre'];
        $apellido = $data['apellido'];
        $tipoDocumento = $data["tipoDocumento"];
        $numeroDocumento = $data["numeroDocumento"];
        $mail = $data["mail"];
        $tipoCliente = $data['tipoCliente'];
        $pais = $data["pais"];
        $ciudad = $data["ciudad"];
        $telefono = $data["telefono"];
        $modalidadPago = $data["modalidadPago"];
        $estado = $data["estado"];
        
        // Definir los arrays de valores permitidos
        $tiposDocumentoPermitidos = array('DNI', 'LE', 'LC', 'PASAPORTE');
        $tiposClientePermitidos = array('INDI', 'CORPO');
        $modalidadesPagoPermitidas = array('EFECTIVO', 'TARJETA', 'MERCADO PAGO');

        // Convertir a mayúsculas para hacer la comparación sin importar mayúsculas/minúsculas
        $tipoDocumento = strtoupper($tipoDocumento);
        $tipoCliente = strtoupper($tipoCliente);
        $modalidadPago = strtoupper($modalidadPago);

        if (in_array($tipoDocumento, $tiposDocumentoPermitidos) &&
            in_array($tipoCliente, $tiposClientePermitidos) &&
            in_array($modalidadPago, $modalidadesPagoPermitidas)) {

            $usuarioHallado = clienteController::buscarClienteNombreTipoCliente($nombre, $tipoCliente);
            if ($usuarioHallado != Null) {
                $clienteController = new clienteController();
                $resultado = $clienteController->modificarCliente($usuarioHallado->id, $numeroCliente, 
                $nombre, $apellido, $tipoDocumento, $numeroDocumento, $mail, $tipoCliente, $pais, $ciudad, 
                $telefono, $modalidadPago,$estado);

                $payload = json_encode(array("Resultado Modificar" => $resultado));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            } else {   
                $cliente = new cliente();
                $cliente->constructParametros($numeroCliente, $nombre, $apellido, $tipoDocumento, $numeroDocumento, 
                $mail, $tipoCliente, $pais, $ciudad, $telefono, $modalidadPago,$estado);
                $respuesta = $cliente->insertarClienteParametros();

                $fotoGuardada = new guardarImagen();
                $fotoGuardada->guardarImagenCliente($cliente);

                $respuestaJson = json_encode(['resultado' => $respuesta]);
                $payload = json_encode($respuestaJson);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
        } else {
            // Manejar caso en que algún valor no sea válido
            $payload = json_encode(array("error" => "Valores de tipoDocumento, tipoCliente o modalidadPago no validos"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }


    public static function buscarClienteNumeroTipoCliente($numeroCliente,$tipoCliente){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM `clientes` 
        WHERE numeroCliente = :numeroCliente AND tipoCliente = :tipoCliente");
        $consulta->bindValue(':numeroCliente', $numeroCliente, PDO::PARAM_INT);
        $consulta->bindValue(':tipoCliente', $tipoCliente, PDO::PARAM_STR);
        $consulta->execute();
    
        $usuarioEncontrado = $consulta->fetchObject("cliente");
        return $usuarioEncontrado;
    }

    public static function buscarClienteNombreTipoCliente($nombre,$tipoCliente){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM `clientes` 
        WHERE nombre = :nombre AND tipoCliente = :tipoCliente");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipoCliente', $tipoCliente, PDO::PARAM_STR);
        $consulta->execute();
    
        $usuarioBuscado = $consulta->fetchObject("cliente");//esta es la linea 79
        return $usuarioBuscado;
    }
    

    public function modificarCliente($id,$numeroCliente, $nombre, $apellido, $tipoDocumento, $numeroDocumento, $mail, $tipoCliente, $pais, $ciudad, $telefono, $modalidadPago, $estado)
    {
        $cliente = new cliente();
        $cliente->id = $id;
        $cliente->numeroCliente = $numeroCliente;
        $cliente->nombre = $nombre;
        $cliente->apellido = $apellido;
        $cliente->tipoDocumento = $tipoDocumento;
        $cliente->numeroDocumento = $numeroDocumento;
        $cliente->mail = $mail;
        $cliente->tipoCliente = $tipoCliente;
        $cliente->pais = $pais;
        $cliente->ciudad = $ciudad;
        $cliente->telefono = $telefono;
        $cliente->modalidadPago = $modalidadPago;
        $cliente->estado =$estado;

        return $cliente->modificarCliente();
    }

    public function consultarCliente($request, $response){
        $data = $request->getParsedBody();
        $tipoCliente = $data["tipoCliente"];
        $numeroCliente = $data["numeroCliente"];

        $clienteBuscado = clienteController::buscarClienteNumeroTipoCliente($numeroCliente,$tipoCliente);

        if ($clienteBuscado != null) {
            $payload = json_encode(array("Pais" => "$clienteBuscado->pais", "Ciudad" => "$clienteBuscado->ciudad", "Telefono" => "$clienteBuscado->telefono"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $payload = json_encode(array("Respuesta" => "Tipo Cliente Incorrecto"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

    }

    public function ReservaHabitacion($request, $response)
    {
        $data = $request->getParsedBody();
        $tipoCliente = $data["tipoCliente"];
        $numeroCliente = $data["numeroCliente"];
        $fechaDeEntrada = $data["fechaDeEntrada"];
        $fechaDeSalida = $data["fechaDeSalida"];
        $tipoHabitacion = $data["tipoHabitacion"];
        $importeTotalReserva = $data["importeTotalReserva"];
        $clienteBuscado = clienteController::buscarClienteNumeroTipoCliente($numeroCliente,$tipoCliente);

        if ($clienteBuscado != null) {
            $payload = json_encode(array("Pais" => "$clienteBuscado->pais", "Ciudad" => "$clienteBuscado->ciudad", "Telefono" => "$clienteBuscado->telefono"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $payload = json_encode(array("Respuesta" => "Tipo Cliente Incorrecto"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }



    }

    public function modificarClienteDos($request, $response) 
    {   
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData, true);
        $numeroCliente = $data["numeroCliente"];
        $nombre = $data['nombre'];
        $apellido = $data['apellido'];
        $tipoDocumento = $data["tipoDocumento"];
        $numeroDocumento = $data["numeroDocumento"];
        $mail = $data["mail"];
        $tipoCliente = $data['tipoCliente'];
        $pais = $data["pais"];
        $ciudad = $data["ciudad"];
        $telefono = $data["telefono"];
        $modalidadPago =$data["modalidadPago"];
        $estado = $data["estado"];

        $clienteBuscado = clienteController::buscarClienteNumeroTipoCliente($numeroCliente,$tipoCliente);

        if ($clienteBuscado != null) {
            $clienteController = new clienteController();
            $resultado = $clienteController->modificarCliente($clienteBuscado->id,$numeroCliente,$nombre,$apellido,
            $tipoDocumento,$numeroDocumento,$mail,$tipoCliente,$pais,$ciudad,$telefono, $modalidadPago, $estado);
            $payload = json_encode(array("Resultado Modificar" => $resultado));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        } else{
            $payload = json_encode(array("Respuesta" => "Tipo Cliente Incorrecto o Numero de Cliente Incorrecto"));
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
 
    public function BorrarCliente($request, $response)
    {
        try {
            $parametros = $request->getQueryParams();
            $nroCliente = $parametros['numeroCliente'] ?? null;
            $tipoCliente = $parametros['tipoCliente'] ?? null;
            $tiposCliente = array('INDI', 'CORPO');

            if ($nroCliente == null || $tipoCliente == null) {
                $payload = json_encode(array("Respuesta" => "numero o tipo vacio"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            $tipo = strtoupper($tipoCliente);
            if (!in_array($tipo, $tiposCliente)) {
                $payload = json_encode(array("Respuesta" => "Tipo cliente Incorrecto INDI O CORPO"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
            $cliente = clienteController::buscarClienteNumeroTipoCliente($nroCliente, $tipoCliente);
            if (!$cliente) {
                $payload = json_encode(array("Respuesta" => "ERROR, al obtener Cliente"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }

            $resultado = $cliente->darDeBajaCliente($nroCliente);
            if (!$resultado) {
                $payload = json_encode(array("Respuesta" => "ERROR, al dar de baja Cliente"));
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
                $mensajeExito = 'Cliente borrado exitosamente';
                $resultado = cliente::moverCarpetaCliente($nroCliente, $tipoCliente);
                $response->getBody()->write(json_encode(['reserva' => $mensajeExito . '<br>' . $resultado]));

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


}