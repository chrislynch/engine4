<h1>Edit Content</h1>

<?php

print admin_content_edit_save();
print admin_content_edit($this);

function admin_content_edit(&$e){
    
    // Open up the item that we are editing
    /*
    $things = e::_search($_GET['directory']);
    if (sizeof($things) == 1){
        $thing = array_shift($things);
    } else {
        // This is an error condition - we should not be editing a path that has dupes
    }
    */
    
    // Open up the template that we are working on
    if (!isset($_GET['template'])) { $_GET['template'] = 'plaintext'; }
    ob_start();
    include('10.content/admin/content/edit/_templates/' . $_GET['template'] . '.inc');
    $template = ob_get_contents();
    ob_end_clean();
    
    $return = "<form action='admin/content/edit?save=save&template={$_GET['template']}&directory={$_GET['directory']}' method='POST'>";
    $return .= $template;
    $return .= "<input name='save' type='submit' value='Save Content'>";
    $return .= "</form>";
    
    return $return;
}

function admin_content_edit_save(){
    
    $data = array();
    
    if(isset($_GET['save'])){
        foreach($_POST as $key=>$value){
            if(strstr($key,'_')){
                $key = explode('_',$key);
                $filetype = $key[0];
                $format = $key[1];
                if (isset($key[2])){ $field = $key[2];} else { $field = ''; }
                    if(isset($data[$filetype])){
                        $data[$filetype][$field] = $value;
                    } else {
                        if (strlen($field) > 0){
                            $data[$filetype] = array();
                            $data[$filetype][$field] = $value;
                        } else {
                            $data[$filetype] = $value;
                        }
                    }
                
            }
        }
    }
    
    $return = print_r($data,TRUE);
    
    if (isset($_GET['directory'])){
        $filename = $_GET['directory'];
        $filename = explode('/',$filename);
        $filename = array_pop($filename);
    } else {
        if (isset($data['title'])){
            // Turn the title into a filename
        }
    }
    
    foreach($data as $key=>$value){
        if(is_array($value)){
            // TODO: Deal with the saving of fields
        } else {
            if (file_put_contents($_GET['directory'] . '/' . $filename . '.' . $filetype, $value)){
                print "<strong>Success:</strong> Saved " . $_GET['directory'] . '/' . $filename . '.' . $key . '<br>';
            } else {
                print "<strong>Error:</strong> Unable to save " . $_GET['directory'] . '/' . $filename . '.' . $key . '<br>';
            }
        }
    }
    
    return $return;
}

?>