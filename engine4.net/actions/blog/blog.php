<?php

/*
 * I am the blog action.
 * I should be called on any site or page that is a blog.
 */

function e4_action_blog_blog_go(&$data){
    /*
     * First thing that we should do is collect up all the tags that exist
     */
    $tags = array();
    $tagsSQL = 'SELECT tag,count(tag) as size FROM e4_tags GROUP BY tag ORDER BY tag ASC';
    $tagsData = e4_db_query($tagsSQL);
    while($tag = mysql_fetch_assoc($tagsData)){
        $tags[$tag['tag']] = $tag['size'];
    }
    
    $data['page']['body']['widgets']['tag']['data'] = $tags;
}

?>
