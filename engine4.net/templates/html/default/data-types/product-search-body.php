<div class="span-18 last">
    <div class="span-3 append-1">
        <br>
        <a href="<?=@$content['link'];?>">
            <img src="<?php print $content['data']['files']['images']['primary']['path']; ?>" width="100%">
        </a>
    </div>
    <div class="span-12 last">
        <div class="span-12 last">
            <a href="<?=@$content['link'];?>">
                <h2><?=@$content['name'];?></h2>
            </a>
        </div>
        <div class="span-6">
            <span class="sellingprice">&pound;<?=@$content['data']['product']['sellingprice'];?></span><br>
            Was: &pound;<?=@$content['data']['product']['listprice'];?><br><br>
        </div>
        <div class="span-6 last">
            <form action="#" method="POST">
                <input type="hidden" name="e4_action" value="cart">
                <input type="hidden" name="e4_cart_op" value="add">
                <input type="hidden" name="e4_cart_item" value="<?=$content['ID'];?>">
                <input type="submit" value="Add to Cart">
            </form>
        </div>
        <div class="span-12 last">
            <small><?=@$content['data']['teaser'];?></small>
        </div>
    </div>
</div>