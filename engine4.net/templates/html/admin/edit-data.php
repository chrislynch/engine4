<div class="span-16">
<?php foreach($data['page']['body']['content'] as $content){?>
	<h2 class="SectionHeading">Edit <?=@$content['type']; ?></h2>
	<form action="?e4_action=admin&e4_op=save&e4_ID=<?php print $content['ID']; ?>" method="POST">
		<table>
			<tr><td>ID</td><td><?php print $content['ID'];?><input name="e4_form_content_ID" type="hidden" value="<?php print $content['ID'];?>"></td></tr>
			<tr><td>Name</td><td><input name="e4_form_content_name" type="text" size="60" value="<?php print $content['name'];?>"></td></tr>
			<tr><td>Type</td><td><?php print $content['type'];?><input name="e4_form_content_type" type="hidden" size="60" value="<?php print $content['type'];?>"></td></tr>
			<?php
				include e4_findtemplate('content-types/' .  strtolower($content['type']) . '.php'); 
				switch ($content['type']){
					case 'Content':
						include e4_findtemplate('content-types/seo.php');
						break;
				}
			?>
			<tr><td class="nobg" colspan="2"><h2 class="SectionHeading">Save and Continue</h2></td></tr>
			<tr><td></td><td><input name="save" type="submit" value="Save Content"></td></tr>					
		</table>
	</form>
<?php }?>
</div>