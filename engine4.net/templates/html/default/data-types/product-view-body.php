<div class="span-7 append-1">
	<img src="<?php print $content['data']['files']['images']['primary']['path']; ?>">
</div>
<div class="span-10 last">
    <div class="span-10 last">
        <h1><?=@$content['name'];?></h1>
        <small>Product Code:<?=@$content['data']['product']['sku'];?></small><br><br>
        <hr>
    </div>
    <div class="span-5">
        <span class="sellingprice">&pound;<?=@$content['data']['product']['sellingprice'];?></span><br>
        Was: &pound;<?=@$content['data']['product']['listprice'];?><br>
    </div>
    <div class="span-5 last">
        <form action="#" method="POST">
            <input type="hidden" name="e4_action" value="cart">
            <input type="hidden" name="e4_cart_op" value="add">
            <input type="hidden" name="e4_cart_item" value="<?=$content['ID'];?>">
            <input type="submit" value="Add to Cart">
        </form>
    </div>
    <hr>
</div>
<div class="span-18 last">
    <h2>Description</h2>
    <?=Markdown(@$content['data']['body']);?>
</div>