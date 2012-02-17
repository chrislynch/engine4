<div class="span-16">
<?php foreach($data['page']['body']['content'] as $content){?>
	<h2 class="SectionHeading">Edit <?=@$content['type']; ?></h2>
	<form action="?e4_action=admin&e4_op=save&e4_ID=<?php print $content['ID']; ?>" method="POST" enctype="multipart/form-data">
		<table>
			<tr><td>ID</td>
				<td><?php print $content['ID'];?>
					<input name="e4_form_content_ID" type="hidden" value="<?php print $content['ID'];?>">
					<input name="e4_form_content_iscontent" type="hidden" value="<?php print $content['iscontent'];?>">
				</td></tr>
			<tr><td>Name</td>
				<td><input name="e4_form_content_name" type="text" size="60" value="<?php print $content['name'];?>"></td></tr>
			<tr><td>Type</td>
				<td><?php print $content['type'];?><input name="e4_form_content_type" type="hidden" size="60" value="<?php print $content['type'];?>"></td></tr>
			<?php
				// @todo: We need to understand the difference between content types and non-content types
				include e4_findtemplate('forms/data-types/' .  strtolower($content['type']) . '.php'); 
                                if ($content['iscontent'] == 1){
                                    include e4_findtemplate('forms/data-types/seo.php');
                                }
                                /*
				switch ($content['type']){
					case 'Content':
						include e4_findtemplate('forms/data-types/seo.php');
						break;
				}
                                 */
			?>
			<tr><td class="nobg" colspan="2"><h2 class="SectionHeading">Save and Continue</h2></td></tr>
			<tr><td>Item Status:</td><td>
										<select name="e4_form_content_status">
											<option value="0" <?php if($content['status'] == 0) { print 'selected="selected"'; }?>>Unpublished/Disabled</option>
											<option value="1" <?php if($content['status'] == 1) { print 'selected="selected"'; }?>>Published/Active</option>
										</select>
									</td></tr>
			<tr><td></td><td><input name="save" type="submit" value="Save Content"></td></tr>					
		</table>
	</form>
<?php }?>
</div>
