<div class="container">
	<div class="span-24 last">
		<?php include e4_html_findtemplate(@$data['configuration']['renderers']['html']['body-header']) ?>
	</div>
	<div class="span-4">
		<?php include e4_html_findtemplate(@$data['configuration']['renderers']['html']['sidebar-left']) ?>
	</div>
	<div class="span-16">
		<div class="span-16 last"><?php include e4_html_findtemplate(@$data['configuration']['renderers']['html']['body-content-header']) ?></div>
		<div class="span-16 last"><?php include e4_html_findtemplate(@$data['configuration']['renderers']['html']['body-content']) ?></div>
		<div class="span-16 last"><?php include e4_html_findtemplate(@$data['configuration']['renderers']['html']['body-content-footer']) ?></div>
	</div>
	<div class="span-4 last">
		<?php include e4_html_findtemplate(@$data['configuration']['renderers']['html']['sidebar-right']) ?>
	</div>
	<div class="span-24 last">
		<?php include e4_html_findtemplate(@$data['configuration']['renderers']['html']['body-footer']) ?>
	</div>
</div>