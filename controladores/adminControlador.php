<?php
/**
 * Este fichero define la clase de modelo admin.
 *
 * @author Albertsuarez
 * @date 13-dic-2013
 **/

class adminControlador {

	private $_tpl;

	public function __construct() {
		$this -> _tpl = new TemplatePower($GLOBALS['tpl_theme_admin'], T_BYFILE);
	}

	/**
	 * este metodo se encarga de pintar el html de la vista por defcto para inmueble, que es el listado.
	 */
	public function index() {
		if (Session::get("auth")) {

			header('Location: ' . URL . 'news/');

		} else {
			//MUETRA LOS ERRORES, EN EL MEtODO INDEX ESTA EL BLOQUE errors_row

			$this -> initializeTpl("pagina", "Login");
			$this -> _tpl -> assign('header_login', Idioma::get("header_login"));
			if (Session::get('errors')) {
				$arr_temp = Session::getAndDestroy('errors');
				foreach ($arr_temp as $value) {
					$this -> _tpl -> newBlock('errors_row');
					$this -> _tpl -> assign("salida", $value);
				}
			}
			$this -> _tpl -> printToScreen();
		}
	}

	public function login() {

		if (Session::get("auth")) {

			header('Location: ' . URL . 'news/');

		} else {

			if (!empty($_POST)) {
				Security::escapa($_POST, $GLOBALS['clsDataClass'] -> getConnection());
			}

			$usuarios = Model::loadModel('admin', $GLOBALS['clsDataClass']);
			$user = $usuarios -> getUserbyUser($_POST['user']);
			
			//Comprobamos si existe el usuario
			if ($user && count($user)) {

				if ($user[0]['loginPassword'] == $_POST['pass'] ) { 

					Session::set('id', $user[0]['id']);
					Session::set('user', $user[0]["loginName"]);
					Session::set('level', $user[0]["tipo_usuario"]);
					Session::set('auth', true);
					Session::set('messages', 'Bienvenido #USER#.', array('#USER#' => $user[0]['loginName']));

					header('Location: ' . URL . 'news/');

				}
			} else {

				Session::set('errors','Usuario o contraseña incorrectos.');
				header('Location: ' . URL . 'admin/');

			}

		}

	}
	
	public function logout() {

        if (Session::get('auth')) {
            Session::destroy('ID');
            Session::destroy('User');
            Session::destroy('Level');
            Session::destroy('auth');
            Session::set('messages',('Te has deslogueado correctamente.'));
        }

       // header('Location: ' . URL . 'admin/');
    }
	
	public function recovery() {

        
        header('Location: ' . URL . 'admin/');
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