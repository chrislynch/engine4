<?php

function e4_datatype_order_new(&$newitem){
    /* Build the new item defaults. */
    global $data;
    
    // Everything must have a name, but how do you name an order?
    // Give it a GUID, so it is unique and we can find it again
    $newitem['name'] = uniqid('', TRUE);
    
    // Make sure orders are not treated as content
    $newitem['iscontent'] = 0;
    
    if (isset($data['cart'])){
        $newitem['data']['cart'] = $data['cart'];
    }
}

?>
