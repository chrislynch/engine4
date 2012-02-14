<?php
/*
 * This is the admin file.
 * This is where our CMS lives, basically.
 */

function e4_action_admin_admin_go(&$data){
	/*
	 * Start by deciding what page we are going to view as our primary page
	 * and what page we are viewing as our sidebar
	 */
	$data['configuration']['renderers']['all']['templates'][0] = 'menu.php';
	
	$data['configuration']['renderers']['html']['skins'] = array('admin','default');
	
	$data['page']['head']['stylesheet'][] = 'engine4.net/templates/html/admin/benevolentdictator/BenevolentDictator.css';
	$data['page']['head']['stylesheet'][] = 'engine4.net/templates/html/admin/css/engine4.css';
	
	$data['page']['head']['scripting'][] =
		'var currentMenuItem = "";
			  $(document).ready(function() {
	          $("a.MenuTab").click(function() {
	           var contentId = "#" + $(this).attr("id").replace("MenuItem","MenuContent");
	           var menuContent = $(contentId).html();
			   if(menuContent == null){
				currentMenuItem = contentId;
				$("#MenuContentContainer").slideUp();
				$("#MenuContentContainer").html(menuContent);
				location.href=$(this).attr("href");
			   } else {
				$("#MenuContentContainer").html(menuContent);
				if(currentMenuItem != contentId){
					currentMenuItem = contentId;
					$("#MenuContentContainer").slideDown();
				} else {
					// $("#MenuContentContainer").slideUp();
				} 
			   }
	          });
			});';
	
	/*
	 * Then override these defaults if necessary
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
				$data['configuration']['renderers']['all']['templates'][1] = 'edit-data.php';
				
				break;
			case 'create':
				$data['configuration']['renderers']['all']['templates'][1] = 'create-data.php';
				break;
			
			case 'search':
				$data['configuration']['renderers']['all']['templates'][1] = 'search.php';
				break;
			
				
		}
	} else {
				$data['configuration']['renderers']['all']['templates'][1] = 'home.php';
	}
	
	$data['configuration']['renderers']['all']['templates'][2] = 'sidebar.php';	
}



/*
 * FORM SAVE FUNCTIONS - Reads form data and saves it to the e4_data table using functions from index.php
 */
function e4_admin_save_formData(){
	/*
	 * Parse through the submitted form data and build up a piece of content to save.
	 * Pass this piece of content over to the save function.
	 */
	$content = e4_data_new();
	
	foreach($_POST as $key=>$value){
		// We transform each posted value into a pointer into our $content
		// We do this by breaking down the key string and building the array structure in $content based on it.
		// At the moment, we only support three levels. @todo More levels to follow?
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