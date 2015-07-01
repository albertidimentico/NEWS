<?php

class Model {
	
	static function loadModel($nombre, $mysql) {
		$modelo = $nombre . 'Modelo';
        $rutaModelo = HOST . 'modelos/' . $modelo . '.php';
        
        if (is_readable($rutaModelo)) {
            include_once $rutaModelo;
            $modelo = new $nombre($mysql);
            return $modelo;
        }
        else { throw new Exception('Error de modelo'); }
	}

}

?>