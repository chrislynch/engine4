<?php

if(isset($data['page']['body']['widgets']['tag']['data'])){
    print '<h3>Tags</h3>';
    foreach($data['page']['body']['widgets']['tag']['data'] as $tag=>$tagsize){
        print '<a href="@@configuration.basedir@@?e4_tag=' . $tag . '">' . $tag . '</a>(' . $tagsize . ') ';
    }
}

?>
