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
                // @todo: Add code to call save function from main admin.
                include_once e4_findinclude('actions/admin/admin.php');
                $content = e4_data_new();               // Get a new content item
                $content['status'] = 1;                 // Override to default published status
                $content['type'] = $_REQUEST['e4_contributeType'];
                $savedID = e4_admin_save_formData($content);
                if ($savedID !== @$_REQUEST['e4_ID']){
                    e4_data_load($savedID);
                    if (is_numeric($savedID)){
                        // Our save was successful. Go and look at the item?
                    }
                }
                break;
            case 'edit':
                // @todo: Will we support editing of contributed items in the contribution form?
            case 'create':
                if(isset($_REQUEST['e4_contributeType'])){
                    $data['configuration']['renderers']['all']['templates'][0] = 'contribute.php';
                }
                break;
        }
    }
}

?>
