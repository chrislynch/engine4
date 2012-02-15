<?php
	print '<h2>' . @$content['name'] . '</h2>';
	print Markdown(@$content['data']['body']); 
?>