<?php

class phpDataClass
{
	private $conConnection;
	private $strDBServer,$strDBUsername,$strDBPassword, $strDBName;
	
	public function __construct($strServer,$strUsername,$strPassword,$strDatabase)
	{
		$this->strDBServer=$strServer;
		$this->strDBUsername=$strUsername;
		$this->strDBPassword=$strPassword;
		$this->strDBName=$strDatabase;
	}
	
	public function __destruct()
	{
		if(isset($this->conConnection)){
			mysql_close($this->conConnection);
		}
	}
	
	private function setConnection()
	{
		if(@$conConnection=mysql_connect($this->strDBServer,$this->strDBUsername,
		$this->strDBPassword))
		{
			if(mysql_select_db($this->strDBName,$conConnection))
			{
				$this->conConnection=$conConnection;
			}
		}
	}
	
	public function closeConnection()
	{
		mysql_close($this->conConnection);
		unset($this->conConnection);
	}
	
	public function getConnection()
	{
		if(!isset($this->conConnection))
		{
			$this->setConnection();
		}
		
		return $this->conConnection;
	}
	
	public function lastError()
	{
		return mysql_error();
	}
	
	public function affectedRows()
	{
		return mysql_affected_rows();
	}
	
	public function executeQuery($strQueryString)
	{
		 @mysql_query("set names utf8");
		return mysql_query($strQueryString,$this->getConnection());
	}
	
	public function rowCount($strQueryString)
	{
		return mysql_num_rows(mysql_query($strQueryString,$this->getConnection()));
	}
	
	public function uniqueValue($strQueryString)
	{
		if($resQuery=mysql_query($strQueryString,$this->getConnection()))
		{
			if(mysql_num_rows($resQuery)){
				return $this->sanitizeOut(mysql_result($resQuery,0));
			}else{
				return null;
			}
		}
		else
		{
			return null;
		}
	}
	
	public function recordsList($strQueryString)
	{
		if($resQuery=mysql_query($strQueryString,$this->getConnection()))
		{
			$objReturn=array();
			while($objRow=mysql_fetch_assoc($resQuery))
			{
				$objReturn[]=$objRow;
			}
			return $objReturn;
		}
		else
		{
			return null;
		}
	}
	
	public function lastID()
	{
		return @mysql_insert_id();
	}
	
	public function sanitizeIn($strString)
	{
		return htmlspecialchars(htmlentities(addslashes(strip_tags($strString)),ENT_QUOTES));
	}
	
	public function sanitizeOut($strString)
	{
		return htmlspecialchars_decode(html_entity_decode(stripslashes($strString)),ENT_QUOTES);
	}
	
}


?>