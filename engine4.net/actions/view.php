<?php
/*
 * Load the file from content and populate the data array
 */

require_once('engine4.net/lib/phpmarkdownextra/markdown.php');

if (strlen($content) > 0){
	// Find the content and output it
	$data = e4_Load($content);
} else {
	// Return our homepage.
	// OR just return a list of all content?
	/*
	 * Find files on a particular path, returning the most recent, up to a given limit on the number of files.
	 * Ignores directories and does not recurse down
	 */
	$mode = 'glob';
	$files = glob('content/' . $path . '/*.html'); 
		
	$return = array();
	
	foreach($files as $file){
		if (!is_dir($file)){
			$startat --;
			if ($startat <= 0){
				$return[] = $file; 
				if (sizeof($return) >= $count){
					break;
				}	
			}
		}
	}
	
	return $return;
	
}

?>