<?php

if(isset($data['page']['body']['widgets']['archive']['data'])){
    print '<h3>Archive</h3>';
    foreach($data['page']['body']['widgets']['archive']['data'] as $date=>$postcount){
        if (strlen(trim($date)) > 0){
            print '<a href="#">' . $date . '</a>(' . $postcount . ') ';
        }
    }
}

?>
