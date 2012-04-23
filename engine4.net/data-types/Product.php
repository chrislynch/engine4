<?php

function e4_datatype_Product_load(&$content){
    // Perform "load" operations.
    
    if (!isset($content['data']['product'])){ $content['data']['product'] = array();}
    if (!isset($content['data']['product']['listprice'])){ $content['data']['product']['listprice'] = 0.00;}
    if (!isset($content['data']['product']['sellingprice'])){ $content['data']['product']['sellingprice'] = 0.00;}
    
    // Calculate our saving data.
    if ($content['data']['product']['listprice'] > $content['data']['product']['sellingprice']) {
        $content['data']['product']['saving'] = $content['data']['product']['listprice'] - $content['data']['product']['sellingprice'];
        $content['data']['product']['savingtext'] = 'Saving: &pound;' . $content['data']['product']['saving'];
    } else {
        $content['data']['product']['saving'] = 0.00;
        $content['data']['product']['savingtext'] = '';
    }
}
?>
