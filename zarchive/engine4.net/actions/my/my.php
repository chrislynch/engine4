<?php

function e4_action_my_my_go(&$data){
    
    if (!@$data['user']['ID']>0){
        e4_goto('security/authenticate');
        $op = '';
    } else {
        if (!isset($_REQUEST['e4_mylistpoint_op'])){$_REQUEST['e4_mylistpoint_op'] = 'view';}
        $op = $_REQUEST['e4_mylistpoint_op'];
    }
    
    switch($op){
        case 'view':
            // Display a page based on the user's account.
            e4_data_load($data['user']['ID']);
            // $data['configuration']['renderers']['all']['templates'][0] = 'mylistpoint/mylistpoint-view.php*content';
    }
}
?>
