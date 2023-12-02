<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

//require_once "require_once 'slim-php-deployment/app/controllers/clienteController.php';";
require_once "./clases/clientes.php";
require_once './controllers/clienteController.php';
require_once './controllers/reservaController.php';
require_once './controllers/usuariosController.php';
require_once './clases/usuarios.php';
require_once "./middlewares/Logger.php";
require_once "./middlewares/AuthJWT.php";

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

//esto abre el puerto 8080
//php -S localhost:8080 -t app


// Routes

$app->group('/clientes', function (RouteCollectorProxy $group) {
    $group->post('/alta', \clienteController::class . ':AltaClienteOModificacion');
    $group->post('/consultar', \clienteController::class . ':consultarCliente');
    $group->put('/modificar', \clienteController::class . ':modificarClienteDos');
    $group->delete('/BorrarCliente', \clienteController::class . ':BorrarCliente');

})->add(\AuthJWT::class . ':registroMovimientos')->add(\AuthJWT::class . ':VerificarTokenValido')->add(\AuthJWT::class . ':VerificarTokenRolGerente')->add(\AuthJWT::class . ':registrarMovimientoExitoso');

$app->group('/reservas', function(RouteCollectorProxy $group){
    $group->post('/alta', \reservaController::class . ':ReservaHabitacion');
    $group->get('/consultaPorFecha', \reservaController::class . ':consultarReservasPorFecha');
    $group->get('/consultaPorNumeroCliente', \reservaController::class . ':consultarReservasPorNumeroCliente');
    $group->get('/consultaPorFechaOrdenada', \reservaController::class . ':consultarReservasPorFechaOrdenada');
    $group->get('/consultaPorTipoReserva', \reservaController::class . ':consultarReservasPorTipoReserva');
    $group->post('/CancelarReserva', \reservaController::class . ':CancelarReserva');
    $group->post('/AjusteReserva', \reservaController::class . ':AjusteReserva');
    $group->get('/cancelacionesPorTipoClienteYFecha', \reservaController::class . ':obtenerTotalCancelacionesPorTipoYFecha');
    $group->get('/listarCancelacionesPorCliente', \reservaController::class . ':listarCancelacionesPorCliente');
    $group->get('/listarCancelacionesEntreFecha', \reservaController::class . ':listarCancelacionesEntreFecha');
    $group->get('/listarCancelacionesPorTipoCliente', \reservaController::class . ':listarCancelacionesPorTipoCliente');
    $group->get('/listarPorCliente', \reservaController::class . ':listarPorCliente');
    $group->get('/listarPorModalidad', \reservaController::class . ':listarPorModalidad');
})->add(\AuthJWT::class . ':registroMovimientos')->add(\AuthJWT::class . ':VerificarTokenValido')->add(\AuthJWT::class . ':VerificarTokenRolGerente')->add(\AuthJWT::class . ':registrarMovimientoExitoso');




$app->group('/usuarios', function(RouteCollectorProxy $group){
    $group->post('/alta', \usuariosController::class . ':AltaUsuario');
});


$app->post('/LoggingUsuario',[\Logger::class, 'LoggearUsuario']);



$app->run();
