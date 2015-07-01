<?php

/**
 * Este fichero define la clase de modelo admin.
 *
 * @author Albertsuarez
 * @date 24-en-2014
 **/

class home{
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
	public function getallform() {

		$query = "select *
  			from formularios f inner join tipoformulario t 
    		on f.id_tipo = t.tipo";

		$resource = $this -> _mysqlHandler -> executeQuery($query);

		$datos = false;

		for ($datos; $row = mysql_fetch_assoc($resource); $datos[] = $row);

		return $datos;

	}
	
	public function Getpostlimit() {
        	
		$query = "SELECT * FROM post LIMIT 3";

		$resource = $this -> _mysqlHandler -> executeQuery($query);

		$datos = false;

		if (!$resource) {
			return $datos = false;
		} else {

			for ($datos; $row = mysql_fetch_assoc($resource); $datos[] = $row);

			return $datos;

		}
	}

   

}
?>
