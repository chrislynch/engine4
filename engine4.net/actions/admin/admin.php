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
	
	$data['configuration']['renderers']['html']['skins'] = array('_admin','_default');
	
	$data['page']['head']['stylesheet'][] = 'engine4.net/templates/html/_admin/benevolentdictator/BenevolentDictator.css';
        $data['page']['head']['stylesheet'][] = 'engine4.net/lib/blueprint/src/typography.css';
        $data['page']['head']['stylesheet'][] = 'engine4.net/lib/blueprint/src/form.css';
	$data['page']['head']['stylesheet'][] = 'engine4.net/templates/html/_admin/css/engine4.css';
	
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
	if (isset($_REQUEST['e4_admin_op'])){
		switch ($_REQUEST['e4_admin_op']){
			case 'save':
				// Save the data that has been submitted, assuming we can.
				// Automatically drop into the edit page again, overriding the ID parameter in case this was a create.
				$savedID = e4_admin_admin_formData_save();
				if ($savedID !== $_REQUEST['e4_ID']){
					e4_data_load($savedID);
				}
				
			case 'edit':
                                e4_data_search();   // Run search to load the content we are editing
				$data['configuration']['renderers']['all']['templates'][1] = 'edit-data.php';
				break;
                            
                        case 'raw':
                            // View the raw XML of an item
                            e4_data_search();
                            $data['configuration']['renderers']['all']['templates'][1] = 'raw-data.php';
                            break;
				
			case 'create':
				if(isset($_REQUEST['e4_adminType'])){
					$content = e4_data_new($_REQUEST['e4_adminType']);
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
                                
                        case 'import':
                            e4_admin_admin_Import_CSV_Fields();
                            $data['configuration']['renderers']['all']['templates'][1] = 'import-data.php';
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
function e4_admin_admin_formData_save($forceContent = array()){
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
	
        // Make sure this content is owned by someone.
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
                // Add a tag
                
                $key = explode('_',$key);
                if (!isset($content['tags'][$key[3]])){ $content['tags'][$key[3]] = array();}
                
                if (!is_array($value)){
                    $value = explode(',',$value);
                }
                
                foreach($value as $realvalue){
                    $content['tags'][$key[3]][] = trim($realvalue);
                }
            }

	}
        
        foreach($_FILES as $key=>$file){
            
            if ($file['error'] == 0){
                // Move the uploaded file to a sensible place.
                e4_trace('Copying uploaded file from ' . $file["tmp_name"] . ' to ' . e4_domaindir() . 'uploads/' . $content['ID'] . '/' . $file['name']);

                if (!is_dir(e4_domaindir() . '/uploads/' . $content['ID'])){mkdir(e4_domaindir() . 'uploads/' . $content['ID']);}

                move_uploaded_file($file["tmp_name"], e4_domaindir() . 'uploads/' . $content['ID'] . '/' . $file['name']);
                e4_trace('Adding ' . $file['name'] . ' to content data');

                if(!isset($content['data']['files'])){$content['data']['files'] = array();}

                $file['path'] = e4_domaindir() . 'uploads/' . $content['ID'] . '/' . $file['name'];
                $key = str_ireplace('e4_form_files_', '', $key);	// Get rid of the pre-amble. This was only to identify our form elements.
                $key=explode('_',$key);								// Explode the key at the underscores

                switch (sizeof($key)){
                        case 1:
                                $content['data']['files'][$key[0]] = $file;
                                break;
                        case 2:
                                if (!isset($content['data']['files'][$key[0]])){ $content['data']['files'][$key[0]] = array();}
                                $content['data']['files'][$key[0]][$key[1]] = $file;
                                break;
                        case 3:
                                if (!isset($content['data']['files'][$key[0]])){ $content['data']['files'][$key[0]] = array();}
                                if (!isset($content['data']['files'][$key[0]][$key[1]])){ $content['data']['files'][$key[0]][$key[1]] = array();}
                                $content['data']['files'][$key[0]][$key[1]][$key[2]] = $file;
                                break;
                }
            } else {
                // There has been an error with the file upload.
                if ($file['name'] !== ''){
                    e4_message('There is a problem with your file "' . $file['name'] . '"<br>. The file may be too large, an incompatible file type, or may have been corrupted during transmission', 'Error');
                }                
            }
        }
        
        // Check to make sure that we have not lost any files
        foreach($_POST as $key=>$value){
            if (strstr($key,'previous_e4_form_files_')){
                
                $key = str_ireplace('previous_e4_form_files_', '', $key);
                $key=explode('_',$key);								// Explode the key at the underscores

                switch (sizeof($key)){
                    case 1:
                            if (!isset($content['data']['files'][$key[0]])){
                                $content['data']['files'][$key[0]] = unserialize(base64_decode($value));
                            }
                            break;
                    case 2:
                            if (!isset($content['data']['files'][$key[0]][$key[1]])){
                                if (!isset($content['data']['files'][$key[0]])){ $content['data']['files'][$key[0]] = array();}
                                $content['data']['files'][$key[0]][$key[1]] = unserialize(base64_decode($value));
                            }
                            break;
                    case 3:
                            if (!isset($content['data']['files'][$key[0]][$key[1]][$key[2]])){
                                if (!isset($content['data']['files'][$key[0]])){ $content['data']['files'][$key[0]] = array();}
                                if (!isset($content['data']['files'][$key[0]][$key[1]])){ $content['data']['files'][$key[0]][$key[1]] = array();}
                                $content['data']['files'][$key[0]][$key[1]][$key[2]] = unserialize(base64_decode($value));
                            }
                            break;
                }    
            }
        }
        
        /*
         * Validate the data that we are saving using our validators
         */
        include_once e4_findinclude('data-types/all.php');
        include_once e4_findinclude('data-types/' . $content['type'] . '.php');
        
        e4_datatype_all_validate($content);
        
        if (function_exists('e4_datatype_' . $content['type'] . '_validate')){
            $parameters = array( &$content );
            call_user_func_array('e4_datatype_' . $content['type'] . '_validate', $parameters);
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

function e4_admin_admin_formData_buildInput_selectorcheckorradio($inputType,$inputName,$contentData = array(),$inputData = array()){
    /*
     * Build an input widget of either radio, select, or checkbox type
     * Ensure that the selected or checked parameters are set based on the content of the item
     */
    
    $return = '';
    if (!(is_array($contentData))){
        $contentData = array($contentData => $contentData);
    }
    
    if ($inputType == 'select'){ $return = '<select name="' . $inputName . '">'; }
    
    foreach($inputData as $value=>$text){
        switch($inputType){
            case 'select':
                $return .= '<option value="' . $value . '"';
                if (in_array($value, $contentData)){ $return .= ' selected="selected"';}
                $return .= '>' . $text . '</option>';
                break;
                
            case 'checkbox':
                $return .= '<input type="checkbox" name="' . $inputName . '" value="' . $value . '"';
                if (in_array($value, $contentData)){ $return .= ' checked="checked"';}
                $return .= '>' . $text . '<br>';
                break;
                
            case 'radio':
                $return .= '<input type="radio" name="' . $inputName . '" value="' . $value . '"';
                if (in_array($value, $contentData)){ $return .= ' checked="checked"';}
                $return .= '>' . $text . '<br>';
                break;
                
        }
    }
    
    return $return;
    
}

function e4_admin_admin_formData_buildInput_file($inputName,$content){
    /*
     * Create a new file upload. File inputs are simpler to locate in the data structure, 
     * so we can use this to locate the data in the content item
     */
    
    $return = '';
    
    $fileLocation = str_ireplace('e4_form_files_', '', $inputName);
    $fileLocation = explode('_',$fileLocation);
    
    if (isset($content['data']['files'][$fileLocation[0]][$fileLocation[1]])){
        // There is an existing file
        // Display a link to see it, and a checkbox to remove it.
        $return .= '<a target="_blank" href="@@configuration.basedir@@' . 
                    $content['data']['files'][$fileLocation[0]][$fileLocation[1]]['path'] . '">' .
                    @$content['data']['files']['images']['primary']['name'] . '</a><br>';
        $return .= '<input type="hidden" name="previous_' . $inputName . '" value=' . base64_encode(serialize($content['data']['files'][$fileLocation[0]][$fileLocation[1]])) . '">';
    } else {
        
    }
    $return .= '<input type="file" name="' . $inputName . '">';
    
    return $return;
}

function e4_admin_admin_Import_CSV_Fields(){
    // Import a CSV file, looking for field definitions in the first line of the file
    global $data;
    
    if (isset($_FILES['e4_import'])){
        $file = $_FILES['e4_import'];
        $fields = array();
        
        $delimiter = ',';
        $enclosure = '"';
        $escape = '\\';
        
        $data['page']['body']['importResult'] = '';
        
        $csvfilepath = e4_domaindir() . 'uploads/' . $file['name'];
        move_uploaded_file($file["tmp_name"], $csvfilepath);
        $csvfile = fopen($csvfilepath,'r');
        
        // Start by getting the list of all fields into an arry
        // Put our prefix on automatically, to make the file more readable.
        $fields = fgetcsv($csvfile,0,$delimiter,$enclosure,$escape);
        for ($fieldindex = 0; $fieldindex < count($fields); $fieldindex++) {
            $fields[$fieldindex] = 'e4_form_content_' . $fields[$fieldindex];
        }
        
        while ($record = fgetcsv($csvfile,0,$delimiter,$enclosure,$escape)){
            // For each record, populate the $_REQUEST object
            for ($fieldindex = 0; $fieldindex < count($fields); $fieldindex++) {
                $_POST[$fields[$fieldindex]] = $record[$fieldindex];
            }
            
            // Then call the admin form submission
            $saveID = e4_admin_admin_formData_save();
            
            if ($saveID > 0){
                $data['page']['body']['importResult'] .= 'Saved ' . $_POST['e4_form_content_name'] . ' as ID ' . $saveID . '<br>';
            } else {
                $data['page']['body']['importResult'] .= 'Unable to save ' . $_POST['e4_form_content_name'] . '<br>';
                $data['page']['body']['importResult'] .= '<pre>' . print_r($_POST,TRUE) . '</pre>';
            }
            
            // Clear out messages that we don't want to display
            cookie_set('messages','');
            $data['page']['messages'] = array();
            
            // Now unset everything for the next run
            for ($fieldindex = 0; $fieldindex < count($fields); $fieldindex++) {
                unset($_POST[$fields[$fieldindex]]);
            }
        }
    }
}

?>