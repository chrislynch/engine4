<?php
/*
 * Load the file from content and populate the data array
 */

require_once('engine4.net/lib/phpmarkdownextra/markdown.php');

if (strlen($content) > 0){
	// Find the content and output it
	$contentfile = 'engine4.net/content/' . $content . '.html';
	$contentdatafile = 'engine4.net/data/' . $content . '.xml';
	
	if (file_exists($contentfile)){
		// Content starts with .html files. These files can contain Markdown as well as HTML
		$data['content'] = file_get_contents($contentfile);
		$data['content'] = Markdown($data['content']);
		
		// We also support wiki-style links to content. TODO: Apply these here?
		
		// Content that has a .html file might *also* have an equivalent .xml file in /data
		if (file_exists($contentdatafile)){
			$xml = file_get_contents($contentdatafile);
			$xml = simplexml_load_string($xml);
			$xml = get_object_vars($xml);
			$data = array_merge($data,$xml);
		}
	}	
} else {
	// Return our homepage.
	// OR just return a list of all content?
	
}

?>