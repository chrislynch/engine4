<?php
/*
 * This is the admin file.
 * This is where our CMS lives, basically.
 */

/*
 * Start by deciding what page we are going to view as our primary page
 * and what page we are viewing as our sidebar
 */
$data['configuration']['renderers']['html']['body-content'] = 'templates/html/admin/home.php';
$data['configuration']['renderers']['html']['sidebar-left'] = 'templates/html/admin/sidebar-left.php';
$data['configuration']['renderers']['html']['sidebar-right'] = 'templates/html/admin/sidebar-right.php';

/*
 * Inform the CMS if it needs to load some addition Javascript and CSS files
 */


if (isset($_REQUEST['e4_op'])){
	switch ($_REQUEST['e4_op']){
		case 'save':
			// Save the data that has been submitted, assuming we can.
			// Automatically drop into the edit page again, overriding the ID parameter in case this was a create.
			$savedID = e4_admin_save_formData();
			if ($savedID !== $_REQUEST['e4_ID']){
				e4_data_load($savedID);
			}
		case 'edit':
			$data['configuration']['renderers']['html']['body-content'] = 'templates/html/admin/edit-data.php';
			break;
		case 'create':
			$data['configuration']['renderers']['html']['body-content'] = 'templates/html/admin/create-data.php';
			break;
		
		case 'search':
			$data['configuration']['renderers']['html']['body-content'] = 'templates/html/admin/search.php';
			break;
	}
}

function e4_admin_save_formData(){
	/*
	 * Parse through the submitted form data and build up a piece of content to save.
	 * Pass this piece of content over to the save function.
	 */
	$content = array('ID'=>0,'name'=>'','type' => '');
	
	foreach($_POST as $key=>$value){
		// We transform each posted value into a pointer into our $content
		// We do this by breaking down the key string and building the array structure in $content based on it.
		// At the moment, we only support three levels. TODO: More levels to follow?
		if(strstr($key,'e4_form_content_')){
			$key = str_ireplace('e4_form_content_', '', $key);	// Get rid of the pre-amble. This was only to identify our form elements.
			$key=explode('_',$key);								// Explode the key at the underscores
			switch (sizeof($key)){
				case 1:
					$content[$key[0]] = $value;
					break;
				case 2:
					if (!isset($content[$key[0]])){ $content[$key[0]] = array();}
					$content[$key[0]][$key[1]] = $value;
					break;
				case 3:
					if (!isset($content[$key[0]])){ $content[$key[0]] = array();}
					if (!isset($content[$key[0]][$key[1]])){ $content[$key[0]][$key[1]] = array();}
					$content[$key[0]][$key[1]][$key[2]] = $value;
					break;
			}
		}
	}
	$savedID = e4_data_save($content);
	return $savedID;
}



?>