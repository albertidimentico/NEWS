<?php
/**
 * Este fichero define la clase de modelo page.
 *
 * @author Albertsuarez
 * @date 24-ene-2014
 **/

class newsControlador {

	var $post = "post";
	var $categorias = "categorias";
	var $categorias_post_usuario = "categorias_post_usuario";

	private $_tpl;

	public function __construct() {
		$this -> _tpl = new TemplatePower($GLOBALS['tpl_theme_admin'], T_BYFILE);

	}

	public function index() {
  
	if (Session::get("auth")) {
	

    $this -> initializeTpl("pagina", "Home");

     $post = Model::loadModel('news', $GLOBALS['clsDataClass']);
     if($allpost = $post->Getpost()){
	     foreach ($allpost as $posty) {
	     
	     $this -> _tpl -> newBlock("post");
	     $this -> _tpl -> assign('id',  $posty["id_post"]);
	     $this -> _tpl -> assign('title',  $posty["titulo"]);
	     $this -> _tpl -> assign('date',  $posty["fecha"]);
		        

	     }
     }

     
	 $this -> _tpl -> printToScreen();

	}else{ 

		header('Location: ' . URL . 'admin/');
	
	}

	}

	public function add() {
	     
    if (Session::get("auth")) {

	     $this -> initializeTpl("pagina", "Home");
	     $post = Model::loadModel('news', $GLOBALS['clsDataClass']);
	     $categoriasdata = $post->Get($tabla = $this->categorias);
	

	     $this -> _tpl -> assignGlobal('usuario', Session::get("id"));
	     $this -> _tpl -> assignGlobal('usuario_name',  "usuario");

	     $this -> _tpl -> assignGlobal('namecategoria', "categorias");

	     foreach ($categoriasdata  as $filas) {

		     	$this -> _tpl -> newBlock("categorias");
		        $this -> _tpl -> assign('id', $filas["id"]);
		        $this -> _tpl -> assign('name', $filas["Name"]);
		     
		     

	     }

	     $form = $post->GetFormsIgnin($tabla = $this->post);

	     foreach ($form as $formfield) {

		     if($formfield["Field"]!="id" && $formfield["Field"]!="fecha" && $formfield["Field"]!="slug"){
		     	$this -> _tpl -> newBlock("add");
		     	$this -> _tpl -> assign('type',  "text");
		     	if($formfield["Field"]=="text"){
		        	$this -> _tpl -> assign('class', "editor form-control");
		        	$this -> _tpl -> assign('etiqueta', "textarea");
		        	$this -> _tpl -> assign('fin_etiqueta', "</textarea>");
		        }else{
		        	$this -> _tpl -> assign('class', "form-control");
		        	$this -> _tpl -> assign('etiqueta', "input");
		        }
		        $this -> _tpl -> assign('input',  $formfield["Field"]);
		     } 
		     

	     }
     
	 $this -> _tpl -> printToScreen();

	}else{ 

		header('Location: ' . URL . 'admin/');
	
	}

	}


	public function save() {
	     
		 	if (Session::get("auth")) {
	        $insertar = array();
			$post= Model::loadModel('news', $GLOBALS['clsDataClass']);
            
			foreach ($post ->GetFormsIgnin($tabla = $this->post) as $form) {

			    if ($form["Field"] !== "id") {

			      	 if ($form["Field"] == "fecha") { 

			      	 	$insertar[$form["Field"]] = date("Y-m-d H:i:s");

			      	 }elseif($form["Field"] == "slug"){

						
						$insertar[$form["Field"]] = url::limpiaslug($_POST["titulo"]);

			      	 }else{

						$insertar[$form["Field"]] = $_POST[$form["Field"]];

			      	 }


					if (empty($insertar[$form["Field"]])) {

						echo $error_cm = "ERROR EMPTY CONTROLADOR REGISTRO->METODO->ADD->>" . $form["Field"];
						

					}
				}

			}
			if (empty($error_cm)) {
                  
				if ($insert = $post -> add($insertar, $this->post)) {
     
                     unset($insertar);

					//SEGURIDAD POR SI SE CAMBIA EL HIDDEN DE USUARIO
                    if($_POST["usuario"]==Session::get("id")){
                    	

                    	foreach ($post ->GetFormsIgnin($tabla = $this->categorias_post_usuario) as $form) {
					
							if( $form["Field"]= "id_categoria" ){

								$insertar[$form["Field"]] =	$_POST["categorias"];
							}

							if( $form["Field"]= "id_post" ){
								
								$insertar[$form["Field"]] =	$insert;
							}

							if( $form["Field"]= "id_usuario" ){
								
								$insertar[$form["Field"]] =	$_POST["usuario"];
							}
 					

					    }			

					    if (empty($insertar[$form["Field"]])) {

						echo $error_cm = "ERROR EMPTY CONTROLADOR REGISTRO->METODO->ADD->>" . $form["Field"];
						

						}

					
                    }
 					
 					if (empty($error_cm)) {
                  
						if ($insert = $post -> add($insertar, $this->categorias_post_usuario)) {	

								 
								 header('Location: ' . URL . 'news/');	
						}


					}	

					

				}

			}

			}else{ 

			header('Location: ' . URL . 'admin/');
	
			}
	}

	public function edit() {
		 if (Session::get("auth")) {
	     $this -> initializeTpl("pagina", "Home");


	     $post = Model::loadModel('news', $GLOBALS['clsDataClass']);
	     $categoriasdata = $post->Get($tabla = $this->categorias);
	

	     $this -> _tpl -> assignGlobal('usuario', Session::get("id"));
	     $this -> _tpl -> assignGlobal('usuario_name',  "usuario");
	     $this -> _tpl -> assignGlobal('namecategoria', "categorias");


	     foreach ($categoriasdata  as $filas) {

		     	$this -> _tpl -> newBlock("categorias");
		        $this -> _tpl -> assign('id', $filas["id"]);
		        $this -> _tpl -> assign('name', $filas["Name"]);
  

	     }

	     $this -> _tpl -> assignGlobal('idedit', $GLOBALS['params'][0]);
	
	     $editid = $post->GetId($tabla = $this->post,$id= "id=".$GLOBALS['params'][0]);

	     //$this -> _tpl -> assign('value', $editid[0]["text"]);

	     $form = $post->GetFormsIgnin($tabla = $this->post);

	     foreach ($form as $formfield) {

		     if($formfield["Field"]!="id" && $formfield["Field"]!="fecha" && $formfield["Field"]!="slug" ){
		     	$this -> _tpl -> newBlock("add");

		     	if($formfield["Field"]=="titulo"){
			   	 $this -> _tpl -> assign('value', $editid[0]["titulo"]);
			   	}
			   	if($formfield["Field"]=="text"){
			   	 $this -> _tpl -> assign('value', $editid[0]["text"]);
			   	}
		     	$this -> _tpl -> assign('type',  "text");
		        $this -> _tpl -> assign('input',  $formfield["Field"]);

		        if($formfield["Field"]=="text"){
		        	$this -> _tpl -> assign('class', "editor form-control");
		        	$this -> _tpl -> assign('etiqueta', "textarea");
		        	$this -> _tpl -> assign('fin_etiqueta', "</textarea>");
		        	$this -> _tpl -> assign('contenido_textarea', $editid[0]["text"]);
		        }else{
		        	$this -> _tpl -> assign('class', "form-control");
		        	$this -> _tpl -> assign('etiqueta', "input");
		        }
		     } 
		     

	     }
     
	 	$this -> _tpl -> printToScreen();
	 	}else{ 

			header('Location: ' . URL . 'admin/');
	
		}
	}

	public function saveedit() {
	     
			if (Session::get("auth")) {
	        $insertar = array();
			$post= Model::loadModel('news', $GLOBALS['clsDataClass']);
            
			foreach ($post ->GetFormsIgnin($tabla = $this->post) as $form) {

			      if ($form["Field"] !== "id") {

			      	 if ($form["Field"] == "fecha") { 

			      	 	$insertar[$form["Field"]] = date("Y-m-d H:i:s");

			      	 }elseif($form["Field"] == "slug"){

						$insertar[$form["Field"]] = url::limpiaslug($_POST["titulo"]);

			      	 }else{

						$insertar[$form["Field"]] = $_POST[$form["Field"]];

			      	 }


					if (empty($insertar[$form["Field"]])) {

						echo $error_cm = "ERROR EMPTY CONTROLADOR REGISTRO->METODO->ADD->>" . $form["Field"];
						

					}
				}

			}
			if (empty($error_cm)) {

				    if($insert = $post -> edit($insertar, $tabla = $this->post , $parametros = "id=".$GLOBALS['params'][0])){
     			
                     unset($insertar);

					//SEGURIDAD POR SI SE CAMBIA EL HIDDEN DE USUARIO
                    if($_POST["usuario"]==Session::get("id")){
                    	

                    	foreach ($post ->GetFormsIgnin($tabla = $this->categorias_post_usuario) as $form) {
					
							if( $form["Field"]= "id_categoria" ){

								$insertar[$form["Field"]] =	$_POST["categorias"];
							}

							if( $form["Field"]= "id_post" ){
								
								$insertar[$form["Field"]] =	$GLOBALS['params'][0];
							}

							if( $form["Field"]= "id_usuario" ){
								
								$insertar[$form["Field"]] =	$_POST["usuario"];
							}
 					

					    }			

					    if (empty($insertar[$form["Field"]])) {

						echo $error_cm = "ERROR EMPTY CONTROLADOR REGISTRO->METODO->ADD->>" . $form["Field"];
						

						}

					
                    }
 					
 					if (empty($error_cm)) {
                  
						if ($insert = $post -> edit($insertar, $tabla = $this->categorias_post_usuario, $parametros = "id_post=".$GLOBALS['params'][0])) {	
								
								 header('Location: ' . URL . 'news/');	
						}	

					}

				}

			}
			}else{ 

			header('Location: ' . URL . 'admin/');
	
			}

	}

	public function delete() {
		if (Session::get("auth")) {
		$post= Model::loadModel('news', $GLOBALS['clsDataClass']);

		if($insert = $post -> delete($tabla = $this->categorias_post_usuario , $parametros = "id_post=".$GLOBALS['params'][0])){

			 
			 if($insert = $post -> delete($tabla = $this->post , $parametros = "id=".$GLOBALS['params'][0])){
			 	 header('Location: ' . URL . 'news/');

			 }

		}

		}else{ 

			header('Location: ' . URL . 'admin/');
	
		}

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
		$this -> _tpl -> assignGlobal('guardar', Idioma::get("guardar"));
		



	}

}
?>