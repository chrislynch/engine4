<?php

/*
 * Default viewing mechanism
 * This will need, somehow, to support some templates.
 * I've created a "templates" directory, just need to work out how this maps to the views, etc.
 */
require_once 'engine4.net/lib/phpmarkdownextra/markdown.php';

if(!isset($data['configuration']['renderers']['html'])){ $data['configuration']['renderers']['html'] = array();}
if(!isset($data['configuration']['renderers']['html']['head'])){ $data['configuration']['renderers']['html']['head'] = 'templates/html/default/head.php';}
if(!isset($data['configuration']['renderers']['html']['body'])){ $data['configuration']['renderers']['html']['body'] = 'templates/html/default/body.php';}
if(!isset($data['configuration']['renderers']['html']['body-content'])){$data['configuration']['renderers']['html']['body-content'] = 'templates/html/default/home.php';}


ob_start();
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	print '<html><head>';
	include e4_findinclude($data['configuration']['renderers']['html']['head']);
	print '</head><body>';
	$output = ob_get_contents();
ob_end_clean();
ob_start();
	// Allow for Markdown inside all body templates.
	include e4_findinclude($data['configuration']['renderers']['html']['body']);
	$output .= Markdown(ob_get_contents());
ob_end_clean();
ob_start();
	print '</body></html>';
	$output .= ob_get_contents();
ob_end_clean();
	
print $output;

?>