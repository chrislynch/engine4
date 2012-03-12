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
        $data['configuration']['renderers']['all']['templates'] = array();
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
				if(isset($_REQUEST['e4_adminType'])){
					$content = e4_data_new();
					$content['type'] = $_REQUEST['e4_adminType'];
                                        if(isset($_REQUEST['e4_adminTypeIsContent'])){
                                            $content['iscontent'] = $_REQUEST['e4_adminTypeIsContent'];
                                        }
					$data['page']['body']['content'][] = $content;
					$data['configuration']['renderers']['all']['templates'][1] = 'edit-data.php';
				} else {
					$data['configuration']['renderers']['all']['templates'][1] = 'create-data.php';	
				}
				
				break;
			
			case 'search':
				if(isset($_REQUEST['e4_adminType'])){
					e4_data_search(array('Type'=>$_REQUEST['e4_adminType']),TRUE,FALSE);
				} else {
					e4_data_search();
				}
				$data['configuration']['renderers']['all']['templates'][1] = 'search.php';
				break;
			
				
		}
	} else {
				$data['configuration']['renderers']['all']['templates'][1] = 'home.php';
	}
	if (isset($data['configuration']['renderers']['all']['templates'][1])){
            $data['configuration']['renderers']['all']['templates'][2] = 'sidebar.php';	
        }
	
}

/*
 * FORM SAVE FUNCTIONS - Reads form data and saves it to the e4_data table using functions from index.php
 */
function e4_admin_save_formData($forceContent = array()){
    global $data;
	/*
	 * Parse through the submitted form data and build up a piece of content to save.
	 * Pass this piece of content over to the save function.
	 */
        if (sizeof($forceContent) == 0){
            $content = e4_data_new();
        } else {
            $content = $forceContent;
        }
	
        // Now add in some standard data, to represent what we are saving
        if (!isset($content['linkages'])) { $content['linkages'] = array(); }
        $content['linkages']['owner'] = $data['user']['ID'];
        
	foreach($_POST as $key=>$value){
            // We transform each posted value into a pointer into our $content
            // We do this by breaking down the key string and building the array structure in $content based on it.
            // At the moment, we only support three levels. @todo More levels to follow?
            if(strstr($key,'e4_form_content_')){
                // Ignore files submissions - these are handled below
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
            } elseif(strstr($key,'e4_form_linkage_')){

                // Add a linkage.
                $key = explode('_',$key);
                $content['linkages'][$key[3]] = $value;   
            } elseif(strstr($key,'e4_form_tags_')){
                $key = explode('_',$key);
                if (!isset($content['tags'][$key[3]])){ $content['tags'][$key[3]] = array();}
                $value = explode(',',$value);
                foreach($value as $realvalue){
                    $content['tags'][$key[3]][] = trim($realvalue);
                }
            }

	}
        foreach($_FILES as $key=>$file){
            // @todo: Need a way to ensure that we remember our old file types.
            if ($file['error'] == 0){
                // Move the uploaded file to a sensible place.
                e4_trace('Copying uploaded file from ' . $file["tmp_name"] . ' to ' . e4_domaindir() . 'uploads/' . $content['ID'] . '/' . $file['name']);

                if (!is_dir(e4_domaindir() . '/uploads/' . $content['ID'])){mkdir(e4_domaindir() . 'uploads/' . $content['ID']);}

                move_uploaded_file($file["tmp_name"], e4_domaindir() . 'uploads/' . $content['ID'] . '/' . $file['name']);
                e4_trace('Adding ' . $file['name'] . ' to content data');

                if(!isset($content['data']['files'])){$content['data']['files'] = array();}

                $file['path'] = e4_domaindir() . 'uploads/' . $content['ID'] . '/' . $file['name'];
                $key = str_ireplace('e4_form_content_', '', $key);	// Get rid of the pre-amble. This was only to identify our form elements.
                $key=explode('_',$key);								// Explode the key at the underscores

                switch (sizeof($key)){
                        case 1:
                                $content['data'][$key[0]] = $file;
                                break;
                        case 2:
                                if (!isset($content['data'][$key[0]])){ $content['data'][$key[0]] = array();}
                                $content[$key[0]][$key[1]] = $file;
                                break;
                        case 3:
                                if (!isset($content['data'][$key[0]])){ $content['data'][$key[0]] = array();}
                                if (!isset($content['data'][$key[0]][$key[1]])){ $content['data'][$key[0]][$key[1]] = array();}
                                $content['data'][$key[0]][$key[1]][$key[2]] = $file;
                                break;
                }
            }
        }
        
        /*
         * Validate the data that we are saving using our validators
         */
        include_once e4_findinclude('data-types/all.php');
        include_once e4_findinclude('data-types/' . $content['type'] . '.php');
        
        e4_action_admin_validate($content);
        
        if (function_exists('e4_action_admin_validate_' . $content['type'])){
            $parameters = array( &$content );
            call_user_func_array('e4_action_admin_validate_' . $content['type'], $parameters);
        }
        
        if (isset($content['valid']) && $content['valid'] == TRUE){
            e4_trace('Submitted content is valid. Attempting save');
            $savedID = e4_data_save($content);
            e4_trace('Save result = ' . $savedID);
            return $savedID;
        } else {
            e4_message('Hit "Back" in your browser to go back to your changes and try again');
            return $content['ID'];
        }
	
}



?>