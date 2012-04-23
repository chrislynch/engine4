<script type="text/javascript">

    this.randomtip = function(){

            var pause = 3000; // define the pause for each tip (in milliseconds) 
            var length = $("#banner-top .banner-item").length; 
            var temp = -1;		

            this.getRan = function(){
                    // get the random number
                    var ran = Math.floor(Math.random() * length) + 1;
                    return ran;
            };
            this.show = function(){
                    var ran = getRan();
                    // to avoid repeating
                    while (ran == temp){
                            ran = getRan();
                    };
                    // $("#banner-top .banner-item:nth-child(" + temp + ")").fadeOut("fast");
                    $("#banner-top .banner-item").hide();	
                    $("#banner-top .banner-item:nth-child(" + ran + ")").fadeIn("fast");
            };

            show(); 
            setInterval(show,pause);

    };

    $(document).ready(function(){	
            randomtip();
    });

</script>

<div id="banner-top" class="span-24 last">
    <div class="banner-item span-24 last">
        <img src="engine4.net/templates/html/shop/images/banner.png">
        <h3>Acer Portable CD Player</h3>
        <p>The compact disc is back and better than ever - GQ</p>
        <div class="product-price-was">RRP: £9.99&nbsp;</div><div class="product-price-selling">Our Price: £4.99!</div>
    </div>
    <div class="banner-item span-24 last">
        <img src="engine4.net/templates/html/shop/images/banner.png">
        <h3>Acer Portable DVD Player</h3>
        <p>An incredibly small and versatile unit - Stuff Magazine</p>
        <div class="product-price-was">RRP: £89.99&nbsp;</div><div class="product-price-selling">Our Price: £17.99!</div>
    </div>
</div>
<div id="banner-bottom" class="span-24 last">

</div>

<div id="content-home-welcome" class="span-24 last">
    <h1>Welcome to Sunday Marketplace</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed consequat tempor faucibus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec ante massa, placerat eget consequat vitae, mollis varius mi. Ut non scelerisque orci. Maecenas sit amet erat erat, ut bibendum lacus. Aliquam sed varius orci. Maecenas eu est diam. Donec in lectus non neque ultricies facilisis. Phasellus rutrum nibh non lacus elementum quis luctus risus feugiat.</p>
</div>

<div id="content-products-latest" class="span-24 last">
    <div class="span-24 last"><h2>New Products</h2></div>
    <?php
        template_shop_home_begin_grid($data['page']['body']['contentByTag']['productpromo']['promo_new_product']);
        template_shop_home_begin_grid($data['page']['body']['contentByTag']['productpromo']['promo_new_product']);
    ?>
</div>

<div id="content-products-bestsellers" class="span-24 last">
    <div class="span-24 last"><h2>Best Sellers</h2></div>
    <?php
        template_shop_home_begin_grid($data['page']['body']['contentByTag']['productpromo']['promo_best_seller']);
        template_shop_home_begin_grid($data['page']['body']['contentByTag']['productpromo']['promo_new_product']);
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
        include e4_findtemplate('data-types/product-home-body.php');
        if ($items == $maxitems){
            break;
        }
    }
}

?>