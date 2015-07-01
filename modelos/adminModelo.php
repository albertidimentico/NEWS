<?php

/**
 * Este fichero define la clase de modelo admin.
 *
 * @author Albertsuarez
 * @date 13-dic-2013
 **/

class admin {
	/**
	 * @var phpDataClass puntero al handler de las conexiones SQL
	 */
	private $_mysqlHandler;

	public function __construct($mysqlHandler) {
		$this -> _mysqlHandler = $mysqlHandler;

	}

	/**  Esta funcion se encarga de obtener los usuarios.
	 *
	 * @param
	 *
	 * @return array devuelve un array con todos los datos.
	 */
	public function getUserByUser($user) { 	

	 	$query = "select *
  			from usuarios WHERE loginName = '$user'";

		$resource = $this -> _mysqlHandler -> executeQuery($query);

		$datos = false;

		if (!$resource) {
			return $datos = false;
		}else{

			for($datos; $row = mysql_fetch_assoc($resource); $datos[] = $row);

			return $datos;

		}

	}

}
?>
