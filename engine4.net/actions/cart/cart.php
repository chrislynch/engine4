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
        
        switch($_REQUEST['e4_cart_op']){
            case 'add':                
                if (isset($cart['items'][$_REQUEST['e4_cart_item']])){
                    $cart['items'][$_REQUEST['e4_cart_item']]['qty'] += $qty;
                } else {
                    $cart['items'][$_REQUEST['e4_cart_item']] = array('qty'=>$qty);
                }
                break;
            case 'update':
                $cart['items'][$_REQUEST['e4_cart_item']] = array('qty'=>$qty);
                break;
            case 'remove':
                unset($cart['items'][$_REQUEST['e4_cart_item']]);
                break;
            case 'empty':
                $cart['items'] = array();
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
}
?>
