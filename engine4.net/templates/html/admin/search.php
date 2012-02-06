<h2>Edit Existing Contents</h2>
<form action="?e4_action=admin&e4_op=search" method="POST">
	Search Content: <input name="e4_search" type="text" size="40" value="<?=@$_REQUEST['e4_search']?>">
	&nbsp;<input type="submit" name="Search">
	&nbsp;<a href="?e4_action=admin&e4_op=search">Clear Search</a>
</form>
<hr>
<table>
	<tr><th>ID</th><th>Name</th><th>Last Modified</th><th>&nbsp</th></tr>
	<?
		foreach($data['page']['body']['content'] as $content){
			print '<tr>';
				print '<td>' . $content['ID'] . '</td>';
				print '<td>' . $content['name'] . '</td>';
				print '<td>' . $content['timestamp'] . '</td>';
				print '<td><a href="?e4_action=admin&e4_op=edit&e4_ID=' . $content['ID'] . '">Edit</a></td>';
			print '</tr>';
		}
	?>
</table>