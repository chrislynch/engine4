<?php
/*
 * HELPER FUNCTIONS FOR INDEX.PHP
 */

function e4_find($filetype,$filename){
	/*
	 * Look for a particular type of file (action, view, template, etc.) and return the path to it.
	 * Look for it in the domain specific directory first, and then (if not found) try defaults all the way up to engine4.net
	 */
	
	// Set up return
	$return = '';
	
	// Build array of potential locations
	$locations = array();
	// $locations[] = 'www.planetofthepenguins.com';
	$locations[] = 'engine4.net';
	
	// Make sure we have decoded the filename
	$filename = str_ireplace('_', '/', $filename);
	
	// Pick our extension
	switch($filetype){
		case 'content': $extension = '.html'; break;
		case 'data': $extension = '.xml'; break;
		case 'templates': $extension = '.html'; break;
		default: $extension = '.php';
	}
	
	foreach($locations as $location){
		$search = $location . '/' . $filetype . '/' . $filename . $extension;
		if (file_exists($search)){
			$return = $search;
			break;
		}
	}
	
	return $return;
	
}

function e4_Load($content){
	$content = str_ireplace('engine4.net/content/', '', $content);
	$content = str_ireplace('.html', '', $content);
	
	$contentfile = e4_find('content',$content);
	$contentdatafile = e4_find('data',$content);

	$return = array();
	
	if (file_exists($contentfile)){
		$return['content'] = e4_loadContent($contentfile);
	}	
	if (file_exists($contentdatafile)){
		$return = array_merge($return,e4_loadData($contentdatafile));
	}
	
	return $return;
}

function e4_loadContent($contentfile){
	// Content starts with .html files. These files can contain Markdown as well as HTML
	$return = file_get_contents($contentfile);
	$return = Markdown($return);
	// We also support wiki-style links to content. TODO: Apply these here?
	return $return;
}

function e4_loadData($contentfile){
	$xml = file_get_contents($contentdatafile);
	$xml = simplexml_load_string($xml);
	$xml = get_object_vars($xml);
	return $xml;
}

// http://www.php.net/manual/en/function.glob.php#106595
if ( ! function_exists('glob_recursive'))
{
    // Does not support flag GLOB_BRACE
    function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}
?>