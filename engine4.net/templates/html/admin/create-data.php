<div class="span-16">
	<h2 class="SectionHeading">Create Content</h2>
	<form action="?e4_action=admin&e4_op=save&e4_ID=0" method="POST">
		<table>
			<tr><td>Name</td><td><input name="e4_form_content_name" type="text" size="60" value=""></td></tr>
			<tr><td>Type</td><td>
								<input type="radio" name="e4_form_content_type" value="Content" checked>Content: The generic content type used for static pages<br>
								<input type="radio" name="e4_form_content_type" value="Action">Action Alias: Create a clean URL for an action<br>
								<input type="radio" name="e4_form_content_type" value="Product">Product: Create a product that can be sold<br>
							</td></tr>
			<tr><td>&nbsp;</td><td><input name="save" type="submit" value="Create Content"></td></tr>			
		</table>
	</form>
</div>