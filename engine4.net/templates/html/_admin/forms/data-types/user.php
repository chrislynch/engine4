<tr><td>eMail Address</td>
	<td><input type="text" name="e4_form_content_data_email" value="<?=@$content['data']['email']?>"></td>
</tr>
<tr><td>Password</td>
	<td><input type="password" name="e4_form_content_data_password" value="<?=@$content['data']['password']?>"></td>
</tr>
<tr><td>Role</td>
	<td>
            <select name="e4_form_content_data_role">
                <option value="User" <?php if(@$content['data']['role'] == 'User') { print 'selected="selected"'; }?>>User</option>
		<option value="Administrator" <?php if(@$content['data']['role'] == 'Administrator') { print 'selected="selected"'; }?>>Administrator</option>
            </select></td>
</tr>

<tr><td class="nobg" colspan="2"><h2 class="SectionHeading">Social Networks</h2></td></tr>
<tr><td>Facebook URL</td>
	<td><input type="text" name="e4_form_content_data_social_facebook" value="<?=@$content['data']['social']['facebook']?>"></td>
</tr>
<tr><td>Twitter Handle</td>
	<td><input type="text" name="e4_form_content_data_social_twitter" value="<?=@$content['data']['social']['twitter']?>"></td>
</tr>