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
	$locations[] = 'engine4.net';
	
	// Make sure we have decoded the filename
	$filename = str_ireplace('_', '/', $filename);
	
	foreach($locations as $location){
		$search = $location . '/' . $filetype . '/' . $filename . '.php';
		if (file_exists($search)){
			$return = $search;
			break;
		}
	}
	
	return $return;
	
}

?>