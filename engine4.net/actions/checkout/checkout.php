<?php

function e4_action_checkout_checkout_go(&$data){
    /*
     * Perform the checkout process.
     * This action should be able to display one or more stages at the same time using templates.
     */
    
    $data['configuration']['renderers']['all']['templates'][0] = 'forms/checkout/address.php';
    $data['configuration']['renderers']['all']['templates'][1] = 'forms/checkout/payment.php';
    
}

?>
