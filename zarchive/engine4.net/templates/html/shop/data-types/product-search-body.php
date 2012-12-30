<div class="span-16 last">
    <div class="product-search span-16 last">
        <div class="product-image span-6">
            <a href="<?=@$content['link'];?>">
                <img src="<?php print $content['data']['files']['images']['primary']['path']; ?>">
            </a>
        </div>
        <div class="span-10 last">
            <div class="product-name span-10 last">
                <a href="<?=@$content['link'];?>">
                    <h3><?=$content['name']?></h3>
                </a>
            </div>
            <div class="product-price span-6">
                <div class="wasprice">Was: &pound;<?=@$content['data']['product']['listprice'];?></div>
                <div class="sellingprice">Now: &pound;<?=@$content['data']['product']['sellingprice'];?></div>
                <div class="saving"><?=@$content['data']['product']['savingtext'];?></div>
            </div>
            <div class="product-addtocart span-4 last">
                <form action="@@configuration.basedir@@" method="POST">
                    <input type="hidden" name="e4_action" value="cart">
                    <input type="hidden" name="e4_cart_op" value="add">
                    <input type="hidden" name="e4_cart_item" value="<?=$content['ID'];?>">
                    <input type="submit" class="button-highlight" value="Buy Now">
                </form>
            </div>
        </div>
    </div>
</div>
