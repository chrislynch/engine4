<?php

function e4_action_checkout_checkout_go(&$data){
    /*
     * Perform the checkout process.
     * This action should be able to display one or more stages at the same time using templates.
     */
    if (!isset($data['cart']) || (isset($data['cart']) && $data['cart']['totalitems'] == 0)){
        // This user does not have a cart, so cannot checkout. Bounce them off back to the cart.
        e4_message('You cart is currently empty. Please add some items to your cart before you check-out');
        e4_goto('cart/view');
    } else {
        // Establish checkout as the viewtype
        $data['renders']['all']['viewtype'] = 'checkout';
        
        if (isset($_REQUEST['e4_checkout_op'])){
            // Some checkout data has been submitted.
            // We can use the admin action to save this data
            include_once e4_findinclude('actions/admin/admin.php');
            
            // Check to see if we already have an order in progress
            $orderID = cookie_get('orderID', 0);
            if ($orderID == 0){
                // Create a new order
                $order = e4_data_new('order');
            } else {
                $order = e4_data_load($orderID, FALSE);
            }
            
            // Now use the admin features to write to the order
            $orderID = e4_admin_save_formData($order);

            // Is this the last post, or is there more to come?
            
            // Set our cookie to make sure we can still see our order on the next post
            
            // If complete, set the thankyou page up and clear the cookies
            $data['configuration']['renderers']['all']['templates'][0] = 'forms/checkout/complete.php';
            cookie_set('completed_orderID',$orderID);
            cookie_set('orderID',0);
            
            // Before we unset the cart, add linkages so that we can track the purchase.
            foreach($data['cart']['items'] as $cartItemID=>$cartItem){
                e4_data_save_link($data['user']['ID'], $cartItemID, 'purchaser');
            }
            
            unset($data['cart']);
            cookie_set('cart','');
            
        } else {
            // We are at the very start of the checkout process.
            // Show the starting forms.
            $data['configuration']['renderers']['all']['templates'][0] = 'forms/checkout/address.php';
            $data['configuration']['renderers']['all']['templates'][1] = 'forms/checkout/payment.php';
        }
    }
    
}

?>
