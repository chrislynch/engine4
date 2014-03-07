<?php

if(file_exists('wp-includes')) {
	// The presenec of the wp-includes folder puts us in Wordpress mode
	define('WP_USE_THEMES', true);
	
	// Run Wordpress natively or run combined Wordpress/engine4.
	if(strstr($_SERVER["REQUEST_URI"],'wp-admin') OR isset($_GET['wp-debug'])){
		include('wp-index.php');
	} else {
		require('wp-load.php');
		
		$_REQUEST['q1'] = parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH); // Our .htaccess is different if we in Wordpress mode
		include ('_e/e.php');
		// get_header();
		_e_go();
		// get_sidebar();
		// get_footer();
	}	 
} else {
	// This is a pure engine4 website.
	if(!isset($_REQUEST['q1'])) { $_REQUEST['q1'] = parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH); } // Just in case our .htaccess is a Wordpress one 
	include ('_e/e.php');
	_e_go();	
}


?>
