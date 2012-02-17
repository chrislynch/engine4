<h1>Product: <?=@$content['name'];?></h1>
<div class="span-4">
	<img src="<?php print $content['data']['files']['images']['primary']['path']; ?>">
</div>
<div class="span-12 last">
	<form action="https://checkout.google.com/api/checkout/v2/checkoutForm/Merchant/627458445324618" id="BB_BuyButtonForm" method="post" name="BB_BuyButtonForm" target="_top">
	    <input name="item_name_1" type="hidden" value="<?=@$content['name'];?>"/>
	    <input name="item_description_1" type="hidden" value="<?=@$content['name'];?>"/>
	    <input name="item_quantity_1" type="hidden" value="1"/>
	    <input name="item_price_1" type="hidden" value="<?=@$content['data']['product']['sellingprice'];?>"/>
	    <input name="item_currency_1" type="hidden" value="GBP"
	    <input name="_charset_" type="hidden" value="utf-8"/>
	    <input alt="" src="https://checkout.google.com/buttons/buy.gif?merchant_id=627458445324618&amp;w=117&amp;h=48&amp;style=white&amp;variant=text&amp;loc=en_US" type="image"/>
	</form>
</div>
