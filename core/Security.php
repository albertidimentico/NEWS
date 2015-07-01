<?php

class Security {
	

	public static function escapa(&$arr = NULL, $mysql) {
		if ($arr) {
			foreach ($arr as $key => $valor) {
				if (is_array($arr[$key]) || is_object($arr[$key])) { self::escapa($arr); }
				else { $arr[$key] = mysql_real_escape_string($valor, $mysql); }
			}
		}
	}
	
}

?>