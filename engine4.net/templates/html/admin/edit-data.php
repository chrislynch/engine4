<div class="span-16">
<h2 class="SectionHeading">Edit Content</h2>
<?php foreach($data['page']['body']['content'] as $content){?>
	<form action="?e4_action=admin&e4_op=save&e4_ID=<?php print $content['ID']; ?>" method="POST">
		<table>
			<tr><td>ID</td><td><?php print $content['ID'];?><input name="e4_form_content_ID" type="hidden" value="<?php print $content['ID'];?>"></td></tr>
			<tr><td>Name</td><td><input name="e4_form_content_name" type="text" size="60" value="<?php print $content['name'];?>"></td></tr>
			<tr><td>Type</td><td><?php print $content['type'];?><input name="e4_form_content_type" type="hidden" size="60" value="<?php print $content['type'];?>"></td></tr>
			<tr><td>URL</td><td><input name="e4_form_content_url" type="text" size="60" value="<?php print $content['url'];?>"></td></tr>
			<tr><td>Body</td><td><textarea name="e4_form_content_data_body" cols="40" rows="10"><?=@$content['data']['body'];?></textarea></td></tr>
		
			<tr><td colspan="2"><h2 class="SectionHeading">SEO Data</h2></td></tr>
		
			<tr><td>Keywords</td><td><input name="e4_form_content_data_seo_keywords" type="text" size="60" value="<?=@$content['data']['seo']['keywords'];?>"></td></tr>			
			<tr><td>Meta Description</td><td><textarea name="e4_form_content_data_seo_description" cols="40" rows="5"><?=@$content['data']['seo']['description'];?></textarea></td></tr>
			<tr><td>Meta Abstract</td><td><textarea name="e4_form_content_data_seo_abstract" cols="40" rows="2"><?=@$content['data']['seo']['abstract'];?></textarea></td></tr>
			<tr><td></td><td><input name="save" type="submit" value="Save Content"></td></tr>			
		</table>
	</form>
<?php }?>
</div>