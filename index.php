<?php

if(file_exists('wp-includes')) {
	// The presenec of the wp-includes folder puts us in Wordpress mode
	define('WP_USE_THEMES', true);
	// Run Wordpress natively or run combined Wordpress/engine4.
	if(strstr($_SERVER["REQUEST_URI"],'wp-admin') OR isset($_GET['wp-debug'])){
		require('./wp-blog-header.php');
	} else {
		$_GET['q1'] = parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH); // Our .htaccess is different if we in Wordpress mode
		require( dirname( __FILE__ ) . '/wp-load.php' );
		include ('_e/e.php');
		// get_header();
		_e_go();
		// get_sidebar();
		// get_footer();
	}	 
} else {
	// This is a pure engine4 website.
	include ('_e/e.php');
	_e_go();	
}







?>