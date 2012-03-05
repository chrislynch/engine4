<?php
    if (sizeof($data['page']['body']['content']) == 1){
        print '<h1 class="articleheading">' . @$content['name'] . '</h1>';
        print '<iframe width="560" height="315" src="http://www.youtube.com/embed/' . @$content['data']['video']['youtubeid'] . '" frameborder="0" allowfullscreen></iframe>';
	print Markdown(@$content['data']['body']); 
    } else {
        print '<h2 class="articleteaserheading"><a href="' . @$content['link'] . '">' . @$content['name'] . '</a></h2>';
        print '<img src="http://img.youtube.com/vi/' . @$content['data']['video']['youtubeid'] . '/1.jpg" style="float:left; padding-right: 10px">';
	print Markdown(@$content['data']['teaser']); 
        print '<div class="clear"></div>';
    }
?>