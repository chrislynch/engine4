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