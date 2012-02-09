<?php 
	foreach ($data['page']['body']['content'] as $content){
		print '<h2><a href="@@configuration.basedir@@' . $content['url'] . '">' . $content['name'] . '</a></h2>';
		print '<p>' . $content['type'] . ' posted on ' . $content['timestamp'] . '</p>';
	}
?>