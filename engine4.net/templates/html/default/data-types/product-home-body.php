<div class="span-14 last product">
    <div class="span-4 product-image">
        <a href="<?=@$content['link'];?>">
            <img src="<?php print $content['data']['files']['images']['primary']['path']; ?>">
        </a>
    </div>
    <div class="span-10 last">
        <div class="span-10 last">
            <a href="<?=@$content['link'];?>">
                <h2><?=@$content['name'];?></h2>
            </a>
        </div>
        <div class="span-5">
            Was: &pound;<?=@$content['data']['product']['listprice'];?><br>
            <span class="sellingprice">Now: &pound;<?=@$content['data']['product']['sellingprice'];?></span><br>
            Saving:
        </div>
        <div class="span-5 last">
            <form action="@@configuration.basedir@@" method="POST">
                <input type="hidden" name="e4_action" value="cart">
                <input type="hidden" name="e4_cart_op" value="add">
                <input type="hidden" name="e4_cart_item" value="<?=$content['ID'];?>">
                <input type="submit" class="button-highlight" value="Buy Now">
            </form>
        </div>
    </div>
    <div class="span-10 last">
        <?=Markdown(@$content['data']['teaser']);?>
    </div>
</div>