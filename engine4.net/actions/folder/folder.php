<?php

function e4_action_folder_folder_go(&$data){
    /* 
     * Load the folder data into menus ready to be displayed.
     */
    $foldersdata = e4_db_query('SELECT folder,substring_index(folder,"/",-1) as name, count(0) as size FROM e4_data GROUP BY folder ORDER BY folder ASC');
    $folders = array();
    while($folder = mysql_fetch_assoc($foldersdata)){
        if(strlen(trim($folder['folder'])) > 0 ){
            $folders[$folder['folder']] = array('name'=>ucwords($folder['name']),'size'=>$folder['size']);
        }
    }
    $data['page']['body']['folders'] = $folders;
}
?>
