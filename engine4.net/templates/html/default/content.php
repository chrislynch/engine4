<?php
    if (sizeof($data['page']['body']['content']) == 1){
        print '<h1 class="articleheading">' . @$content['name'] . '</h1>';
        include e4_findtemplate('widgets/content-info.php');
	print Markdown(@$content['data']['body']); 
        include e4_findtemplate('widgets/addthis.php');
        print '<hr>';
        print '<h2>Discuss "' . @$content['name'] . '"</h2>';
        include e4_findtemplate('widgets/disqus.php');
    } else {
        print '<h2 class="articleteaserheading"><a href="' . @$content['link'] . '">' . @$content['name'] . '</a></h2>';
	print Markdown(@$content['data']['teaser']); 
        print '<div class="articleteaserlinks">';
        include e4_findtemplate('widgets/content-info.php');
        print '<div class="clear"></div>';
    }
?>