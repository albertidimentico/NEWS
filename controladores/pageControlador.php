<?php
/**
 * Este fichero define la clase de modelo page.
 *
 * @author Albertsuarez
 * @date 24-ene-2014
 **/

class pageControlador {

	var $post = "post";
	

	private $_tpl;

	public function __construct() {
		$this -> _tpl = new TemplatePower($GLOBALS['tpl_theme'], T_BYFILE);

	}

	

	public function index() {
     
     $this -> initializeTpl("pagina", "Home");

     $url = explode('-', $GLOBALS['params'][0]);
     $iddato = strtolower(array_shift($url));
    

     $post = Model::loadModel('page', $GLOBALS['clsDataClass']);
     $onlypost = $post->GetId($tabla= $this->post,$id= "id=".$iddato) ;

     $this -> _tpl -> assign('id',  $onlypost[0]["id"]);
     $this -> _tpl -> assign('title',  $onlypost[0]["titulo"]);
     $this -> _tpl -> assign('date',  $onlypost[0]["fecha"]);
     $this -> _tpl -> assign('text',  $onlypost[0]["text"]);

	 $this -> _tpl -> printToScreen();




	}

	

	/** funcion privada para inicializar el objeto tpl de la clase. Se le pasal el titulo y demas
	 *
	 * @param string $str
	 * @param string $titulo titlo del documetno
	 */
	private function initializeTpl($str, $titulo) {
		$this -> _tpl -> assignInclude('content', $GLOBALS['tpl_theme_views'] . $GLOBALS['controlador'] . '-' . $GLOBALS['metodo'] . '.html');
		
		if (Session::get('errors')) {
			$this -> _tpl -> assignInclude('errors', $GLOBALS['tpl_theme_views'] . $GLOBALS['controlador'] . '-errors.html');
		}

		$this -> _tpl -> prepare();

		$this -> _tpl -> assign('content', '');

		$this -> _tpl -> gotoBlock('_ROOT');
		$this -> _tpl -> assign('title', TITULO . ' - ' . $titulo);
		$this -> _tpl -> assign('theme_url', $GLOBALS['tpl_theme_url']);
		$this -> _tpl -> assign('page_url', $GLOBALS['tpl_host']);
		

	}

}
?>