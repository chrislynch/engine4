<?php
    if (sizeof($data['page']['body']['content']) == 1){
        print '<h1 class="articleheading">' . @$content['name'] . '</h1>';
        include e4_findtemplate('widgets/content-info.php');
	print Markdown(@$content['data']['body']); 
    } else {
        print '<h2 class="articleteaserheading"><a href="' . @$content['link'] . '">' . @$content['name'] . '</a></h2>';
        print '<img src="http://img.youtube.com/vi/' . @$content['data']['video']['youtubeid'] . '/1.jpg" style="float:left; padding-right: 10px">';
	print Markdown(@$content['data']['teaser']); 
        print '<div class="articleteaserlinks">';
        include e4_findtemplate('widgets/content-info.php');
    }
?>