<div class="product-4 centered">
    <div class="product-heading">NEW PRODUCT</div>
    <div class="product-image">
        <a href="<?=@$content['link'];?>">
            <img src="<?php print $content['data']['files']['images']['primary']['path']; ?>">
        </a>
    </div>
    
    <div class="product-name">
        <a href="<?=@$content['link'];?>">
            <h2><?=@$content['name'];?></h2>
        </a>
    </div>
    <div class="product-price">
        <span class="wasprice">Was: &pound;<?=@$content['data']['product']['listprice'];?></span><br>
        <span class="sellingprice">Now: &pound;<?=@$content['data']['product']['sellingprice'];?></span><br>
        <span class="saving">Saving:</span>
    </div>
    <div class="product-addtocart">
        <form action="@@configuration.basedir@@" method="POST">
            <input type="hidden" name="e4_action" value="cart">
            <input type="hidden" name="e4_cart_op" value="add">
            <input type="hidden" name="e4_cart_item" value="<?=$content['ID'];?>">
            <input type="submit" class="button-highlight" value="Buy Now">
        </form>
    </div>
</div>