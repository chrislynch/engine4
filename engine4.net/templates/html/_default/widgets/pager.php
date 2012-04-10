<?php
/*
 * I am the pager widget.
 * It is my job to output paging controls.
 */
if (isset($data['page']['pager']) && 
    isset($data['page']['pager']['pagecount']) && 
    $data['page']['pager']['pagecount'] > 1){
    
    // Yes, there is pager data
    // Yes, there is more than one page
    // We need to output a pager.
    
    if (isset($_REQUEST['e4_page'])){
        $page = $_REQUEST['e4_page'];
    } else {
        $page = 0;
    }
    $pagecount = $data['page']['pager']['pagecount'];
    
    print '<div class="PaginationWidget"><ul>';
    if ($page > 0){
        print '<li><a href="' . pager_BuildURL(0) . '">First</a></li>';
        print '<li><a href="' . pager_BuildURL($page - 1) . '">Previous</a></li>';
    }
    for ($index = 1; $index <= $pagecount; $index++) {
        if (($index -1) == $page){
            print '<li>' . $index . '</li>';
        } else {
            print '<li><a href="' . pager_BuildURL($index - 1) . '">' . $index . '</a></li>';
        }
        
    }
    if ($page < $pagecount -1){
        print '<li><a href="' . pager_BuildURL($page + 1) . '">Next</a></li>';
        print '<li><a href="' . pager_BuildURL($pagecount -1) . '">Last</a></li>';
    }
    print '</ul></div>';
}

function pager_BuildURL($gotoPage){
    if ($gotoPage >= 1){
        return e4_BuildURL(array('e4_page'=>$gotoPage));
    } else {
        return e4_BuildURL(array('e4_page'=>''));
    }
    
}

?>