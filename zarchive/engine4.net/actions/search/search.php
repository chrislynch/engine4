<?php
/*
 * I am the search action
 * It is my job to search content, before view loads it.
 * I am cleverer than view, when it comes to searching, and can be overridden separately.
 */

/* 
 * Existing parameters for search:
 * e4_action    =   Checks for admin status
 * e4_url       =   Checks for clean urls
 * e4_ID        =   Checks for a specific ID
 * e4_search    =   Checks for a keyword phrase
 * e4_page      =   Sets the page when paging data
 * 
 * The criteria() array is then checked for 
 *      XML     =   Searches for this item in the XML. This is a "like" search
 *      TAG     =   Searches for a particular tag in e4_tags
 *      Other   =   Creates an additional clause in the where for the key (Other) value pair.
 * 
 * The e4_search_criteria parameter is not read by the master search, only by this action.
 * TODO: Think about this - is it logical?
*/

function e4_action_search_search_go(&$data){
    e4_data_search();
}
?>
