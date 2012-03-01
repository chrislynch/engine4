<?php
    if (sizeof($data['page']['body']['content']) == 1){
        print '<h1 class="articleheading">' . @$content['name'] . '</h1>';
	print Markdown(@$content['data']['body']); 
    } else {
        print '<div class="span-7 highlight">';
        print '<h2 class="articleteaserheading"><a href="' . @$content['link'] . '">' . @$content['name'] . '</a></h2>';
	print Markdown(@$content['data']['teaser']); 
        print '</div>';
    }
?>