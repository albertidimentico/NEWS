<?php 
//DEBUG ERRORES 
error_reporting(E_ALL);
ini_set("display_errors", 1);
 

DEFINE('URL','http://'.$_SERVER['SERVER_NAME'].'/union/');
DEFINE('HOST',$_SERVER['DOCUMENT_ROOT'].'/union/');
/////////CONFIGURACIÓN////////////////
//ARCHIVO DE CONFIGURACIÓIN//////////
DEFINE('TITULO', 'News');
//RUTA DE SERVER
DEFINE('RUTA', 'http://'.$_SERVER['SERVER_NAME']);
//RUTA 
DEFINE('SERVER_RUTA', 'controlador/');
//RUTA CARPETA IMG
DEFINE('IMG', '/media/img/');
//Tiempo de Session en minutos
DEFINE('SESSION_MINUTES', 1);
//Nombre del sitio
DEFINE('SITE_NAME', '');

 
?>