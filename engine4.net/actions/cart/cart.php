<?php

function e4_action_cart_cart_go(&$data){
    // Load up our cart, if it exists.
    if (isset($_COOKIE['e4_cart'])){
        $cart = unserialize($_COOKIE['e4_cart']);
        
        $cart['totalitems'] = 0;
        $cart['totalvalue'] = 0.00;
        
    } else {
        $cart = array();
        $cart['items'] = array();
        $cart['totalitems'] = 0;
        $cart['totalvalue'] = 0.00;
    }
    
    $data['cart'] = $cart;
}
?>
