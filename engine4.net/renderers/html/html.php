<?php

/*
 * Default viewing mechanism
 * This will need, somehow, to support some templates.
 * I've created a "templates" directory, just need to work out how this maps to the views, etc.
 */
require_once 'engine4.net/lib/phpmarkdownextra/markdown.php';

if(!isset($data['configuration']['renderers']['html'])){ $data['configuration']['renderers']['html'] = array();}
if(!isset($data['configuration']['renderers']['html']['head'])){ $data['configuration']['renderers']['html']['head'] = 'head.php';}
if(!isset($data['configuration']['renderers']['html']['body'])){ $data['configuration']['renderers']['html']['body'] = 'body.php';}

ob_start();
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	print '<html><head>';
	include e4_findinclude('templates/html/default/' . $data['configuration']['renderers']['html']['head']);
	print '</head><body>';
	include e4_findinclude('templates/html/default/' . $data['configuration']['renderers']['html']['body']);
	print '</body></html>';
	$output = ob_get_contents();
ob_end_clean();

// Allow for Markdown inside templates as well
$output = Markdown($output);
print $output;

?>