<?php

/*
 * "Contribute" action - this is what users who are not administrators
 * and do not have CMS access use to create content
 */

function e4_action_contribute_contribute_go(&$data){
    /*
     * Works in the same way as the admin action, but using contribution forms
     * and overriding the data from newData() when creating content
     */
    if (isset($_REQUEST['e4_op'])){
	switch ($_REQUEST['e4_op']){
            case 'save':
                
                break;
            case 'edit':
                
                break;
            case 'create':
                if(isset($_REQUEST['e4_contributeType'])){
                    
                }
                break;
        }
    }
}

?>
