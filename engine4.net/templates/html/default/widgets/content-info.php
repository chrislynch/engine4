<?php
if (isset($content['linkages']['owner'])){
    print 'Posted By: ';
    foreach($content['linkages']['owner'] as $contentuserID=>$contentuser){
        if ($contentuserID ==  0){
            print 'Site Owner';
        } else {
            print '<a href="@@configuration.basedir@@e4_ID=' . $contentuser['ID'] . '">' . $contentuser['name'] . '</a>';
        }
    }
}
print '&nbsp;|&nbsp;Posted On: ' . $content['timestamp'];
print '&nbsp;|&nbsp;Tags: ';
foreach(@$content['tags'] as $tags){
    foreach(@$tags as $tag){
        print '<a href="@@configuration.basedir@@?e4_tag=' . $tag . '">' . $tag . '</a>,';
    }
}
?>
