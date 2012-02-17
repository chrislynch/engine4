<tr><td class="nobg" colspan="2"><h2 class="SectionHeading">Product Data</h2></td></tr>
<tr><td>SKU</td><td><input name="e4_form_content_data_product_sku" type="text" size="30" value="<?=@$content['data']['product']['sku'];?>"></td></tr>			
<tr><td>List Price/RRP</td><td><input name="e4_form_content_data_product_listprice" type="text" size="6" value="<?=@$content['data']['product']['listprice'];?>"></td></tr>
<tr><td>Selling Price</td><td><input name="e4_form_content_data_product_sellingprice" type="text" size="6" value="<?=@$content['data']['product']['sellingprice'];?>"></td></tr>

<tr><td class="nobg" colspan="2"><h2 class="SectionHeading">Product Images</h2></td></tr>
<tr><td>Primary Image</td>
    <td>
        <a target="_blank" href="@@configuration.basedir@@<?php print $content['data']['files']['images']['primary']['path'];?>"><?php print $content['data']['files']['images']['primary']['name'];?></a><br>
        <input type="file" name="e4_form_content_files_images_primary">
    </td>
</tr>