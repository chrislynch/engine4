<tr><td class="nobg" colspan="2"><h2 class="SectionHeading">Social Networking Options</h2></td></tr>
<tr><td>Show AddThis</td>
    <td>
        <select name="e4_form_content_data_social_addthis">
            <option value="0" <?php if(@$content['data']['social']['addthis'] == 0) { print 'selected="selected"'; }?>>Do not display</option>
            <option value="1" <?php if(@$content['data']['social']['addthis'] == 1) { print 'selected="selected"'; }?>>Display</option>
        </select>    
    </td>
</tr>
<tr><td>Show Disqus</td>
    <td>
        <select name="e4_form_content_data_social_disqus">
            <option value="0" <?php if(@$content['data']['social']['disqus'] == 0) { print 'selected="selected"'; }?>>Do not display</option>
            <option value="1" <?php if(@$content['data']['social']['disqus'] == 1) { print 'selected="selected"'; }?>>Display</option>
        </select>    
    </td></tr>
