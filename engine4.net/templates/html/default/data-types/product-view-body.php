<div class="span-18 last product">
    <div class="span 18 last product-name">
        
    </div>
    <div class="span-7 append-1 product-image">
	<img src="<?php print $content['data']['files']['images']['primary']['path']; ?>">
    </div>
    <div class="span-10 last">
        <div class="span-10 last">
            <h1><?=@$content['name'];?></h1>
            <small>Product Code:<?=@$content['data']['product']['sku'];?></small>
            <hr>
        </div>
        <div class="span-5">
            Was: &pound;<?=@$content['data']['product']['listprice'];?><br>
            <span class="sellingprice">Now: &pound;<?=@$content['data']['product']['sellingprice'];?></span><br>
            Saving:
        </div>
        <div class="span-5 last">
            <form action="#" method="POST">
                <input type="hidden" name="e4_action" value="cart">
                <input type="hidden" name="e4_cart_op" value="add">
                <input type="hidden" name="e4_cart_item" value="<?=$content['ID'];?>">
                <input type="submit" value="Add to Cart">
            </form>
        </div>
        <div class="span-10 last">
            <hr>
            <p>In Stock: Will be despatched within 24 hours.</p>
            <p>Rating: 5/5 from 16 buyers.</p>
        </div>
        
    </div>
    <div class="span-18 last">
        <h2>Description</h2>
        <?=Markdown(@$content['data']['body']);?>
    </div>
</div>
