<?php


//CONFIG general
require_once 'config/config.php';
//include_once 'config/lang.php';
require_once 'core/es.php';
//SESSION 
require_once 'core/Session.php';
//Url
require_once 'core/Url.php';
Session::init();
//GetText
//require_once 'lib/lib.gettext.php';
//Conexión con base de datos
require_once 'core/db.php';
//Security
require_once 'core/Security.php';
//model
require_once 'core/Model.php';
//TemplatePower
require_once 'lib/class.TemplatePower.inc.php';
//Inicializar variables TemplatePower
$GLOBALS['tpl_theme'] = HOST . 'themes/default/index.html';
$GLOBALS['tpl_theme_url'] = URL . 'themes/default/';
$GLOBALS['tpl_theme_views'] = HOST . 'themes/default/views/';
$GLOBALS['tpl_theme_host'] = HOST . 'themes/default/';
$GLOBALS['tpl_theme_admin'] = HOST . 'themes/default/admin.html';
$GLOBALS['tpl_host'] = URL ;

if (isset($_GET['url']) && $_GET['url']) {
    $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
    $url = explode('/', $url);
    $url = array_filter($url);
    Security::escapa($url, $GLOBALS['clsDataClass']->getConnection());
    //Controlador
    $controlador = strtolower(array_shift($url));
    //Método
    $metodo = strtolower(array_shift($url));
    //Parámetros
    $params = $url;
}

if (!isset($controlador) ) {
    $controlador = 'home';
    $metodo = 'index';
    $params = array();
}

$GLOBALS['controlador'] = $controlador;
$GLOBALS['metodo'] = $metodo;
$GLOBALS['params'] = $params;

 $path = HOST . 'controladores/' . $controlador . 'Controlador.php';
//INDICA SI EL FICHERO SE PUEDE LEER 
if (is_readable($path)) {
    
	//CARGA EL CONTROLADOR Y EL OBJETO SEGÚN EL NOMBRE	
    require_once $path;
    $objControlador = $controlador . 'Controlador';
    $objControlador = new $objControlador();

    if ( !is_callable(array($objControlador, $metodo)) ) {
        $metodo = 'index';
    }

    if ( count($params) ) {
        call_user_func_array( array( $objControlador, $metodo ), $params);
    }
    else {
        call_user_func( array($objControlador, $metodo) );
    }
}
else {
    header('Location: '.URL);
}
?>