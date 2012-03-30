<?php

function e4_datatype_order_new(&$newitem){
    /* Build the new item defaults. */
    
    // Everything must have a name, but how do you name an order?
    // Give it a GUID, so it is unique and we can find it again
    $newitem['name'] = uniqid('', TRUE);
    print 'Set name to ' . $newitem['name'];
}

?>
