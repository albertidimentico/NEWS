<?

//Función global para la traducción de toda la aplicación
//Ejemplo de uso: __('Hay disponibles #NUM# posts en la base de datos.', array('#NUM#' => $this->numPosts))
function __($text, array $vars = array()) {
	$search = array();
	$replace = array();
	foreach ($vars as $key => $value) {
		$search[] = $key;
		$replace[] = $value;
	}
	return str_replace($search, $replace, _($text));
}

function translation($lang, $textdomain_cod, $locale_cod) {
    $rname = SITE_NAME;
    $targetFolder = "locale/".$lang."/LC_MESSAGES/";
    if (is_dir($targetFolder)) {
        $folder = opendir($targetFolder);
        while (false !== ($file = readdir($folder))) {
            $pathFiles = $targetFolder."/".$file; 
            if ($file != ".." AND $file != "." AND !is_dir($file) AND strrchr($file,'.') == '.mo' AND $file != $rname.'.mo') { unlink($pathFiles); }
        }
        closedir($folder);
        $translatefile = $rname.time();
        copy('locale/'.$lang.'/LC_MESSAGES/'.$rname.'.mo','locale/'.$lang.'/LC_MESSAGES/'.$translatefile.'.mo');
    }
    else { $translatefile = $rname; }
    
    setlocale(LC_ALL, $lang.'.'.$locale_cod);
    putenv("LC_ALL=".$lang.'.'.$locale_cod);
    bindtextdomain($translatefile, './locale'); 
    bind_textdomain_codeset($translatefile, $textdomain_cod);  
    textdomain($translatefile);
} 

?>