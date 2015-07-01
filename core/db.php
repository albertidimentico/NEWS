<?php

//DATOS DE LA CONEXION 
define("host","localhost");
define("usuario","root");
define("contrasena","");
define("db","news");


//CONEXION CON LA BASE DE DATOS
$dir = HOST."lib/class.phpDataClass.php";
include_once $dir;

$database=db;
$username=usuario;
$password=contrasena;
$clsDataClass=new phpDataClass(host,usuario,contrasena,db);
$GLOBALS['clsDataClass'] =  $clsDataClass;



//9pC4TUNT  myunionno newsunion
?>