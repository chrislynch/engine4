<?php
    if (sizeof($data['page']['body']['content']) == 1){
        print '<hr>';
        if(isset($content['data']['social']['addthis']) && $content['data']['social']['addthis'] == 1){
            include e4_findtemplate('widgets/addthis.php');
            print '<br><hr>';
        }
        if(isset($content['data']['social']['disqus']) && $content['data']['social']['disqus'] == 1){
            print '<h2>Discuss "' . @$content['name'] . '"</h2>';
            include e4_findtemplate('widgets/disqus.php');
        }
    } else {
        include e4_findtemplate('widgets/content-info.php');
        print '<div class="clear"></div>';
    }
?>