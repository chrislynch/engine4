<?php
/*
 * Validate a piece of content.
 * Attach a "valid" flag to the content if it is valid. Set messages if it isn't.
 */

function e4_datatype_user_validate(&$content){
    // Validate the user type.
    
    if (strlen($content['data']['password']) == 0){
        e4_message('All users must have a password','Error');
        $content['valid'] = FALSE;
        return $content;
    }
}

?>
