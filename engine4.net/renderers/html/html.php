<?php

/*
 * Default viewing mechanism
 * This will need, somehow, to support some templates.
 * I've created a "templates" directory, just need to work out how this maps to the views, etc.
 */
require_once 'engine4.net/lib/phpmarkdownextra/markdown.php';

if(!isset($data['configuration']['renderers']['html'])){ $data['configuration']['renderers']['html'] = array();}
if(!isset($data['configuration']['renderers']['html']['templates'])){ 
	$data['configuration']['renderers']['html']['templates'] = array();
	$data['configuration']['renderers']['html']['templates']['head'] = 'head.php';	
} 
if (!isset($data['configuration']['renderers']['html']['templates']['head'])){
	// Ensure we **always** have a header, even if someone forgets to put one in the templates array
	$data['configuration']['renderers']['html']['templates']['head'] = 'head.php';
}
if (sizeof($data['configuration']['renderers']['html']['templates']) == 1){
	// Ensure we **always** at least one body template.
	// This is also the mechanism for default template selection
	$data['configuration']['renderers']['html']['templates'][] = 'home.php';
}

if(!isset($data['configuration']['renderers']['html']['skins'])){ 
	$data['configuration']['renderers']['html']['skins'] = array();
	$data['configuration']['renderers']['html']['skins'][] = 'default';
}

/*
 * Sort the templates, to allow for appending a pre-pending templates
 */
ksort($data['configuration']['renderers']['html']['templates']);

/*
 * Buffering output, start to include files in the right order.
 * Keep all the output until the end.
 */
ob_start();
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	print '<html><head>';
	include e4_findtemplate($data['configuration']['renderers']['html']['templates']['head']);
	print '</head><body>';
	$output = ob_get_contents();
ob_end_clean();
ob_start();
	foreach($data['configuration']['renderers']['html']['templates'] as $key=>$bodytemplate){
		if ($key !== 'head'){
			if($bodytemplate == '?'){
				$bodytemplate = e4_pickContentTemplate();	
			}
			include e4_findtemplate($bodytemplate);	
		}
	}
	$output .= ob_get_contents();
ob_end_clean();
ob_start();
	print '</body></html>';
	$output .= ob_get_contents();
ob_end_clean();
	
print $output;

/*
 * RENDERER FUNCTIONS
 */

?>