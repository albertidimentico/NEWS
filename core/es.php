<?php

/**
 * Este fichero define la clase Idioma.
 *
 * @author Albertsuarez
 * @date 6-ene-2014
 **/
class Idioma {

	public static function get($clave) {
		if (isset($clave)) {

			$array = array();

			/*TERMINOS DE USO*/
			$array["terminos_de_uso"] = "Politica de Privacidad.";
			$array["header_login"] = "Login";
			$array["guardar"] = "Guardar Post";
			/**/

			return $array[$clave];
		} else {
			return false;
		}
	}

}
?>