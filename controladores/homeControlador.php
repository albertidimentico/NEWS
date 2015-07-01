<?php
/**
 * Este fichero define la clase de modelo page.
 *
 * @author Albertsuarez
 * @date 24-ene-2014
 **/

class homeControlador {

	private $_tpl;

	public function __construct() {
		$this -> _tpl = new TemplatePower($GLOBALS['tpl_theme'], T_BYFILE);

	}

	public function index() {
     $this -> initializeTpl("pagina", "Home");


     $post = Model::loadModel('home', $GLOBALS['clsDataClass']);

     if($allpost = $post->Getpostlimit()){
    
	     foreach ($allpost as $posty) {
	     
	     $this -> _tpl -> newBlock("post");
	     $this -> _tpl -> assign('id',  $posty["id"]);
	     $this -> _tpl -> assign('title',  $posty["titulo"]);
	     $this -> _tpl -> assign('text', substr($posty["text"],0, 200));
	     $this -> _tpl -> assign('date',  $posty["fecha"]);
	     $this -> _tpl -> assign('slug',  $posty["slug"]);
		        

	     }
     }

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
		$this -> _tpl -> assignGlobal('page_url', $GLOBALS['tpl_host']);
		

	}

}
?>