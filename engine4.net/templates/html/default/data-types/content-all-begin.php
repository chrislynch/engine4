<?php
    if (sizeof($data['page']['body']['content']) == 1){
        print '<h1>' . @$content['name'] . '</h1>';
        include e4_findtemplate('widgets/content-info.php');
    } else {
        print '<h2><a href="' . @$content['link'] . '">' . @$content['name'] . '</a></h2>';
    }
?>