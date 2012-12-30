<?php
    if (sizeof($data['page']['body']['content']) == 1){
	print Markdown(@$content['data']['body']); 
    } else {
	print Markdown(@$content['data']['teaser']); 
    }
?>