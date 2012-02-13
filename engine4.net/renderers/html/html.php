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
			e4_trace('HTML Renderer processing template ' . $bodytemplate);
			if ($key !== 'header' AND $key !== 'footer'){
				if (strstr($bodytemplate,'*')){
					// This is a repeater
					$bodytemplate = explode('*',$bodytemplate);
					if ($bodytemplate[1] !== 'content'){ 
						$contentArray = $data['page']['body']['content'][$bodytemplate[1]]; 
					} else {
						$contentArray = $data['page']['body']['content'];
					}
					foreach($contentArray as $content){
						if($bodytemplate[0] == '?'){
							$bodytemplate[0] = e4_pickContentTemplate($content);	
						}
						include e4_findtemplate($bodytemplate[0]);		
					}
				} else {
					foreach($data['page']['body']['content'] as $content){
						if($bodytemplate == '?'){ $bodytemplate = e4_pickContentTemplate($content); }
						include e4_findtemplate($bodytemplate);	
						break; // Only do this once. We just use the loop to get at the first item in the array without knowing its key
					}
				}
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