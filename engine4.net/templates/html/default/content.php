<?php
    if (sizeof($data['page']['body']['content']) == 1){
        print '<h1>' . @$content['name'] . '</h1>';
        include e4_findtemplate('widgets/content-info.php');
	print Markdown(@$content['data']['body']); 
        print '<hr>';
        include e4_findtemplate('widgets/addthis.php');
        print '<br><hr>';
        print '<h2>Discuss "' . @$content['name'] . '"</h2>';
        include e4_findtemplate('widgets/disqus.php');
    } else {
        print '<h2><a href="' . @$content['link'] . '">' . @$content['name'] . '</a></h2>';
	print Markdown(@$content['data']['teaser']); 
        include e4_findtemplate('widgets/content-info.php');
        print '<div class="clear"></div>';
    }
?>