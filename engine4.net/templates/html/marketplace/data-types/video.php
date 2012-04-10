<?php
    if (sizeof($data['page']['body']['content']) == 1){
        print '<iframe width="560" height="315" src="http://www.youtube.com/embed/' . @$content['data']['video']['youtubeid'] . '" frameborder="0" allowfullscreen></iframe>';
	print Markdown(@$content['data']['body']); 
    } else {
        print '<img src="http://img.youtube.com/vi/' . @$content['data']['video']['youtubeid'] . '/1.jpg" style="float:left; padding-right: 10px">';
    }
?>