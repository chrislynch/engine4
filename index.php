<?php
// Run engine4
if(!isset($_REQUEST['q1'])) { $_REQUEST['q1'] = parse_url(@$_SERVER["REQUEST_URI"],PHP_URL_PATH); } // Just in case our .htaccess is a Wordpress one 
include ('_e/e.php');
_e_go();	
?>
