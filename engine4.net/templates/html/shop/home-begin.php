<div id="content-home-welcome" class="span-24 last">
    <h1>Welcome to Sunday Marketplace</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed consequat tempor faucibus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec ante massa, placerat eget consequat vitae, mollis varius mi. Ut non scelerisque orci. Maecenas sit amet erat erat, ut bibendum lacus. Aliquam sed varius orci. Maecenas eu est diam. Donec in lectus non neque ultricies facilisis. Phasellus rutrum nibh non lacus elementum quis luctus risus feugiat.</p>
</div>

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