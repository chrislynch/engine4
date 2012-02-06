<h1><?php print $data['page']['body']['H1']; ?></h1>
<?php 
	foreach ($data['page']['body']['content'] as $content){
		print '<h2>' . $content['name'] . '</h2>';
		print Markdown(@$content['data']['body']);
	}
?>