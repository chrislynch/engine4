<div class="span-15 append-1 last product-view">
    <div class="span-8 product-image">
        <a href="<?=@$content['link'];?>">
            <img src="<?php print $content['data']['files']['images']['primary']['path']; ?>">
        </a>
    </div>
    <div class="span-7 last product-data">
        <div class="span-7 last product-price">
            <span class="wasprice">Was: &pound;<?=@$content['data']['product']['listprice'];?></span><br>
            <span class="sellingprice">Now: &pound;<?=@$content['data']['product']['sellingprice'];?></span><br>
            <span class="saving"><?=@$content['data']['product']['savingtext'];?></span>
        </div>
        <div class="span-7 last product-addtocart">
            <form action="@@configuration.basedir@@" method="POST">
                <input type="hidden" name="e4_action" value="cart">
                <input type="hidden" name="e4_cart_op" value="add">
                <input type="hidden" name="e4_cart_item" value="<?=$content['ID'];?>">
                <input type="submit" class="button-highlight" value="Buy Now">
            </form>
        </div>
    </div>
    <div class="span-15 append-1 last">
        <h2>Description</h2>
        <?=$content['data']['body'];?>
        <h2>Specifications</h2>
        <?=$content['data']['body'];?>
        <h2>Reviews</h2>
        This product has no reviews.
    </div>
</div>
<div class="span-8 last">
    <h3>Other Bestsellers</h3>
</div>
