<?php

function e4_action_admin_validate(&$content){
    // Establish a "state of grace" on new data - all data is good until we find out otherwise.
    $content['valid'] = TRUE;
    
    if (strlen($content['name']) == 0){
        // All things must have a name
        e4_message('All things must have a unique name. Name cannot be blank.','Error');
        $content['valid'] = FALSE;
        return $content;
    } else {
        // Check to make sure this name is unique
        e4_trace('Looking for another item with this name ...');
        $namecheck = e4_data_search(array('name'=>$content['name']),FALSE,FALSE,TRUE);
        e4_trace('Found ' . sizeof($namecheck));
        if (sizeof($namecheck) > 0){
            foreach($namecheck as $ID=>$item){
                if (strval($ID) !== strval($content['ID'])){
                    e4_message('The name ' . $content['name'] . ' is already taken by item ' . $ID . '(' . $item['name'] .'). Your ID is ' . $content['ID'] . 
                                '<br>All things must have a unique name.','Error');
                    $content['valid'] = FALSE;            
                }
            }
        }
    }
}
?>
