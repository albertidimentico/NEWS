<?php

/**
 * Este fichero define la clase de modelo admin.
 *
 * @author Albertsuarez
 * @date 13-dic-2013
 **/

class news {
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
	public function GetFormsIgnin($tabla) {
        	
		$query = "SHOW FIELDS FROM $tabla";

		$resource = $this -> _mysqlHandler -> executeQuery($query);

		$datos = false;

		if (!$resource) {
			return $datos = false;
		} else {

			for ($datos; $row = mysql_fetch_assoc($resource); $datos[] = $row);

			return $datos;

		}
	}

	public function add($insertar, $tabla) {
        
		$columnas = array();
		$valores = array();

		foreach ($insertar as $key => $value) {

			$columnas[] = $key;
			$valores[] = $value;

		}

		@$keys = implode(", ", array_map('mysql_escape_string', $columnas));
		@$values = implode("', '", array_map('mysql_escape_string', $valores));

	    $query = "INSERT INTO $tabla (" . $keys . ") VALUES (" . "'" . $values . "'" . ");";

		$resource = $this -> _mysqlHandler -> executeQuery($query);
		echo $this -> _mysqlHandler ->lastError();
		$lastid = $this -> _mysqlHandler -> lastID();
		$datos = false;

		if (!$resource) {
			
			return $datos = false;
		
		} else {

			return $lastid; $datos = true;
		}
	}
	
	public function Get($tabla,$paremetros=null) {
        	
		$query = "SELECT * FROM $tabla $paremetros";

		$resource = $this -> _mysqlHandler -> executeQuery($query);

		$datos = false;

		if (!$resource) {
			return $datos = false;
		} else {

			for ($datos; $row = mysql_fetch_assoc($resource); $datos[] = $row);

			return $datos;

		}
	}
	public function Getpost() {
        	
		$query = "SELECT * FROM categorias_post_usuario cpu 
		Inner Join categorias c on c.id = cpu.id_categoria 
		Inner Join post p on p.id = cpu.id_post
		Inner Join usuarios u on u.id = cpu.id_categoria
		";

		$resource = $this -> _mysqlHandler -> executeQuery($query);

		$datos = false;

		if (!$resource) {
			return $datos = false;
		} else {

			for ($datos; $row = mysql_fetch_assoc($resource); $datos[] = $row);

			return $datos;

		}
	}
	public function GetId($tabla,$id) {
        	
	    $query = "SELECT * FROM $tabla WHERE $id";

		$resource = $this -> _mysqlHandler -> executeQuery($query);

		$datos = false;

		if (!$resource) {
			return $datos = false;
		} else {

			for ($datos; $row = mysql_fetch_assoc($resource); $datos[] = $row);

			return $datos;

		}
	}

	public function edit($insertar, $tabla, $parametros) {

		$columnas = array();
		$valores = array();

		foreach ($insertar as $key => $value) {

			$columnas[] = $key;
			$valores[] = $value;

			if(!empty($value)){ 

				$update[] = $key.'='."#comilla#".$value."#comilla#";   

			}else{ 

				$update[] = $key."="."#comilla#sin datos#comilla#";

			}

			
		}

		
		@$updates = implode(", ", array_map('mysql_escape_string', $update));
	    $updates = str_replace("#comilla#","'",$updates);

		echo $query = "UPDATE $tabla SET $updates WHERE  $parametros ";

		$resource = $this -> _mysqlHandler -> executeQuery($query);
	
        	
		if (!$resource) {
			
			return $datos = false;
		
		} else {
          
			return $datos = true;
		
		}
	}


	public function delete($tabla,$id) {
        	
	    $query = "delete FROM $tabla WHERE $id";

		$resource = $this -> _mysqlHandler -> executeQuery($query);

		$datos = false;

		if (!$resource) {
			return $datos = false;
		} else {

			return $datos = true;

		}
	}

}
?>
