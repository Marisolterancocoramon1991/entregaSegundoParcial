<?php

use Slim\Psr7\Response;

//require_once "./clases/archivos.php";
require_once "./clases/usuarios.php";

class usuariosController{

    public function AltaUsuario($request, $response)
    {
        $data = $request->getParsedBody();
    
        $rolesPermitidos = ['GERENTE', 'RECEPCIONISTA', 'CLIENTE'];
    
        if (
            isset($data["nombre"]) &&
            isset($data["mail"]) &&
            isset($data["clave"]) &&
            isset($data["rol"])
        ) {
            $nombre = $data["nombre"];
            $mail = $data["mail"];
            $clave = $data["clave"];
            $rol = strtoupper($data["rol"]);
    
            if (!in_array($rol, $rolesPermitidos)) {
                $payload = json_encode(["Respuesta" => "Rol no válido"]);
                $response->getBody()->write($payload);
                return $response->withHeader('Content-Type', 'application/json');
            }
    
            $usuario = new usuario();
            $usuario->constructParametros($nombre, $mail, $clave, $rol);
    
            $resultado = $usuario->insertarUsuarioParametros();
    
            $respuestaJson = json_encode(['resultado' => $resultado]);
            $response->getBody()->write($respuestaJson);
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $payload = json_encode(["Respuesta" => "Campos incompletos"]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }
    
}