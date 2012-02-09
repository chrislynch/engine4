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
if(!isset($data['configuration']['renderers']['html']['body-header'])){$data['configuration']['renderers']['html']['body-header'] = 'header.php';}
if(!isset($data['configuration']['renderers']['html']['body-content-header'])){$data['configuration']['renderers']['html']['body-content-header'] = 'body-top.php';}
if(!isset($data['configuration']['renderers']['html']['body-content'])){$data['configuration']['renderers']['html']['body-content'] = 'home.php';}
if(!isset($data['configuration']['renderers']['html']['body-content-footer'])){$data['configuration']['renderers']['html']['body-content-footer'] = 'body-bottom.php';}
if(!isset($data['configuration']['renderers']['html']['body-footer'])){$data['configuration']['renderers']['html']['body-footer'] = 'footer.php';}

if(!isset($data['configuration']['renderers']['html']['skins'])){ 
	$data['configuration']['renderers']['html']['skins'] = array();
	$data['configuration']['renderers']['html']['skins'][] = 'default';
}

ob_start();
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	print '<html><head>';
	include e4_html_findtemplate($data['configuration']['renderers']['html']['head']);
	print '</head><body>';
	$output = ob_get_contents();
ob_end_clean();
ob_start();
	// Allow for Markdown inside all body templates.
	include e4_html_findtemplate($data['configuration']['renderers']['html']['body']);
	$output .= Markdown(ob_get_contents());
ob_end_clean();
ob_start();
	print '</body></html>';
	$output .= ob_get_contents();
ob_end_clean();
	
print $output;


function e4_html_findtemplate($template){
	/*
	 * We need to find a template. Ideally we have a list of skins that we can use.
	 */
	global $data;
	$return = 'engine4.net/void.php';
	if (strlen($template) > 0){
		foreach($data['configuration']['renderers']['html']['skins'] as $skin){
			$return = e4_findinclude('templates/html/' . $skin . '/' . $template);
			if ($return !== 'engine4.net/void.php'){
				break;
			}
		}	
	}
	
	return $return;
}
?>