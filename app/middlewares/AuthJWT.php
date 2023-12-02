<?php
require_once "./utils/Autenticador.php";
require_once "./clases/movimientoOk.php";
require_once "./clases/movimiento.php";
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthJWT
{
    public static function VerificarTokenValido(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        
        if (!$header) {
            $payload = json_encode(array("Error" => "Debe proporcionar el token de autenticacion"));
            $response = new Response();
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $token = trim(explode("Bearer", $header)[1]);

        try {
            JwtUtil::VerificarToken($token);
            $data = JwtUtil::DecodificarToken($token);     
            $request = $request->withAttribute("dataToken", json_encode($data));
            $response = $handler->handle($request);
            return $response;
        } catch (Exception $err) {
            $payload = json_encode(array("Error" => $err->getMessage()));
            $response = new Response();
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }

    public static function VerificarTokenRolGerente(Request $request, RequestHandler $handler) :Response
    {
        $header = $request->getHeaderLine('Authorization');
        if ($header) {
            $token = trim(explode("Bearer", $header)[1]);
            $data = JwtUtil::DecodificarToken($token);
            if ($data != null) {
                $usuarioRol = $data->Rol;
                $comaparacionUno = strtoupper("gerente");
                var_dump($usuarioRol);
                var_dump($comaparacionUno);
                if ($usuarioRol !=  $comaparacionUno) {
                    $response = new Response();
                    $response->getBody()->write(json_encode(['Error' => 'Rol Invalido']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            }
        }

        return $handler->handle($request);
    }

    public static function VerificarTokenRolClienteOrRecepcionista(Request $request, RequestHandler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        if ($header) {
            $token = trim(explode("Bearer", $header)[1]);
            $data = JwtUtil::DecodificarToken($token);
          
            if ($data != null) {
                
                $usuarioRol =strtoupper($data->Rol);        
                $comaparacionUno =strtoupper("cliente");
                $comaparacionDos =strtoupper("recepcionista");
                var_dump($comaparacionDos);
                if ($usuarioRol ==  $comaparacionUno || $usuarioRol ==  $comaparacionDos) {
                    return $handler->handle($request);
                } 
            }
        }
        $response = new Response();
        $response->getBody()->write(json_encode(['Error' => 'Rol Invalido']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    public static function registrarMovimientoExitoso(Request $request, RequestHandler $handler) : Response
    {
        $header = $request->getHeaderLine('Authorization');
        $requestNombre = $request->getMethod() . ' ' . $request->getUri()->getPath();
        if ($header) {
            $token = trim(explode("Bearer", $header)[1]);
            $data = JwtUtil::DecodificarToken($token);
            if ($data != null) {
                $nombreUsuario = $data->Nombre;
                movimientoExitoso::insertarMovimientoExitosoParametros($requestNombre,$nombreUsuario);
            }
        }
        return $handler->handle($request);
    }

    public static function registroMovimientos($request, $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $requestNombre = $request->getMethod() . ' ' . $request->getUri()->getPath();
        if ($header) {
            $token = trim(explode("Bearer", $header)[1]);
            $data = JwtUtil::DecodificarToken($token);
            if ($data != null) {
                $nombreUsuario = $data->Nombre;
                movimiento::insertarMovimientoParametros($requestNombre,$nombreUsuario);
            }
        }
        return $handler->handle($request);
    }
}
?>