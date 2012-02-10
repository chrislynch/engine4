<?php

/*
 * Default viewing mechanism
 * This will need, somehow, to support some templates.
 * I've created a "templates" directory, just need to work out how this maps to the views, etc.
 */

function e4_renderer_html_html_go($templates){
	global $data;
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
		include e4_findtemplate($templates['header']);
		foreach($templates as $key=>$bodytemplate){
			if ($key !== 'header' AND $key !== 'footer'){
				if($bodytemplate == '?'){
					$bodytemplate = e4_pickContentTemplate();	
				}
				include e4_findtemplate($bodytemplate);	
			}
		}
		include e4_findtemplate($templates['footer']);
		$output .= ob_get_contents();
	ob_end_clean();
	
	/*
	 * Perform search and replace for shortcodes (like a Warp widget, but simpler)
	 */
	$output = str_ireplace('@@configuration.basedir@@', '/' . $data['configuration']['basedir'], $output);
	
	print $output;	
}



/*
 * RENDERER FUNCTIONS
 */

?>