<?php
	foreach ($data['page']['body']['content'] as $content){
		print Markdown(@$content['data']['body']);
	}
?>