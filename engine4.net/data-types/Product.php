<?php

function e4_datatype_Product_load(&$content){
    // Perform "load" operations.
    
    // Calculate our saving data.
    if ($content['data']['product']['listprice'] > $content['data']['product']['sellingprice']) {
        $content['data']['product']['saving'] = $content['data']['product']['listprice'] - $content['data']['product']['sellingprice'];
    }
}
?>
