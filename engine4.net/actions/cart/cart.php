<?php

function e4_action_cart_cart_go(&$data){
    // Load up our cart, if it exists.
    $cartstring = cookie_get('cart', serialize(array('items'=>array())));
    $cart = unserialize($cartstring);
    
    // Check to see if there are any cart actions due
    if (isset($_REQUEST['e4_cart_op'])){
        
        if (isset($_REQUEST['e4_cart_item_qty'])){
            $qty = $_REQUEST['e4_cart_item_qty'];
        } else {
            $qty = 1;
        }
        
        $cartitem = e4_data_load($_REQUEST['e4_cart_item'],FALSE,FALSE);
        $readyToCheckoutMessage = ' Are you ready to <a href="?e4_action=checkout">checkout</a>?';
        
        switch($_REQUEST['e4_cart_op']){
            case 'add':                
                if (isset($cart['items'][$_REQUEST['e4_cart_item']])){
                    $cart['items'][$_REQUEST['e4_cart_item']]['qty'] += $qty;
                    e4_message('Updated quantity of ' . $cartitem['name'] . ' to ' . $cart['items'][$_REQUEST['e4_cart_item']]['qty'] . ' in your cart.' . $readyToCheckoutMessage);
                } else {
                    $cart['items'][$_REQUEST['e4_cart_item']] = array('qty'=>$qty);
                    e4_message('Added ' . $cartitem['name'] . ' to your cart.' . $readyToCheckoutMessage);
                }                
                break;
            case 'update':
                $cart['items'][$_REQUEST['e4_cart_item']] = array('qty'=>$qty);
                e4_message('Updated quantity of ' . $cartitem['name'] . ' to ' . $qty . ' in your cart' . $readyToCheckoutMessage);
                break;
            case 'remove':
                unset($cart['items'][$_REQUEST['e4_cart_item']]);
                e4_message('Removed ' . $cartitem['name'] . ' from your cart');
                break;
            case 'empty':
                $cart['items'] = array();
                e4_message('Your cart is now empty.');
                break;
        }
    }
    
    // Serialise and save the cart
    cookie_set('cart', serialize($cart));
    
    // Calculate the totals for the cart
    $cart['totalitems'] = 0;
    $cart['totalvalue'] = 0.00;
    
    foreach($cart['items'] as $cart_item_code=>$cart_item_data){
        $cart['totalitems'] += $cart_item_data['qty'];
        $cart_item_data = e4_data_load($cart_item_code, FALSE, FALSE);
        $cart['totalvalue'] += @$cart_item_data['data']['product']['sellingprice'];
    }
    
    $data['cart'] = $cart;
    
    if (isset($_REQUEST['e4_cart_op']) && $_REQUEST['e4_cart_op'] == 'view'){
        e4_action_cart_cart_view($data);
    }
}

function e4_action_cart_cart_view(&$data){
    /*
     * Load data from the cart into the content array so that we can display it.
     */
    foreach($data['cart']['items'] as $cartitemID=>$cartitem){
        e4_data_load($cartitemID);
        $data['page']['body']['content'][$cartitemID]['cart'] = $cartitem;
    }
    
    $data['configuration']['renderers']['all']['templates'][-1] = 'forms/cart/cart-begin.php';
    $data['configuration']['renderers']['all']['templates'][0] = 'forms/cart/cart-body.php*content';
    $data['configuration']['renderers']['all']['templates'][1] = 'forms/cart/cart-end.php';
    
}
?>
