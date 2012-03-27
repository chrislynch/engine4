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
	
        if (isset($data['renders']['all']['viewtype'])){
            $viewtype = $data['renders']['all']['viewtype'];
        } else {
            $viewtype = 'all';
        }
        
	/*
	 * Buffering output, start to include files in the right order.
	 * Keep all the output until the end.
	 */
	ob_start();
        include e4_findtemplate('header.php');
        include e4_findtemplate('body-begin.php');
            foreach($templates as $key=>$bodytemplate){
                    e4_trace('HTML Renderer processing template ' . $bodytemplate);
                    if ($key !== 'header' AND $key !== 'footer'){
                            if (strstr($bodytemplate,'*')){
                                    // This is a repeater
                                    $bodytemplate = explode('*',$bodytemplate);
                                    if ($bodytemplate[1] !== 'content'){ 
                                        //TODO: This needs to be rewritten - it doesn't actually work!
                                        $contentArray = $data['page']['body']['content'][$bodytemplate[1]]; 
                                    } else {
                                        $contentArray = $data['page']['body']['content'];
                                    }

                                    foreach($contentArray as $content){
                                        if($bodytemplate[0] == '?'){
                                            include e4_findtemplate(e4_pickContentTemplate($content, $viewtype, 'begin'));
                                            include e4_findtemplate(e4_pickContentTemplate($content, $viewtype, 'body'));
                                            include e4_findtemplate(e4_pickContentTemplate($content, $viewtype, 'end'));
                                        } else {
                                            include e4_findtemplate($bodytemplate[0]);		
                                        }
                                    }
                            } else {
                                    if (sizeof(@$data['page']['body']['content']) == 0){
                                            include e4_findtemplate($bodytemplate);
                                    } else {
                                        foreach($data['page']['body']['content'] as $content){
                                            if($bodytemplate == '?'){ 
                                                include e4_findtemplate(e4_pickContentTemplate($content, $viewtype, 'begin'));
                                                include e4_findtemplate(e4_pickContentTemplate($content, $viewtype, 'body'));
                                                include e4_findtemplate(e4_pickContentTemplate($content, $viewtype, 'end'));
                                            }  else { 
                                                $pickedbodytemplate = $bodytemplate;
                                                include e4_findtemplate($pickedbodytemplate);
                                            }

                                            break; // Only do this once. We just use the loop to get at the first item in the array without knowing its key
                                        }	
                                    }
                            }
                    }
            }
	include e4_findtemplate('body-end.php');
        include e4_findtemplate('footer.php');
	$output .= ob_get_contents();
	ob_end_clean();
	
	/*
	 * Perform search and replace for shortcodes (like a Warp widget, but simpler) that are global across the template.
	 */
	$output = str_ireplace('@@configuration.basedir@@', '/' . $data['configuration']['basedir'], $output);
	
	print $output;	
}

/*
 * RENDERER FUNCTIONS
 */

?>