<div class="span-16">
	<div class="span-16 last">
		<h2 class="SectionHeading">Edit Existing Contents</h2>
		<form action="?e4_action=admin&e4_admin_op=search" method="POST">
			<div class="span-10">
				<input name="e4_search" type="text" size="40" value="<?=@$_REQUEST['e4_search']?>">
			</div>
			<div class="span-6 last">
			<input type="submit" name="Search" value="Search">
			&nbsp;<a href="?e4_action=admin&e4_admin_op=search">Clear Search</a>
			</div>
		</form>
		<hr>
	</div>
	<div class="span-16 last">
		<table class="DataTable">
			<tr><th>ID</th><th>Type</th><th>Name</th><th>Last Modified</th><th>Status<th>Options/Actions</th></tr>
			<?
				foreach($data['page']['body']['content'] as $content){
					print '<tr>';
						print '<td>' . $content['ID'] . '</td>';
						print '<td>' . $content['type'] . '</td>';
						print '<td>' . $content['name'] . '</td>';
						print '<td>' . $content['timestamp'] . '</td>';
						print '<td>' . $content['status'] . '</td>';
						print '<td><a href="?e4_action=admin&e4_admin_op=edit&e4_ID=' . $content['ID'] . '">Edit</a></td>';
					print '</tr>';
				}
			?>
		</table>
	</div>
        <div class="span-16 last">
            <?php include e4_findtemplate('widgets/pager.php'); ?>
        </div>
</div>
