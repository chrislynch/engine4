<div id="content-products-latest" class="span-24 last">
    <div class="span-24 last"><h2>New Products</h2></div>
    <?php
        foreach($data['page']['body']['contentByType'] as $type=>$contentarray){
            template_shop_home_begin_grid($contentarray);
        }
        
    ?>
</div>

<div id="content-products-bestsellers" class="span-24 last">
    <div class="span-24 last"><h2>Best Sellers</h2></div>
    <?php
        foreach($data['page']['body']['contentByType'] as $type=>$contentarray){
            template_shop_home_begin_grid($contentarray);
        }
        
    ?>
</div>

<?php
//TODO: This should be a general gridding function ?
function template_shop_home_begin_grid($contentarray){
    $grid = 0;
    $items = 0;
    $last = '';
    $cols = 6;
    $rows = 1;
    
    $maxitems = $cols * $rows;
    foreach($contentarray as $ID=>$content){
        $grid ++;
        $items ++;
        if ($grid % $cols == 0) { $last = ' last '; } else { $last = ''; }
        include e4_findtemplate('data-types/product-home.php');
        if ($items == $maxitems){
            break;
        }
    }
}

?>