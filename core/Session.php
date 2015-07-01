<?php

class Session {
	
    public static function init() { session_start(); }
	
	public static function initdestroy() {  return session_destroy(); }
    
    public static function destroy($clave = false) {
        if ($clave) {
            if (is_array($clave)) {
                for ($i = 0; $i < count($clave); $i++) {
                    if (isset($_SESSION[$clave[$i]])) { self::destroy($_COOKIE[$clave[$i]]); }
                }
            }
            else {
                if (isset($_SESSION[$clave])) { unset($_SESSION[$clave]); }
            }
        }
        else{
            session_destroy();
            self::init();
        }
    }
    
    public static function set($clave, $valor) {
        if(!empty($clave)) {
        	switch($clave) {
        		case 'messages':
        		case 'errors':
        			$_SESSION[$clave][] = $valor;
        		break;
        		default:
        			$_SESSION[$clave] = $valor;
        	}
		}
    }
    
    public static function get($clave) {
        if (isset($_SESSION[$clave])) { return $_SESSION[$clave]; }
        else { return false; }
    }
    
    public static function getWithTime($clave) {
    	self::tiempo();
        if (isset($_SESSION[$clave])) { return $_SESSION[$clave]; }
        else { return false; }
    }
    
    public static function getAndDestroy($clave) {
        if(isset($_SESSION[$clave])) {
        	$session = $_SESSION[$clave];
        	self::destroy($clave);
        	return $session;
		}
		return false;
    }
    
    public static function acceso($level) {
        if(!Session::getWithTime('auth')){
            header('location:' . BASE_URL . 'error/access/5050');
            exit;
        }
        
        Session::tiempo();
        
        if(Session::getLevel($level) > Session::getLevel(Session::getWithTime('level'))){
            header('location:' . BASE_URL . 'error/access/5050');
            exit;
        }
    }
    
    public static function accesoView($level) {
        if(!Session::getWithTime('auth')){
            return false;
        }
        
        if(Session::getLevel($level) > Session::getLevel(Session::getWithTime('level'))){
            return false;
        }
        
        return true;
    }
    
    public static function getLevel($level) {
        $role['admin'] = 3;
        $role['especial'] = 2;
        $role['usuario'] = 1;
        
        if(!array_key_exists($level, $role)){
            throw new Exception('Error de acceso');
        }
        else{
            return $role[$level];
        }
    }
    
    public static function accesoEstricto(array $level, $noAdmin = false) {
        if(!Session::getWithTime('auth')){
            header('location:' . BASE_URL . 'error/access/5050');
            exit;
        }
        
        Session::tiempo();
        
        if($noAdmin == false){
            if(Session::getWithTime('level') == 'admin'){
                return;
            }
        }
        
        if(count($level)){
            if(in_array(Session::getWithTime('level'), $level)){
                return;
            }
        }
        
        header('location:' . BASE_URL . 'error/access/5050');
    }
    
    public static function accesoViewEstricto(array $level, $noAdmin = false) {
        if(!Session::getWithTime('auth')){
            return false;
        }
        
        if($noAdmin == false){
            if(Session::getWithTime('level') == 'admin'){
                return true;
            }
        }
        
        if(count($level)){
            if(in_array(Session::getWithTime('level'), $level)){
                return true;
            }
        }
        
        return false;
    }
    
    public static function tiempo() {
        if (!defined('SESSION_MINUTES') || SESSION_MINUTES == 0) { throw new Exception('No se ha definido el tiempo de sesión'); }
        if (!self::get('Tiempo')) { return false; }
        
        if (time() - self::get('Tiempo') > (SESSION_MINUTES * 60)) {
            self::destroy();
            return false;
        }
        else {
            self::set('Tiempo', time());
            return true;
        }
    }
    
}

?>