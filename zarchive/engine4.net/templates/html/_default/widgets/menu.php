<?php

/*
 * Output a menu/list from the folders data.
 */
$lastslashcount = -1;
$menu = '';

foreach($data['page']['body']['folders'] as $folder=>$folderdata){
    $slashcount = substr_count($folder, '/');
    if ($lastslashcount == -1){
        $menu .= '<ul id="nav" class="dropdown dropdown-horizontal">';
    } else {
        if ($slashcount > $lastslashcount){
            $menu .= '<ul>';
        } elseif ($slashcount < $lastslashcount){
            $menu .= '</ul></li>';
        } else {
            $menu .= '</li>';
        }
    }
    $lastslashcount = $slashcount;
    
    $menu .= '<li>';
    $menu .= $folderdata['name'];
}
$menu .= '</li>';
for ($index = $lastslashcount; $index > 0; $index--) {
    $menu .= '</ul>';
}

print $menu;
?>
