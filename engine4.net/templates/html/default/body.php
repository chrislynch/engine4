<div class="container">
	<h1><?php print $data['page']['body']['H1']; ?></h1>
	<?php 
		foreach ($data['page']['body']['content'] as $content){
			print '<h2>' . $content['data']['Title'] . '</h2>';
			print Markdown($content['data']['Body']);
		}
	?>
</div>