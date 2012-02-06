<div class="container">
	<div class="span-24 last">
		<h1><a href="?e4_action=admin">engine4 Administration</a></h1>
	</div>
	<div class="span-4">
		<?php include e4_findinclude(@$data['configuration']['renderers']['html']['sidebar-left']) ?>
	</div>
	<div class="span-16">
		<div class="span-16 last"><?php include e4_findinclude(@$data['configuration']['renderers']['html']['body-header']) ?></div>
		<div class="span-16 last"><?php include e4_findinclude(@$data['configuration']['renderers']['html']['body-content']) ?></div>
		<div class="span-16 last"><?php include e4_findinclude(@$data['configuration']['renderers']['html']['body-footer']) ?></div>
	</div>
	<div class="span-4 last">
		<?php include e4_findinclude(@$data['configuration']['renderers']['html']['sidebar-right']) ?>
	</div>
</div>