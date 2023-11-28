<?php
require_once(__DIR__ . '/clientes.php');
class guardarImagen
{
	public function guardarImagenCliente($cliente)
	{
		$carpeta_archivos = './datos/ImagenesClientes/2023/';
		

		// Datos del arhivo enviado por POST
		$nombre_archivo = $cliente->numeroCliente . $cliente->tipoCliente . ".png";
		$tipo_archivo = $_FILES['fotoPerfil']['type'];
		$tamano_archivo = $_FILES['fotoPerfil']['size'];

		// Ruta destino, carpeta + nombre del archivo que quiero guardar
		$ruta_destino = $carpeta_archivos . $nombre_archivo;

		// Realizamos las validaciones del archivo
		if (!((strpos($tipo_archivo, "png") || strpos($tipo_archivo, "jpeg")) && ($tamano_archivo < 10000000000))) {
			echo "La extensión o el tamaño de los archivos no 
            es correcta. <br><br><table><tr><td><li>Se permiten archivos .png o 
            .jpg<br><li>se permiten archivos de 100 Kb máximo.</td></tr></table>";
		} else {
			if (move_uploaded_file($_FILES['fotoPerfil']['tmp_name'],  $ruta_destino)) {
				echo "El archivo ha sido cargado correctamente.";
			} else {
				echo "Ocurrió algún error al subir el fichero. No pudo guardarse.";
			}
		}
		
	}

	public function guardarImagenReserva($reserva){
		$carpeta_archivos = './datos/imagenesDeReserva2023/';

		// Datos del arhivo enviado por POST
		$nombre_archivo = $reserva->tipoCliente . $reserva->numeroCliente . "-" . $reserva->fechaDeEntrada . ".png";
		$tipo_archivo = $_FILES['fotoReserva']['type'];
		$tamano_archivo = $_FILES['fotoReserva']['size'];

		// Ruta destino, carpeta + nombre del archivo que quiero guardar
		$ruta_destino = $carpeta_archivos . $nombre_archivo;

		// Realizamos las validaciones del archivo
		if (!((strpos($tipo_archivo, "png") || strpos($tipo_archivo, "jpeg")) && ($tamano_archivo < 1000000000000000000000))) {
			echo "La extension o el tamanio de los archivos no es correcta. <br><br><table><tr><td><li>Se permiten archivos .png o .jpg<br><li>se permiten archivos de 100 Kb máximo.</td></tr></table>";
		} else {
			if (move_uploaded_file($_FILES['fotoReserva']['tmp_name'],  $ruta_destino)) {
				echo "El archivo ha sido cargado correctamente.";
			} else {
				echo "Ocurrio algun error al subir el fichero. No pudo guardarse.";
			}
		}
	}



}