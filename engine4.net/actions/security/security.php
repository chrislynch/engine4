<?php
/*
 * Security module. Make sure we are where we are supposed to be, and nowhere else.
 */

function e4_action_security_security_go(&$data){
	/*
	 * Ascertain if the current user is a logged in user or not
	 * Security is the only part of the system, at the moment, that uses cookies.
	 * Hence the cookie shortcut functions are here.
	 */

	$data['user'] = array();
	$data['user']['id'] = cookie_get('userid',0);
	if ($data['user']['id'] > 0){
		$data['user'] = e4_data_load($data['user']['id'],FALSE);
	}

	/*
 	* Now that we know who the user is, ascertain if this user can access the page that they are looking at.
 	*/
	
}


function cookie_set($cookiename,$cookievalue){
	$cookiename = 'e4_' . e4_domain() . '_' . $cookiename;
	$cookiename = str_ireplace('.', '_', $cookiename);
	setcookie($cookiename,$cookievalue,0,'/');
	$_REQUEST['cookie_' . $cookiename] = $cookievalue; // Tuck cookie value here in case it is needed during this page render 
}

function cookie_get($cookiename,$defaultvalue = '',$widget = FALSE){
	if (!$widget) {$cookiename = 'e4_' . e4_domain() . '_' . $cookiename;}
	$cookiename = str_ireplace('.', '_', $cookiename);
	
	if (isset($_REQUEST['cookie_' . $cookiename])){
		return $_REQUEST['cookie_' . $cookiename];
	} else {
		if (isset($_COOKIE[$cookiename])){
			return $_COOKIE[$cookiename];
		} else {
			return $defaultvalue;	
		}
	}
}

?>