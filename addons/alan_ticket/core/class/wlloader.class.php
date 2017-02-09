<?php
defined('IN_IA') or exit('Access Denied');


function wl_load() {
	static $loader;
	if(empty($loader)) {
		$wl_loader = new Wl_loader();
	}
	return $wl_loader;
}

class Wl_loader {
	
	private $cache = array();
	
	function func($name) {
		global $_W;
		if (isset($this->cache['atfunc'][$name])) {
			return true;
		}
		$file = TICKET_CORE . 'function/' . $name . '.func.php';
		if (file_exists($file)) {
			include_once $file;
			$this->cache['atfunc'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Helper Function '.TICKET_CORE.'function/' . $name . '.func.php', E_USER_ERROR);
			return false;
		}
	}
	
	function model($name) {
		global $_W;
		if (isset($this->cache['atmodel'][$name])) {
			return true;
		}
		$file = TICKET_CORE . 'model/' . $name . '.mod.php';
		if (file_exists($file)) {
			include_once $file;
			$this->cache['atmodel'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Model '.TICKET_CORE.'model/' . $name . '.mod.php', E_USER_ERROR);
			return false;
		}
	}
	
	function classs($name) {
		global $_W;
		if (isset($this->cache['atclass'][$name])) {
			return true;
		}
		$file = TICKET_CORE . 'class/' . $name . '.class.php';
		if (file_exists($file)) {
			include_once $file;
			$this->cache['atclass'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Class '.TICKET_CORE.'class/' . $name . '.class.php', E_USER_ERROR);
			return false;
		}
	}
	
	function web($name) {
		global $_W;
		if (isset($this->cache['atweb'][$name])) {
			return true;
		}
		$file = TICKET_PATH . '/web/common/' . $name . '.func.php';
		if (file_exists($file)) {
			include_once $file;
			$this->cache['atweb'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid Web Helper '.TICKET_PATH.'/web/common/' . $name . '.func.php', E_USER_ERROR);
			return false;
		}
	}
	
	function app($name) {
		global $_W;
		if (isset($this->cache['atapp'][$name])) {
			return true;
		}
		$file = TICKET_PATH . '/app/common/' . $name . '.func.php';
		if (file_exists($file)) {
			include_once $file;
			$this->cache['atapp'][$name] = true;
			return true;
		} else {
			trigger_error('Invalid App Function '.TICKET_PATH.'/app/common/' . $name . '.func.php', E_USER_ERROR);
			return false;
		}
	}
	
}
