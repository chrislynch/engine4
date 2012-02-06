<h2>Create Content</h2>
<p><a href="?e4_action=admin&e4_op=create">Click here to add content to your system</a></p>
<h2>Edit Existing Contents</h2>
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