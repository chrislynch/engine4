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
	$files = glob_recursive('engine4.net/content/*.html'); // See helper.php
	$startat = 0;
	$count = 10;
	$data['content'] = array();
	
	foreach($files as $file){
		if (!is_dir($file)){
			$startat --;
			if ($startat <= 0){
				$data['content'][] = e4_Load($file); 
				if (sizeof($data) >= $count){
					break;
				}	
			}
		}
	}
	
}

?>