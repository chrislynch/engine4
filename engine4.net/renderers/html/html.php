<?php

/*
 * Default viewing mechanism
 * This will need, somehow, to support some templates.
 * I've created a "templates" directory, just need to work out how this maps to the views, etc.
 */
require_once 'engine4.net/lib/phpmarkdownextra/markdown.php';

if(!isset($data['configuration']['renderers']['html']['skins'])){ 
	$data['configuration']['renderers']['html']['skins'] = array();
	$data['configuration']['renderers']['html']['skins'][] = 'default';
}

/*
 * Buffering output, start to include files in the right order.
 * Keep all the output until the end.
 */
ob_start();
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	print '<html><head>';
	include e4_findtemplate($data['configuration']['renderers']['templates']['head']);
	print '</head><body>';
	$output = ob_get_contents();
ob_end_clean();
ob_start();
	foreach($data['configuration']['renderers']['templates'] as $key=>$bodytemplate){
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

/*
 * Perform search and replace for shortcodes (like a Warp widget, but simpler)
 */
$output = str_ireplace('@@configuration.basedir@@', '/' . $data['configuration']['basedir'], $output);

print $output;

/*
 * RENDERER FUNCTIONS
 */

?>